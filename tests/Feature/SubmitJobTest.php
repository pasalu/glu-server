<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SubmitJobTest extends TestCase
{
    public function testPostToTaskWhenSuccessfulExpectJobID()
    {
        $response = $this->post('task');
        $response->assertStatus(200);
    }
}
