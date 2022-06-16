<?php

namespace Tests\Feature;

use Tests\TestCase;

class TripTest extends TestCase
{
    public function testLogin()
    {
        $response = $this->json('post', '/auth', [
            'email' => 'adam@mail.com',
            'password' => '123456'
        ]);

        $response->assertStatus(200);

        return $response->json();
    }

    public function testLoginAnotherUser()
    {
        $response = $this->json('post', '/auth', [
            'email' => 'bilal@mail.com',
            'password' => '123456'
        ]);

        $response->assertStatus(200);

        return $response->json();
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

    /**
     * @depends testLogin
     */
    public function testCreateTitleMissing($auth)
    {
        $response = $this->json('post', '/trips', [
            "origin" => "Jakarta",
            "destination" => "Surabaya",
            "start_at" => "2022-08-01T09:00:00+07:00",
            "end_at" => "2022-07-25T09:00:00+07:00",
            "type" => "business",
            "description" => "Business trip to Surabaya",
        ], [
            'Authorization' => $auth['token_type'] . ' ' . $auth['access_token'],
        ]);

        $response->assertStatus(422);
    }

    /**
     * @depends testLogin
     */
    public function testCreateScheduleInvalid($auth)
    {
        $response = $this->json('post', '/trips', [
            "title" => "JKT-SBY",
            "origin" => "Jakarta",
            "destination" => "Surabaya",
            "start_at" => "2022-08-01T09:00:00+07:00",
            "end_at" => "2022-07-25T09:00:00+07:00",
            "type" => "business",
            "description" => "Business trip to Surabaya",
        ], [
            'Authorization' => $auth['token_type'] . ' ' . $auth['access_token'],
        ]);

        $response->assertStatus(422);
    }

    /**
     * @depends testLogin
     */
    public function testCreateSucceed($auth)
    {
        $response = $this->json('post', '/trips', [
            "title" => "JKT-SBY",
            "origin" => "Jakarta",
            "destination" => "Surabaya",
            "start_at" => "2022-07-01T09:00:00+07:00",
            "end_at" => "2022-07-25T09:00:00+07:00",
            "type" => "business",
            "description" => "Business trip to Surabaya",
        ], [
            'Authorization' => $auth['token_type'] . ' ' . $auth['access_token'],
        ]);

        $response->assertStatus(201);

        return (object) $response->json();
    }

    public function testListUnauthenticated()
    {
        $response = $this->json('get', '/trips');

        $response->assertStatus(401);
    }

    /**
     * @depends testLogin
     */
    public function testListSucceed($auth)
    {
        $response = $this->json('get', '/trips', [], [
            'Authorization' => $auth['token_type'] . ' ' . $auth['access_token'],
        ]);

        $response->assertStatus(200);
    }

    /**
     * @depends testCreateSucceed
     */
    public function testGetUnauthorized($trip)
    {
        $response = $this->json('get', "/trips/{$trip->uuid}");

        $response->assertStatus(401);
    }

    /**
     * @depends testLogin
     */
    public function testGetNotFound($auth)
    {
        $response = $this->json('get', '/trips/abc123', [], [
            'Authorization' => $auth['token_type'] . ' ' . $auth['access_token'],
        ]);

        $response->assertStatus(404);
    }

    /**
     * @depends testLoginAnotherUser
     * @depends testCreateSucceed
     */
    public function testGetAnotherUserTripNotFound($auth, $trip)
    {
        $response = $this->json('get', "/trips/{$trip->uuid}", [], [
            'Authorization' => $auth['token_type'] . ' ' . $auth['access_token'],
        ]);

        $response->assertStatus(404);
    }

    /**
     * @depends testLogin
     * @depends testCreateSucceed
     */
    public function testGetSucceed($auth, $trip)
    {
        $response = $this->json('get', "/trips/{$trip->uuid}", [], [
            'Authorization' => $auth['token_type'] . ' ' . $auth['access_token'],
        ]);

        $response->assertStatus(200);
    }
}
