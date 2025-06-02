<?php

namespace App\Command\net\exelearning\Command;

use App\Constants;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'elp:export-scorm12',
    description: 'Export ELP to SCORM 1.2 format'
)]
class ElpExportScorm12Command extends ElpExportCommand
{
    protected string $defaultFormat = Constants::EXPORT_TYPE_SCORM12;
}
