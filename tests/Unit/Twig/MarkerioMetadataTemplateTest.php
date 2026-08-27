<?php

/*
 * This file is part of Monsieur Biz's Marker.io Plugin for Sylius.
 * (c) Monsieur Biz <sylius@monsieurbiz.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\MonsieurBiz\SyliusMarkerioPlugin\Unit\Twig;

use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

final class MarkerioMetadataTemplateTest extends TestCase
{
    public function testItDoesNotRenderTheWidgetWithoutAProjectId(): void
    {
        $twig = $this->createTwig(null, []);

        self::assertSame('', trim($twig->render('shared/metadata.html.twig', ['area' => 'shop'])));
    }

    public function testItSafelyRendersTheMarkerConfiguration(): void
    {
        $twig = $this->createTwig('</script>', ['value' => '<script>alert("xss")</script>']);

        $html = $twig->render('shared/metadata.html.twig', ['area' => 'shop']);

        self::assertStringContainsString('project: "\\u003C\\/script\\u003E"', $html);
        self::assertStringContainsString('"value":"\\u003Cscript\\u003Ealert(\\u0022xss\\u0022)\\u003C\\/script\\u003E"', $html);
        self::assertStringNotContainsString('project: "</script>"', $html);
        self::assertStringContainsString('https://edge.marker.io/latest/shim.js', $html);
    }

    private function createTwig(?string $projectId, array $customData): Environment
    {
        $twig = new Environment(new FilesystemLoader(\dirname(__DIR__, 3) . '/templates'));
        $twig->addFunction(new TwigFunction('monsieurbiz_markerio_project_id', static fn (string $area): ?string => $projectId));
        $twig->addFunction(new TwigFunction('monsieurbiz_markerio_custom_data', static fn (string $area): array => $customData));

        return $twig;
    }
}
