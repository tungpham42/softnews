<?php
namespace App\Controller;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class AuthController extends ApiController
{
 #[Route('/login', methods:['POST'])]
 public function login(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): JsonResponse
 {
   $data=json_decode($request->getContent(),true) ?? [];
   $email=mb_strtolower(trim((string)($data['email']??'')));
   $password=(string)($data['password']??'');
   $user=$em->getRepository(User::class)->findOneBy(['email'=>$email]);
   if(!$user || !$user->isActive() || !$hasher->isPasswordValid($user,$password)) return $this->error('Invalid email or password.',Response::HTTP_UNAUTHORIZED);
   $request->getSession()->set('admin_user_id',$user->getId());
   return $this->ok(['user'=>$this->serializeUser($user)]);
 }

 #[Route('/me', methods:['GET'])]
 public function me(Request $request, EntityManagerInterface $em): JsonResponse
 {
   $id=$request->getSession()->get('admin_user_id');
   if(!$id)return $this->error('Unauthorized',401);
   $user=$em->find(User::class,$id);
   if(!$user)return $this->error('Unauthorized',401);
   return $this->ok(['user'=>$this->serializeUser($user)]);
 }

 #[Route('/logout', methods:['POST'])]
 public function logout(Request $request): JsonResponse
 {
   $request->getSession()->invalidate();
   return $this->ok(['message'=>'Logged out']);
 }
 private function serializeUser(User $user): array { return ['id'=>$user->getId(),'email'=>$user->getEmail(),'displayName'=>$user->getDisplayName(),'roles'=>$user->getRoles()]; }
}
