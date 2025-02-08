<?php

declare(strict_types=1);

namespace App\Telegram\Commands;

use App\Models\Subscription;
use App\Models\User;
use Telegram\Bot\Commands\Command;

final class ListCommand extends Command {
    protected string $name = 'list';
    protected array $aliases = ['список'];
    protected string $description = 'Получить список отслеживаемых заведений';

    public function handle(): void
    {
        $message = $this->getUpdate()->getMessage();

        if (property_exists($message, 'chat') === false) {
            $this->replyWithMessage(['text' => 'Произошла ошибка при получении информации о пользователе']);

            return;
        }

        $user = User::query()->firstOrCreate(
            ['telegram_chat_id' => $message->chat->id],
        );

        if ($user->subscriptions->isNotEmpty()) {
            $response = 'Список отслеживаемых заведений:' . PHP_EOL . PHP_EOL;
            foreach ($user->subscriptions as $subscription) {
                /* @var Subscription $subscription */

                $response .= sprintf('%s' . PHP_EOL, $subscription->unit_id);
            }
        } else {
            $response = 'У вас нет отслеживаемых заведений';
        }

        $this->replyWithMessage(['text' => $response]);
    }
}
