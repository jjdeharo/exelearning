<?php

namespace App\Controller\net\exelearning\Controller\Api;

use App\Repository\net\exelearning\Repository\GameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class GamesApiController extends AbstractController
{
    private GameRepository $gameRepository;

    public function __construct(GameRepository $gameRepository)
    {
        $this->gameRepository = $gameRepository;
    }

    #[Route('/api/games/{odeSessionId}/idevices', name: 'api_games_session_idevices', methods: ['GET'])]
    public function getIdevicesBySessionId(string $odeSessionId): JsonResponse
    {
        $data = $this->gameRepository->getIdevicesBySessionId($odeSessionId);

        return $this->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}
