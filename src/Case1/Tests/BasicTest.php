<?php

namespace App\Tests;

use App\Case1\Entity\User;
use App\Case1\Exception\ValidationException;
use App\Case1\Service\Manager;
use App\Case1\Service\UserValidator;
use PHPUnit\Framework\TestCase;

class BasicTest extends TestCase
{
    public function testManagerValid()
    {
        $manager = $this->getManager();

        $user = new User();
        $user->setName("cooler2024");
        $user->setEmail("cooler2000111@ya.ru");
        $manager->persist($user);
        $manager->flush();

        $this->assertTrue(true);
    }

    protected function getManager(): Manager
    {
        //Где-то в дали загрустил маленький DI
        $manager = new Manager();
        new UserValidator($manager);

        return $manager;
    }

    public function testManagerDelete()
    {
        $manager = $this->getManager();
        $user = $manager->select(User::class, 1);

        $manager->delete($user);
        $manager->flush();

        $this->assertTrue(true);
    }

    public function testManagerInvalid1()
    {
        $manager = $this->getManager();

        $this->expectException(ValidationException::class);
        $user = new User();
        $user->setName("pupkin");
        $manager->persist($user);
        $manager->flush();
    }

    public function testManagerInvalid2()
    {
        $manager = $this->getManager();
        $user = $manager->select(User::class, 222);
        $this->assertSame($user, null);
    }

    public function testManagerGeneral()
    {
        $manager = $this->getManager();

        /** @var User $user */
        $user = $manager->select(User::class, 1);
        $this->assertIsObject($user);
    }

    public function testUserEntity()
    {
        $manager = $this->getManager();
        $validator = new UserValidator($manager);

        $user = new User();
        $this->assertContains("name", $validator->getErrors($user));

        $user->setName("Putin");
        $this->assertContains("name.length", $validator->getErrors($user));

        $user->setName("CoolGuy1944");
        $this->assertContains("name.regex", $validator->getErrors($user));

        $user->setName("putinism");
        $this->assertContains("name.denied_words", $validator->getErrors($user));

        $user->setName("coolguy1990");
        $this->assertContains("email", $validator->getErrors($user));

        $user->setEmail("rofling@mail.ru");
        $this->assertContains("email.domain", $validator->getErrors($user));

        $user->setEmail("roflmain22@22");
        $this->assertContains("email.email", $validator->getErrors($user));

        $user->setEmail("coollady_1970@yahoo.com");
        $this->assertContains("email.non_unique", $validator->getErrors($user));
    }

}
