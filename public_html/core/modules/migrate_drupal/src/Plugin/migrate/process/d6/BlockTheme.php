<?php
/**
 * @file
 * Contains \Drupal\migrate_drupal\Plugin\migrate\Process\d6\BlockTheme.
 */

namespace Drupal\migrate_drupal\Plugin\migrate\Process\d6;

use Drupal\migrate\Entity\MigrationInterface;
use Drupal\migrate\MigrateExecutable;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Drupal\Core\Config\Config;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @MigrateProcessPlugin(
 *   id = "d6_block_theme"
 * )
 */
class BlockTheme extends ProcessPluginBase implements ContainerFactoryPluginInterface {

  /**
   * Contains the configuration object factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

 /**
  * Contains the system.theme configuration object.
  *
  * @var \Drupal\Core\Config\Config
  */
  protected $themeConfig;

  /**
   * Constructs a BlockTheme object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\migrate\Entity\MigrationInterface $migration
   *   The migration entity.
   * @param \Drupal\Core\Config\Config $theme_config
   *   The system.theme configuration factory object.
   * @param array $themes
   *   The list of themes available on the destination.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration, Config $theme_config, array $themes) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration);
    $this->themeConfig = $theme_config;
    $this->themes = $themes;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration = NULL) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $migration,
      $container->get('config.factory')->get('system.theme'),
      $container->get('theme_handler')->listInfo()
    );
  }

  /**
   * {@inheritdoc}
   *
   * Set the block theme, based on the current default theme.
   */
  public function transform($value, MigrateExecutable $migrate_executable, Row $row, $destination_property) {
    list($theme, $d6_default_theme, $d6_admin_theme) = $value;

    // If the source theme exists on the destination, we're good.
    if (isset($this->themes[$theme])) {
      return $theme;
    }

    // If the source block is assigned to a region in the source default theme,
    // then assign it to the destination default theme.
    if (strtolower($theme) == strtolower($d6_default_theme)) {
      return $this->themeConfig->get('default');
    }

    // If the source block is assigned to a region in the source admin theme,
    // then assign it to the destination admin theme.
    if (strtolower($theme) == strtolower($d6_admin_theme)) {
      return $this->themeConfig->get('admin');
    }

    // We couldn't map it to a D8 theme so just return the incoming theme.
    return $theme;
  }

}
