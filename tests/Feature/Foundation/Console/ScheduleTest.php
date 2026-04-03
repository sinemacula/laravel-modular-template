<?php

namespace Tests\Feature\Foundation\Console;

use Illuminate\Console\Scheduling\Schedule;
use Tests\TestCase;

/**
 * Feature tests for the scheduled task definitions.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 *
 * @SuppressWarnings("php:S4833")
 * @SuppressWarnings("php:S2003")
 *
 * @internal
 */
#[\PHPUnit\Framework\Attributes\CoversNothing]
class ScheduleTest extends TestCase
{
    /**
     * Test that the model:prune command is scheduled.
     *
     * @return void
     */
    public function testModelPruneIsScheduled(): void
    {
        $schedule = app(Schedule::class);
        $events   = $schedule->events();

        $matched = array_filter(
            $events,
            fn ($event) => str_contains($event->command, 'model:prune'),
        );

        static::assertNotEmpty($matched);
    }

    /**
     * Test that the schedule file registers commands when
     * directly included.
     *
     * @return void
     */
    public function testScheduleFileRegistersCommands(): void
    {
        $countBefore = count(app(Schedule::class)->events());

        include base_path(
            'modules/Foundation/Console/schedule.php',
        );

        $countAfter = count(app(Schedule::class)->events());

        static::assertGreaterThan($countBefore, $countAfter);
    }

    /**
     * Test that the model:prune command runs on a daily
     * schedule.
     *
     * @return void
     */
    public function testModelPruneRunsDaily(): void
    {
        $event = $this->findModelPruneEvent();

        static::assertSame('0 0 * * *', $event->expression);
    }

    /**
     * Test that the model:prune command is configured to
     * run on one server only.
     *
     * @return void
     */
    public function testModelPruneRunsOnOneServer(): void
    {
        $event = $this->findModelPruneEvent();

        static::assertTrue($event->onOneServer);
    }

    /**
     * Find the model:prune scheduled event.
     *
     * @return \Illuminate\Console\Scheduling\Event
     */
    private function findModelPruneEvent(): \Illuminate\Console\Scheduling\Event
    {
        $schedule = app(Schedule::class);
        $events   = $schedule->events();

        foreach ($events as $event) {
            if (str_contains($event->command, 'model:prune')) {
                return $event;
            }
        }

        static::fail('The model:prune event was not found in the schedule.');
    }
}
