<h1 align="center">Taskapp</h1>

<p align="center">
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Taskapp

Taskapp is a minimalist task management software API
- User authentication system
- Task management system

## Setup guide

Here's how to set this up on your development server:
Clone this repository and cd into the root directory, then run the following commands -

1. installing composer dependencies `composer install`
2. Copy `.env.example` to `.env`
3. Run `php artisan key:generate` to set your .env's `APP_KEY` value
4. Configure the database within the `.env` file by setting appropriate values for the `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` environment variables.
5. running db migration `php artisan migrate`

## API documentation
You can test the APIs in Postman. See documentation via the link below:
[Taskapp's Postman public documentation](https://documenter.getpostman.com/view/7490481/2sA3kYjg2b)

## Test
First, create a `.env.testing` file in your root directory, and update with the following parameters:

```bash
APP_NAME=Taskapp
APP_ENV=testing
APP_KEY=base64:1LA2Qo3EKPeB/P/4Y7W/Xkk5s8Sab5hotCgzjESstJo=
APP_DEBUG=true
APP_URL=http://taskapp.test

VALID_PASSWORD_SAMPLE=Fg7U@%zP/K1c
```

Then run: `php artisan test`

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
