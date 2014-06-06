<?php

include "../vendor/autoload.php";

//Parameters
$params = array("name" => "andy");

/*
//Signature without andtyl/signature
//Auth parameters
$params['auth_timestamp'] = time();
$params['auth_key'] = "123";

//Generate signature
$params = array_change_key_case($params, CASE_LOWER); //Keys lower case
ksort($params, SORT_STRING); //Keys sorted
$params_string = implode("\n", array(
	"POST",
	"/hello",
	http_build_query($params)
)); //Concatenated to one string
$params['auth_signature'] = hash_hmac('sha256', "456", $params_string); //HMAC SHA256
*/

//Signature with andtyl/signature
$client = new Signature\Client(new Signature\Signer());
$params = $client->getSignedRequestParams("123", "456", "POST", "/hello", $params);

//Make the request
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, "http://127.0.0.1/a/rest-server/examples/hello");
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

//Handle the response
$response = curl_exec($curl);
$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

if ($http_code != 200) {
	if (isset($response['error_message_developer'])) {
	    echo "Error: " . $response['error_message_developer'];
	} else {
		echo "Error";
	}
	exit;
}

$data = json_decode($response, true); 

echo "<pre>" . print_r($data, true) . "</pre>";