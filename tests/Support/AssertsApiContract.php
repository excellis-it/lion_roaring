<?php

namespace Tests\Support;

use Illuminate\Testing\TestResponse;

trait AssertsApiContract
{
    protected function assertApiEnvelope(TestResponse $response, int $status = 200): void
    {
        $response->assertStatus($status);
        $json = $response->json();

        $this->assertIsArray($json);
        $this->assertArrayHasKey('status', $json);
        $this->assertIsBool($json['status']);
        $this->assertArrayHasKey('message', $json);
        $this->assertIsString($json['message']);
    }

    protected function assertIso8601Dates(array $payload, array $keys): void
    {
        foreach ($keys as $key) {
            if (! array_key_exists($key, $payload) || $payload[$key] === null) {
                continue;
            }

            $value = $payload[$key];
            $this->assertIsString($value, "Expected {$key} to be an ISO 8601 string.");
            $this->assertNotFalse(
                strtotime($value),
                "Expected {$key} to be a parseable date, got: {$value}"
            );
        }
    }
}
