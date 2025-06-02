<?php

namespace App\Service\net\exelearning\Service\Api;

use App\Entity\net\exelearning\Entity\User;
use App\Helper\net\exelearning\Helper\FileHelper;
use App\Helper\net\exelearning\Helper\UserHelper;
use App\Settings;
use App\Util\net\exelearning\Util\IntegrationUtil;
use App\Util\net\exelearning\Util\SettingsUtil;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class PlatformIntegrationService implements PlatformIntegrationServiceInterface
{
    private $entityManager;
    private $logger;
    private $fileHelper;
    private $currentOdeUsersService;
    private $userHelper;
    private $integrationUtil;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger, FileHelper $fileHelper, CurrentOdeUsersServiceInterface $currentOdeUsersService, UserHelper $userHelper)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->fileHelper = $fileHelper;
        $this->currentOdeUsersService = $currentOdeUsersService;
        $this->userHelper = $userHelper;
        $this->integrationUtil = new IntegrationUtil($logger);
    }

    /**
     * Petition to upload elp to the platform.
     *
     * @param User   $user
     * @param array  $odeResultParameters
     * @param string $filePathName
     * @param string $jwtToken
     *
     * @return array content
     */
    public function platformPetitionSet($user, $odeResultParameters, $platformIntegrationUrlSet, $filePathName, $jwtToken)
    {
        $decodedJWT = [];
        try {
            $fp = fopen($filePathName, 'rb');
            $binary = fread($fp, filesize($filePathName));
            $fileBase64 = base64_encode($binary);
            $decodedJWT = $this->integrationUtil->decodeJWT($jwtToken);
        } catch (Exception $e) {
            /* TO DO: mandar mensaje de error */
            $this->logger->error('Exception:'.$e->getMessage());
            /* Parar aplicación */
        }

        $httpClient = HttpClient::create();

        $postJson = SettingsUtil::getPlatformJsonStructure();
        $postJson['ode_id'] = $decodedJWT['cmid'];
        $postJson['ode_filename'] = $odeResultParameters['exportProjectName'] ? $odeResultParameters['exportProjectName'] : $odeResultParameters['elpFileName']; // si viene vacío poner  $odeResultParameters['elpFileName'];
        $postJson['ode_file'] = $fileBase64;
        $postJson['ode_user'] = $decodedJWT['userid'];
        $postJson['ode_uri'] = $decodedJWT['returnurl'];
        $postJson['jwt_token'] = $jwtToken;

        $jsonData = $this->getJsonSerialized($postJson);

        $formData = [
            'ode_data' => $jsonData,
        ];
        $formData = new FormDataPart($formData);
        $formData->getParts();

        try {
            $response = $httpClient->request('POST', $platformIntegrationUrlSet, [
                'headers' => $formData->getPreparedHeaders()->toArray(),
                'body' => $formData->bodyToIterable(),
            ]);

            $content = $response->toArray();
        } catch (\Exception $e) {
            $this->logger->error('platform upload error:'.$e->getMessage(), ['className' => get_class($e), 'file:' => $this, 'line' => __LINE__]);
            $content['error'] = 'There was a problem with the upload';
        }

        return $content;
    }

    /**
     * Send petition to get the elp from platform.
     *
     * @param User   $user
     * @param string $odePlatformId
     * @param string $platformIntegrationUrlGet
     * @param string $jwtToken
     *
     * @return array $content
     */
    public function platformPetitionGet($user, $odePlatformId, $platformIntegrationUrlGet, $jwtToken)
    {
        $httpClient = HttpClient::create();

        $postJson = SettingsUtil::getPlatformJsonStructure();
        $postJson['ode_id'] = $odePlatformId;
        $postJson['ode_user'] = $user;
        $postJson['jwt_token'] = $jwtToken;
        $jsonData = $this->getJsonSerialized($postJson);

        try {
            $formData = [
                'ode_data' => $jsonData,
            ];
            $formData = new FormDataPart($formData);
            $formData->getParts();

            $response = $httpClient->request('POST', $platformIntegrationUrlGet, [
                'headers' => $formData->getPreparedHeaders()->toArray(),
                'body' => $formData->bodyToIterable(),
            ]);
            $content = $response->toArray();

            // Debug log petition
            $this->logger->debug('petition get ode file response:', ['content:' => $content, 'file:' => $this, 'line' => __LINE__]);

            // Error get data
            if ('1' == $content['status']) {
                $content['error'] = $content['description'];
            }
        } catch (\Exception $e) {
            $this->logger->error('platform get elp error:'.$e->getMessage(), ['className' => get_class($e), 'file:' => $this, 'line' => __LINE__]);
            $content['error'] = 'Error, couldn\'t get elp';
        }

        return $content;
    }

    /**
     * Send petitions to upload elp to the platform.
     *
     * @param User   $user
     * @param array  $odeResultParameters
     * @param string $platformIntegrationUrlSet
     * @param string $filePathName
     * @param array  $secondTypePlatformClient
     * @param string $symfonyFullUrl
     *
     * @return array $content
     */
    public function secondTypePlatformPetitionSet($user, $odeResultParameters, $platformIntegrationUrlSet, $filePathName, $secondTypePlatformClient, $symfonyFullUrl)
    {
        $content = [];

        // Get file base 64
        $fp = fopen($filePathName, 'rb');
        $binary = fread($fp, filesize($filePathName));
        $fileBase64 = base64_encode($binary);

        // Get access token
        $accessToken = $this->getSecondTypePlatformAccessToken($platformIntegrationUrlSet, $secondTypePlatformClient);

        if (isset($accessToken->error)) {
            $platformJsonResponse['error'] = $accessToken->error_description;

            return $platformJsonResponse;
        }

        // Create file
        $putFileResponse = $this->secondTypePlatformCreateFile('', $odeResultParameters, $platformIntegrationUrlSet, $fileBase64);

        if (isset($putFileResponse->error)) {
            $platformJsonResponse['error'] = $putFileResponse->error_description;
            $this->logger->error('platform upload error', ['putFileResponse:' => $putFileResponse, 'file:' => $this, 'line' => __LINE__]);

            return $platformJsonResponse;
        }

        // Create item
        $putItemResponse = $this->secondTypePlatformCreateItem('', $putFileResponse, $odeResultParameters, $platformIntegrationUrlSet, $symfonyFullUrl);

        if (isset($putItemResponse->error)) {
            $platformJsonResponse['error'] = $putItemResponse->error_description;
            $this->logger->error('platform upload error', ['putItemResponse:' => $putItemResponse, 'file:' => $this, 'line' => __LINE__]);

            return $platformJsonResponse;
        }

        $content['description'] = 'File was uploaded correctly';

        return $content;
    }

    /**
     * Send petitions to get elp from the platform.
     *
     * @param User   $user
     * @param string $odePlatformId
     * @param string $platformIntegrationUrlGet
     * @param array  $secondTypePlatformClient
     *
     * @return array $content
     */
    public function secondTypePlatformPetitionGet($user, $odePlatformId, $platformIntegrationUrlGet, $secondTypePlatformClient)
    {
        // Get access token
        $accessToken = $this->getSecondTypePlatformAccessToken($platformIntegrationUrlGet, $secondTypePlatformClient);
        if (isset($accessToken->error)) {
            $platformJsonResponse['error'] = $accessToken->error_description;
            $this->logger->error('platform get elp error', ['accessToken:' => $accessToken, 'file:' => $this, 'line' => __LINE__]);

            return $platformJsonResponse;
        }

        $content = [];
        // Get api item
        $itemId = $odePlatformId;

        $accessToken = '';
        $addItemUrl = $platformIntegrationUrlGet.'/api/item/'.$itemId.'/latest';
        $method = 'GET';
        $postFields = null;

        $getItemResponse = $this->secondTypePlatformCurlPetitions($addItemUrl, $accessToken, $method, $postFields);
        $getItemResponse = json_decode($getItemResponse);
        if (isset($getItemResponse)) {
            $platformJsonResponse['error'] = $getItemResponse->error_description;
            $this->logger->error('platform get elp error', ['getItemResponse:' => $getItemResponse, 'file:' => $this, 'line' => __LINE__]);

            return $platformJsonResponse;
        }

        $odePlatformName = $getItemResponse->name;
        $contentFileUrl = $getItemResponse->attachments[0]->links->content;
        $method = 'GET';
        $postFields = null;
        $fileContentResponse = $this->secondTypePlatformCurlPetitions($contentFileUrl, $accessToken, $method, $postFields);
        $fileContentResponse = json_decode($fileContentResponse);
        if (isset($fileContentResponse)) {
            $platformJsonResponse['error'] = $fileContentResponse->error_description;
            $this->logger->error('platform get elp error', ['fileContentReponse:' => $fileContentResponse, 'file:' => $this, 'line' => __LINE__]);

            return $platformJsonResponse;
        }

        $fileContentResponse = json_decode($fileContentResponse);
        $fileContentBase64 = $fileContentResponse->base64;

        $content['ode_file'] = $fileContentBase64;
        $content['ode_filename'] = $odePlatformName;

        return $content;
    }

    /**
     * Curl petitions to the platform.
     *
     * @param string $url
     * @param string $accessToken
     * @param string $method
     * @param array  $postFields
     */
    private function secondTypePlatformCurlPetitions($url, $accessToken, $method, $postFields)
    {
        $ch = curl_init();

        $headers = ['Content-Type: application/json'];

        if (!empty($accessToken)) {
            $headers[] = 'X-Authorization: access_token='.$accessToken;
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        switch ($method) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
                break;

            case 'PUT':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
                break;

            default:
                break;
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        try {
            $data = curl_exec($ch);

            // Debug log petition
            $this->logger->debug('curl petition response:', ['data:' => $data, 'file:' => $this, 'line' => __LINE__]);
        } catch (\Exception $e) {
            $this->logger->error('curl error:'.$e->getMessage(), ['className' => get_class($e), 'data:' => $data, 'file:' => $this, 'line' => __LINE__]);
        }

        return $data;
    }

    /**
     * Get acces token from client_id and client_secret.
     *
     * @param string $platformIntegrationUrl
     * @param array  $secondTypePlatformClient
     *
     * @return mixed $accesToken
     */
    private function getSecondTypePlatformAccessToken($platformIntegrationUrl, $secondTypePlatformClient)
    {
        $client_id = $secondTypePlatformClient['secondTypePlatformClientId'];
        $client_secret = $secondTypePlatformClient['secondTypePlatformClientSecret'];

        // Get access_token
        $accessToken = null;
        $method = 'GET';
        $postFields = null;

        $accesTokenUrl = $platformIntegrationUrl.'/oauth/access_token?grant_type=client_credentials&client_id='.$client_id.'&client_secret='.$client_secret.'&redirect_uri=default';

        $accesTokenResponse = $this->secondTypePlatformCurlPetitions($accesTokenUrl, $accessToken, $method, $postFields);

        $accesTokenResponse = json_decode($accesTokenResponse);

        // Debug log petition
        $this->logger->debug('petition get acces token openEquella:', ['accesTokenReponse:' => $accesTokenResponse, 'file:' => $this, 'line' => __LINE__]);

        if (isset($accesTokenResponse->error)) {
            $platformJsonResponse['error'] = $accesTokenResponse->error_description;
            $this->logger->error('platform get acces token error', ['accesToken:' => $accessToken, 'file:' => $this, 'line' => __LINE__]);

            return $accesTokenResponse;
        }
        $accessToken = $accesTokenResponse->access_token;

        return $accessToken;
    }

    /**
     * Create file in platform.
     *
     * @param string $accessToken
     * @param array  $odeResultParameters
     * @param string $platformIntegrationUrlSet
     * @param string $fileBase64
     *
     * @return object $putFileResponse
     */
    private function secondTypePlatformCreateFile($accessToken, $odeResultParameters, $platformIntegrationUrlSet, $fileBase64)
    {
        // Add file
        $addFileData = [
            'uuid' => '',
            'version' => 1,
            'collection' => Settings::SECOND_TYPE_PLATFORM_COLLECTION_ID,
        ];

        $addFileUrl = $platformIntegrationUrlSet.'/api/file';
        $method = 'POST';
        $postFields = json_encode($addFileData);

        $addFileResponse = $this->secondTypePlatformCurlPetitions($addFileUrl, $accessToken, $method, $postFields);
        $addFileResponse = json_decode($addFileResponse);

        // Debug log petition
        $this->logger->debug('petition add file openEquella:', ['addFileResponse:' => $addFileResponse, 'file:' => $this, 'line' => __LINE__]);

        if (isset($addFileResponse->error)) {
            $platformJsonResponse['error'] = $addFileResponse->error_description;
            $this->logger->error('platform upload error', ['addFileResponse:' => $addFileResponse, 'file:' => $this, 'line' => __LINE__]);

            return $addFileResponse;
        }

        // Put file
        $addItemData = [
            'uuid' => '',
            'version' => 1,
            'collection' => Settings::SECOND_TYPE_PLATFORM_COLLECTION_ID,
            'base64' => $fileBase64,
        ];

        $addItemUrl = $platformIntegrationUrlSet.'/api/file/'.$addFileResponse->uuid.'/content/'.$odeResultParameters['elpPath'];
        $method = 'PUT';
        $postFields = json_encode($addItemData);

        $putFileResponse = $this->secondTypePlatformCurlPetitions($addItemUrl, $accessToken, $method, $postFields);
        $putFileResponse = json_decode($putFileResponse);

        // Debug log petition
        $this->logger->debug('petition put file openEquella:', ['putFileResponse:' => $putFileResponse, 'file:' => $this, 'line' => __LINE__]);

        if (isset($putFileResponse->error)) {
            $platformJsonResponse['error'] = $putFileResponse->error_description;
            $this->logger->error('platform upload error', ['putFileResponse:' => $putFileResponse, 'file:' => $this, 'line' => __LINE__]);

            return $addFileResponse;
        }

        return $putFileResponse;
    }

    /**
     * Create item in platform.
     *
     * @param string $accessToken
     * @param object $putFileResponse
     * @param array  $odeResultParameters
     * @param string $platformIntegrationUrlSet
     * @param string $symfonyFullUrl
     *
     * @return object $editItemResponse
     */
    private function secondTypePlatformCreateItem($accessToken, $putFileResponse, $odeResultParameters, $platformIntegrationUrlSet, $symfonyFullUrl)
    {
        // Contributing - Add item

        $xmlMetadata = '<xml><item><name>'.$odeResultParameters['elpFileName'].'</name></item></xml>';

        $links = [
            'view' => $symfonyFullUrl.'/workarea', // Obtain path from symfony url
        ];
        $links = json_encode($links);

        $addItemData = [
            'uuid' => '',
            'version' => 1,
            'collection' => Settings::SECOND_TYPE_PLATFORM_COLLECTION_ID,
            'metadata' => $xmlMetadata,
            'attachments' => [$putFileResponse],
            'links' => $links,
        ];

        $addItemUrl = $platformIntegrationUrlSet.'/api/item?draft=true';
        $method = 'POST';
        $postFields = json_encode($addItemData);

        $addItemResponse = $this->secondTypePlatformCurlPetitions($addItemUrl, $accessToken, $method, $postFields);
        $addItemResponse = json_decode($addItemResponse);

        // Debug log petition
        $this->logger->debug('petition add item openEquella:', ['addItemResponse:' => $addItemResponse, 'file:' => $this, 'line' => __LINE__]);

        if (isset($addItemResponse->error)) {
            $platformJsonResponse['error'] = $addItemResponse->error_description;
            $this->logger->error('platform upload error', ['addItemResponse:' => $addItemResponse, 'file:' => $this, 'line' => __LINE__]);

            return $addItemResponse;
        }

        // Contributing - Edit item
        $addItemResponse = json_decode($addItemResponse);
        $itemId = $addItemResponse->uuid;

        $xmlMetadata = '<xml><item><name>'.$odeResultParameters['elpFileName'].'</name></item></xml>';

        $links = [
            'view' => $symfonyFullUrl.'/workarea?item_uuid='.$itemId.'',  // Obtain path from symfony url
        ];
        $links = json_encode($links);

        $addItemData = [
            'links' => $links,
        ];

        $addItemUrl = $platformIntegrationUrlSet.'/api/item/'.$itemId;
        $method = 'PUT';
        $postFields = json_encode($addItemData);

        $editItemResponse = $this->secondTypePlatformCurlPetitions($addItemUrl, $accessToken, $method, $postFields);
        $editItemResponse = json_decode($editItemResponse);

        // Debug log petition
        $this->logger->debug('petition edit item openEquella:', ['editItemResponse:' => $editItemResponse, 'file:' => $this, 'line' => __LINE__]);

        if (isset($editItemResponse->error)) {
            $platformJsonResponse['error'] = $editItemResponse->error_description;
            $this->logger->error('platform upload error', ['editItemResponse:' => $editItemResponse, 'file:' => $this, 'line' => __LINE__]);

            return $$editItemResponse;
        }

        return $editItemResponse;
    }

    /**
     * Converts the data received to json.
     *
     * @return string json
     */
    private function getJsonSerialized($data)
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);

        $jsonSerialized = $serializer->serialize($data, 'json');

        return $jsonSerialized;
    }
}
