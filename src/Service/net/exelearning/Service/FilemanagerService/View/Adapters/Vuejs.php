<?php

namespace App\Service\net\exelearning\Service\FilemanagerService\View\Adapters;

use App\Service\net\exelearning\Service\FilemanagerService\View\ViewInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Sets view of filemanager.
 */
class Vuejs implements ViewInterface
{
    public function getIndexPage(Request $request)
    {
        $symfonyBaseURL = $request->getSchemeAndHttpHost();
        $symfonyBasePath = $request->getBaseURL();
        if ($symfonyBasePath) {
            $symfonyFullUrl = $symfonyBaseURL.'/'.$symfonyBasePath;
        } else {
            $symfonyFullUrl = $symfonyBaseURL;
        }
        $title = 'Filemanager';
        $public_path = $symfonyFullUrl.'/libs/filegator/';
        $public_dir = '';
        $html = '<!DOCTYPE html>
                    <html lang=en>
                      <head>
                        <meta charset=utf-8>
                        <meta http-equiv=X-UA-Compatible content="IE=edge">
                        <meta name=viewport content="width=device-width,initial-scale=1">
                        <meta name="robots" content="noindex,nofollow">
                        <title>'.$title.'</title>
                        <link href="'.$public_path.'css/app.css?'.@filemtime($public_dir.'/css/app.css').'" rel=stylesheet>
                        <link href="'.$public_path.'css/chunk-vendors.css?'.@filemtime($public_dir.'/css/chunk-vendors.css').'" rel=stylesheet>
                      </head>
                      <body>
                        <noscript><strong>Please enable JavaScript to continue.</strong></noscript>
                        <div id=app></div>
                        <script src="'.$public_path.'js/app.js?'.@filemtime($public_dir.'/js/app.js').'"></script>
                        <script src="'.$public_path.'js/chunk-vendors.js?'.@filemtime($public_dir.'/js/chunk-vendors.js').'"></script>
                      </body>
                    </html>
        ';

        return $html;
    }
}
