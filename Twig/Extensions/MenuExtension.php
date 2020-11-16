<?php

namespace Elao\Bundle\AdminThemeBundle\Twig\Extensions;

use Elao\Bundle\AdminThemeBundle\Menu\Builder;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\TwigTest;

class MenuExtension extends AbstractExtension
{
    private Builder $builder;

    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('menu_path', [$this->builder, 'getMenuPath']),
            new TwigFunction('url_add', [$this->builder, 'addParametersToCurrentUrl']),
        ];
    }

    public function getTests(): array
    {
        return [
            new TwigTest('active', [$this->builder, 'isActive']),
            new TwigTest('currentRoot', [$this->builder, 'isCurrentRoot']),
            new TwigTest('currentRoute', [$this->builder, 'isCurrentRoute']),
        ];
    }
}
