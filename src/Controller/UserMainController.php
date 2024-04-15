<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\WeatherDataRepository;
use App\Service\IPLocatorService;
use App\Service\StopwatchService;
use App\Service\WeatherService;
use DateTimeZone;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Clock\Clock;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Entity\User;


class UserMainController extends AbstractController
{

    

    #[Route('/user/main', name: 'app_user_main')]
    public function index(
        AuthenticationUtils $authenticationUtils,
        IPLocatorService $iPLocatorService,
        WeatherService $weatherService,
        WeatherDataRepository $weatherDataRepository,
        UserRepository $userRepository,
        StopwatchService $stopwatchService,
        ): Response
    {
        $temp = 0;
        $stopwatchService->startStopwatch('stopwatch');

        $error = $authenticationUtils->getLastAuthenticationError();
        $request = Request::createFromGlobals();
        $ip = $request->getClientIp();

        $initTime = $stopwatchService->lap('stopwatch');
        $temp += $initTime;

        #locate IP
        $location = $iPLocatorService->locateIP($ip);

        $ip2locationTime = $stopwatchService->lap('stopwatch') - $temp;
        $temp += $ip2locationTime;

        #get weather data from IP
        $weather = $weatherService->getWeatherJSON($iPLocatorService->extractCoordinates($location));

        $openWeatherTime = $stopwatchService->lap('stopwatch') - $temp;
        $temp += $openWeatherTime;

        $weatherDataRepository->upsertWeatherData($this->getUser(), $weather);

        $finalTime = $stopwatchService->stopStopwatch('stopwatch') - $temp;
        $temp += $finalTime;
        $totalTime = $temp;

        return $this->render('user_main/index.html.twig', [
            'controller_name' => 'UserMainController',
            'client_ip' =>  $ip,
            'location'=> $location['city_name'],
            'temp' => $weatherService->getWeather($weather)['temp'],
            'feels_like' => $weatherService->getWeather($weather)['feels_like'],
            'cond' => $weatherService->getWeather($weather)['cond'],
            'saved_cities' => $this->getUser()->getSavedCities(),
            'update_time' => substr((new Clock())->now()->setTimezone(new DateTimeZone('Europe/Berlin'))->format(\DateTimeInterface::RFC1036), 0, -5),
            'initsection' => $initTime,
            'ip2locationsection' => $ip2locationTime,
            'openweathersection' => $openWeatherTime,
            'finalsection' => $finalTime,
            'totaltime' => $totalTime,
        ]);
    }

    #[Route('user/main/save_city', name:'app_user_main_savecity')]
    public function saveCity(
        AuthenticationUtils $authenticationUtils,
        IPLocatorService $iPLocatorService,
        WeatherService $weatherService,
        UserRepository $userRepository
    )
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $request = Request::createFromGlobals();
        $ip = $request->getClientIp();

        #locate IP
        $location = $iPLocatorService->locateIP($ip);

        $userIdentifier = $this->getUser()->getUserIdentifier();
        $user = $userRepository->findByEmail($userIdentifier)[0];

        $userRepository->addSavedCity($user, $location['city_name']);

        return $this->redirectToRoute('app_user_main', [300]);
    }

    #[Route('user/main/delete_city/{city}', name:'app_user_main_deletecity')]
    public function deleteCity(
        string $city,
        UserRepository $userRepository,
        
    )
    {
        /** @var User */
        $user = $this->getUser();
        $userRepository->removeSavedCity($user, $city);

        return $this->redirectToRoute('app_user_main', [300]);
    }
}
