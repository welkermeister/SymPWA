<?php

namespace App\Service;

use Symfony\Component\Stopwatch\Stopwatch;

class StopwatchService {

    private Stopwatch $stopwatch;

    public function __construct(

    ) {
        $this->stopwatch = new Stopwatch();
    }

    public function startStopwatch(string $stopwatchId) 
    {
        $this->stopwatch->start($stopwatchId);
    }

    /** 
     *  Stops the stopwatch and returns an array of sections and their timed durations
     *  @param string $eventName
     *  @return array
     */
    public function stopStopwatch(string $stopwatchId): float|int
    {
        // //declare sections array
        // $sections = [];
        // //declare and initialize the StopwatchEvent
        // $event = $this->stopwatch->stop($eventName);
        // //get durations of sections starting with initial grab of client IP
        // $initSection = $this->stopwatch->getSectionEvents('init');
        // $initSectionTime = $initSection->getDuration();
        // //continuing with the API call to ip2location
        // $ip2locationSection = $this->stopwatch->getSectionEvents('ip2location')[0];
        // $ip2locationSectionTime = $ip2locationSection->getDuration();
        // //then the API call to OpenWeather
        // $openWeatherSection = $this->stopwatch->getSectionEvents('openweather')[0];
        // $openWeatherSectionTime = $openWeatherSection->getDuration();
        // //end with everything until serving of the template
        // $finalSection = $this->stopwatch->getSectionEvents('final')[0];
        // $finalSectionTime = $finalSection->getDuration();
        // //get the total time from StopwatchEvent
        // $totalTime = $event->getDuration();

        // //store durations in sections array
        // $sections['initsection'] = $initSectionTime;
        // $sections['ip2locationsection'] = $ip2locationSectionTime;
        // $sections['openweathersection'] = $openWeatherSectionTime;
        // $sections['finalsection'] = $finalSectionTime; 
        // //finally, store total time in sections array
        // $sections['totaltime'] = $totalTime; 

        // return $sections;

        return $this->stopwatch->stop($stopwatchId)->getDuration();
    }

    public function openStopwatchSection($id = null) 
    {
        $this->stopwatch->openSection($id);
    }

    public function stopStopwatchSection(string $sectionName) 
    {
        $this->stopwatch->stopSection($sectionName);
    }

    public function lap(string $eventName)
    {
        return $this->stopwatch->lap($eventName)->getDuration();
    }

    public function getTotalTime(array $times)
    {
        $result = 0;
        foreach($times as $time) 
        {
            $result += $time;
        }
        return $result;
    }

}

