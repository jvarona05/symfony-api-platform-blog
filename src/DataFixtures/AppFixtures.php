<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use App\Entity\Comment;
use App\Entity\User;
use App\Security\TokenAuthenticator;
use App\Security\TokenGenerator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private const USER = [
        [
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'fullname' => 'Pepe',
            'password' => 'Alosk@olsdjnkfg09sdgf',
            'roles' => [User::ROLE_ADMIN],
            'enabled' => true
        ],
        [
            'username' => 'jmrv001',
            'email' => 'jmrv001@gmail.com',
            'fullname' => 'Jose',
            'password' => 'Alosk@olsdjnkfg09sdgf',
            'roles' => [User::ROLE_SUPERADMIN],
            'enabled' => true
        ],
        [
            'username' => 'jmrv003',
            'email' => 'jmrv003@gmail.com',
            'fullname' => 'Marcos',
            'password' => 'Alosk@olsdjnkfg09sdgf',
            'roles' => [User::ROLE_WRITER],
            'enabled' => true
        ],
        [
            'username' => 'jmrv004',
            'email' => 'jmrv004@gmail.com',
            'fullname' => 'Rafa',
            'password' => 'Alosk@olsdjnkfg09sdgf',
            'roles' => [User::ROLE_WRITER],
            'enabled' => true
        ],
        [
            'username' => 'jmrv005',
            'email' => 'jmrv005@gmail.com',
            'fullname' => 'Yormi',
            'password' => 'Alosk@olsdjnkfg09sdgf',
            'roles' => [User::ROLE_EDITOR],
            'enabled' => false
        ],
        [
            'username' => 'jmrv006',
            'email' => 'jmrv006@gmail.com',
            'fullname' => 'Yormi',
            'password' => 'Alosk@olsdjnkfg09sdgf',
            'roles' => [User::ROLE_COMMENTATOR],
            'enabled' => true
        ]
    ];

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * @var Factory
     */
    private $faker;

    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;

    public function __construct(
        UserPasswordEncoderInterface $encoder,
        TokenGenerator $tokenGenerator
    ){
        $this->encoder = $encoder;
        $this->tokenGenerator = $tokenGenerator;
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager)
    {
        $this->loadUser($manager);
        $this->loadBlogPosts($manager);
        $this->loadComments($manager);
    }

    public function loadBlogPosts(ObjectManager $manager)
    {
        for ($i=0; $i<100; $i++) {
            $post = new BlogPost();
            $post->setAuthor($this->getRandomUser($post))
                    ->setContent($this->faker->realText())
                    ->setPublished($this->faker->dateTimeThisYear)
                    ->setSlug($this->faker->slug)
                    ->setTitle($this->faker->realText(30));

            $this->setReference("post_{$i}", $post);

            $manager->persist($post);
        }

        $manager->flush();
    }

    public function loadComments(ObjectManager $manager)
    {
        for ($i=0; $i<100; $i++) {
            for ($j=0; $j<rand(1,10); $j++) {
                $comment = new Comment();
                $comment->setAuthor($this->getRandomUser($comment))
                        ->setPublished($this->faker->dateTimeThisYear)
                        ->setContent($this->faker->realText())
                        ->setPost($this->getReference("post_{$i}"));
                $manager->persist($comment);
            }
        }

        $manager->flush();
    }

    public function loadUser(ObjectManager $manager)
    {
        foreach (self::USER as $userFixture){
            $user = new User();
            $user->setEmail($userFixture['email'])
                    ->setFullname($userFixture['fullname'])
                    ->setUsername($userFixture['username'])
                    ->setRoles($userFixture['roles'])
                    ->setEnabled($userFixture['enabled'])
                    ->setPassword(
                        $this->encoder->encodePassword($user, $userFixture['password'])
                    );
            if(!$userFixture['enabled']){
                $user->setConfirmationToken(
                    $this->tokenGenerator->getRandomSecureToken()
                );
            }

            $this->addReference("user_{$userFixture['username']}", $user);

            $manager->persist($user);
        }

        $manager->flush();
    }

    public function getRandomUser($entity)
    {
        $randomUser = self::USER[rand(0, count(self::USER)-1)];

        if($entity instanceof BlogPost && !count(array_intersect(
            $randomUser['roles'],
            [User::ROLE_SUPERADMIN, User::ROLE_ADMIN, User::ROLE_WRITER]))
        ){
            return $this->getRandomUser($entity);
        }

        if($entity instanceof Comment && !count(array_intersect(
                $randomUser['roles'],
                [User::ROLE_SUPERADMIN, User::ROLE_ADMIN, User::ROLE_WRITER, User::ROLE_COMMENTATOR]))
        ){
            return $this->getRandomUser($entity);
        }

        return $this->getReference("user_{$randomUser['username']}");
    }
}
