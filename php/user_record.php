<?php
include_once("./include/dbclass.php");
include_once("./include/function.php");
include_once("./include/lib_user.php");
$mydb = new DbClass;
$mydb->DbLogon();
$module=$_GET["module"];
$id=$_GET["id"];
$action=$_GET["action"];
$user_id=$_COOKIE["user_id"];
$ct_num=$_GET["ct_num"];
$rmid=$_GET["rmid"];
$depth=$_GET["depth"];
$lineid=$_GET["lineid"];
$path=$_GET["path"];
if($module && $id && $action)
{
	if(!$ct_num) $ct_num = 'NULL';
	if(!$rmid) $rmid = 'NULL';
	if(!$depth) $depth = 'NULL';
	if(!$lineid) $lineid = 'NULL';
	if(!$user_id) $user_id = 1;
	$ct = 0;
	if($id && $user_id && $rmid !='NULL' && $action!="repeat"){
		$file_arr = explode(',', 'ct');
		$count = $mydb->DbQuery("select count(*) ct from sys_user_record where id = $id and user_id = $user_id and rmid = $rmid",$file_arr);
		$ct = $count[0]['ct'];
	}
	if($ct==0){
		$rs = $mydb->DbInsert("insert into sys_user_record(rid,module,id,user_id,action,ct_num,rmid,depth,lineid,path)values(seq_record_id.nextval,'$module',$id,$user_id,'$action',$ct_num,$rmid,$depth,$lineid,'$path')");
	}
	if($rmid&&$user_id&& $action=="view"){
		$rm_user_info = user_info($rmid);
		$user_login = user_info($user_id);
		$cid = $user_login['cid']; 
		if($cid){
			$filed_arr = explode(",","user_id,user_type");
			$cid_user = $mydb->DbQuery("select user_id,user_type from sys_user where user_type=2 and user_id='$cid'",$filed_arr);
		}
		if(!$cid||empty($cid_user)){
			//递归判断$rim是不是他的传播人
			$filed_count = explode(',','ct');
			$cid_count = $mydb->DbQuery("SELECT count(*) ct FROM sys_user where user_id = $rmid START WITH cid= $user_id CONNECT BY PRIOR user_id = cid",$filed_count);
			if(!$cid_count[0]['ct']){
				if($cid && $rm_user_info['user_type']==2){
					$rs1 = $mydb->DbUpdate("update sys_user set last_time=sysdate,cid=$rmid where user_id =$user_id");
				}else if(!$cid){
					$rs1 = $mydb->DbUpdate("update sys_user set last_time=sysdate,cid=$rmid where user_id =$user_id");
				}
				if($rs1){
					$file_arr = explode(',','user_id');
					$relation_user = $mydb->DbQuery("select user_id from sys_api_user_relation where user_id=$user_id",$file_arr);
					if(!empty($relation_user)){
						$rs2 = $mydb->DbDelete("delete from sys_api_user_relation where user_id=".$user_id);
					}
					$rs3 =	$mydb->DbInsert("insert into sys_api_user_relation(id,member_id,user_id,user_unionid,c_id,c_unionid,add_time)values (seq_api_user_relation_id.nextval,".$user_login["member_id"].",".$user_login["user_id"].",'".$user_login["wx_unionid"]."',$rmid,'".$rm_user_info["wx_unionid"]."',sysdate)");
					
				}
			}
		}
	}
}
if($rs)
{
	$mydb->DbCommit();
	echo "ok";
}
else
{
	echo "error";
}
?>