<?php

declare(strict_types=1);

/**
 * @file
 * Theme settings form for Summit Base.
 */

use Drupal\Core\Form\FormState;

/**
 * Implements hook_form_system_theme_settings_alter().
 */
function summit_base_form_system_theme_settings_alter(array &$form, FormState $form_state): void {

  $form['summit_base'] = [
    '#type' => 'details',
    '#title' => t('Summit Base'),
    '#open' => TRUE,
  ];

  $form['summit_base']['example'] = [
    '#type' => 'textfield',
    '#title' => t('Example'),
    '#default_value' => theme_get_setting('example'),
  ];

}
