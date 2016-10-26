# Groupable
Groupable is a Laravel package for grouping content.

It takes its inspiration from the Drupal community - think of it as a simplified Organic Groups for Laravel.

## Introduction
The idea of Groupable is to turn any Eloquent model into a group which can be 'joined' by users and act as a container for 'content'.

Groupable works by adding traits to your models.

### The Traits
Groupable provides three traits which can be added to your models:

- The `CanJoinGroups` trait allows users to be added to and removed from teams and should be added to your User model.
- The `IsGroup` trait is added to a model which you would like to be treated as a group.
- The `IsGroupable` trait is added to models which you would like to be treated as group content.

### Helper methods
Groupable includes a class called `Groupable` which offers some simple helper methods.

### Database Structure
Groupable requires 3 tables to be added to your schema and includes database migrations out of the box. There is no need to publish these migrations to your project as the accompanying service provider points to the migrations folder within your Composer vendor folder.

## Installation
Installation is via composer:

```
composer require etsh\groupable
```

Then be sure to include the `GroupableServiceProvider` in you a `app` config file:

```
Etsh\Groupable\GroupableServiceProvider::class
```

Finally, run the migrations:

```
art migrate
```
