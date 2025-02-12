<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\AddSubscriptionData;
use App\Data\DeleteSubscriptionData;
use App\Models\ProcessedOrder;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class SubscriptionsService
{
    public function getSubscriptions(array $message): string
    {
        if (array_key_exists('chat', $message) === false) {
            Log::error('Произошла ошибка при получении информации о пользователе');

            return 'Произошла ошибка при получении информации о пользователе';
        }

        $user = User::query()->firstOrCreate(
            ['telegram_chat_id' => $message['chat']['id']],
        );

        if ($user->subscriptions->isEmpty()) {
            return 'У вас нет отслеживаемых заведений';
        }

        $response = 'Список отслеживаемых заведений:'.PHP_EOL.PHP_EOL;
        foreach ($user->subscriptions as $subscription) {
            /* @var Subscription $subscription */
            $response .= sprintf('%s'.PHP_EOL, $subscription->unit_id);
        }

        return $response;
    }

    public function addSubscription(AddSubscriptionData $data, array $message): string
    {
        if (array_key_exists('chat', $message) === false) {
            Log::error('Произошла ошибка при получении информации о пользователе');

            return 'Произошла ошибка при получении информации о пользователе';
        }

        $user = User::query()->firstOrCreate(
            ['telegram_chat_id' => $message['chat']['id']],
        );

        if (Subscription::query()->where(
            [
                'user_id' => $user->id,
                'unit_id' => $data->unitId,
                'api_key' => $data->apiKey,
            ],
        )->first() !== null) {
            return "Вы уже подписаны на заведение {$data->unitId}";
        }

        $response = Http::get(config('app.order_api_host')."/api/unit/{$data->unitId}/order", [
            'api_key' => $data->apiKey,
            'page' => 1,
            'per_page' => 20,
        ]);

        if ($response->status() === 401) {
            return 'Указаны неверные unit_id и api_key';
        }

        if ($response->successful() === false || array_key_exists('data', $response->json()) === false) {
            Log::error("Произошла ошибка при получении информации о заведении {$data->unitId} : {$data->apiKey}");

            return 'Произошла ошибка, попробуйте ещё раз';
        }

        $subscription = Subscription::query()->updateOrCreate(
            [
                'user_id' => $user->id,
                'unit_id' => $data->unitId,
            ],
            ['api_key' => $data->apiKey],
        );

        foreach ($response->json()['data']['orders'] as $order) {
            ProcessedOrder::query()->create([
                'subscription_id' => $subscription->id,
                'order_id' => $order['id'],
            ]);
        }

        return "Вы подписались на заведение {$data->unitId}";
    }

    public function deleteSubscription(DeleteSubscriptionData $data, array $message): string
    {
        if (array_key_exists('chat', $message) === false) {
            Log::error('Произошла ошибка при получении информации о пользователе');

            return 'Произошла ошибка при получении информации о пользователе';
        }

        $user = User::query()->firstOrCreate(
            ['telegram_chat_id' => $message['chat']['id']],
        );

        $deleted = Subscription::query()->where([
            'user_id' => $user->id,
            'unit_id' => $data->unitId,
        ])->delete();

        if ($deleted === true) {
            return "Вы отписались от заведения {$data->unitId}";
        } else {
            return "Вы не подписаны заведение {$data->unitId}";
        }
    }
}
