<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

date_default_timezone_set('America/Bogota');

/**
 * Un usuario puede:
 * Acumular millas
 * Restar millas si cancela una liberacion
 * Redimir millas
 * Consultar millas 
 * --- El sistema debe tener ----
 * Guardar los productos y su costo de millas
 * 
 */
//Millas por usuario
$app->get('/api/miles/detail/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $sql = "SELECT * FROM `tb_millas` WHERE`tb_usuario_id_usuario`=$id";
    $data =  getFechAll($sql);
    return $response->withStatus(200)
                    ->withHeader('Content-Type', 'application/json')
                    ->write(json_encode($data));
 
});
//Sumatoria Millas por usuario
$app->get('/api/miles/{iduser}', function(Request $request, Response $response){
    $id = $request->getAttribute('iduser');
    $sql = "SELECT SUM(numero_millas) AS total_millas, `tb_usuario_id_usuario` FROM `tb_millas` WHERE`tb_usuario_id_usuario`=$id";
    $data =  getFechAll($sql);
    return $response->withStatus(200)
                    ->withHeader('Content-Type', 'application/json')
                    ->write(json_encode($data));
 
});
//Sumatoria Millas por usuario
$app->get('/api/miles/history/{iduser}', function(Request $request, Response $response){
    $id = $request->getAttribute('iduser');
    $sql = "SELECT * FROM `tb_millas` WHERE`tb_usuario_id_usuario`=$id ORDER BY `tb_millas`.`fecha` DESC ";
    $data =  getFechAll($sql);
    return $response->withStatus(200)
                    ->withHeader('Content-Type', 'application/json')
                    ->write(json_encode($data));
 
});
//Redimir millas
$app->post('/api/miles/redeem', function(Request $request, Response $response){
    $id_product = $request->getParam('id_product');
    $iduser = $request->getParam('iduser');
    // vaLidar si el susario tiene millas suficientes
    $result_millas = consultarMillasUsuario($iduser);
    echo json_encode( $result_millas);
    $valor_millas_producto = consultarCostoMilllasProductos($id_product);
    echo "consultarCostoMilllasProductos ". intval($valor_millas_producto). "- ".intval($result_millas) ;
    if( intval($result_millas) >= intval($valor_millas_producto)){
        echo "Tieme millas";
        $sql = "INSERT INTO `tb_millas_redimidas` 
        (`id_redimidas`, `fecha`, `estado`, `id_usuario`, `id_productos_millas`) 
        VALUES 
        (NULL, CURRENT_TIMESTAMP, 'SOLICITADA', $iduser, $id_product);";
        $res = insert($sql);
        eliminacionMillas($iduser, $valor_millas_producto, 'Redimir');
        return $response->withStatus(201)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode($res));
    }else{
        $arr = array('message' => 'No tienes millas suficientes');
        return $response->withStatus(201)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode($arr));
    }

});

/****
 * **************************************************
 * **************USUARIOS****************************
 * **************************************************
 */
$app->post('/api/newuser', function(Request $request, Response $response){
    $user = $request->getParam('user');
    $pass = $request->getParam('password');
    $nombre = $request->getParam('name');
    $apellido = $request->getParam('lastname');
    // crear usuario sin estacionamiento
    $validar_user = verificar_usuario($user);
    //echo 'val '.$validar_user;
    if($validar_user === "vacio"){

        $sql="INSERT INTO `tb_usuario` (`id_usuario`, `email`, `clave`, `img`, `nombre`, `apellido`, `token`, `rol`, `estado`, `id_edificio`) VALUES
        (NULL, '$user', '".md5($pass)."', 'user.png', '$nombre', '$apellido', NULL, '3', '1', '1')";
        $data = insert($sql);
        return $response->withStatus(201)
                    ->withHeader('Content-Type', 'application/json')
                    ->write(json_encode($data));
    }else{
        $array = array(
            "data" => "Usuario no disponible",
        );
        return $response->withStatus(201)
                    ->withHeader('Content-Type', 'application/json')
                    ->write(json_encode($array));
    }
   
});

 //Acumular millas
