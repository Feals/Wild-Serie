<?php

namespace App\DataFixtures;

use App\Entity\Episode;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;
use Symfony\Component\String\Slugger\SluggerInterface;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{
    public const EPISODES_PER_SEASON = 10;

    public function __construct(private SluggerInterface $slugger)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = FakerFactory::create();

        $totalSeasons = count(ProgramFixtures::PROGRAMS) * SeasonFixtures::SEASONS_PER_PROGRAM;

        for ($seasonIndex = 0; $seasonIndex < $totalSeasons; $seasonIndex++) {
            for ($episodeIndex = 0; $episodeIndex < self::EPISODES_PER_SEASON; $episodeIndex++) {
                $episode = new Episode();
                $episode->setNumber($episodeIndex + 1);
                $episode->setTitle($faker->words($faker->numberBetween(1, 6), true));
                $episode->setSlug($this->slugger->slug($episode->getTitle()));
                $episode->setSynopsis($faker->paragraphs(3, true));
                $episode->setDuration($faker->randomElement([20, 45, 60]), true);
                $episode->setSeason($this->getReference('season_' . $seasonIndex));

                $manager->persist($episode);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            SeasonFixtures::class,
        ];
    }
}
