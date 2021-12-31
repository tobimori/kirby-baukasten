# coralic Kirby Plainkit

An opinionated, supercharged version of the Kirby Plainkit used internally at
[coralic](https://coralic.com) for our clients' sites, with preconfigured tooling
and plugins.

- [Features](#features)
- [Requirements](#requirements)
- [Usage](#usage)
- [Best Practices](#best-practices)
  - [Styles](#styles)
    - [Naming](#naming)
    - [Namespaces](#namespaces)
    - [Examples](#examples)
  - [PHP code typing](#php-code-typing)
    - [Controllers](#controllers)
    - [Templates](#templates)
    - [Page Models](#page-models)
    - [Auto completion in VS Code](#auto-completion-in-vs-code)
  - [Usage of Composer](#usage-of-composer)
  - [Development Environment](#development-environment)
  - [Managing the `content` folder and Git](#managing-the-content-folder-and-git)
- [Credits](#credits)

## Features

- Code formatting with [Prettier](https://prettier.io/), helpful for JavaScript files
  & Kirby blueprints
- SCSS linting and formatting with [stylelint](https://stylelint.io/)
- JavaScript linting with [ESLint](https://eslint.org/) and [JavaScript Standard Style](https://standardjs.com/)
- [Browsersync](https://www.browsersync.io/) dev server with **livereload** for changes in content, stylesheets, Kirby templates & JavaScript code
- File watching and automatic recompilation on change
- Optional **built-in PHP Server** wrapped in the dev server - with option to rely on third-party servers like [Laravel Valet](https://laravel.com/docs/8.x/valet)
- JavaScript bundling with [esbuild](https://esbuild.github.io/)
- SCSS compilation with [Dart Sass](https://sass-lang.com/dart-sass) and additional, configurable processing done by [PostCSS](https://postcss.org/)

**todo:**

- GitHub Actions Workflows for Linting, Formatting, etc.

## Requirements

- `node`
- `pnpm`
- `php` >= 7.4 / 8.0
  - macOS → [Install PHP using Homebrew or use the bundled PHP](https://www.php.net/manual/en/install.macosx.php)
  - Windows → [Download it from the official PHP website](http://windows.php.net/download)
  - Linux → You know what to do :)

## Usage

Install Composer and Node dependencies with `composer install` and `pnpm install`.

```sh
composer install && pnpm install
```

## Best Practices

### Styles

[Sass](https://sass-lang.com/dart-sass) is our CSS preprocessor. We use SCSS syntax. [Autoprefixer](https://github.com/postcss/autoprefixer) and minification with [CSSNano](https://github.com/cssnano/cssnano) is also included.

#### Naming

We use a [BEM](https://github.com/bem)-like syntax. This naming scheme is enforced via [stylelint](https://stylelint.io/).

```css
.namespace-element .namespace-element_child -modifier;
```

#### Namespaces

We namespace our classes for more transparency. Namespacing is enforced via [stylelint](https://stylelint.io/).

- `s-`: Scope creates a new styling context. Similar to a Theme, but not necessarily cosmetic, these should be used sparingly—they can be open to abuse and lead to poor CSS if not used wisely.
- `o-`: Object that it may be used in any number of unrelated contexts to the one you can currently see it in. Making modifications to these types of class could potentially have knock-on effects in a lot of other unrelated places.
- `b-`: Block component and/or section that relates to Kirby's block system and or the page structure.
- `c-`: A children component is a concrete, implementation-specific piece of UI. All of the changes you make to its styles should be detectable in the context you’re currently looking at. Modifying these styles should be safe and have no side effects.
- `u-`: Utility has a very specific role (often providing only one declaration) and should not be bound onto or changed. It can be reused and is not tied to any specific piece of UI.
- `f-`: Utility classes for font declarations that can be reused all over the project. Should also be availabe as SCSS mixin.
- `is-`, `has-`: Is currently styled a certain way because of a state or condition. It tells us that the DOM currently has a temporary, optional, or short-lived style applied to it due to a certain state being invoked.

#### Example

```html
<div class="b-text-with-image -image-left">
  <div class="o-container">
    <div class="c-text-with-image_img-wrapper">
      <?php if ($image = $block->image()->toFile()) : ?>
      <img src="<?= $image->url() ?>" alt="<?= $image->alt() ?>" />
      <?php endif; ?>
    </div>
    <div class="c-text-with-image_text"><?= $block->text() ?></div>
  </div>
</div>
```

```scss
.b-text-with-image {
  background: var(--background);
  padding: 0 0 120px;

  &.-image-left {
    .o-container {
      gap: 68px;
    }

    .c-text-with-image_text {
      margin-right: 48px;
    }
  }

  .c-text-with-image_text {
    color: var(--primary);

    h2 {
      @include fonts.headline-48;
      margin-bottom: 24px;
    }

    p {
      @include fonts.body-16;
    }
  }
}
```

### PHP code typing

This template tries to make Kirby development more accessible by adding PHP code
typing and auto completion. Sadly, this doesn't work straight out of the box.

#### Controllers

For controllers, we can add type declarations by importing the classes using
PHP’s `use`:

```php
<?php // site/controllers/article.php
use Kirby\Cms\App;
use Kirby\Cms\Page;
use Kirby\Cms\Site;

return function (Site $site, App $kirby, Page $page) {
  …
}
```

#### Templates

Templates will receive variables defined by Kirby (like the `$page` and `$kirby`
objects), and any other variable you return in a controller. Sadly, we can't
declare them in PHP directly, so we need to
[use the PHPDoc @var tag](https://github.com/php-fig/fig-standards/blob/2668020622d9d9eaf11d403bc1d26664dfc3ef8e/proposed/phpdoc-tags.md#517-var).

```php
<?php // site/templates/article.php

/** @var Kirby\Cms\Page $page */

<h1><?= $page->title() ?></h1>
...
```

As PHPDoc comments aren't a native PHP feature, this won't affect how our code
runs, although all IDEs and most code editors (like VS Code) should support
them.

#### Page Models

If we're using a
[Page Model](https://getkirby.com/docs/guide/templates/page-models) to expand
Kirby's default page object, we can use it in our templates in the same way.

```php
<?php // site/models/article.php

class ArticlePage extends Kirby\Cms\Page {
  public function getArticleBody(): string {
    if ($this->content()->body()->isNotEmpty()) {
      return $this->content()->body()->markdown();
    }
    return '';
  }
}
```

```php
<?php // site/templates/article.php

/** @var ArticlePage $page */

<h1><?= $page->title() ?></h1>
<?= $page->getArticleBody() ?>
```

For classes reference, check out the
[Kirby reference/documentation](https://getkirby.com/docs/reference/@/classes).

#### Auto completion in VS Code

For excellent PHP support in VS Code, we use
[PHP Intelephense](https://marketplace.visualstudio.com/items?itemName=bmewburn.vscode-intelephense-client).
Follow the Quick Start instructions. Other IDEs like PhpStorm may support this
out-of-the-box.

### Usage of Composer

We strictly use Composer as package manager for managing and updating the used
Kirby installation and its plugins. More information about this practice can be
found in the corresponding
[Kirby cookbook](https://getkirby.com/docs/cookbook/setup/composer).

### Development environment

We use the included PHP router as local development environment and proxy its
server with the preconfigured [Browsersync](https://www.browsersync.io/)
installation of this template. [VS Code](https://code.visualstudio.com/) is our
preferred code editor. A list of recommended extensions for use with this
template is included.

### Managing the `content` folder and Git

We don't push our clients' `content` folders. It's excluded from Git. In case
there's something wrong, our hosting environments usually have hourly backups.

When working with a staging environment, we download the current content from
site before starting and upload it after we're done using `rsync`. Although
often we let our clients validate the staging environment before we sync the
`content` folder of `staging` to `production`.

We're working on an automated way to do this, but for now, it needs to be done manually.

## Credits

This boilerplate setup heavily took inspiration from:

- [locomotivemtl/locomotive-boilerplate](https://github.com/locomotivemtl/locomotive-boilerplate) &
- [brocessing/kirby-webpack](https://github.com/brocessing/kirby-webpack) &
- [getkirby/plainkit](https://github.com/getkirby/plainkit)

Thanks to [Florens Verschelde](https://fvsch.com/) for his comprehensive article
about [Kirby & PHP code typing](https://fvsch.com/kirby-typing).

## License

[MIT License](./LICENSE) © 2021 [Tobias Möritz](https://github.com/tobimori)
