<?php

namespace App\Controller\net\exelearning\Controller\Api;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Uid\Uuid;

class DefaultApiController extends AbstractController
{
    // Status codes
    public const STATUS_CODE_OK = 200;
    public const STATUS_CODE_MOVED_PERMANENTLY = 301;
    public const STATUS_CODE_NOT_FOUND = 404;
    public const STATUS_CODE_SERVICE_UNAVAILABLE = 503;

    protected $entityManager;

    protected $logger;

    protected $status;
    /**
     * Mercure hub.
     *
     * @var HubInterface
     */
    protected $hub;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger, ?HubInterface $hub = null)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->status = self::STATUS_CODE_OK;
        $this->hub = $hub;
    }

    /**
     * Converts the data received to json.
     *
     * @return string json
     */
    protected function getJsonSerialized($data)
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);

        $jsonSerialized = $serializer->serialize($data, 'json');

        return $jsonSerialized;
    }

    /**
     * Return symfony path.
     *
     * @param Request $request
     *
     * @return string $symfonyFullUrl
     */
    protected function getSymfonyUrl($request)
    {
        // Base URL
        $symfonyBaseURL = $request->getSchemeAndHttpHost();
        $symfonyBasePath = $request->getBaseURL();
        $symfonyFullUrl = $symfonyBaseURL;

        if ($symfonyBasePath) {
            $symfonyFullUrl .= $symfonyBasePath;
        }

        return $symfonyFullUrl;
    }

    /**
     * Return session id.
     *
     * @param Request     $request
     * @param string|null $parameterOdeSessionId
     *
     * @return string $odeSessionId
     */
    protected function getOdeSessionId($request, $parameterOdeSessionId = null)
    {
        $odeSessionId = $request->get('odeSessionId');

        if (null !== $odeSessionId) {
            $odeSessionId = $odeSessionId;
        } else {
            // First check if parameterOdeSessionId has been send
            if ($parameterOdeSessionId) {
                $odeSessionId = $parameterOdeSessionId;
            }
        }

        return $odeSessionId;
    }

    /**
     * Get a user from the Security Token Storage.
     *
     * @throws \LogicException If SecurityBundle is not available
     *
     * @see TokenInterface::getUser()
     */
    protected function getUser(): ?UserInterface
    {
        $user = parent::getUser();
        if (empty($user)) {
            $user = $this->container->get('session')->get('SESSION_USER_DATA');
        }

        return $user;
    }

    /**
     * Publish message to mercure hub. $odeSessionId is used as topic. Returns false if no hub available.
     */
    protected function publish(string $odeSessionId, string $eventName): string|bool
    {
        if (null === $this->hub) {
            return false;
        }
        $uuid = Uuid::v4();
        $update = new Update(
            $odeSessionId,
            json_encode(['name' => $eventName]),
            false,
            $uuid,
        );
        try {
            $result = $this->hub->publish($update);
        } catch (\RuntimeException $exception) {
            $result = false;
            $this->logger->error("Failed to publish event '$eventName' on topic $odeSessionId.");
        }

        return $result;
    }
}
