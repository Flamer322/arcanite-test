<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\ProcessedOrder;
use App\Models\Subscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Telegram\Bot\Api;

final class SendNotificationsCommand extends Command
{
    protected $signature = 'notifications:send';

    protected $description = 'Отправить уведомления пользователям о новых заказах';

    public function __construct(
        protected Api $telegram
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Отправка уведомлений начата');

        $subscriptions = Subscription::query()->with('user')->get();

        foreach ($subscriptions as $subscription) {
            $response = Http::get(config('app.order_api_host')."/api/unit/{$subscription->unit_id}/order", [
                'api_key' => $subscription->api_key,
                'page' => 1,
                'per_page' => 20,
            ]);

            $orders = $response->json()['data']['orders'];

            foreach ($orders as $order) {
                if (ProcessedOrder::query()->where([
                    'subscription_id' => $subscription->id,
                    'order_id' => $order['id'],
                ])->first() === null) {
                    $this->telegram->sendMessage([
                        'chat_id' => $subscription->user->telegram_chat_id,
                        'text' => "Заказ: {$order['id']}",
                    ]);

                    ProcessedOrder::query()->create([
                        'subscription_id' => $subscription->id,
                        'order_id' => $order['id'],
                    ]);
                }
            }
        }

        $this->info('Отправка уведомлений завершена');

        return self::SUCCESS;
    }
}
