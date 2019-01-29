<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


date_default_timezone_set('America/Bogota');
//traer todas las novedades
$app->get('/api/novelty', function(Request $request, Response $response){
    $sql = "SELECT * FROM `tb_novedades`";
    $data =  getFechAll($sql);
    return $response->withStatus(200)
                    ->withHeader('Content-Type', 'application/json')
                    ->write(json_encode($data));
 
});
//traer las novedades por id usuario
$app->get('/api/novelty/{iduser}', function(Request $request, Response $response){
    $id = $request->getAttribute('iduser');
    $sql = "SELECT * FROM `tb_novedades` WHERE `iduser` = $id";
    
    $data =  getFechAll($sql);
    return $response->withStatus(200)
                    ->withHeader('Content-Type', 'application/json')
                    ->write(json_encode($data));
 
});
//traer las novedades por id
$app->get('/api/novelty/id/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $sql = "SELECT * FROM `tb_novedades` WHERE `id_novedad` = $id";
    
    $data =  getFech($sql);
    return $response->withStatus(200)
                    ->withHeader('Content-Type', 'application/json')
                    ->write(json_encode($data));
 
});
//eliminar novedad
$app->delete('/api/novelty/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $sql = "DELETE FROM `tb_novedades` WHERE `id_novedad` = $id";
    $result = eliminarRespuestaDeNovedad($id);
    //Codicion
    if($result=='ok'){
            $data = delete($sql);
            return $response->withStatus(200)
                    ->withHeader('Content-Type', 'application/json')
                    ->write(json_encode($data));
    }else{
        return $response->withStatus(400)
                    ->withHeader('Content-Type', 'application/json')
                    ->write(json_encode($result));
    }

});
//crear novedad
$app->post('/api/novelty', function(Request $request, Response $response){
    $titulo = $request->getParam('titulo');
    $descripcion = $request->getParam('descripcion');
    $iduser = $request->getParam('iduser');
    $op = $request->getParam('op');
    $sql = "INSERT INTO `tb_novedades` 
    (`id_novedad`, `titulo`, `descripcion`, `estado`, `fecha_creacion`, `opcional`, `iduser`) 
    VALUES (NULL, '$titulo', '$descripcion', 'ENVIADA', CURRENT_TIMESTAMP, 'op',  $iduser);";
    $data = insert($sql);
    return $response->withStatus(201)
                    ->withHeader('Content-Type', 'application/json')
                    ->write(json_encode($data));
});
//crear respuesta
$app->post('/api/answer', function(Request $request, Response $response){
    $titulo = $request->getParam('titulo');
    $idnovedad = $request->getParam('idnovedad');
    $quien_responde = $request->getParam('quien_responde');
    $sql = "INSERT INTO `tb_respuestas` (`id_respuesta`, `mensaje`, `fecha`, `estado`, `quien_envia`, `id_novedad`)
    VALUES (NULL, '$titulo', CURRENT_TIMESTAMP, 'ACTIVA','$quien_responde',  '$idnovedad');";  
    $data = insert($sql);
    return $response->withStatus(201)
                    ->withHeader('Content-Type', 'application/json')
                    ->write(json_encode($data));
});
//eliminar respuesta
$app->delete('/api/answer/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $sql = "DELETE FROM `tb_respuestas` WHERE `id_respuesta` = $id";
    $data = delete($sql);
        return $response->withStatus(200)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode($data));
});
//traer las repuesta por novedad
$app->get('/api/answerByNoveltyd/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $sql = "SELECT * FROM `tb_respuestas` WHERE `id_novedad` = $id";
    
    $data =  getFechAll($sql);
    return $response->withStatus(200)
                    ->withHeader('Content-Type', 'application/json')
                    ->write(json_encode($data));
 
});
/* Funciones */
function eliminarRespuestaDeNovedad($id){
    $arr = array('message' => 'Register delete');
    $sql = "DELETE FROM `tb_respuestas` WHERE `id_novedad` = $id";
    $data = delete($sql);
    echo "data: ".$data;
    if($data == $arr){
        return 'ok';
    }else{
        return $data;
    }
}
function getFechAll($sql){
       try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        if($result && $result!=null) {
            return $result;
        } else {
             $arr = array('message' => 'No records found');
             return  $arr;
        }
       
    } catch(PDOException $e){
        $arr = array('message' => $e->getMessage());
        return  $arr;
    }
}
function getFech($sql){
     try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->query($sql);
        $data = $stmt->fetch(PDO::FETCH_OBJ);
        $db = null;
        
        if($data && $data!=false) {
            return $data;
        } else { 
             $arr = array('message' => 'No records found');
             return  $arr;
        }
    } catch(PDOException $e){
        $arr = array('message' => $e->getMessage());
        return  $arr;
    }
}
function delete($sql){
       try{
       // Get DB Object
       $db = new db();
       // Connect
       $db = $db->connect();
       $stmt = $db->prepare($sql);
       $stmt->execute();
       $db = null;
       $arr = array('message' => 'Register delete');
       return  $arr;
   } catch(PDOException $e){
       $arr = array('message' => $e->getMessage());
       return  $arr;
   }
}
function update($sql){
    try{
    // Get DB Object
    $db = new db();
    // Connect
    $db = $db->connect();
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $db = null;
    $arr = array('message' => 'Register update');
    return  $arr;
} catch(PDOException $e){
    $arr = array('message' => $e->getMessage());
    return  $arr;
}
}
function insert($sql){
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $lastInsertId = $db->lastInsertId();
        $arr = array('message' => 'Registers Added', 'Id' => $lastInsertId);
        return  $arr;
    } catch(PDOException $e){
        $arr = array('message' => $e->getMessage());
        return  $arr;
    }
}
?>