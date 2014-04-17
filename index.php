<?php
//require 'vendor/autoload.php';
require_once 'Slim/Slim.php';
require 'templates/CustomView.php';

echo "rerouteando";

// create new Slim instance
$app = new Slim();
/*$app = new \Slim\Slim(array(
    'templates.path' => './templates', // este es el valor por defecto, no hace nada
    'view' => new CustomView() // asignamos una vista personalizada
    ));
*/

// Para el directorio raíz del sitio
/*
$app->get('/', function() {
    echo "Root!";
});
*/

// Llamado si es por post
$app->post('/books/:id', function ($id) {
    echo "POST del book: $id";
});

// Llamado si es get
$app->get('/books/:id', function ($id) {
    echo "GET del book: $id";
});

// Responde a post y get --> via()
$app->map('/foo/bar', function() {
    echo "Fancy, huh?";
})->via('GET', 'POST'); // se corresponde con ambos tipos


// Los browsers no enviant PUT y DELETE, pero se puede modificar dentro del formulario y desde java/android si que se envia

// Dos paràmetres
$app->get('/books/:one/:two', function ($one, $two) {
    echo "GET del primer book $one <br>";
    echo "GET del segundo book $two <br>";
});

// Parámetros con wildcard, devuelve un array
$app->get('/hello/:array+', function ($array) {
    foreach ($array as  $item) {
        echo "Paràmetros wildcard $item <br>";
    }
});

// Asignamos un nombre a la ruta
$app->get('/hell/:name', function ($name) {
    echo "Hell, $name! <br>";
})->name('hell');

// Creamos una url a partir del name de la ruta
$url = $app->urlFor('hell', array('name' => 'Josh')); // /test_slim/hell/Josh

// Route Conditions, las condiciones que tienen que cumplir los parámetros son las correspondientes al array asociativo
// compuesto por el nombre del parámetro y una expresión regular que debe cumplir
$app->get('/archive/:year', function ($year) {
    echo "You are viewing archives from $year";
})->conditions(array('year' => '(19|20)\d\d')); // 1900 a 2099

// Route Conditions para toda la aplicación. Esto hace que el parámetro firstName tenga que cumplir esas condiciones
// para todas las rutas de la aplicación que lo usen
\Slim\Route::setDefaultConditions(array(
    'firstName' => '[a-zA-Z]{3,}'
));

// Comprobación
$app->get('/register/:firstName', function ($firstName) {
    echo "Ahora estas registrado $firstName"; // Solo caràcteres y mayor de 3
});

// Pueden usarse al mismo tiempo condiciones especificas y generales

$app->get('/register/:firstName/:lastName', function ($firstName, $lastName) {
    echo "Ahora estas registrado $firstName $lastName";
})->conditions(array('lastName' => '[a-z]{10,}'));;// Solo minúsculas y mayor de 10



// route, middleware...middleware, function()
function mw1() {
    echo "This is middleware ONE!<br>";
}

function mw2() {
    echo "This is middleware TWO!<br>";
}

// Las funciones son llamadas en orden antes de llamar a a la función principal
$app->get('/foo', 'mw1', 'mw2', function () {
    echo "Esto llama al middleware<br>";
});


// Puede usarse el middleware para añadir la autenticación de usuario antes de proceder por ejemplo
$authenticateForRole = function ( $role = 'member' ) { // Esta función es Middleware
    return function () use ( $role ) {
        $user = "no admin"; //Obtenemos el rol de usuario de la base de datos de alguna manera
        if ( $user!== "admin") {
            $app = \Slim\Slim::getInstance();
            $app->flash('error', 'Tiene que ser admin');
            $app->redirect('error'); // /test_slim/error
        }
    };
};


$app->get('/foobar', $authenticateForRole('admin'), function () {
    echo "Bienvenido al panel de admin";
});

$app->get('/error', function () {
    echo "Error de autenticación";
});

