<?php

namespace Tests\Feature;

use Tests\TestCase;

class AuthTest extends TestCase
{
    public function testInvalidEmail()
    {
        $response = $this->post('/auth', [
            'email' => 'invalid@mail.com',
            'password' => '123456'
        ]);

        $response->assertStatus(401);
    }

    public function testInvalidPassword()
    {
        $response = $this->post('/auth', [
            'email' => 'adam@mail.com',
            'password' => '1234567'
        ]);

        $response->assertStatus(401);
    }

    public function testValid()
    {
        $response = $this->post('/auth', [
            'email' => 'adam@mail.com',
            'password' => '123456'
        ]);

        $response->assertStatus(200);
    }
}
