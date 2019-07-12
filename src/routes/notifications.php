<?php
 use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
//*************** */
date_default_timezone_set('America/Bogota');
function sendMessage($msj){
		$content = array(
			"en" => $msj
			);
		
		$fields = array(
			'app_id' => "c5d67ed6-d117-4987-88c3-4b7dc45f2ba8",
			'included_segments' => array('All'),
			'data' => array("foo" => "bar"),
			'contents' => $content
		);
		
		$fields = json_encode($fields);
    	print("\nJSON sent:\n");
    	print($fields);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
												   'Authorization: Basic ODg3YmM4MDktNWUwNS00NGEzLThmOTAtYTU0Nzc3ODczMjBj'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response = curl_exec($ch);
		curl_close($ch);
		
		return $response;
	}
	
	function sendMessageByPlayerId($msj, $playerID){
		$content = array(
			"en" => $msj
			);
		
		$fields = array(
			'app_id' => "c5d67ed6-d117-4987-88c3-4b7dc45f2ba8",
			'include_player_ids' => array($playerID),
			'data' => array("foo" => "bar"),
			'contents' => $content
		);
		
		$fields = json_encode($fields);
    	print("\nJSON sent:\n");
    	print($fields);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response = curl_exec($ch);
		curl_close($ch);
		
		return $response;
	}

	
$app->get('/api/pushhola', function(Request $request, Response $response){
 
    return $response->withStatus(200)
                    ->withHeader('Content-Type', 'application/json')
                    ->write(json_encode( 'hola'));
 
});

$app->get('/api/push', function(Request $request, Response $response){
    
	sendMessage('hola ');
 
 
    return $response->withStatus(200)
                    ->withHeader('Content-Type', 'application/json')
                    ->write(json_encode( 'push'));
 
});
$app->get('/api/playerid', function(Request $request, Response $response){
    
	$res = sendMessageByPlayerId('Hola paltye', 'fb175834-d0c9-4d86-a697-67eb887ffe7b');

 
    return $response->withStatus(200)
                    ->withHeader('Content-Type', 'application/json')
                    ->write(json_encode('OK'));
 
});
$app->get('/api/timenow', function(Request $request, Response $response){
    $hour = array(
		"hour" => date('H'),
	);
	return $response->withStatus(200)
                    ->withHeader('Content-Type', 'application/json')
                    ->write(json_encode($hour));
});
?>