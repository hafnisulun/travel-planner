<?php

namespace Tests\Feature;

use Tests\TestCase;

class TripTest extends TestCase
{
    private $accessToken;
    private $tokenType;

    public function testListUnauthenticated()
    {
        $response = $this->json('get', '/trips');

        $response->assertStatus(401);
    }

    public function testListSucceed()
    {
        $this->login();

        $response = $this->json('get', '/trips', [
            'Authorization' => $this->tokenType . ' ' . $this->accessToken,
        ]);

        $response->assertStatus(200);
    }

    public function testCreateUnauthenticated()
    {
        $response = $this->json('post', '/trips', [
            "origin" => "Jakarta",
            "destination" => "Surabaya",
            "start_at" => "2022-07-01T09:00:00+07:00",
            "end_at" => "2022-07-25T09:00:00+07:00",
            "type" => "business",
            "description" => "Business trip to Surabaya",
        ]);

        $response->assertStatus(401);
    }

    public function testCreateTitleMissing()
    {
        $this->login();

        $response = $this->json('post', '/trips', [
            "origin" => "Jakarta",
            "destination" => "Surabaya",
            "start_at" => "2022-08-01T09:00:00+07:00",
            "end_at" => "2022-07-25T09:00:00+07:00",
            "type" => "business",
            "description" => "Business trip to Surabaya",
        ], [
            'Authorization' => $this->tokenType . ' ' . $this->accessToken,
        ]);

        $response->assertStatus(422);
    }

    public function testCreateScheduleInvalid()
    {
        $this->login();

        $response = $this->json('post', '/trips', [
            "title" => "JKT-SBY",
            "origin" => "Jakarta",
            "destination" => "Surabaya",
            "start_at" => "2022-08-01T09:00:00+07:00",
            "end_at" => "2022-07-25T09:00:00+07:00",
            "type" => "business",
            "description" => "Business trip to Surabaya",
        ], [
            'Authorization' => $this->tokenType . ' ' . $this->accessToken,
        ]);

        $response->assertStatus(422);
    }

    public function testCreateSucceed()
    {
        $this->login();

        $response = $this->json('post', '/trips', [
            "title" => "JKT-SBY",
            "origin" => "Jakarta",
            "destination" => "Surabaya",
            "start_at" => "2022-07-01T09:00:00+07:00",
            "end_at" => "2022-07-25T09:00:00+07:00",
            "type" => "business",
            "description" => "Business trip to Surabaya",
        ], [
            'Authorization' => $this->tokenType . ' ' . $this->accessToken,
        ]);

        $response->assertStatus(201);
    }

    private function login()
    {
        $response = $this->json('post', '/auth', [
            'email' => 'adam@mail.com',
            'password' => '123456'
        ]);

        $this->accessToken = $response->json('access_token');
        $this->tokenType = $response->json('token_type');
    }
}
