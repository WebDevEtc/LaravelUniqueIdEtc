# WebDevEtc Laravel Unique Id
## Easy to install package to create unique IDs in your Eloquent models. Just import this with composer then use the included trait file and it will auto gen a unique id

## Introduction

This is [WebDevEtc's](https://webdevetc.com/) Laravel Unique Id package. 

It is only really one main file (and an exception, plus a test file). It isn't complicated. But this might be useful if you want to use some form of an ID on public facing things, but still use an auto incrementing integer as your actual primary key.

When creating a new Eloquent item, it will run a while loop (until a max number of attempts, then it will throw an exception) trying to create a unique ID then checking if it exists in the database. If not, it will set that as the unique id.

I've used this in a few projects, so I thought I'd put this up online, maybe it can help others. Please give it a star on Github if you find this useful :)

The tests assume you have Laravel  installed.

## Usage (super simple!)

Require it in composer:

    composer require webdevetc/laraveluniqueidetc

Then just add the following to your Eloquent models:

    use \WebDevEtc\LaravelUniqueIdEtc;

to use the included trait file.

You must also create a db migration to add the unique id field. By default this is `unique_id`.

For example, to add a unique_id field to your User model (which uses the `users` table):

    php artisan make:migration --table='users'


And within the migration's up method, add this:

    $table->string("unique_id")->unique();


And in the down() method:

    $table->dropColumn("unique_id");


## How it works

It uses the `static::creating()` method.

This is tested with `new Model([])` and `Model::create([])`.

It assumes that you don't need to recreate it at a later stage - it will only create the unique id once.

## Configuration options

There are some options, configurable by adding some methods to your model.

Please see the UniqueId trait for details. They are at the top of the file.

They are methods as opposed to variables so that you have the option to implement more logic if required. They only get called once (when creating an object and saving it for the first time)


## Changelog History
- 1.0.1                 - slight changes
- 1.0                   - Initial release


## Issues, support, bug reports, security issues

Please contact me on the contact from on [WebDev Etc](https://webdevetc.com/) or on [twitter](https://twitter.com/web_dev_etc/) and I'll get back to you asap.




