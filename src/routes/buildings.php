<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// buildings*****************************************************************
//************************************************************************************
//************************************************************************************
date_default_timezone_set('America/Bogota');
$app->get('/api/buildings', function(Request $request, Response $response){
    $sql = "SELECT * FROM tb_edificio WHERE estado=1";
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
$app->get('/', function(Request $request, Response $response){
    
    try{
        // Get DB Object
            echo'
<!DOCTYPE html>
<html>
<head>
    <title></title>
</head>
<body style="background-image: url(../../views/asset/img/background.jpg);background-size:cover">
   <div style="padding: 5px; font-family: helvetica;">
      <div style="width: 100%;text-align: center;padding-top: 100px;padding-bottom: 30px !important;">
        <img src="/views/asset/img/logo_tran.jpg" style="border-radius: 50px;">
      </div>
      <div style="float: left;text-align: center;min-width: 49%;">
        <a href="../../views/login/login.php">
            <img src="../../views/asset/img/icon-free.png" width="300px" height="250px"><br>
            <span>Panel</span>
        </a>
      </div>
      <div style="float: right;text-align: center;min-width: 49%;">
        <a href="#">
            <img src="../../views/asset/img/icon-silla.png" width="260px" height="250px"><br>
            <span>Panel</span>
        </a>
      </div>
    
   </div>
</body>
</html>
</html>
               
            ';
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

//Parametros de apliacion
$app->get('/api/parametros', function(Request $request, Response $response){
    $sql = "SELECT * FROM `tb_parametros`";
    $data =  getFechAll($sql);
    return $response->withStatus(200)
                    ->withHeader('Content-Type', 'application/json')
                    ->write(json_encode($data));
 
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
    $sql = "SELECT `id_bloque`, `numero`, `estado`, `id_edificio`, 
            (SELECT COUNT(*) AS espacioslibresporbloque 
            FROM bd_park.spacelibres WHERE idbloque = id_bloque
            AND dia=".date('d')." AND mes = ".date('m')." AND anio = ".date('Y').") AS numfreeblock 
            FROM tb_bloque WHERE id_edificio =  $id AND estado=1";
            //echo $sql;
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
//no se esta utlizando
$app->get('/api/freeSpacesByBlock/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');

    $sql="SELECT COUNT(*) AS espacioslibresporbloque FROM bd_park.spacelibres 
            WHERE  dia = '29' AND mes = '05' AND anio = '2017' AND  idbloque = $id";
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
    $sql = "SELECT `id_piso`, `numero`, `estado`, `id_bloque`,
    (SELECT COUNT(*) AS espacioslibresporbloque 
    FROM bd_park.spacelibres WHERE idpiso = id_piso 
    AND dia=".date('d')." AND mes = ".date('m')." AND anio = ".date('Y').") AS numfreefloor
    FROM tb_piso WHERE id_bloque = $id AND estado=1";

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
    $dia = date("d");$mes=date("m");$anio=date("Y");
    $id = $request->getAttribute('id');
    $sql = "SELECT * FROM bd_park.spacelibres
            WHERE  dia = '".$dia."' AND mes = '".$mes."' AND anio = '".$anio."' AND idpiso = '$id'
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
    $dia = date("d");$mes=date("m");$anio=date("Y");
    /*
    $sql = "SELECT * FROM bd_park.spacelibres
            WHERE  dia = '".$dia."' AND mes = '".$mes."' AND anio = '".$anio."'
            ORDER BY hora
            LIMIT 0 , 5;";*/
    $sql = sqlspacefreetoday();
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

//numero de ESPACIOS LIBRES POR EDIFICIO ID


$app->get('/api/freeSpacesByBuilding/{id}', function(Request $request, Response $response){
    $dia = date("d");$mes=date("m");$anio=date("Y");

    $id = $request->getAttribute('id');
    /*
    $sql="SELECT COUNT(*) AS ESPACIOSLIBRESHOY FROM bd_park.spacelibres
            WHERE  dia = '".$dia."' AND mes = '".$mes."' AND anio = '".$anio."'";*/
    $sql=ESPACIOSLIBRESHOY_count();


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

//numero total de esacio por edificio
$app->get('/api/SpacesByBuilding/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $sql = "SELECT COUNT(*) AS totalEspacios FROM tb_espacio 
            INNER JOIN tb_piso ON tb_espacio.id_piso = tb_piso.id_piso 
            INNER JOIN tb_bloque ON tb_bloque.id_bloque = tb_piso.id_bloque 
            INNER JOIN tb_edificio ON tb_edificio.id_edificio = tb_bloque.id_edificio 
            WHERE tb_edificio.id_edificio='$id' 
           ";
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

//TRAER ESPACIO LIBRE CON SU CALENDARIO DISPONIBLE DE "HOY"
$app->get('/api/SpacesWhithCalendarFree/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
     $dia = date("d");$mes=date("m");$anio=date("Y");
    $sql ="SELECT tb_calendario.id_calendario, tb_calendario.horario, tb_espacio.id_espacio, tb_espacio.numero, tb_piso.numero AS piso, tb_bloque.numero as bloque 
    FROM tb_calendario 
    INNER JOIN tb_espacio ON tb_calendario.id_espacio = tb_espacio.id_espacio 
    INNER JOIN tb_piso ON tb_espacio.id_piso = tb_piso.id_piso 
    INNER JOIN tb_bloque ON tb_bloque.id_bloque = tb_piso.id_bloque 
    WHERE tb_espacio.id_espacio='$id'
    and tb_calendario.dia = '$dia' and tb_calendario.mes = '$mes' and tb_calendario.anio = '$anio'";
    //echo $sql;
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



// operaciones con spaces*****************************************************************
//************************************************************************************
//************************************************************************************

$app->post('/api/save/space', function(Request $request, Response $response){
    $id_espacio = $request->getParam('id');;
    $id_usuario = $request->getParam('iduser');

    //verficar si esta usado
    $validacion = consultar_si_exite_tb_temp_usuario($id_espacio);
    if($validacion=="vacio"){
        
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
            echo '["{"notice":"Customer Added"}]';
        } catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
        
    }else{
        echo '{"notice": {"text": "Espacio ocupado "'.$id_espacio.'}';
    }
    
});

// Delete desbloquear spacio
$app->delete('/api/delSpaceTmp/delete/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $sql = "DELETE FROM `tb_temp_usuario` WHERE `tb_temp_usuario`.`id_usuario` = '$id'";
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $db = null;
        // actulizar_registro_reserva_desbloquear($id);
        actulizar_registro_reserva_desbloquear_uno($id);
        echo '{"notice": {"text": "Customer Deleted"}';
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});



// usuarios***************************************************************************
//************************************************************************************
//************************************************************************************
$app->get('/api/User/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $sql = "SELECT 
    tb_usuario.id_usuario, 
    tb_usuario.email,
    tb_usuario.clave,
    tb_usuario.nombre,
    tb_usuario.apellido,
    tb_usuario.rol,
    tb_edificio.id_edificio,
    tb_edificio.nombre as edificio,
    tb_edificio.direccion,
    tb_edificio.ciudad
    FROM `tb_usuario` 
    inner JOIN tb_edificio ON tb_usuario.id_edificio = tb_edificio.id_edificio
    WHERE tb_usuario.id_usuario = '$id'";
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
$app->get('/api/SpacesOccupiedByUser/{id}', function(Request $request, Response $response){
     $dia = date("d");$mes=date("m");$anio=date("Y");
    $id = $request->getAttribute('id');
    $sql = "SELECT COUNT(*) AS ocupado FROM `tb_temp_usuario` WHERE `id_usuario`='$id' AND fecha = '".$dia."/".$mes."/".$anio."'";
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
/**
 * verifica si exite liberacion de un espacio por fecha
 */
$app->get('/api/SpacesFreedByDate/{id}/{d}/{m}/{y}', function(Request $request, Response $response){
   $dia = $request->getAttribute('d');
   $mes= $request->getAttribute('m');
   $anio= $request->getAttribute('y');
   $id = $request->getAttribute('id');
   $sql = "SELECT COUNT(*) AS ocupado FROM `tb_temp_usuario` WHERE `id_usuario`='$id' AND fecha = '".$dia."/".$mes."/".$anio."'";
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

$app->get('/api/log/{data}/{data2}', function(Request $request, Response $response){
    $email = strtolower($request->getAttribute('data'));
    $clave = $request->getAttribute('data2');
    //echo $email;
    //echo $clave;
    $sql="SELECT * FROM `tb_usuario` 
    WHERE `email`='".$email."' AND `clave`='".md5($clave)."' AND `estado`='1'";
    //echo "<br>".$sql."<br>";
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
$app->get('/api/daysFreeByUser/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $dia = date("d");$mes=date("m");$anio=date("Y");
    //echo $email;
    //echo $clave;
   $sql = "SELECT tb_calendario.`id_calendario`, tb_calendario.`dia`, tb_calendario.`mes`, tb_calendario.`anio`, 
        tb_calendario.`horario`,tb_espacio.`numero`, tb_espacio.`estado` 
        FROM `tb_usuario` INNER JOIN tb_espacio ON tb_usuario.id_usuario = tb_espacio.id_usuario 
        INNER JOIN tb_calendario oN tb_espacio.id_espacio = tb_calendario.id_espacio 
        WHERE tb_usuario.id_usuario = '$id' AND tb_usuario.id_edificio = 1
        AND tb_calendario.dia >= '$dia' AND tb_calendario.mes >= '$mes' AND tb_calendario.anio = '$anio'
         ORDER BY tb_calendario.`id_calendario` ASC";
    //echo "<br>".$sql."<br>";
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

$app->get('/api/SpaceOccupiedForMe/{id}', function(Request $request, Response $response){//que espacio esta ocupando un usuario
    $dia = date("d");$mes=date("m");$anio=date("Y");
    $id = $request->getAttribute('id');
    //echo $email;
    //echo $clave;
    $sql="SELECT * FROM `tb_temp_usuario`
    INNER JOIN tb_espacio ON tb_temp_usuario.id_espacio = tb_espacio.id_espacio
    WHERE tb_temp_usuario.id_usuario = '$id' AND fecha = '".$dia."/".$mes."/".$anio."'";
  
    //echo "<br>".$sql."<br>";
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


/**
 * Elimina el espacio del calenario queda eliminado
 * No se puede elimiar si esta ocupado
 */
$app->delete('/api/FreeDayByUser/delete/{id}/{fecha}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $fecha = $request->getAttribute('fecha');
    $fecha_hoy = date("d-m-Y");
    //traer el id_espacio por el id_calenadrio
    $id_espacio = obtener_espacioid_por_idcalendario($id);
    //echo '$id_espacio ' . $id_espacio . '<br>';
    //echo 'entro ocupado fechas: '. $fecha .' - '. $fecha_hoy. '<br>';
    if($id_espacio != "vacio" ){
        // Consultar si el espacio no esta ocupado
        $result = consultar_si_exite_tb_temp_usuario($id_espacio);
        //echo 'entrp $result ' . $result . '<br>';
        if($result === "vacio"){
            //echo 'entro delete 1: '.$result. '<br>';
            $sql = "DELETE FROM `tb_calendario` WHERE `tb_calendario`.`id_calendario` = '$id'";
            try{
                // Get DB Object
                $db = new db();
                // Connect
                $db = $db->connect();
                $stmt = $db->prepare($sql);
                $stmt->execute();
                $db = null;
                //echo '{"notice": {"text": "Customer Deleted"}';
                $iduser =  obtener_id_usuario_por_id_espacio($id_espacio);
                $data = array('message' => 'Eliminado 2');
                eliminacionMillas($iduser, 100, 'Eliminacion de espacio');
                return $response->withStatus(200)
                                ->withHeader('Content-Type', 'application/json')
                                ->write(json_encode($data));
            } catch(PDOException $e){
                //echo '{"error": {"text": '.$e->getMessage().'}';
                $data = array('error' => '$e->getMessage()');
                return $response->withStatus(200)
                    ->withHeader('Content-Type', 'application/json')
                    ->write(json_encode($data));
            }
        }else{
            // Si la fehca de liberacion es diferente a la actual, se puede eliminar
            //echo 'entro ocupado fechas 2: '. $fecha .' - '. $fecha_hoy;
            if($fecha != $fecha_hoy){
                $sql = "DELETE FROM `tb_calendario` WHERE `tb_calendario`.`id_calendario` = '$id'";
                try{
                    // Get DB Object
                    $db = new db();
                    // Connect
                    $db = $db->connect();
                    $stmt = $db->prepare($sql);
                    $stmt->execute();
                    $db = null;
                    //echo '{"notice": {"text": "Customer Deleted"}';
                    $data = array('message' => 'Eliminado');
                    return $response->withStatus(200)
                                    ->withHeader('Content-Type', 'application/json')
                                    ->write(json_encode($data));
                } catch(PDOException $e){
                    //echo '{"error": {"text": '.$e->getMessage().'}';
                    $data = array('error' => '$e->getMessage()');
                        return $response->withStatus(200)
                        ->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($data));
                }
            }else{
                    // ocupado no puede elmiar
                    //echo '{"notice": {"text": "ocupado no puede elmiar"}';
                    $data = array('message' => 'EL ESPACIO ESTA SIENDO USADO ACTUALMENTE');
                    return $response->withStatus(200)
                                    ->withHeader('Content-Type', 'application/json')
                                    ->write(json_encode($data));

            }

        }
    }else{
        // No se encontro id espacio
        $data = array('message' => 'No se encontro id espacio');
        return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode($data));
    }


});

$app->get('/api/idSpaceByuser/{iduser}', function(Request $request, Response $response){
    $dia = date("d");$mes=date("m");$anio=date("Y");
    $id = $request->getAttribute('iduser');
    $sql="SELECT `id_espacio`FROM `tb_espacio` WHERE id_usuario='$id'";
    //echo "<br>".$sql."<br>";
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
/**
 * Traer espacio por Id userio
 */
$app->get('/api/idSpaceNumberByuser/{iduser}', function(Request $request, Response $response){
    $dia = date("d");$mes=date("m");$anio=date("Y");
    $id = $request->getAttribute('iduser');
    $sql="SELECT * FROM `tb_espacio` WHERE id_usuario='$id'";
    //echo "<br>".$sql."<br>";
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->query($sql);
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        $db = null;
        //echo json_encode($result);
        return $response->withStatus(200)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode($result));
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});




$app->get('/api/MySpacesOccupiedByUser/{idspace}', function(Request $request, Response $response){
    $dia = date("d");$mes=date("m");$anio=date("Y");
    $id = $request->getAttribute('idspace');
    $sql="SELECT tb_temp_usuario.id_usuario, CONCAT(tb_usuario.nombre, ' ',tb_usuario.apellido) as nombre FROM `tb_temp_usuario` INNER JOIN tb_usuario ON tb_temp_usuario.id_usuario = tb_usuario.id_usuario 
        WHERE `id_espacio`= '$id' AND `fecha`= '".$dia."/".$mes."/".$anio."'";
    //echo "<br>".$sql."<br>";
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

$app->get('/api/md5/{pass}', function(Request $request, Response $response){
    $id = $request->getAttribute('pass');
   
        echo '{"md5":'.json_encode(md5($id)).'}';
  
});

$app->put('/api/updatepass/{id_usuario}', function(Request $request, Response $response){
    
    $id_usuario = $request->getAttribute('id_usuario');
    $clave = $request->getParam('clave');
    $clave = md5($clave);

    $sql = "UPDATE `tb_usuario` 
    SET `clave` = :clave 
    WHERE `tb_usuario`.`id_usuario` = '$id_usuario';";

     try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->prepare($sql);

        
        $stmt->bindParam(':clave',  $clave);
      
        $stmt->execute();
        echo '{"notice": {"text": "Pass Updated"}';
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }

}); 
   

$app->get('/api/freeSpace/{info}', function(Request $request, Response $response){
    $info = $request->getAttribute('info');
    $cont = 0;
    $idespacio = "";
    echo $info."<br>";

    $anio1 = substr($info, 0,4);
    $mes = substr($info, -19,2);
    $dia = substr($info, -17,2);
    //echo $anio1."<br>";
    //echo $dia."<br>";
  //echo $mes."<br>"; 
    $anio = substr($info, -14,4);
    $mes2 = substr($info, -10,2);
    $dia2 = substr($info, -8,2);
    //echo $anio."<br>";
    //echo $dia2."<br>";
    //echo $mes2."<br>"; 
    $userid = (int) substr($info, -5,3);
    //echo $userid."<br>";
    $jornada = (int) substr($info, -1,1);
    //echo $jornada."<br>";
    //consultar el espacio por el id del usuario
    $sql="SELECT * FROM `tb_espacio` WHERE `id_usuario`= ".$userid."";
     try{
        // Get DB Object
       $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->query($sql);
        foreach($stmt as $row)
         $idespacio = $row[0];
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        $db = null;
    } catch(PDOException $e){
        //echo '{"error": {"text": '.$e->getMessage().'}';
    }

    //difeecia entre los meses

    $diferenciames = (int) $mes2 - (int) $mes;
    $diferenciadia = (int) $dia2 - (int) $dia;
    $diferenciaanio = (int) $anio - (int) $anio1;
    //echo "diferencia de mese ".$diferenciames."<br>";
    //echo "diferencia de anio1 ".$diferenciaanio."<br>";
    //echo "diferencia de dias ".$diferenciadia."<br>";
    //si mes de 31 dias
    $quemes = (int) $mes;
    $diasdelmes = cuantodistieneelmes($quemes); //establecer cuantos dias tine el mes selecionado 31, 30 o febrero
    
    //echo "el mes tiene:  ".$diasdelmes."<br>";
    if ($jornada==0) {
        $jornada = "Libre Todo el dia";
    }
    if ($jornada==1) {
        $jornada = "Libre de 8:00 a 12:00";
    }
    if ($jornada==2) {
        $jornada = "Libre de 14:00 a 18:00";
    }
    //mes = mes and anio1 = anio1
    if($diferenciames == 0 AND $diferenciaanio == 0){
        
        $d1 = (int) $dia;
        $d2 = (int) $dia2;
        while ( $d1 <=  $d2) {
            $cont = $cont + 1;
            $var = consultar_si_exite_calendario($d1, $mes, $anio, $idespacio);
            //echo "entro var: ".$var;
            if($var=="vacio"){
                insertar_calendario($d1, $mes, $anio, $jornada, $idespacio);
                if($cont <= 1){
                    sendMessage('Liberaron un espacio para el:'.$d1.'-'.$mes.'-'.$anio.'.');
                }
                
            }
            $d1 = $d1 + 1;
        }
    }elseif($diferenciames!=0 AND $diferenciaanio==0){//mes =! mes and anio1 = anio1
        $m1 = (int) $mes;$m2 = (int) $mes2;
        $d1 = (int) $dia;$d2 = (int) $dia2;
        $diai = $d1;
        $diaf = $diasdelmes;
        while($m1<=$m2){
             
             while ( $diai <=  $diaf) {
                $cont = $cont + 1;
                $var = consultar_si_exite_calendario($diai, $m1, $anio, $idespacio);
                //echo "entro var: ".$var;
                if($var=="vacio"){
                    insertar_calendario($diai, $m1, $anio, $jornada, $idespacio);
                    if($cont <= 1){
                        sendMessage('Liberaron un espacio para el:'.$diai.'-'.$m1.'-'.$anio.'.');
                    }
                }
                //$sql = "INSERT INTO `tb_calendario` (`id_calendario`, `dia`, `mes`, `anio`, `horario`, `id_espacio`) VALUES 
                //(NULL, '".$diai."', '".$m1."', '".$anio."', '".$jornada."', '".$idespacio."');";
               
                $diai = $diai +1;
             }
             $diai = 1;
             $m1 = $m1 +1;
             $diaf = cuantodistieneelmes($m1);
             if($m1==$m2 ){
               $diaf = $d2;
             }
        }
    }elseif($diferenciames!=0 AND $diferenciaanio!=0){
        $m1 = (int) $mes;$m2 = (int) $mes2;
        $d1 = (int) $dia;$d2 = (int) $dia2;
        $a1 = (int) $anio1;$a2 = (int) $anio;
        $diai = $d1;
        $diaf = $diasdelmes;
        $m2f = 12;
        while ($a1 <= $a2) {
             
            while($m1<=$m2f){
                while ( $diai <=  $diaf) {
                    $cont = $cont + 1;
                    $diaa = $diai;
                    $mess = $m1;
                  
                    $var = consultar_si_exite_calendario($diaa, $mess, $a1, $idespacio);
                    //echo "entro var: ".$var;
                    if($var=="vacio"){
                        insertar_calendario($diaa, $mess, $a1, $jornada, $idespacio);
                        if($cont <= 1){
                            sendMessage('Liberaron un espacio para el:'.$diaa.'-'.$mess.'-'.$a1.'.');
                        }
                    }
                     $diai = $diai +1;
                }
                $diai = 1;
                //echo $m1."<br>";
                $m1 = $m1 +1;
                if($m1==13){
                        $m1 = 1;
                        $a1 = $a1 +1;
                }
                $diaf = cuantodistieneelmes($m1);
                if($m1==$m2 AND $a1==$a2 ){
                       $diaf = $d2;
                       $m2f = $m2;
                }
            }
            $a1 = $a1 +1;
        }
    }
    
});

    function consultar_si_exite_calendario($dia, $mes, $anio, $id_espacio){
         if($dia<10 && (strlen($dia)<2)){
            $dia = "0". $dia;
          }
          if($mes<10 && (strlen($mes)<2)){
            $mes = "0".$mes;
          }
         $resultado = "vacio"; 
         $sql="SELECT * FROM `tb_calendario` WHERE `dia`=".$dia." AND `mes` = ".$mes." AND `anio`= ".$anio." AND `id_espacio` = ".$id_espacio."";
         //echo $sql."<br>";
          try{
        // Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();
            $stmt = $db->query($sql);
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            $db = null;
            //echo json_encode($result);
            if($result!=null){
                $resultado = "fecha_duplicada";
            }else{ $resultado = "vacio";}
            } catch(PDOException $e){
                echo '{"error": {"text": '.$e->getMessage().'}';
                $resultado = $e->getMessage();
            }

            return $resultado;
    }
    /**
     * Devueve el Id del espacio correpondiente a la liberacion
     * del calaendario indiicado
     */
    function obtener_espacioid_por_idcalendario($id_calendario){
        $resultado = ""; 
        $sql="SELECT id_espacio FROM `tb_calendario` WHERE id_calendario = $id_calendario";
        //echo $sql . '<br>';
        try{
        // Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();
            $stmt = $db->query($sql);
            foreach($stmt as $row)
            $id_espacio = $row[0];
            $result = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            //echo 'echo CF '. $id_espacio . '<br>';
            if($id_espacio){
                $resultado = $id_espacio;
            }else{ $resultado = "vacio";}
        } catch(PDOException $e){
                //echo '{"error": {"text": '.$e->getMessage().'}';
                $resultado = $e->getMessage();
        }
        //echo $resultado;
        return $resultado;
    }
     function consultar_si_exite_tb_temp_usuario($id_espacio){//validar si ya se esta usando el parquedero evistar duplicidad
         
         $resultado = ""; 
         $sql="SELECT * FROM `tb_temp_usuario` WHERE `id_espacio`= ".$id_espacio."";
         //echo $sql."<br>";
          try{
        // Get DB Object
            $db = new db();
            // Connect
            $db = $db->connect();
            $stmt = $db->query($sql);
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            $db = null;
            //echo json_encode($result);
            if($result!=null){
                $resultado = "ocupado";
            }else{ $resultado = "vacio";}
            } catch(PDOException $e){
                //echo '{"error": {"text": '.$e->getMessage().'}';
                $resultado = $e->getMessage();
            }
            //echo $resultado;
            return $resultado;
    }

    function insertar_calendario($dia, $mes, $anio, $jornada, $id_espacio){
                    if($dia<10 && (strlen($dia)<2)){
                        $dia = "0". $dia;
                    }
                    if($mes<10 && (strlen($mes)<2)){
                        $mes = "0".$mes;
                    }
          $sql = "INSERT INTO `tb_calendario` (`id_calendario`, `dia`, `mes`, `anio`, `horario`, `id_espacio`) VALUES 
            (NULL, '".$dia."', '".$mes."', '".$anio."', '".$jornada."', '".$id_espacio."');";
            //echo $sql."<br>";

            try{
                // Get DB Object
                $db = new db();
                // Connect
                $db = $db->connect();
                $stmt = $db->prepare($sql);
                $stmt->execute();
                $lastInsertId = $db->lastInsertId();
                // Valiadar la hora de ejecucion
                $hora = date("H:i:s");
                registrar_liberacion($dia, $mes, $anio, $jornada, $id_espacio);
                //echo '{"notice": {"text": "Customer Added" '.$sql.'}';
            } catch(PDOException $e){
                //echo '{"error": {"text": '.$e->getMessage().'}';
            }

    }

   function cuantodistieneelmes($mes)
    {

        if($mes == 1 OR $mes == 3 OR $mes == 5 OR $mes == 7 OR $mes == 8 OR $mes == 10 OR $mes == 12){
        $diasdelmes = 31;
        }
        if($mes == 4 OR $mes == 6 OR $mes == 9 OR $mes == 11){
            $diasdelmes = 30;
        }
        if($mes == 2){
            $diasdelmes = 28;
        }
       
        return $diasdelmes;
    }

    function sqlspacefreetoday(){
         $dia = date("d");$mes=date("m");$anio=date("Y");
            $sql = " SELECT 
        `tb_espacio`.`id_espacio` AS `espacioid`,
        `tb_espacio`.`numero` AS `nombre`,
        `tb_espacio`.`id_piso` AS `idpiso`,
        `tb_bloque`.`id_bloque` AS `idbloque`,
        `tb_piso`.`numero` AS `nombrepiso`,
        `tb_calendario`.`dia` AS `dia`,
        `tb_calendario`.`mes` AS `mes`,
        `tb_calendario`.`anio` AS `anio`,
        `tb_calendario`.`horario` AS `hora`,
        `tb_edificio`.`id_edificio` AS `idedificio`
    FROM
        ((((`tb_calendario`
        JOIN `tb_espacio` ON ((`tb_calendario`.`id_espacio` = `tb_espacio`.`id_espacio`)))
        JOIN `tb_piso` ON ((`tb_piso`.`id_piso` = `tb_espacio`.`id_piso`)))
        JOIN `tb_bloque` ON ((`tb_bloque`.`id_bloque` = `tb_piso`.`id_bloque`)))
        JOIN `tb_edificio` ON ((`tb_edificio`.`id_edificio` = `tb_bloque`.`id_edificio`)))
    WHERE
        ((NOT (`tb_espacio`.`id_espacio` IN (SELECT 
                `tb_temp_usuario`.`id_espacio`
            FROM
                `tb_temp_usuario` WHERE fecha = '".$dia."/".$mes."/".$anio."')))
            AND (`tb_espacio`.`estado` = 1))
    AND  dia = '".$dia."' AND mes = '".$mes."' AND anio = '".$anio."'
            ORDER BY hora
            LIMIT 0 , 5
            ";
            return $sql;
    }
     function ESPACIOSLIBRESHOY_count(){
         $dia = date("d");$mes=date("m");$anio=date("Y");
            $sql = " SELECT 
        COUNT(*) AS ESPACIOSLIBRESHOY
    FROM
        ((((`tb_calendario`
        JOIN `tb_espacio` ON ((`tb_calendario`.`id_espacio` = `tb_espacio`.`id_espacio`)))
        JOIN `tb_piso` ON ((`tb_piso`.`id_piso` = `tb_espacio`.`id_piso`)))
        JOIN `tb_bloque` ON ((`tb_bloque`.`id_bloque` = `tb_piso`.`id_bloque`)))
        JOIN `tb_edificio` ON ((`tb_edificio`.`id_edificio` = `tb_bloque`.`id_edificio`)))
    WHERE
        ((NOT (`tb_espacio`.`id_espacio` IN (SELECT 
                `tb_temp_usuario`.`id_espacio`
            FROM
                `tb_temp_usuario` WHERE fecha = '".$dia."/".$mes."/".$anio."')))
            AND (`tb_espacio`.`estado` = 1))
    AND  dia = '".$dia."' AND mes = '".$mes."' AND anio = '".$anio."'
          
            ";
            return $sql;
    }
    /**
     * Eliminar registros asignados tb_asignacion_reserva_temp
     */
    function actulizar_registro_reserva_desbloquear($id_usuario){
        $servername = "localhost";
        $username = "root";
        //$password = "";
        $password = "Mysqlparkbd";
        $dbname = "bd_park";
        $conn = mysqli_connect($servername, $username, $password, $dbname);
        $sql = "SELECT * FROM `tb_asignacion_reserva_temp` WHERE `ocupado_m` = '$id_usuario' OR `ocupado_t` = '$id_usuario' OR `ocupado_dia` = '$id_usuario' ";
        $result = $conn->query($sql);
        $cont = 0;
        if ($result->num_rows > 0) {
            // output data of each row id_espacio
            while($row = $result->fetch_assoc()) {
                actulizar_registro_reserva_desbloquear_uno($id_usuario);
                $cont = $cont + 1;
            }
        } else {
            echo "0 results";
        }
        echo "Elimnados asignacion". $cont;
        return $cont;
        
    }
    /**
     * Actualiza la tabla tb_asignacion_reserva_temp cuando un usuario libera un espacio
     * asignado o selecionado uno a uno
     */
    function actulizar_registro_reserva_desbloquear_uno($id_user){
        // echo '$id_espacio', $id_espacio;
        $servername = "localhost";
        $username = "root";
        //$password = "";
        $password = "Mysqlparkbd";
        $dbname = "bd_park";
        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        } 
        // Actualizar tb_asignacion_reserva_temp en el campo ocupado_t si desbloquea tarde el usuario
        $sql = "UPDATE `tb_asignacion_reserva_temp` SET `ocupado_t` = '0' 
        WHERE `tb_asignacion_reserva_temp`.`ocupado_t` = '$id_user'
        ;";
         echo $sql . "<br>";
    
        if ($conn->query($sql) === TRUE) {
            echo "UPDATEs ";
            // actulizar_registro_reserva_asignada($id_reserva, $id_user, $id_espacio);
        } else {
            echo "Error updating record: " . $conn->error;
        }
        // Actualizar
        $sql = "UPDATE `tb_asignacion_reserva_temp` SET `ocupado_m` = '0' 
        WHERE `tb_asignacion_reserva_temp`.`ocupado_m` = '$id_user'
     
        ;";
         echo $sql . "<br>";
    
        if ($conn->query($sql) === TRUE) {
            echo "UPDATEs ";
            // actulizar_registro_reserva_asignada($id_reserva, $id_user, $id_espacio);
        } else {
            echo "Error updating record: " . $conn->error;
        }
        $sql = "UPDATE `tb_asignacion_reserva_temp` SET `ocupado_dia` = '0' 
        WHERE `tb_asignacion_reserva_temp`.`ocupado_dia` = '$id_user'
        ;";
         echo $sql . "<br>";
    
        if ($conn->query($sql) === TRUE) {
            echo "UPDATEs ";
           //  actulizar_registro_reserva_asignada($id_reserva, $id_user, $id_espacio);
        } else {
            echo "Error updating record: " . $conn->error;
        }
    }
