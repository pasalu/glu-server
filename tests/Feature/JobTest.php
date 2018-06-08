<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class JobTest extends TestCase
{
    use RefreshDatabase;

    public function testPostTaskWhenSuccessfulExpectJobID()
    {
        $result = $this->post('/task', ['command' => 'whoami', 'priority' => 3]);

        $result->assertJson(['jobID' => 1]);
    }

    public function testGetStatusWhenSubmittedExpectSubmitted()
    {
        $result = $this->post('/task', ['command' => 'whoami', 'priority' => 3]);
        $jobIDJson = json_decode($result->getContent(), true);
        $jobID = $jobIDJson['jobID'];

        $result = $this->get("/task/$jobID");

        $result->assertJson(['status' => 'SUBMITTED']);
    }

    public function testGetStatusWhenRequestedByJobProcessorExpectProcessing()
    {
        $result = $this->post('/task', ['command' => 'whoami', 'priority' => 3]);
        $jobIDJson = json_decode($result->getContent(), true);
        $jobID = $jobIDJson['jobID'];
        $this->get('/task');

        $result = $this->get("/task/$jobID");

        $result->assertJson(['status' => 'PROCESSING']);
    }

    public function testGetStatusWhenFinishedProcessingExpectFinished()
    {
        $result = $this->post('/task', ['command' => 'whoami', 'priority' => 3]);
        $jobIDJson = json_decode($result->getContent(), true);
        $jobID = $jobIDJson['jobID'];
        $this->post("/process/$jobID");

        $result = $this->get("/task/$jobID");

        $result->assertJson(['status' => 'FINISHED']);
    }

    public function testGetAverageWhenNoJobsExpect500()
    {
        $result = $this->get('/getAverageProcessingTime');

        $result->assertStatus(500);
    }

    public function testGetAverageWhenFinishedJobsExpectAverage()
    {
        $result = $this->post('/task', ['command' => 'whoami', 'priority' => 3]);
        $jobIDJson = json_decode($result->getContent(), true);
        $jobID = $jobIDJson['jobID'];
        $this->post("/process/$jobID");
        $this->get("/task/$jobID");

        $result = $this->get('/getAverageProcessingTime');
        $averageArray = json_decode($result->getContent(), true);

        $this->assertArrayHasKey('average', $averageArray);
    }

    public function testProcessWhen2JobsAndNoSpecifiedJobExpectHigherPriorityJobProcessed()
    {
        $whoamiResult = $this->post('/task', ['command' => 'whoami', 'priority' => 5]);
        $lsResult = $this->post('/task', ['command' => 'ls', 'priority' => 3]);
        $whoamiJobID = json_decode($whoamiResult->getContent(), true)['jobID'];
        $lsJobID = json_decode($lsResult->getContent(), true)['jobID'];

        $this->post('/process');

        $whoamiStatus = $this->get("/task/$whoamiJobID");
        $lsStatus = $this->get("/task/$lsJobID");
        $whoamiStatus->assertJson(['status' => 'FINISHED']);
        $lsStatus->assertJson(['status' => 'SUBMITTED']);
    }

    public function testGetTaskWhenNoTasksExpect500()
    {
        $result = $this->get('/task');

        $result->assertStatus(500);
    }
}
