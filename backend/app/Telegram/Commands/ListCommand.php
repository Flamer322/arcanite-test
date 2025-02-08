<?php

namespace App\Telegram\Commands;

use App\Models\Subscription;
use App\Models\User;
use Telegram\Bot\Commands\Command;

class ListCommand extends Command {
    protected string $name = 'list';
    protected array $aliases = ['список'];
    protected string $description = 'Получить список отслеживаемых заведений';

    public function handle(): void
    {
        $user = User::query()->firstOrCreate(
            ['telegram_chat_id' => $this->getUpdate()->getMessage()->chat->id],
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
