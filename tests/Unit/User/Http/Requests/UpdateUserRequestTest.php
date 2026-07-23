<?php

declare(strict_types = 1);

namespace Tests\Unit\User\Http\Requests;

use App\User\Http\Requests\UpdateUserRequest;
use Illuminate\Validation\Rules\Unique;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

/**
 * Unit tests for the UpdateUserRequest.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @internal
 */
#[CoversClass(UpdateUserRequest::class)]
final class UpdateUserRequestTest extends TestCase
{
    /**
     * Test that the rules method returns the expected validation keys.
     *
     * @return void
     */
    public function testRulesReturnsExpectedKeys(): void
    {
        $request = new UpdateUserRequest;

        $rules = $request->rules();

        self::assertArrayHasKey('name', $rules);
        self::assertArrayHasKey('email', $rules);
        self::assertCount(2, $rules);
    }

    /**
     * Test that the name rule includes sometimes, string, and max:255.
     *
     * @return void
     */
    public function testNameRuleContainsExpectedValidation(): void
    {
        $request = new UpdateUserRequest;

        $rules = $request->rules();

        self::assertContains('sometimes', $rules['name']);
        self::assertContains('string', $rules['name']);
        self::assertContains('max:255', $rules['name']);
    }

    /**
     * Test that the email rule includes sometimes, string, email, and max:255.
     *
     * @return void
     */
    public function testEmailRuleContainsExpectedValidation(): void
    {
        $request = new UpdateUserRequest;

        $rules = $request->rules();

        self::assertContains('sometimes', $rules['email']);
        self::assertContains('string', $rules['email']);
        self::assertContains('email', $rules['email']);
        self::assertContains('max:255', $rules['email']);
    }

    /**
     * Test that the email rule includes a unique constraint.
     *
     * @return void
     */
    public function testEmailRuleContainsUniqueConstraint(): void
    {
        $request = new UpdateUserRequest;

        $rules = $request->rules();

        $hasUnique = false;

        foreach ($rules['email'] as $rule) {
            if ($rule instanceof Unique) {
                $hasUnique = true;

                break;
            }
        }

        self::assertTrue(
            $hasUnique,
            'Expected the email rules to contain a Unique rule instance.',
        );
    }
}
