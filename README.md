# Groupable
Groupable is a Laravel package for grouping content.

It takes its inspiration from the Drupal community - think of it as a simplified Organic Groups for Laravel.

## Introduction
The idea of Groupable is to turn any Eloquent model into a group which can be 'joined' by users and act as a container for 'content'.

Addtionally, users may be given additional group roles on a group-by-group basis.

Groupable works by adding traits to the models within your application that you wish to adopt this group like behaviour.

### The Traits
Groupable provides three traits which can be added to your models:

- The `IsGroup` trait is added to a model which you would like to be treated as a group.
- The `IsGroupable` trait is added to models which you would like to be treated as group content.
- The `JoinsGroups` trait is added to your User model.

In fact, only the `IsGroup` trait is necessary in order to obtain group funtionality. However, the `IsGroupable` and `JoinsGroups` traits provide useful group related functionality to your user model and groupable content types.

### Helper methods
Groupable includes a class called `Groupable` which offers internal helper methods. You likely won't need to use this class unless you intend to modify the code within this project yourself.

### Database Structure
Groupable requires 3 tables to be added to your schema and includes database migrations out of the box.

There is no need to publish these migrations to your project as the accompanying service provider points to the migrations folder within your Composer vendor folder.

The table structure is as follows:

```
groupables:
    id
    group_id
    group_type
    groupable_id
    groupable_type
    created_at
    updated_at

groupable_roles:
    id
    group_id
    group_type
    user_id
    role
    created_at
    updated_at

groupable_members:
    id
    group_id
    group_type
    user_id
    created_at
    updated_at
```

## Installation
Installation is via composer:

```php
composer require etsh\groupable
```

Then be sure to include the `GroupableServiceProvider` in you a `app` config file:

```php
Etsh\Groupable\GroupableServiceProvider::class
```

Finally, run the migrations:

```php
art migrate
```

## Instructions: Setup

### Creating a Group
Simply `use` the `IsGroup` trait in the model that you wish to become a group:

```php
use Etsh\Groupable\Traits\IsGroup;

class Group extends Model
{
    use IsGroup
```

Then create the properties `$groupable_models` and `$groupable_roles`:

```php
    protected $groupable_models = [
        GroupableContent::class,
    ];

    protected $groupable_roles = [
        'admin',
    ];

    ...
```

`$groupable_models` should be an array containing the fully-qualified class name of the models which should be allowed to be grouped within this group. Groupable will throw an exception if you attempt to add a content type not specified here to the group.

`$groupable_roles` should be an array containing the names of additional roles that you wish members to be grantable to members of this group.

### Creating Groupable content
Only models specified within the `$groupable_models` property on your group model may be added to a given group.

To add additional functionality use the `IsGroupable` trait on the model that represents your groupable content.

```php
use Etsh\Groupable\Traits\IsGroup;

class Group extends Model
{
    use IsGroupable
```

### Allowing users to join groups
It's possible to join users to groups without using the 'CanJoinGroups' trait, however it provides some useful helper functions.

Include it in your user model like so:

```php
use Etsh\Groupable\Traits\CanJoinGroups;

class User extends Authenticatable
{
    use CanJoinGroups;
```

## Instructions: Usage

These instructions assume that you have used the `IsGroupable` trait in your groupable models and the `JoinsTeams` trait on your user model.

### Add and remove group content
Content can be added to a group like this:

```php
$group->addContent($groupable_content);
```

And removed like this:

```php
$group->removeContent($groupable_content);
```

### Retrieve group content
You can retrieve all group content like this:

```php
$group->content();
```

Which returns a Laravel collection containing each content model.

You can also make your content requests more specific by passing an array of required types to the content() method:

```php
$group->content([GroupableContentType1::class, GroupableContentType2::class]);
```

### Join and leave a group
Users can be joined to groups like this:

```php
$group->join($user);
```

And removed like this:

```php
$group->leave($user);
```

### Retrieve group members
You can retrieve all group members like this:

```php
$group->members();
```

Which returns a Laravel collection containing each user model.

<!-- You can also make your member requests more specific by passing an array of required roles to the members() method:

```php
$group->members(['admin', 'editor']);
``` -->

### Checking whether a user is a group member

You can check whether a user is a member of a given group like this:

```php
$user->belongsToGroup($group);
```

### Grant and revoke special group roles
The `join()` method is all that is required to make a user a 'member' of a given group.

You will probably want to grant some users special priveleges within your groups and this can be done in the following ways:

Users can be granted group roles like this:

```php
$group->grant($user, $role);
```

And those roles can be revoked like this:

```php
$group->revoke($user, $role);
```

The available roles can be defined on a group by group basis and should be expressed by adding the required roles to the `$groupable_roles` property on the group model.

### Checking whether a user has a group role
You can check whether a group member has a given group role like this:

```php
$user->hasGroupRole($group, $role);
```

### Seeing which group roles a user has
You can see all roles a user has for a given group like this:

```php
$user->groupRoles($group);
```

### Checking which content types may be added to a group
You can check which content types may be added to a group like this:

```php
$group->types()
```

### Checking which roles are available within a group
You can check which roles are available within a group like this:

```php
$group->roles()
```

## To Do
- Sort out get members by type for a given group.
- Add named groups (rather than having to use the FCCN)
