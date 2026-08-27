<?php

/*
 * This file is part of Monsieur Biz's Marker.io Plugin for Sylius.
 * (c) Monsieur Biz <sylius@monsieurbiz.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\MonsieurBiz\SyliusMarkerioPlugin\Unit\Twig;

use MonsieurBiz\SyliusMarkerioPlugin\Event\MarkerioCustomDataEvent;
use MonsieurBiz\SyliusMarkerioPlugin\Provider\MarkerioContextProvider;
use MonsieurBiz\SyliusMarkerioPlugin\Provider\MarkerioProjectIdProvider;
use MonsieurBiz\SyliusMarkerioPlugin\Twig\MarkerioTwigExtension;
use MonsieurBiz\SyliusSettingsPlugin\Provider\SettingsProviderInterface;
use MonsieurBiz\SyliusSettingsPlugin\Settings\RegistryInterface;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Context\ShopperContextInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class MarkerioTwigExtensionTest extends TestCase
{
    public function testItRegistersMarkerioFunctions(): void
    {
        $functionNames = array_map(
            static fn ($function): string => $function->getName(),
            $this->createExtension()->getFunctions(),
        );

        self::assertEqualsCanonicalizing([
            'monsieurbiz_markerio_custom_data',
            'monsieurbiz_markerio_project_id',
        ], $functionNames);
    }

    public function testItDispatchesAnEventToExtendContextData(): void
    {
        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher
            ->expects(self::once())
            ->method('dispatch')
            ->willReturnCallback(static function (MarkerioCustomDataEvent $event): MarkerioCustomDataEvent {
                return $event->setData('integration', 'custom');
            })
        ;

        $extension = $this->createExtension($dispatcher);

        self::assertSame(
            ['integration' => 'custom'],
            $extension->getMarkerioCustomData(MarkerioContextProvider::ADMIN_AREA),
        );
    }

    public function testItReturnsTheProjectIdForTheRequestedArea(): void
    {
        $settingsProvider = $this->createMock(SettingsProviderInterface::class);
        $settingsProvider
            ->expects(self::once())
            ->method('getSettingValue')
            ->with('monsieurbiz.markerio', 'project_id')
            ->willReturn('shop-project')
        ;

        $extension = $this->createExtension(settingsProvider: $settingsProvider);

        self::assertSame('shop-project', $extension->getMarkerioProjectId(MarkerioContextProvider::SHOP_AREA));
    }

    private function createExtension(
        ?EventDispatcherInterface $dispatcher = null,
        ?SettingsProviderInterface $settingsProvider = null,
    ): MarkerioTwigExtension {
        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn(null);

        $contextProvider = new MarkerioContextProvider(
            $this->createMock(ShopperContextInterface::class),
            $this->createMock(CartContextInterface::class),
            $security,
        );
        $projectIdProvider = new MarkerioProjectIdProvider(
            $settingsProvider ?? $this->createMock(SettingsProviderInterface::class),
            $this->createMock(RegistryInterface::class),
        );

        return new MarkerioTwigExtension(
            $dispatcher ?? $this->createMock(EventDispatcherInterface::class),
            $contextProvider,
            $projectIdProvider,
        );
    }
}
