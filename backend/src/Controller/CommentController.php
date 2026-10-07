<?php
namespace App\Controller;
use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/comments')]
class CommentController extends ApiController
{
 #[Route('',methods:['GET'])]
 public function index(EntityManagerInterface $em):JsonResponse{$items=$em->getRepository(Comment::class)->latest();return $this->ok(['items'=>array_map(fn(Comment $c)=>$this->data($c),$items)]);}
 #[Route('/{id}/status',methods:['PATCH'])]
 public function status(Comment $comment,EntityManagerInterface $em):JsonResponse{$d=$this->body();$status=(string)($d['status']??'');if(!in_array($status,['pending','approved','rejected'],true))return $this->error('Invalid status.');$comment->setStatus($status);$em->flush();return $this->ok($this->data($comment));}
 #[Route('/{id}',methods:['DELETE'])]
 public function delete(Comment $comment,EntityManagerInterface $em):JsonResponse{$em->remove($comment);$em->flush();return $this->ok(['message'=>'Deleted']);}
 private function data(Comment $c):array{return ['id'=>$c->getId(),'authorName'=>$c->getAuthorName(),'authorEmail'=>$c->getAuthorEmail(),'body'=>$c->getBody(),'status'=>$c->getStatus(),'createdAt'=>$c->getCreatedAt()->format(DATE_ATOM),'post'=>['id'=>$c->getPost()->getId(),'title'=>$c->getPost()->getTitle()]];}
}
