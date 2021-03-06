<?php

/**
 * @file
 * Contains \Drupal\migrate_drupal\Plugin\migrate\process\d6\FileUri.
 */

namespace Drupal\migrate_drupal\Plugin\migrate\process\d6;

use Drupal\migrate\MigrateExecutable;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Process the file url into a D8 compatible URL.
 *
 * @MigrateProcessPlugin(
 *   id = "file_uri"
 * )
 */
class FileUri extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutable $migrate_executable, Row $row, $destination_property) {

    list($filepath, $file_directory_path, $is_public) = $value;

    // Strip the files path from the uri instead of using basename
    // so any additional folders in the path are preserved.
    $uri = preg_replace('/^' . preg_quote($file_directory_path, '/') . '/', '', $filepath);

    return $is_public ? "public://$uri" : "private://$uri";
  }

}
