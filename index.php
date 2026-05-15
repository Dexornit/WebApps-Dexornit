<?php

/**
 * Laravel Shared Hosting Entry Point
 *
 * This file acts as a transparent redirect to the actual
 * Laravel public/index.php entry point.
 *
 * This is needed when the hosting server's document root
 * points to the project root rather than the /public folder.
 */

// Change the working directory to the public folder
chdir(__DIR__ . '/public');

// Include the actual Laravel entry point
require __DIR__ . '/public/index.php';
