# Summit Base

A Drupal 10/11 **base theme** of [DaisyUI](https://daisyui.com/) Single Directory Components and a [Tailwind CSS](https://tailwindcss.com/) v4 build.

Composer installs the full set of primitives. Site themes are **thin children**: they extend Summit Base, compile CSS, and add brand. They do not copy SDCs.

## Stack

- Drupal 10 or 11 (`base theme: false`)
- Tailwind CSS v4 (Vite)
- DaisyUI 5
- `@tailwindcss/typography` for CMS/WYSIWYG HTML (`.prose`)

## Requirements

- Drupal 10 or 11
- Drush 12+
- Node 22.12+ (Vite 8)
- npm

## What lives where

| Summit Base (`themes/contrib`) | Child (`themes/custom`) |
| --- | --- |
| All SDCs (`summit_base:card`, `summit_base:button`, …) | Site-only SDCs and template overrides |
| Default DaisyUI theme, forms, `.prose`, utilities | Brand fonts, colors, `--color-cta` |
| Vite starter used by the child build | Compiled `css/dist/app.css` (gitignored) |
| `drush summit-base:create` | BrowserSync host and other site env |

If a class, token, font, or icon would be wrong on the next unrelated site, it belongs in the child.

## Install

From the **project root** (the directory that contains `composer.json` and `docroot/` or `web/`):

```sh
composer require jeffhartman/summit_base:dev-main
```

Use a version constraint like `^1.0` once the repo has tags.

Until the package is on Packagist, add a VCS repository in the site `composer.json`:

```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/jeffhartman/summit_base.git"
    }
  ]
}
```

Composer places it at `themes/contrib/summit_base` (relative to the Drupal root: `docroot/` or `web/`).

## Create a child theme

```sh
drush summit-base:create MACHINE_NAME
```

Examples:

```sh
ddev drush summit-base:create scratchboard_kitchen
ddev drush summit-base:create scratchboard_kitchen --proxy=scratchboard-kitchen.ddev.site
```

That wraps Drupal core `generate-theme` using the **thin** `summit_starterkit` skeleton (not a copy of Summit Base). The new theme is written to `themes/custom/MACHINE_NAME` and has `base theme: summit_base`.

Equivalent core command (Drupal 11.4+, from the Drupal root):

```sh
vendor/bin/dr generate-theme MACHINE_NAME \
  --starterkit=summit_starterkit \
  --path themes/custom
```

On Drupal 11.3 and earlier: `php core/scripts/drupal generate-theme …` (from the Drupal root).

### After generate

```sh
cd docroot/themes/custom/MACHINE_NAME   # or web/themes/custom/MACHINE_NAME
npm install
npm run build

drush theme:enable summit_base MACHINE_NAME
drush config:set system.theme default MACHINE_NAME -y
drush cr
```

Override DaisyUI tokens, fonts, and `--color-cta` in the child’s `css/src/app.css`. Set BrowserSync in the child’s `package.json` (`npm run browser`). `--proxy` on the Drush command defaults to `machine-name.ddev.site` (underscores become hyphens).

## Child CSS

The generated `css/src/app.css` imports Summit Base and scans both themes:

```css
@import "@summit-base/css/src/app.css";

@source "../../components";
@source "../../templates";
```

`@summit-base` is aliased in `vite.config.mjs` to `themes/contrib/summit_base`. Rebuild after editing tokens.

### Tokens to override

In the child’s `@plugin "daisyui/theme"` block:

`base-100`, `base-200`, `base-300`, `base-content`, `primary`, `primary-content`, `secondary`, `accent`, `neutral`, `info`, `success`, `warning`, `error`

In `@theme inline`:

```css
--font-display: ui-sans-serif, system-ui, sans-serif;
--font-body: ui-sans-serif, system-ui, sans-serif;
--color-cta: oklch(0.55 0.15 250);
--color-cta-foreground: oklch(0.99 0 0);
```

Load webfonts in the child. Use DaisyUI class names (`bg-base-100`, `text-base-content`, `btn`).

## Components

```twig
{{ include('summit_base:button', {
  label: 'Get in touch'|t,
  url: '/contact',
  variant: 'cta',
}, with_context = false) }}
```

Layout examples: `hero`, `section`, `section-heading`, `card`, `image-with-text`, `wysiwyg`, `faq`, `timeline`, `site-header`, `site-footer`.

Formatted text (body fields, WYSIWYG) gets `.prose` for `text`, `text_long`, and `text_with_summary` field items. Use `not-prose` on nested components that should not inherit article styles.

## License

ISC
