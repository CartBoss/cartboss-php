<?php
function template($file, $args)
{
    // ensure the file exists
    if (!file_exists($file)) {
        return '';
    }

    // Make values in the associative array easier to access by extracting them
    if (is_array($args)) {
        extract($args);
    }

    // buffer the output (including the file is "output")
    ob_start();
    include $file;
    return ob_get_clean();
}