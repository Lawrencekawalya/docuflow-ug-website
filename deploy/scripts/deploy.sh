#!/usr/bin/env bash

set -Eeuo pipefail

if [[ $# -ne 2 ]]; then
    echo "Usage: deploy.sh <release-archive> <git-sha>" >&2
    exit 64
fi

archive_path="$1"
release_sha="$2"
application_root="/var/www/docuflowug"
releases_root="$application_root/releases"
shared_root="$application_root/shared"
release_path="$releases_root/$release_sha"

if [[ ! "$release_sha" =~ ^[0-9a-f]{40}$ ]]; then
    echo "Refusing invalid release SHA: $release_sha" >&2
    exit 65
fi

if [[ ! -f "$archive_path" ]]; then
    echo "Release archive was not found: $archive_path" >&2
    exit 66
fi

if [[ ! -f "$shared_root/.env" ]]; then
    echo "Production environment file is missing: $shared_root/.env" >&2
    exit 67
fi

mkdir -p "$releases_root" "$shared_root/storage"

if [[ -e "$release_path" ]]; then
    active_release="$(readlink -f "$application_root/current" 2>/dev/null || true)"

    if [[ "$active_release" == "$release_path" ]]; then
        rm -f "$archive_path"
        echo "Release $release_sha is already active"
        exit 0
    fi

    rm -rf -- "$release_path"
fi

mkdir "$release_path"
tar -xzf "$archive_path" -C "$release_path"

ln -s "$shared_root/.env" "$release_path/.env"
ln -s "$shared_root/storage" "$release_path/storage"

cd "$release_path"

composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-progress

php artisan migrate --force
php artisan storage:link
php artisan optimize

rm -f "$application_root/current.next"
ln -s "$release_path" "$application_root/current.next"
mv -Tf "$application_root/current.next" "$application_root/current"

php "$application_root/current/artisan" queue:restart || true
php "$application_root/current/artisan" inertia:stop-ssr --no-interaction || true

rm -f "$archive_path"

mapfile -t old_releases < <(find "$releases_root" -mindepth 1 -maxdepth 1 -type d -printf '%T@ %p\n' | sort -rn | tail -n +6 | cut -d' ' -f2-)

for old_release in "${old_releases[@]}"; do
    rm -rf -- "$old_release"
done

echo "Deployed DocuFlow release $release_sha"
