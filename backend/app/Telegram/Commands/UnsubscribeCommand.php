<?php

declare(strict_types=1);

namespace App\Telegram\Commands;

use App\Models\Subscription;
use App\Models\User;
use Telegram\Bot\Commands\Command;

final class UnsubscribeCommand extends Command {
    protected string $name = 'unsubscribe';
    protected array $aliases = ['отписаться'];
    protected string $pattern = '{unit_id}';
    protected string $description = 'Перестать отслеживать заведение';

    public function handle(): void
    {
        $unitId = $this->argument('unit_id');

        if ($unitId === null) {
            $this->replyWithMessage([
                'text' => "Не указан unit_id",
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

        Subscription::query()->where([
            'user_id' => $user->id,
            'unit_id' => $unitId,
        ])->delete();

        $this->replyWithMessage([
            'text' => 'Ты отписался',
        ]);
    }
}
