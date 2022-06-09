<?php
declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use App\Application\Actions\Controllers\LoginController as Login;

return function (App $app) {

    $app->options('/api', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/test', function(Request $request, Response $response){
        //$data = Demo::decryptToken($request->getQueryParams("token")['token']);
        $response->getBody()->write(json_encode("OK"));

        return $response->withStatus(200);
    });

    $app->post('/admin/login', function (Request $request, Response $response) {
        $data = Login::loginAdmin($request->getParsedBody()['user'], $request->getParsedBody()['password']);
        $response->getBody()->write(json_encode($data));
        return $response->withStatus($data['response_code']);
    });

};
