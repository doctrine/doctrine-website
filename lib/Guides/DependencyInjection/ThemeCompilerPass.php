<?php

declare(strict_types=1);

namespace Doctrine\Website\Guides\DependencyInjection;

use phpDocumentor\Guides\Twig\Theme\ThemeManager;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ThemeCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $guidesConfig = $container->getExtensionConfig('guides');

        foreach ($guidesConfig as $config) {
            $theme ??= $config['theme'] ?? null;
        }

        $themeManager = $container->getDefinition(ThemeManager::class);
        $themeManager->addMethodCall('useTheme', [$theme ?? 'default']);
    }
}
