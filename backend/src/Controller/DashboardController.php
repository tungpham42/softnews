<?php
namespace App\Controller;
use App\Entity\{Post,Category,Tag,Comment};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/api/dashboard')]
class DashboardController extends ApiController
{
 #[Route('',methods:['GET'])]
 public function index(EntityManagerInterface $em):JsonResponse{return $this->ok(['posts'=>$em->getRepository(Post::class)->count([]),'publishedPosts'=>$em->getRepository(Post::class)->count(['status'=>'published']),'draftPosts'=>$em->getRepository(Post::class)->count(['status'=>'draft']),'reviewPosts'=>$em->getRepository(Post::class)->count(['status'=>'review']),'categories'=>$em->getRepository(Category::class)->count([]),'tags'=>$em->getRepository(Tag::class)->count([]),'comments'=>$em->getRepository(Comment::class)->count([]),'pendingComments'=>$em->getRepository(Comment::class)->count(['status'=>'pending'])]);}
}
