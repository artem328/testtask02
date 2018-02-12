<?php

namespace App\Tests\Controller\Auth;

use App\Entity\User;
use App\Tests\WebTestCase;
use Symfony\Component\HttpKernel\Client;

class RegisterControllerTest extends WebTestCase
{

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $entityManager;

    public function testRegisterPage(): void
    {
        $client = $this->createClient();

        $client->request('GET', '/register');

        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

        $this->seeRegisterForm($client);

    }

    public function testRegisterWithInvalidData(): void
    {
        $client = static::createClient();

        $client->request('POST', '/register');

        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

        $this->seeRegisterForm($client);
    }

    public function testRegisterWithValidData(): void
    {
        $client = static::createClient();

        $username = 'username';

        $client->request(
            'POST',
            '/register',
            [
                'user' => [
                    'username' => $username,
                    'name' => 'John Doe',
                    'plainPassword' => [
                        'first' => '123',
                        'second' => '123',
                    ],
                ],
            ]
        );

        $response = $client->getResponse();

        $this->assertTrue($response->isRedirection());

        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['username' => $username]);

        $this->assertInstanceOf(User::class, $user);
    }

    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * @param \Symfony\Component\HttpKernel\Client $client
     */
    protected function seeRegisterForm(Client $client)
    {
        $crawler = $client->getCrawler();

        $this->assertEquals(1, $crawler->filter('input[type="text"][name="user[username]"]')->count());
        $this->assertEquals(1, $crawler->filter('input[type="text"][name="user[name]"]')->count());
        $this->assertEquals(1, $crawler->filter('input[type="password"][name="user[plainPassword][first]"]')->count());
        $this->assertEquals(1, $crawler->filter('input[type="password"][name="user[plainPassword][second]"]')->count());
    }

}