# UserModule

Manage Users and Sessions

## Configuration

The following keys are needed in your config

```php
<?php

// Modules Database
define("AUTH_MODULE_DB", "path/to/database");

// Session Duration in Minutes
define("AUTH_MODULE_SESSION_DURATION", 120);
```

## Setup

Require `setup.php` in your main `setup.php`.
Also require `config.php` in your main `config.php`.
