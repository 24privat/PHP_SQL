<?php
require_once '../../../sess.inc';
$user_id = @$_SESSION['auth']['uid'];
//var_dump($_POST); die();

$user_id = @$_SESSION['auth']['uid'];
    


$table= mysql_query("CREATE TABLE IF NOT EXISTS `table_prosrochka`(
    `firm_id` INT(11) NOT NULL ,
    `summa` DEC(20,2) NOT NULL,
    `id` INT(11) NOT NULL AUTO_INCREMENT, PRIMARY KEY(`id`),
    `dat_od` DATE)") or die(mysql_error());  

//var_dump($table); die();     
 
   
 $sel_proc='
SELECT 
       os.dat_od
     , sum(os.ong) AS summa
     , f.id AS firm_id


FROM
  pl_dlpb dl
JOIN pl_dmpb dm ON dm.eos_dlr = dl.eos_dlr
JOIN rep_acc_ost os ON os.bsc_acc = dm.bdm_d_acc AND os.dat_od = (SELECT dat_od_last
                                              FROM
                                                _odb)
JOIN spr_firms f ON f.okpo = dl.egf_crf

WHERE
  dl.bdl_rat > 0 
  GROUP BY f.id
 
       ';     
   //  var_dump($sel_proc); die();

$response_paym_2062=$DB->select($sel_proc);        

 
foreach($response_paym_2062 as $val) {
    $summa = round($val['summa']*12/360,2);
    $firm_id = $val['firm_id'];
    $date = explode('-',$val['dat_od']);
    $sel = 'INSERT table_prosrochka(
                                     firm_id,
                                     summa,
                                     dat_od )
                                     
                                     VALUES ('.$firm_id.',"'.$summa.'","'.$date[0].$date[1].$date[2].'")';

      $DB->query($sel);

}

      //  var_dump($sel); die();

 
               
 $sel_pr_proc='
SELECT 
       os.dat_od
     , sum(os.ong) AS summa
     , f.id AS firm_id


FROM
  pl_dlpb dl
JOIN pl_dmpb dm ON dm.eos_dlr = dl.eos_dlr
JOIN rep_acc_ost os ON os.bsc_acc = dm.bdm_t_acc AND os.dat_od = (SELECT dat_od_last
                                              FROM
                                                _odb)
JOIN spr_firms f ON f.okpo = dl.egf_crf

WHERE
  dl.bdl_rat > 0 
  GROUP BY f.id
 
       ';     
$response_paym_2067=$DB->select($sel_pr_proc);



foreach($response_paym_2067 as $val) {
    $summa = round($val['summa']*48/360,2);
    $firm_id = $val['firm_id'];
    $date = explode('-',$val['dat_od']);
   
    $sel = 'INSERT table_prosrochka(
                                     firm_id,
                                     summa,
                                     dat_od )
                                     
                                     VALUES ('.$firm_id.',"'.$summa.'","'.$date[0].$date[1].$date[2].'")';
   $DB->query($sel);

}

     //    var_dump($sel); die();
         
         
// var_dump($sel); die();


$sel_proc_ob = 'SELECT  sum(summa) AS sum_ob,
                        firm_id,
                        dat_od
                FROM table_prosrochka GROUP BY firm_id';
               
$response_paym_ob=$DB->select($sel_proc_ob);  //выбираем все записи по временной таблице из расчета сумм остатков по счетам 2067 2062 2063             

//var_dump($response_paym_ob);die();

$tran='TRUNCATE TABLE table_prosrochka';
$sel=$DB->query($tran);    // очистка временной таблицы

// var_dump($response_paym_ob); die();


foreach($response_paym_ob as $val){

    $summa=$val['sum_ob'];
    $firm_id=$val['firm_id'];
    list($y,$m,$d) = explode('-',$val['dat_od']);

  $sel='INSERT ps_data (
                                  firm_id,
                                  stat_id,
                                  ps_year,
                                  ps_month,
                                  day14,
                                  date_mode
                    )

                    VALUES ( '.$val['firm_id'].',22,' . $y . ',' . $m . ',
                           "'.$val['sum_ob'].'",
                           NOW()) ON DUPLICATE KEY  UPDATE
                           day'.(int)$d.'='. $val['sum_ob'] ;
                           var_dump($val['firm_id']. ' '.$y.$m.$d);

                    // var_dump($sel);die(); 
                  
                    $DB->query($sel);                    
              

 }           

                    if ($sel) $resp['status'] = 'OK';                  
                      

    echo json_encode($resp);

?>
