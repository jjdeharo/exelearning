<?php

namespace App\Config\net\exelearning\Config\FilemanagerConfig\SymfonyExtended;

use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * Implements extra methods of response to the filemanager.
 */
class FilemanagerResponse extends SymfonyResponse
{
    public function json($content, $status_code = 200)
    {
        $this->headers->set('Content-Type', 'application/json');
        $this->setStatusCode($status_code);

        $this->setContent(json_encode([
            'data' => $content,
        ]));
    }

    public function html($content, $status_code = 200)
    {
        $this->setStatusCode($status_code);
        $this->setContent($content);
    }

    public function redirect($url, $status_code = 302)
    {
        $this->setStatusCode($status_code);
        $this->setContent(
            sprintf('<!DOCTYPE html>
                        <html>
                            <head>
                                <meta charset="UTF-8" />
                                <meta http-equiv="refresh" content="0;url=%1$s" />
                                <title>Redirecting to %1$s</title>
                            </head>
                            <body>
                                Redirecting to <a href="%1$s">%1$s</a>.
                            </body>
                        </html>', htmlspecialchars($url, ENT_QUOTES, 'UTF-8'))
        );
        $this->headers->set('Location', $url);
    }
}
