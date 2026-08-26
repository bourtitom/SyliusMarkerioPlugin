<p align="center">
    <a href="https://monsieurbiz.com" target="_blank">
        <img src="https://monsieurbiz.com/logo.png" width="250px" alt="Monsieur Biz logo" />
    </a>
    <a href="https://monsieurbiz.com/agence-web-experte-sylius" target="_blank">
        <img src="https://monsieurbiz.com/sylius_logo.png" width="200px" alt="Sylius logo" />
    </a>
    <br/>
    <img src="https://monsieurbiz.com/assets/images/sylius_badge_extension-artisan.png" width="100" alt="Monsieur Biz is a Sylius Extension Artisan partner">
</p>

<h1 align="center">Marker.io's plugin for Sylius</h1>

[![Plugin license](https://img.shields.io/github/license/monsieurbiz/SyliusMarkerioPlugin?public)](https://github.com/monsieurbiz/SyliusMarkerioPlugin/blob/2.x/LICENSE) [![Flex Recipe](https://github.com/monsieurbiz/SyliusMarkerioPlugin/actions/workflows/recipe.yaml/badge.svg)](https://github.com/monsieurbiz/SyliusMarkerioPlugin/actions/workflows/recipe.yaml) [![Security](https://github.com/monsieurbiz/SyliusMarkerioPlugin/actions/workflows/security.yaml/badge.svg)](https://github.com/monsieurbiz/SyliusMarkerioPlugin/actions/workflows/security.yaml) [![Tests](https://github.com/monsieurbiz/SyliusMarkerioPlugin/actions/workflows/tests.yaml/badge.svg)](https://github.com/monsieurbiz/SyliusMarkerioPlugin/actions/workflows/tests.yaml)

This plugin is a Sylius integration for [Marker.io](https://marker.io).

It gives the capability to integrate the extension if you have a Project ID.  
In the same time, if the script is loaded, we've added some metadata to the configuration sent to Marker.io.

## Compatibility

| Plugin branch | Sylius version | PHP version |
|---------------|----------------|-------------|
| 2.x           | 2.0            | 8.3         |

The 2.x branch does not support Sylius 1.x. Sylius 2.0 is currently the latest
compatible minor because `monsieurbiz/sylius-settings-plugin` 2.x requires
Sylius `~2.0`.

## Installation

If you want to use our recipes, you can configure your composer.json by running:

```bash
composer config --no-plugins --json extra.symfony.endpoint '["https://api.github.com/repos/monsieurbiz/symfony-recipes/contents/index.json?ref=flex/master","flex://defaults"]'
```

```
composer require monsieurbiz/sylius-markerio-plugin
```

You may need to install our recipes first:

```
composer config --no-plugins --json extra.symfony.endpoint '["https://api.github.com/repos/monsieurbiz/symfony-recipes/contents/index.json?ref=flex/master","flex://defaults"]'
```

## Update the metadata

You can create your own event listener and then update the data sent to the plugin using the event itself.

Listen to `MonsieurBiz\SyliusMarkerioPlugin\Event\MarkerioCustomDataEvent` and
use `setData()` or `mergeData()` to enrich the data sent to Marker.io.

## Data sent to Marker.io

The shop integration sends channel, customer, currency and cart metadata. The
admin integration sends the authenticated administrator identifier and roles.
Review this data against your privacy policy before enabling Marker.io.

The shop uses the current channel's Project ID. The admin uses the default
Project ID configured in the settings panel.

## License

This plugin is under the MIT license.
Please see the [LICENSE](LICENSE) file for more information.
