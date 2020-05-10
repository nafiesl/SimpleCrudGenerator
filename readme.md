# Laravel Simple CRUD Generator
[![Build Status](https://travis-ci.org/nafiesl/SimpleCrudGenerator.svg?branch=master)](https://travis-ci.org/nafiesl/SimpleCrudGenerator)
[![Total Downloads](https://poser.pugx.org/luthfi/simple-crud-generator/downloads)](https://packagist.org/packages/luthfi/simple-crud-generator)

Need faster TDD in Laravel project? This is a simple CRUD generator complete with automated testing suite.

<br>

## About this package

This package contains artisan `make:crud` commands to create a simple CRUD feature with test classes on our Laravel 5.5 (and later) application. This package is fairly simple, to **boost test-driven development** method on our laravel application.

With this package installed on local environment, we can use (e.g.) `php artisan make:crud Vehicle` command to generate some files :

- `App\Vehicle.php` eloquent model
- `xxx_create_vehicles_table.php` migration file
- `VehicleController.php`
- `index.blade.php` and `forms.blade.php` view file in `resources/views/vehicles` directory
- `resources/lang/vehicle.php` lang file
- `VehicleFactory.php` model factory file
- `VehiclePolicy.php` model policy file in `app/Policies` directory
- `ManageVehiclesTest.php` feature test class in `tests/Feature` directory
- `VehicleTest.php` unit test class in `tests/Unit/Models` directory
- `VehiclePolicyTest.php` unit test class in `tests/Unit/Policies` directory

It will update some file :

- Update `routes/web.php` to add `vehicles` resource route
- Update `app/providers/AuthServiceProvider.php` to add Vehicle model Policy class in `$policies` property

It will also create this file **if it not exists** :

- `resources/lang/app.php` lang file if it not exists
- `tests/BrowserKitTest.php` base Feature TestCase class if it not exists

<br>

#### Main purpose

The main purpose of this package is for **faster Test-driven Development**, it generates model CRUD scaffolds complete **with Testing Classes** which will use [Laravel Browserkit Testing](https://github.com/laravel/browser-kit-testing) package and [PHPUnit](https://packagist.org/packages/phpunit/phpunit).

<br>

## How to install

#### For Laravel 5.6 or later

```bash
# Get the package
$ composer require luthfi/simple-crud-generator --dev
```

> The package will **auto-discovered** and ready to go.

#### For Laravel 5.5

To use this package on laravel 5.5, we need to **add the package** (with browserkit) within `require-dev` in `composer.json` file, like so :


```bash
# Install required package for laravel/browser-kit-testing
$ composer require symfony/css-selector:^3.0

# Get the package
$ composer require luthfi/simple-crud-generator 1.2.* --dev
```

> The package will **auto-discovered**.

<br>

## How to use
Just type in terminal `$ php artisan make:crud ModelName` command, it will create simple Laravel CRUD files of given **model name** completed with tests.

For example we want to create CRUD for '**App\Vehicle**' model.

```bash
$ php artisan make:crud-simple Vehicle

Vehicle resource route generated on routes/web.php.
Vehicle model generated.
Vehicle table migration generated.
VehicleController generated.
Vehicle index view file generated.
Vehicle form view file generated.
lang/app.php generated.
vehicle lang files generated.
Vehicle model factory generated.
Vehicle model policy generated.
AuthServiceProvider class has been updated.
BrowserKitTest generated.
ManageVehiclesTest generated.
VehicleTest (model) generated.
VehiclePolicyTest (model policy) generated.
CRUD files generated successfully!
```

Make sure we have **set database credential** on `.env` file, then :

```bash
$ php artisan migrate
$ php artisan serve
```

Then visit our application url: `http://localhost:8000/vehicles`.

<br>

#### Usage on Fresh Install Laravel 7.x

In this example, we are using the [laravel installer](https://packagist.org/packages/laravel/installer) package to install new laravel project.

```bash
# This is example commands for Ubuntu users.
$ laravel new --auth project-directory
$ cd project-directory
$ vim .env # Edit your .env file to update database configuration

# Install the package
$ composer require luthfi/simple-crud-generator --dev

$ php artisan make:crud Vehicle # Model name in singular
# I really suggest "git commit" your project right before run make:crud command

$ php artisan migrate
$ php artisan serve
# Visit your route http://127.0.0.1:8000
# Register as new user
# Visit your route http://127.0.0.1:8000/vehicles
```

#### Available Commands

```bash
# Create Full CRUD feature with tests
$ php artisan make:crud ModelName

# Create Full CRUD feature with tests and Bootstrap 3 views
$ php artisan make:crud ModelName --bs3

# Create Simple CRUD feature with tests
$ php artisan make:crud-simple ModelName

# Create Simple CRUD feature with tests and Bootstrap 3 views
$ php artisan make:crud-simple ModelName --bs3

# Create API CRUD feature with tests
$ php artisan make:crud-api ModelName
```

<br>

#### Model Attribute/column

The Model and table will **only have 2 pre-definded** attributes or columns : **name** and **description** on each generated model and database table. You can continue working on other column on the table.

<br>

#### Bootstrap 4 Views

The generated view files **use Bootstrap 4 by default** (for Laravel 5.6 and later).

<br>

#### Bootstrap 3 Views

We can also generates views that use Bootstrap 3 with `--bs3` command option, eg for Laravel version 5.5.

<br>

#### For API

If we want to generate API Controller with feature tests, we use following command :

```bash
$ php artisan make:crud-api Vehicle
```

By default, we use Laravel **Token Based Authentication**, so we need to update our user model.

1. Add `api_token` **column** on our **users_table_migration**.
2. Add `api_token` as **fillable** property on **User model**.
3. Add `api_token` **field** on our **UserFactory**.

<br>

#### API Usage

The generated API is a REST API, using GET and POST verbs, with a URI of `/api/modelname`.

Example code for calling the generated API, using Guzzle:

    // Read data a specific Vehicle record...
    $uri = 'http://your-domain.com/api/vehicles/'.$vehicleID;
    $headers = ['Authorization' => 'Bearer '.$apiToken];

    $client = new \GuzzleHttp\Client();
    $res = $client->request('GET', $uri, ['headers' => $headers]);
<br>

    // Create a new Vehicle record...
    $uri = 'http://your-domain.com/api/vehicles';
    $headers = ['Authorization' => 'Bearer '.$apiToken];
    $payload = json_encode([
        'name' => 'Vehicle Name 1',
        'description' => 'Vehicle Description 1',
    ]);

    $client = new \GuzzleHttp\Client();
    $res = $client->request('POST', $uri, ['body' => $payload, 'headers' => $headers]);

The generated functional tests will give you examples of how to adapt this code for other call types.

<br>

## Config file

You can configure your own by publishing the config file:

```bash
$ php artisan vendor:publish --provider="Luthfi\CrudGenerator\ServiceProvider"
```

That will generate `config/simple-crud.php` file.

By default, this package have some configuration:

```php
<?php

return [
    // The master view layout that generated views will extends
    'default_layout_view' => 'layouts.app',

    // The base test case class path for generated testing classes
    'base_test_path' => 'tests/BrowserKitTest.php',

    // The base test class full name
    'base_test_class' => 'Tests\BrowserKitTest',
];
```

<br>

## Attention

- The package will creates the **Model** class file, the command will stop if the **Model already exists**.
- **You need** a `resources/views/layouts/app.blade.php` view file, simply create one with `php artisan make:auth` command. You can change this configuration via the `config/simple-crud.php` file.

<br>

## Screenshots

Visit your application in new resource route : `http://127.0.0.1:8000/vehicles`

![Generated CRUD page by Simple CRUD Generator](screenshots/simple-crud-generator-01.jpg)

<br>

## Generated testing suite

Next, let us try the generated testing suite. To use the generated testing classes, we can set the database environment using ***in-memory* database SQLite**. Open `phpunit.xml`. Add two lines below on the `env` :

```xml
<phpunit>
    <!-- ..... -->
    <php>
        <!-- ..... -->
        <env name="DB_CONNECTION" value="sqlite"/>
        <env name="DB_DATABASE" value=":memory:"/>
    </php>
</phpunit>
```

Then run PHPUnit

```bash
$ vendor/bin/phpunit
```

All tests should be passed.

![Generated Testing Suite on Simple CRUD Generator](screenshots/simple-crud-generator-02.jpg)

<br>

## Issue/Proposal

If you find any issue, or want to propose some idea to help this package better, please [create an issue](https://github.com/nafiesl/SimpleCrudGenerator/issues) in this github repo.

<br>

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).
