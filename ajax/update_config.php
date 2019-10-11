<?php 
include("../class/connect.php");
$date_search =  date("Y-d-m", strtotime($_POST["date_plan"]));
$config_machine = "";
$config_date = "";
$class_con_125 = new Sqlsrv();
$class_con_125->getConnect();
// Select
$query=$class_con_125->getQuery("
    SELECT id,config_machine,config_date
    FROM config_oee
    WHERE config_machine = '".$_POST["machine"]."' AND config_date = '".$date_search."'
");
while ($result=$class_con_125->getResult($query)) {
	$id = $result["id"];
	$config_machine = $result["config_machine"];
	$config_date = $result["config_date"];
}
if($config_machine=="" && $config_date==""){
// Insert
	$query=$class_con_125->getQuery("
	    INSERT INTO config_oee (config_machine,config_date,num_hour,meal_break,meeting,five_s,num_paper_for_day)
     	VALUES ('".$_POST["machine"]."','".$date_search."','".$_POST["num_hour"]."','".$_POST["meal_break"]."',
     	'".$_POST["meeting"]."','".$_POST["five_s"]."','".$_POST["num_paper_for_day"]."')
	");
}else{
// Update
	$query=$class_con_125->getQuery("
	    UPDATE config_oee SET config_machine = '".$_POST["machine"]."',config_date = '".$date_search."',num_hour = '".$_POST["num_hour"]."',meal_break = '".$_POST["meal_break"]."',meeting = '".$_POST["meeting"]."',five_s = '".$_POST["five_s"]."',num_paper_for_day = '".$_POST["num_paper_for_day"]."'
		 WHERE id = '".$id."'
	");
}
		if(!$query)
		{
			echo "<script type=\"text/javascript\">";
			echo "alert(\"Record already exist!".'\n'."Please enter again.\");";
			echo "window.history.back();";
			echo "</script>";
			exit();
		}else{
			echo "<script type=\"text/javascript\">";
			echo "alert(\"Save Successfully!<<\");";
			echo "window.history.back();";
			echo "</script>";
		}
?>