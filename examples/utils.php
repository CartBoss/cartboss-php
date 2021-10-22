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

function function_get_output($fn)
{
    $args = func_get_args();
    unset($args[0]);
    ob_start();
    call_user_func_array($fn, $args);
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
}

function display($template, $params = array())
{
    extract($params);
    include $template;
}

function render($template, $params = array())
{
    return function_get_output('display', $template, $params);
}