<?php

namespace App\Controller;

use App\Entity\Plantes;
use App\Service\PlantApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class PlantSearchController extends AbstractController
{
    #[Route('/admin/plantes/recherche', name: 'app_admin_plant_search')]
    public function search(Request $request, PlantApiService $plantApiService): Response
    {
        $query = $request->query->get('q', '');
        $results = [];

        if (!empty($query)) {
            $results = $plantApiService->searchPlants($query);
        }

        return $this->render('plant_search/index.html.twig', [
            'query' => $query,
            'results' => $results,
        ]);
    }

    #[Route('/admin/plantes/importer', name: 'app_admin_plant_import', methods: ['POST'])]
    public function import(Request $request, EntityManagerInterface $em): Response
    {
        $nom = $request->request->get('nom');
        $image = $request->request->get('image');
        $description = $request->request->get('description');

        $isAjax = $request->isXmlHttpRequest() || $request->headers->get('Accept') === 'application/json';

        if ($nom && $image && $description) {
            $plante = new Plantes();
            $plante->setNom($nom);
            $plante->setImage($image);
            $plante->setDescription($description);

            $em->persist($plante);
            $em->flush();

            if ($isAjax) {
                return new JsonResponse([
                    'success' => true,
                    'message' => 'La plante "' . $nom . '" a été ajoutée au catalogue !'
                ]);
            }

            $this->addFlash('success', 'La plante "' . $nom . '" a été ajoutée au catalogue !');
        } elseif ($isAjax) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Données d\'importation incomplètes.'
            ], Response::HTTP_BAD_REQUEST);
        }

        $query = $request->request->get('q', '');
        if (!empty($query)) {
            return $this->redirectToRoute('app_admin_plant_search', ['q' => $query]);
        }

        return $this->redirectToRoute('app_admin_dashboard');
    }
}
