<?php

namespace App\DataFixtures;

use App\Entity\Unit;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UnitFixtures extends Fixture
{
    public const REFERENCES = 'UNIT_';

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 10; $i++) {
            $unit = new Unit();
            $unit->setName(\sprintf('Unit %d', $i));
            $manager->persist($unit);
            $manager->flush();
            $this->addReference(\sprintf('%s%d', self::REFERENCES, $i), $unit);
        }
    }
}
