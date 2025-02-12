<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\ProcessedOrder;
use App\Models\Subscription;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;
use Throwable;

final class SendOrderNotification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Subscription $subscription,
        private readonly array $order
    ) {}

    public function handle(Api $telegram): void
    {
        try {
            $telegram->sendMessage([
                'chat_id' => $this->subscription->user->telegram_chat_id,
                'text' => "Заказ: {$this->order['id']}",
            ]);

            ProcessedOrder::query()->create([
                'subscription_id' => $this->subscription->id,
                'order_id' => $this->order['id'],
            ]);
        } catch (Throwable $throwable) {
            Log::error("Произошла ошибка при отправке уведомления по заказу {$this->order['id']} "
                ."подписки {$this->subscription->id}: {$throwable->getMessage()}");
        }
    }
}
