<?php
require_once '../../sess.inc';
$user_id = @$_SESSION['auth']['uid'];
$sel="select uf.firm_id as id from spr_users_firms uf
    where uf.user_id=".$user_id." and uf.firm_id=0";
$right=$DB->select($sel);
$isAdmin = $right ? true : false;

@$_POST['filters']=str_replace('\"','"',$_POST['filters']);

    // исключение людей кот. не видят фирмы

// $firm_id = $firm[0]['id'];

if(!isset($right[0]['id'])){
    $sel="
            SELECT a.`id`
                 , a.`name`
            FROM
              spr_users_firms c
            JOIN spr_firms a
            ON a.id = c.firm_id
            AND a.grp = 'AH'
            AND `state` = 'a'
            WHERE
              c.user_id =".$user_id
          ;
}

else{
    $sel="
            SELECT a.`id`, a.`name`
            FROM `spr_firms` a
            WHERE a.grp = 'AH'
                AND `state`='a'
            ORDER BY
                a.`name`;
        ";
}

$firm=$DB->select($sel);

$firm_id = $firm[0]['id'];
$sqWhere = '';

if (!$isAdmin && $firm_id) {
    $sqWhere = " AND `firm_id`=" . $firm_id . " ";
}

try{
    $curPage = $_POST['page'];
    $rowsPerPage = $_POST['rows'];
    $sortingField = $_POST['sidx'];
    $sortingOrder = $_POST['sord'];
    $join='';

    $dat=explode('-',$_POST['dateS']);
    $dateS = $dat[2].$dat[1].$dat[0];
    $dat=explode('-',$_POST['dateF']);
    $dateF = $dat[2].$dat[1].$dat[0];

    $qWhere = ' where sec.datain BETWEEN "'.$dateS.'" and "'.$dateF.'"' . $sqWhere;


    //определяем команду (поиск или просто запрос на вывод данных)
    //если поиск, конструируем WHERE часть запроса
    if (isset($_POST['_search']) && $_POST['_search'] == 'true') {
        $allowedFields = array( 'sec.firm_id', 'sec.object_id',
                                'sec.datain','sec.notes','sec.persona_kod1c');

        $allowedOperations = array('AND', 'OR');
        //if(@$tree!='') $tree=' and tree_id ='.$tree;

        $searchData = json_decode($_POST['filters']);

        //ограничение на количество условий
        if (count($searchData->rules) > 5) {
            throw new Exception('Много условий ( >5 )');
            die();
        }

        $qWhere .= ' and ';
        $firstElem = true;


        //объединяем все полученные условия
        foreach ($searchData->rules as $rule) {
            if (!$firstElem) {
                //объединяем условия (с помощью AND или OR)
                if (in_array($searchData->groupOp, $allowedOperations)) {
                    $qWhere .= ' '.$searchData->groupOp.' ';
                }
                else {
                    //если получили не существующее условие - возвращаем описание ошибки
                    throw new Exception('Условие не существует 1');
                    die();
                }
            }
            else {
                $firstElem = false;
            }
         //  var_dump($rule->field,$allowedFields);
            //вставляем условия
            if (in_array($rule->field, $allowedFields)) {

                switch ($rule->op) {
                    case 'eq': $qWhere .= $rule->field.' = "'.TRIM($rule->data).'"'; break;
                    case 'ne': $qWhere .= $rule->field.' <> "'.$rule->data.'"'; break;
                    case 'bw': $qWhere .= $rule->field.' LIKE "'.$rule->data.'%"'; break;
                    case 'cn': $qWhere .= $rule->field.' LIKE '.'"%'.TRIM($rule->data).'%"'; break;
                    default: throw new Exception('Ошибка создания запроса');
                }

            }
            else {
                //если получили не существующее условие - возвращаем описание ошибки
                throw new Exception('Условие не существует 2'); die();
            }
        }

         //$qWhere .=' ';
    }

    //определяем количество записей в таблице
    $sel='
            select
            count(sec.id) as count
            FROM
            sb_zvit sec
            JOIN sb_spr_object obj ON obj.id=sec.object_id
            JOIN spr_firms sf ON sf.id=sec.firm_id'

            .$qWhere;

    $totalRows = $DB->select($sel);

    $firstRowIndex = $curPage * $rowsPerPage - $rowsPerPage;

    $response = Array();
    $response['page'] = $curPage;
    @$response['total'] = ceil($totalRows[0]['count'] / $rowsPerPage);
    @$response['records'] = $totalRows[0]['count'];


/// вывод на печать

 $sel='
SELECT
       sec.id,
       sf.name,
       obj.name as name_obj,
       sec.datain,
       sec.notes,
       sec.persona_kod1c,
       obj.id as id_obj

    FROM sb_zvit sec
    JOIN sb_spr_object obj ON obj.id=sec.object_id
    JOIN spr_firms sf ON sf.id=sec.firm_id



       '.$qWhere.'
    ORDER BY '.$sortingField.' '.$sortingOrder.' LIMIT '.$firstRowIndex.', '.$rowsPerPage;

//var_dump($sel);die();

         $rows = $DB->select($sel);


      foreach($rows as $i=>$row){

        $response['rows'][$i]['id']=$row['id'];
        $response['rows'][$i]['cell']=array(
                                           $row['name']
                                          ,$row['name_obj']
                                          ,$row['datain']
                                          ,$row['notes']
                                          ,$row['persona_kod1c']
                                     //  ,(bool)$row['sign']?'<center><img src="images/info.gif"></center>':''
                                          ,$row['id_obj']



                  );
      }


    echo json_encode($response);
}
catch (Exception $e) {
    var_dump($e->getMessage());
    echo json_encode(array('errMess'=>'Error: '.$e->getMessage())); die();
}


//
// JOIN spr_firms sf ON sf.id=sec.firm_id
