<?php
declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use App\Application\Actions\Controllers\DemoController as Demo;

return function (App $app) {

    $app->options('/api', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/decryptToken', function(Request $request, Response $response){
        $data = Demo::decryptToken($request->getQueryParams("token")['token']);
        $response->getBody()->write(json_encode($data));

        return $response->withStatus(200);
    });

    $app->get('/generateToken', function (Request $request, Response $response) {
        $data = Demo::getToken(5, "Demo");

        $response->getBody()->write(json_encode($data));
        return $response->withStatus(200);
    });
};
