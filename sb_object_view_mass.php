<?php
require_once '../../sess.inc';
$user_id = @$_SESSION['auth']['uid'];
$firm_id=(int)$_GET['firm_id'];


try{
    $curPage = $_POST['page'];
    $rowsPerPage = $_POST['rows'];
    $sortingField = $_POST['sidx'];
    $sortingOrder = $_POST['sord'];
    
        
    if (isset($_POST['_search']) && $_POST['_search'] == 'true') {
        $allowedFields = array( 'pla.id', 'pla.name_place','pla.name_object','pla.notes');
        $allowedOperations = array('AND', 'OR');
        //if(@$tree!='') $tree=' and tree_id ='.$tree;

        $searchData = json_decode($_POST['filters']);

        //ограничение на количество условий
        if (count($searchData->rules) > 5) {
            throw new Exception('Много условий ( >5 )');
            die();
        }

        
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
                //$left='';

                switch ($rule->op) {
                    case 'eq': $qWhere .= $rule->field.' = "'.$rule->data.'"'; break;
                    case 'ne': $qWhere .= $rule->field.' <> "'.$rule->data.'"'; break;
                    case 'bw': $qWhere .= $rule->field.' LIKE "'.$rule->data.'%"'; break;
                    case 'cn': $qWhere .= $rule->field.' LIKE '.'"%'.$rule->data.'%"'; break;
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
    $sel='  SELECT
            count(pla.id) as count
            FROM  sb_spr_place pla';   // дописать вере!!!!!!!!!!!!!!! приравнять к ай ди 

    $totalRows = $DB->select($sel);
//var_dump($sel);die();

    $firstRowIndex = $curPage * $rowsPerPage - $rowsPerPage;

//var_dump($firstRowIndex);die();
    $response->page = $curPage;
    @$response->total = ceil($totalRows[0]['count'] / $rowsPerPage);
    @$response->records = $totalRows[0]['count'];

 $sel='
    SELECT
   pla.id
  ,pla.name_place
  ,pla.name_object
  ,pla.notes

  from
     sb_spr_place  pla    WHERE  firm_id=' .$firm_id.';

          ORDER BY '.$sortingField.' '.$sortingOrder.' LIMIT '.$firstRowIndex.', '.$rowsPerPage;
//var_dump($sel);die();

         $rows = $DB->select($sel);

//var_dump($rows);die();

      foreach($rows as $i=>$row){
      
        $response->rows[$i]['id']=$row['id'];//.'_'.$row['ul_id'];
        $response->rows[$i]['cell']=array(
                                           $row['id']
                                          ,$row['name_place']
                                          ,$row['name_object']
                                          ,$row['notes']


                  );
      }


    echo json_encode($response);
}
catch (Exception $e) {
    var_dump($e->getMessage());
    echo json_encode(array('errMess'=>'Error: '.$e->getMessage())); die();
}

