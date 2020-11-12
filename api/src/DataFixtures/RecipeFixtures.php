<?php

namespace App\DataFixtures;

use App\Entity\Recipe;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class RecipeFixtures extends Fixture implements DependentFixtureInterface
{
    public const REFERENCES = 'RECIPES_';

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 10; $i++) {
            $recipe = new Recipe();
            $recipe->setName(\sprintf('Recipe %d', $i));
            $recipe->setAuthor($this->getReference(\sprintf('%s%d', UserFixtures::REFERENCES, $i)));
            $manager->persist($recipe);
            $manager->flush();
            $this->addReference(\sprintf('%s%d', self::REFERENCES, $i), $recipe);
        }
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
