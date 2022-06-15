<?php

namespace Tests\Feature;

use Tests\TestCase;

class TripTest extends TestCase
{
    private $accessToken;
    private $tokenType;

    public function testListUnauthenticated()
    {
        $response = $this->get('/trips');

        $response->assertStatus(401);
    }

    public function testListSucceed()
    {
        $this->login();

        $response = $this->get('/trips', [
            'Authorization' => $this->tokenType . ' ' . $this->accessToken,
        ]);

        $response->assertStatus(200);
    }

    private function login()
    {
        $response = $this->post('/auth', [
            'email' => 'adam@mail.com',
            'password' => '123456'
        ]);

        $this->accessToken = $response->json('access_token');
        $this->tokenType = $response->json('token_type');
    }
}
