# Petshop


### TEST DATA
Admin: test@test.com // Password: test1234
User: random email (get from userListing) // Password: test1234


### Clone project

```bash
git clone git@github.com:awallgr/petshop.git
```

### Run install

Follow the instructions. Select y (YES) or n (NO).

```bash
cd petshop

chmod +x install.sh

./install.sh
```

### Change settings in .env

```bash
vim src/.env
```

```env
DB_PASSWORD="YOUR_PASSWORD"
TEAMS_WEBHOOK_URL="WEBHOOK_URL"
```

### Run docker-compose

To start the app and database.

```bash
docker-compose --env-file=src/.env --profile app up
```

### Migrate and seed the database

In another terminal while the app is running.

```bash
cd petshop 

docker-compose --env-file=src/.env run --rm artisan_bu migrate:fresh --seed
```

### Open http://localhost in browser

Everything is done!


### Run Tests

Unit tests

```bash
cd petshop

docker-compose run --rm php_bu ./vendor/bin/phpunit --testdox
```

Larastan

```bash
cd petshop

docker-compose run --rm php_bu ./vendor/bin/phpstan analyse --memory-limit=2G
```

Codesniffer

```bash
cd petshop

docker-compose run --rm php_bu ./vendor/bin/phpcs
```

PHP Insight

```bash
cd petshop

docker-compose run --rm php_bu ./vendor/bin/phpinsights
```
