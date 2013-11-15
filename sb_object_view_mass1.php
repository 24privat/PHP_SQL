<?php
require_once '../../sess.inc';
$user_id = @$_SESSION['auth']['uid'];

try{
    $curPage = $_POST['page'];
    $rowsPerPage = $_POST['rows'];
    $sortingField = $_POST['sidx'];
    $sortingOrder = $_POST['sord'];
    $join='';

    $qWhere = ' where otv.gps=0 and grp="AH" and otv.state="a" ';

    //определяем команду (поиск или просто запрос на вывод данных)
    //если поиск, конструируем WHERE часть запроса
    if (isset($_POST['_search']) && $_POST['_search'] == 'true') {
        $allowedFields = array( 'otv.name','ot.fio');
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

    
    }


    //определяем количество записей в таблице
    $sel='
            select
            count(id) as count
            FROM
            spr_firms   otv

         '.$qWhere;

    $totalRows = $DB->select($sel);
//var_dump($sel);die();

    $firstRowIndex = $curPage * $rowsPerPage - $rowsPerPage;


    $response->page = $curPage;
    @$response->total = ceil($totalRows[0]['count'] / $rowsPerPage);
    @$response->records = $totalRows[0]['count'];

 $sel='
        SELECT
            otv.name,
            otv.id,
            ot.fio


        FROM
            spr_firms   otv
      LEFT JOIN sb_phone ot ON ot.firm_id=otv.id


             '.$qWhere.'

          ORDER BY '.$sortingField.' '.$sortingOrder.' LIMIT '.$firstRowIndex.', '.$rowsPerPage;
//var_dump($sel);die();

         $rows = $DB->select($sel);

//var_dump($rows);die();

      foreach($rows as $i=>$row){
         //$name=explode(' ',$row['name_ts']);

          $response->rows[$i]['id']=$row['id'];
        $response->rows[$i]['cell']=array(
                                           $row['name']
                                          ,$row['fio']


                  );
      }


    echo json_encode($response);
}
catch (Exception $e) {
    var_dump($e->getMessage());
    echo json_encode(array('errMess'=>'Error: '.$e->getMessage())); die();
}

