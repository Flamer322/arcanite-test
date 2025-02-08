<?php

namespace App\Telegram\Commands;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Telegram\Bot\Commands\Command;

class SubscribeCommand extends Command {
    protected string $name = 'subscribe';
    protected array $aliases = ['подписаться'];
    protected string $pattern = '{unit_id} {api_key}';
    protected string $description = 'Подписаться на новое заведение';

    public function handle(): void
    {
        $unitId = $this->argument('unit_id');

        if (empty($unitId)) {
            $this->replyWithMessage([
                'text' => "Не указан unit_id",
            ]);

            return;
        }

        $apiKey = $this->argument('api_key');

        if (empty($apiKey)) {
            $this->replyWithMessage([
                'text' => "Не указан api_key",
            ]);

            return;
        }

        $user = User::query()->firstOrCreate(
            ['telegram_chat_id' => $this->getUpdate()->getMessage()->chat->id],
        );

        $response = Http::get(config('app.order_api_host') . "/api/unit/{$unitId}/order", [
            'api_key' => $apiKey,
            'page' => 1,
            'per_page' => 1
        ]);

        if (!$response->successful() || !isset($response->json()['data'])) {
            $this->replyWithMessage([
                'text' => "Указаны неверные unit_id и api_key",
            ]);

            return;
        }

        Subscription::query()->updateOrCreate(
            [
                'user_id' => $user->id,
                'unit_id' => $unitId,
            ],
            ['api_key' => $apiKey],
        );

        $this->replyWithMessage([
            'text' => "Вы подписались на заведение {$unitId}",
        ]);
    }
}
