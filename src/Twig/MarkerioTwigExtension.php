<?php

/*
 * This file is part of Monsieur Biz's  for Sylius.
 * (c) Monsieur Biz <sylius@monsieurbiz.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MonsieurBiz\SyliusMarkerioPlugin\Twig;

use MonsieurBiz\SyliusMarkerioPlugin\Event\MarkerioCustomDataEvent;
use MonsieurBiz\SyliusMarkerioPlugin\Provider\MarkerioContextProvider;
use MonsieurBiz\SyliusMarkerioPlugin\Provider\MarkerioProjectIdProvider;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class MarkerioTwigExtension extends AbstractExtension
{
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly MarkerioContextProvider $contextProvider,
        private readonly MarkerioProjectIdProvider $projectIdProvider,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('monsieurbiz_markerio_custom_data', [$this, 'getMarkerioCustomData']),
            new TwigFunction('monsieurbiz_markerio_project_id', [$this, 'getMarkerioProjectId']),
        ];
    }

    public function getMarkerioCustomData(string $area = MarkerioContextProvider::SHOP_AREA): array
    {
        $event = new MarkerioCustomDataEvent();
        $event->mergeData($this->contextProvider->getData($area));
        $this->eventDispatcher->dispatch($event);

        return $event->getData();
    }

    public function getMarkerioProjectId(string $area = MarkerioContextProvider::SHOP_AREA): ?string
    {
        return $this->projectIdProvider->getProjectId($area);
    }
}
