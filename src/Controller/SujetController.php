<?php

namespace App\Controller;

use App\Service\TuteurProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

class SujetController extends AbstractController
{
    #[Route('/sujets', name: 'sujet_index', methods: ['GET'])]
    public function index(TuteurProvider $tuteurProvider, Request $request)
    {
        $sujets = [];
        $tuteurs = $tuteurProvider->findAll();

        $entreprise = $request->query->get('entreprise');
        foreach ($tuteurs as $tuteur) {
            foreach ($tuteur['etudiants'] as $etudiant) {
                $sujet = [
                    'id_tuteur' => $tuteur['id'],
                    'nom_etudiant' => $etudiant['nom'],
                    'nom_tuteur' => $tuteur['nom'],
                    'sujet' => $etudiant['sujet'],
                    'entreprise' => $tuteur['entreprise'],
                ];
                if (($entreprise != null && $tuteur['entreprise'] === $entreprise) || $entreprise == '') {
                    $sujets = [...$sujets, $sujet];
                }
            }
        }
        $entreprises = [];

        foreach ($tuteurs as $tuteur) {
            $entreprises = [...$entreprises, $tuteur['entreprise']];
        }

        $entreprises = array_unique($entreprises);

        return $this->render('sujet/index.html.twig', [
            'sujets' => $sujets,
            'entreprises' => $entreprises,
        ]);
    }
}
