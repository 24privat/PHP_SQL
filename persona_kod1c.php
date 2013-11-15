<?php include "../../sess.inc";


$user_id = @$_SESSION['auth']['uid'];
$text=trim($_POST['name_secure']);
$acc=Array();





$sel="SELECT inn, `name`
      FROM `spr_personal` WHERE `name` LIKE '%".$_POST['name_secure']."%' LIMIT 1";// . $sWhere;

//echo $sel;exit;


$acc=$DB->select($sel);


 $answ='';

 foreach($acc as $val){
	 $answ.='<div style="height:20px;" id="'.$val['inn'].'" onclick="selname(this)" onMouseOver=newtextdiv(this) onMouseOut=backtextdiv(this)>'.$val['name'].'</div>';
 }
 echo $answ;




?>

