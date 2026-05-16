<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AcademicApiService
{
    /**
     * Sanitize string to ensure it's valid UTF-8
     */
    private function sanitize(?string $text): string
    {
        if (!$text) return '';
        // Convert to UTF-8 and ignore invalid characters
        return mb_convert_encoding($text, 'UTF-8', 'UTF-8');
    }

    /**
     * Search for academic papers using Semantic Scholar with ArXiv fallback
     */
    public function searchPapers(string $query, int $limit = 10): array
    {
        // 1. Try Semantic Scholar
        $papers = $this->searchSemanticScholar($query, $limit);
        
        if (!empty($papers)) {
            return $papers;
        }

        // 2. Fallback to ArXiv if Semantic Scholar fails or returns nothing
        Log::info('Falling back to ArXiv API for query: ' . $query);
        return $this->searchArXiv($query, $limit);
    }

    private function searchSemanticScholar(string $query, int $limit): array
    {
        try {
            $response = Http::timeout(10)->get('https://api.semanticscholar.org/graph/v1/paper/search', [
                'query'  => $query,
                'limit'  => $limit,
                'fields' => 'title,abstract,authors,year,venue,url,s2FieldsOfStudy',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $results = [];
                foreach (($data['data'] ?? []) as $paper) {
                    $results[] = [
                        'external_id' => $paper['paperId'] ?? null,
                        'title'       => $this->sanitize($paper['title'] ?? 'No Title'),
                        'abstract'    => $this->sanitize($paper['abstract'] ?? null),
                        'authors'     => collect($paper['authors'] ?? [])->pluck('name')->toArray(),
                        'year'        => $paper['year'] ?? null,
                        'venue'       => $paper['venue'] ?? 'Semantic Scholar',
                        'url'         => $paper['url'] ?? null,
                        'keywords'    => $paper['s2FieldsOfStudy'] ?? [],
                    ];
                }
                return $results;
            }

            Log::error('Semantic Scholar API error: ' . $response->status() . ' - ' . $response->body());
            return [];
        } catch (\Exception $e) {
            Log::error('Semantic Scholar Exception: ' . $e->getMessage());
            return [];
        }
    }

    private function searchArXiv(string $query, int $limit): array
    {
        try {
            // ArXiv search_query needs to be escaped
            $response = Http::timeout(10)->get('http://export.arxiv.org/api/query', [
                'search_query' => 'all:' . $query,
                'start'        => 0,
                'max_results'  => $limit,
            ]);

            if ($response->successful()) {
                $xml = simplexml_load_string($response->body());
                $results = [];
                
                foreach ($xml->entry as $entry) {
                    $authors = [];
                    foreach ($entry->author as $author) {
                        $authors[] = (string) $author->name;
                    }

                    $results[] = [
                        'external_id' => (string) $entry->id,
                        'title'       => $this->sanitize(trim((string) $entry->title)),
                        'abstract'    => $this->sanitize(trim((string) $entry->summary)),
                        'authors'     => $authors,
                        'year'        => date('Y', strtotime((string) $entry->published)),
                        'venue'       => 'ArXiv',
                        'url'         => (string) $entry->id,
                        'keywords'    => ['ArXiv Paper'], // ArXiv doesn't have explicit keywords in basic API
                    ];
                }
                return $results;
            }

            Log::error('ArXiv API error: ' . $response->status());
            return [];
        } catch (\Exception $e) {
            Log::error('ArXiv API Exception: ' . $e->getMessage());
            return [];
        }
    }
}
