<?php

namespace App\Service\net\exelearning\Service\FilemanagerService\View;

use Symfony\Component\HttpFoundation\Request;

/**
 * Functions of view.
 */
interface ViewInterface
{
    public function getIndexPage(Request $request);
}
