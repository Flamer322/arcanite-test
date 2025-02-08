<?php

declare(strict_types=1);

namespace App\Telegram\Commands;

use Telegram\Bot\Commands\Command;
use Telegram\Bot\Laravel\Facades\Telegram;

final class HelpCommand extends Command {
    protected string $name = 'help';
    protected array $aliases = ['помощь', 'start', 'старт'];
    protected string $description = 'Получить список доступных команд';

    public function handle(): void
    {
        $commands = Telegram::getCommands();

        $response = '';
        foreach ($commands as $name => $command) {
            $pattern = $command->getPattern() !== "" ? " {$command->getPattern()}" : "";

            $response .= sprintf('/%s%s - %s' . PHP_EOL, $name, $pattern, $command->getDescription());
        }

        $this->replyWithMessage(['text' => $response]);
    }
}
