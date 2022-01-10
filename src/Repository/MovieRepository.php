<?php

namespace App\Repository;

use App\Entity\Movie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Movie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Movie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Movie[]    findAll()
 * @method Movie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MovieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Movie::class);
    }


    public function findOneWithGenre($id): ?Movie
    {
        // Le LEFT JOIN permet de conserver les films meme s'ils n'ont pas de genre associé
        // Le (INNER) JOIN ne récupère les films que s'ils ont des genres associés 
        $em = $this->getEntityManager();
        $query = $em->createQuery("
        
            SELECT m, g, c, p
            FROM App\Entity\Movie m
            LEFT JOIN m.genres g
            LEFT JOIN m.castings c
            LEFT JOIN c.person p
            WHERE m.id = :id
            ORDER BY m.title DESC 
        ");

        $query->setParameter(':id', $id);

               // version avec le queryBuilder
        // $qb = $this->createQueryBuilder('m');
        // $qb->addSelect('g');
        // $qb->join('m.genre', 'g');

        // $qb->addSelect('c');
        // $qb->join('m.castings', 'c');

        // $qb->addSelect('p');
        // $qb->join('c.person', 'p');

        // $qb->andWhere('m.id = :id');
        
        // $qb->setParameter(':id', $id);

        // $query = $qb->getQuery();

        // cette méthode permet de renvoyer un objet ou null si rien n'a été trouvé
        return $query->getOneOrNullResult();

    }


    public function findAllOrdered()
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery("
            SELECT m
            FROM App\Entity\Movie m
            ORDER BY m.title DESC 
        ");


        return $query->getResult();
    }
    

    public function findAllOrderedDQL()
    {
        // // on récupère un objet query Builder
        // $qb = $this->createQueryBuilder('m');
        // // on précise les spécificités de la requête
        // $qb->orderBy('m.title', 'ASC');
        // // on récupère un objet query
        // $query = $qb->getQuery();
        // // on renvoit les résultats de la requête
        // return $query->getResult();

        // le tout en "une" ligne
        return $this->createQueryBuilder('m') 
            ->orderBy('m.title', 'ASC')
            ->getQuery()
            ->getResult();
       
    }

    // /**
    //  * @return Movie[] Returns an array of Movie objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Movie
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
