<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Elao\Bundle\AdminThemeBundle\Twig\Extensions\MenuExtension;
use Elao\Bundle\AdminThemeBundle\Menu\Builder;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

return static function (ContainerConfigurator $container): void {
    $container->services()

        ->set(MenuExtension::class)
            ->args([
                '$builder' => service(Builder::class),
            ])
            ->tag('twig.extension')

        ->set(Builder::class)
            ->args([
                '$requestStack' => service(RequestStack::class),
                '$urlGenerator' => service(UrlGeneratorInterface::class),
            ])
            ->tag('twig.runtime')
    ;
};
