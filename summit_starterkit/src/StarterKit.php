<?php

declare(strict_types=1);

namespace Drupal\summit_starterkit;

use Drupal\Core\Theme\StarterKitInterface;

/**
 * Post-process a generated Summit Base child theme.
 */
final class StarterKit implements StarterKitInterface {

  /**
   * {@inheritdoc}
   */
  public static function postProcess(string $working_dir, string $machine_name, string $theme_name): void {
    $info_file = "$working_dir/$machine_name.info.yml";
    if (is_file($info_file)) {
      $info = file_get_contents($info_file);
      if ($info !== FALSE) {
        $info = preg_replace('/^hidden: true\R/m', '', $info) ?? $info;
        $info = preg_replace(
          '/^description: .+$/m',
          "description: '$theme_name site theme, generated from Summit Base.'",
          $info,
        ) ?? $info;
        file_put_contents($info_file, $info);
      }
    }

    self::writeBrowserSyncProxy($working_dir, str_replace('_', '-', $machine_name) . '.ddev.site');
  }

  /**
   * Point BrowserSync at the DDEV hostname (underscores become hyphens).
   */
  private static function writeBrowserSyncProxy(string $theme_dir, string $proxy): void {
    $package_json = $theme_dir . '/package.json';
    if (!is_file($package_json)) {
      return;
    }

    $contents = file_get_contents($package_json);
    if ($contents === FALSE) {
      return;
    }

    $updated = preg_replace(
      '#http://[a-zA-Z0-9._-]+\.ddev\.site#',
      'http://' . $proxy,
      $contents,
      1,
    );
    if (is_string($updated) && $updated !== $contents) {
      file_put_contents($package_json, $updated);
    }
  }

}
