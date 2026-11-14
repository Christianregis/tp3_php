<?php

namespace App\Command;

use App\Entity\Profil;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:charger-profils',
    description: 'Creation de profils de test.',
)]
class ChargerProfilsCommand extends Command
{

    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $donnees = [
            ["Lune29", "1998-05-12", "Brest", "Cinéma et randonnée."],
            ["Pixel35", "1995-11-03", "Rennes", "Jeux et cuisine."],
            ["Maree29", "2000-02-18", "Brest", "Musique et voile."],
            ["Escapade", "1993-08-21", "Quimper", "Voyages et lecture."],
        ];
        $repo = $this->em->getRepository(Profil::class);
        foreach ($donnees as [$pseudo, $date, $ville, $description]) {
            if ($repo->findOneBy(["pseudo" => $pseudo])) {
                continue;
            }
            $profil = new Profil();
            $profil->setPseudo($pseudo);
            $profil->setDateNaissance(new \DateTimeImmutable($date));
            $profil->setVille($ville);
            $profil->setDescription($description);
            $this->em->persist($profil);
            
        }
        $this->em->flush();
        $output->writeln("Profils enregistrés.");
        return Command::SUCCESS;
    }
}
