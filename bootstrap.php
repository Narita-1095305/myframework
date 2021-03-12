<?php
    require 'mvc/Loader.php';

    $loader->regDirectory(dirname(__FILE__).'/mvc');

    $loader->regDirectory(dirname(__FILE__).'/models');

    $loader->register();

?>