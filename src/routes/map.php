<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

 

$app->get('/api/routes/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $sql = "SELECT * FROM `tb_estaciones` WHERE `ruta`='$id'";
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        
        if($result && $result!=null) {
            return $response->withStatus(200)
                            ->withHeader('Content-Type', 'application/json')
                            ->write(json_encode($result));
        } else { throw new PDOException('No records found');}
    } catch(PDOException $e){
        $err =  array("Error"  => $e->getMessage());
        return $response->withStatus(400)
                        ->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($err));
    }
});

?>