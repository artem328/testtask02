<?php

namespace App\Tests\Form;

use App\Entity\User;
use App\Form\UserType;
use App\Tests\TypeTestCase;

class UserTypeTest extends TypeTestCase
{

    public function testSubmitValidData(): void
    {
        $username = 'username';
        $name = 'John Doe';
        $password = '123';

        $formData = [
            'username' => $username,
            'name' => $name,
            'plainPassword' => [
                'first' => $password,
                'second' => $password,
            ],
        ];

        $form = $this->factory->create(UserType::class);

        $user = new User();
        $user->setUsername($username);
        $user->setName($name);
        $user->setPlainPassword($password);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($user, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }

    }

}