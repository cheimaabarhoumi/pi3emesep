<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class LegalTermGeneratorService
{
    private $client;
    private $apiKey;
    private $endpoint = 'https://api.groq.com/openai/v1/chat/completions';

    public function __construct(HttpClientInterface $client, string $groqApiKey)
    {
        $this->client = $client;
        $this->apiKey = $groqApiKey;
    }

    public function generateClause(string $contractType): ?string
    {
        $prompt = "Génère une clause juridique professionnelle pour un contrat de type : " . $contractType . ". 
        Sois clair, formel et concis. Rédige directement la clause sans titre.ainsi le nom du societe est mecharift";

        $response = $this->client->request('POST', $this->endpoint, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => 'llama3-8b-8192', // ou llama3-8b ou llama3-70b si tu veux plus costaud
                'messages' => [
                    ['role' => 'system', 'content' => 'Tu es un assistant juridique spécialisé en rédaction de contrats.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.2, // pour rester très formel
                'max_tokens' => 500,
            ],
        ]);

        $data = $response->toArray();

        return $data['choices'][0]['message']['content'] ?? null;
    }
}
