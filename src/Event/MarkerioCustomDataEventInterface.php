<?php

/*
 * This file is part of Monsieur Biz's Marker.io Plugin for Sylius.
 * (c) Monsieur Biz <sylius@monsieurbiz.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MonsieurBiz\SyliusMarkerioPlugin\Event;

interface MarkerioCustomDataEventInterface
{
    public function setData(string $key, mixed $value): static;

    public function mergeData(array $data): static;

    public function getData(): array;
}
