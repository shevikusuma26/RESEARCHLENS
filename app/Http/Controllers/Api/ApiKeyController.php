<?php

namespace App\Http\Controllers\Api;

use App\Models\ApiKey;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

class ApiKeyController extends Controller
{
    /**
     * Generate new API key
     */
    public function generateKey(Request $request)
    {
        try {
            // Check if user already has an active key
            $existingKey = ApiKey::where('user_id', $request->user()->id)
                ->where('status', 'active')
                ->first();

            if ($existingKey) {
                return response()->json([
                    'success' => true,
                    'message' => 'Active API key already exists',
                    'data' => $existingKey,
                ], 200);
            }

            $apiKey = Str::random(40);

            $key = ApiKey::create([
                'user_id' => $request->user()->id,
                'api_key' => $apiKey,
                'status' => 'active',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'API key generated successfully',
                'data' => $key,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'API key generation failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get API keys for user
     */
    public function getKeys(Request $request)
    {
        try {
            $keys = ApiKey::where('user_id', $request->user()->id)
                ->get()
                ->makeHidden(['api_key']);

            return response()->json([
                'success' => true,
                'message' => 'API keys retrieved successfully',
                'data' => $keys,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve API keys: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Deactivate API key
     */
    public function deactivateKey(Request $request, $keyId)
    {
        try {
            $key = ApiKey::where('id', $keyId)
                ->where('user_id', $request->user()->id)
                ->first();

            if (!$key) {
                return response()->json([
                    'success' => false,
                    'message' => 'API key not found',
                ], 404);
            }

            $key->update(['status' => 'inactive']);

            return response()->json([
                'success' => true,
                'message' => 'API key deactivated successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to deactivate API key: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get API usage statistics
     */
    public function getUsage(Request $request)
    {
        try {
            $keys = ApiKey::where('user_id', $request->user()->id)->get();
            
            $totalRequests = $keys->sum('request_count');
            $stats = $keys->map(function ($key) {
                return [
                    'id' => $key->id,
                    'status' => $key->status,
                    'request_count' => $key->request_count,
                    'last_used_at' => $key->last_used_at,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'API usage retrieved successfully',
                'data' => [
                    'total_requests' => $totalRequests,
                    'keys_statistics' => $stats,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve API usage: ' . $e->getMessage(),
            ], 500);
        }
    }
}
