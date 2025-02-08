<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Telegram\Bot\Events\UpdateEvent;
use Telegram\Bot\Laravel\Facades\Telegram;
use Throwable;

final class WebhookController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        try {
            // Processing buttons
            Telegram::on('callback_query.text', function (UpdateEvent $event) {

            });

            Telegram::commandsHandler(true);

            return response()
                ->json([
                    'status' => true,
                    'error' => null
                ]);
        } catch (Throwable $exception) {
            return response()
                ->json([
                    'status' => false,
                    'error' => $exception->getMessage()
                ]);
        }
    }
}
