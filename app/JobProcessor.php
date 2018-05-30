<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JobProcessor
{
    /**
     * @throws \Exception
     */
    public function getJobToProcess()
    {
        /**
         * SELECT jobid
         * FROM   jobs
         * WHERE  priority = (SELECT Max(priority)
         *                    FROM   jobs)
         * GROUP  BY jobid
         * LIMIT  1
         * FOR UPDATE
         */
        $job =
            DB::table('jobs')
                ->select('jobID')
                ->whereRaw('priority = (SELECT MAX(priority) FROM jobs)')
                ->groupBy('jobID')
                ->limit(1)
                ->lockForUpdate()
                ->first();

        Log::info("JobID: " . $job->jobID);
        return $job->jobID;
    }
}

