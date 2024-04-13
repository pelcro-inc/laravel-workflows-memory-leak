## About

Vanilla Laravel 9 project /w version 1.0.23 of [laravel-workflow](https://github.com/laravel-workflow/laravel-workflow/tree/1.0.23) demonstrating inefficient memory usage.

## Installation instructions

Follow typical instructions to set up a Laravel project.
Copy .env.dist to .env, modify to fit your environment, flush cache and run migrations.

## Replication instructions

This test compares memory consumption using chained jobs and batches and Laravel Workflows.
Both are performing the same operations and dispatching 1000 processes.

### Test batched chain jobs
- Create one or many workers: `php artisan queue:work redis-queue-default --queue=jobs --memory 512`
- Generate jobs `php artisan test:jobs`
- Tail logs in real-time via `tail -f storage/logs/jobs.log` and observe a stable memory consumption over time.

### Test workflows
- Create one or many workers: `php artisan queue:work redis-queue-default --queue=workflows --memory 512`
- Generate workflows `php artisan test:workflows`
- Tail logs in real-time via `tail -f storage/logs/workflows.log` and observe a fast, consistent increase in memory consumption over time.

## Observations

- Using batched chain jobs, memory consumption reaches 26 MB
- Using Laravel Workflows, memory consumption reaches 182 MB