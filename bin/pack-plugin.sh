#!/bin/bash
NAME="yoti-wordpress-edge.zip"
BIN_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
BASE_DIR="$BIN_DIR/.."

cd ./yoti/ && composer install --no-dev

echo "Packing plugin ..."

cd $BASE_DIR
cp "./LICENSE" "./yoti"
zip -r "$NAME" "./yoti/"
rm "./yoti/LICENSE"
cd -

echo "Plugin packed. File $NAME created."
echo ""
