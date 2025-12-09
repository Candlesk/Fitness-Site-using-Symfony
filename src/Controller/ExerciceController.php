<?php

namespace App\Controller;

use App\Entity\Exercise;
use App\Form\ExerciseType;
use App\Repository\ExerciseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/exercice')]
final class ExerciceController extends AbstractController
{
    #[Route(name: 'app_exercice_index', methods: ['GET'])]
    public function index(ExerciseRepository $exerciseRepository): Response
    {
        return $this->render('exercice/index.html.twig', [
            'exercises' => $exerciseRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_exercice_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $exercise = new Exercise();
        $form = $this->createForm(ExerciseType::class, $exercise);
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
                $exercise->setImage($newFilename);
            }
            
            // Set timestamps
            $exercise->setCreatedAt(new \DateTime());
            $exercise->setUpdatedAt(new \DateTime());
            
            $entityManager->persist($exercise);
            $entityManager->flush();

            $this->addFlash('success', 'Exercise created successfully!');

            return $this->redirectToRoute('app_exercice_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('exercice/new.html.twig', [
            'exercise' => $exercise,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_exercice_show', methods: ['GET'])]
    public function show(Exercise $exercise): Response
    {
        return $this->render('exercice/show.html.twig', [
            'exercise' => $exercise,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_exercice_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Exercise $exercise, EntityManagerInterface $entityManager): Response
    {
        // Store the old image filename in case we need to delete it
        $oldImage = $exercise->getImage();
        
        $form = $this->createForm(ExerciseType::class, $exercise);
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
                $exercise->setImage($newFilename);
                
                // Delete old image file if it exists
                if ($oldImage) {
                    $oldImagePath = $this->getParameter('kernel.project_dir').'/public/uploads/'.$oldImage;
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
            }
            
            // Update the timestamp
            $exercise->setUpdatedAt(new \DateTime());
            
            $entityManager->flush();

            $this->addFlash('success', 'Exercise updated successfully!');

            return $this->redirectToRoute('app_exercice_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('exercice/edit.html.twig', [
            'exercise' => $exercise,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_exercice_delete', methods: ['POST'])]
    public function delete(Request $request, Exercise $exercise, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$exercise->getId(), $request->getPayload()->getString('_token'))) {
            // Delete the image file if it exists
            $imageFilename = $exercise->getImage();
            if ($imageFilename) {
                $imagePath = $this->getParameter('kernel.project_dir').'/public/uploads/'.$imageFilename;
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            
            $entityManager->remove($exercise);
            $entityManager->flush();
            
            $this->addFlash('success', 'Exercise deleted successfully!');
        }

        return $this->redirectToRoute('app_exercice_index', [], Response::HTTP_SEE_OTHER);
    }
}