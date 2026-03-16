# Development Rules

This is a **Kirby 5 CMS** project. Individual pages available as markdown by appending `.md` to any URL.

Docs: https://getkirby.com/llms.txt

## Existing Project Patterns

Before implementing anything, check what already exists in the project:

- **`site/plugins/`** has installed plugins that may already solve the problem. Also check `composer.json` for dependencies.
- **`site/snippets/`** has existing snippets to reuse. Use the `image` snippet for all images instead of raw `<img>` tags. It handles responsive images, lazy loading, and SVG inlining.
- **Existing blocks/templates** show patterns to follow before writing new ones.

When told to "use X", check if X is an existing package/plugin in the project before doing anything else.

## HTML / Markup

### Cards

When creating cards that have a link, wrap the anchor tag only around the card's title, then expand it to cover the entire card using absolutely positioned pseudo-elements:

```html
<div class="relative">
    <a href="/page" class="after:absolute after:inset-0"> Card Title </a>
    <p class="relative z-10">Long-form text that can still be selected and copied.</p>
</div>
```

This ensures screen readers only announce the link once. Use `relative z-10` on elements that should remain interactive above the link overlay.

### Strings / i18n

Use the `t()` function for hardcoded labels and strings. Pass the English translation as the argument, it serves as both the key and default value:

```php
<?= t('Read more') ?>
<?= tt('Open submenu: {title}', ['title' => $title]) ?>
<?= tc('{{ count }} members', $count) ?>
```

After adding new strings, run `kirby trawl:extract` to extract them into the JSON translation files. Let the user translate and don't manually edit translation files.

In blueprints, most properties (`label`, `help`, `placeholder`, `before`, `after`, section `headline`/`text`) auto-resolve plain strings as translation keys, so no special syntax is needed.

Option `text` in select/radio/checkbox fields does not auto-resolve. Use `*:` to reference a translation key:

```yaml
fields:
  category:
    type: select
    options:
      summer:
        "*": Summer
      autumn:
        "*": Fall
```

## Styling (Tailwind CSS v4)

This project resets all default Tailwind theme values (colors, font sizes, radii, shadows, etc.) using `--*: initial` in `src/styles/index.css` and only defines project-specific values. Check `src/styles/index.css` for available theme tokens before using any Tailwind utility. For example, `text-red-500` or `rounded-lg` won't work unless explicitly defined in the theme.

### Custom classes

Custom classes are valid in two cases:

1. **Single HTML elements** that need consistent styling (e.g. `.button`), because creating a snippet for one element is overkill
2. **Nested/prose content** where you need to style child elements you don't control (e.g. `.prose` targeting `p`, `h2`, `li` inside rich text)

If a component combines markup with styling (e.g. a badge with text + icon), create a **Kirby snippet** instead of a custom class. The rendering logic belongs with the styles.

### Focus Styles

Add custom focus styles to interactive elements. Silence browser defaults with `outline-none` and add custom styles with `focus-visible:`:

- Prefer `ring` utilities for interactive elements
- Use `underline` for links unless specified otherwise
- Use `hocus:` (custom variant) to apply styles on both hover and focus

### Desktop-first

This project uses **desktop-first** breakpoints (opposite of Tailwind's default mobile-first).

- **No prefix** = default (large screens)
- **With prefix** = applies at that width **and below**

Don't use a prefix like `min-xl` to target larger screens.

```html
<div class="p-8 md:p-4"></div>
```

| Viewport | Padding |
| -------- | ------- |
| > 44rem  | p-8     |
| ≤ 44rem  | p-4     |

The config can be found in `src/styles/index.css`.

### !important

If you really need to force `!important`, use the `!` suffix (not the prefix):

```html
<div class="bg-white bg-black!"></div>
```

### Spacing scale

Tailwind 4 calculates spacing as `n * 0.25rem`. You don't have to follow the known spacing scale, so use `h-128` instead of `h-[32rem]`.

## Tailwind Merge

When conditionally applying classes, use one of the following functions:

- `merge()` outputs a `class="..."` attribute with merged classes
- `cls()` outputs just the merged class string
- `attr()` works like Kirby's built-in, but merges Tailwind classes in the `class` attribute

You don't need to use `cls()` inside `attr()`.

All support conditional syntax with arrays:

```php
<div <?= merge(['bg-white', 'px-16' => $isWide]) ?>></div>
```

## Alpine.js

Define components with a default export in `src/components/`. These will be automatically registered and can be used in the frontend with `x-data`.

Don't use `document.querySelector()` or similar document-level methods. Scope to the component's elements instead, e.g. using `this.$root.querySelector()`. Prefer Alpine's built-in methods like `x-on`, `x-show`, `x-transition` to trigger methods, etc. Avoid global event listeners. Use Alpine.js plugins like Focus, Intersect, Resize, etc. when required.

Documentation: https://alpinejs.dev

## Icons

SVG icons live in `assets/icons/` and are compiled into a sprite by Vite. Use the `icon()` helper to render them. Don't inline SVGs or use `<img>` tags for icons.

```php
<?= icon('angle-right', class: 'size-4') ?>
```

Icons use `currentcolor` and are `aria-hidden="true"` by default. Size them with Tailwind's `size-*` utilities.

## PHP

### Kirby Fields

Fields store raw strings and need casting (`->toPage()`, `->toFiles()`, `->toBool()`, etc.). For output, use `->esc()` on text fields and `->permalinksToUrls()` on writer fields.

### Collections

Use `$collection->indexOf($item)` instead of `$i => $item` when you need the index in a loop. Kirby collection keys are content-based, not numeric.

### Code Style

Avoid setting preliminary variables in files. Prefer inline expressions instead, unless you'd need to repeat a statement.

Inline when used once:

```php
<?php snippet('image', ['image' => $block->image()->toFile()]) ?>
```

Assign in a condition when reused:

```php
<?php if ($isLtr = $block->order()->value() === 'ltr') : ?>
	<?php snippet('image', ['image' => $block->image()->toFile()]) ?>
<?php endif ?>

<?php if (!$isLtr) : ?>
	<?php snippet('image', ['image' => $block->image()->toFile()]) ?>
<?php endif ?>
```
