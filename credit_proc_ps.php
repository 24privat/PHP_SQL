<?php
require_once '../../../sess.inc';
// require_once 'sess.inc';

$user_id = 0;

 $sel_proc='
SELECT
       os.dat_od
     , os.ong  AS summa
     , f.id AS firm_id
     , dm.eos_dlr
     , f.name
     , f.okpo
     , dl.bos_nd
     , dm.bdm_d_acc


FROM
  pl_dlpb dl
JOIN pl_dmpb dm ON dm.eos_dlr = dl.eos_dlr
JOIN rep_acc_ost os ON os.bsc_acc = dm.bdm_d_acc AND os.dat_od = (SELECT MAX(dat_od) FROM rep_acc_ost)
JOIN spr_firms f ON f.okpo = dl.egf_crf

WHERE
  dl.bdl_rat > 0
  GROUP BY bdm_d_acc

       ';

$response_paym_2062=$DB->select($sel_proc);
 //  var_dump($response_paym_2062);


foreach($response_paym_2062 as $val) {
    $summa = round($val['summa']*12/360/100*(-1)*30,2);
    $firm_id = $val['firm_id'];
    list($y,$m,$d) = explode('-',$val['dat_od']);
    
    
     $data=$DB->select('select 1 from ps_data where firm_id='.$firm_id.' and stat_id=22 and ps_year='.
                                                $y.' and ps_month='.$m);

                if(!isset($data[0]))
                        $DB->query('insert ps_data (firm_id,stat_id,ps_year,ps_month) select '.$firm_id.',22,'.
                                                $y.','.$m);
    


    $sel='select id from ps_contr where  firm_id='.$firm_id.' and stat_id=22 and ps_year='.
                                                $y.' and day='.(int)$d.' and ps_month='.$m.' and eos_dlr="'.TRIM($val['eos_dlr']).'"';
    $res=$DB->select($sel);

    $osnov='Погашение процентов по договору '.TRIM($val['bos_nd']);

    if(!isset($res[0]['id'])){
     $sel = 'insert ps_contr(
                  name,
                  okpo,
                  summa,
                  ps_type_id,
                  osnov,
                  day,
                  user_id,
                  firm_id,
                  stat_id,
                  ps_year,
                  ps_month,
                  eos_dlr
                  )
                  select
                     "'.$val['name'].'","'.$val['okpo'].'",'.(int)$summa.',1,"'.$osnov.'",14,-1'.
                      ','.$firm_id.',22,'.$y.','.$m.',"'.TRIM($val['eos_dlr']).'"';
    }else{
        $sel=' UPDATE ps_contr SET summa='.(int)$summa.' WHERE id='.$res[0]['id'];
    }

      $DB->query($sel);
}
//echo "Данные внесены в таблицу contr"; die();

 $sel_pr_proc='
SELECT
       os.dat_od
     , os.ong  AS summa
     , f.id AS firm_id
     , dm.eos_dlr
     , f.name
     , f.okpo
     , dl.bos_nd
     , dm.bdm_t_acc


FROM
  pl_dlpb dl
JOIN pl_dmpb dm ON dm.eos_dlr = dl.eos_dlr
JOIN rep_acc_ost os ON os.bsc_acc = dm.bdm_t_acc AND os.dat_od = (SELECT MAX(dat_od) FROM rep_acc_ost)
JOIN spr_firms f ON f.okpo = dl.egf_crf

WHERE
  dl.bdl_rat > 0
  GROUP BY bdm_t_acc

       ';
$response_paym_2067=$DB->select($sel_pr_proc);

//var_dump($response_paym_2067);

foreach($response_paym_2067 as $val) {
    $summa_2067 = round($val['summa']*48/360/100*(-1)*30,2);
    $firm_id = $val['firm_id'];
    $eos_dlr = trim($val['eos_dlr']);
    list($y,$m,$d) = explode('-',$val['dat_od']);

    $sel='select id from ps_contr where  firm_id='.$firm_id.' and stat_id=22 and ps_year='.
                                                $y.' and `day`=14 and ps_month='.$m.' and eos_dlr="'.$eos_dlr.'"';
        //var_dump($sel); echo '<br>'; continue;
    $res=$DB->select($sel);

  //   var_dump($res); die();

    if(isset($res[0]['id'])){
    $sel_update=' UPDATE ps_contr SET `summa`=`summa`+'.(int)$summa_2067.' WHERE id='.$res[0]['id'];
      
     //  var_dump($sel_update);die();

       $response=$DB->query($sel_update);
       }

 //echo "Cумма в таблице contr увеличена на сумму процента по просрочке";  die();


     $sel='update ps_data set day14=(select ROUND(sum(summa),0) from ps_contr where firm_id='.$firm_id.'
                                    and stat_id=22 and ps_year='.$y.' and ps_month='.$m.'
                                    and status=0 and day=14)

                                    where firm_id='.$firm_id.'
                                    and stat_id=22 and ps_year='.$y.' and ps_month='.$m;
      

       $DB->query($sel);
       
     //  var_dump($sel); echo '<br>'; continue;
       
       
//echo "Данные  в таблице ps_data изменены";  die();

}



?>
