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

