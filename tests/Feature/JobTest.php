<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\JobSubmitter;
use Illuminate\Foundation\Testing\RefreshDatabase;

class JobTest extends TestCase
{
    use RefreshDatabase;

    /** @var JobSubmitter */
    private $jobsSubmitter;

    public function testSubmitWhenSuccessfulExpectJobID()
    {
        $jobID = $this->jobSubmitter->submit('ls -l', 3);

        $this->assertInternalType('int', $jobID);
    }

    public function testGetTaskWhenNoTasksExpect500()
    {
        $result = $this->get('/task');

        $result->assertStatus(500);
    }

    public function setUp()
    {
        parent::setUp();

        $this->jobSubmitter = new JobSubmitter();
    }
}
