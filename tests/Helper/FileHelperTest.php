<?php

namespace App\Tests\Helper;

use PHPUnit\Framework\TestCase;
use App\Helper\net\exelearning\Helper\FileHelper;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FileHelperTest extends TestCase
{
    private FileHelper $fileHelper;
    private ContainerInterface $containerMock;
    private LoggerInterface $loggerMock;

    protected function setUp(): void
    {
        $this->containerMock = $this->createMock(ContainerInterface::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->containerMock->method('getParameter')->willReturnCallback(function ($param) {
            return match ($param) {
                'filesdir' => '/path/to/files',
                'kernel.project_dir' => '/project/dir',
                default => null,
            };
        });

        $this->fileHelper = new FileHelper($this->containerMock, $this->loggerMock);
    }

    public function testAddFilenameSuffix(): void
    {
        // Define inputs
        $filename = "example.txt";
        $path = "/tmp/files";

        // Mock file_exists function
        $mockFiles = [
            "/tmp/files/example.txt" => true,
            "/tmp/files/example_1.txt" => true,
            "/tmp/files/example_2.txt" => false
        ];

        $this->mockFileExists($mockFiles);

        // Invoke the function
        $reflection = new \ReflectionClass(FileHelper::class);
        $method = $reflection->getMethod('addFilenameSuffix');
        $method->setAccessible(true);
        $result = $method->invoke($this->fileHelper, $filename, $path);

        // Assert expected result
        $this->assertEquals("example_2.txt", $result);
    }

    private function mockFileExists(array $mockFiles): void
    {
        foreach ($mockFiles as $filePath => $exists) {
            if ($exists) {
                @mkdir(dirname($filePath), 0777, true);
                file_put_contents($filePath, '');
            } else if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
    }

    protected function tearDown(): void
    {
        $path = "/tmp/files";
        if (is_dir($path)) {
            $this->deleteDirectory($path);
        }
    }

    private function deleteDirectory(string $dir): void
    {
        foreach (scandir($dir) as $file) {
            if ($file !== '.' && $file !== '..') {
                $filePath = $dir . DIRECTORY_SEPARATOR . $file;
                is_dir($filePath) ? $this->deleteDirectory($filePath) : unlink($filePath);
            }
        }
        rmdir($dir);
    }
}