// Todos los elementos del middleware son llamados con un argumento (opcional) que corresponde a la ruta
$info = function (\Slim\Route $route) { // Objeto de tipo \Slim\Route
    echo "Current route is " . $route->getName() . "<br>"; // NO FUNCIONA, el route es NULL
    echo "Current route is " . is_null($route->getName()) . "<br>";
};

$app->get('/infooMiddleWare', $info, function () {
    echo "dentro de infooMiddleWare";
});


// Agrupando rutas, así se ahorra tener que añadir el /api/library/ a todas las demás llamadas
// API group
$app->group('/api', function () use ($app) {

    // Library group
    $app->group('/library', function () use ($app) {

        // Get book with ID
        $app->get('/books/:id', function ($id) {
            echo "GET de grupo del book $id";
        });

        // Update book with ID
        $app->put('/books/:id', function ($id) {
            echo "PUT de grupo del book $id";
        });

        // Delete book with ID
        $app->delete('/books/:id', function ($id) {
            echo "DELETE de grupo del book $id";
        });

    });

});



// Route helpers: redirect, halt, pass, stop

// Redirect
$app->get('/redirect', function() use ($app) {
    $app->redirect('foo'); // redirige a foo, No continua cargando el resto de la página
    echo "esto no debe verse";
});

// Permanent redirect
$app->get('/olde', function () use ($app) {
    $app->redirect('new', 301);
});

$app->get('/new', function () {
    echo "Esta es la nueva";
});



// Provocan una excepción. Devuelve una respuesta immediatamente al browser ocn el codigo especificado
//Send a default 500 error response
//$app->halt(500);
//Or if you encounter a Balrog...
//$app->halt(403, 'You shall not pass!');

// Pasa a la de la ruta coincidente actual a la siguiente que coincida
$app->get('/hola/Frank', function () use ($app) {
    echo "You won't see this...";
    $app->pass();
});
$app->get('/hola/:name', function ($name) use ($app) {
    echo "But you will see this! $name";
});


/* Si se añaden dos names y urlFor da error, si se comenta la del principio esta funciona normalmente.

// URLfor permite crear rutas dinámicas
// Asignamos un nombre a la ruta
$app->get('/tata/:name', function ($name) {
    echo "Tata, $name! <br>";
})->name('tata');

// Creamos una url a partir del name de la ruta
$url2 = $app->urlFor('tata', array('name' => 'Josh')); // /test_slim/hell/Josh
*/


/* ESTO SOLO FUNCIONA CON VERSIONES POSTERIORES Y PHP 5.3+

// Request param variables, devuelve el valor de la variable pasada por el metodo

//GET variable
$paramValue = $app->request->get('books');
echo "GET: $paramValue";

//POST variable
$paramValue = $app->request->post('books');
echo "POST: $paramValue";

//PUT variable
$paramValue = $app->request->put('books');
echo "PUT: $paramValue";

// Devuelve un array con todas las variables
$allGetVars = $app->request->get();
$allPostVars = $app->request->post();
$allPutVars = $app->request->put();

// Returns instance of \Slim\Http\Request
// Request permite acceder a la request desde el cliente
$request = $app->request;

// Response
// Permite acceder a la response que se envia al cliente status/header/body
//$app->response->headers->set('Content-Type', 'application/json');
//$app->response->headers->set('Content-Type', 'application/xml'); // <-- debe estar correctamente formateado


///////////////////////////////////////////////////////////
/// RENDERING

$app->get('/render/:id', function ($id) use ($app) {
    $app->render('myTemplate.php', array('title' => $id));
});



/* se puede pasar también un valor fijo, y el status
$app->render(
    'myTemplate.php',
    array( 'name' => 'Josh' ),
    404
);
*/

