<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class FormController
{
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

    private function formulaire(array $valeurs = [], array $erreurs = []): string
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

        $caseSecteurs = '';
        foreach (self::SECTEURS as $key => $value) {
            $selected = ($valeurs['secteur'] ?? null) === $key ? 'selected' : '';
            $caseSecteurs .= '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
        }

        $nomError = htmlspecialchars($erreurs['nom'] ?? '');
        $prenomError = htmlspecialchars($erreurs['prenom'] ?? '');
        $emailError = htmlspecialchars($erreurs['email'] ?? '');
        $civiliteError = htmlspecialchars($erreurs['civilite'] ?? '');
        $secteurError = htmlspecialchars($erreurs['secteur'] ?? '');
        $competencesError = htmlspecialchars($erreurs['competences'] ?? '');
        $languesError = htmlspecialchars($erreurs['langues'] ?? '');
        $maxStagiairesError = htmlspecialchars($erreurs['maxStagiaires'] ?? '');
        $commentError = htmlspecialchars($erreurs['comment'] ?? '');

        $html = <<<HTML
        <!DOCTYPE html>
        <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <title>Nouveau tuteur</title>
            </head>

            <body>
                <h1>Inscription d'un tuteur</h1>
                <p style="color: red;">$nomError</p>
                <p style="color: red;">$prenomError</p>
                <p style="color: red;">$emailError</p>
                <p style="color: red;">$civiliteError</p>
                <p style="color: red;">$secteurError</p>
                <p style="color: red;">$competencesError</p>
                <p style="color: red;">$languesError</p>
                <p style="color: red;">$maxStagiairesError</p>
                <p style="color: red;">$commentError</p>

                <form action="/tuteur/enregistrer" method="post">
                    <label>Nom : <input type="text" name="nom" required></label><br><br>
                    <label>Prénom : <input type="text" name="prenom" required></label><br><br>
                    <label>Email : <input type="email" name="email" required></label><br><br>
                    <label>Civilite</label>
                    <input id="civ-m" type="radio" value="M" name="civilite" />
                    <label for="civ-m">.M</label>
                    <input id="civ-mme" type="radio" value="Mme" name="civilite" />
                    <label for="civ-mme">.Mme</label><br><br>
                    <select name="secteur">
                        $caseSecteurs
                    </select><br><br><br>
                    <label>Competences</label><br>
                    $caseCompetences
                    <select name="langues[]" multiple size="5">
                        $caseLangues
                    </select><br>
                    <input type="number" name="maxStagiaires" min="1" max="5"/>
                    <label for="maxStagiaires">Nombre de stagiaires maximum</label><br>
                    <input type="date" name="date" />
                    <label for="date">Date de naissance</label><br> 
                    <textarea type="text" name="comment" placeholder="Commentaire"></textarea>
                    <label for="comment">Commentaire</label><br>

                    <button type="submit">Enregistrer</button>
                </form>
            </body>
        </html>
        HTML;
        return $html;
    }


    #[Route('/tuteur/nouveau', methods: ['GET'])]
    public function nouveau(): Response
    {
        $html = $this->formulaire([], []);
        return new Response($html);
    }

    #[Route('/tuteur/enregistrer', methods: ['POST'])]
    public function enregistrer(Request $request): Response
    {
        $request = Request::createFromGlobals();

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

        $resultCompetences = "";
        foreach ($competences as $competence) {
            $resultCompetences .= htmlspecialchars($competence) . ", ";
        }

        $resultLangues = "";
        foreach ($langues as $langue) {
            $resultLangues .= htmlspecialchars($langue) . ", ";
        }
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

            $html = $this->formulaire($valeurs, $erreurs);
            return new Response($html);
        }
        $secteurResult = self::SECTEURS[$secteur] ?? 'Inconnu';
        $competencesResult = "";

        foreach ($competences as $competence) {
            $competencesResult .= self::COMPETENCES[$competence] ?? 'Inconnu';
        }
        $languesResult = "";
        foreach ($langues as $langue) {
            $languesResult .= self::LANGUES[$langue] ?? 'Inconnu';
        }
        $html = <<<HTML
                <!DOCTYPE html>
                <html lang="fr">
                <head>
                    <meta charset="UTF-8">
                    <title>Formulaire</title>
                </head>
                <body>
                    <h1>Formulaire Soumis !</h1>
                    <tbody>
                    <tr>
                        <td>Nom :</td>
                        <td>$nom</td>
                    </tr>
                    <tr>
                        <td>Prenom :</td>
                        <td>$prenom</td>
                    </tr>
                    <tr>
                        <td>Email :</td>
                        <td>$email</td>
                    </tr>
                    <tr>
                        <td>Civilite :</td>
                        <td>$civilite</td>
                    </tr>
                    <tr>
                        <td>Competences :</td>
                        <td>$competencesResult</td>
                    </tr>
                    <tr>
                        <td>Langues :</td>
                        <td>$languesResult</td>
                    </tr>
                    <tr>
                        <td>Secteur :</td>
                        <td>$secteurResult</td>
                    </tr>
                    </tbody>

                </body>
                </html>
                HTML;
        return new Response($html);
    }

    #[Route('/search_tuteur', methods: ['GET'])]
    public function searchTuteur(Request $request): Response
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
