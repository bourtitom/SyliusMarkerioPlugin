<?php

/*
 * This file is part of Monsieur Biz's  for Sylius.
 * (c) Monsieur Biz <sylius@monsieurbiz.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\MonsieurBiz\SyliusMarkerioPlugin\Unit\Provider;

use MonsieurBiz\SyliusMarkerioPlugin\Provider\MarkerioContextProvider;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Context\ShopperContextInterface;
use Sylius\Component\Order\Context\CartContextInterface;
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
}
