<?php

namespace App\Repository;

use App\Entity\BoardGame;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BoardGame>
 */
class BoardGameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BoardGame::class);
    }

    /** @return BoardGame[] */
    public function findByFilters(
        ?int $categoryId = null,
        ?float $maxPrice = null,
        ?int $players = null,
        ?string $q = null,
    ): array {
        $qb = $this->createQueryBuilder('g')
            ->addSelect('c')
            ->join('g.category', 'c')
            ->orderBy('g.title', 'ASC');

        if ($categoryId !== null) {
            $qb->andWhere('c.id = :cat')->setParameter('cat', $categoryId);
        }
        if ($maxPrice !== null) {
            $qb->andWhere('g.price <= :maxPrice')->setParameter('maxPrice', $maxPrice);
        }
        if ($players !== null) {
            $qb->andWhere(':players BETWEEN g.minPlayers AND g.maxPlayers')
                ->setParameter('players', $players);
        }
        if ($q !== null && $q !== '') {
            $qb->andWhere('LOWER(g.title) LIKE :q')
                ->setParameter('q', '%' . mb_strtolower($q) . '%');
        }

        return $qb->getQuery()->getResult();
    }
}