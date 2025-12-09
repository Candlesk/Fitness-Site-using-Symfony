<?php

namespace App\Controller;

use App\Entity\WorkoutPlan;
use App\Form\WorkoutPlanType;
use App\Repository\WorkoutPlanRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/workout/plan')]
final class WorkoutPlanController extends AbstractController
{
    #[Route(name: 'app_workout_plan_index', methods: ['GET'])]
    public function index(WorkoutPlanRepository $workoutPlanRepository): Response
    {
        return $this->render('workout_plan/index.html.twig', [
            'workout_plans' => $workoutPlanRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_workout_plan_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $workoutPlan = new WorkoutPlan();
        $form = $this->createForm(WorkoutPlanType::class, $workoutPlan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                
                // Move the file to the uploads directory
                $imageFile->move(
                    $this->getParameter('kernel.project_dir').'/public/uploads/',
                    $newFilename
                );
                
                // Set the image filename in the entity
                $workoutPlan->setImage($newFilename);
            }
            
            // Set timestamps
            $workoutPlan->setCreatedAt(new \DateTime());
            $workoutPlan->setUpdatedAt(new \DateTime());
            
            $entityManager->persist($workoutPlan);
            $entityManager->flush();

            $this->addFlash('success', 'Workout plan created successfully!');

            return $this->redirectToRoute('app_workout_plan_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('workout_plan/new.html.twig', [
            'workout_plan' => $workoutPlan,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_workout_plan_show', methods: ['GET'])]
    public function show(WorkoutPlan $workoutPlan): Response
    {
        return $this->render('workout_plan/show.html.twig', [
            'workout_plan' => $workoutPlan,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_workout_plan_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, WorkoutPlan $workoutPlan, EntityManagerInterface $entityManager): Response
    {
        // Store the old image filename in case we need to delete it
        $oldImage = $workoutPlan->getImage();
        
        $form = $this->createForm(WorkoutPlanType::class, $workoutPlan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                // Generate new filename
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                
                // Move the file to the uploads directory
                $imageFile->move(
                    $this->getParameter('kernel.project_dir').'/public/uploads/',
                    $newFilename
                );
                
                // Set the new image filename
                $workoutPlan->setImage($newFilename);
                
                // Delete old image file if it exists
                if ($oldImage) {
                    $oldImagePath = $this->getParameter('kernel.project_dir').'/public/uploads/'.$oldImage;
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
            }
            
            // Update the timestamp
            $workoutPlan->setUpdatedAt(new \DateTime());
            
            $entityManager->flush();

            $this->addFlash('success', 'Workout plan updated successfully!');

            return $this->redirectToRoute('app_workout_plan_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('workout_plan/edit.html.twig', [
            'workout_plan' => $workoutPlan,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_workout_plan_delete', methods: ['POST'])]
    public function delete(Request $request, WorkoutPlan $workoutPlan, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$workoutPlan->getId(), $request->getPayload()->getString('_token'))) {
            // Delete the image file if it exists
            $imageFilename = $workoutPlan->getImage();
            if ($imageFilename) {
                $imagePath = $this->getParameter('kernel.project_dir').'/public/uploads/'.$imageFilename;
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            
            $entityManager->remove($workoutPlan);
            $entityManager->flush();
            
            $this->addFlash('success', 'Workout plan deleted successfully!');
        }

        return $this->redirectToRoute('app_workout_plan_index', [], Response::HTTP_SEE_OTHER);
    }
}
