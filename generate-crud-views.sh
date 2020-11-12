#!/bin/sh

files="$(find ./api/src/Entity -type f \( -iname "*.php" ! -iname "User.php" \) -exec basename -s '.php' {} +)"

for entry in $files
do
  docker-compose exec php bin/console make:crud $entry -q
done
