<?php

namespace App\DataFixtures;

use App\Entity\Step;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class StepFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 10; $i++) {
            $step = new Step();
            $step->setAction(\sprintf('Action %d', $i));
            $step->addIngredient($this->getReference(\sprintf('%s%d', IngredientFixtures::REFERENCES, $i)));
            $step->setRecipe($this->getReference(\sprintf('%s%d', RecipeFixtures::REFERENCES, $i)));
            $manager->persist($step);
            $manager->flush();
        }
    }

    public function getDependencies()
    {
        return [
            RecipeFixtures::class,
            IngredientFixtures::class,
        ];
    }
}
