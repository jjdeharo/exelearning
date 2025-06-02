<?php

namespace App\Command\net\exelearning\Command;

use App\Constants;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'elp:export-scorm2004',
    description: 'Export ELP to SCORM 2004 format'
)]
class ElpExportScorm2004Command extends ElpExportCommand
{
    protected string $defaultFormat = Constants::EXPORT_TYPE_SCORM2004;
}
