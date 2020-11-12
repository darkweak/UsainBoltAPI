<?php

namespace App\DataFixtures;

use App\Entity\RecipeIngredient;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class RecipeIngredientFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 10; $i++) {
            $recipeIngredient = new RecipeIngredient();
            $recipeIngredient->setQuantity($i);
            $recipeIngredient->setIngredient($this->getReference(\sprintf('%s%d', IngredientFixtures::REFERENCES, $i)));
            $recipeIngredient->setRecipe($this->getReference(\sprintf('%s%d', RecipeFixtures::REFERENCES, $i)));
            $recipeIngredient->setUnit($this->getReference(\sprintf('%s%d', UnitFixtures::REFERENCES, $i)));
            $manager->persist($recipeIngredient);
            $manager->flush();
        }
    }

    public function getDependencies()
    {
        return [
            IngredientFixtures::class,
            RecipeFixtures::class,
            UnitFixtures::class,
        ];
    }
}
