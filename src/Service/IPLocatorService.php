<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class IPLocatorService
{


    public function __construct(
        private ContainerBagInterface $containerBag,
        private HttpClientInterface $client,
    ) {
    }

    public function locateIP(string $client_ip): array
    {

        $key = $this->containerBag->get('ip2location.api_key');

        $location = $this->client->request(
            'GET',
            'https://api.ip2location.io',
            ['headers' =>
                [
                'key' => $key,
                'ip' => $client_ip
                ]
            ]
        );
        

        return json_decode((string) $location->getContent(), true);
    }

    public function extractCoordinates(array $location)
    {
        return ['lat' => $location['latitude'], 
                'lon' => $location['longitude']];
    }
}