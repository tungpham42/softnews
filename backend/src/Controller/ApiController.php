<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
abstract class ApiController extends AbstractController
{
 public function __construct(private RequestStack $requestStack) {}
 protected function request(): \Symfony\Component\HttpFoundation\Request { return $this->requestStack->getCurrentRequest(); }
 protected function ok(mixed $data, int $status=200): JsonResponse { return $this->json($data,$status,['Content-Type'=>'application/json']); }
 protected function error(string $message,int $status=400): JsonResponse { return $this->json(['error'=>$message],$status); }
 protected function body(): array { $raw=$this->request()->getContent(); if($raw==='')return []; $data=json_decode($raw,true); return is_array($data)?$data:[]; }
 protected function slug(string $value): string { return \App\Util::slug($value); }
}
