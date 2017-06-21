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
    $sql="SELECT COUNT(*) AS ESPACIOSLIBRESHOY FROM bd_park.spacelibres
            WHERE  dia = '".$dia."' AND mes = '".$mes."' AND anio = '".$anio."'";

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
    $sql = "SELECT tb_calendario.id_calendario, tb_calendario.horario, tb_espacio.id_espacio, tb_espacio.numero,
            tb_piso.numero AS piso, tb_bloque.numero as bloque 
            FROM tb_calendario 
            INNER JOIN tb_espacio ON tb_calendario.id_espacio  = tb_espacio.id_espacio  
            INNER JOIN tb_piso ON tb_espacio.id_piso = tb_piso.id_piso 
            INNER JOIN tb_bloque ON tb_bloque.id_bloque = tb_piso.id_bloque 
            WHERE tb_espacio.id_espacio='$id'
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



// operaciones con spaces*****************************************************************
//************************************************************************************
//************************************************************************************

$app->post('/api/save/space', function(Request $request, Response $response){
    $id_espacio = $request->getParam('id');;
    $id_usuario = $request->getParam('iduser');;

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
        echo '{"notice": {"text": "Customer Added" '.$sql.'- esp: '.$id_espacio.'}';
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
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
        echo '{"notice": {"text": "Customer Deleted"}';
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});



// usuarios***************************************************************************
//************************************************************************************
//************************************************************************************

$app->get('/api/SpacesOccupiedByUser/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $sql = "SELECT COUNT(*) AS ocupado FROM `tb_temp_usuario` WHERE `id_usuario`='$id'
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

$app->get('/api/log/{data}/{data2}', function(Request $request, Response $response){
    $email = $request->getAttribute('data');
    $clave = $request->getAttribute('data2');
    //echo $email;
    //echo $clave;
    $sql="SELECT * FROM `tb_usuario` 
    WHERE `email`='".$email."' AND `clave`='".md5($clave)."'";
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
   
    //echo $email;
    //echo $clave;
   $sql = "SELECT tb_calendario.`id_calendario`, tb_calendario.`dia`, tb_calendario.`mes`, tb_calendario.`anio`, 
        tb_calendario.`horario`,tb_espacio.`numero`, tb_espacio.`estado` 
        FROM `tb_usuario` INNER JOIN tb_espacio ON tb_usuario.id_usuario = tb_espacio.id_usuario 
        INNER JOIN tb_calendario oN tb_espacio.id_espacio = tb_calendario.id_espacio 
        WHERE tb_usuario.id_usuario = '$id' AND tb_usuario.id_edificio = 1";
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

$app->delete('/api/FreeDayByUser/delete/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $sql = "DELETE FROM `tb_calendario` WHERE `tb_calendario`.`id_calendario` = '$id'";
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


$app->get('/api/freeSpace/{info}', function(Request $request, Response $response){
    $info = $request->getAttribute('info');
    $idespacio = "";
    //echo $info."<br>";

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
            $sql = "INSERT INTO `tb_calendario` (`id_calendario`, `dia`, `mes`, `anio`, `horario`, `id_espacio`) VALUES 
            (NULL, '".$d1."', '".$mes."', '".$anio."', '".$jornada."', '".$idespacio."');";
            //echo $sql."<br>";

            try{
                // Get DB Object
                $db = new db();
                // Connect
                $db = $db->connect();
                $stmt = $db->prepare($sql);
                $stmt->execute();
                //echo '{"notice": {"text": "Customer Added" '.$sql.'}';
            } catch(PDOException $e){
                //echo '{"error": {"text": '.$e->getMessage().'}';
            }
                    $d1 = $d1 + 1;
        }
    }elseif($diferenciames!=0 AND $diferenciaanio==0){//mes =! mes and anio1 0 anio1
        $m1 = (int) $mes;$m2 = (int) $mes2;
        $d1 = (int) $dia;$d2 = (int) $dia2;
        $diai = $d1;
        $diaf = $diasdelmes;
        while($m1<=$m2){

             while ( $diai <=  $diaf) {
                $sql = "INSERT INTO `tb_calendario` (`id_calendario`, `dia`, `mes`, `anio`, `horario`, `id_espacio`) VALUES 
                (NULL, '".$diai."', '".$m1."', '".$anio."', '".$jornada."', '".$idespacio."');";
                ////echo $sql."<br>";
                 try{
                // Get DB Object
                $db = new db();
                // Connect
                $db = $db->connect();
                $stmt = $db->prepare($sql);
                $stmt->execute();
                //echo '{"notice": {"text": "Customer Added" '.$sql.'}';
            } catch(PDOException $e){
                //echo '{"error": {"text": '.$e->getMessage().'}';
            }
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
                    $diaa = $diai;
                    $mess = $m1;
                    if($diai<10){
                        $diaa = "0". $diai;
                    }
                    if($m1<10){
                        $mess = "0".$m1;
                    }
                    $sql = "INSERT INTO `tb_calendario` (`id_calendario`, `dia`, `mes`, `anio`, `horario`, `id_espacio`) VALUES 
                    (NULL, '".$diaa."', '".$mess."', '".$a1."', '".$jornada."', '".$idespacio."');";
                    //echo $sql."<br>";
                     try{
                // Get DB Object
                $db = new db();
                // Connect
                $db = $db->connect();
                $stmt = $db->prepare($sql);
                $stmt->execute();
                //echo '{"notice": {"text": "Customer Added" '.$sql.'}';
                } catch(PDOException $e){
                    //echo '{"error": {"text": '.$e->getMessage().'}';
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