<?php

namespace App\Command\net\exelearning\Command;

use App\Constants;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'elp:export-elp',
    description: 'Re-export ELP file'
)]
class ElpExportElpCommand extends ElpExportCommand
{
    protected string $defaultFormat = Constants::EXPORT_TYPE_ELP;
}
