<?php

declare(strict_types=1);

namespace Elao\Bundle\JsonHttpFormBundle\Tests\Form\RequestHandler;

use Elao\Bundle\AdminThemeBundle\Menu\Builder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class BuilderTest extends TestCase
{
    private Builder $builder;

    protected function setUp(): void
    {
        $this->builder = new Builder(
            $requestStack = $this->createMock(RequestStack::class),
            $urlGenerator = $this->createMock(UrlGeneratorInterface::class)
        );

        $requestStack
            ->method('getCurrentRequest')
            ->will($this->returnValue($this->getSampleRequest()));

        $urlGenerator
            ->method('generate')
             ->will($this->returnCallback([$this, 'mockUrlGenerator']));
    }

    public function testGetMenuPath(): void
    {
        // by Route
        $this->assertSame('/user/1', $this->builder->getMenuPath([
            'route' => 'admin_user_show',
            'parameters' => ['id' => 1],
        ]));

        // by Url
        $this->assertSame('/user/2', $this->builder->getMenuPath(['url' => '/user/2']));

        // Default
        $this->assertSame('#', $this->builder->getMenuPath([]));
    }

    public function testGetCurrentUrl(): void
    {
        $this->assertSame('/user/1', $this->builder->getCurrentUrl());
    }

    public function testAddParametersToCurrentUrl(): void
    {
        $this->assertSame('/user/1?page=1&foo=bar', $this->builder->addParametersToCurrentUrl([
            'page' => 1,
            'foo' => 'bar',
        ]));
    }

    public function testIsActive(): void
    {
        // by Active property
        $this->assertTrue($this->builder->isActive(['active' => true]));
        $this->assertFalse($this->builder->isActive(['active' => false]));

        // by Route
        $this->assertTrue($this->builder->isActive(['route' => 'admin_user_show']));
        $this->assertFalse($this->builder->isActive(['route' => 'admin_user_edit']));

        // by Root
        $this->assertTrue($this->builder->isActive(['root' => 'admin']));
        $this->assertFalse($this->builder->isActive(['root' => 'front']));

        // by Branch
        $this->assertTrue($this->builder->isActive(['branch' => 'user']));
        $this->assertFalse($this->builder->isActive(['branch' => 'client']));

        // by Url
        $this->assertTrue($this->builder->isActive(['url' => '/user/1']));
        $this->assertFalse($this->builder->isActive(['url' => '/user/2']));
    }

    public function testIsCurrentRoot(): void
    {
        $this->assertTrue($this->builder->isCurrentRoot('admin'));
        $this->assertFalse($this->builder->isCurrentRoot('front'));
    }

    public function testIsCurrentBranch(): void
    {
        $this->assertTrue($this->builder->isCurrentBranch('user'));
        $this->assertFalse($this->builder->isCurrentBranch('client'));
    }

    public function testIsCurrentRoute(): void
    {
        $this->assertTrue($this->builder->isCurrentRoute('admin_user_show'));
        $this->assertFalse($this->builder->isCurrentRoute('admin_user_edit'));
    }

    public function testIsAccessible(): void
    {
        // by Access property
        $this->assertTrue($this->builder->isAccessible(['access' => true]));
        $this->assertFalse($this->builder->isAccessible(['access' => false]));

        // Default
        $this->assertTrue($this->builder->isAccessible([]));
    }

    /**
     * @param array<string,mixed> $params
     */
    public function mockUrlGenerator(string $route, array $params = []): string
    {
        $path = '';

        switch ($route) {
            case 'admin_user_show':
                $path = "/user/{$params['id']}";
                unset($params['id']);
                break;

            default:
                throw new \Exception("Route '$route' not found.");
        }

        if (\count($params) > 0) {
            $path .= '?' . http_build_query($params);
        }

        return $path;
    }

    private function getSampleRequest(): Request
    {
        return new Request(
            [], // Query
            [], // Request
            [
                '_route' => 'admin_user_show',
                '_route_params' => ['id' => 1],
                '_menu_root' => 'admin',
                '_menu_branch' => 'user',
            ], // Attributes
            [], // Cookies
            [], // Files
            [], // Server
            null // Content
        );
    }
}
