<?php

/*
 * This file is part of Monsieur Biz's  for Sylius.
 * (c) Monsieur Biz <sylius@monsieurbiz.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\MonsieurBiz\SyliusMarkerioPlugin\Unit\Event;

use MonsieurBiz\SyliusMarkerioPlugin\Event\MarkerioCustomDataEvent;
use PHPUnit\Framework\TestCase;

final class MarkerioCustomDataEventTest extends TestCase
{
    public function testItBuildsAndMergesCustomData(): void
    {
        $event = new MarkerioCustomDataEvent();

        self::assertSame($event, $event->setData('first', 'value'));
        self::assertSame($event, $event->mergeData(['second' => 2, 'first' => 'overridden']));
        self::assertSame([
            'first' => 'overridden',
            'second' => 2,
        ], $event->getData());
    }
}
