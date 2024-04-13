<?php

namespace App\Workflows\Customer\Activities;

use App\Models\User;
use Ramsey\Uuid\Uuid;
use Workflow\Activity;
use Illuminate\Support\Facades\Log;

class GenerateStrongPassword extends Activity
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
        $bytes = random_bytes(16);

        $uuid = Uuid::uuid8($bytes);

        $customer->fill(['password' => bcrypt($uuid)])->save();

        Log::channel('workflows')->debug(sprintf(
            '[%d] [%d] -> %s',
            getmypid(),
            (memory_get_usage(true) / 1024 / 1024),
            get_class($this),
        ));

        return $customer;
    }
}
