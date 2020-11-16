<?php

namespace Elao\Bundle\AdminThemeBundle\Menu;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Builder
{
    private RequestStack $requestStack;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(RequestStack $requestStack, UrlGeneratorInterface $urlGenerator)
    {
        $this->requestStack = $requestStack;
        $this->urlGenerator = $urlGenerator;
    }

    public function getMenuPath(array $item): string
    {
        if (isset($item['url'])) {
            return $item['url'];
        }

        if (isset($item['route'])) {
            return $this->urlGenerator->generate($item['route'], isset($item['parameters']) ? $item['parameters'] : []);
        }

        return '#';
    }

    /**
     * Is the given menu item active?
     */
    public function isActive(array $item): bool
    {
        if (isset($item['active'])) {
            return (bool) $item['active'];
        }

        return (isset($item['root']) && $this->isCurrentRoot($item['root']))
            || (isset($item['branch']) && $this->isCurrentBranch($item['branch']))
            || (isset($item['route']) && $this->isCurrentRoute($item['route']));
    }

    public function isCurrentRoot(string $root): bool
    {
        return $this->getCurrentAttribute('_root') === $root;
    }

    public function isCurrentBranch(string $branch): bool
    {
        return $this->getCurrentAttribute('_branch') === $branch;
    }

    public function isCurrentRoute(string $route): bool
    {
        return $this->getCurrentAttribute('_route') === $route;
    }

    public function addParametersToCurrentUrl(array $parameters, int $referenceType = null): string
    {
        return $this->urlGenerator->generate(
            $this->getCurrentAttribute('_route'),
            array_merge(
                $this->getCurrentAttribute('_route_params'),
                $this->getCurrentQuery(),
                $parameters
            ),
            $referenceType
        );
    }

    /**
     * Get attribute from current request
     *
     * @return string|array
     */
    private function getCurrentAttribute(string $name)
    {
        return $this->requestStack->getCurrentRequest()->attributes->get($name);
    }

    private function getCurrentQuery(): array
    {
        return $this->requestStack->getCurrentRequest()->query->all();
    }
}
