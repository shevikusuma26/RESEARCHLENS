<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ResearchLens API Documentation</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 text-gray-100 min-h-screen">
    <div class="max-w-6xl mx-auto px-6 py-12">
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-white">ResearchLens API Documentation</h1>
            <p class="text-slate-400 mt-2">REST API endpoints for authentication, project analysis, recommendations, and system monitoring.</p>
        </div>

        <div class="space-y-6">
            <div class="bg-slate-800 rounded-3xl border border-slate-700 p-6">
                <h2 class="text-2xl font-semibold mb-4">Authentication</h2>
                <ul class="space-y-3 text-slate-300">
                    <li><strong>POST</strong> /api/register</li>
                    <li><strong>POST</strong> /api/login</li>
                    <li><strong>POST</strong> /api/logout</li>
                    <li><strong>POST</strong> /api/refresh</li>
                    <li><strong>POST</strong> /api/forgot-password</li>
                    <li><strong>POST</strong> /api/reset-password</li>
                </ul>
            </div>

            <div class="bg-slate-800 rounded-3xl border border-slate-700 p-6">
                <h2 class="text-2xl font-semibold mb-4">Profile</h2>
                <ul class="space-y-3 text-slate-300">
                    <li><strong>GET</strong> /api/profile</li>
                    <li><strong>PUT</strong> /api/profile/update</li>
                    <li><strong>PUT</strong> /api/profile/change-password</li>
                </ul>
            </div>

            <div class="bg-slate-800 rounded-3xl border border-slate-700 p-6">
                <h2 class="text-2xl font-semibold mb-4">Final Projects</h2>
                <ul class="space-y-3 text-slate-300">
                    <li><strong>GET</strong> /api/projects</li>
                    <li><strong>POST</strong> /api/projects</li>
                    <li><strong>GET</strong> /api/projects/{id}</li>
                    <li><strong>PUT</strong> /api/projects/{id}</li>
                    <li><strong>DELETE</strong> /api/projects/{id}</li>
                </ul>
            </div>

            <div class="bg-slate-800 rounded-3xl border border-slate-700 p-6">
                <h2 class="text-2xl font-semibold mb-4">Analysis</h2>
                <ul class="space-y-3 text-slate-300">
                    <li><strong>POST</strong> /api/analyze/similarity</li>
                    <li><strong>POST</strong> /api/analyze/novelty</li>
                    <li><strong>GET</strong> /api/analyze/trending</li>
                    <li><strong>GET</strong> /api/analyze/statistics</li>
                </ul>
            </div>

            <div class="bg-slate-800 rounded-3xl border border-slate-700 p-6">
                <h2 class="text-2xl font-semibold mb-4">Recommendations</h2>
                <ul class="space-y-3 text-slate-300">
                    <li><strong>GET</strong> /api/recommendation/novelty</li>
                    <li><strong>GET</strong> /api/recommendation/{project_id}</li>
                    <li><strong>GET</strong> /api/recommendation/{project_id}/type/{type}</li>
                </ul>
            </div>

            <div class="bg-slate-800 rounded-3xl border border-slate-700 p-6">
                <h2 class="text-2xl font-semibold mb-4">Notifications</h2>
                <ul class="space-y-3 text-slate-300">
                    <li><strong>GET</strong> /api/notifications</li>
                    <li><strong>GET</strong> /api/notifications/unread-count</li>
                    <li><strong>PUT</strong> /api/notifications/{id}/read</li>
                    <li><strong>PUT</strong> /api/notifications/read-all</li>
                </ul>
            </div>

            <div class="bg-slate-800 rounded-3xl border border-slate-700 p-6">
                <h2 class="text-2xl font-semibold mb-4">API Keys</h2>
                <ul class="space-y-3 text-slate-300">
                    <li><strong>POST</strong> /api/api-keys/generate</li>
                    <li><strong>GET</strong> /api/api-keys</li>
                    <li><strong>PUT</strong> /api/api-keys/{keyId}/deactivate</li>
                    <li><strong>GET</strong> /api/api-keys/usage</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
