<?php

namespace App\Util\net\exelearning\Util;

use App\Constants;

class DropboxUtil
{
    public const DROPBOX_API_FOLDER_LIST = 'https://api.dropboxapi.com/2/files/list_folder';
    public const DROPBOX_API_UPLOAD = 'https://content.dropboxapi.com/2/files/upload';
    public const DROPBOX_API_OAUTH2_TOKEN_URI = 'https://api.dropboxapi.com/oauth2/token';
    public const DROPBOX_API_OAUTH2_URL = 'https://www.dropbox.com/oauth2/authorize';
    public const DROPBOX_API_GET_METADATA = 'https://api.dropboxapi.com/2/files/get_metadata';

    /**
     * List the folders of Dropbox.
     *
     * @param string $access_token
     */
    public static function listDropboxFolders($access_token)
    {
        $elements = [
            'apiURL' => self::DROPBOX_API_FOLDER_LIST,
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
     * @param string $odePropertiesName
     * @param string $addParents
     * @param string $filePath
     *
     * @return string
     */
    public static function uploadFileToDropbox($access_token, $odePropertiesName, $filePath, $addParents = null)
    {
        if (null != $addParents) {
            $path = $addParents.Constants::SLASH.$odePropertiesName;
        } else {
            $path = Constants::SLASH.$odePropertiesName;
        }

        $elements = [
            'apiURL' => self::DROPBOX_API_UPLOAD,
            'access_token' => $access_token,
            'path' => $path,
            'file_dir' => $filePath,
            'mime_type' => null,
        ];

        $data['id'] = Util::sendCurlRequest(null, $elements);

        return $data['id'];
    }

    /**
     * Get the access token of the user to upload the file.
     *
     * @param string $redirect_uri
     * @param string $code
     */
    public static function getAccessDropboxToken($redirect_uri, $code)
    {
        $curlPost = 'code='.$code.'&grant_type=authorization_code&redirect_uri='.$redirect_uri;

        $data = Util::sendCurlRequest($curlPost, null, self::DROPBOX_API_OAUTH2_TOKEN_URI);

        return $data;
    }

    /**
     * Update the metadata of the file and sets the folder of the upload given the file id.
     *
     * @param string $access_token
     * @param string $file_id
     */
    public static function updateFileMetaToDropbox($access_token, $file_id)
    {
        $elements = [
            'apiURL' => self::DROPBOX_API_GET_METADATA,
            'access_token' => $access_token,
            'file_id' => $file_id,
            'mime_type' => null,
        ];

        $data = Util::sendCurlRequest(true, $elements);

        return $data;
    }
}
