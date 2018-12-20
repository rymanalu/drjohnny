# Dr. Johnny

Experimental chat bot to diagnose a disease by symptoms.

## Installation

1. Clone this repo
2. `composer install`
3. `cp .env.example .env`
4. `php artisan key:generate`
5. Update your `.env`
6. `php artisan migrate`
7. `php artisan passport:install` 
8. `php artisan db:seed`
9. `php artisan serve`
10. `ngrok http <LARAVEL_SERVE_PORT>`
11. Setup your webhook using ngrok URL
