<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Blog\PostMapper;

require __DIR__ . '/../vendor/autoload.php';

$loader = new FilesystemLoader(__DIR__ . '/../templates');
$twig = new Environment($loader);

$config = include __DIR__ . '/../config/database.php';

try {
    $connection = new PDO($config['dsn'], $config['username'], $config['password']);
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $exception) {
    echo 'Database error: ' . $exception->getMessage();
    die();
}

$postMapper = new PostMapper($connection);

// Instantiate app
$app = AppFactory::create();

// Add Error Handling Middleware
$app->addErrorMiddleware(true, false, false);

// Add route callbacks
$app->get('/', function (Request $request, Response $response, $args) use ($twig, $postMapper) {
    $posts = $postMapper->getList();
    $body = $twig->render('home.twig.html', ['posts' => $posts]);
    $response->getBody()->write($body);
    return $response;
});

$app->get('/{name}', function (Request $request, Response $response, $args) use ($twig, $postMapper) {
    $name = $args['name'];
    $post = $postMapper->getBySlug((string) $name);
    $body = $twig->render('hello.twig.html', ['post' => $post]);
    $response->getBody()->write($body);
    return $response;
});

// Run application
$app->run();