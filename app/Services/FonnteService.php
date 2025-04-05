<?php

namespace App\Services;

use GuzzleHttp\Client;

class FonnteService
{
    protected $apiKey;
    protected $client;

    public function __construct()
    {
        $this->apiKey = env('FONNTE_API_KEY'); // Ambil API Key dari .env
        $this->client = new Client([
            'base_uri' => 'https://api.fonnte.com/',
        ]);
    }

    public function sendMessage($number, $message)
    {
        $response = $this->client->post('send', [
            'headers' => [
                'Authorization' => $this->apiKey
            ],
            'form_params' => [
                'target' => $number,  // Nomor tujuan
                'message' => $message,
                'delay' => rand(5, 10), // Delay 2 detik (opsional)
                'schedule' => 0 // Kirim langsung
            ]
        ]);

        return json_decode($response->getBody(), true);
    }

    public function sendBulkMessages($numbers, $message)
    {
        $targets = implode(',', $numbers); // Gabungkan nomor dengan koma

        $response = $this->client->post('send', [
            'headers' => [
                'Authorization' => $this->apiKey
            ],
            'form_params' => [
                'target' => $targets,
                'message' => $message,
                'delay' => 2,
                'schedule' => 0
            ]
        ]);

        return json_decode($response->getBody(), true);
    }
}
