<p align="center"><img src="https://res.cloudinary.com/dtfbvvkyp/image/upload/v1566331377/laravel-logolockup-cmyk-red.svg" width="400"></p>


## About This project
 Confirm DNS Owner
 
0) Docker as development enviroment 
1) Laravel 6 as Back-end  
2) Vue.js 2 as Front-end 
3) Use CSRF Token Insted of Bearer Toekn  
4) Use Trait Response Handler   
5) Write tiny UnitTest


How to Use?
========
0) Install docker and docker compose
1) Clone or Download 
```bash
$ git clone https://github.com/devlifeX/arvan.git
```
3) Use this .env file
```plain
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:AJBpZl9yQrB9QLgDakOvFkvoPddn48sc/MzN74cHp80=
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=arvan
DB_USERNAME=root
DB_PASSWORD=root
```

```bash
$ cd arvan
$ composer install
$ docker-compose up -d
$ cp .env.example .env
$ vim .env # update with below content
$ php artisan key:generate
$ php artisan migrate
```
### UnitTest

```bash
$ vendor/bin/phpunit
```
