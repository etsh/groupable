# Groupable
Groupable is a Laravel package for grouping content.
It takes its inspiration from the Drupal community. Think of it as a simplified Organic Groups for Laravel.

## Introduction
The idea of Groupable is to turn any Eloquent model into a group which can have content added to it and can be joined by users.
Groupable works by including traits within your models.

### The Traits
The CanJoinsGroups trait allows users to be added to and removed from teams and should be added to your User model(s).
The IsGroup trait is added to a model which you would like to be treated as a group.
The IsGroupable trait is added to models which you would like to be treated as group content.
