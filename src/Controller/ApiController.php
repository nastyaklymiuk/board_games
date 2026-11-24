<?php

namespace App\Controller;

use App\Entity\BoardGame;
use App\Repository\BoardGameRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class ApiController extends AbstractController
{
    #[Route('/games', name: 'api_games', methods: ['GET'])]
    public function games(Request $request, BoardGameRepository $repo): JsonResponse
    {
        $num = static fn (?string $v) => is_numeric($v) ? $v + 0 : null;
        $cat = $num($request->query->get('category'));
        $price = $num($request->query->get('maxPrice'));
        $players = $num($request->query->get('players'));

        $games = $repo->findByFilters(
            $cat !== null ? (int) $cat : null,
            $price !== null ? (float) $price : null,
            $players !== null ? (int) $players : null,
            $request->query->get('q'),
        );

        return $this->json(array_map($this->gameToArray(...), $games));
    }

    #[Route('/games/{id}', name: 'api_game', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function game(int $id, BoardGameRepository $repo): JsonResponse
    {
        $game = $repo->find($id);
        if (!$game) {
            return $this->json(['error' => 'Game not found'], 404);
        }

        return $this->json($this->gameToArray($game));
    }

    #[Route('/categories', name: 'api_categories', methods: ['GET'])]
    public function categories(CategoryRepository $repo): JsonResponse
    {
        $data = array_map(fn ($c) => [
            'id' => $c->getId(),
            'name' => $c->getName(),
            'description' => $c->getDescription(),
        ], $repo->findBy([], ['name' => 'ASC']));

        return $this->json($data);
    }

    private function gameToArray(BoardGame $g): array
    {
        return [
            'id' => $g->getId(),
            'title' => $g->getTitle(),
            'description' => $g->getDescription(),
            'price' => (float) $g->getPrice(),
            'minPlayers' => $g->getMinPlayers(),
            'maxPlayers' => $g->getMaxPlayers(),
            'playTimeMinutes' => $g->getPlayTimeMinutes(),
            'minAge' => $g->getMinAge(),
            'inStock' => $g->isInStock(),
            'category' => [
                'id' => $g->getCategory()->getId(),
                'name' => $g->getCategory()->getName(),
            ],
        ];
    }
}