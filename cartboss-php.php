<?php
/**
 * This file is used to load the Composer autoloader if required.
 */

// Define path/existence of Composer autoloader
use CartBoss\Api\CartBoss;

$composerAutoloadFile = __DIR__ . '/vendor/autoload.php';
$composerAutoloadFileExists = (is_file($composerAutoloadFile));

// Can't locate SendGrid\Mail\Mail class?
if (!class_exists(CartBoss::class)) {
    // Suggest to load Composer autoloader of project
    if (!$composerAutoloadFileExists) {
        //  Can't load the Composer autoloader in this project folder
        error_log("Composer autoloader not found. Execute 'composer install' in the project folder first.");
    } else {
        // Load Composer autoloader
        require_once $composerAutoloadFile;

        // If desired class still not existing
        if (!class_exists(CartBoss::class)) {
            // Suggest to review the Composer autoloader settings
            error_log("Error finding SendGrid classes. Please review your autoloading configuration.");
        }
    }
}