<?php

declare(strict_types=1);

namespace Elao\Bundle\AdminThemeBundle\Menu;

use Symfony\Component\HttpFoundation\Request;
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

    /**
     * @param array<string,mixed> $item
     */
    public function getMenuPath(array $item): string
    {
        if (isset($item['url'])) {
            \assert(
                \is_string($item['url']),
                new \InvalidArgumentException('Option "url" should be a string.')
            );

            return $item['url'];
        }

        if (isset($item['route'])) {
            \assert(
                \is_string($item['route']),
                new \InvalidArgumentException('Option "route" should be a string.')
            );

            if (isset($item['parameters'])) {
                \assert(
                    \is_array($item['parameters']),
                    new \InvalidArgumentException('Option "parameters" should be an array.')
                );
            }

            return $this->urlGenerator->generate(
                $item['route'],
                isset($item['parameters']) ? $item['parameters'] : []
            );
        }

        return '#';
    }

    /**
     * Is the given menu item active?
     *
     * @param array<string,mixed> $item
     */
    public function isActive(array $item): bool
    {
        if (isset($item['active'])) {
            return (bool) $item['active'];
        }

        if (isset($item['url'])) {
            return $item['url'] === $this->getCurrentUrl();
        }

        return $this->isCurrentBranch($this->resolve($item, ['branch', 'root', 'route']))
            || $this->isCurrentRoot($this->resolve($item, ['root', 'route']))
            || $this->isCurrentRoute($this->resolve($item, ['route']));
    }

    /**
     * @param array<string,mixed> $item
     */
    public function isAccessible(array $item): bool
    {
        if (isset($item['access'])) {
            return (bool) $item['access'];
        }

        return true;
    }

    /**
     * @param array<string,mixed> $item
     * @param array<string>       $keys
     *
     * @return mixed|null
     */
    private function resolve(array $item, array $keys)
    {
        foreach ($keys as $key) {
            if (isset($item[$key])) {
                return $item[$key];
            }
        }

        return null;
    }

    public function isCurrentRoot(?string $root): bool
    {
        if (\is_null($root)) {
            return false;
        }

        return $this->getCurrentAttributeAsString('_menu_root') === $root;
    }

    public function isCurrentBranch(?string $branch): bool
    {
        if (\is_null($branch)) {
            return false;
        }

        return $this->getCurrentAttributeAsString('_menu_branch') === $branch;
    }

    public function isCurrentRoute(?string $route): bool
    {
        return $this->getCurrentAttributeAsString('_route') === $route;
    }

    /**
     * @param array<string,mixed> $parameters
     */
    public function addParametersToCurrentUrl(array $parameters, int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string
    {
        return $this->urlGenerator->generate(
            $this->getCurrentAttributeAsString('_route'),
            array_merge(
                $this->getCurrentAttributeAsArray('_route_params', []),
                $this->getCurrentQuery(),
                $parameters
            ),
            $referenceType
        );
    }

    public function getCurrentUrl(int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string
    {
        return $this->urlGenerator->generate(
            $this->getCurrentAttributeAsString('_route'),
            $this->getCurrentAttributeAsArray('_route_params'),
            $referenceType
        );
    }

    /**
     * Get attribute from current request
     */
    private function getCurrentAttributeAsString(string $name, string $default = ''): string
    {
        $request = $this->requestStack->getCurrentRequest();

        \assert($request instanceof Request, new \RuntimeException('No current request'));

        $value = $request->attributes->get($name, $default);

        \assert(
            \is_string($value),
            new \InvalidArgumentException('Attribute "$name" should be a string.')
        );

        return $value;
    }

    /**
     * Get attribute from current request
     *
     * @param array<string,mixed> $default
     *
     * @return array<string,mixed>
     */
    private function getCurrentAttributeAsArray(string $name, array $default = []): array
    {
        $request = $this->requestStack->getCurrentRequest();

        \assert($request instanceof Request, new \RuntimeException('No current request'));

        $value = $request->attributes->get($name, $default);

        \assert(
            \is_array($value),
            new \InvalidArgumentException('Attribute "$name" should be an array.')
        );

        return $value;
    }

    /**
     * @return array<string,mixed>
     */
    private function getCurrentQuery(): array
    {
        $request = $this->requestStack->getCurrentRequest();

        \assert($request instanceof Request, new \RuntimeException('No current request'));

        return $request->query->all();
    }
}
