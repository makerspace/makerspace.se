<?php

/**
 * @file
 * Contains \Drupal\Tests\Core\Logger\LoggerChannelFactoryTest.
 */

namespace Drupal\Tests\Core\Logger;

use Drupal\Core\Logger\LoggerChannelFactory;
use Drupal\Core\Session\AccountInterface;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\HttpFoundation\Request;

// @todo Remove in https://www.drupal.org/node/2267545.
if (!defined('WATCHDOG_EMERGENCY')) {
  define('WATCHDOG_EMERGENCY', 0);
  define('WATCHDOG_ALERT', 1);
  define('WATCHDOG_CRITICAL', 2);
  define('WATCHDOG_ERROR', 3);
  define('WATCHDOG_WARNING', 4);
  define('WATCHDOG_NOTICE', 5);
  define('WATCHDOG_INFO', 6);
  define('WATCHDOG_DEBUG', 7);
}

/**
 * @coversDefaultClass \Drupal\Core\Logger\LoggerChannelFactory
 * @group Logger
 */
class LoggerChannelFactoryTest extends UnitTestCase {

  /**
   * Tests LoggerChannelFactory::get().
   *
   * @covers ::get
   */
  public function testGet() {
    $factory = new LoggerChannelFactory();
    $factory->setContainer($this->getMock('Symfony\Component\DependencyInjection\ContainerInterface'));

    // Ensure that when called with the same argument, always the same instance
    // will be returned.
    $this->assertSame($factory->get('test'), $factory->get('test'));
  }

}
