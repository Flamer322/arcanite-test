<?php

declare(strict_types=1);

namespace App\Telegram\Commands;

use App\Services\SubscriptionsService;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Commands\Command;
use Throwable;

final class ListCommand extends Command
{
    protected string $name = 'list';

    protected array $aliases = ['список'];

    protected string $description = 'Получить список отслеживаемых заведений';

    public function __construct(
        private readonly SubscriptionsService $service,
    ) {}

    public function handle(): void
    {
        try {
            $message = $this->service->getSubscriptions(
                $this->getUpdate()->getMessage()->toArray(),
            );
        } catch (Throwable $throwable) {
            $message = 'Произошла неизвестная ошибка';

            Log::error("Произошла неизвестная ошибка: {$throwable->getMessage()}");
        } finally {
            $this->replyWithMessage([
                'text' => $message,
            ]);
        }
    }
}
