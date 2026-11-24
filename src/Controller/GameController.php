<?php

namespace App\Controller;

use App\Entity\BoardGame;
use App\Entity\Category;
use App\Repository\BoardGameRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GameController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(BoardGameRepository $games, CategoryRepository $categories): Response
    {
        return $this->render('game/index.html.twig', [
            'games' => $games->findBy([], ['title' => 'ASC']),
            'categories' => $categories->findBy([], ['name' => 'ASC']),
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