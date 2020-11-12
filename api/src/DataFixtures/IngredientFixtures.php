<?php

namespace App\DataFixtures;

use App\Entity\Ingredient;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class IngredientFixtures extends Fixture
{
    public const REFERENCES = 'INGREDIENT_';

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 10; $i++) {
            $ingredient = new Ingredient();
            $ingredient->setName(\sprintf('Ingredient %d', $i));
            $manager->persist($ingredient);
            $manager->flush();
            $this->addReference(\sprintf('%s%d', self::REFERENCES, $i), $ingredient);
        }
    }
}
