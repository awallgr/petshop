#!/bin/bash

store_jwt_keys() {
    echo ""
    echo "### Creating directory src/storage/jwt-keys/"
    mkdir -p src/storage/jwt-keys/
    echo "### Generating private.pem"
    openssl genpkey -algorithm RSA -out src/storage/jwt-keys/private.pem
    echo "### Generating public.pem"
    openssl rsa -pubout -in src/storage/jwt-keys/private.pem -out src/storage/jwt-keys/public.pem
    echo ""
}

skip_store_jwt_keys() {
    echo ""
    echo "### Skipping JWT keys. Please add your own keys into src/storage/jwt-keys/"
    echo ""
}

copy_env_file() {
    cp src/.env.example src/.env
}

skip_copy_env_file() {
    echo ""
    echo "### Skipping .env file. Please create your own .env file"
    echo ""
}

composer_install() {
    echo ""
    echo "### Composer install in main"
    echo ""
    docker-compose run --rm composer_bu install
    echo ""
    echo "### Composer install in package currency exchange"
    echo ""
    docker-compose run --rm composer_bu install --working-dir=packages/andreas/currency-exchange-laravel/
    echo ""
    echo "### Composer install in package notification service"
    echo ""
    docker-compose run --rm composer_bu install --working-dir=packages/andreas/notification-service-laravel/
    echo ""
    echo "### Composer install in package statemachine"
    echo ""
    docker-compose run --rm composer_bu install --working-dir=packages/andreas/statemachine-laravel/
}

skip_composer_install() {
    echo "### Skipping running composer install"
}

npm_install() {
    docker-compose run --rm npm_bu install
}

skip_npm_install() {
    echo "### Skipping running npm install"
}

generate_swagger_file() {
    echo ""
    echo "### Creating directory src/storage/api-docs/"
    mkdir -p src/storage/api-docs/
    echo "### Generating Swagger file"
    docker-compose run --rm artisan_bu l5-swagger:generate
    echo ""
}

skip_generate_swagger_file() {
    echo ""
    echo "### Skipping generating swagger documentation file. Please create your own with php artisan l5-swagger:generate"
    echo ""
}

generate_laravel_key() {
    echo ""
    echo "### Generating laravel key to .env"
    docker-compose run --rm artisan_bu key:generate
    echo ""
}

skip_generate_laravel_key() {
    echo ""
    echo "### Skipping generating laravel key to .env. Please create your own with php artisan key:generate"
    echo ""
}

set_permissions() {
    echo ""
    echo "### Setting permissions"
    sudo chown -R $(whoami):$(whoami) src/
    echo ""
}

skip_set_permissions() {
    echo ""
    echo "### Skipping settings permissions. Please set them with sudo chown -R USER:USER src/"
    echo ""
}



prompt_user() {
    local prompt_message=$1
    local yes_action=$2
    local no_action=$3

    while true; do
        read -p "$prompt_message (y/n): " yn
        case $yn in
            [Yy]* ) eval "$yes_action"; break;;
            [Nn]* ) eval "$no_action"; break;;
            * ) echo "Please answer y or n.";;
        esac
    done
}

echo ""
echo ""
echo ""
echo "Hello!"
echo ""
echo ""
echo ""
prompt_user ">>> Generate JWT keys and put inside src/storage/jwt-keys/?" "store_jwt_keys" "skip_store_jwt_keys"
echo ""
prompt_user ">>> Copy .env.example to .env?" "copy_env_file" "skip_copy_env_file"
echo ""
prompt_user ">>> Run composer install?" "composer_install" "skip_composer_install"
echo ""
prompt_user ">>> Run npm install?" "npm_install" "skip_npm_install"
echo ""
prompt_user ">>> Generate Swagger API documentation?" "generate_swagger_file" "skip_generate_swagger_file"
echo ""
prompt_user ">>> Generate Laravel KEY in .env?" "generate_laravel_key" "skip_laravel_key"
echo ""
prompt_user ">>> Change permission to current user for whole project? (sudo)" "set_permissions" "skip_set_permissions"
echo ""

echo ""
echo ""
echo "Done!"
echo ""
echo ""
