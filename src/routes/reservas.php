<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

date_default_timezone_set('America/Bogota');

//traer todas las reservas
$app->get('/api/reservations', function(Request $request, Response $response){
    $sql = "SELECT * FROM `tb_reservas`";
    $data =  getFechAll($sql);
    return $response->withStatus(200)
                    ->withHeader('Content-Type', 'application/json')
                    ->write(json_encode($data));
 
});
//traer las reservas por id usuario
$app->get('/api/reservations/{iduser}', function(Request $request, Response $response){
    $id = $request->getAttribute('iduser');
    $sql = "SELECT * FROM `tb_reservas` WHERE `tb_usuario_id_usuario` = $id AND `estado`='ACTIVA'";
    
    $data =  getFechAll($sql);
    return $response->withStatus(200)
                    ->withHeader('Content-Type', 'application/json')
                    ->write(json_encode($data));
 
});
//eliminar reservas
$app->delete('/api/reservations/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $sql = "UPDATE `tb_reservas` SET `estado` = 'ELIMINADA' WHERE `tb_reservas`.`id_reserva` = $id;";
    $result = update($sql);
    //Codicion
            return $response->withStatus(200)
                    ->withHeader('Content-Type', 'application/json')
                    ->write(json_encode($result));
   

});
//crear reservas
$app->post('/api/reservations', function(Request $request, Response $response){
    $jornada = $request->getParam('jornada');
    $iduser = $request->getParam('iduser');

    $sql = "INSERT INTO `tb_reservas` 
    (`id_reserva`, `fecha_creacion`, `jornada`, `tb_usuario_id_usuario`, `estado`) 
    VALUES (NULL, CURRENT_TIMESTAMP, '$jornada', $iduser, 'ACTIVA');";
 
    $data = insert($sql);
    return $response->withStatus(201)
                    ->withHeader('Content-Type', 'application/json')
                    ->write(json_encode($data));
});

