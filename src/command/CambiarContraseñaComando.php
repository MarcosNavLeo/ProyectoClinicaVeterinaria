<?php
namespace App\command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CambiarContraseñaComando extends Command
{
    protected static $defaultName = 'app:change-password';

    private $entityManager;
    private $passwordHasher;

    // Actualiza el constructor para usar UserPasswordHasherInterface
    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    protected function configure()
    {
        $this
            ->setDescription('Cambia la contraseña de un usuario administrador.')
            ->addArgument('username', InputArgument::REQUIRED, 'El nombre de usuario del administrador.')
            ->addArgument('newPassword', InputArgument::REQUIRED, 'La nueva contraseña.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $username = $input->getArgument('username');
        $newPassword = $input->getArgument('newPassword');
    
        // Validación básica de la contraseña
        if (strlen($newPassword) < 4) {
            $output->writeln('La contraseña debe tener al menos 4 caracteres.');
            return Command::FAILURE;
        }
    
        try {
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $username]);
    
            if (!$user) {
                $output->writeln('Usuario no encontrado.');
                return Command::FAILURE;
            }
    
            $hashedPassword = $this->passwordHasher->hashPassword($user, $newPassword);
            $user->setPassword($hashedPassword);
    
            $this->entityManager->flush();
    
            $output->writeln('Contraseña cambiada con éxito.');
    
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('Ocurrió un error al cambiar la contraseña: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}