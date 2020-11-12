<?php

namespace App\Controller;

use App\Entity\Step;
use App\Form\StepType;
use App\Repository\StepRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/step")
 */
class StepController extends AbstractController
{
    /**
     * @Route("/", name="step_index", methods={"GET"})
     */
    public function index(StepRepository $stepRepository): Response
    {
        return $this->render('step/index.html.twig', [
            'steps' => $stepRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="step_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $step = new Step();
        $form = $this->createForm(StepType::class, $step);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($step);
            $entityManager->flush();

            return $this->redirectToRoute('step_index');
        }

        return $this->render('step/new.html.twig', [
            'step' => $step,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="step_show", methods={"GET"})
     */
    public function show(Step $step): Response
    {
        return $this->render('step/show.html.twig', [
            'step' => $step,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="step_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Step $step): Response
    {
        $form = $this->createForm(StepType::class, $step);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('step_index');
        }

        return $this->render('step/edit.html.twig', [
            'step' => $step,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="step_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Step $step): Response
    {
        if ($this->isCsrfTokenValid('delete'.$step->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($step);
            $entityManager->flush();
        }

        return $this->redirectToRoute('step_index');
    }
}
