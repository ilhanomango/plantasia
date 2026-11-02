<?php
namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;
class PlantApiService
{
    public function __construct(
        private HttpClientInterface $httpClient,
        #[Autowire('%perenual_api_key%')] private string $apiKey
    ) {}

    public function searchPlants(string $query): array
    {
        try {
            $response = $this->httpClient->request('GET', 'https://perenual.com/api/v2/species-list', [
                'query' => [
                    'key' => $this->apiKey,
                    'q' => $query,
                ],
            ]);

            if ($response->getStatusCode() === 200) {
                return $response->toArray()['data'] ?? [];
            }
        } catch (\Throwable $e) {
            // Log de l'erreur si nécessaire
        }

        return [];
    }
}
