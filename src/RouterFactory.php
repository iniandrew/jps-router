<?php

namespace Jalameta\Router;

use RuntimeException;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;

/**
 * Router Factory
 *
 * @author      veelasky <veelasky@gmail.com>
 */
class RouterFactory
{
    /**
     * Laravel router
     *
     * @var \Illuminate\Routing\Router
     */
    protected $router;

    /**
     * Route groups.
     *
     * @var array
     */
    protected $routes = [];

    /**
     * List of all options.
     *
     * @var array
     */
    protected $options = [];

    /**
     * RouterFactory constructor.
     *
     * @param \Illuminate\Routing\Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Create new route group.
     *
     * @param       $key
     * @param array $options
     * @param array $items
     *
     * @return \Illuminate\Support\Collection
     */
    public function make($key, array $options = [], array $items = [])
    {
        if (array_key_exists($key, $this->routes) == false) {
            $this->routes[$key] = new Collection($items);
        } else {
            throw new RuntimeException("Route Group with key: `$key` already exist.");
        }

        $this->options[$key] = $options;

        return $this->get($key);
    }

    /**
     * Register to route group
     *
     * @return void
     */
    public function register()
    {
        foreach ($this->groups() as $group) {
            $this->map($group);
        }
    }

    /**
     * Map all routes into laravel routes
     *
     * @param $group
     *
     * @return void
     */
    public function map($group)
    {
        $this->router->group($this->getOptions($group), function() use ($group) {
            foreach ($this->get($group) as $item) {
                $item::bind();
            }
        });
    }

    /**
     * Get options for specific route group.
     *
     * @param $key
     * @return mixed
     */
    public function getOptions($key)
    {
        return $this->options[$key];
    }

    /**
     * Get Route Group container by its key.
     *
     * @param $key
     *
     * @return \Illuminate\Support\Collection
     */
    public function get($key)
    {
        if (array_key_exists($key, $this->routes)) {
            return $this->routes[$key];
        }

        throw new RuntimeException("Route Group with key: `$key` doesn't exists.");
    }

    /**
     * List of all registered route groups
     *
     * @return array
     */
    public function groups()
    {
        return array_keys($this->routes);
    }

    /**
     * Return all registered route.
     *
     * @return array
     */
    public function all()
    {
        return $this->routes;
    }
}