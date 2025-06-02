<?php

namespace App\Tests\Command;

use App\Command\net\exelearning\Command\DatabaseTestCommand;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class DatabaseTestCommandTest extends TestCase
{
    public function testExecuteSuccessfulConnection()
    {
        // Mock the Connection
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('isConnected')
            ->willReturn(true);

        // Initialize the command
        $command = new DatabaseTestCommand($connection);

        // Set up the command tester
        $commandTester = new CommandTester($command);

        // Execute the command
        $commandTester->execute([]);

        // Assert output and status code
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Database connection successful!', $output);
        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testExecuteFailedConnection()
    {
        // Mock the Connection
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->once())
            ->method('isConnected')
            ->will($this->throwException(new \Exception('Connection error')));

        // Initialize the command
        $command = new DatabaseTestCommand($connection);

        // Set up the command tester
        $commandTester = new CommandTester($command);

        // Execute the command
        $commandTester->execute([]);

        // Assert output and status code
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Database connection failed: Connection error', $output);
        $this->assertEquals(1, $commandTester->getStatusCode());
    }
}
