<?php

namespace App\Controller;

use App\Service\TuteurProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class SujetController extends AbstractController
{
    #[Route('/sujets', name: 'sujet_index')]
    public function index(TuteurProvider $tuteurProvider)
    {
        $sujets = [];
        $tuteurs = $tuteurProvider->findAll();
        foreach ($tuteurs as $tuteur) {
            foreach ($tuteur['etudiants'] as $etudiant) {
                $sujet = [
                    'id_tuteur' => $tuteur['id'],
                    'nom_etudiant' => $etudiant['nom'],
                    'nom_tuteur' => $tuteur['nom'],
                    'sujet' => $etudiant['sujet'],
                    'entreprise' => $tuteur['entreprise'],
                ];
                $sujets = [...$sujets, $sujet];
            }
        }

        return $this->render('sujet/index.html.twig', [
            'sujets' => $sujets,
        ]);
    }
}
