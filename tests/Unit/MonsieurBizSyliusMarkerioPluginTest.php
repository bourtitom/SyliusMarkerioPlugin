<?php

/*
 * This file is part of Monsieur Biz's Marker.io Plugin for Sylius.
 * (c) Monsieur Biz <sylius@monsieurbiz.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\MonsieurBiz\SyliusMarkerioPlugin\Unit;

use MonsieurBiz\SyliusMarkerioPlugin\MonsieurBizSyliusMarkerioPlugin;
use PHPUnit\Framework\TestCase;

final class MonsieurBizSyliusMarkerioPluginTest extends TestCase
{
    public function testItUsesTheSyliusPluginConvention(): void
    {
        $plugin = new MonsieurBizSyliusMarkerioPlugin();

        self::assertSame(\dirname(__DIR__, 2), $plugin->getPath());
        self::assertSame('monsieur_biz_sylius_markerio', $plugin->getContainerExtension()?->getAlias());
    }
}
