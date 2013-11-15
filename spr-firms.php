<?php include "../../sess.inc";
$user_id = @$_SESSION['auth']['uid'];
$right=$DB->select("select 1 from spr_users_firms where user_id=".$user_id." and firm_id=0");
//var_dump($right);//die();
if(!$right)
	$sel="
			SELECT a.id
			     , a.name
			FROM
			  spr_users_firms c
			JOIN spr_firms a
			ON a.id = c.firm_id
			AND a.grp = 'AH'
			AND state = 'a'
			and gps=0

			WHERE
			  c.user_id =".$user_id
  		;
else
	$sel="
			SELECT a.id, a.name
			FROM `spr_firms` a
			WHERE a.grp = 'AH'
				AND state='a'
				AND gps=0
			ORDER BY
				a.name;
		";

$acc=$DB->select($sel);

   $answ='<select id="ListFirms">';

	if($_GET['fl']=='1')
	   $answ.='<option style="font-size:11px;" value="">- -</option>';

foreach($acc as $val) {
	$answ.='<option style="font-size:11px;" value='.$val['id'].'>'.$val['name'].'</option>';
}

$answ.='</select>';
echo $answ;
?>
