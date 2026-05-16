<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\FinalProject;
use App\Models\Keyword;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class ProjectWebController extends Controller
{
    /**
     * Store a new project via web form (session auth).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id'     => 'required|exists:categories,id',
            'title'           => 'required|string|max:255',
            'abstract'        => 'required|string|min:50',
            'research_method' => 'nullable|string',
            'keywords'        => 'nullable|array',
            'keywords.*'      => 'string|max:100',
            'proposal_file'   => 'nullable|file|mimes:pdf|max:10240',
        ], [
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists'   => 'Kategori tidak valid.',
            'title.required'       => 'Judul penelitian wajib diisi.',
            'abstract.required'    => 'Abstrak wajib diisi.',
            'abstract.min'         => 'Abstrak minimal 50 karakter.',
            'proposal_file.mimes'  => 'File proposal harus berformat PDF.',
            'proposal_file.max'    => 'Ukuran file maksimal 10MB.',
        ]);

        $data = [
            'user_id'         => Auth::id(),
            'category_id'     => $validated['category_id'],
            'title'           => $validated['title'],
            'abstract'        => $validated['abstract'],
            'research_method' => $validated['research_method'] ?? null,
            'status'          => 'submitted',
        ];

        // Handle file upload
        if ($request->hasFile('proposal_file')) {
            $file     = $request->file('proposal_file');
            $filename = 'proposal_' . Auth::id() . '_' . time() . '.pdf';
            $file->storeAs('proposals', $filename, 'public');
            $data['proposal_file'] = 'proposals/' . $filename;
        }

        $project = FinalProject::create($data);

        // Save keywords
        if (!empty($validated['keywords'])) {
            foreach ($validated['keywords'] as $kw) {
                $kw = trim($kw);
                if ($kw !== '') {
                    Keyword::create([
                        'final_project_id' => $project->id,
                        'keyword'          => strtolower($kw),
                    ]);
                }
            }
        }

        return redirect()->route('dashboard.projects')
            ->with('success', 'Proyek "' . $project->title . '" berhasil ditambahkan!');
    }

    /**
     * Delete a project via web (session auth).
     */
    public function destroy(Request $request, $id)
    {
        $project = FinalProject::findOrFail($id);

        // Only owner or admin can delete
        if ($project->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Tidak diizinkan menghapus proyek ini.');
        }

        $title = $project->title;
        $project->delete();

        return redirect()->route('dashboard.projects')
            ->with('success', 'Proyek "' . \Illuminate\Support\Str::limit($title, 40) . '" berhasil dihapus.');
    }
}
