<?php

namespace App;

use Webpatser\Uuid\Uuid;
use App\Jobs;

class JobSubmitter
{
    private $submitterID;

    /**
     * JobSubmitter constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        // Using UUID's here to avoid other job submitters knowing about each
        // other.
        $this->submitterID = (string) Uuid::generate();
    }

    /**
     * Insert the command into the database for later processing. Returns the
     * jobID which can be used for checking the status of the job.
     * @param string $command
     * @param int $priority
     * @return int
     */
    public function submit(string $command, int $priority): int
    {
        $jobID = Jobs::create([
            'submitterID' => $this->submitterID,
            'status' =>'SUBMITTED',
            'command' => $command,
            'output' => NULL,
            'priority' => $priority,
            'submittedOn' => now()
        ])->id;

        return $jobID;
    }
}
