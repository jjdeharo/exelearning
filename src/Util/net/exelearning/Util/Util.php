<?php

namespace App\Util\net\exelearning\Util;

use App\Controller\net\exelearning\Controller\Api\DefaultApiController;
use App\Exception\net\exelearning\Exception\Logical\PhpGdExtensionException;
use App\Exception\net\exelearning\Exception\Logical\PhpZipExtensionException;

/**
 * Util.
 *
 * General utility functions
 */
class Util
{
    public const STATUS_CODE_OK = 200;
    public const CURL_ERROR_3 = 'URL using bad/illegal format or missing URL';

    /**
     * Generates a string of 20 unique characters in "20201018103234EWHDKF" format (current date and time + 6 random chars).
     *
     * @return string
     */
    public static function generateId()
    {
        $result = '';

        $current_date = date_create();
        $timestamp_current = date_timestamp_get($current_date);

        $yyyy = gmdate('Y', $timestamp_current);

        // $mm (from 01 to 12)
        $mm_aux = (int) gmdate('m', $timestamp_current);
        // $mm_aux = $mm_aux - 1;
        if ($mm_aux <= 9) {
            $mm = '0'.(string) $mm_aux;
        } else {
            $mm = (string) $mm_aux;
        }

        // Day
        $dd = gmdate('d', $timestamp_current);

        // Hour (00 to 24)
        $hh = gmdate('H', $timestamp_current);

        // Minutes
        $mi = gmdate('i', $timestamp_current);

        // Seconds
        $ss = gmdate('s', $timestamp_current);

        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 6; ++$i) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        $result = $yyyy.$mm.$dd.$hh.$mi.$ss.$randomString;

        return $result;
    }

    /**
     * Generates an id and checks that it's not repeated in the array passed as param.
     *
     * @param array $generatedIds
     *
     * @return string
     */
    public static function generateIdCheckUnique($generatedIds)
    {
        do {
            $newId = self::generateId();
        } while (isset($generatedIds[$newId]));

        return $newId;
    }

    /**
     * Generates a random id of the specified size.
     *
     * @param int $length
     *
     * @return string
     */
    public static function generateRandomStr($length)
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);

        $randomString = '';
        for ($i = 0; $i < $length; ++$i) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    /**
     * Generates a response to the Curl sent.
     *
     * @param bool  $curlPost
     * @param array $elements
     */
    public static function sendCurlRequest($curlPost = null, $elements = null, $curlPostApiUrl = null)
    {
        // Variables to detect dropbox curl
        $curlPostApiUrlDropbox = strpos($curlPostApiUrl, 'dropboxapi');

        if (null !== $elements) {
            if (isset($elements['apiURL'])) {
                $elementsApiURLDropbox = strpos($elements['apiURL'], 'dropboxapi');
            } else {
                $elementsApiURLDropbox = false;
            }
        }

        $ch = curl_init();

        // List drive folders google util and dropbox util
        if (false === $curlPost) {
            curl_setopt($ch, CURLOPT_URL, $elements['apiURL']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            // Case dropbox
            if (false !== $elementsApiURLDropbox) {
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, '{"path":"", "recursive":true}');
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer '.$elements['access_token'], 'Content-Type: application/json']);
            } else {
                curl_setopt($ch, CURLOPT_POST, 0);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer '.$elements['access_token']]);
            }
        // Upload and update file google util, upload dropbox util
        } elseif (null != $elements && null == $curlPost) {
            curl_setopt($ch, CURLOPT_URL, $elements['apiURL']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            // Case dropbox
            if (false !== $elementsApiURLDropbox) {
                $fp = fopen($elements['file_dir'], 'rb');

                $cheaders = [
                    'Authorization: Bearer '.$elements['access_token'],
                    'Content-Type: application/octet-stream',
                    'Dropbox-API-Arg: {"path":"'.$elements['path'].'", "mode":"overwrite"}',
                ];

                curl_setopt($ch, CURLOPT_INFILE, $fp);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $cheaders);
            } else {
                (null != $elements['mime_type']) ? curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: '.$elements['mime_type'], 'Authorization: Bearer '.$elements['access_token']]) : (null != $elements['file_metadata'] ? curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Authorization: Bearer '.$elements['access_token']]) : '');
                (null != $elements['file_metadata']) ? curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH') : '';
                curl_setopt($ch, CURLOPT_POSTFIELDS, $elements['file_content'] ? $elements['file_content'] : json_encode($elements['file_metadata']));
            }
        // Update file dropbox
        } elseif (true === $curlPost) {
            $headers = [
                'Authorization: Bearer '.$elements['access_token'],
                'Content-Type: application/json',
            ];

            curl_setopt($ch, CURLOPT_URL, $elements['apiURL']);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, '{"path":"'.$elements['file_id'].'"}');

        // Get oauth token dropbox util and google util
        } else {
            curl_setopt($ch, CURLOPT_URL, $curlPostApiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);

            // Case dropbox
            if (false !== $curlPostApiUrlDropbox) {
                $dropboxClientId = $_ENV['DROPBOX_CLIENT_ID'];
                $dropboxClientSecret = $_ENV['DROPBOX_CLIENT_SECRET'];

                curl_setopt($ch, CURLOPT_USERPWD, $dropboxClientId.':'.$dropboxClientSecret);
            }
        }

        $data = json_decode(curl_exec($ch), true);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (self::STATUS_CODE_OK != $http_code) {
            $error_msg = 'Failed to receive access token';
            if (curl_errno($ch)) {
                $error_msg = curl_error($ch);
            }

            return $error_msg;
        }

        if (null != $elements) {
            if (null != $elements['mime_type']) {
                return $data['id'];
            } else {
                return $data;
            }
        } else {
            return $data;
        }
    }

    /**
     * Generates a simple curl request and returns http response.
     *
     * @param string $curlUrl
     */
    public static function sendCurlHttpRequest($curlUrl)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $curlUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        json_decode(curl_exec($ch), true);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (self::STATUS_CODE_OK != $http_code) {
            if (curl_errno($ch)) {
                if (6 == curl_errno($ch)) {
                    $error_msg = curl_error($ch);
                } elseif (3 == curl_errno($ch)) {
                    $error_msg = self::CURL_ERROR_3;
                } else {
                    $error_msg = $http_code.' '.curl_errno($ch).': '.curl_error($ch);
                }
            } else {
                if (DefaultApiController::STATUS_CODE_MOVED_PERMANENTLY == $http_code) {
                    $error_msg = null;
                } else {
                    $error_msg = $http_code;
                }
            }

            return $error_msg;
        }
        curl_close($ch);
    }

    /**
     * Check if the zip extension is loaded.
     *
     * @return bool
     */
    public static function checkPhpZipExtension()
    {
        $zipExtensionLoaded = extension_loaded('zip');
        if (true == $zipExtensionLoaded) {
            return true;
        } else {
            throw new PhpZipExtensionException(self::class, $zipExtensionLoaded);
        }
    }

    /**
     * Check if the imagick extension is loaded.
     *
     * @return bool
     */
    public static function checkPhpGdExtension()
    {
        $imagickExtensionLoaded = extension_loaded('gd');
        if (true == $imagickExtensionLoaded) {
            return true;
        } else {
            throw new PhpGdExtensionException(self::class, $imagickExtensionLoaded);
        }
    }
}
