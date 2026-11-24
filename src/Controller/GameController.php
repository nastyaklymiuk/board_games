<?php

namespace App\Controller;

use App\Entity\BoardGame;
use App\Entity\Category;
use App\Repository\BoardGameRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GameController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, BoardGameRepository $games, CategoryRepository $categories): Response
    {
        $num = static fn (?string $v) => is_numeric($v) ? $v + 0 : null;

        $categoryId = $num($request->query->get('category'));
        $maxPrice = $num($request->query->get('maxPrice'));
        $players = $num($request->query->get('players'));
        $q = trim((string) $request->query->get('q', ''));

        return $this->render('game/index.html.twig', [
            'games' => $games->findByFilters(
                $categoryId !== null ? (int) $categoryId : null,
                $maxPrice !== null ? (float) $maxPrice : null,
                $players !== null ? (int) $players : null,
                $q,
            ),
            'categories' => $categories->findBy([], ['name' => 'ASC']),
            'filters' => [
                'category' => $categoryId,
                'maxPrice' => $maxPrice,
                'players' => $players,
                'q' => $q,
            ],
        ]);
    }

    #[Route('/game/{id}', name: 'app_game_show', requirements: ['id' => '\d+'])]
    public function show(BoardGame $game): Response
    {
        return $this->render('game/show.html.twig', ['game' => $game]);
    }

    #[Route('/category/{id}', name: 'app_category_show', requirements: ['id' => '\d+'])]
    public function category(Category $category, BoardGameRepository $games): Response
    {
        return $this->render('game/category.html.twig', [
            'category' => $category,
            'games' => $games->findBy(['category' => $category], ['title' => 'ASC']),
        ]);
    }
}