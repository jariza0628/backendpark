<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
$app = new \Slim\App;
$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});
$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});
// buildings*****************************************************************
//************************************************************************************
//************************************************************************************
$app->get('/api/buildings', function(Request $request, Response $response){
    $sql = "SELECT * FROM tb_edificio";
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($result);
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});


// Get Single buildings
$app->get('/api/building/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $sql = "SELECT * FROM tb_edificio WHERE id_edificio = $id";
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->query($sql);
        $customer = $stmt->fetch(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($customer);
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});
//  tb_bloque*****************************************************************
//************************************************************************************
//************************************************************************************
$app->get('/api/blocks', function(Request $request, Response $response){
    $sql = "SELECT * FROM tb_bloque";
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($result);
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

$app->get('/api/block/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $sql = "SELECT * FROM tb_bloque WHERE id_bloque = $id AND estado=1";
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->query($sql);
        $customer = $stmt->fetch(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($customer);
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

$app->get('/api/blockByBuilding/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $sql = "SELECT * FROM tb_bloque WHERE id_edificio = $id AND estado=1";
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->query($sql);
        $customer = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($customer);
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

// floors*****************************************************************
//************************************************************************************
//************************************************************************************
$app->get('/api/floors', function(Request $request, Response $response){
    $sql = "SELECT * FROM tb_piso";
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($result);
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

$app->get('/api/floorByIdBlock/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $sql = "SELECT * FROM tb_piso WHERE id_bloque = $id AND estado=1";
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($result);
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

// spaces*****************************************************************************
//************************************************************************************
//************************************************************************************

$app->get('/api/spaces', function(Request $request, Response $response){
    $sql = "SELECT * FROM tb_espacio";
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($result);
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

$app->get('/api/space/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $sql = "SELECT * FROM tb_espacio WHERE id_espacio = $id AND estado=1";
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->query($sql);
        $customer = $stmt->fetch(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($customer);
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

$app->get('/api/spaceByfloorId/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $sql = "SELECT * FROM bd_park.spacelibres
            WHERE  dia = '29' AND mes = '05' AND anio = '2017' AND idpiso = '$id'
            ";
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($result);
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});
//

$app->get('/api/freeSpaces', function(Request $request, Response $response){
    $sql = "SELECT * FROM bd_park.spacelibres
            WHERE  dia = '29' AND mes = '05' AND anio = '2017'
            ORDER BY hora
            LIMIT 0 , 5;";
     try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($result);
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }

});

//ESPACIOS LIBRES POR EDIFICIO ID


$app->get('/api/freeSpacesByBuilding/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $sql = "SELECT COUNT(*) AS ESPACIOSLIBRESHOY FROM tb_espacio 
            INNER JOIN tb_piso ON tb_espacio.id_piso = tb_piso.id_piso 
            INNER JOIN tb_bloque ON tb_bloque.id_bloque = tb_piso.id_bloque 
            INNER JOIN tb_edificio ON tb_edificio.id_edificio = tb_bloque.id_edificio 
            WHERE tb_edificio.id_edificio='$id' 
            AND tb_espacio.id_espacio NOT in (SELECT id_espacio FROM tb_temp_usuario)";
     try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->query($sql);
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($result);
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }

});


// buildings*****************************************************************
//************************************************************************************
//************************************************************************************

$app->post('/api/save/space', function(Request $request, Response $response){
    $id_espacio = 1;
    $id_usuario = 2;

    $sql="INSERT INTO `tb_temp_usuario` (`id_temp`, `fecha`, `estado`, `id_usuario`, `id_espacio`)
          VALUES (NULL, '".date("d/m/Y")."', '1', :id_usuario, :id_espacio);";
    
   
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id_espacio', $id_espacio);
        $stmt->bindParam(':id_usuario',  $id_usuario);
        
        $stmt->execute();
        echo '{"notice": {"text": "Customer Added"}';
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

// Delete desbloquear spacio
$app->DELETE('/api/delSpaceTmp/delete/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $sql = "DELETE FROM `tb_temp_usuario` WHERE `tb_temp_usuario`.`id_temp` = '$id'";
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $db = null;
        echo '{"notice": {"text": "Customer Deleted"}';
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});