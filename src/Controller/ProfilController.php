<?php

namespace App\Controller;

use App\Repository\ProfilRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProfilController extends AbstractController
{
    #[Route('/profils', name: 'profils_index', methods: ['GET'])]
    public function index(ProfilRepository $repo): Response
    {
        return $this->render('profil/index.html.twig', [
            'profils' => $repo->findBy(['actif' => true], ['pseudo' => 'ASC']),
        ]);
    }

    #[Route('/profils/{id<\d+>}', methods: ['GET'], name: 'profil_show')]
    public function show(ProfilRepository $repo, int $id)
    {
        $profil = $repo->find($id);
        if (!$profil) {
            throw new Exception('Erreur, element introuvable !');
        } else {
            return $this->render('/profil/show.html.twig', [
                'profil' => $profil,
            ]);
        }
    }
}
