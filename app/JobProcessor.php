<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class JobProcessor
{
    /**
     * @return int The jobID of the job to process
     * @throws \Exception
     */
    public function getJobToProcess()
    {
        DB::beginTransaction();

        /**
         * Using "for update" in a transaction prevents the same job from being assigned to 2 job processors.
         *
         * SELECT jobid
         * FROM   jobs
         * WHERE  priority = (SELECT Max(priority)
         *                    FROM   jobs
         *                    WHERE status = 'SUBMITTED')
         * AND status = 'SUBMITTED'
         * GROUP  BY jobid
         * LIMIT  1
         * FOR UPDATE
         */
        $job =
            DB::table('jobs')
                ->select('jobID')
                ->whereRaw(
                    "priority = (SELECT MAX(priority) FROM jobs WHERE status = 'SUBMITTED') and status = 'SUBMITTED'"
                )
                ->groupBy('jobID')
                ->limit(1)
                ->lockForUpdate()
                ->first();

        if (!$job) {
            DB::rollBack();
            throw new \Exception("No available jobs!");
        }

        $jobID = $job->jobID;

        DB::table('jobs')
            ->where('jobID', $jobID)
            ->update(['status' => 'PROCESSING']);

        DB::commit();

        return $jobID;
    }

    /**
     * @param int $jobID
     * @return string The output of the job.
     */
    public function process($jobID)
    {
        $commandOutput =
            DB::table('jobs')
                ->select('command')
                ->where('jobID', $jobID)
                ->first();

        $command = $commandOutput->command;

        Log::info("Running: '$command' JobID: $jobID");

        $process = new Process($command);
        $process->run();
        $output = $process->getOutput();

        DB::table('jobs')
            ->where('jobID', $jobID)
            ->update([
                'output' => $output,
                'finishedOn' => now(),
                'status' => 'FINISHED'
            ]);

        return $output;
    }

    /**
     * @param int $jobID
     * @return string
     * @throws \Exception
     */
    public function getStatus($jobID)
    {
        $result =
            DB::table('jobs')
                ->select('status')
                ->where('jobID', $jobID)
                ->first();

        if (!$result) {
            throw new \Exception("Invalid jobID $jobID");
        }

        return $result->status;
    }
}

