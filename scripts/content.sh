#!/usr/bin/env bash
set -Eeuo pipefail

cd "$(dirname "${BASH_SOURCE[0]}")/.."
fail() {
	echo "[content] error: $*" >&2
	exit 1
}

direction="${1:-}"
[[ "$direction" == pull || "$direction" == push ]] || fail "usage: scripts/content.sh pull|push [--delete] [paths...]"
shift

delete=""
paths=()
for arg; do
	case "$arg" in
		--delete) delete=1 ;;
		/* | .. | ../* | */../* | */..) fail "$arg is not inside the content folder" ;;
		*) paths+=("${arg%/}") ;;
	esac
done

url="${CONTENT_SYNC_URL:-$(sed -n 's/^CONTENT_SYNC_URL=//p' .env 2>/dev/null | tr -d "\"'")}"
[[ "$url" == ssh://*/* ]] || fail "set CONTENT_SYNC_URL in .env, e.g. ssh://user@host/~/www/data/storage/content"

rest="${url#ssh://}"
host="${rest%%/*}"
remote_path="/${rest#*/}"
remote_path="${remote_path#/\~/}"
port=""
if [[ "${host##*@}" == *:* ]]; then
	port="${host##*:}"
	host="${host%:*}"
fi
remote="$host:${remote_path%/}"
local=data/storage/content

options=(-az --exclude=.lock --exclude=.gitkeep -e "ssh${port:+ -p $port}")
sources=()
if ((${#paths[@]})); then
	options+=(--relative)
	for path in "${paths[@]}"; do
		if [[ "$direction" == pull ]]; then sources+=("$remote/./$path"); else sources+=("$local/./$path"); fi
	done
elif [[ "$direction" == pull ]]; then
	sources=("$remote/")
else
	sources=("$local/")
fi

mkdir -p "$local"

if [[ "$direction" == pull ]]; then
	rsync "${options[@]}" --delete --stats "${sources[@]}" "$local/"
	exit
fi

[[ -n "$delete" ]] && options+=(--delete)
[[ -t 0 ]] || fail "push needs a person at the terminal to confirm"
changes="$(rsync "${options[@]}" --dry-run --itemize-changes "${sources[@]}" "$remote/" | grep -E '^(\*deleting|<f)' || true)"
[[ -n "$changes" ]] || {
	echo "Nothing to push."
	exit
}
printf '%s\n' "$changes"
read -r -p "Push these changes to ${host##*@}? [y/N] " answer
[[ "$answer" == [yY]* ]] || fail "push cancelled"
rsync "${options[@]}" --stats "${sources[@]}" "$remote/"
