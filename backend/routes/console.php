<?php

use app\Console\Commands\SendNotificationsCommand;
use Illuminate\Support\Facades\Schedule;

Schedule::command(SendNotificationsCommand::class)
    ->everyMinute();
