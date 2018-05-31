<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
         * Using for update in a transaction prevents the same job from being assigned to 2 job processors.
         *
         * SELECT jobid
         * FROM   jobs
         * WHERE  priority = (SELECT Max(priority)
         *                    FROM   jobs)
         * AND status = 'SUBMITTED'
         * GROUP  BY jobid
         * LIMIT  1
         * FOR UPDATE
         */
        $job =
            DB::table('jobs')
                ->select('jobID')
                ->whereRaw("priority = (SELECT MAX(priority) FROM jobs) and status = 'SUBMITTED'")
                ->groupBy('jobID')
                ->limit(1)
                ->lockForUpdate()
                ->first();

        if (!$job) {
            DB::rollBack();
            throw new \Exception("No available jobs!");
        }

        $jobID = $job->jobID;

        Log::info("Job: " . var_export($job, true));
        Log::info("JobID: " . $jobID);

        DB::table('jobs')
            ->where('jobID', $jobID)
            ->update(['status' => 'PROCESSING']);

        DB::commit();

        return $jobID;
    }
}

