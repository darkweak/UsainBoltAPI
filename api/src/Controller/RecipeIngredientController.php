<?php

namespace App\Controller;

use App\Entity\RecipeIngredient;
use App\Form\RecipeIngredientType;
use App\Repository\RecipeIngredientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/recipe/ingredient")
 */
class RecipeIngredientController extends AbstractController
{
    /**
     * @Route("/", name="recipe_ingredient_index", methods={"GET"})
     */
    public function index(RecipeIngredientRepository $recipeIngredientRepository): Response
    {
        return $this->render('recipe_ingredient/index.html.twig', [
            'recipe_ingredients' => $recipeIngredientRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="recipe_ingredient_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $recipeIngredient = new RecipeIngredient();
        $form = $this->createForm(RecipeIngredientType::class, $recipeIngredient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($recipeIngredient);
            $entityManager->flush();

            return $this->redirectToRoute('recipe_ingredient_index');
        }

        return $this->render('recipe_ingredient/new.html.twig', [
            'recipe_ingredient' => $recipeIngredient,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="recipe_ingredient_show", methods={"GET"})
     */
    public function show(RecipeIngredient $recipeIngredient): Response
    {
        return $this->render('recipe_ingredient/show.html.twig', [
            'recipe_ingredient' => $recipeIngredient,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="recipe_ingredient_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, RecipeIngredient $recipeIngredient): Response
    {
        $form = $this->createForm(RecipeIngredientType::class, $recipeIngredient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('recipe_ingredient_index');
        }

        return $this->render('recipe_ingredient/edit.html.twig', [
            'recipe_ingredient' => $recipeIngredient,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="recipe_ingredient_delete", methods={"DELETE"})
     */
    public function delete(Request $request, RecipeIngredient $recipeIngredient): Response
    {
        if ($this->isCsrfTokenValid('delete'.$recipeIngredient->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($recipeIngredient);
            $entityManager->flush();
        }

        return $this->redirectToRoute('recipe_ingredient_index');
    }
}
