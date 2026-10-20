<?php

namespace App\Service;

class TuteurProvider
{
    private const TUTEURS = [
        [
            'id' => 1,
            'nom' => 'Johnson',
            'prenom' => 'Paul',
            'entreprise' => 'Acme',
            'email' => 'paul.johnson@acme.com',
            'telephone' => '06 00 00 00 01',
            'competences' => ['java', 'devops'],
            'etudiants' => [
                [
                    'nom' => 'Martin',
                    'prenom' => 'Léa',
                    'sujet' => 'Détection d\'anomalies sur flux bancaires'
                ],
                [
                    'nom' => 'Durand',
                    'prenom' => 'Noah',
                    'sujet' => 'Dashboard risques crédit'
                ],
            ],
        ],
        [
            'id' => 2,
            'nom' => 'Walberg',
            'prenom' => 'Mark',
            'entreprise' => 'Globex',
            'email' => 'mark.walberg@globex.com',
            'telephone' => '06 00 00 00 02',
            'competences' => ['web', 'cyber'],
            'etudiants' => [],
        ],
        [
            'id' => 3,
            'nom' => 'Le Gall',
            'prenom' => 'Maëlle',
            'entreprise' => 'Acme',
            'email' => 'maelle.legall@acme.com',
            'telephone' => '',
            'competences' => ['data', 'java', 'cyber'],
            'etudiants' => [
                [
                    'nom' => 'Kerboul',
                    'prenom' => 'Erwan',
                    'sujet' => 'Pipeline MLOps pour la prévision de la demande'
                ],
            ],
        ],
    ];

    public function findAll(): array
    {
        return self::TUTEURS;
    }

    public function find(int $id): ?array
    {
        foreach (self::TUTEURS as $tuteur) {
            if ($tuteur['id'] === $id) {
                return $tuteur;
            }
        }
        return null;
    }
}
