<?php
/**
 * require.php
 *
 * PHP version 5
 *
 * @category OsApp
 * @package  Loader
 * @author   Osvaldo Garcia <aoga88@gmail.com>
 * @license  This cannot be reproduced without explicit permission
 * @link     http://santander.os-cloud.net 
 */

/**
 * The osrest loader
 *
 * @param string $class The class
 *
 * @return void
 */
function osrestLoader($class)
{
    $parts = explode("\\", $class);

    $module    = strtolower($parts[0]);
    $package   = strtolower($parts[1]);
    $className = $parts[2];

    $filename = '../lib/' . $module . '/' . $package . '/' . $className . '.php';

    if (file_exists($filename)) {
        include_once $filename;
    } else {
        throw new Exception("No class named " . $class, 1);
    }
}

spl_autoload_register('osrestLoader');

/**
 * Gets a config key
 *
 * @param string $key null
 *
 * @return array
 */
function osrestConfig($key = null)
{
    $config = include '../app/config.php';

    if ($key == null) {
        return null;
    }

    if (isset($config[$key])) {
        return $config[$key];
    }

    throw new Exception("Config {$key} not set", 1);
}