<?php

namespace App\Telegram\Commands;

use App\Models\Subscription;
use App\Models\User;
use Telegram\Bot\Commands\Command;

class UnsubscribeCommand extends Command {
    protected string $name = 'unsubscribe';
    protected array $aliases = ['отписаться'];
    protected string $pattern = '{unit_id}';
    protected string $description = 'Перестать отслеживать заведение';

    public function handle(): void
    {
        $unitId = $this->argument('unit_id');

        if (empty($unitId)) {
            $this->replyWithMessage([
                'text' => "Не указан unit_id",
            ]);

            return;
        }

        $user = User::query()->firstOrCreate(
            ['telegram_chat_id' => $this->getUpdate()->getMessage()->chat->id],
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
