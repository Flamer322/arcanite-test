<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\SendOrderNotification;
use App\Models\ProcessedOrder;
use App\Models\Subscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class SendNotificationsCommand extends Command
{
    protected $signature = 'notifications:send';

    protected $description = 'Отправить уведомления пользователям о новых заказах';

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

            $responseJson = $response->json();

            if ($response->status() === 401) {
                Log::error("Указаны неверные unit_id и api_key для подписки {$subscription->id}");

                continue;
            }

            if ($response->successful() === false
                || array_key_exists('data', $responseJson) === false
                || array_key_exists('orders', $responseJson['data'])) {
                Log::error("Произошла ошибка при получении заказов подписки {$subscription->id}");

                continue;
            }

            $this->processOrders($response->json()['data']['orders'], $subscription);
        }

        $this->info('Отправка уведомлений завершена');

        return self::SUCCESS;
    }

    private function processOrders(array $orders, Subscription $subscription): void
    {
        $processedOrderIds = ProcessedOrder::query()
            ->where('subscription_id', $subscription->id)
            ->pluck('order_id')
            ->toArray();

        $newOrders = collect($orders)->filter(fn ($order) => ! in_array($order['id'], $processedOrderIds, true));

        foreach ($newOrders as $order) {
            SendOrderNotification::dispatch($subscription, $order);
        }
    }
}
