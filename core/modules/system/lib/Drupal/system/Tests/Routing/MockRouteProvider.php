<?php

/**
 * @file
 * Contains Drupal\system\Tests\Routing\MockRouteProvider.
 */

namespace Drupal\system\Tests\Routing;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RouteCollection;
use Drupal\Core\Routing\RouteProviderInterface;

/**
 * Easily configurable mock route provider.
 */
class MockRouteProvider implements RouteProviderInterface {

  /**
   * A collection of routes for this route provider.
   *
   * @var RouteCollection
   */
  protected $routes;

  /**
   * Constructs a new MockRouteProvider.
   *
   * @param \Symfony\Component\Routing\RouteCollection $routes
   *   The route collection to use for this provider.
   */
  public function __construct(RouteCollection $routes) {
    $this->routes = $routes;
  }

  /**
   * Implements \Symfony\Cmf\Component\Routing\RouteProviderInterface::getRouteCollectionForRequest().
   *
   * Simply return all routes to prevent
   * \Symfony\Component\Routing\Exception\ResourceNotFoundException.
   */
  public function getRouteCollectionForRequest(Request $request) {
    return $this->routes;
  }

  /**
   * Implements \Symfony\Cmf\Component\Routing\RouteProviderInterface::getRouteByName().
   */
  public function getRouteByName($name, $parameters = array()) {
    $routes = $this->getRoutesByNames(array($name), $parameters);
    if (empty($routes)) {
      throw new RouteNotFoundException(sprintf('Route "%s" does not exist.', $name));
    }

    return reset($routes);
  }

  /**
   * Implements \Symfony\Cmf\Component\Routing\RouteProviderInterface::getRoutesByName().
   */
  public function getRoutesByNames($names, $parameters = array()) {
    $routes = array();
    foreach ($names as $name) {
      $routes[] = $this->routes->get($name);
    }

    return $routes;
  }

  /**
   * {@inheritdoc}
   */
  public function getRoutesByPattern($pattern) {
    return new RouteCollection();
  }

  /**
   * @inheritdoc}
   */
  public function getAllRoutes() {
    return $this->routes->all();
  }

}
