# Development Rules

## Tailwind CSS v4

### Desktop-first

This project uses **desktop-first** breakpoints (opposite of Tailwind's default mobile-first).

- **No prefix** = default (large screens)
- **With prefix** = applies at that width **and below**

DO NOT use a prefix like `min-xl` to target larger screens.

```html
<div class="p-8 md:p-4">
```

| Viewport | Padding |
|----------|---------|
| > 44rem  | p-8     |
| ≤ 44rem  | p-4     |
  
The config can be found in `src/styles/index.css`.

### !important

If you really need to force `!important`, use the `!` suffix (NOT prefix):

```html
<div class="bg-teal-500 bg-red-500!">
```

### Spacing scale

Tailwind 4 calculates spacing as `n * 0.25rem`. You DO NOT have to follow the known spacing scale. Use `h-128` instead of `h-[32rem]`.

## Tailwind Merge

If you need to conditionally apply classes, ALWAYS refer to one of the following functions:

- `merge()` - outputs `class="..."` attribute with merged classes
- `cls()` - outputs just the merged class string
- `attr()` - like Kirby's built-in, but merges Tailwind classes in the `class` attribute

They support conditional syntax with arrays:

```php
<div <?= merge(['bg-white', 'px-16' => $isWide]) ?>></div>
```

## Alpine.js

Define components with a default export in `src/components/`. These will be automatically registered and can be used in the frontend with `x-data`.
