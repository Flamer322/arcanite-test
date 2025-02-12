<?php

declare(strict_types=1);

namespace App\Telegram\Commands;

use App\Data\DeleteSubscriptionData;
use App\Services\SubscriptionsService;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Telegram\Bot\Commands\Command;
use Throwable;

final class UnsubscribeCommand extends Command
{
    protected string $name = 'unsubscribe';

    protected array $aliases = ['отписаться'];

    protected string $pattern = '{unit_id}';

    protected string $description = 'Перестать отслеживать заведение';

    public function __construct(
        private readonly SubscriptionsService $service,
    ) {}

    public function handle(): void
    {
        try {
            $message = $this->service->deleteSubscription(
                DeleteSubscriptionData::validateAndCreate([
                    'unitId' => $this->argument('unit_id'),
                    'apiKey' => $this->argument('api_key'),
                ]),
                $this->getUpdate()->getMessage()->toArray(),
            );
        } catch (ValidationException $exception) {
            $message = $exception->getMessage();
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
