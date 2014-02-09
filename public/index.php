<?php
require_once '../vendor/autoload.php';

use Noodlehaus\Config;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Klein\Klein;
// load the config
$conf = Config::load(__DIR__ . '/../app/config.php');

//idiorm configuration
ORM::configure('mysql:host=' . $conf->get('database.server') . ';dbname=' . $conf->get('database.db'));
ORM::configure('username', $conf->get('database.user'));
ORM::configure('password', $conf->get('database.password'));
ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

$klein = new Klein();

$klein->respond(function ($request, $response, $service, $app) use ($klein) {
    $app->register('twig', function() {
        $loader = new Twig_Loader_Filesystem('../templates/');
        $twig = new Twig_Environment($loader, array(
            'cache' => '../cache/',
            'auto_reload' => true
        ));
        return $twig;
    });

    $app->register('log', function(){
        $log = new Logger('name');
        $log->pushHandler(new StreamHandler(__DIR__ . '/../logs/', Logger::WARNING));
        return $log;
    });
});

$klein->respond('GET', '/', function ($request, $response, $service, $app) {
    echo $app->twig->render('index.twig', array());
});

$klein->dispatch();
