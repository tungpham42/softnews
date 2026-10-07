<?php
namespace App\Controller;
use App\Entity\{Post,Category,Tag,User,Comment};
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/posts')]
class PostController extends ApiController
{
 #[Route('',methods:['GET'])]
 public function index(PostRepository $repo):JsonResponse { $r=$repo->search((string)$this->getRequest()->query->get('q',''),$this->getRequest()->query->get('status') ?: null); return $this->ok(['items'=>array_map(fn(Post $p)=>$this->data($p),$r)]); }
 #[Route('/{id}',methods:['GET'])]
 public function show(Post $post):JsonResponse { return $this->ok($this->data($post,true)); }
 #[Route('',methods:['POST'])]
 public function create(EntityManagerInterface $em):JsonResponse { $d=$this->body(); try{$p=$this->upsert(new Post(),$d,$em);$p->setAuthor($this->currentUser($em));$em->persist($p);$em->flush();return $this->ok($this->data($p),201);}catch(\Throwable $e){return $this->error($e->getMessage(),400);} }
 #[Route('/{id}',methods:['PUT'])]
 public function update(Post $post,EntityManagerInterface $em):JsonResponse { try{$this->upsert($post,$this->body(),$em);$em->flush();return $this->ok($this->data($post));}catch(\Throwable $e){return $this->error($e->getMessage(),400);} }
 #[Route('/{id}',methods:['DELETE'])]
 public function delete(Post $post,EntityManagerInterface $em):JsonResponse{$em->remove($post);$em->flush();return $this->ok(['message'=>'Deleted']);}
 #[Route('/{id}/comments',methods:['POST'])]
 public function comment(Post $post,EntityManagerInterface $em):JsonResponse{$d=$this->body();$name=trim((string)($d['authorName']??''));$email=trim((string)($d['authorEmail']??''));$body=trim((string)($d['body']??''));if($name===''||$email===''||$body==='')return $this->error('Name, email and comment are required.');$c=(new Comment())->setPost($post)->setAuthorName($name)->setAuthorEmail($email)->setBody($body);$em->persist($c);$em->flush();return $this->ok(['message'=>'Comment submitted for moderation.'],201);}
 private function upsert(Post $p,array $d,EntityManagerInterface $em):Post{$title=trim((string)($d['title']??''));$content=(string)($d['content']??'');$excerpt=(string)($d['excerpt']??'');$status=(string)($d['status']??'draft');$categoryId=(int)($d['categoryId']??0);if($title===''||$content==='')throw new \RuntimeException('Title and content are required.');if(!in_array($status,['draft','review','published'],true))throw new \RuntimeException('Invalid status.');$cat=$em->find(Category::class,$categoryId);if(!$cat)throw new \RuntimeException('Category not found.');$p->setTitle($title)->setSlug($this->uniqueSlug($this->slug($title),$p,$em))->setExcerpt($excerpt)->setContent($content)->setStatus($status)->setCategory($cat)->setCoverImage($d['coverImage']??null);$p->clearTags();foreach(($d['tagIds']??[]) as $tid){$tag=$em->find(Tag::class,(int)$tid);if($tag)$p->addTag($tag);}return $p;}
 private function uniqueSlug(string $slug,Post $post,EntityManagerInterface $em):string{$base=$slug;$i=2;while(true){$existing=$em->getRepository(Post::class)->findOneBy(['slug'=>$slug]);if(!$existing||$existing->getId()===$post->getId())return $slug;$slug=$base.'-'.$i++;}}
 private function currentUser(EntityManagerInterface $em):User{$id=$this->getRequest()->getSession()->get('admin_user_id');$u=$id?$em->find(User::class,$id):null;if(!$u)throw new \RuntimeException('Unauthorized.');return $u;}
 private function data(Post $p,bool $detail=false):array{$tags=[];foreach($p->getTags() as $t)$tags[]=['id'=>$t->getId(),'name'=>$t->getName(),'slug'=>$t->getSlug()];$d=['id'=>$p->getId(),'title'=>$p->getTitle(),'slug'=>$p->getSlug(),'excerpt'=>$p->getExcerpt(),'status'=>$p->getStatus(),'coverImage'=>$p->getCoverImage(),'views'=>$p->getViews(),'createdAt'=>$p->getCreatedAt()->format(DATE_ATOM),'publishedAt'=>$p->getPublishedAt()?->format(DATE_ATOM),'category'=>['id'=>$p->getCategory()->getId(),'name'=>$p->getCategory()->getName(),'slug'=>$p->getCategory()->getSlug()],'author'=>['id'=>$p->getAuthor()->getId(),'displayName'=>$p->getAuthor()->getDisplayName()],'tags'=>$tags];if($detail)$d['content']=$p->getContent();return $d;}
}
