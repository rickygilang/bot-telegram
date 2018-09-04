<?php
$mytoken = ""; //your token bot telegram 
$botname = "@Excrightbtcbot"; //your username bot telegram

// get update data from Bot Telegram getUpdates
$update = file_get_contents("php://input"); 
$update = json_decode($update, TRUE);
getReply($update);

//this is the main function to get data reply
function getReply($message) {
	$update_id = $message["update_id"];
	$incoming_message = $message["message"];
	if (isset($incoming_message["text"])) { //if the variable text message is not null or we have a text message
		$chat_id = $incoming_message["chat"]["id"]; 
		$message_id = $incoming_message["message_id"];
		$text = $incoming_message["text"];
		$parse_mode = "HTML";
		$response = createReply($text, $incoming_message);
		if (!empty($response))
			sendMessage($chat_id, $message_id, $response, $parse_mode);
	}
	return $update_id;
}

//this function for send a reply
function sendMessage($chat_id, $message_id, $response, $parse_mode) {
	$data = array(
		'chat_id' => $chat_id,
		'text' => $response,
		'parse_mode' => $parse_mode,
		'reply_to_message_id' => $message_id
	);
    $result = sendRequest('sendMessage', $data);
}

//this functiin for send a request
function sendRequest($method, $data)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, requestUrl($method));
    curl_setopt($ch, CURLOPT_POST, count($data));
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec ($ch);
    curl_close ($ch);

    return $response;
}

//To make a text reply
function createReply($text, $incoming_message) {
	global $botname;

	$result = "";

	$text = str_replace("$botname", "@Excrightbtcbot", $text);
	switch ($text) {
		case '/rgt':
		case '/rgt'.$botname :
		    if($incoming_message["chat"]["type"] == "group" || $incoming_message["chat"]["type"] == "supergroup"){
				$explodtext = explode("@", $text);
		        if($explodtext[1] == "Excrightbtcbot"){
		       		$result = "\xf0\x9f\x8d\x80 Selamat datang di bot exchange Rightbtc ketik /help untuk melihat daftar perintah !";
		    	}else{
		    		$return = "";
		    	}
			}else{
				$result = "\xf0\x9f\x8d\x80 Layanan Personal Message Tidak Tersedia !!!";
			}
		    break;
		case '/help':
			if($incoming_message["chat"]["type"] == "group" || $incoming_message["chat"]["type"] == "supergroup"){
				$result = "";
			}else{
				$result = "\xf0\x9f\x8d\x80 Layanan Personal Message Tidak Tersedia !!!";
			}
			break;
		case '/help'.$botname :
				$result = getHelp();
			break;
		case '/berita':
			if($incoming_message["chat"]["type"] == "group" || $incoming_message["chat"]["type"] == "supergroup"){
				$result = "";
			}else{
				$result = "\xf0\x9f\x8d\x80 Layanan Personal Message Tidak Tersedia !!!";
			}
			break;
		case '/berita'.$botname :
			if($incoming_message["chat"]["type"] == "group" || $incoming_message["chat"]["type"] == "supergroup"){
				$result = "Belum Tersedia";
			}else{
				$result = "\xf0\x9f\x8d\x80 Layanan Personal Message Tidak Tersedia !!!";
			}
			break;
		case '/rightbtc':
			if($incoming_message["chat"]["type"] == "group" || $incoming_message["chat"]["type"] == "supergroup"){
				$result = "";
			}else{
				$result = "\xf0\x9f\x8d\x80 Layanan Personal Message Tidak Tersedia !!!";
			}
			break;
		case '/rightbtc'.$botname :
			$result = getPair();
			break;
		case '/pairUSD' :
		case '/pairBTC' :
		case '/pairETH' :
		case '/pairETP' :
			if($incoming_message["chat"]["type"] == "group" || $incoming_message["chat"]["type"] == "supergroup"){
				$result = "";
			}else{
				$result = "\xf0\x9f\x8d\x80 Layanan Personal Message Tidak Tersedia !!!";
			}
			break;
		case '/pairUSD'.$botname :
		case '/pairBTC'.$botname :
		case '/pairETH'.$botname :
		case '/pairETP'.$botname :
			if($incoming_message["chat"]["type"] == "group" || $incoming_message["chat"]["type"] == "supergroup"){
				$pair = "";
				$explodtext = explode("@", $text);
				if($explodtext[1] == "Excrightbtcbot"){
					$pair = substr($explodtext[0],-3);
		       		$result = getList($pair);
		    	}
			}else{
				$result = "\xf0\x9f\x8d\x80 Layanan Personal Message Tidak Tersedia !!!";
			}
			break;
		default:
		    if($incoming_message["chat"]["type"] == "group" || $incoming_message["chat"]["type"] == "supergroup"){
		        $explodtext = explode("@", $text);
		        if($explodtext[1] == "Excrightbtcbot"){
		       		$result = getMarket($explodtext[0]);
		    	}else{
		    		$result = "\xf0\x9f\x8d\x80 Layanan Personal Message Tidak Tersedia !!!";
		    	}
		    }else{
		    	$result = getMarket($text);
		    }
			break;
	}
	return $result;
}

