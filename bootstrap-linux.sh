#!/bin/bash

echo "ENSURE THERE WILL BE NO VOLUMES"
docker compose --profile all down -v

source .env

USERID=`id -u $USER`;
GROUPID=`id -g $USER`;

sed -i "s/GROUPID=.*/GROUPID=$GROUPID/" ./.env
sed -i "s/USERID=.*/USERID=$USERID/" ./.env

echo "PULL IMAGES"
docker compose pull

echo "BUILD IMAGES"
docker compose build

docker compose up database -d

echo "BOOTSTRAP APACHE IN DOCKER COMPOSE VOLUMES"
docker compose run --entrypoint="/usr/local/bin/bootstrap.sh" apache

docker compose stop database
docker compose down

sed -i "s/GROUPID=.*/GROUPID=1000/" ./.env
sed -i "s/USERID=.*/USERID=1000/" ./.env

echo "all donte, happy developing"
echo "to start run in project root dir:"
echo "docker compose up"
