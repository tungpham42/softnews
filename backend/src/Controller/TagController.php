<?php
namespace App\Controller;
use App\Entity\Tag;
use App\Util;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/tags')]
class TagController extends ApiController
{
 #[Route('',methods:['GET'])]
 public function index(EntityManagerInterface $em):JsonResponse{$items=$em->getRepository(Tag::class)->ordered();return $this->ok(['items'=>array_map(fn(Tag $t)=>$this->data($t),$items)]);}
 #[Route('',methods:['POST'])]
 public function create(EntityManagerInterface $em):JsonResponse{$d=$this->body();$name=trim((string)($d['name']??''));if($name==='')return $this->error('Name is required.');if($em->getRepository(Tag::class)->findOneBy(['name'=>$name]))return $this->error('Tag already exists.',409);$t=(new Tag())->setName($name)->setSlug(Util::slug($name));$em->persist($t);$em->flush();return $this->ok($this->data($t),201);}
 #[Route('/{id}',methods:['PUT'])]
 public function update(Tag $tag,EntityManagerInterface $em):JsonResponse{$d=$this->body();$name=trim((string)($d['name']??''));if($name==='')return $this->error('Name is required.');$tag->setName($name)->setSlug(Util::slug($name));$em->flush();return $this->ok($this->data($tag));}
 #[Route('/{id}',methods:['DELETE'])]
 public function delete(Tag $tag,EntityManagerInterface $em):JsonResponse{$em->remove($tag);$em->flush();return $this->ok(['message'=>'Deleted']);}
 private function data(Tag $t):array{return ['id'=>$t->getId(),'name'=>$t->getName(),'slug'=>$t->getSlug(),'count'=>$t->getPosts()->count()];}
}
