<?php

namespace App\Command\net\exelearning\Command;

use App\Entity\net\exelearning\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Creates a new user.
 */
#[AsCommand(
    name: 'app:create-user',
    description: 'Creates a new user.',
    hidden: false,
    aliases: ['app:add-user']
)]
class CreateUserCommand extends Command
{
    private $entityManager;
    private $passwordHasher;

    /**
     * Constructor.
     */
    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;

        parent::__construct();
    }

    /**
     * Configures the command options.
     *
     * {@inheritDoc}
     *
     * @see \Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'The email of the user.')
            ->addArgument('password', InputArgument::REQUIRED, 'The password of the user.')
            ->addArgument('user_id', InputArgument::REQUIRED, 'The user ID.')
            ->addOption('no-fail', null, InputOption::VALUE_NONE, 'If set, the command will not fail if the user already exists.');
    }

    /**
     * Executes the command.
     *
     * {@inheritDoc}
     *
     * @see \Symfony\Component\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $userId = $input->getArgument('user_id');
        $noFail = $input->getOption('no-fail');
        $isLopdAccepted = true;

        // Check if the user already exists
        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($existingUser) {
            $output->writeln('<error>User with email '.$email.' already exists!</error>');

            return $noFail ? Command::SUCCESS : Command::FAILURE;
        }

        // Create a new User entity
        $user = new User();
        $user->setEmail($email);
        $user->setUserId($userId);
        $user->setIsLopdAccepted($isLopdAccepted);

        // Hash the password and set it
        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        // Persist the new user entity to the database
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Output success message
        $output->writeln('<info>User successfully created!</info>');

        return Command::SUCCESS;
    }
}
