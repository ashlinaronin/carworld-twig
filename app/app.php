<?php
    require_once __DIR__."/../vendor/autoload.php";
    require_once __DIR__."/../src/Car.php";

    //initializes cookies
    session_start();
    if (empty($_SESSION['list_of_cars'])) {
        $_SESSION['list_of_cars'] = array();
    }


    $app = new Silex\Application();
    $app['debug'] = true;

    //enables twig for the website
    $app->register (new Silex\Provider\TwigServiceProvider(), array(
        'twig.path' => __DIR__.'/../views'
    ));

    // If Twig doesn't need to display any special data, we don't need to pass it an array at all.
    $app->get('/', function() use ($app) {
        return $app['twig']->render('welcome.html.twig');
    });

    $app->get('/buy', function() use ($app) {
        return $app['twig']->render('buy.html.twig');
    });

    $app->get('/sell', function() use ($app) {
        return $app['twig']->render('sell.html.twig');
    });

    $app->post('/car_added', function() use ($app) {
        $newcar = new Car($_POST['make_model'], $_POST['price'], $_POST['miles'], $_POST['image_path']);
        $newcar->save();
        return $app['twig']->render('car_added.html.twig', array('newcar' => $newcar));
    });

    $app->post('/results', function() use ($app) {
        setlocale(LC_MONETARY, 'en_US'); // Add location info for money format

        $all_cars = Car::getAll();
        $cars_matching_search = array();

        foreach ($all_cars as $car) {
            if ($car->worthBuying($_POST['price'], $_POST['miles'])) {
                array_push($cars_matching_search, $car);
            }
        }

        // Twig pages take a dictionary of whatever data you might want to display in them.
        // Here we send it a dictionary with one key/value pair- the key is 'search_results'
        // and the value is the array of cars that match the user's search.
        // This gives twig access to this information.
        return $app['twig']->render('results.html.twig', array('search_results' => $cars_matching_search));

    });

    $app->get('/all_cars', function() use ($app){
        return $app['twig']->render('all_cars.html.twig', array('all_cars' => Car::getAll()));
    });

    $app->get('/delete_all', function() use ($app) {
        Car::deleteAll();
        return $app['twig']->render('delete_all.html.twig');
    });

    return $app;

?>
