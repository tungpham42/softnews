<?php
namespace App\Security;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class SessionAuthenticator extends AbstractAuthenticator
{
 public function __construct(private EntityManagerInterface $em) {}
 public function supports(Request $request): ?bool {
   $path=$request->getPathInfo(); $method=strtoupper($request->getMethod());
   if(!str_starts_with($path,'/api') || $path==='/api/login') return false;
   if(($method==='GET' && ($path==='/api/posts' || preg_match('#^/api/posts/\d+$#',$path) || $path==='/api/categories' || $path==='/api/tags')) || ($method==='POST' && preg_match('#^/api/posts/\d+/comments$#',$path))) return false;
   return true;
 }
 public function authenticate(Request $request): Passport
 {
   $id=$request->getSession()->get('admin_user_id');
   if(!$id) throw new AuthenticationException('Authentication required.');
   $user=$this->em->getRepository(User::class)->find($id);
   if(!$user || !$user->isActive()) throw new AuthenticationException('Authentication required.');
   return new SelfValidatingPassport(new UserBadge($user->getUserIdentifier(),fn()=>$user));
 }
 public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response { return null; }
 public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response { return new JsonResponse(['error'=>'Unauthorized','message'=>$exception->getMessageKey()], Response::HTTP_UNAUTHORIZED); }
}
