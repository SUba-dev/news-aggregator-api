# news-aggregator-api

It is a News Aggregator API project in Laravel

## Description

The Project is to build a RESTful API for a news aggregator service that pulls articles from various sources and provides endpoints for a frontend application to consume. 
<hr>

### Application Details

- LAMP Server:
- PHP 8.1
- LARAVEL 10
- DB MySQL

- Developer Name - Subashini Thanikaikumaran

<hr>


### Project Setup

<b>1.</b> Download the project.

<b>2.</b> Need to Update the all dependencies.

```command
composer upgrade
```

<b>3.</b> Folder permission if Ubundu OS.

```command
sudo chmod -R 777 public/* storage/* bootstrap/*
```

<b>4.</b> Need to edit the .env file. Following details needs to be update.

#### DB
- `DB_HOST=` 
- `DB_PORT=` 
- `DB_DATABASE=` 
- `DB_USERNAME=` 
- `DB_PASSWORD=` 

#### News APIs
- `NEWS_API_KEY=` 
- `GUARDIAN_API_KEY=` 
- `NEWYORKTIMES_API_KEY=` 

<b>5.</b> After edit the .env file, we need to clear the cache.


```command
php artisan key:generate
php artisan cache:clear
php artisan route:clear
php artisan config:clear
php artisan view:clear
php artisan config:cache
```
<hr>

#### Database

Start to create the table and migrate the data:

```command
php artisan migrate:fresh --seed
```


<hr>

#### Run Application

Finally we can run the application by following command

```command
php artisan serve
```

Sample URL 

```html
http://127.0.0.1:8000/
```

<hr>

## Features

- Fetch news from multiple sources (News API Org, The Gurdian and New York Times).

- RESTful API for auth and retrieving article and preferences user.

- Filter article based on user preferences.

- API documentation `/public/api-docs/` (Including Postman collection and Swagger json and yaml files)

<hr>

## Schedule and Queues

- The project scheduled commands to regularly fetch and update articles from the chosen news APIs.

for run command to run schedule:

```command
php artisan schedule:work
```

for run command to dispatch queues:

```command
php artisan app:fetch-news-command
```

<hr>

## Unit Test

- The Unit and feature tests for the API endpoints and core functionalities.
```command
php artisan test --filter=AuthServiceTest
```

```command
php artisan test --filter=NewsFetchServiceTest
```

<hr>

## Setup (Docker)
I have setup the Laravel sail. So you can run following command to start the docker env.

```command
./vendor/bin/sail up
./vendor/bin/sail artisan migrate:fresh --seed
```
<hr>