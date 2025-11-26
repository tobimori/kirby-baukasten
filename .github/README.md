![Kirby 4 Baukasten Banner by Johann Schopplich](/.github/banner.png)

# Kirby Baukasten

An opinionated, supercharged version of the Kirby Plainkit used internally at Love & Kindness for our clients' sites, with preconfigured tooling and plugins.

If you're not interested in all of my frontend opinions, you can use [composerkit](https://github.com/getkirby/composerkit/tree/main), which is essentially a simpler version with _only_ Composer and the public & data folder setup.

> [!NOTE]
> While Kirby Baukasten is open source & used in production as base for my own projects, it's not properly versioned, and I'm not offering support for it. Instead, it should serve as a reference or guide for implementing certain best practices in your own starterkit.

## Requirements

- PHP 8.4+ with composer
- Node.js 24+ with pnpm

## Usage

##### Install Composer & Node dependencies

```sh
composer install && pnpm install
```

##### Copy and configure environment configuration file

```sh
cp .env.example .env
```

##### Create images page

```sh
mkdir -p data/storage/content/images && printf 'Title: Images\n\n----\n\nUuid: images\n' > data/storage/content/images/images.de.txt
```

##### Start the dev server.

```sh
pnpm run dev
```

## License

[MIT License](.github/LICENSE) © 2021-2025 [Tobias Möritz](https://github.com/tobimori)

Thanks to [Johann](https://github.com/johannschopplich) for the cute banner gecko!
