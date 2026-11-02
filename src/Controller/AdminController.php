<?php

namespace App\Controller;

use App\Repository\PlantesRepository;
use App\Repository\UserPlanteRepository;
use App\Repository\UserRepository;
use App\Entity\Plantes;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('', name: 'app_admin_dashboard')]
    public function index(UserRepository $userRepo, PlantesRepository $plantesRepo, UserPlanteRepository $userPlanteRepo): Response
    {
        return $this->render('admin/index.html.twig', [
            'totalUsers' => $userRepo->count([]),
            'totalPlantes' => $plantesRepo->count([]),
            'totalUserPlantes' => $userPlanteRepo->count([]),
        ]);
    }

    #[Route('/plantes', name: 'app_admin_plantes_index')]
    public function listPlantes(PlantesRepository $plantesRepo): Response
    {
        return $this->render('admin/plantes_list.html.twig', [
            'plantes' => $plantesRepo->findAll(),
        ]);
    }

    #[Route('/plantes/{id}/modifier', name: 'app_admin_plante_edit', methods: ['GET', 'POST'])]
    public function editPlante(Plantes $plante, Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $nom = $request->request->get('nom');
            $image = $request->request->get('image');
            $description = $request->request->get('description');

            if ($nom && $image && $description) {
                $plante->setNom($nom);
                $plante->setImage($image);
                $plante->setDescription($description);

                $em->flush();
                $this->addFlash('success', 'Plante mise à jour avec succès !');

                return $this->redirectToRoute('app_admin_plantes_index');
            }
        }

        return $this->render('admin/plante_edit.html.twig', [
            'plante' => $plante,
        ]);
    }

    #[Route('/plantes/{id}/supprimer', name: 'app_admin_plante_delete', methods: ['POST'])]
    public function deletePlante(Plantes $plante, EntityManagerInterface $em): Response
    {
        $em->remove($plante);
        $em->flush();

        $this->addFlash('success', 'Plante supprimée du catalogue.');

        return $this->redirectToRoute('app_admin_plantes_index');
    }

    // Routes API JSON

    #[Route('/api/stats', name: 'app_admin_api_stats', methods: ['GET'])]
    public function apiStats(UserRepository $userRepo, PlantesRepository $plantesRepo, UserPlanteRepository $userPlanteRepo): JsonResponse
    {
        return $this->json([
            'status' => 'success',
            'data' => [
                'users' => $userRepo->count([]),
                'plantes_catalogue' => $plantesRepo->count([]),
                'plantes_adoptees' => $userPlanteRepo->count([]),
            ]
        ]);
    }

    #[Route('/api/plantes', name: 'app_admin_api_plantes', methods: ['GET'])]
    public function apiPlantes(PlantesRepository $plantesRepo): JsonResponse
    {
        $plantes = $plantesRepo->findAll();
        $data = [];

        foreach ($plantes as $plante) {
            $data[] = [
                'id' => $plante->getId(),
                'nom' => $plante->getNom(),
                'description' => $plante->getDescription(),
                'image' => $plante->getImage(),
            ];
        }

        return $this->json($data);
    }
}
