<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * @internal
 */
#[\PHPUnit\Framework\Attributes\CoversNothing]
class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testTheApplicationReturnsASuccessfulResponse(): void
    {
        $response = $this->get('/health');

        $response->assertStatus(200);
    }
}
