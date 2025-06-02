<?php

namespace App\Command\net\exelearning\Command;

use App\Constants;
use App\Entity\net\exelearning\Entity\User;
use App\Helper\net\exelearning\Helper\FileHelper;
use App\Repository\net\exelearning\Repository\UserRepository;
use App\Service\net\exelearning\Service\Api\OdeExportServiceInterface;
use App\Service\net\exelearning\Service\Api\OdeServiceInterface;
use App\Service\net\exelearning\Service\FilesDir\FilesDirServiceInterface;
use App\Util\net\exelearning\Util\FileUtil;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'elp:export',
    description: 'Export ELP files in a given format to a folder structure'
)]
class ElpExportCommand extends Command
{
    protected string $defaultFormat = Constants::EXPORT_TYPE_HTML5;

    public function __construct(
        private OdeServiceInterface $odeService,
        private OdeExportServiceInterface $odeExportService,
        private FileHelper $fileHelper,
        private FilesDirServiceInterface $filesDirService,
        private UserRepository $userRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('input', InputArgument::REQUIRED, 'Input ELP file path (use "-" for stdin)')
            ->addArgument('output', InputArgument::REQUIRED, 'Output directory')
            ->addArgument('format', InputArgument::OPTIONAL, 'Export format (elp, html5, html5-sp, scorm12, scorm2004, ims, epub3)', $this->defaultFormat)
            ->addOption('debug', 'd', InputOption::VALUE_NONE, 'Enable debug mode')
            ->addOption('base-url', 'b', InputOption::VALUE_OPTIONAL, 'Base URL for links', false);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $inputPath = $input->getArgument('input');
        $outputDir = $input->getArgument('output');
        $format = $input->getArgument('format') ?? $this->defaultFormat;
        $debug = $input->getOption('debug');
        $baseUrl = $input->getOption('base-url');

        $validFormats = [
            Constants::EXPORT_TYPE_ELP,
            Constants::EXPORT_TYPE_HTML5,
            Constants::EXPORT_TYPE_HTML5_SP,
            Constants::EXPORT_TYPE_SCORM12,
            Constants::EXPORT_TYPE_SCORM2004,
            Constants::EXPORT_TYPE_IMS,
            Constants::EXPORT_TYPE_EPUB3,
        ];

        if (!in_array($format, $validFormats, true)) {
            $io->error("Invalid format: $format");
            $io->note('Valid formats: '.implode(', ', $validFormats));

            return Command::FAILURE;
        }

        $tempFile = null;
        $useStdin = ('-' === $inputPath);

        if ($useStdin) {
            if ($debug) {
                $io->section('Reading ELP data from stdin');
            }

            $tempFile = tempnam(sys_get_temp_dir(), 'elp_export_');
            $tempHandle = fopen($tempFile, 'w');
            $stdinHandle = fopen('php://stdin', 'r');
            stream_copy_to_stream($stdinHandle, $tempHandle);
            fclose($stdinHandle);
            fclose($tempHandle);
            $inputPath = $tempFile;

            if ($debug) {
                $io->text("Stdin data saved to temporary file: $tempFile");
            }
        } else {
            if (!file_exists($inputPath)) {
                $io->error("Input file not found: $inputPath");

                return Command::FAILURE;
            }
        }

        if (!file_exists($outputDir)) {
            if ($debug) {
                $io->text("Creating output directory: $outputDir");
            }
            if (!mkdir($outputDir, 0755, true)) {
                $io->error("Failed to create output directory: $outputDir");

                return Command::FAILURE;
            }
        } elseif (!is_dir($outputDir) || !is_writable($outputDir)) {
            $io->error("Output path is not a writable directory: $outputDir");

            return Command::FAILURE;
        }

        if ($debug) {
            $io->section("Exporting to format: $format");
        }

        $user = $this->getUser();
        $this->filesDirService->checkFilesDir();
        $sessionId = $this->generateSessionId();
        $this->createSessionDirectories($user, $sessionId, $debug, $io);
        $sessionDir = $this->fileHelper->getOdeSessionDir($sessionId);
        $sessionDistDir = $sessionDir.'dist/';
        FileUtil::ensureDirectoryExists($sessionDir);
        FileUtil::ensureDirectoryExists($sessionDistDir);

        $inputFileName = $useStdin ? 'stdin.elp' : basename($inputPath);
        $sessionFilePath = $sessionDistDir.$inputFileName;
        FileUtil::copyFile($inputPath, $sessionFilePath);

        $checkResult = $this->odeService->checkLocalOdeFile(
            $inputFileName,
            $sessionFilePath,
            $user,
            true
        );

        if ('OK' !== $checkResult['responseMessage']) {
            $io->error('Invalid ELP file: '.$checkResult['responseMessage']);
            $this->cleanupSession($sessionDir);
            if ($tempFile) {
                unlink($tempFile);
            }

            return Command::FAILURE;
        }

        $this->odeService->createElpStructureAndCurrentOdeUser(
            $inputFileName,
            $user,
            $user,
            '127.0.0.1',
            true,
            $checkResult
        );

        $exportResult = $this->odeExportService->export(
            $user,
            $user,
            $checkResult['odeSessionId'],
            $baseUrl,
            $format,
            false,
            false
        );

        if ('OK' !== $exportResult['responseMessage']) {
            $io->error('Export failed: '.$exportResult['responseMessage']);
            $this->cleanupSession($sessionDir);
            if ($tempFile) {
                unlink($tempFile);
            }

            return Command::FAILURE;
        }

        $exportDirPath = $this->fileHelper->getOdeSessionUserTmpExportDir($checkResult['odeSessionId'], $user);
        $this->copyDirectory($exportDirPath, $outputDir);
        $this->cleanupSession($sessionDir);

        if ($tempFile) {
            unlink($tempFile);
        }

        $io->success("Exported successfully to $outputDir");

        return Command::SUCCESS;
    }

