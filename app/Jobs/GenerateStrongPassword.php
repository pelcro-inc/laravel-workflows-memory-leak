<?php

namespace App\Jobs;

use App\Models\User;
use Ramsey\Uuid\Uuid;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;

class GenerateStrongPassword implements ShouldQueue, ShouldBeEncrypted
{
    use Queueable;
    use Batchable;
    use Dispatchable;
    use SerializesModels;
    use InteractsWithQueue;

    /**
     * The maximum number of exceptions to allow before failing.
     *
     * @var int
     */
    public int $maxExceptions = PHP_INT_MAX;

    /**
     * The number of seconds the job can run before it is considered failed.
     *
     * @var int
     */
    public int $timeout = PHP_INT_MAX;

    /**
     * The customer.
     *
     * @var User
     */
    private User $customer;

    /**
     * Constructor.
     *
     * @param User $customer
     */
    public function __construct(User $customer)
    {
        $this->customer = $customer;
    }

    /**
     * @throws \Exception
     */
    public function handle()
    {
        $bytes = random_bytes(16);

        $uuid = Uuid::uuid8($bytes);

        $this->customer->fill(['password' => bcrypt($uuid)])->save();

        Log::channel('jobs')->debug(sprintf(
            '[%d] [%d] -> %s',
            getmypid(),
            (memory_get_usage(true) / 1024 / 1024),
            get_class($this),
        ));
    }
}
