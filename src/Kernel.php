<?php

// src/Kernel.php

namespace App;

use App\Util\net\exelearning\Util\SettingsUtil;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    // Sobrescribir el directorio de caché usando la variable de entorno CACHE_DIR
    public function getCacheDir(): string
    {
        // Verificar si la variable CACHE_DIR existe y no está vacía
        $cacheDir = !empty($_ENV['CACHE_DIR']) ? $_ENV['CACHE_DIR'] : parent::getCacheDir();

        // Verificar si el directorio existe, si no, crearlo
        $filesystem = new Filesystem();
        if (!$filesystem->exists($cacheDir)) {
            $filesystem->mkdir($cacheDir, 0755); // Crear el directorio con permisos adecuados
        }

        return $cacheDir;
    }

    // Sobrescribir el directorio de logs usando la variable de entorno LOG_DIR
    public function getLogDir(): string
    {
        // Verificar si la variable LOG_DIR existe y no está vacía
        $logDir = !empty($_ENV['LOG_DIR']) ? $_ENV['LOG_DIR'] : parent::getLogDir();

        // Verificar si el directorio existe, si no, crearlo
        $filesystem = new Filesystem();
        if (!$filesystem->exists($logDir)) {
            $filesystem->mkdir($logDir, 0755); // Crear el directorio con permisos adecuados
        }

        return $logDir;
    }

    protected function initializeContainer(): void
    {
        parent::initializeContainer();

        // Asegurarse de que el contenedor esté disponible para SettingsUtil
        SettingsUtil::setContainer($this->getContainer());
    }
}
