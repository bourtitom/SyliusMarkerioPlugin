<?php

/*
 * This file is part of Monsieur Biz's Marker.io Plugin for Sylius.
 * (c) Monsieur Biz <sylius@monsieurbiz.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\MonsieurBiz\SyliusMarkerioPlugin\Unit\Provider;

use MonsieurBiz\SyliusMarkerioPlugin\Provider\MarkerioContextProvider;
use MonsieurBiz\SyliusMarkerioPlugin\Provider\MarkerioProjectIdProvider;
use MonsieurBiz\SyliusSettingsPlugin\Exception\SettingsException;
use MonsieurBiz\SyliusSettingsPlugin\Provider\SettingsProviderInterface;
use MonsieurBiz\SyliusSettingsPlugin\Settings\RegistryInterface;
use MonsieurBiz\SyliusSettingsPlugin\Settings\SettingsInterface;
use PHPUnit\Framework\TestCase;

final class MarkerioProjectIdProviderTest extends TestCase
{
    public function testItUsesThePersistedDefaultSettingInTheAdminArea(): void
    {
        $settingsProvider = $this->createMock(SettingsProviderInterface::class);
        $settingsProvider->expects(self::never())->method('getSettingValue');

        $settings = $this->createMock(SettingsInterface::class);
        $settings->expects(self::once())
            ->method('getCurrentValue')
            ->with(null, null, 'project_id')
            ->willReturn('admin-project')
        ;

        $registry = $this->createMock(RegistryInterface::class);
        $registry->method('getByAlias')->with('monsieurbiz.markerio')->willReturn($settings);

        $provider = new MarkerioProjectIdProvider($settingsProvider, $registry);

        self::assertSame('admin-project', $provider->getProjectId(MarkerioContextProvider::ADMIN_AREA));
    }

    public function testItReturnsNullWhenTheShopSettingCannotBeRead(): void
    {
        $settingsProvider = $this->createMock(SettingsProviderInterface::class);
        $settingsProvider->method('getSettingValue')->willThrowException(new SettingsException());

        $provider = new MarkerioProjectIdProvider(
            $settingsProvider,
            $this->createMock(RegistryInterface::class),
        );

        self::assertNull($provider->getProjectId(MarkerioContextProvider::SHOP_AREA));
    }
}
