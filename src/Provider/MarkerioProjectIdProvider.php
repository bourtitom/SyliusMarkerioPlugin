<?php

/*
 * This file is part of Monsieur Biz's Marker.io Plugin for Sylius.
 * (c) Monsieur Biz <sylius@monsieurbiz.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MonsieurBiz\SyliusMarkerioPlugin\Provider;

use InvalidArgumentException;
use MonsieurBiz\SyliusSettingsPlugin\Exception\SettingsException;
use MonsieurBiz\SyliusSettingsPlugin\Provider\SettingsProviderInterface;
use MonsieurBiz\SyliusSettingsPlugin\Settings\RegistryInterface;

final class MarkerioProjectIdProvider
{
    private const SETTINGS_ALIAS = 'monsieurbiz.markerio';

    private const PROJECT_ID_PATH = 'project_id';

    public function __construct(
        private readonly SettingsProviderInterface $settingsProvider,
        private readonly RegistryInterface $settingsRegistry,
    ) {
    }

    public function getProjectId(string $area): ?string
    {
        $projectId = match ($area) {
            MarkerioContextProvider::SHOP_AREA => $this->getShopProjectId(),
            MarkerioContextProvider::ADMIN_AREA => $this->getAdminProjectId(),
            default => throw new InvalidArgumentException(\sprintf('Unknown Marker.io area "%s".', $area)),
        };

        return \is_string($projectId) && '' !== trim($projectId) ? $projectId : null;
    }

    private function getShopProjectId(): mixed
    {
        try {
            return $this->settingsProvider->getSettingValue(self::SETTINGS_ALIAS, self::PROJECT_ID_PATH);
        } catch (SettingsException) {
            return null;
        }
    }

    private function getAdminProjectId(): mixed
    {
        return $this->settingsRegistry
            ->getByAlias(self::SETTINGS_ALIAS)
            ?->getCurrentValue(null, null, self::PROJECT_ID_PATH)
        ;
    }
}
