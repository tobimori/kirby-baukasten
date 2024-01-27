![Kirby 4 Baukasten Banner by Johann Schopplich](/.github/banner.png)

# Kirby Baukasten

An opinionated, supercharged version of the Kirby Plainkit used internally at Love & Kindness for our clients' sites, with preconfigured tooling and plugins.

## Requirements

- PHP 8.2+ with composer
- Node.js 20+ with pnpm

## Usage

##### Install Composer & Node dependencies with `composer install` and `pnpm install`.

```
composer install && pnpm install
```

##### Running the scaffold command with Kirby CLI

```
kirby baukasten:scaffold
```

> NOTE: If you don't have the Kirby CLI installed, you will need to run `composer global require getkirby/cli` first.

##### Start the dev server.

```
pnpm run dev
```

## Best Practices/Features

### Styling ([Tailwind CSS](https://tailwindcss.com/))

Styling is done with [Tailwind CSS](https://tailwindcss.com/) directly in Kirby templates or snippets.

The only pre-made change to the default theme involves switching from the Mobile-first approach to a Desktop-first approach. I do think that this is still the go-to approach for most projects.

#### What does this mean?

- Don’t use `sm:` to target all non-mobile devices
- Use unprefixed utilities to target desktop, and override them at smaller breakpoints

### TypeScript / interactivity ([Stimulus](https://stimulus.hotwired.dev/))

I try to avoid using TypeScript as much as possible, but some things are impossible to solve with just HTML + CSS, which is why I'm following a strict Progressive Enhancement policy when needed.

Since Kirby v4, I switched to [Stimulus](https://stimulus.hotwired.dev/) as framework supporting this approach. It's integrated with server-side templates exactly how I need it to be and very similiar to my own, previously used, "micro framework".

More information can be found in [src ▸ controllers ▸ README.md](src/controllers/README.md).

### Block system & the `$block->attr()` helper

Most of our pages are build in a page-builder-like fashion utilizing the Kirby Blocks field. To make it easier to work with, I've implemented a helper that allows you to deploy a block with a set of base attributes.

```php
<section <?= $block->attr(['class' => 'container']) ?>>
// <section
//    class="container"
//    id="id-from-fields"
//    data-next-block="footer"
//    data-prev-block="navigation"
// >
```

This function is part of the [`tobimori/kirby-spielzeug`](https://github.com/tobimori/kirby-spielzeug) plugin, which contains a encapsulated set of helpers & tools I use for my projects and serves as the independent foundation for Baukasten.

### View Transitions ([Taxi.js](https://taxi.js.org/))

When working on fancy sites that use a lot of animations, I use [Taxi.js](https://taxi.js.org/) to go the extra-mile & handle view transitions. It's a very lightweight library that has a nice API and is easy to use.

If you don't want to use Taxi:

- remove the `@unseenco/taxi` JS dependency
- delete the `src/transitions` & `src/renderers` folder
- remove the `data-taxi` & `data-taxi-view` attributes from `layout.php`
- remove the related code from `src/index.ts`

### Code Typing

This template tries to make Kirby development more accessible by adding PHP code
typing and auto completion. Sadly, this doesn't work straight out of the box.

#### Controllers

For controllers & other PHP files, we can add type declarations by importing the classes using
PHP’s `use`:

```php
<?php // site/controllers/article.php

use Kirby\Cms\App;
use Kirby\Cms\Page;
use Kirby\Cms\Site;

return function (Site $site, App $kirby, Page $page) {
  […]
}
```

#### Templates

Templates will receive variables defined by Kirby (like the `$page` and `$kirby`
objects), and any other variable you return in a controller. Unfortunately, we can't
declare them in PHP directly, so we need to
[use the PHPDoc @var tag](https://github.com/php-fig/fig-standards/blob/2668020622d9d9eaf11d403bc1d26664dfc3ef8e/proposed/phpdoc-tags.md#517-var).

```php
<?php // site/templates/article.php

/** @var Kirby\Cms\Page $page */

<h1><?= $page->title() ?></h1>
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
[Kirby reference](https://getkirby.com/docs/reference/objects).

#### Auto completion in VS Code

For excellent PHP support in VS Code, we use
[PHP Intelephense](https://marketplace.visualstudio.com/items?itemName=bmewburn.vscode-intelephense-client).
Follow the Quick Start instructions. Other IDEs like PhpStorm may support this
out-of-the-box.

## License

[MIT License](.github/LICENSE) © 2021-2023 [Tobias Möritz](https://github.com/tobimori)

Thanks to [Johann](https://github.com/johannschopplich) for the cute banner gecko!
