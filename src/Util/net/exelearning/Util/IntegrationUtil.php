<?php

namespace App\Util\net\exelearning\Util;

use App\Constants;
use App\Settings;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Log\LoggerInterface;

class IntegrationUtil
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Decode a JSON Web Token.
     *
     * @param string $jwtToken
     *
     * @return array|null decoded
     */
    public function decodeJWT($jwtToken)
    {
        try {
            $decoded = (array) JWT::decode($jwtToken, new Key($_ENV['APP_SECRET'], Settings::JWT_SECRET_HASH));

            return $decoded;
        } catch (\Throwable $e) {
            $this->logger->error('Exception: '.$e->getMessage());

            // Opcional: lanzar una excepción personalizada
            // throw new \RuntimeException('Error decoding JWT', 0, $e);

            return null;
        }
    }

    /**
     * Decode JSON Web Token and export parameters for Moodle.
     *
     * @param string $jwtToken
     *
     * @return array|null decoded
     */
    public function getParamsMoodleIntegration($jwtToken, $setOp)
    {
        if ('set' === $setOp) {
            $op = 's';
        } else {
            $op = 'g';
        }

        $exportParams = $this->decodeJWT($jwtToken);
        if (null != $exportParams) {
            // Check if 'localhost' is in the return URL and replace it if necessary
            $returnUrl = $exportParams['returnurl'] ?? '';
            if ('' !== $returnUrl && str_contains($returnUrl, 'localhost')) {
                $clientIP = $this->getClientIP();
                $returnUrl = str_replace('localhost', $clientIP, $returnUrl);
            }

            // Generate platformIntegrationUrl based on package type
            switch ($exportParams['pkgtype']) {
                case 'scorm':
                    if (str_contains($returnUrl, '/mod/exescorm')) {
                        $exportParams['platformIntegrationUrl'] = strstr($returnUrl, '/mod/exescorm', true).'/mod/exescorm/'.$op.'et_ode.php';
                    } elseif (str_contains($returnUrl, '/course/section')) {
                        $exportParams['platformIntegrationUrl'] = strstr($returnUrl, '/course/section', true).'/mod/exescorm/'.$op.'et_ode.php';
                    }
                    // $exportParams['platformIntegrationUrl'] = strstr($returnUrl, '/mod/exescorm', true).'/mod/exescorm/'.$op.'et_ode.php';
                    $exportParams['exportType'] = Constants::EXPORT_TYPE_SCORM12;
                    break;
                case 'webzip':
                    if (str_contains($returnUrl, '/mod/exeweb')) {
                        $exportParams['platformIntegrationUrl'] = strstr($returnUrl, '/mod/exeweb', true).'/mod/exeweb/'.$op.'et_ode.php';
                    } elseif (str_contains($returnUrl, '/course/section')) {
                        $exportParams['platformIntegrationUrl'] = strstr($returnUrl, '/course/section', true).'/mod/exeweb/'.$op.'et_ode.php';
                    }
                    // $exportParams['platformIntegrationUrl'] = strstr($returnUrl, '/mod/exeweb', true).'/mod/exeweb/'.$op.'et_ode.php';
                    $exportParams['exportType'] = Constants::EXPORT_TYPE_HTML5;
                    break;
            }
        }

        return $exportParams;
    }

    /**
     * Get client IP address from server variables.
     *
     * @return string IP address of the client
     */
    private function getClientIP()
    {
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // Use X-Forwarded-For if available (for proxy setups)
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            // Otherwise, use REMOTE_ADDR
            $ip = $_SERVER['REMOTE_ADDR'];
        } else {
            $ip = 'UNKNOWN';
        }

        return $ip;
    }
}
