<?php

namespace App\Workflows;

use App\Models\User;
use Workflow\Workflow;
use Workflow\ActivityStub;
use Illuminate\Support\Facades\Log;
use App\Workflows\Customer\Activities\VerifyEmail;
use App\Workflows\Customer\Activities\GenerateStrongPassword;

class CustomerWorkflow extends Workflow
{

    public $connection = 'redis-queue-default';
    public $queue = 'workflows';

    /**
     * Execute workflow.
     *
     * @param int $customer_id
     * @return \Generator
     * @throws \Throwable
     */
    public function execute(int $customer_id): \Generator
    {
        /** @var User $subscription */
        $customer = User::findOrFail($customer_id);

        try {
            /** @var User $customer */
            $customer = yield ActivityStub::make(GenerateStrongPassword::class, $customer);
        } catch (\Throwable $throwable) {
            // handle the exception here
            throw $throwable;
        }

        try {
            /** @var User $customer */
            $customer = yield ActivityStub::make(VerifyEmail::class, $customer);
        } catch (\Throwable $throwable) {
            // handle the exception here
            throw $throwable;
        }

        // do other stuff
        // ...

        // Wipe objects.
        unset($customer);

        Log::channel('workflows')->debug(sprintf(
            '[%d] [%d] -> %s',
            getmypid(),
            (memory_get_usage(true) / 1024 / 1024),
            get_class($this),
        ));
    }
}
