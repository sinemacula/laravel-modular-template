<?php

namespace Tests\Unit\User\Http\Requests;

use App\User\Http\Requests\UpdateUserRequest;
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
class UpdateUserRequestTest extends TestCase
{
    /**
     * Test that the rules method returns the expected validation
     * keys.
     *
     * @return void
     */
    public function testRulesReturnsExpectedKeys(): void
    {
        $request = new UpdateUserRequest;

        $rules = $request->rules();

        static::assertArrayHasKey('name', $rules);
        static::assertArrayHasKey('email', $rules);
        static::assertCount(2, $rules);
    }

    /**
     * Test that the name rule includes sometimes, string, and
     * max:255.
     *
     * @return void
     */
    public function testNameRuleContainsExpectedValidation(): void
    {
        $request = new UpdateUserRequest;

        $rules = $request->rules();

        static::assertContains('sometimes', $rules['name']);
        static::assertContains('string', $rules['name']);
        static::assertContains('max:255', $rules['name']);
    }

    /**
     * Test that the email rule includes sometimes, string, email,
     * and max:255.
     *
     * @return void
     */
    public function testEmailRuleContainsExpectedValidation(): void
    {
        $request = new UpdateUserRequest;

        $rules = $request->rules();

        static::assertContains('sometimes', $rules['email']);
        static::assertContains('string', $rules['email']);
        static::assertContains('email', $rules['email']);
        static::assertContains('max:255', $rules['email']);
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

        foreach ((array) $rules['email'] as $rule) {
            if ($rule instanceof \Illuminate\Validation\Rules\Unique) {
                $hasUnique = true;

                break;
            }
        }

        static::assertTrue(
            $hasUnique,
            'Expected the email rules to contain a Unique rule instance.',
        );
    }
}
