<?php
include("class/connect.php");
$class_con = new Sqlsrv();
$class_con->getConnect();
// Select
$query=$class_con->getQuery("
SELECT * FROM Table
");
while($result=$class_con->getResult($query)){
    $Test_01 = $result["Test_01"];
    $Test_02 = $result["Test_02"];
}
// Insert

?>