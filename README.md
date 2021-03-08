# coralic Kirby Plainkit

An opinionated, supercharged version of the Kirby Plainkit used internally at
[coralic](https://coralic.de) for our clients' sites, with preconfigured tooling
and plugins.

- [Features](#features)
- [Requirements](#requirements)
- [Usage](#usage)
- [Best Practices](#best-practices)
  - [PHP code typing](#php-code-typing)
    - [Controllers](#controllers)
    - [Templates](#templates)
    - [Page Models](#page-models)
    - [Auto completion in VS Code](#auto-completion-in-vs-code)
    - [Special Thanks](#special-thanks)
  - [Usage of Composer](#usage-of-composer)
  - [Development Environment](#development-environment)
  - [Managing the `content` folder and Git](#managing-the-content-folder-and-git)
- [Credits](#credits)

## Features

- Code formatting with [Prettier](https://prettier.io/), helpful for JavaScript files
  & Kirby blueprints
- SCSS linting and formatting with [stylelint](https://stylelint.io/)
- JavaScript linting with [ESLint](https://eslint.org/)
- [Browsersync](https://www.browsersync.io/) dev server with **livereload** for changes in content, stylesheets, Kirby templates & JavaScript code
- File watching and automatic recompilation on change
- Optional **built-in PHP Server** wrapped in the dev server - with option to rely on third-party servers like [Laravel Valet](https://laravel.com/docs/8.x/valet)
- Extremely fast JavaScript bundling with [esbuild](https://esbuild.github.io/)
- SCSS compilation with [Dart Sass](https://sass-lang.com/dart-sass) and additional processing done by [PostCSS](https://postcss.org/)

**todo:**

- GitHub Actions Workflows for Linting, Formatting, etc.

## Requirements

- `node`
- `yarn`
- `php` >= 7.4 / 8.0
  - macOS → [Install PHP using Homebrew or use the bundled PHP](https://www.php.net/manual/en/install.macosx.php)
  - Windows → [Download it from the official PHP website](http://windows.php.net/download)
  - Linux → You know what to do :)

## Usage

Install Composer and Node dependencies with `composer install` and `yarn`.

```sh
composer install && yarn
```

## Best Practices

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

#### Special Thanks

Thanks to [Florens Verschelde](https://fvsch.com/) for his comprehensive article
about [Kirby & PHP code typing](https://fvsch.com/kirby-typing).

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

## Credits

This boilerplate setup heavily took inspiration from:

- [brocessing/kirby-webpack](https://github.com/brocessing/kirby-webpack) &
- [getkirby/plainkit](https://github.com/getkirby/plainkit)
