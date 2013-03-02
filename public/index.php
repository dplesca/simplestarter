<?php
require_once '../vendor/autoload.php';

use Toml\Parser;
$toml = Parser::fromFile(__DIR__ . '/../app/config.toml');

//idiorm configuration
ORM::configure('mysql:host=' . $toml['database']['server'] . ';dbname=' . $toml['database']['db']);
ORM::configure('username', $toml['database']['user']);
ORM::configure('password', $toml['database']['password']);
ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

respond(function ($request, $response, $app) {
	$app->register('twig', function() {
        $loader = new Twig_Loader_Filesystem('../templates/');
		$twig = new Twig_Environment($loader, array(
			'cache' => '../cache/',
			'auto_reload' => true
		));
		return $twig;
    });

    $app->register('log', function(){
    	return new KLogger('../logs/', KLogger::DEBUG);
    });
});

respond('GET', '/', function ($request, $response, $app) {
	echo $app->twig->render('index.twig', array());
});

dispatch();
?>