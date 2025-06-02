<?php

namespace App\Util\net\exelearning\Util;

/**
 * GoogleUtil.
 *
 * Utility functions for working with Google Drive
 */
class GoogleUtil
{
    // Constants
    public const GOOGLE_OAUTH_URL = 'https://accounts.google.com/o/oauth2/auth';
    public const GOOGLE_DRIVE_OAUTH_SCOPE = 'https://www.googleapis.com/auth/drive';
    public const GOOGLE_DRIVE_FOLDER_LIST = 'https://www.googleapis.com/drive/v3/files/';
    public const GOOGLE_DRIVE_FILE_UPLOAD_URI = 'https://www.googleapis.com/upload/drive/v3/files';
    public const GOOGLE_DRIVE_FILE_META_URI = 'https://www.googleapis.com/drive/v3/files/';
    public const GOOGLE_OAUTH2_TOKEN_URI = 'https://oauth2.googleapis.com/token';
    public const GOOGLE_DRIVE_FILE_OPEN_URL = 'https://drive.google.com/open';

    /**
     * Get the access token of the user to upload the file.
     *
     * @param string $client_id
     * @param string $redirect_uri
     * @param string $client_secret
     * @param string $code
     */
    public static function getAccessToken($client_id, $client_secret, $redirect_uri, $code)
    {
        $curlPost = 'client_id='.$client_id.'&redirect_uri='.$redirect_uri.'&client_secret='.$client_secret.'&code='.$code.'&grant_type=authorization_code';
        $data = Util::sendCurlRequest($curlPost, null, self::GOOGLE_OAUTH2_TOKEN_URI);

        return $data;
    }

    /**
     * List the folders of drive.
     *
     * @param string $access_token
     */
    public static function listDriveFolders($access_token)
    {
        $apiURL = self::GOOGLE_DRIVE_FOLDER_LIST."?q=mimeType='application/vnd.google-apps.folder'";
        $elements = [
            'apiURL' => $apiURL,
            'access_token' => $access_token,
            'file_content' => null,
            'mime_type' => null,
            'file_metadata' => null,
        ];
        $data = Util::sendCurlRequest(false, $elements);

        return $data;
    }

    /**
     * Upload the file and gets the id of the uploaded file in order to update the metadata.
     *
     * @param string $access_token
     * @param string $file_content
     * @param string $mime_type
     *
     * @return string
     */
    public static function uploadFileToDrive($access_token, $file_content, $mime_type)
    {
        $apiURL = self::GOOGLE_DRIVE_FILE_UPLOAD_URI.'?uploadType=media';
        $elements = [
            'apiURL' => $apiURL,
            'access_token' => $access_token,
            'file_content' => $file_content,
            'mime_type' => $mime_type,
            'file_metadata' => null,
        ];
        $data['id'] = Util::sendCurlRequest(null, $elements);

        return $data['id'];
    }

    /**
     * Update the metadata of the file and sets the folder of the upload.
     *
     * @param string $access_token
     * @param string $file_id
     * @param string $file_metadata
     * @param string $addParents
     */
    public static function updateFileMetaToDrive($access_token, $file_id, $file_metadata, $addParents = null)
    {
        $apiURL = self::GOOGLE_DRIVE_FILE_META_URI.$file_id;
        $parents = '?addParents='.$addParents;
        if ('null' != $addParents) {
            $apiURL = $apiURL.$parents;
        }
        $elements = [
            'apiURL' => $apiURL,
            'access_token' => $access_token,
            'file_content' => null,
            'mime_type' => null,
            'file_metadata' => $file_metadata,
        ];
        $data = Util::sendCurlRequest(null, $elements);

        return $data;
    }
}
