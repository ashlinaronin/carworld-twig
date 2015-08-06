<?php
    require_once __DIR__."/../vendor/autoload.php";
    require_once __DIR__."/../src/Car.php";

    //initializes cookies
    session_start();
    if (empty($_SESSION['list_of_cars'])) {
        $_SESSION['list_of_cars'] = array();
    }


    $app = new Silex\Application();
    //enables twig for the website
    $app->register (new Silex\Provider\TwigServiceProvider(), array('twig.path' => __DIR.'/../views'
    ));

    $app->get('/', function() use ($app) {
        return $app['twig']->render('cars-welcome.html.twig', array('cars' => Car::getAll()));
    });

    return $app;

?>
