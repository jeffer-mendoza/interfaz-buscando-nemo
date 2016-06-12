<?php

require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/../Controller/Controller.php';
//registrar cada uno de las clases que se necesiten

$app = new Silex\Application(); //registrar la aplicacion

$app['debug'] = true; //activar el degug

$app->register(new Silex\Provider\UrlGeneratorServiceProvider()); //usar el enrutamiento y se agrega el bind a la ruta
$app->register(new Silex\Provider\SwiftmailerServiceProvider()); //para el envio de correo electronico


//registrando twig
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../Resources/views',
));

$app->register(new Silex\Provider\HttpCacheServiceProvider(), array(
    'http_cache.cache_dir' => __DIR__ . '/cache/',
));

//se toma la ruta del controllador con su namespace
$app->get('/', 'Anunciar\Controller\Controller::indexAction')->bind('index');
$app->get('/busqueda', 'Anunciar\Controller\Controller::busquedaAction')->bind('busqueda');
$app->get('/resultado', 'Anunciar\Controller\Controller::resultadoAction')->bind('resultado');
$app->get('/buscar', 'Anunciar\Controller\Controller::buscarAction')->method('POST')->bind('buscar');

//$app->run();

if ($app['debug']) {
    $app->run();
} else {
    $app['http_cache']->run();
}