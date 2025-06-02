<?php

namespace App\Command\net\exelearning\Command;

use App\Constants;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'elp:export-epub3',
    description: 'Export ELP to EPUB 3 format'
)]
class ElpExportEpub3Command extends ElpExportCommand
{
    protected string $defaultFormat = Constants::EXPORT_TYPE_EPUB3;
}
