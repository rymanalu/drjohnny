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

## Setup Facebook Messenger Bot

1. Create a new app at [Facebook Developers](https://developers.facebook.com)
2. Add `Messenger` to your app
3. Create a new page
4. Set Facebook Messenger configuration in `.env`
5. Setup your webhook using ngrok URL (check `messages` and `messanging_postbacks`)
6. Subscribe the webhook to your page
7. `php artisan botman:facebook:AddStartButton`

## Setup Telegram Bot

1. Register a new bot [here](https://core.telegram.org/bots#3-how-do-i-create-a-bot)
2. Grab the token and save it in `.env`
3. `php artisan botman:telegram:register`
