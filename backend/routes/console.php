<?php

declare(strict_types=1);

use App\Console\Commands\SendNotificationsCommand;
use Illuminate\Support\Facades\Schedule;

Schedule::command(SendNotificationsCommand::class)
    ->everyMinute();
