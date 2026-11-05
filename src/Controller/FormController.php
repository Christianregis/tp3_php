<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class FormController extends AbstractController
{
    private const FORM_TEMPLATE = 'tuteur/nouveau.html.twig';

    private const COMPETENCES = [
        'web' => 'Développement web',
        'java' => 'Java',
        'devops' => 'DevOps',
        'cyber' => 'Cybersécurité',
        'data' => 'Data / IA',
    ];
    private const LANGUES = [
        'fr' => 'Français',
        'en' => 'Anglais'
    ];

    private const SECTEURS = [
        'sante' => 'Santé',
        'service_numerique' => 'Service Numérique',
        'banque_assurance' => 'Banque/Assurance',
        'industrie' => 'Industrie',
        'administration' => 'Administration'
    ];
    private const CIVILITES = ['M', 'Mme'];




    #[Route('/tuteur/nouveau', methods: ['GET'], name: 'tuteur_nouveau')]
    public function nouveau()
    {

        $competences = self::COMPETENCES;
        $langues = self::LANGUES;
        $secteurs = self::SECTEURS;
        $civilites = self::CIVILITES;

        return $this->render(self::FORM_TEMPLATE, [
            'erreurs' => [],
            'valeurs' => [],
            'success' => null,
            'competences' => $competences,
            'langues' => $langues,
            'secteurs' => $secteurs,
            'civilites' => $civilites
        ]);
    }

    #[Route('/tuteur/enregistrer', methods: ['POST'], name: 'tuteur_enregistrer')]
    public function enregistrer(Request $request)
    {
        // Si je veux récupérer les données du formulaire, je peux utiliser $request->query->get() (En GET) et $request->request->all() (En POST) pour les champs multiples (checkboxes, selects multiples).

        $nom = $request->request->get('nom');
        $prenom = $request->request->get('prenom');
        $email = $request->request->get('email');
        $civilite = $request->request->get('civilite');
        $secteur = $request->request->get('secteur');
        $date = $request->request->get('date');
        $maxStagiaires = $request->request->get('maxStagiaires');
        $comment = $request->request->get('comment');
        $competences = $request->request->all('competences');
        $langues = $request->request->all('langues');

        $erreurs = [];
        $valeurs = [
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'civilite' => $civilite,
            'secteur' => $secteur,
            'date' => $date,
            'comment' => $comment,
            'competences' => $competences,
            'langues' => $langues
        ];
        if (empty($nom) || empty($prenom) || empty($email) || empty($civilite) || empty($secteur) || $maxStagiaires < 1 || $maxStagiaires > 5 || empty($comment)) {

            if (empty($nom)) {
                $erreurs['nom'] = 'Le nom est requis.';
            }
            if (empty($prenom)) {
                $erreurs['prenom'] = 'Le prénom est requis.';
            }
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $erreurs['email'] = 'L\'email est requis.';
            }
            if (empty($civilite) || !in_array($civilite, self::CIVILITES, true)) {
                $erreurs['civilite'] = 'La civilité est requise.';
            }
            if (empty($secteur) || !in_array($secteur, self::SECTEURS, true)) {
                $erreurs['secteur'] = 'Le secteur est requis.';
            }
            if ($maxStagiaires < 1 || $maxStagiaires > 5) {
                $erreurs['maxStagiaires'] = 'Le nombre de stagiaires doit être compris entre 1 et 5.';
            }
            if (empty($comment)) {
                $erreurs['comment'] = 'Le commentaire est requis.';
            }


            return $this->render(self::FORM_TEMPLATE, [
                'erreurs' => $erreurs,
                'success' => null,
                'valeurs' => $valeurs,
                'competences' => self::COMPETENCES,
                'langues' => self::LANGUES,
                'secteurs' => self::SECTEURS,
                'civilites' => self::CIVILITES
            ]);
        }
        return $this->render(self::FORM_TEMPLATE, [
            'success' => 'Tuteur enregistré avec succès !',
            'valeurs' => $valeurs,
            'erreurs' => [],
            'competences' => self::COMPETENCES,
            'langues' => self::LANGUES,
            'secteurs' => self::SECTEURS,
            'civilites' => self::CIVILITES
        ]);
    }

    #[Route('/search_tuteur', methods: ['GET'])]
    public function searchTuteur(): Response
    {
        $caseCompetences = '';
        foreach (self::COMPETENCES as $code => $libelle) {
            $coche = in_array($code, $valeurs['competences'] ?? [], true) ? 'checked' : '';
            $caseCompetences .= '<label><input type="checkbox" name="competences[]" value="' . $code . '" ' . $coche . '> ' . $libelle . '</label><br>';
        }
        $caseLangues = '';
        foreach (self::LANGUES as $key => $value) {
            $selected = in_array($key, $valeurs['langues'] ?? [], true) ? 'selected' : '';
            $caseLangues .= '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
        }
        $html = <<<HTML
        <!DOCTYPE html>
        <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <title>Recherche de tuteur</title>
            </head>
            <body>

                <h1>Recherche de tuteur</h1>
                <form action="/search_tuteur" method="post">
                    <label>Nom : <input type="text" name="nom" required></label><br><br>
                    <label>Competences</label><br>
                    $caseCompetences
                    <select name="langues[]" multiple size="5">
                        $caseLangues
                    </select><br>
                    <button type="submit">Rechercher</button>
                </form>
            </body>
        </html>
        HTML;
        return new Response($html);
    }

    #[Route('/search_tuteur', methods: ['POST'])]
    public function verify(Request $request): Response
    {

        $tuteurs = [
            [
                'nom' => 'Johnson',
                'prenom' => 'Paul',
                'competences' => ['java', 'devops'],
                'langues' => ['fr', 'en']
            ],
            [
                'nom' => 'Walberg',
                'prenom' => 'Mark',
                'competences' => ['web', 'cyber'],
                'langues' => ['en']
            ],
            [
                'nom' => 'Le Gall',
                'prenom' => 'Maëlle',
                'competences' => ['data', 'java', 'cyber'],
                'langues' => ['fr', 'br', 'en']
            ],
        ];


        $nom = $request->request->get('nom');
        $competences = $request->request->all('competences');

        $langues = $request->request->all('langues');

        $resultats = [];
        foreach ($tuteurs as $tuteur) {
            if (
                stripos($tuteur['nom'], $nom) !== false &&
                empty(array_diff($competences, $tuteur['competences'])) &&
                !empty(array_intersect($langues, $tuteur['langues']))
            ) {
                $resultats[] = $tuteur;
            }
        }

        $reponse = empty($resultats) ? 'Aucun tuteur trouvé.' : count($resultats) . ' tuteur(s) trouvé(s) :';
        $html = <<<HTML
        <!DOCTYPE html>
        <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <title>Resultats de recherche</title>
            </head>
            <body>
                <h1>Resultats de recherche : $reponse</h1>
                    
            </body>
        </html>
        HTML;
        return new Response($html);
    }
}
