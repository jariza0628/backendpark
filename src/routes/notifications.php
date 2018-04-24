<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

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

	function sendMessage2(){
		$content = array(
			"en" => 'English Message'
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
												   'Authorization: Basic YOUR_REST_API_KEY'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response = curl_exec($ch);
		curl_close($ch);
		
		return $response;
	}


$app->get('/api/push/{sms}', function(Request $request, Response $response){
    $id = $request->getAttribute('sms');
   
		echo 'mensaje: '.$id;
		//sendMessage($id);
  
});

$app->get('/api/push2/{sms}', function(Request $request, Response $response){
    $id = $request->getAttribute('sms');
   	
	$response = sendMessage2();
	$return["allresponses"] = $response;
	$return = json_encode( $return);
	
	print("\n\nJSON received:\n");
	print($return);
	print("\n");
  
});