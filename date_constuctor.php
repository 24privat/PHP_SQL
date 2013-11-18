     $ts = mktime(0,0,0,date('m')+1,date('d'),date('Y'));
     $day = date('d',$ts);       // вытаскивает из параметра таймстам нужный нам день
     $month = date('m',$ts);     // вытаскивает из параметра таймстам нужный нам месяц
     $year = date('Y',$ts);     // вытаскивает из параметра таймстам нужный нам месяц
     $date = date('Ymd',$ts);     // соединение параметров
     
     $ts_now = mktime(0,0,0,date('m'),date('d'),date('Y'));
     $day_now = date('d',$ts_now);       // вытаскивает из параметра таймстам нужный нам день
     $month_now = date('m',$ts_now);     // вытаскивает из параметра таймстам нужный нам месяц
     $year_now = date('Y',$ts_now);
     $date_now = date('Ymd',$ts_now);

 //var_dump($dateday);die();
 $sel='    SELECT   SUM(dl.summa) AS need_r_p_plan
            FROM
                   spr_firms sf
            JOIN   ps_contr dl ON  dl.firm_id=sf.id 
            WHERE
                    sf.okpo="'.$firm['okpo'].'" AND dl.ps_month BETWEEN  "'.$month_now.'"  AND  "'.$month.'" AND dl.ps_year ="'.$year_now.'" AND dl.day> "'.$day_now.'" '
              ;

      //  var_dump($sel);die();
        $aa=$DB->select($sel);
        



     $ts = mktime(0,0,0,date('m')-1,date('d'),date('Y'));
     $day = date('d',$ts);       // вытаскивает из параметра таймстам нужный нам день
     $month = date('m',$ts);     // вытаскивает из параметра таймстам нужный нам месяц
     $year = date('Y',$ts);     // вытаскивает из параметра таймстам нужный нам месяц
     $date = date('Ymd',$ts);     // соединение параметров
     
     $ts_now = mktime(0,0,0,date('m'),date('d'),date('Y'));
     $day_now = date('d',$ts_now);       // вытаскивает из параметра таймстам нужный нам день
     $month_now = date('m',$ts_now);     // вытаскивает из параметра таймстам нужный нам месяц
     $year_now = date('Y',$ts_now);
     $date_now = date('Ymd',$ts_now);
     


 //var_dump($dateday);die();
 $sel='   SELECT   SUM(dl.summa) AS need_f_p
            FROM
                   spr_firms sf
            JOIN   plpb dl ON  dl.okpo_b=sf.okpo  
            WHERE
                    sf.okpo="'.$firm['okpo'].'" AND  dl.nazn  LIKE "fp%"  AND dl.dat BETWEEN  "'.$date.'"  AND  "'.$date_now.'"'
              ;

    //   var_dump($sel);die();
        $aa=$DB->select($sel);        
