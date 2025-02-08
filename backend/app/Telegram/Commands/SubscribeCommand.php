<?php

declare(strict_types=1);

namespace App\Telegram\Commands;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Telegram\Bot\Commands\Command;

final class SubscribeCommand extends Command
{
    protected string $name = 'subscribe';

    protected array $aliases = ['подписаться'];

    protected string $pattern = '{unit_id} {api_key}';

    protected string $description = 'Подписаться на новое заведение';

    public function handle(): void
    {
        $unitId = $this->argument('unit_id');

        if ($unitId === null) {
            $this->replyWithMessage([
                'text' => 'Не указан unit_id',
            ]);

            return;
        }

        $apiKey = $this->argument('api_key');

        if ($apiKey === null) {
            $this->replyWithMessage([
                'text' => 'Не указан api_key',
            ]);

            return;
        }

        $message = $this->getUpdate()->getMessage();

        if (property_exists($message, 'chat') === false) {
            $this->replyWithMessage(['text' => 'Произошла ошибка при получении информации о пользователе']);

            return;
        }

        $user = User::query()->firstOrCreate(
            ['telegram_chat_id' => $message->chat->id],
        );

        $response = Http::get(config('app.order_api_host')."/api/unit/{$unitId}/order", [
            'api_key' => $apiKey,
            'page' => 1,
            'per_page' => 1,
        ]);

        if ($response->successful() === false || array_key_exists('data', $response->json()) === false) {
            $this->replyWithMessage([
                'text' => 'Указаны неверные unit_id и api_key',
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
