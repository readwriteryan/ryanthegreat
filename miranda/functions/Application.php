<?php
use miranda\config\Config;

function format($location)
{
    return Config::get('site', 'base') . $location;
}
function stylsheet($stylesheet)
{
    return Config::get('site', 'base') . Config::get('locations', 'css') . $stylesheet;
}
function link_to($location, $display)
{
    return '<a href="' . Config::get('site', 'base') . $location . '">' . $display . '</a>';
}
?>