    private function getUser(): User
    {
        $user = $this->userRepository->find(1);
        if (!$user) {
            throw new \RuntimeException('User with ID 1 not found.');
        }

        return $user;
    }

    private function generateSessionId(): string
    {
        $timestamp = date('YmdHis');
        $suffix = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 5);

        return $timestamp.$suffix;
    }

    private function createSessionDirectories(User $user, string $sessionId, bool $debug, SymfonyStyle $io): void
    {
        $sessionDir = $this->fileHelper->getOdeSessionDir($sessionId);
        $userTmpDir = $this->fileHelper->getOdeSessionUserTmpDir($sessionId, $user);
        $exportDir = $this->fileHelper->getOdeSessionUserTmpExportDir($sessionId, $user);

        if ($debug) {
            $io->text("Session directory: $sessionDir");
            $io->text("User temporary directory: $userTmpDir");
            $io->text("Export directory: $exportDir");
        }

        FileUtil::ensureDirectoryExists($sessionDir);
        FileUtil::ensureDirectoryExists($userTmpDir);
        FileUtil::ensureDirectoryExists($exportDir);
    }

    private function cleanupSession(string $sessionDir): void
    {
        if (file_exists($sessionDir)) {
            FileUtil::removeDir($sessionDir);
        }
    }

    private function copyDirectory(string $sourceDir, string $destDir): void
    {
        $sourceDir = rtrim($sourceDir, '/').'/';
        $destDir = rtrim($destDir, '/').'/';

        if (!file_exists($destDir)) {
            mkdir($destDir, 0755, true);
        }

        foreach (scandir($sourceDir) as $item) {
            if (in_array($item, ['.', '..'], true)) {
                continue;
            }

            $src = $sourceDir.$item;
            $dst = $destDir.$item;

            if (is_dir($src)) {
                $this->copyDirectory($src, $dst);
            } else {
                copy($src, $dst);
            }
        }
    }
}
