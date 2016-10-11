<?php

    /**
 * Функція __autoload для автоматичного підключення класів
 */
function __autoload($class_name)
{
    /**
     * Масив папок, в яких можуть знаходитися необхідні класи
     */
    $array_paths = array(
        '/models/',
        '/components/',
        '/controllers/',
    );

    foreach ($array_paths as $path) {

        $path = ROOT . $path . $class_name . '.php';

        if (is_file($path)) {
            include_once $path;
        }
    }
}
