<?php
namespace App\Controller;
use App\Entity\Category;
use App\Util;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/categories')]
class CategoryController extends ApiController
{
 #[Route('',methods:['GET'])]
 public function index(EntityManagerInterface $em): JsonResponse { $items=$em->getRepository(Category::class)->ordered(); return $this->ok(['items'=>array_map(fn(Category $c)=>$this->data($c),$items)]); }
 #[Route('',methods:['POST'])]
 public function create(EntityManagerInterface $em): JsonResponse { $d=$this->body(); $name=trim((string)($d['name']??'')); if($name==='')return $this->error('Name is required.'); if($em->getRepository(Category::class)->findOneBy(['name'=>$name]))return $this->error('Category already exists.',409); $c=(new Category())->setName($name)->setSlug(Util::slug($name)); $em->persist($c);$em->flush();return $this->ok($this->data($c),201); }
 #[Route('/{id}',methods:['PUT'])]
 public function update(Category $category, EntityManagerInterface $em): JsonResponse { $d=$this->body(); $name=trim((string)($d['name']??'')); if($name==='')return $this->error('Name is required.'); $category->setName($name)->setSlug(Util::slug($name));$em->flush();return $this->ok($this->data($category)); }
 #[Route('/{id}',methods:['DELETE'])]
 public function delete(Category $category, EntityManagerInterface $em): JsonResponse { if($category->getPosts()->count()>0)return $this->error('Cannot delete a category that still has posts.',409);$em->remove($category);$em->flush();return $this->ok(['message'=>'Deleted']); }
 private function data(Category $c):array{return ['id'=>$c->getId(),'name'=>$c->getName(),'slug'=>$c->getSlug(),'count'=>$c->getPosts()->count()];}
}
