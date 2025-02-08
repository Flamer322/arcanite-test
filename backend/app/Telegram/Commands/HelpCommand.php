<?php

namespace App\Telegram\Commands;

use Telegram\Bot\Commands\Command;

class HelpCommand extends Command {
    protected string $name = 'help';
    protected array $aliases = ['помощь', 'start', 'старт'];
    protected string $description = 'Получить список доступных команд';

    public function handle(): void
    {
        $commands = $this->getTelegram()->getCommands();

        $response = '';
        foreach ($commands as $name => $command) {
            $pattern = $command->getPattern() ? " {$command->getPattern()}" : "";

            $response .= sprintf('/%s%s - %s' . PHP_EOL, $name, $pattern, $command->getDescription());
        }

        $this->replyWithMessage(['text' => $response]);
    }
}
