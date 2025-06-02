<?php

namespace App\Tests\Command;

use App\Command\net\exelearning\Command\CreateUserCommand;
use App\Entity\net\exelearning\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateUserCommandTest extends TestCase
{
    public function testExecuteSuccessfullyCreatesUser()
    {
        $email = 'test@example.com';
        $password = 'password123';
        $userId = '12345';

        // Mock the EntityManager
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
            ->method('getRepository')
            ->willReturn($this->createMockUserRepository(null));

        $entityManager->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(User::class));

        $entityManager->expects($this->once())
            ->method('flush');

        // Mock the UserPasswordHasher
        $passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
        $passwordHasher->expects($this->once())
            ->method('hashPassword')
            ->willReturn('hashed_password');

        // Initialize the command
        $command = new CreateUserCommand($entityManager, $passwordHasher);

        // Set up the command tester
        $commandTester = new CommandTester($command);

        // Execute the command
        $commandTester->execute([
            'email' => $email,
            'password' => $password,
            'user_id' => $userId,
        ]);

        // Assert output and status code
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('User successfully created!', $output);
        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testExecuteFailsIfUserAlreadyExists()
    {
        $email = 'existing@example.com';
        $password = 'password123';
        $userId = '12345';

        // Mock the EntityManager
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
            ->method('getRepository')
            ->willReturn($this->createMockUserRepository(new User()));

        // Mock the UserPasswordHasher
        $passwordHasher = $this->createMock(UserPasswordHasherInterface::class);

        // Initialize the command
        $command = new CreateUserCommand($entityManager, $passwordHasher);

        // Set up the command tester
        $commandTester = new CommandTester($command);

        // Execute the command
        $commandTester->execute([
            'email' => $email,
            'password' => $password,
            'user_id' => $userId,
        ]);

        // Assert output and status code
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('User with email existing@example.com already exists!', $output);
        $this->assertEquals(1, $commandTester->getStatusCode());
    }

    private function createMockUserRepository($returnValue)
    {
        $repository = $this->createMock(EntityRepository::class);

        $repository->expects($this->any())
            ->method('findOneBy')
            ->willReturn($returnValue);

        return $repository;
    }
}
