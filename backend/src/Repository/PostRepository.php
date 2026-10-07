<?php
namespace App\Repository;
use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
class PostRepository extends ServiceEntityRepository
{
 public function __construct(ManagerRegistry $registry){parent::__construct($registry,Post::class);}
 public function findPublished(int $limit=20): array {return $this->createQueryBuilder('p')->andWhere('p.status = :status')->setParameter('status','published')->orderBy('p.publishedAt','DESC')->setMaxResults($limit)->getQuery()->getResult();}
 public function search(?string $q=null, ?string $status=null): array { $qb=$this->createQueryBuilder('p')->orderBy('p.createdAt','DESC'); if($q){$qb->andWhere('p.title LIKE :q OR p.excerpt LIKE :q')->setParameter('q','%'.$q.'%');} if($status){$qb->andWhere('p.status = :status')->setParameter('status',$status);} return $qb->getQuery()->getResult(); }
}
