<?php

namespace Symfonian\Indonesia\AdminBundle\Route;

/*
 * Author: Muhammad Surya Ihsanuddin<surya.kejawen@gmail.com>
 * Url: https://github.com/ihsanudin
 */

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

final class HomeRouteLoader implements LoaderInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    private $loaded = false;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $resource
     * @param null $type
     * @return RouteCollection
     */
    public function load($resource, $type = null)
    {
        if (true === $this->loaded) {
            throw new \RuntimeException('Do not add the "siab" loader twice');
        }

        $routes = new RouteCollection();

        $path = $this->container->getParameter('symfonian_id.admin.home.route_path');
        $defaults = array(
            '_controller' => $this->container->getParameter('symfonian_id.admin.home.controller'),
        );
        $route = new Route($path, $defaults, array(), array('expose' => true));
        $route->setMethods('GET');

        $routes->add('home', $route);
        $this->loaded = true;

        return $routes;
    }

    /**
     * @param string $resource
     * @param null $type
     * @return bool
     */
    public function supports($resource, $type = null)
    {
        return 'siab' === $type;
    }

    public function setResolver(LoaderResolverInterface $resolver)
    {
    }

    public function getResolver()
    {
    }
}
