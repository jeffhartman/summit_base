<?php

declare(strict_types=1);

namespace Drupal\summit_base\Commands;

use Drush\Attributes as CLI;
use Drush\Boot\DrupalBootLevels;
use Drush\Commands\DrushCommands;
use Drush\Drush;
use Symfony\Component\Process\Process;

/**
 * Generates a thin Summit Base child theme.
 */
final class CreateSubthemeCommands extends DrushCommands {

  /**
   * Create a child theme that extends Summit Base.
   */
  #[CLI\Command(name: 'summit-base:create', aliases: ['sbc'])]
  #[CLI\Bootstrap(level: DrupalBootLevels::ROOT)]
  #[CLI\Argument(name: 'machine_name', description: 'Machine name of the new theme, e.g. scratchboard_kitchen.')]
  #[CLI\Option(name: 'name', description: 'Human-readable theme name.')]
  #[CLI\Option(name: 'path', description: 'Path relative to the Drupal root.')]
  #[CLI\Option(name: 'proxy', description: 'BrowserSync proxy host, e.g. scratchboard-kitchen.ddev.site.')]
  #[CLI\Usage(name: 'drush summit-base:create scratchboard_kitchen', description: 'Generate a child theme in themes/custom.')]
  public function create(
    string $machine_name,
    array $options = [
      'name' => NULL,
      'path' => 'themes/custom',
      'proxy' => NULL,
    ],
  ): void {
    if (!preg_match('/^[a-z][a-z0-9_]*$/', $machine_name)) {
      throw new \InvalidArgumentException('Machine name must start with a letter and contain only lowercase letters, numbers, and underscores.');
    }

    $name = $options['name'] ?: str_replace('_', ' ', ucwords($machine_name, '_'));
    $path = $options['path'] ?: 'themes/custom';
    $proxy = $options['proxy'] ?: str_replace('_', '-', $machine_name) . '.ddev.site';

    $drupalRoot = Drush::bootstrapManager()->getRoot();
    $theme_dir = $drupalRoot . '/' . $path . '/' . $machine_name;
    if (is_dir($theme_dir)) {
      throw new \RuntimeException("Theme directory already exists: $theme_dir");
    }

    $command = $this->generateThemeCommand($drupalRoot, $machine_name, $name, $path);
    $this->io()->writeln('Running: ' . implode(' ', $command));

    $process = new Process($command, $drupalRoot);
    $process->setTimeout(120);
    $process->run(function (string $type, string $buffer): void {
      $this->io()->write($buffer);
    });

    if (!$process->isSuccessful()) {
      throw new \RuntimeException('generate-theme failed. Is summit_starterkit discoverable? Try `drush cr` after composer require.');
    }

    $this->writeBrowserSyncProxy($theme_dir, $proxy);

    $relative = $path . '/' . $machine_name;
    $this->io()->success("Created $name at $relative");
    $this->io()->writeln(<<<EOT

Next steps (paths are relative to the Drupal root, e.g. docroot/ or web/):

  cd $relative
  npm install
  npm run build

  drush theme:enable summit_base $machine_name
  drush config:set system.theme default $machine_name -y
  drush cr

Override DaisyUI tokens, fonts, and --color-cta in css/src/app.css.
BrowserSync proxy is set to http://$proxy (npm run browser).

EOT);
  }

  /**
   * @return list<string>
   */
  private function generateThemeCommand(string $drupalRoot, string $machine_name, string $name, string $path): array {
    $candidates = [
      dirname($drupalRoot) . '/vendor/bin/dr',
      $drupalRoot . '/vendor/bin/dr',
    ];

    foreach ($candidates as $binary) {
      if (is_executable($binary) || is_file($binary)) {
        return [
          $binary,
          'generate-theme',
          $machine_name,
          '--starterkit=summit_starterkit',
          "--path=$path",
          "--name=$name",
        ];
      }
    }

    $script = $drupalRoot . '/core/scripts/drupal';
    if (!is_file($script)) {
      throw new \RuntimeException('Could not find vendor/bin/dr or core/scripts/drupal.');
    }

    return [
      PHP_BINARY,
      $script,
      'generate-theme',
      $machine_name,
      '--starterkit=summit_starterkit',
      "--path=$path",
      "--name=$name",
    ];
  }

  private function writeBrowserSyncProxy(string $theme_dir, string $proxy): void {
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
