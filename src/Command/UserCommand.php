<?php

namespace App\Command;

use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;


class UserCommand extends Command
{
    protected static $defaultName = 'app:delete-user';
    /**
     * @var SymfonyStyle
     */
    private $io;

    private $entityManager;
    private $userRepository;
    public function __construct(EntityManagerInterface $em, UserRepository $userRepository)
    {
        parent::__construct();

        $this->entityManager = $em;
        $this->userRepository = $userRepository;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Supprime les utilisateurs qui ont une date de sortie passé a la date actuelle')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $users= $this->userRepository->findall();
        foreach($users as $user){
            $dateDeSortie =$user->getDateDeSortie();
            $day = new DateTime('now');
            if($dateDeSortie){
                if($dateDeSortie<=$day->format('Y-m-d')){
                    $this->entityManager->remove($user);
                    $this->entityManager->flush();
                    $output->writeln('Et hop une personne qui n\est plus ici supprimer');
                }
            }
        }

        return Command::SUCCESS;
    }
}
