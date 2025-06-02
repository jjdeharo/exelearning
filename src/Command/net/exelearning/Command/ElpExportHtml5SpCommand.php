<?php

namespace App\Command\net\exelearning\Command;

use App\Constants;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'elp:export-html5-sp',
    description: 'Export ELP to HTML5 single page format'
)]
class ElpExportHtml5SpCommand extends ElpExportCommand
{
    protected string $defaultFormat = Constants::EXPORT_TYPE_HTML5_SP;
}
