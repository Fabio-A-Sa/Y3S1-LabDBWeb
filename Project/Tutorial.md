# LBAW's framework

## Introduction

This README describes how to setup the development environment for LBAW. These instructions address the development with a local environment, i.e. on the machine (that can be a VM) **without using a Docker container for PHP or Laravel**.
Containers are used for PostgreSQL and pgAdmin, though.

- [LBAW's framework](#lbaws-framework)
  - [Introduction](#introduction)
  - [Installing the Software Dependencies](#installing-the-software-dependencies)
  - [Setting up the Development repository](#setting-up-the-development-repository)
  - [Installing local PHP dependencies](#installing-local-php-dependencies)
  - [Working with PostgreSQL](#working-with-postgresql)
  - [Developing the project](#developing-the-project)
  - [Laravel code structure](#laravel-code-structure)
    - [1) Routes](#1-routes)
    - [2) Controllers](#2-controllers)
    - [3) Database and Models](#3-database-and-models)
    - [4) Policies](#4-policies)
    - [5) Views](#5-views)
    - [6) CSS](#6-css)
    - [7) JavaScript](#7-javascript)
    - [8) Configuration](#8-configuration)
  - [Publishing your image](#publishing-your-image)


## Installing the Software Dependencies

To prepare you computer for development you need to install PHP >=v8.1 and Composer >=v2.

We recommend using an __Ubuntu__ distribution that ships with these versions (e.g Ubuntu 22.04 or newer). You may install the required software with:

```bash
sudo apt update
sudo apt install git composer php8.1 php8.1-mbstring php8.1-xml php8.1-pgsql php8.1-curl
```

On MacOS, you can install them using [Homebrew](https://brew.sh/) and:
```bash
brew install php@8.1 composer
```

If you use [Windows WSL](https://learn.microsoft.com/en-us/windows/wsl/install), please ensure you are also using Ubuntu 22.04 inside. Previous versions do not provide the requirements needed for this template, and then follow the Ubuntu instructions above.


## Setting up the Development repository

You should have your own repository and a copy of the demo repository in the same folder in your machine.
Then, copy the contents of the demo repository to your own.

```bash
# Clone your group repository (replacing lbawYYXX, eg. lbaw2211), if not yet available locally
# Notice that you need to substitute XX by your group's number and YY or YYYY as needed.
git clone https://git.fe.up.pt/lbaw/lbawYYYY/lbawYYXX.git

# clone the LBAW's project skeleton
git clone https://git.fe.up.pt/lbaw/template-laravel.git

# remove the Git folder from the demo folder
rm -rf template-laravel/.git
# preserve existing README.md
mv template-laravel/README.md template-laravel/README_lbaw.md

# go to your repository
cd lbawYYXX

# make sure you are using the main branch
git checkout main

# copy all the demo files
cp -r ../template-laravel/. .

# add the new files to your repository
git add .
git commit -m "Base Laravel structure"
git push origin main
```

At this point you should have the project skeleton in your local machine and be ready to start working on it.
You may remove the __template-laravel__ demo directory, as it is not needed anymore.


## Installing local PHP dependencies

After the steps above you will have updated your repository with the required Laravel structure from this repository.
Afterwards, the command bellow will install all local dependencies, required for development.

```bash
composer install
```

If this fails, ensure you're using version 2 or above of Composer. If there are errors regarding missing extensions, make sure you uncomment them in your [php.ini file](https://www.php.net/manual/en/configuration.file.php).

## Working with PostgreSQL

We've created a _docker compose_ file that sets up __PostgreSQL__ and __pgAdmin4__ to run as local Docker containers.

From the project root issue the following command:

```bash
docker compose up
```

Navigate on your browser to http://localhost:4321 to access pgAdmin4 and manage your database. Depending on your installation setup, you might need to use the IP address from the virtual machine providing docker instead of localhost. Please refer to your installation documentation.
Use the following credentials to login:

    Email: postgres@lbaw.com
    Password: pg!password

On the first usage you will need to add the connection to the database using the following attributes:

    hostname: postgres
    username: postgres
    password: pg!password

Hostname is _postgres_ instead of _localhost_ since _Docker Compose_ creates an internal DNS entry to facilitate the connection between linked containers.


## Developing the project

You're all set up to start developing the project.
In the provided skeleton you will already find a basic todo list App, which you will modify to start implementing your own project.

To start the development server, from the project's root run:

```bash
# Seed database from the seed.sql file.
# Needed on first run and everytime the database script changes.
php artisan db:seed

# Start the development server
php artisan serve
```

Access http://localhost:8000 to access the app. Username is `admin@example.com`, and password `1234`. These credentials are copied to the database on the first instruction above.

To stop the server just hit Ctrl-C.

## Laravel code structure

Before you start, you should make yourself familiar with [Laravel's documentation](https://laravel.com/docs/8.x).

In Laravel, a typical web request will touch the following concepts and files.

### 1) Routes

The web page is processed by *Laravel*'s [routing](https://laravel.com/docs/8.x/routing) mechanism.
By default, routes are defined inside *routes/web.php*. A typical *route* looks like this:

```php
Route::get('posts/{id}', 'PostController@show');
```

This route receives a parameter *id* that is passed on to the *show* method of a controller called *PostController*.

### 2) Controllers

[Controllers](https://laravel.com/docs/8.x/controllers) group related request handling logic into a single class.
Controllers are normally defined in the __app/Http/Controllers__ folder.

```php
class PostController extends Controller
{
    public function show($id)
    {
        $card = Card::find($id);
        $this->authorize('show', $card);
        return view('pages.card', ['card' => $card]);
    }
}
```

This particular controller contains a *show* method that receives an *id* from a route.
The method searches for a card in the database, checks if the user as permission to view the card, and then returns a view.

### 3) Database and Models

To access the database, we will use the query builder capabilities of [Eloquent](https://laravel.com/docs/8.x/eloquent) but the initial database seeding will still be done using raw SQL (the script that creates the tables can be found in __resources/sql/seed.sql__).

```php
$card = Card::find($id);
```

This line tells *Eloquent* to fetch a card from the database with a certain *id* (the primary key of the table).
The result will be an object of the class *Card* defined in __app/Card.php__.
This class extends the *Model* class and contains information about the relation between the *card* tables and other tables:

```php
/* A card belongs to one user */
public function user() {
    return $this->belongsTo('App\User');
}

/* A card has many items */
public function items() {
    return $this->hasMany('App\Item');
}
```

### 4) Policies

[Policies](https://laravel.com/docs/8.x/authorization#writing-policies) define which actions a user can take.
You can find policies inside the __app/Policies__ folder.
For example, in the __CardPolicy.php__ file, we defined a *show* method that only allows a certain user to view a card if that user is the card owner:

```php
public function show(User $user, Card $card)
{
    return $user->id == $card->user_id;
}
```

In this example policy method, *$user* and *$card* are models that represent their respective tables, *$id* and *$user_id* are columns from those tables that are automatically mapped into those models.

To use this policy, we just have to use the following code inside the *PostController*:

```php
$this->authorize('show', $card);
```

As you can see, there is no need to pass the current *user*.

### 5) Views

A *controller* only needs to return HTML code for it to be sent to the *browser*. However we will be using [Blade](https://laravel.com/docs/8.x/blade) templates to make this task easier:

```php
return view('pages.card', ['card' => $card]);
```

In this example, *pages.card* references a blade template that can be found at __resources/views/pages/card.blade.php__.
The second parameter contains the data we are injecting into the template.

The first line of the template states that it extends another template:

```php
@extends('layouts.app')
```

This second template can be found at __resources/views/layouts/app.blade.php__ and is the basis of all pages in our application. Inside this template, the place where the page template is introduced is identified by the following command:

```php
@yield('content')
```

Besides the __pages__ and __layouts__ template folders, we also have a __partials__ folder where small snippets of HTML code can be saved to be reused in other pages.

### 6) CSS

The easiest way to use CSS is just to edit the CSS file found at __public/css/app.css__. You can have multiple CSS files to better organize your style definitions.

### 7) JavaScript

To add JavaScript into your project, just edit the file found at __public/js/app.js__.

### 8) Configuration

Laravel configurations are acquired from environment variables. They can be available in the environment where the Laravel process is started, or acquired by reading the `.env` file in the root folder of the Laravel project. This file can set environment variables, which set or overwride the variables from the current context. You will likely have to update these variables, mainly the ones configuring the access to the database, starting with `DB_`. *You must manually create a schema that matches your username.*

If you change the configuration, you might need to run the following command to discard a compiled version of the configuration from Laravel's cache:

```bash
php artisan route:clear
php artisan cache:clear
php artisan config:clear
```

## Publishing your image

The following links provide instructions for installing [Docker](https://docs.docker.com/get-docker/) and [Docker Compose](https://docs.docker.com/compose/install/).

Please note that if you are using an ARM CPU, you need to explicitly build an AMD64 Docker image. Docker suppors multi-platform building. Create a multi-platform builder and adjust your `upload_image.sh` file to use it, as described in [this guide](https://docs.docker.com/build/building/multi-platform/).

You should keep your git main branch functional and frequently build and deploy your code as a Docker image. LBAW's production machine will frequently pull all these images and make them available at http://lbawYYXX.lbaw.fe.up.pt/.

**Always ensure your `.env` file is configured with your group's `db.fe.up.pt` credentials before building your docker image, by updating the DB section:**

```bash
DB_CONNECTION=pgsql
DB_HOST=db.fe.up.pt
DB_PORT=5432
DB_SCHEMA=lbawYYXX
DB_DATABASE=lbawYYXX
DB_USERNAME=lbawYYXX
DB_PASSWORD=password
```

This demo repository is available at http://template-laravel.lbaw.fe.up.pt/. To view it make sure you are inside FEUP's network or are using the VPN.

Images must be published to Gitlab's Container Registry, available from the side menu option `Packages & Registries > Container Registry`.

The first thing you need to do is login against this registry with your docker. In your command line use the following command, and authenticate with your instituitional credentials, and inside FEUP's VPN/network:

```bash
docker login git.fe.up.pt:5050
```

Once your Docker is authenticated, configure the `upload_image.sh` script with your image name.
Example configuration:

```bash
IMAGE_NAME=git.fe.up.pt:5050/lbaw/lbawYYYY/lbawYYXX # Replace with your group's image name
```

You can now build and upload the docker image by executing that script from the project root folder:

```bash
./upload_image.sh
```

There should be only one image per group. All team members should be able to update the image at any time, after they login with the Gitlab's registry.

## Testing your image

After building it, you can test locally the image by running:

```bash
docker run -it -p 8000:80 --name=lbawYYXX -e DB_DATABASE="lbawYYXX" -e DB_SCHEMA="lbawYYXX" -e DB_USERNAME="lbawYYXX" -e DB_PASSWORD="PASSWORD" git.fe.up.pt:5050/lbaw/lbawYYYY/lbawYYXX # Replace with your group's image name
```

The above command exposes your application on http://localhost:8000.
The `-e` argument creates environment variables inside the container, used to provide Laravel with the required database configurations.

Your database configuration will be provided as an environment variable to your container on start. You do not need to specify it on your env file. Any specification there will be replaced when the docker image starts.


While running your container, you can use another terminal to run a shell inside the container by executing:

```bash
docker exec -it lbawYYXX bash
```

Inside the container you may, for example, see the content of the Web server logs by executing:

```bash
root@2804d54698c0:/# tail -f /var/log/nginx/error.log    # follow the errors
root@2804d54698c0:/# tail -f /var/log/nginx/access.log   # follow the accesses
```

You can stop the container with `CTRL+C` on the terminal running it, or with `docker stop lbawYYXX` on another terminal.
-- LBAW, 2022
