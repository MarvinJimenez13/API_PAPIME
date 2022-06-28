<?php
declare(strict_types=1);

use Slim\App;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Application\Actions\Controllers\LoginController as Login;
use App\Application\Actions\Controllers\AdminController as Admin;
use App\Application\Actions\Controllers\PanelProfessor as Panel;

return function (App $app) {

    $app->options('/admin/actualizarProfesor', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response
                    ->withHeader('Access-Control-Allow-Origin', '*')
                    ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
                    ->withHeader('Access-Control-Allow-Methods', 'GET, POST, DELETE, PATCH, PUT, OPTIONS');
    });

    $app->get('/admin/obtenerProfesores', function(Request $request, Response $response){
        $data = Admin::getProfessors($request->getQueryParams("token")['token']);
        $response->getBody()->write(json_encode($data));

        return $response->withStatus($data['response_code']);
    });

    $app->post('/admin/eliminarProfesor', function(Request $request, Response $response){
        $data = Admin::eliminarProfesor(json_encode($request->getParsedBody()));
        $response->getBody()->write(json_encode($data));

        return $response->withStatus($data['response_code']);
    });

    $app->put('/admin/actualizarProfesor', function(Request $request, Response $response){
        $data = Admin::updateProfessor(json_encode($request->getParsedBody()));
        $response->getBody()->write(json_encode($data));

        return $response->withStatus($data['response_code']);
    });

    $app->post('/admin/registrarProfesor', function(Request $request, Response $response){
        $data = Admin::saveProfessor(json_encode($request->getParsedBody()));
        $response->getBody()->write(json_encode($data));

        return $response->withStatus($data['response_code']);
    });

    $app->post('/admin/login', function (Request $request, Response $response) {
        $data = Login::loginAdmin($request->getParsedBody()['user'], $request->getParsedBody()['password']);
        $response->getBody()->write(json_encode($data));

        return $response->withStatus($data['response_code']);
    });


    /*PANEL JUEGO PROFESOR*/

    $app->post('/profesor/guardarJuego', function (Request $request, Response $response) {
        $data = Panel::saveGame(json_encode($request->getParsedBody()));
        $response->getBody()->write(json_encode($data));

        return $response->withStatus($data['response_code']);
    });


};
