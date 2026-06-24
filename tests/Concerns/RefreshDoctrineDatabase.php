<?php

namespace Tests\Concerns;

use Illuminate\Support\Facades\Artisan;

trait RefreshDoctrineDatabase
{
    protected static bool $schemaInitialized = false;

    protected function refreshDoctrineDatabase(): void
    {
        $this->initializeSchemaOnce();

        $connection = app('em')->getConnection();

        if (!$connection->isTransactionActive()) {
            $connection->beginTransaction();
        }
    }

    protected function initializeSchemaOnce(): void
    {
        if (static::$schemaInitialized) {
            return;
        }

        $lockFile = storage_path('framework/testing-schema.lock');
        $lockHandle = fopen($lockFile, 'c');

        if ($lockHandle === false) {
            throw new \RuntimeException('Unable to open test schema lock file.');
        }

        try {
            flock($lockHandle, LOCK_EX);

            if (static::$schemaInitialized) {
                return;
            }

            app('em')->getConnection()->close();

            $this->runDoctrineMigrateWithRetry();

            static::$schemaInitialized = true;
        } finally {
            flock($lockHandle, LOCK_UN);
            fclose($lockHandle);
        }
    }

    protected function runDoctrineMigrateWithRetry(int $maxAttempts = 5): void
    {
        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                Artisan::call('doctrine:migrations:migrate', ['--no-interaction' => true]);

                return;
            } catch (\Throwable $exception) {
                if (!$this->isDeadlock($exception) || $attempt === $maxAttempts) {
                    throw $exception;
                }

                usleep(100_000 * $attempt);
                app('em')->getConnection()->close();
            }
        }
    }

    protected function isDeadlock(\Throwable $exception): bool
    {
        $message = $exception->getMessage();

        while ($exception = $exception->getPrevious()) {
            $message .= $exception->getMessage();
        }

        return str_contains($message, 'deadlock detected')
            || str_contains($message, '40P01');
    }

    protected function rollbackDoctrineDatabase(): void
    {
        $em = app('em');
        $connection = $em->getConnection();

        if ($connection->isTransactionActive()) {
            $connection->rollBack();
        }

        $em->clear();
    }
}
