<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Tests\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class DashboardControllerTest extends WebTestCase
{

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var \Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function testUnauthorizedAccess(): void
    {
        $client = static::createClient();

        $client->request('GET', '/');

        $response = $client->getResponse();

        $this->assertTrue($response->isRedirection());
        $this->assertRegExp('/\/login$/', $response->headers->get('Location'));
    }

    public function testAuthorizedAccess(): void
    {
        $name = 'John Doe';
        $username = 'username';
        $password = 'pass123';

        $user = new User();
        $user->setName($name);
        $user->setUsername($username);
        $user->setPassword($this->passwordEncoder->encodePassword($user, $password));

        $this->entityManager->persist($user);

        $this->entityManager->flush();

        $client = static::createClient();

        $session = $client->getContainer()->get('session');

        $firewallContext = 'main';

        $token = new UsernamePasswordToken(
            $user,
            null,
            $firewallContext,
            $user->getRoles()
        );
        $session->set('_security_'.$firewallContext, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);

        $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $client->getCrawler()->filter('html:contains("Hello John Doe")')->count());
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
}