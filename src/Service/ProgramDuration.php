<?php

namespace App\Service;

use App\Service\ProgramDuration;
use App\Entity\Program;
use App\DataFixtures\ProgramFixtures;
use App\DataFixtures\SeasonFixtures;
use App\DataFixtures\EpisodeFixtures;
use App\Entity\Episode;
use App\Entity\Season;

class ProgramDuration {

    public function calculate(Program $program): string
    {   $durationEpisodes = 0;
        $seasons = $program->getSeasons();
        foreach ($seasons as $season) {
            $episodes = $season->getEpisodes();
               
        foreach ($episodes as $episode ) {
            $durationEpisodes += $episode->getDuration();
        }
        
    }  
    $nbDays = floor ($durationEpisodes / 1440);
    $nbHours = floor (($durationEpisodes - $nbDays * 1440) / 60);
    $nbMinutes = $durationEpisodes - ($nbDays * 1440) - ($nbHours * 60);

         return "{$nbDays}jour(s), {$nbHours}heure(s) {$nbMinutes}minute(s)";
    }
    }