//Get info a market
function getMarket($text){
	$url = "https://www.rightbtc.com/api/public/ticker".$text;
	$hasil = (array)json_decode(getData($url));
	        
	if($hasil['result']->market != null){
        $result = "\xF0\x9F\x8E\x81 Market	: ".$hasil['result']->market."\n";
        $result .= "\xf0\x9f\x8d\x92 Low		: ".number_format((float)$hasil['result']->low24h,8)."\n";
        $result .= "\xf0\x9f\x8d\x8f High	: ".number_format((float)$hasil['result']->high24h,8)."\n";
        $result .= "\xf0\x9f\x8d\x87 Last	 : ".number_format((float)$hasil['result']->last24h,8)."\n";
    }else{
		$result = "\xf0\x9f\x8d\x88 Layanan tidak tersedia ! \xf0\x9f\x8d\x88";
    }
    return $result;
}

//get list all active markets/coins
function getList($pair){
	$sub = "";
	$data = "<b>\xf0\x9f\x8d\x8b Market List Rightbtc Pair ".$pair." : </b>\n\n";
	$hitung = 1;
	$hasil = (array)json_decode(getData("https://www.rightbtc.com/api/public/tickers"));

	//for below codes its depend on the structure of the data
	if($hasil != null){
		foreach($hasil['result'] as $result):
			$sub = substr($result->market,-3);
			if($sub == $pair){
				$data .= $hitung.". /".$result->market."\n";
				//	if(($hitung%4) == 0){
				//		$data .= "\n";
				// 	}
				$hitung++;
			}
		endforeach;
		$data .= "\n\n".getPair();
		$result =  $data;
    	return $result;
	}
}

//Make text reply about Command List
function getHelp(){
	$result = "<b>\xf0\x9f\x8d\x93 Command List</b>\n\n";
    $result .= "/rightbtc - Get the market list \n";
    $result .= "/help - Get the command list \n";
    return $result;
}

//Make text reply about Pair List
function getPair(){
	$result = "<b>\xf0\x9f\x8d\x93 Pair List Rightbtc </b>\n\n";
    $result .= "\xf0\x9f\x8d\x81 /pairUSD  \xf0\x9f\x8d\x81 /pairBTC\n";
    $result .= "\xf0\x9f\x8d\x81/pairETH  \xf0\x9f\x8d\x81/pairETP\n";
    return $result;
}

function requestUrl($method)
{
    global $mytoken;
    return "https://api.telegram.org/bot".$mytoken."/".$method;
}

//Get data from the url
function getData($url){
  $curl = curl_init($url);

  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

  $result = curl_exec($curl);

  curl_close($curl);

  return $result;
}


?>