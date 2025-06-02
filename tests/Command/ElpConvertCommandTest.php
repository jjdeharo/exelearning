<?php

namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;
use App\Command\net\exelearning\Command\ElpConvertCommand;
use PHPUnit\Framework\Attributes\DataProvider;
use App\Entity\net\exelearning\Entity\User;
use App\Repository\net\exelearning\Repository\UserRepository;

class ElpConvertCommandTest extends KernelTestCase
{
    private CommandTester $commandTester;
    private Filesystem $filesystem;
    private array $tempFiles = [];

    protected function setUp(): void
    {
        self::bootKernel();
        $this->filesystem = new Filesystem();

        $container = static::getContainer();

        // Create user 1 if not exists
        $userRepository = $container->get(UserRepository::class);
        $entityManager = $container->get('doctrine.orm.entity_manager');

        if (!$userRepository->find(1)) {
            $user = new User();
            $user->setUserId(1);
            $user->setEmail('tests@example.com');
            $user->setPassword('random-pass');
            $user->setIsLopdAccepted(true);

            $metadata = $entityManager->getClassMetadata(User::class);
            $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);

            $entityManager->persist($user);
            $entityManager->flush();
        }

        $application = new Application();
        $command = $container->get(ElpConvertCommand::class);
        $application->add($command);

        $this->commandTester = new CommandTester($command);
    }

    #[DataProvider('elpFileProvider')]
    public function testConvertElpFixture(string $fixtureFilename): void
    {
        $inputFile = realpath(__DIR__ . '/../Fixtures/' . $fixtureFilename);
        $outputFile = tempnam(sys_get_temp_dir(), 'elp_');
        $this->tempFiles[] = $outputFile;

        $this->assertFileExists($inputFile, "Input ELP file does not exist: $inputFile");

        $this->commandTester->execute([
            'command' => 'elp:convert',
            'input' => $inputFile,
            'output' => $outputFile,
            '--debug' => true,
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertSame(0, $this->commandTester->getStatusCode(), $output);
        $this->assertFileExists($outputFile);
        // $this->assertGreaterThan(0, filesize($outputFile), 'Converted file is empty.');
    }

    public static function elpFileProvider(): array
    {
        return [
            ['basic-example.elp'],
            ['old_elp_modelocrea.elp'],
            ['old_elp_nebrija.elp'],
            ['old_elp_poder_conexiones.elp'],
            ['old_manual_exe29_compressed.elp'],
        ];
    }

    public function testFailsWithInvalidFile(): void
    {
        $tempInvalidFile = tempnam(sys_get_temp_dir(), 'invalid_elp_');
        $tempOutput = tempnam(sys_get_temp_dir(), 'elp_output_');
        $this->tempFiles[] = $tempInvalidFile;
        $this->tempFiles[] = $tempOutput;

        file_put_contents($tempInvalidFile, 'this is not a valid ELP file');

        $this->assertFileExists($tempInvalidFile);

        $this->commandTester->execute([
            'command' => 'elp:convert',
            'input' => $tempInvalidFile,
            'output' => $tempOutput,
            '--debug' => true,
        ]);

        $output = $this->commandTester->getDisplay();

        $this->assertSame(1, $this->commandTester->getStatusCode(), $output);
        $this->assertStringContainsString('Invalid ELP file', $output);
        $this->assertFileExists($tempOutput);
        $this->assertSame(0, filesize($tempOutput), 'Output file should be empty or not created.');
    }

    protected function tearDown(): void
    {
        foreach ($this->tempFiles as $file) {
            if ($this->filesystem->exists($file)) {
                $this->filesystem->remove($file);
            }
        }
    }
}
