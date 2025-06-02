<?php
declare(strict_types=1);

namespace App\Tests\Helper;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpKernel\KernelInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\net\exelearning\Entity\User;

class TestDatabaseHelper
{
    /**
     * Override database configuration to use in-memory SQLite.
     */
    public static function useInMemoryDatabase(): void
    {
        putenv('DATABASE_URL=sqlite:///:memory:');
        $_ENV['DATABASE_URL'] = 'sqlite:///:memory:';
        $_SERVER['DATABASE_URL'] = 'sqlite:///:memory:';
    }

    /**
     * Reset the database schema.
     *
     * @param KernelInterface $kernel
     */
    public static function resetDatabase(KernelInterface $kernel): void
    {
        $application = new Application($kernel);
        $application->setAutoExit(false);

        // Drop the database if it exists
        $dropInput = new ArrayInput([
            'command'      => 'doctrine:database:drop',
            '--force'      => true,
            '--if-exists'  => true,
        ]);
        $application->run($dropInput, new NullOutput());

        // Create the database
        $createInput = new ArrayInput([
            'command' => 'doctrine:database:create',
        ]);
        $application->run($createInput, new NullOutput());

        // Create the schema
        $schemaInput = new ArrayInput([
            'command' => 'doctrine:schema:create',
        ]);
        $application->run($schemaInput, new NullOutput());
    }

    /**
     * Create a user if it does not exist.
     *
     * @param EntityManagerInterface $em
     * @param string|null $email
     * @param string|null $userId
     * @param string|null $password
     *
     * @return User
     */
    public static function createUser(
        EntityManagerInterface $em,
        ?string $email = null,
        ?string $userId = null,
        ?string $password = null
    ): User {
        $email = $email ?? ($_ENV['TEST_USER_EMAIL'] ?? 'default@example.com');
        $userId   = $userId   ?? (($_ENV['TEST_USER_USERNAME'] ?? 'default') . uniqid());
        $password = $password ?? ($_ENV['TEST_USER_PASSWORD'] ?? '1234');

        $userRepository = $em->getRepository(User::class);
        $user = $userRepository->findOneBy(['email' => $email]);
        if ($user) {
            return $user;
        }

        $user = new User();
        $user->setEmail($email);
        $user->setUserId($userId);
        $user->setPassword($password);
        $user->setIsLopdAccepted(true);
        $em->persist($user);
        $em->flush();

        return $user;
    }
}
