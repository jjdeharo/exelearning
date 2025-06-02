<?php

namespace App\Command\net\exelearning\Command;

use App\Constants;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'elp:export-ims',
    description: 'Export ELP to IMS format'
)]
class ElpExportImsCommand extends ElpExportCommand
{
    protected string $defaultFormat = Constants::EXPORT_TYPE_IMS;
}
