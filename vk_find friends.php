<?php

// httppost is a function that will send a request and get an information about users (their: photo, firstname, lastName and status):

function httpPost($url)
{
    $curl = curl_init($url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_URL, $url);    
    $response = curl_exec($curl);
	$response = json_decode($response, true);
    curl_close($curl);
	$i = 1;
	while($response['response'][$i-1]['uid']) {
	c('user->imageUser' . $i)->loadFromUrl($response['response'][$i-1]['photo_100']);
	c('user->firstName' . $i)->caption = iconv('UTF-8', 'cp1251', $response['response'][$i-1]['first_name']);
	c('user->lastName' . $i)->caption = iconv('UTF-8', 'cp1251', $response['response'][$i-1]['last_name']);
	c('user->status' . $i)->text = 'https://vk.com/id' . iconv('UTF-8', 'cp1251', $response['response'][$i-1]['uid']);
	$i++;
	}
	LoadForm(c("user"), LD_NONE); // load the form that is filled of the information about users;

}

// this class is supposed to have few useful functions
// It will have ... in future

class VK{

// send a request to the vk.com server
	
	function Request($method, $pars) {
		global $token;
		global $uid;
		global $temp;
		global $responseId;
		
		// form a request using curl.php 
		
		$curl = curl_init(); 
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_URL, 'https://api.vk.com/method/' . $method . '?' . $pars . '&access_token=' . $token . 'v=5.52');
		$response = curl_exec($curl);
		$response = json_decode($response, true);
		curl_close($curl);
		
//		handle the response and iterate throught users:

		$responseId = $response['response']['0'];
		for($i = 1; $i < $responseId; $i++) {
			$count = $i - 1;
			$uid[$count] = $response['response'][$i]['uid']; // get the id for the user;
			if ($i > 1) {
				$temp = $temp . ',' . $response['response'][$i]['uid']; // append to our storage the user`s id;
			}
			else {
				$temp = $temp . $response['response'][$i]['uid'];
			}
		}
		$url = 'https://api.vk.com/method/users.get' . '?' . 'user_ids=' . $temp . '&fields=photo_100&status' . 'v=5.52'; // create an url for another request;
		
		//https://api.vk.com/method/METHOD_NAME?PARAMETERS&access_token=ACCESS_TOKEN&v=V 
		 

		$result = httpPost($url);
	}
}
?>



