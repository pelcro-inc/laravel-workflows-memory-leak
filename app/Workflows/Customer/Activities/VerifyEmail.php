<?php

namespace App\Workflows\Customer\Activities;

use App\Models\User;
use Workflow\Activity;
use Illuminate\Support\Facades\Log;
class VerifyEmail extends Activity
{
    public $connection = 'redis-queue-default';
    public $queue = 'workflows';

    /**
     * @throws \Exception
     */
    public function execute(User $customer)
    {
        $customer->fill(['email_verified_at' => now()])->save();

        Log::channel('workflows')->debug(sprintf(
            '[%d] [%d] -> %s',
            getmypid(),
            (memory_get_usage(true) / 1024 / 1024),
            get_class($this),
        ));
    }
}
