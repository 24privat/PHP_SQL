<?php
require_once '../../sess.inc';
$user_id = @$_SESSION['auth']['uid'];
//var_dump($_POST); die();

$user_id = @$_SESSION['auth']['uid'];
$oper    = $_POST["oper"];


//$sign    = $_POST["sign"];
$resp = array('status' => 'IN');

 switch($_POST['oper']){
     case 'get':
     	 $response=$DB->select("SELECT * FROM `sb_zvit` WHERE `id`=" . (int)$_POST['id']);
     	 if ($response) {
     	 	switch ($response[0]['object_id']) {
     	 		case 1:
     	 			$response1 = $DB->select("SELECT `persona_kod1c`,
     	 			                                  `subject`,
     	 			                                  `distance`,
     	 			                                  `timein`,
     	 			                                  `timeout`,
     	 			                                  `alcohol`,
     	 			                                  `check`
     	 			                           FROM `sb_secure` WHERE `id_zvit_prot`=" . $response[0]['id']);
     	 			if($response1) {
     	 				$response[0]['persona_kod1c'] = $response1[0]['persona_kod1c'];
     	 				$response[0]['subject'] = $response1[0]['subject'];
     	 				$response[0]['distance'] = $response1[0]['distance'];
     	 				$response[0]['timein'] = $response1[0]['timein'];
     	 				$response[0]['timeout'] = $response1[0]['timeout'];
     	 				$response[0]['alcohol'] = $response1[0]['alcohol'];
     	 				$response[0]['check'] = $response1[0]['check'];
     	 			}
     	 			// var_dump($response1);
                 case 2:
     	 			$response1 = $DB->select("SELECT `summ_d`,
     	 			                                 `subject_theft`,     	 			                                
     	 			                                 `summ`,
     	 			                                 `tmc`,
     	 			                                 `reaction`,
     	 			                                 `summ_h`

     	 			                         FROM `sb_theft` WHERE `id_zvit_theft`=" . $response[0]['id']);
     	 			if($response1) {
     	 				$response[0]['summ_d'] = $response1[0]['summ_d'];
     	 				$response[0]['subject_theft'] = $response1[0]['subject_theft'];
     	 			//	$response[0]['theft_data'] = $response1[0]['theft_data'];
     	 				$response[0]['summ'] = $response1[0]['summ'];
     	 				$response[0]['tmc'] = $response1[0]['tmc'];
     	 				$response[0]['reaction'] = $response1[0]['reaction'];
     	 				$response[0]['summ_h'] = $response1[0]['summ_h'];


     	 			}
     	 			// var_dump($response1);

     	 		    break;

     	 		  case 3:
     	 			$response1 = $DB->select("SELECT `subject_tmc`,
     	 			                                 `summ_tmc_p`,
     	 			                                 `tmc_type`,
     	 			                                 `disturbance_type`,
     	 			                                 `summ_tmc_n`

     	 			                          FROM `sb_tmc` WHERE `id_zvit_tmc`=" . $response[0]['id']);
     	 			if($response1) {
     	 				$response[0]['subject_tmc'] = $response1[0]['subject_tmc'];
     	 				$response[0]['summ_tmc_p'] = $response1[0]['summ_tmc_p'];
     	 				$response[0]['tmc_type'] = $response1[0]['tmc_type'];
     	 				$response[0]['disturbance_type'] = $response1[0]['disturbance_type'];
     	 				$response[0]['summ_tmc_n'] = $response1[0]['summ_tmc_n'];
     	 			}
     	 			// var_dump($response1);

     	 		    break;

     	 		     case 4:
     	 			$response1 = $DB->select("SELECT `subject_labor`,      	 			                                 
     	 			                                 `reaction_labor`,
     	 			                                 `summ_d_labor`,
     	 			                                 `type_labor`

     	 			                          FROM `sb_labor` WHERE `id_zvit_labor`=" . $response[0]['id']);
     	 			if($response1) {
     	 				$response[0]['subject_labor'] = $response1[0]['subject_labor'];
     	 			//	$response[0]['labor_data'] = $response1[0]['labor_data'];
     	 				$response[0]['reaction_labor'] = $response1[0]['reaction_labor'];
     	 				$response[0]['summ_d_labor'] = $response1[0]['summ_d_labor'];
     	 				$response[0]['type_labor'] = $response1[0]['type_labor'];
     	 			}
     	 			// var_dump($response1);

     	 		    break;

     	 	}
     	 	$resp['status'] = 'OK';
     	 	$resp['data'] = $response[0];
     	 }
         break;

  case 'add' :

                      $_POST['persona_kod1c'] = str_replace('\"','"',$_POST['persona_kod1c']);
                      $_POST['notes'] = str_replace('\"','"',$_POST['notes']);
                      $_POST['subject'] = str_replace('\"','"',$_POST['subject']);
  
  
                      $date=explode("-",$_POST['datain']);
                	  $sel='insert sb_zvit (
  				                  `firm_id`,
								  `datain`,
								  `object_id`,
								  `notes`,
								  `persona_kod1c`,
								  `user_id`)

					select '.(int)$_POST['firm_id'].',"'.$date[2].$date[1].$date[0].'",'.$_POST['object_id'].',
					       "'.mysql_escape_string($_POST['notes']).'","'.mysql_escape_string($_POST['persona_kod1c']).'",'.$user_id;
//			 var_dump($sel);die();

                	$response=$DB->query($sel);

                     $id=$DB->lastInsertId();

                	 $sel='insert sb_secure (
  				                  `id_zvit_prot`,
								  `persona_kod1c`,
								  `subject`,
								  `alcohol`,
								  `distance`,
								  `check`,
								  `timein`,
								  `timeout`,
								  `notes`,
								  `user_id`)

					select '.$id.',"'.mysql_escape_string($_POST['persona_kod1c']).'","'.mysql_escape_string($_POST['subject']).'",'.$_POST['alcohol'].','.$_POST['distance'].',
					       '.$_POST['check'].',"'.$_POST['timein'].'","'.$_POST['timeout'].'",
					       "'.mysql_escape_string($_POST['notes']).'",'.$user_id;
//			 var_dump($sel);die();

                	$response=$DB->query($sel);



                	if ($response) $resp['status'] = 'OK';

  				break;
 case 'edit' :

                      $_POST['persona_kod1c'] = str_replace('\"','"',$_POST['persona_kod1c']);
                      $_POST['notes'] = str_replace('\"','"',$_POST['notes']);
                      $_POST['subject'] = str_replace('\"','"',$_POST['subject']);
                      
 
                      $date=explode("-",$_POST['datain']);
                	  $sel='update `sb_zvit`
                	  		set
                	  			`firm_id`='.(int)$_POST['firm_id'].',
                	  			`datain`="'.$date[0].$date[1].$date[2].'",
                	  			`object_id`='.$_POST['object_id'].',
 					            `notes`="'.mysql_escape_string($_POST['notes']).'",
 					            `persona_kod1c`="'.mysql_escape_string($_POST['persona_kod1c']).'",
 					            `user_id`='.$user_id.'

					  where `id`='.$_POST['id_zvit_prot'];

			// var_dump($sel);//die();

                	$response=$DB->query($sel);

                     $id=$DB->lastInsertId();

                	 $sel='update `sb_secure`
                	        set

                	       `persona_kod1c`="'.mysql_escape_string($_POST['persona_kod1c']).'",
                	       `subject`="'.mysql_escape_string($_POST['subject']).'",
                	       `alcohol`='.$_POST['alcohol'].',
                	       `distance`='.$_POST['distance'].',
                	       `check`='.$_POST['check'].',
                	       `timein`="'.$_POST['timein'].'",
                	       `timeout`="'.$_POST['timeout'].'",
					       `notes`="'.mysql_escape_string($_POST['notes']).'",
					       `user_id`='.$user_id.'

					     where id_zvit_prot='.$_POST['id_zvit_prot'];
			// var_dump($sel);die();

                	$response=$DB->query($sel);

                	if ($response) $resp['status'] = 'OK';

  				break;



  case 'del' :
  				//var_dump($_POST);die();
  				$sel='delete from sb_zvit where id='.$_POST['id'];
  				$response=$DB->query($sel);
               	if ($response) $resp['status'] = 'OK';
				break;

 }
    echo json_encode($resp);

?>
