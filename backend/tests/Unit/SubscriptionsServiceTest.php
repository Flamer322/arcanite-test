<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Data\AddSubscriptionData;
use App\Data\DeleteSubscriptionData;
use App\Models\Subscription;
use App\Models\User;
use App\Services\SubscriptionsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

final class SubscriptionsServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_subscriptions_no_chat_key(): void
    {
        $message = [];

        $service = new SubscriptionsService;

        Log::shouldReceive('error')
            ->once()
            ->with('Произошла ошибка при получении информации о пользователе');

        $result = $service->getSubscriptions($message);

        $this->assertEquals('Произошла ошибка при получении информации о пользователе', $result);
    }

    public function test_get_subscriptions_no_subscriptions(): void
    {
        /* @var $user User */
        $user = User::factory()->create();
        $message = ['chat' => ['id' => $user->telegram_chat_id]];

        $service = new SubscriptionsService;
        $result = $service->getSubscriptions($message);

        $this->assertEquals('У вас нет отслеживаемых заведений', $result);
    }

    public function test_get_subscriptions_with_subscriptions(): void
    {
        /* @var $user User */
        $user = User::factory()->create();
        $message = ['chat' => ['id' => $user->telegram_chat_id]];
        /* @var $subscription Subscription */
        $subscription = Subscription::factory()
            ->for($user)->create();
        $unitId = $subscription->unit_id;

        $service = new SubscriptionsService;
        $result = $service->getSubscriptions($message);

        $this->assertStringContainsString('Список отслеживаемых заведений:', $result);
        $this->assertStringContainsString((string) $unitId, $result);
    }

    public function test_add_subscription_no_chat_key(): void
    {
        $message = [];
        $data = AddSubscriptionData::from([
            'unitId' => fake()->randomNumber(),
            'apiKey' => fake()->uuid(),
        ]);

        $service = new SubscriptionsService;

        Log::shouldReceive('error')
            ->once()
            ->with('Произошла ошибка при получении информации о пользователе');

        $result = $service->addSubscription($data, $message);

        $this->assertEquals('Произошла ошибка при получении информации о пользователе', $result);
    }

    public function test_add_subscription_already_subscribed(): void
    {
        /* @var $user User */
        $user = User::factory()->create();
        $message = ['chat' => ['id' => $user->telegram_chat_id]];
        /* @var $subscription Subscription */
        $subscription = Subscription::factory()
            ->for($user)->create();
        $data = AddSubscriptionData::from([
            'unitId' => $subscription->unit_id,
            'apiKey' => $subscription->api_key,
        ]);

        $service = new SubscriptionsService;
        $response = $service->addSubscription($data, $message);

        $this->assertEquals("Вы уже подписаны на заведение {$data->unitId}", $response);
    }

    public function test_add_subscription_invalid_api_key(): void
    {
        Http::fake([
            '*' => Http::response([], 401),
        ]);

        /* @var $user User */
        $user = User::factory()->create();
        $message = ['chat' => ['id' => $user->telegram_chat_id]];
        $data = AddSubscriptionData::from([
            'unitId' => fake()->randomNumber(),
            'apiKey' => fake()->uuid(),
        ]);

        $service = new SubscriptionsService;
        $response = $service->addSubscription($data, $message);

        $this->assertEquals('Указаны неверные unit_id и api_key', $response);
    }

    public function test_add_subscription_successful_subscription()
    {
        Http::fake([
            '*' => Http::response(['data' => ['orders' => [['id' => 1]]]], 200),
        ]);

        /* @var $user User */
        $user = User::factory()->create();
        $message = ['chat' => ['id' => $user->telegram_chat_id]];
        $data = AddSubscriptionData::from([
            'unitId' => fake()->randomNumber(),
            'apiKey' => fake()->uuid(),
        ]);

        $service = new SubscriptionsService;
        $response = $service->addSubscription($data, $message);

        $this->assertEquals("Вы подписались на заведение {$data->unitId}", $response);
    }

    public function test_delete_subscription_no_chat_key()
    {
        $message = [];
        $data = DeleteSubscriptionData::from([
            'unitId' => fake()->randomNumber(),
        ]);

        $service = new SubscriptionsService;

        Log::shouldReceive('error')
            ->once()
            ->with('Произошла ошибка при получении информации о пользователе');

        $result = $service->deleteSubscription($data, $message);

        $this->assertEquals('Произошла ошибка при получении информации о пользователе', $result);
    }

    public function test_delete_subscription_without_subscription(): void
    {
        /* @var $user User */
        $user = User::factory()->create();
        $message = ['chat' => ['id' => $user->telegram_chat_id]];
        $data = DeleteSubscriptionData::from([
            'unitId' => fake()->randomNumber(),
        ]);

        $service = new SubscriptionsService;

        $result = $service->deleteSubscription($data, $message);

        $this->assertEquals("Вы не подписаны заведение {$data->unitId}", $result);
    }

    public function test_delete_subscription_successful_deletion()
    {
        /* @var $user User */
        $user = User::factory()->create();
        $message = ['chat' => ['id' => $user->telegram_chat_id]];
        /* @var $subscription Subscription */
        $subscription = Subscription::factory()
            ->for($user)->create();

        $data = DeleteSubscriptionData::from([
            'unitId' => $subscription->unit_id,
        ]);

        $service = new SubscriptionsService;

        $response = $service->deleteSubscription($data, $message);

        $this->assertEquals("Вы отписались от заведения {$data->unitId}", $response);
    }
}
