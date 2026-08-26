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
use PHPUnit\Framework\TestCase;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Context\ShopperContextInterface;
use Sylius\Component\Currency\Context\CurrencyNotFoundException;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

final class MarkerioContextProviderTest extends TestCase
{
    public function testItDoesNotReadShopContextInTheAdminArea(): void
    {
        $shopperContext = $this->createMock(ShopperContextInterface::class);
        $shopperContext->expects(self::never())->method('getChannel');
        $cartContext = $this->createMock(CartContextInterface::class);
        $cartContext->expects(self::never())->method('getCart');

        $user = $this->createMock(UserInterface::class);
        $user->method('getUserIdentifier')->willReturn('admin@example.com');
        $user->method('getRoles')->willReturn(['ROLE_ADMINISTRATION_ACCESS']);

        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn($user);

        $provider = new MarkerioContextProvider($shopperContext, $cartContext, $security);

        self::assertSame([
            'admin' => [
                'user' => [
                    'id' => null,
                    'username' => 'admin@example.com',
                    'roles' => ['ROLE_ADMINISTRATION_ACCESS'],
                ],
            ],
        ], $provider->getData(MarkerioContextProvider::ADMIN_AREA));
    }

    public function testItReturnsEmptyDataWhenTheShopContextIsUnavailable(): void
    {
        $shopperContext = $this->createMock(ShopperContextInterface::class);
        $shopperContext->method('getChannel')->willThrowException(new ChannelNotFoundException());

        $provider = new MarkerioContextProvider(
            $shopperContext,
            $this->createMock(CartContextInterface::class),
            $this->createMock(Security::class),
        );

        self::assertSame([], $provider->getData(MarkerioContextProvider::SHOP_AREA));
    }

    public function testItReturnsEmptyDataWhenTheCurrencyContextIsUnavailable(): void
    {
        $shopperContext = $this->createMock(ShopperContextInterface::class);
        $shopperContext->method('getChannel')->willReturn($this->createMock(ChannelInterface::class));
        $shopperContext->method('getCustomer')->willReturn(null);
        $shopperContext->method('getCurrencyCode')->willThrowException(new CurrencyNotFoundException());

        $provider = new MarkerioContextProvider(
            $shopperContext,
            $this->createMock(CartContextInterface::class),
            $this->createMock(Security::class),
        );

        self::assertSame([], $provider->getData(MarkerioContextProvider::SHOP_AREA));
    }

    public function testItReturnsEmptyDataWhenTheLocaleContextIsUnavailable(): void
    {
        $shopperContext = $this->createMock(ShopperContextInterface::class);
        $shopperContext->method('getChannel')->willReturn($this->createMock(ChannelInterface::class));
        $shopperContext->method('getCustomer')->willReturn(null);
        $shopperContext->method('getCurrencyCode')->willReturn('EUR');
        $shopperContext->method('getLocaleCode')->willThrowException(new LocaleNotFoundException());

        $cartContext = $this->createMock(CartContextInterface::class);
        $cartContext->method('getCart')->willThrowException(new CartNotFoundException());

        $provider = new MarkerioContextProvider(
            $shopperContext,
            $cartContext,
            $this->createMock(Security::class),
        );

        self::assertSame([], $provider->getData(MarkerioContextProvider::SHOP_AREA));
    }

    public function testItUsesAnEmptyCartWhenNoCartIsAvailable(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $channel->method('getCode')->willReturn('WEB');
        $channel->method('getName')->willReturn('Web Store');

        $shopperContext = $this->createMock(ShopperContextInterface::class);
        $shopperContext->method('getChannel')->willReturn($channel);
        $shopperContext->method('getCustomer')->willReturn(null);
        $shopperContext->method('getCurrencyCode')->willReturn('EUR');
        $shopperContext->method('getLocaleCode')->willReturn('en_US');

        $cartContext = $this->createMock(CartContextInterface::class);
        $cartContext->method('getCart')->willThrowException(new CartNotFoundException());

        $provider = new MarkerioContextProvider(
            $shopperContext,
            $cartContext,
            $this->createMock(Security::class),
        );

        self::assertSame([
            'front' => [
                'channel' => [
                    'code' => 'WEB',
                    'name' => 'Web Store',
                ],
                'customer' => [
                    'id' => null,
                    'email' => null,
                ],
                'currency' => 'EUR',
                'cart' => [
                    'total' => 0,
                    'items' => [],
                ],
            ],
            'locale' => 'en_US',
        ], $provider->getData(MarkerioContextProvider::SHOP_AREA));
    }
}
