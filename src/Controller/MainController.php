<?php
// src/Controller/MainController.php
namespace App\Controller;

use App\Service\TuteurProvider;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{


    #[Route("/bonjour/{nom}", name: 'bonjour', defaults: ['nom' => 'Inconnu'])]
    public function indexbis(string $nom)
    {
        return $this->render('main/bonjour.html.twig', [
            'nom' => $nom
        ]);
    }


    public function indexter(int $nombre)
    {
        if ($nombre >  100) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Le nombre doit être inférieur ou égal à 100.');
        }

        $resultat = $nombre * 2;
        return new Response(
            '<html><body><h1>Le resultat du calcul est : ' . $resultat . '</h1></body></html>'
        );
    }
    #[Route('/tuteurs', name: 'tuteur_index', methods: ['GET'])]
    public function index(TuteurProvider $tuteurProvider, Request $request)
    {
        $tuteurs = $tuteurProvider->findAll();

        $sort = $request->query->get('sort');
        $dir = $request->query->get('dir');

        if ($sort) {
            usort($tuteurs, function ($a, $b) use ($sort, $dir) {

                if ($sort === 'nb etudiants') {
                    $valA = count($a['etudiants'] ?? []);
                    $valB = count($b['etudiants'] ?? []);
                } else {
                    $valA = $a[$sort] ?? '';
                    $valB = $b[$sort] ?? '';
                }

                $resultat = $valA <=> $valB;

                return $dir === 'desc' ? -$resultat : $resultat;
            });
        }

        return $this->render('tuteur/index.html.twig', [
            'tuteurs' => $tuteurs,
        ]);
    }

    #[Route('tuteurs/{id}', name: 'tuteur_show', methods: ['GET'], requirements: ['nom' => '\d+'])]
    public function show(int $id, TuteurProvider $tuteurProvider)
    {
        if ($tuteurProvider->find($id) == null) {
            throw $this->createNotFoundException('Tuteur introuvable');
        }
        return $this->render('tuteur/show.html.twig', [
            "tuteur" => $tuteurProvider->find($id),
        ]);
    }
}
