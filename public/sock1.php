<?php
include "dom.php";
//the info per user with custom func. 
$path = '';

//login form action url
$loginUrl="http://m.pic5678.com/web-root/public/process-login.aspx"; 
$arrPost = array(
	'username' => 'liem989',
	'password' => 'Aaaa1234',
	'HidCK' => 'twHXL'
);
$postinfo = http_build_query($arrPost);
$proxies = array();	
$proxies = '89.47.28.211:3128';
//init curl
$ch = curl_init();
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Host: m.pic5678.com',
    'Referer: "http://m.pic5678.com/web-root/public/login.aspx?errorCode=0&lang=en"'
    ));
curl_setopt($ch, CURLOPT_NOBODY, true);
//Set the URL to work with
curl_setopt($ch, CURLOPT_URL, $loginUrl);
//Set the post parameters
curl_setopt($ch, CURLOPT_POSTFIELDS, 'username=liem989&password=Aaaa1234&HidCK=twHXL');

//Handle cookies for the login
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');

//Setting CURLOPT_RETURNTRANSFER variable to 1 will force cURL
//not to print out the results of its query.
//Instead, it will return the results as a string return value
//from curl_exec() instead of the usual true/false.
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/32.0.1700.107 Chrome/32.0.1700.107 Safari/537.36');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_PROXY, $proxies);
//execute the request (the login)
$result = curl_exec($ch);
var_dump($result);die;
$redir = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
var_dump($redir);die;
$html = new simple_html_dom();
        // Load HTML from a string
$html->load($result);


foreach ($html->find('span.ip-address') as $key => $value) {
	//var_dump($value);die;
	$port = $html->find('span.port', $key)->innertext;
	$str = $value->innertext.":".$port;
	if( checkduplicateIp($str) == false){
		saveFile($str);
	}
}

function saveFile($str){
	$str = "\n".$str;
	$my_file = 'socks.txt';
	$handle = fopen($my_file, 'a') or die('Cannot open file:  '.$my_file);	
	fwrite($handle, $str);	
	fclose($handle);
}

function checkduplicateIp($str){
	$file = 'socks.txt';
	$handle = fopen($file, "r");
	$contents = fread($handle, filesize($file));
	fclose($handle);

	$list = array();

	$list = explode("\n",$contents);
	$list = array_map("trim", $list);

	$str = trim($str);

	return in_array($str,$list) ? true : false;
}
