# php-hooks

A small PHP hook system for extending an application at user-defined events.

The core idea is simple:
- your application declares events
- hook classes are organized into folders by event name
- `Hooker` scans the hook folder, binds matching hook classes, and executes them when an event is triggered

## What this project does
`php-hooks` lets you add plug-in style behavior to an existing PHP application without hard-coding every action directly into the main script.

This is useful when you want to:
- keep your main application logic simpler
- attach custom behavior to specific events
- extend a system without editing the same core file over and over

## How it works
The library uses a folder structure where each event has its own directory.

Example:

```text
hooks/
  userLogInAttempt/
    SendSalesEmail.php
    LogAttempt.php
```

When `Hooker::init()` runs, it scans the hooks folder and binds each hook file to the event folder it lives under.

So in the example above:
- files inside `hooks/userLogInAttempt/` are attached to the `userLogInAttempt` event

Later, when this runs:

```php
Hooker::event('userLogInAttempt', $data);
```

all hooks bound to that event are executed.

## Basic usage
### 1. Include the core file

```php
require 'Hooker.php';
```

### 2. Point Hooker at your hooks directory

```php
Hooker::setHookFolderPath(__DIR__ . '/hooks');
```

### 3. Initialize hooks

```php
Hooker::init();
```

### 4. Trigger an event

```php
Hooker::event('userLogInAttempt', array('username' => 'ray'));
```

## Example hook class
A hook class must implement the `Hook` interface and define an `execute()` method.

```php
<?php

class LogAttempt implements Hook {
    public static $priority = 10;

    public function execute($use) {
        echo 'User login attempt detected';
    }
}
```

## Priorities
If a hook class defines a static `$priority` property, hooks are executed in ascending priority order.

Example:
- priority `10` runs before priority `50`
- if no priority is defined, the default is `50`

## Included files
- `Hooker.php` — core hook system
- `masterApp.php` — small example/demo entry point

## Notes
This project has the feel of an older lightweight PHP utility library.
A good next modernization pass could include:
- examples folder structure in the repo
- tests
- composer support
- a license
- clearer naming around hook discovery and configuration
