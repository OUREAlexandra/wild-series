<?php

namespace App\DataFixtures;

use App\Entity\Episode;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Service\Slugify;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{
    private $slugify;

    public function __construct(Slugify $slugify)
    {
        $this->slugify = $slugify;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        for ($i = 0; $i < 80; $i++) {
            $episode = new Episode();
            $episode->setTitle($faker->sentence($nbWords = 6, $variableNbWords = true));
            $episode->setNumber($faker->numberBetween(1, 12));
            $episode->setSynopsis($faker->text($maxNbChars = 200));
            $this->setReference('episode_' . $i, $episode);
            $title = $this->slugify->generate($episode->getTitle());
            $episode->setSlug($title);
            $manager->persist($episode);
            $episode->setSeason($this->getReference('season_' . rand(0, 5)));
        }

        $manager->flush();
    }

    public function getDependencies()  
    {
        return [SeasonFixtures::class];  
    }
}