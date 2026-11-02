<?php

namespace App\Controller;

use App\Entity\Plantes;
use App\Entity\UserPlante;
use App\Repository\PlantesRepository;
use App\Repository\UserPlanteRepository;
use App\Entity\UserPlanteNote;
use App\Entity\UserPlantePhoto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\Request;

#[IsGranted('ROLE_USER')]
class UserPlanteController extends AbstractController
{
    #[Route('/mes-plantes', name: 'app_user_plante_index')]
    public function index(
        UserPlanteRepository $userPlanteRepo,
        PlantesRepository $plantesRepo,
        Request $request
    ): Response {
        $user = $this->getUser();
        $search = trim($request->query->get('q', ''));

        // Si une recherche est effectuée, on filtre, sinon on prend tout
        if (!empty($search)) {
            $catalogue = $plantesRepo->findBySearch($search);
        } else {
            $catalogue = $plantesRepo->findAll();
        }

        return $this->render('user_plante/index.html.twig', [
            'mesPlantes' => $userPlanteRepo->findBy(['user' => $user]),
            'catalogue' => $catalogue,
            'search' => $search,
        ]);
    }

    #[Route('/mes-plantes/ajouter/{id}', name: 'app_user_plante_add', methods: ['POST'])]
    public function add(Plantes $plante, EntityManagerInterface $em): Response
    {
        $userPlante = new UserPlante();
        $userPlante->setUser($this->getUser());
        $userPlante->setPlante($plante);
        $userPlante->setDernierArrosage(new \DateTimeImmutable());

        $em->persist($userPlante);
        $em->flush();

        $this->addFlash('success', 'Plante ajoutée à votre collection !');

        return $this->redirectToRoute('app_user_plante_index');
    }

    #[Route('/mes-plantes/arroser/{id}', name: 'app_user_plante_water', methods: ['POST'])]
    public function water(UserPlante $userPlante, EntityManagerInterface $em): Response
    {
        // Vérification de sécurité : l'utilisateur ne peut arroser que ses propres plantes
        if ($userPlante->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $userPlante->setDernierArrosage(new \DateTimeImmutable());
        $em->flush();

        $this->addFlash('success', 'Plante arrosée avec succès !');

        return $this->redirectToRoute('app_user_plante_index');
    }

    #[Route('/mes-plantes/supprimer/{id}', name: 'app_user_plante_delete', methods: ['POST'])]
    public function delete(UserPlante $userPlante, EntityManagerInterface $em): Response
    {
        if ($userPlante->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $em->remove($userPlante);
        $em->flush();

        $this->addFlash('success', 'Plante retirée de votre jardin.');

        return $this->redirectToRoute('app_user_plante_index');
    }

    #[Route('/mes-plantes/{id}', name: 'app_user_plante_show', methods: ['GET', 'POST'])]
    public function show(
        UserPlante $userPlante,
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger
    ): Response {
        if ($userPlante->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($request->isMethod('POST') && $request->request->has('update_info')) {
            $surnom = trim($request->request->get('surnom', ''));
            $emplacement = trim($request->request->get('emplacement', ''));

            $userPlante->setSurnom(!empty($surnom) ? $surnom : null);

            if (method_exists($userPlante, 'setEmplacement')) {
                $userPlante->setEmplacement(!empty($emplacement) ? $emplacement : null);
            }

            $em->flush();

            $this->addFlash('success', 'Informations de la plante mises à jour !');
            return $this->redirectToRoute('app_user_plante_show', ['id' => $userPlante->getId()]);
        }

        if ($request->isMethod('POST') && $request->request->has('frequence_arrosage')) {
            $frequence = (int) $request->request->get('frequence_arrosage');
            if ($frequence > 0) {
                $userPlante->setFrequenceArrosage($frequence);
                $em->flush();

                $this->addFlash('success', 'Fréquence d\'arrosage mise à jour !');
                return $this->redirectToRoute('app_user_plante_show', ['id' => $userPlante->getId()]);
            }
        }

        if ($request->isMethod('POST') && $request->request->has('note_content')) {
            $content = trim($request->request->get('note_content'));
            if (!empty($content)) {
                $note = new UserPlanteNote();
                $note->setContent($content);
                $note->setCreatedAt(new \DateTimeImmutable());
                $note->setUserPlante($userPlante);

                $em->persist($note);
                $em->flush();

                $this->addFlash('success', 'Note ajoutée au journal !');
                return $this->redirectToRoute('app_user_plante_show', ['id' => $userPlante->getId()]);
            }
        }

        if ($request->isMethod('POST') && $request->files->has('photo_file')) {
            $file = $request->files->get('photo_file');

            if ($file && $file->isValid()) {
                $uploadsDir = $this->getParameter('kernel.project_dir') . '/public/uploads/plantes';

                if (!file_exists($uploadsDir)) {
                    mkdir($uploadsDir, 0777, true);
                }

                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $extension = $file->guessExtension() ?? 'jpg';
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $extension;

                try {
                    $file->move($uploadsDir, $newFilename);

                    $photo = new UserPlantePhoto();
                    $photo->setImagePath('uploads/plantes/' . $newFilename);
                    $photo->setCreatedAt(new \DateTimeImmutable());
                    $photo->setUserPlante($userPlante);

                    $em->persist($photo);
                    $em->flush();

                    $this->addFlash('success', 'Nouvelle photo ajoutée à la galerie !');
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Erreur lors du déplacement de l\'image.');
                }
            } else {
                $this->addFlash('danger', 'Le fichier envoyé est invalide ou dépasse la taille maximale autorisée.');
            }

            return $this->redirectToRoute('app_user_plante_show', ['id' => $userPlante->getId()]);
        }

        return $this->render('user_plante/show.html.twig', [
            'userPlante' => $userPlante,
        ]);
    }
    #[Route('/mes-plantes/photo/{id}/supprimer', name: 'app_user_plante_photo_delete', methods: ['POST'])]
    public function deletePhoto(UserPlantePhoto $photo, EntityManagerInterface $em): Response
    {
        $userPlante = $photo->getUserPlante();

        if ($userPlante->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        // Suppression du fichier physique sur le serveur
        $filePath = $this->getParameter('kernel.project_dir') . '/public/' . $photo->getImagePath();
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $em->remove($photo);
        $em->flush();

        $this->addFlash('success', 'Photo supprimée de la galerie.');

        return $this->redirectToRoute('app_user_plante_show', ['id' => $userPlante->getId()]);
    }

    #[Route('/mes-plantes/note/{id}/supprimer', name: 'app_user_plante_note_delete', methods: ['POST'])]
    public function deleteNote(UserPlanteNote $note, EntityManagerInterface $em): Response
    {
        $userPlante = $note->getUserPlante();

        if ($userPlante->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $em->remove($note);
        $em->flush();

        $this->addFlash('success', 'Note supprimée du journal.');

        return $this->redirectToRoute('app_user_plante_show', ['id' => $userPlante->getId()]);
    }
}
