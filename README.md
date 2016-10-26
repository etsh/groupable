# Groupable
Groupable is a Laravel package for grouping content.

It takes its inspiration from the Drupal community - think of it as a simplified Organic Groups for Laravel.

## Introduction
The idea of Groupable is to turn any Eloquent model into a group which can be 'joined' by users and act as a container for 'content'.

Addtionally, users may be given group roles on a role-by-role, group-by-group, basis.

Groupable works by adding traits to the models within your application that you wish to have this group like behaviour.

### The Traits
Groupable provides three traits which can be added to your models:

- The `CanJoinGroups` trait is added to your User model.
- The `IsGroup` trait is added to a model which you would like to be treated as a group.
- The `IsGroupable` trait is added to models which you would like to be treated as group content.

### Helper methods
Groupable includes a class called `Groupable` which offers some simple helper methods.

### Database Structure
Groupable requires 3 tables to be added to your schema and includes database migrations out of the box.

There is no need to publish these migrations to your project as the accompanying service provider points to the migrations folder within your Composer vendor folder.

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

## Instructions

### Creating a Group
Simply `use` the `Is group` trait in the model that you wish to become a group.

Then create the properties `$groupable_models` and `$groupable_roles`.

`$groupable_models` should be an array containing the fully-qualified class name of the models which should be allowed to be grouped within this group.

`$groupable_roles` should be an array contining the names of additional roles that you wish members to be grantable to members of this group.

```
use Etsh\Groupable\Traits\IsGroup;

class Group extends Model
{
    use IsGroup

    protected $groupable_models = [
        Department::class,
    ];

    protected $groupable_roles = [
        'admin',
    ];
}
```
