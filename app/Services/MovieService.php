<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MovieService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = config('omdb.api_key');
    }

    public function search($query, $page = 1)
    {
        try {

            $response = Http::withoutVerifying()->get(
                'https://www.omdbapi.com/',
                [
                    'apikey' => $this->apiKey,
                    's'      => $query,
                    'page'   => $page,
                    'type'   => 'movie'
                ]
            );

            $data = $response->json();

            Log::info('OMDB Search Response', $data);

            if (
                isset($data['Response']) &&
                $data['Response'] === 'True'
            ) {
                return [
                    'movies' => $data['Search'] ?? [],
                    'total'  => (int) ($data['totalResults'] ?? 0),
                    'error'  => null,
                ];
            }

            return [
                'movies' => [],
                'total'  => 0,
                'error'  => $data['Error'] ?? 'Film tidak ditemukan',
            ];

        } catch (\Throwable $e) {

            Log::error('OMDB Search Error', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ]);

            return false;
        }
    }

    public function detail($imdbId)
    {
        try {

            $response = Http::withoutVerifying()->get(
                'https://www.omdbapi.com/',
                [
                    'apikey' => $this->apiKey,
                    'i'      => $imdbId,
                    'plot'   => 'full',
                ]
            );

            $data = $response->json();

            Log::info('OMDB Detail Response', $data);

            if (
                isset($data['Response']) &&
                $data['Response'] === 'True'
            ) {
                return $data;
            }

            return false;

        } catch (\Throwable $e) {

            Log::error('OMDB Detail Error', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ]);

            return false;
        }
    }
}