# Kirby Baukasten

An opinionated, supercharged version of the Kirby Plainkit used internally at
[coralic](https://coralic.com) for our clients' sites, with preconfigured tooling
and plugins.

## Usage

##### Install Composer & Node dependencies with `composer install` and `pnpm install`.

```
composer install && pnpm install
```

##### Copy the example `env` file.

```
cp .env.example .env
```

##### Start the dev server.

```
pnpm run dev
```

## Best Practices/Features

### Styling

[Sass](https://sass-lang.com/dart-sass) is our CSS preprocessor. We use SCSS syntax. [Autoprefixer](https://github.com/postcss/autoprefixer) and minification with [CSSNano](https://github.com/cssnano/cssnano) is also included.

#### Naming syntax

We use a [BEM](https://github.com/bem)-like syntax. This naming scheme is enforced via [stylelint](https://stylelint.io/).

```css
.namespace-element .namespace-element_child -modifier;
```

For certain modifiers, it makes more sense to use `data-*` attributes as they are more flexible in terms of state management, especially if values are changed via TypeScript.

```css
[data-direction='left'] {
  transform: translateX(-100%);
}
```

#### Namespaces

We namespace our classes for more transparency. Namespacing is enforced via [stylelint](https://stylelint.io/).

- `s-`: Scope creates a new styling context. Similar to a Theme, but not necessarily cosmetic, these should be used sparingly—they can be open to abuse and lead to poor CSS if not used wisely.
- `o-`: Object that it may be used in any number of unrelated contexts to the one you can currently see it in. Making modifications to these types of class could potentially have knock-on effects in a lot of other unrelated places.
- `c-`: A component is a concrete, implementation-specific piece of UI. All of the changes you make to its styles should be detectable in the context you’re currently looking at. Modifying these styles should be safe and have no side effects.
- `u-`: Utility has a very specific role (often providing only one declaration) and should not be bound onto or changed. It can be reused and is not tied to any specific piece of UI.
- `is-`, `has-`: Is currently styled a certain way because of a state or condition. It tells us that the DOM currently has a temporary, optional, or short-lived style applied to it due to a certain state being invoked.

### Block system

Most of our pages are build in a page-builder-like fashion utilizing the Kirby Blocks field. To support the modularity of this approach, we've implemented a few specific helpers to make this easier.

#### TypeScript / interactivity

We try to avoid using TypeScript as much as possible, but some things are impossible to solve with just HTML + CSS, which is why we are following a strict Progressive Enhancement policy when needed.

Our simple blocks module allows you to create classes that will be initialized with the element if any selector is matching, optionally, you can specify blocks to be included in the bundle (useful for sub 1kB blocks) or only be lazy-loaded if any element is found (useful when blocks require large JS libraries, such as Swiper).

This works similarly to the customElements API, which we don't use yet due to lack of support for [Customized built-in elements in Safari](https://bugs.webkit.org/show_bug.cgi?id=182671).

In `src/modules/blocks.ts`, you can define which blocks should be loaded lazily or eagerly, including the target selectors.

```ts
// Default blocks will be loaded lazily from `src/blocks/*.ts`
const blocks: { selector: string; file: string }[] = [
  {
    selector: '.example',
    file: 'example'
  }
]

// Eager blocks will be included by default in the main bundle from `src/blocks/eager/*.ts`
const eager: { selector: string; file: string }[] = [
  {
    selector: '.bundled',
    file: 'bundled'
  }
]
```

An example class could look like this:

```ts
// src/blocks/example.ts
import { Block } from '@/utils'

export default class ExampleBlock extends Block {
  constructor(el: HTMLElement) {
    super(el)
    console.log('Initialzed:', el)
  }
}
```

As stated above, it'll be loaded lazily if any element with the class `example` is found on the page.

#### `$block->attr()` helper

To make it easier to work with the Kirby Blocks field, we've implemented a helper that allows you to deploy a block with a set of base attributes.

```php
<section <?= $block->attr(['class' => 'c-example-block']) ?>>
// <section
//    class="c-example-block o-block"
//    id="id-from-fields" data-block-id="[...]"
//    data-next-block="footer"
//    data-prev-block="navigation"
// >
```

This function is part of the `baukasten-base` plugin.

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

[MIT License](./LICENSE) © 2021-2023 [Tobias Möritz](https://github.com/tobimori)
