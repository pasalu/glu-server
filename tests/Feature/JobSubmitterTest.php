<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\JobSubmitter;

class JobSubmitterTest extends TestCase
{
    /** @var JobSubmitter */
    private $jobsSubmitter;

    public function testSubmitWhenSuccessfulExpectJobID()
    {
        $jobID = $this->jobSubmitter->submit('ls -l', 3);

        $this->assertInternalType('int', $jobID);
    }

    public function setup()
    {
        $this->jobSubmitter = new JobSubmitter();
    }
}
