<?php

namespace App\Command\net\exelearning\Command;

use App\Constants;
use Symfony\Component\Console\Attribute\AsCommand;

/**
 * Command to export ELP files to HTML5 format in a folder structure.
 *
 * This command leverages the existing application functionality to open and process ELP files,
 * using the same flow as the web application's preview functionality rather than download
 * (which creates a ZIP file).
 *
 * Supports reading from stdin using "-" as the input argument.
 */
#[AsCommand(
    name: 'elp:export-html5',
    description: 'Export ELP to HTML5 format'
)]
class ElpExportHtml5Command extends ElpExportCommand
{
    protected string $defaultFormat = Constants::EXPORT_TYPE_HTML5;
}
