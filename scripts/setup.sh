#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")/.."

main="$(dirname "$(git rev-parse --path-format=absolute --git-common-dir)")"

composer install --no-interaction
pnpm install --frozen-lockfile

dirty="$(git status --porcelain --untracked-files=no)"
pnpm build
[[ -n "$dirty" ]] || git checkout -- .

if [[ "$main" != "$(pwd -P)" ]]; then
	[[ -e .env || ! -f "$main/.env" ]] || ln -s "$main/.env" .env
	cp -Rn "$main/data/storage/." data/storage/
elif [[ ! -f .env ]]; then
	cp .env.example .env
fi

if [[ -f .env ]] && ! grep -q '^CONTENT_SALT=.' .env; then
	salt="CONTENT_SALT=$(openssl rand -hex 32)"
	env="$(sed "s/^CONTENT_SALT=\$/$salt/" .env)"
	grep -q '^CONTENT_SALT=' .env || env+=$'\n'"$salt"
	# write through the worktree symlink, sed -i would replace it with a copy
	printf '%s\n' "$env" > .env
fi
