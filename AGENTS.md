# Development Rules

This is a **Kirby 5 CMS** project. Individual pages available as markdown by appending `.md` to any URL.

Docs: https://getkirby.com/llms.txt
  
## Existing Project Patterns

Before implementing anything, ALWAYS check what already exists in the project:

- **`site/plugins/`** — Check for installed plugins that already solve the problem. Also check `composer.json` for dependencies.
- **`site/snippets/`** — Use existing snippets. NEVER use raw `<img>` tags — use the `image` snippet for all images. It handles responsive images, lazy loading, and SVG inlining.
- **Existing blocks/templates** — Look at similar blocks for patterns before writing new ones.

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

ALWAYS use the `t()` function for hardcoded labels and strings. Pass the English translation as the argument, it serves as both the key and default value:

```php
<?= t('Read more') ?>
<?= tt('Open submenu: {title}', ['title' => $title]) ?>
<?= tc('{{ count }} members', $count) ?>
```

After adding new strings, run `kirby trawl:extract` to extract them into the JSON translation files. Let the user translate. DO NOT manually edit translation files.

In blueprints, most properties (`label`, `help`, `placeholder`, `before`, `after`, section `headline`/`text`) auto-resolve plain strings as translation keys — no special syntax needed.

Option `text` in select/radio/checkbox fields does NOT auto-resolve. Use `*:` to reference a translation key:

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

**IMPORTANT:** This project resets ALL default Tailwind theme values (colors, font sizes, radii, shadows, etc.) using `--*: initial` in `src/styles/index.css` and only defines project-specific values. ALWAYS check `src/styles/index.css` for available theme tokens before using any Tailwind utility. For example, `text-red-500` or `rounded-lg` will NOT work unless explicitly defined in the theme.

### Custom classes

Custom classes are valid in two cases:

1. **Single HTML elements** that need consistent styling (e.g. `.button`) — because creating a snippet for one element is overkill
2. **Nested/prose content** where you need to style child elements you don't control (e.g. `.prose` targeting `p`, `h2`, `li` inside rich text)

If a component combines markup with styling (e.g. a badge with text + icon), create a **Kirby snippet** instead of a custom class, the rendering logic belongs with the styles.

### Focus Styles

ALWAYS add custom focus styles. Silence browser defaults with `outline-none` and add custom styles with `focus-visible:`:

- Prefer `ring` utilities for interactive elements
- Use `underline` for links unless specified otherwise
- Use `hocus:` (custom variant) to apply styles on both hover and focus

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

NEVER use document.querySelector() or similar document-level methods. ALWAYS scope to the component's elements, e.g. using `this.$root.querySelector()`. Generally, prefer Alpine's built-in methods like `x-on`, `x-show`, `x-transition`, to trigger methods, etc. Avoid global event listeners. Use Alpine.js plugins like Focus, Intersect, Resize, etc. when required.

Documentation: https://alpinejs.dev

## Icons

SVG icons live in `assets/icons/` and are compiled into a sprite by Vite. Use the `icon()` helper to render them. Never inline SVGs or use `<img>` tags for icons.

```php
<?= icon('angle-right', class: 'size-4') ?>
```

Icons use `currentcolor` and are `aria-hidden="true"` by default. Size them with Tailwind's `size-*` utilities.

## PHP

### Kirby Fields

Fields store raw strings and need casting (`->toPage()`, `->toFiles()`, `->toBool()`, etc.). For output, use `->esc()` on text fields and `->permalinksToUrls()` on writer fields.

### Code Style

AVOID setting preliminary variables in files. Prefer inline expressions instead UNLESS you'd need to repeat a statement.

GOOD: inline when used once:

```php
<?php snippet('image', ['image' => $block->image()->toFile()]) ?>
```

GOOD: assign in condition when reused:

```php
<?php if ($isLtr = $block->order()->value() === 'ltr') : ?>
	<?php snippet('image', ['image' => $block->image()->toFile()]) ?>
<?php endif ?>

<?php if (!$isLtr) : ?>
	<?php snippet('image', ['image' => $block->image()->toFile()]) ?>
<?php endif ?>
```
