<?php
include_once("global.php");
$mz_id=$_GET["mz_id"];
$h5_url = $_POST['h5_url'];
$_par = parse_url(urldecode($url_out));
parse_str($_par['query'],$_query);

$_SESSION["referer"]=$url_now;

if($rmid == $_COOKIE["user_id"])
{
	//只修改推荐人ID,否则就变成自已推荐自已
	$path_array = explode("_",$_query["path"]);
	$rmid_array_num = count($path_array)-2;
	$rmid = $path_array[$rmid_array_num];
}
else
{
	$_query["depth"] = $_query["depth"]+1;
	$_query["path"] ? $_query["path"] = $_query["path"].$rmid."_" : $_query["path"]="_";
}


if(!$_query["lineid"]) $_query["lineid"] = time().$_COOKIE["user_id"];
if($_query["h5_url"]) $_query["h5_url"] = '';
$url_out = '?'.http_build_query($_query);
if($_query["state"]=="") $url_out=$url_out."&state=repeat";

$timestamp = time();
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$url_jssdk = $h5_url;
$signature = sha1("jsapi_ticket=".$wx_config["00"]["js_ticket"]."&noncestr=GZOgQmEI9hKgyCLg&timestamp=$timestamp&url=$url_jssdk");
$data = array();
$data['result'] = 'succ';
$data['user_id'] = $_COOKIE["user_id"];
$data['appid'] = $wx_config["00"]["appid"];
$data['timestamp'] = $timestamp;
$data['url_jssdk'] = $url_jssdk;
$data['signature'] = $signature;
$data['out_url'] = $url_out;
$data['repeat_url'] = "../../user_record.php?module=mz&id=".$mz_id."&action=repeat&rmid=".$rmid."&depth=".$_query['depth']."&lineid=".$_query['lineid']."&path=".$_query['path'];
echo json_encode($data);
?>


