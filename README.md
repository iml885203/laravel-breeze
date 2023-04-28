<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

## Introduction
Simple strong password rules in practice with Laravel Breeze

## Getting Started

To use this application, you need to have Composer and Docker installed on your machine. Once you have installed these dependencies, follow these steps:

```
composer require laravel/sail --dev
cp .env.example .env
sail up
sail php artisan key:generate
sail php artisan migrate
sail php artisan db:seed
sail npm install
sail npm run dev
```

Now you can access the application at http://localhost.

## Test User
To log in to the application, you can use the following test user:

Email: `test@example.com`
Password: `password`