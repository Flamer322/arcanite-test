<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

final class DeleteSubscriptionData extends Data
{
    public int $unitId;

    public static function rules(ValidationContext $context): array
    {
        return [
            'unitId' => ['required', 'integer'],
        ];
    }
}
