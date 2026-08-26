<?php

/*
 * This file is part of Monsieur Biz's  for Sylius.
 * (c) Monsieur Biz <sylius@monsieurbiz.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MonsieurBiz\SyliusMarkerioPlugin\Provider;

use InvalidArgumentException;
use Sylius\Component\Core\Context\ShopperContextInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

final class MarkerioContextProvider
{
    public const SHOP_AREA = 'shop';

    public const ADMIN_AREA = 'admin';

    public function __construct(
        private readonly ShopperContextInterface $shopperContext,
        private readonly CartContextInterface $cartContext,
        private readonly Security $security,
    ) {
    }

    public function getData(string $area): array
    {
        return match ($area) {
            self::SHOP_AREA => $this->getShopData(),
            self::ADMIN_AREA => $this->getAdminData(),
            default => throw new InvalidArgumentException(\sprintf('Unknown Marker.io area "%s".', $area)),
        };
    }

    private function getShopData(): array
    {
        $channel = $this->shopperContext->getChannel();

        return [
            'front' => [
                'channel' => [
                    'code' => $channel->getCode(),
                    'name' => $channel->getName(),
                ],
                'customer' => [
                    'id' => $this->shopperContext->getCustomer()?->getId(),
                    'email' => $this->shopperContext->getCustomer()?->getEmail(),
                ],
                'currency' => $this->shopperContext->getCurrencyCode(),
                'cart' => $this->getCartData(),
            ],
            'locale' => $this->shopperContext->getLocaleCode(),
        ];
    }

    private function getAdminData(): array
    {
        $user = $this->security->getUser();
        if (!$user instanceof UserInterface) {
            return [];
        }

        return [
            'admin' => [
                'user' => [
                    'id' => method_exists($user, 'getId') ? $user->getId() : null,
                    'username' => $user->getUserIdentifier(),
                    'roles' => $user->getRoles(),
                ],
            ],
        ];
    }

    private function getCartData(): array
    {
        $cart = $this->cartContext->getCart();
        $items = [];

        foreach ($cart->getItems() as $item) {
            if (!$item instanceof OrderItemInterface) {
                continue;
            }

            $items[] = [
                'id' => $item->getId(),
                'variant' => $this->getVariantData($item->getVariant()),
                'quantity' => $item->getQuantity(),
                'unit_price' => $item->getUnitPrice(),
                'original_unit_price' => $item->getOriginalUnitPrice(),
                'total' => $item->getTotal(),
            ];
        }

        return [
            'total' => $cart->getTotal(),
            'items' => $items,
        ];
    }

    private function getVariantData(?ProductVariantInterface $variant): array
    {
        return [
            'id' => $variant?->getId(),
            'code' => $variant?->getCode(),
            'name' => $variant?->getName(),
            'on_hand' => $variant?->getOnHand(),
            'on_hold' => $variant?->getOnHold(),
        ];
    }
}
