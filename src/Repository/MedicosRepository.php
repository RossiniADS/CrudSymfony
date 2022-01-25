<?php

namespace App\Repository;

use App\Entity\Medico;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Medicos|null find($id, $lockMode = null, $lockVersion = null)
 * @method Medicos|null findOneBy(array $criteria, array $orderBy = null)
 * @method Medicos[]    findAll()
 * @method Medicos[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MedicosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Medico::class);
    }
}
