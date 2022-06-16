<?php

namespace Tests\Feature;

use Tests\TestCase;

class TripTest extends TestCase
{
    public function testLogin()
    {
        $response = $this->postJson('/auth', [
            'email' => 'adam@mail.com',
            'password' => '123456'
        ]);

        $response->assertStatus(200);

        return $response->json();
    }

    public function testLoginAnotherUser()
    {
        $response = $this->postJson('/auth', [
            'email' => 'bilal@mail.com',
            'password' => '123456'
        ]);

        $response->assertStatus(200);

        return $response->json();
    }

    public function testCreateUnauthenticated()
    {
        $response = $this->postJson('/trips', [
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
        $response = $this->postJson('/trips', [
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
        $response = $this->postJson('/trips', [
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
        $response = $this->postJson('/trips', [
            'title' => 'JKT-SBY',
            'origin' => 'Jakarta',
            'destination' => 'Surabaya',
            'start_at' => '2022-07-01T09:00:00+07:00',
            'end_at' => '2022-07-25T09:00:00+07:00',
            'type' => 'business',
            'description' => 'Business trip to Surabaya',
        ], [
            'Authorization' => $auth['token_type'] . ' ' . $auth['access_token'],
        ]);

        $response->assertStatus(201);

        return (object) $response->json();
    }

    public function testListUnauthenticated()
    {
        $response = $this->getJson('/trips');

        $response->assertStatus(401);
    }

    /**
     * @depends testLogin
     */
    public function testListSucceed($auth)
    {
        $response = $this->getJson('/trips', [
            'Authorization' => $auth['token_type'] . ' ' . $auth['access_token'],
        ]);

        $response->assertStatus(200);
    }

    /**
     * @depends testCreateSucceed
     */
    public function testGetUnauthorized($trip)
    {
        $response = $this->getJson("/trips/{$trip->uuid}");

        $response->assertStatus(401);
    }

    /**
     * @depends testLogin
     */
    public function testGetNotFound($auth)
    {
        $response = $this->getJson('/trips/abc123', [
            'Authorization' => $auth['token_type'] . ' ' . $auth['access_token'],
        ]);

        $response->assertStatus(404);
    }

    /**
     * @depends testLoginAnotherUser
     * @depends testCreateSucceed
     */
    public function testGetAnotherUserRecordNotFound($auth, $trip)
    {
        $response = $this->getJson("/trips/{$trip->uuid}", [
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
        $response = $this->getJson("/trips/{$trip->uuid}", [
            'Authorization' => $auth['token_type'] . ' ' . $auth['access_token'],
        ]);

        $response->assertStatus(200);
    }

    /**
     * @depends testCreateSucceed
     */
    public function testUpdateUnauthorized($trip)
    {
        $response = $this->putJson("/trips/{$trip->uuid}", [
            'title' => "{$trip->title} Updated",
            'origin' => 'Jakarta',
            'destination' => 'Surabaya',
            'start_at' => '2022-07-01T09:00:00+07:00',
            'end_at' => '2022-07-25T09:00:00+07:00',
            'type' => 'business',
            'description' => "{$trip->description} updated",
        ]);

        $response->assertStatus(401);
    }

    /**
     * @depends testLogin
     */
    public function testUpdateNotFound($auth)
    {
        $response = $this->putJson('/trips/abc123', [
            'title' => 'JKT-SBY',
            'origin' => 'Jakarta',
            'destination' => 'Surabaya',
            'start_at' => '2022-07-01T09:00:00+07:00',
            'end_at' => '2022-07-25T09:00:00+07:00',
            'type' => 'business',
            'description' => 'Business trip to Surabaya',
        ], [
            'Authorization' => $auth['token_type'] . ' ' . $auth['access_token'],
        ]);

        $response->assertStatus(404);
    }

    /**
     * @depends testLoginAnotherUser
     * @depends testCreateSucceed
     */
    public function testUpdateAnotherUserRecordNotFound($auth, $trip)
    {
        $response = $this->putJson("/trips/{$trip->uuid}", [
            'title' => "{$trip->title} Updated",
            'origin' => 'Jakarta',
            'destination' => 'Surabaya',
            'start_at' => '2022-07-01T09:00:00+07:00',
            'end_at' => '2022-07-25T09:00:00+07:00',
            'type' => 'business',
            'description' => "{$trip->description} updated",
        ], [
            'Authorization' => $auth['token_type'] . ' ' . $auth['access_token'],
        ]);

        $response->assertStatus(404);
    }

    /**
     * @depends testLogin
     * @depends testCreateSucceed
     */
    public function testUpdateSucceed($auth, $trip)
    {
        $newTrip = [
            'title' => "{$trip->title} Updated",
            'origin' => 'Jakarta',
            'destination' => 'Surabaya',
            'type' => 'business',
            'description' => "{$trip->description} updated",
        ];

        $response = $this->putJson("/trips/{$trip->uuid}", $newTrip, [
            'Authorization' => $auth['token_type'] . ' ' . $auth['access_token'],
        ]);

        $response->assertStatus(200);
        $response->assertJson($newTrip);
    }

    /**
     * @depends testCreateSucceed
     */
    public function testDeleteUnauthorized($trip)
    {
        $response = $this->deleteJson("/trips/{$trip->uuid}");

        $response->assertStatus(401);
    }

    /**
     * @depends testLogin
     */
    public function testDeleteNotFound($auth)
    {
        $response = $this->deleteJson('/trips/abc123', [], [
            'Authorization' => $auth['token_type'] . ' ' . $auth['access_token'],
        ]);

        $response->assertStatus(404);
    }

    /**
     * @depends testLoginAnotherUser
     * @depends testCreateSucceed
     */
    public function testDeleteAnotherUserRecordNotFound($auth, $trip)
    {
        $response = $this->deleteJson("/trips/{$trip->uuid}", [], [
            'Authorization' => $auth['token_type'] . ' ' . $auth['access_token'],
        ]);

        $response->assertStatus(404);
    }

    /**
     * @depends testLogin
     * @depends testCreateSucceed
     */
    public function testDeleteSucceed($auth, $trip)
    {
        $response = $this->deleteJson("/trips/{$trip->uuid}", [], [
            'Authorization' => $auth['token_type'] . ' ' . $auth['access_token'],
        ]);

        $response->assertStatus(204);
    }
}
