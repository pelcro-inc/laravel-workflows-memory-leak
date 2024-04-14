## About

Vanilla Laravel project /w version 1.0.23 of [laravel-workflow](https://github.com/laravel-workflow/laravel-workflow/tree/1.0.23) demonstrating inefficient memory usage.

## Installation instructions

Follow typical instructions to set up a Laravel project.
Copy .env.dist to .env, modify to fit your environment, flush cache and run migrations.

## Replication instructions

This test compares memory consumption using chained jobs and batches and Laravel Workflows.
Both are performing the same operations and dispatching 1000 processes.

### Test batched chain jobs
- Create one worker: `php artisan queue:work redis-queue-default --queue=jobs --memory 512`
- Generate jobs `php artisan test:jobs`
- Tail logs in real-time via `tail -f storage/logs/jobs.log` and observe a minimal increase in memory consumption over time (2 MB after 1000 processes).

### Test workflows
- Create one worker: `php artisan queue:work redis-queue-default --queue=workflows --memory 512`
- Generate workflows `php artisan test:workflows`
- Tail logs in real-time via `tail -f storage/logs/workflows.log` and observe a fast, significant and consistent increase in memory consumption over time.

## Observations

### Laravel 9
- Using batched chain jobs: memory consumption starts at 24 MB, reaches 26 MB after 1000 processes.
- Using Laravel Workflows: memory consumption starts at 26 MB, reaches 182 MB after 1000 processes.

### Laravel 11
- Using batched chain jobs: memory consumption starts at 26 MB, reaches 28 MB after 1000 processes.
- Using Laravel Workflows: memory consumption starts at 38 MB, reaches 202 MB after 1000 processes.
