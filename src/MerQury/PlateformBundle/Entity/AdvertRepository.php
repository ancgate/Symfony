<?php

namespace MerQury\PlateformBundle\Entity;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\EntityRepository;
/**
 * AdvertRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AdvertRepository extends EntityRepository {

    
    
    public function getAdverts($page,$nbPerPage) {
       $query = $this->createQueryBuilder('a')
      // Jointure sur l'attribut image
      ->leftJoin('a.image', 'i')
      ->addSelect('i')
      // Jointure sur l'attribut categories
      ->leftJoin('a.categories', 'c')
      ->addSelect('c')
      ->orderBy('a.date', 'DESC')
      ->getQuery();

        $query
      // On définit l'annonce à partir de laquelle commencer la liste
      ->setFirstResult(($page-1) * $nbPerPage)
      // Ainsi que le nombre d'annonce à afficher sur une page
      ->setMaxResults($nbPerPage)
    ;
    // Enfin, on retourne l'objet Paginator correspondant à la requête construite
    // (n'oubliez pas le use correspondant en début de fichier)
    return new Paginator($query, true);
    }

    public function myFindAll() {
        return $this
                        ->createQueryBuilder('a')
                        ->getQuery()
                        ->getResult()
        ;
    }

    public function myFindOne($id) {
        $qb = $this->createQueryBuilder('a');
        $qb
                ->where('a.id = :id')
                ->setParameter('id', $id)
        ;

        return $qb
                        ->getQuery()
                        ->getResult()
        ;
    }

    public function findByAuthorAndDate($author, $year) {
        $qb = $this->createQueryBuilder('a');
        $qb->where('a.author = :author')
                ->setParameter('author', $author)
                ->andWhere('a.date < :year')
                ->setParameter('year', $year)
                ->orderBy('a.date', 'DESC')
        ;
        return $qb
                        ->getQuery()
                        ->getResult()
        ;
    }

    public function whereCurrentYear(QueryBuilder $qb) {
        $qb
                ->andWhere('a.date BETWEEN :start AND :end')
                ->setParameter('start', new \Datetime(date('Y') . '-01-01'))  // Date entre le 1er janvier de cette année
                ->setParameter('end', new \Datetime(date('Y') . '-12-31'))  // Et le 31 décembre de cette année
        ;
    }

    public function myFind() {

        $qb = $this->createQueryBuilder('a');
        // On peut ajouter ce qu'on veut avant
        $qb
                ->where('a.author = :author')
                ->setParameter('author', 'Marine')
        ;


        // On applique notre condition sur le QueryBuilder
        $this->whereCurrentYear($qb);
        // On peut ajouter ce qu'on veut après
        $qb->orderBy('a.date', 'DESC');
        return $qb
                        ->getQuery()
                        ->getResult()
        ;
    }

    public function getAdvertWithApplications() {
        $qb = $this
                ->createQueryBuilder('a')
                ->leftJoin('a.applications', 'app')
                ->addSelect('app')
        ;

        return $qb->getQuery()->getResult();
    }

    public function getAdvertWithCategories(array $categoryNames) {

        $qb = $this->createQueryBuilder('a')->leftJoin('a.categories', 'c')->addSelect('c');
        $qb->where($qb->expr()->in('c.name', $categoryNames));
        return $qb = $this->getQuery()->getResult();
    }

}
