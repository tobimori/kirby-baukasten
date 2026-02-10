# Development Rules

## Tailwind CSS v4

### Desktop-first

This project uses **desktop-first** breakpoints (opposite of Tailwind's default mobile-first).

- **No prefix** = default (large screens)
- **With prefix** = applies at that width **and below**

DO NOT use a prefix like `min-xl` to target larger screens.

```html
<div class="p-8 md:p-4"></div>
```

| Viewport | Padding |
| -------- | ------- |
| > 44rem  | p-8     |
| ≤ 44rem  | p-4     |

The config can be found in `src/styles/index.css`.

### !important

If you really need to force `!important`, use the `!` suffix (NOT prefix):

```html
<div class="bg-white bg-black!"></div>
```

### Spacing scale

Tailwind 4 calculates spacing as `n * 0.25rem`. You DO NOT have to follow the known spacing scale. Use `h-128` instead of `h-[32rem]`.

## Tailwind Merge

If you need to conditionally apply classes, ALWAYS refer to one of the following functions:

- `merge()` - outputs `class="..."` attribute with merged classes
- `cls()` - outputs just the merged class string
- `attr()` - like Kirby's built-in, but merges Tailwind classes in the `class` attribute

You DO NOT need to use `cls()` inside `attr()`.

All support conditional syntax with arrays:

```php
<div <?= merge(['bg-white', 'px-16' => $isWide]) ?>></div>
```

## Alpine.js

Define components with a default export in `src/components/`. These will be automatically registered and can be used in the frontend with `x-data`.

NEVER use document.querySelector() or similar document-level methods. ALWAYS scope to the component's elements, e.g. using `this.$root.querySelector()`. Generally, prefer Alpine's built-in methods like `x-on`, `x-show`, `x-transition`, to trigger methods, etc. Avoid global event listeners.

## PHP

### Code Style

AVOID setting preliminary variables in files. Prefer inline expressions instead UNLESS you'd need to repeat a statement.

BAD: unnecessary variable:

```php
<?php
$image = $block->image()->toFile();
snippet('picture', ['image' => $image]);
?>
```

GOOD: inline when used once:

```php
<?php snippet('picture', ['image' => $block->image()->toFile()]) ?>
```

GOOD: assign in condition when reused:

```php
<?php if ($isLtr = $block->order()->value() === 'ltr') : ?>
	<?php snippet('picture', ['image' => $block->image()->toFile()]) ?>
<?php endif ?>

<?php if (!$isLtr) : ?>
	<?php snippet('picture', ['image' => $block->image()->toFile()]) ?>
<?php endif ?>
```
