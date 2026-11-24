<?php

namespace App\DataFixtures;

use App\Entity\BoardGame;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categories = [];
        foreach ([
            'Стратегії' => 'Планування, ресурси, довгі партії',
            'Кооперативні' => 'Грають разом проти гри',
            'Party-ігри' => 'Для великої компанії',
            'Карткові' => 'Колоди та карти',
            'Сімейні' => 'Прості правила для всіх віків',
        ] as $name => $desc) {
            $c = (new Category())->setName($name)->setDescription($desc);
            $manager->persist($c);
            $categories[$name] = $c;
        }

        // [назва, категорія, ціна, minP, maxP, хвилин, вік, є в наявності]
        $games = [
            ['Catan', 'Стратегії', 1450, 3, 4, 90, 10, true],
            ['Carcassonne', 'Сімейні', 980, 2, 5, 45, 7, true],
            ['Pandemic', 'Кооперативні', 1650, 2, 4, 60, 8, true],
            ['Dixit', 'Party-ігри', 1200, 3, 6, 30, 8, true],
            ['Ticket to Ride', 'Сімейні', 1790, 2, 5, 60, 8, false],
            ['Codenames', 'Party-ігри', 850, 2, 8, 15, 10, true],
            ['Terraforming Mars', 'Стратегії', 2400, 1, 5, 120, 12, true],
            ['Spirit Island', 'Кооперативні', 2900, 1, 4, 120, 13, true],
            ['Uno', 'Карткові', 320, 2, 10, 20, 7, true],
            ['Exploding Kittens', 'Карткові', 750, 2, 5, 15, 7, true],
            ['Azul', 'Сімейні', 1300, 2, 4, 40, 8, true],
            ['7 Wonders', 'Стратегії', 1850, 2, 7, 30, 10, false],
        ];

        foreach ($games as [$title, $cat, $price, $min, $max, $time, $age, $stock]) {
            $g = (new BoardGame())
                ->setTitle($title)
                ->setDescription("Настільна гра «{$title}».")
                ->setPrice((string) $price)
                ->setMinPlayers($min)
                ->setMaxPlayers($max)
                ->setPlayTimeMinutes($time)
                ->setMinAge($age)
                ->setInStock($stock)
                ->setCategory($categories[$cat]);
            $manager->persist($g);
        }

        $manager->flush();
    }
}