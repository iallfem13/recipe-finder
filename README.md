## Getting started

### Pre-requisites
- docker
- docker-compose

### Run composer to kickstart laravel sail
Inside the project, run:

```bash
docker run --rm \
    --pull=always \
    -v "$(pwd)":/opt \
    -w /opt \
    laravelsail/php82-composer:latest \
    bash -c "composer install"
```

### Run the application
`cp .env.example .env`

`./vendor/bin/sail up -d`

`./vendor/bin/sail artisan key:generate`

`./vendor/bin/sail artisan migrate`

### Seed the database
`./vendor/bin/sail artisan db:seed`

### Kickstart the nuxt frontend
`./vendor/bin/sail npm install --prefix frontend`

### Run the frontend
`./vendor/bin/sail npm run dev --prefix frontend`

### Confirm your application
Visit the frontend http://localhost:3000

### Run tests
`./vendor/bin/phpunit`

### Notes
An database schema can be found [here](db_schema.png)