function acomularMillas($iduser, $numero_millas){
    try {
            $sql ="
            INSERT INTO `tb_millas` 
            (`id_millas`, `numero_millas`, `fecha`, `motivo`, `tb_usuario_id_usuario`) 
            VALUES 
            (NULL, $numero_millas, CURRENT_TIMESTAMP, 'Liberacion', $iduser);
            ";
            //echo $sql;
            //code...
                 // Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $lastInsertId = $db->lastInsertId();
            $arr = array('message' => 'Millas Añadidas', 'Id' => $lastInsertId);
            return  $arr;
        } catch (PDOException $e) {
            //throw $th;
            $arr = array('message' => $e->getMessage());
            return  $arr;
        }
    }
     //Eliminar millas
    function eliminacionMillas($iduser, $numero_millas, $motivo){
        try {
            $numero_millas = $numero_millas * -1;
            $sql ="
            INSERT INTO `tb_millas` 
            (`id_millas`, `numero_millas`, `fecha`, `motivo`, `tb_usuario_id_usuario`) 
            VALUES 
            (NULL, $numero_millas, CURRENT_TIMESTAMP, '$motivo', $iduser);
            ";
            //echo $sql;
            //code...
                 // Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $lastInsertId = $db->lastInsertId();
            $arr = array('message' => 'Millas Eliminadas', 'Id' => $lastInsertId);
            return  $arr;
        } catch (PDOException $e) {
            //throw $th;
            $arr = array('message' => $e->getMessage());
            return  $arr;
        }
    }
    // COnsultar millas de un usario
    function consultarMillasUsuario($id){
        try{
            // Get DB Object
            $db = new db();
            // Connect
            $sql ="SELECT SUM(numero_millas) AS total_millas FROM `tb_millas` 
            WHERE`tb_usuario_id_usuario`=$id";
            $db = $db->connect();
            $stmt = $db->query($sql);
            foreach($stmt as $row)
            $num_millas = $row[0];
            $data = $stmt->fetchAll(PDO::FETCH_OBJ);
            
            $db = null;
             if($num_millas && $num_millas!=false) {
                return $num_millas;
            } else { 
                 $arr = array('message' =>  $data);
                 echo json_encode( $arr);
                 return  $arr;
            }
        } catch(PDOException $e){
            $arr = array('message' => $e->getMessage());
            return  $arr;
        }    
    }
    // COnsultar ccosto de millas de un producto
    function consultarCostoMilllasProductos($id){
        try{
            // Get DB Object
            $db = new db();
            // Connect
            $sql ="SELECT `costo_millas` FROM `tb_productos_millas`
             WHERE `id_productos_millas` = $id";
            $db = $db->connect();
            $stmt = $db->query($sql);
            foreach($stmt as $row)
            $num_millas = $row[0];
            $data = $stmt->fetchAll(PDO::FETCH_OBJ);
             $db = null;
            
            if($num_millas && $num_millas!=false) {
                return $num_millas;
            } else { 
                 $arr = array('message' =>  $data);
                 return  $arr;
            }
        } catch(PDOException $e){
            $arr = array('message' => $e->getMessage());
            return  $arr;
        }    
    }

    function verificar_usuario($username){
        $resultado = ""; 
        $sql="SELECT * FROM `tb_usuario` WHERE `email` = '$username'";
        // echo $sql . '<br>';
        try{
        // Get DB Object
            $db = new db();
            // Connect
            $numero = null;
            $db = $db->connect();
            $stmt = $db->query($sql);
            foreach($stmt as $row)
            $numero = $row[0];
            $result = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            //echo 'echo CF '. $numero . '<br>';
            if(isset($numero) && $numero!=null){
                $resultado = $numero;
            }else
            { 
                $resultado = "vacio";
            }
        } catch(PDOException $e){
                //echo '{"error": {"text": '.$e->getMessage().'}';
                $resultado = $e->getMessage();
        }
        //echo $resultado;
        return $resultado;
    }

?>