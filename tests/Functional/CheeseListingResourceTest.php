<?php

namespace App\Tests\Functional;

use App\Entity\CheeseListing;
use App\Entity\User;
use App\Test\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class CheeseListingResourceTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;
    public function testCreateCheeseListing()
    {
        $client = self::createClient();

        $client->request('POST', '/api/cheeses', [
            'json'=>[]
        ]);
        $this->assertResponseStatusCodeSame(401);

        $this->createUser('cheeseplease@example.com','foo');

        $this->CreateUserAndLogIn($client,'cheeseplease1@example.com','foo');

        $client->request('POST', '/api/cheeses', [
            'json'=>[]
        ]);
        $this->assertResponseStatusCodeSame(400);

    }
    public function testUpdateCheeseListing()
    {
        $client = self::createClient();
        $user= $this->CreateUser('user@example.com','foo');
        $user1= $this->CreateUser('user1@example.com','foo');

        $cheeseListing = new CheeseListing('Block of chedar');
        $cheeseListing->setOwner($user);
        $cheeseListing->setPrice(1000);
        $cheeseListing->setDescription('Block of chedar');

        $em = $this->getEntityManager();
        $em->persist($cheeseListing);
        $em->flush();

        $this->logIn($client,'user1@example.com','foo');

        $client->request('PUT', '/api/cheeses/'.$cheeseListing->getId(), [
            'json'=>['title'=>'updated ', 'owner'=>'/api/users/'.$user1->getId()]
        ]);
        $this->assertResponseStatusCodeSame(403);

        $this->logIn($client,'user@example.com','foo');
        $client->request('PUT', '/api/cheeses/'.$cheeseListing->getId(), [
            'json'=>['title'=>'updated ']
        ]);
        $this->assertResponseStatusCodeSame(200);
    }

}