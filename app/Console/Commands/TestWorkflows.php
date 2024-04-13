<?php

namespace App\Console\Commands;

use App\Models\User;
use Workflow\WorkflowStub;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Workflows\CustomerWorkflow;

class TestWorkflows extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:workflows';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        User::factory()->count(1000)->create([
            'email_verified_at' => null,
        ]);

        $query = DB::connection('mysql')
            ->table('users')
            ->select('id')
            ->whereNull('email_verified_at');

        $query->lazyByIdDesc()->each(function ($customer)  {

            $workflow = WorkflowStub::make(CustomerWorkflow::class);
            $workflow->start($customer->id);

            $this->info("[{$workflow->id()}] Triggered workflow for Customer ID {$customer->id}");

        });

        exit(0);

    }
}

