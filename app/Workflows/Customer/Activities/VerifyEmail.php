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
     * Execute activity.
     *
     * @param User $customer
     * @return User
     * @throws \Throwable
     */
    public function execute(User $customer): User
    {
        $customer->fill(['email_verified_at' => now()])->save();

        Log::channel('workflows')->debug(sprintf(
            '[%d] [%d] -> %s',
            getmypid(),
            (memory_get_usage(true) / 1024 / 1024),
            get_class($this),
        ));

        return $customer;
    }
}