/* ESTO SOLO FUNCIONA CON VERSIONES POSTERIORES Y PHP 5.3+
// Añadir datos al view. SET sustituye los datos anteriores por los nuevos
$app->get('/render2/:id', function ($id) use ($app) {
    $app->view->setData(array(
        'color' => $id,
        'size' => 'medium'
    ));
    $app->render('myTemplate.php');
});

// Usamos una vista personalizada en lugar de un template
$app->get('/renderView/:id', function ($id) use ($app) {
    $app->view->setData(array(
        'color' => $id,
        'size' => 'medium'
    ));
    $app->render('CustomView.php');
});


// APPEND añade nuevos datos a los antiguos
$app->get('/renderView2/:id', function ($id) use ($app) {
    $app->view->setData(array(
        'size' => 'medium'
    ));
    $app->view->appendData(array(
        'color' => $id.' total',
    ));
    $app->render('CustomView.php');
});

/// MIDDLEWARE. Son capas que son aplicadas al resultado antes de mostrarse
// Cada middleware debe llamar al siguiente, si no lo hace se interrumpe el ciclo de vida de la aplicación y se sirve
// tal cual esté.
// Es fácil obtener información respecto a la aplicación, environment, request y response desde el componente

class MyMiddleware extends \Slim\Middleware
{
    public function call()
    {
        //The Slim application
        $app = $this->app;

        //The Environment object
        $env = $app->environment;

        //The Request object
        $req = $app->request;

        //The Response object
        $res = $app->response;

        //Optionally call the next middleware
        $this->next->call();
    }
}

// Ejemplo de middleware que capitaliza todas las letras
class AllCapsMiddleware extends \Slim\Middleware
{
    public function call()
    {
        // Get reference to application
        $app = $this->app;

        // Run inner middleware and application
        $this->next->call();

        // Capitalize response body
        $res = $app->response;
        $body = $res->getBody();
        $res->setBody(strtoupper($body));
    }
}

// HOOKS
// Los hooks permiten registrar un callable a un evento
$app->hook('the.hook.name', function () {
    //Do something
}); // la prioridad por defecto es 10

$app->hook('the.hook.name', function () {
    //Do something else
}, 5); // asignamos la prioridad 5

// DEFAULT HOOJS
// slim.before
// slim.before.router
// slim.before.dispatch
// slim.after.dispatch
// slim.after.router
// slim.after

// Custom hooks, cuando se llama a un custom hook se llama a todos los callables asignados a ses hook
$app->applyHook('my.hook.name'); // llama a todos los callables asignados a my.hook.name


// FLASH
// Flash Next, pasa el message al proximo request view template
$app->flash('error', 'User email is required'); // Recuperable en el template con flash['error']

// Flash now, estará disponible en el view template actual, pero no en el siguiente
$app->flashNow('info', 'Your credit card is expired');// Recuperable en el template con flash['info']

// Flash keep, el mensaje recibido previamente estará disponible tambien en el siguiente
$app->flashKeep();

// TODO Sessions y cookies con slim
// TODO Logging



// Reaccionar a los erroes
$app->error(function (\Exception $e) use ($app) {
    $app->render('error.php'); // esta página no existe, habria que crearla
});

// Set notFound
$app->notFound(function () use ($app) {
    $app->render('404.html'); // esta página no existe, habria que crearla
});

// Invoke notFound
$app->get('/hella/:name', function ($name) use ($app) {
    if ( $name === 'Waldo' ) {
        $app->notFound();
    } else {
        echo "Hella, $name";
    }
});


// DEPENDENCY INJECTION
// Se puede usar como un key-value store:
$app->foo = 'bar';
$app->foo; // Devuelve 'bar';

// Usando el resource locator
// Determine method to create UUIDs
$app->uuid = function () {
    return exec('uuidgen');
};

// Get a new UUID
$uuid = $app->uuid;


// Como Singleton resources
// Define log resource
$app->container->singleton('log', function () {
    // return new \My\Custom\Log(); // siempre devuelve el valor de la primera ejecución de la función
});
// Get log resource
$log = $app->log; // Siempre recibe el mismo resultado


// Closure
// Para almacenar un closure sin ejecutar
// Define closure
$app->myClosure = $app->container->protect(function () {});

// Return raw closure without invoking it
$myClosure = $app->myClosure; // devuelve funcion() {} y no su resultado





// Aññadimos el middleware
$app->add(new \AllCapsMiddleware());

// run the Slim app
$app->run();


