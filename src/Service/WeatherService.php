<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class WeatherService
{

    public function __construct(
        private ContainerBagInterface $containerBag,
        private HttpClientInterface $client,
    ) {
    }

    public function getWeatherJSON(array $coords): array
    {

        $key = $this->containerBag->get("openweather.api_key");

        $response = $this->client->request('GET', 'https://api.openweathermap.org/data/2.5/weather',
         [
            'query' => 
                [
                'lat' => $coords['lat'],
                'lon' => $coords['lon'],
                'appid' => $key,
                'units' => 'metric',
                'lang' => 'de'
                ]
         ]
        );

        return json_decode((string) $response->getContent(), true);
    }

    public function getWeather(array $weather): array
    {
        return [
            'temp' => $weather['main']['temp'],
            'feels_like' => $weather['main']['feels_like'],
            'cond' => $weather['weather'][0]['description']
        ];
    }
}