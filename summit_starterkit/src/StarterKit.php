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
    if (!is_file($info_file)) {
      return;
    }

    $info = file_get_contents($info_file);
    if ($info === FALSE) {
      return;
    }

    $info = preg_replace('/^hidden: true\R/m', '', $info) ?? $info;
    $info = preg_replace(
      '/^description: .+$/m',
      "description: '$theme_name site theme, generated from Summit Base.'",
      $info,
    ) ?? $info;
    file_put_contents($info_file, $info);
  }

}
