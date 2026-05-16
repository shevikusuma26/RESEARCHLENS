<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\FinalProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function show()
    {
        $user     = Auth::user();
        $projects = FinalProject::where('user_id', $user->id)->with('category')->get();
        $apiKeys  = $user->apiKeys()->orderBy('created_at', 'desc')->get();
        return view('dashboard.profile', compact('user', 'projects', 'apiKeys'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name'       => 'required|string|max:255',
            'bio'        => 'nullable|string|max:500',
            'student_id' => 'nullable|string|max:20',
            'phone'      => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user->update($request->only(['name', 'bio', 'student_id', 'phone']));

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password'     => 'required|min:8|confirmed',
        ], [
            'new_password.min'       => 'Password baru minimal 8 karakter.',
            'new_password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }

        $user->update(['password' => Hash::make($request->new_password)]);

        return back()->with('success', 'Password berhasil diubah!');
    }

    public function uploadPhoto(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'profile_photo.required' => 'File foto wajib dipilih.',
            'profile_photo.image'    => 'File harus berupa gambar.',
            'profile_photo.max'      => 'Ukuran foto maksimal 2MB.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $user = Auth::user();

        // Delete old photo
        if ($user->profile_photo && file_exists(storage_path('app/public/' . $user->profile_photo))) {
            unlink(storage_path('app/public/' . $user->profile_photo));
        }

        $file     = $request->file('profile_photo');
        $filename = 'profile_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->storeAs('profile_photos', $filename, 'public');

        $user->update(['profile_photo' => 'profile_photos/' . $filename]);

        return back()->with('success', 'Foto profil berhasil diperbarui!');
    }
}
