# Setup Instructions

## Fork or Clone this repository
```
git clone git@github.com:glennraya/muval-laravel-test.git
```

## Install Composer dependencies
```
composer install
```

## Setup the .env file
Copy the `.env.example` file to `.env`

## Generate the App Key
```
php artisan key:generate
```

## Setup database connection
You need to supply the proper database credentials to establish connection.
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=backend_api_test
DB_USERNAME=root
DB_PASSWORD=your-database-password
```

## Configure the Sanctum Stateful Domains
Make sure that you have setup the `SESSION_DOMAIN` and the `SANCTUM_STATEFUL_DOMAINS` in the `.env` file with the proper values:
```
SESSION_DOMAIN=localhost
SANCTUM_STATEFUL_DOMAINS=localhost:5173,127.0.0.1:5173
```
>**IMPORTANT** Since port numbers may vary on different development environments, you may need to change the port number to the actual port number of your Vite dev server.

## Run database migrations
```
php artisan migrate
```

## Start the backend server
```
php artisan serve
```

## Run Tests
I have included several test cases for quick check if the core functionalities are working. You can run the command below:
```
php artisan test
```
>**IMPORTANT:** You may need to create a new directory named `Unit` inside the `tests/` directory to run PestPHP tests.
