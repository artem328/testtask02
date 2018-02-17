<?php

namespace App\Tests\Controller\Auth;

use App\Entity\User;
use App\Tests\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class LoginControllerTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var \Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function testLoginPage(): void
    {
        $client = $this->createClient();

        $client->request('GET', '/login');

        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

        $this->seeLoginForm($client);
    }

    public function testLoginWithInvalidData(): void
    {
        $client = static::createClient();

        $client->request('POST', '/login', ['_username' => '', '_password' => '']);

        $response = $client->getResponse();

        $this->assertTrue($response->isRedirection());
        $this->assertRegExp('/\/login$/', $response->headers->get('Location'));
    }

    public function testLoginWithValidData(): void
    {
        $client = static::createClient();

        $name = 'John Doe';
        $username = 'username';
        $password = 'pass123';

        $user = new User();
        $user->setName($name);
        $user->setUsername($username);
        $user->setPassword($this->passwordEncoder->encodePassword($user, $password));

        $this->entityManager->persist($user);

        $this->entityManager->flush();

        $client->request(
            'POST',
            '/login',
            [
                '_username' => $username,
                '_password' => $password,
            ]
        );

        $response = $client->getResponse();

        $this->assertTrue($response->isRedirection());
        $this->assertRegExp('/\/$/', $response->headers->get('Location'));
    }

    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $kernel = static::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->passwordEncoder = $kernel->getContainer()->get('security.password_encoder');
    }

    /**
     * @param \Symfony\Bundle\FrameworkBundle\Client $client
     */
    protected function seeLoginForm(Client $client): void
    {
        $crawler = $client->getCrawler();

        $this->assertEquals(1, $crawler->filter('input[type="text"][name="_username"]')->count());
        $this->assertEquals(1, $crawler->filter('input[type="password"][name="_password"]')->count());
    }
}