<?php
namespace App\DataFixtures;
use App\Entity\{User,Category,Tag,Post,Comment};
use App\Util;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
 public function __construct(private UserPasswordHasherInterface $hasher) {}
 public function load(ObjectManager $manager):void
 {
   $admin=(new User())->setEmail('admin@newsroom.test')->setDisplayName('Admin')->setRoles(['ROLE_ADMIN'])->setActive(true);
   $admin->setPassword($this->hasher->hashPassword($admin,'admin12345'));
   $manager->persist($admin);
   $names=['Technology','Business','Culture','World','Lifestyle'];$categories=[];
   foreach($names as $name){$c=(new Category())->setName($name)->setSlug(Util::slug($name));$manager->persist($c);$categories[$name]=$c;}
   $tagNames=['AI','Startups','Climate','Design','Media','Remote Work'];$tags=[];
   foreach($tagNames as $name){$t=(new Tag())->setName($name)->setSlug(Util::slug($name));$manager->persist($t);$tags[$name]=$t;}
   $posts=[
    ['How AI is changing the newsroom in 2026','Technology','published','From research and verification to the final edit, newsroom teams are redesigning workflows around AI.','AI is changing how modern newsrooms research, verify, draft and distribute reporting. Human judgment remains central, but routine work is increasingly assisted by software.',['AI','Media']],
    ['The new economics of independent publishing','Business','published','Independent publishers are finding new ways to build sustainable audiences.','Memberships, newsletters, events and focused communities are giving smaller editorial teams more control over their economics.',['Media','Startups']],
    ['A city guide to the season’s best exhibitions','Culture','draft','A practical guide to the exhibitions worth adding to your weekend itinerary.','This draft collects standout exhibitions, gallery openings and design installations across the city.',['Design']],
    ['What the latest data says about remote work','World','review','The newest research paints a more nuanced picture of where work happens.','New workforce research suggests remote and hybrid work have become more structured, with meaningful differences by role and industry.',['Remote Work']],
   ];
   foreach($posts as [$title,$cat,$status,$excerpt,$content,$tagList]){ $p=(new Post())->setTitle($title)->setSlug(Util::slug($title))->setCategory($categories[$cat])->setStatus($status)->setExcerpt($excerpt)->setContent($content)->setAuthor($admin); foreach($tagList as $tn)$p->addTag($tags[$tn]);$manager->persist($p); }
   $manager->flush();
   $post=$manager->getRepository(Post::class)->findOneBy(['slug'=>Util::slug('How AI is changing the newsroom in 2026')]);
   $comments=[['Alex Morgan','alex@example.com','Clear analysis and practical examples. The section on verification was especially useful.','pending'],['Thu Ha','thu@example.com','Would love to see the same analysis for Southeast Asian publishers.','approved']];
   foreach($comments as [$n,$e,$b,$s]){$c=(new Comment())->setPost($post)->setAuthorName($n)->setAuthorEmail($e)->setBody($b)->setStatus($s);$manager->persist($c);}
   $manager->flush();
 }
}
