<?php
class connect_db{

    function  __construct() {
        $db_name="aghold";
        $connection = mysql_connect($_SESSION['ah']['server'],$_SESSION['ah']['login'],$_SESSION['ah']['pass'],0,65536)
//	$connection = mysql_connect('localhost','root','',0,65536)
        or die("Could not connect: " . mysql_error());
        $qry = mysql_query("SET NAMES 'utf8'",$connection) or die(mysql_error());
	//ini_set('default_charset', 'UTF-8');
	//mysql_query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
        $db = mysql_select_db($db_name, $connection);
    }

    public function query($query) {
	$result=mysql_query($query);
	if (!$result){
	    die('Query error '.mysql_error());
	}

	return $result;
    }
    
    public function lastInsertId() {
	return mysql_insert_id();
    }

    public function select($query) {
	$qry=mysql_query($query);
	if (!$qry){
	    die('Query error '.mysql_error());
	}

	$result=array();
	while ($rez = mysql_fetch_assoc($qry)){
	    $result[]=$rez;
	}
        return $result;
    }
     
    public function affectedRows() {
	return mysql_affected_rows();
    }

    public function queryEX($query) {
	@$dbEX = sybase_connect("EXPERT",$_SESSION['ah']['loginE'],$_SESSION['ah']['passE'],"cp866");
	$qry=sybase_query($query,$dbEX);
        if (!$qry){
            die('Query error '.$qry);
        }

        return $qry;
    }
    
    public function selectEX($query) {
	@$dbEX = sybase_connect("EXPERT",$_SESSION['ah']['loginE'],$_SESSION['ah']['passE'],"cp866");
	$qry=sybase_query($query,$dbEX);
        if (!$qry){
            die('Query error '.$qry);
        }


	$result=array();
	while ($rez = sybase_fetch_assoc($qry)){
	    $result[]=$rez;
	}
        return $result;
    }
}
$mysql_connect = new connect_db();
$DB = new connect_db;

class connect_db1{

    function  __construct() {
        $db_name="aghold";
        $connection = mysql_connect($_SESSION['ah']['server'],$_SESSION['ah']['login'],$_SESSION['ah']['pass'],0,65536)
        or die("Could not connect: " . mysql_error());
        $qry = mysql_query("SET NAMES 'utf8'",$connection) or die(mysql_error());
        $db = mysql_select_db($db_name, $connection);
    }

     public function query1($query) {
            $result=mysql_query($query);
            if (!$result){
                die('Query error '.mysql_error());
            }

            return $result;
    }

     public function select1($query) {
            $qry=mysql_query($query);
            if (!$qry){
                die('Query error '.mysql_error());
            }

      $result=array();
		while ($rez = mysql_fetch_assoc($qry))
		{ $result[]=$rez;
		}
            return $result;
    }

}
/*
class connect_pg{

    function  __construct() {
        $db_name="candelaprivat";
        $connection = pg_connect("host='192.168.254.2' dbname='candelaprivat' user='user1c' password='user@1c'")
        or die("Could not connect: " . mysql_error());
        //$qry = mysql_query("SET NAMES 'utf8'",$connection) or die(mysql_error());
        //$db = mysql_select_db($db_name, $connection);
    }

     public function queryPG($query) {
            $result=pg_query($query);
            if (!$result){
                die('Query error pg_query');
            }

            return $result;
    }

     public function select($query) {
            $qry=pg_query($query);
            if (!$qry){
                die('Query error pg_select');
            }

      $result=array();
		while ($rez = pg_fetch_assoc($qry))
		{ $result[]=$rez;
		}
            return $result;
    }

}
$pg_connect = new connect_pg();
$PG = new connect_pg;
 */

?>
