<?php
require_once '../../sess.inc';
$user_id = @$_SESSION['auth']['uid'];
//var_dump($_POST); die();

$user_id = @$_SESSION['auth']['uid'];
$oper    = $_POST["oper"];
$date=explode(".",$_POST['datain']);

$id    = $_POST["id"];


 switch($_POST['oper']){
  case 'add' :
  				$sel='insert sb_zvit (
								  firm_id,
								  object_id,
								  datain,
								  user_id)

					select '.$_POST['firm_id'].','.$_POST['object_id'].','.$date[2].$date[1].$date[0].','.$user_id;
			// var_dump($sel);die();

                	$response=$DB->query($sel);

  				break;

  case 'del' :
  				//var_dump($_POST);die();
  				$sel='delete from sb_zvit  where id='.$_POST['id'];
  				$response=$DB->query($sel);
				break;
  case 'edit' :
  				$sel='update sb_zvit  set firm_id='.$_POST['firm_id'].',object_id='.$_POST['object_id'].',
  				                                         dataoin='.$date[2].$date[1].$date[0].',
  				                                         user_id='.$user_id.'
  				where id='.$_POST['id'];
                //var_dump($sel);die();
                $response=$DB->query($sel);
  				break;
 }
    echo json_encode($response);

?>
