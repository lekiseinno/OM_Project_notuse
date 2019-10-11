
<?php


require_once 'connect.php';

require_once '../_lib_excel.php';




#echo '<h1>Read several sheets</h1>';
if ( $xlsx = SimpleXLSX::parse('../_file.xlsx')) {

	#echo '<pre>'.print_r( $xlsx->sheetNames(), true ).'</pre>';

	$dim = $xlsx->dimension();
	$num_cols = $dim[0];
	$num_rows = $dim[1];

	$array_col =array(0,3,4,7,8,9,10,11,12,13,14,15,16,17,18,20,21,23,24,25,26,27,31,32,33,34,35,36,37);
	$machine_id = $_POST['machine_id'];
	$plan_date = $_POST['plan_date'];
	$sheets=$_POST['sheets'];

	#echo '<h2>'.$xlsx->sheetName(0).'</h2>';
	echo '<table border=1>';

	$sql = "DELETE FROM [dbo].[planning] WHERE plan_date '$plan_date' AND machine_id = '$plan_date'";

	$query = sqlsrv_query( $connect, $sql ) or die($sql);

	foreach ( $xlsx->rows($sheets) as $key => $r ) {
		if($key>=7&&$r[0]!=''){

			echo '<tr>';
			$sql = "INSERT INTO [dbo].[planning]
				           ([order_no]
				           ,[prod_order_no]
				           ,[so_no]
				           ,[customer_name]
				           ,[product_name]
				           ,[quantity]
				           ,[due_date]
				           ,[color]
				           ,[block_no]
				           ,[plat_no]
				           ,[block_old]
				           ,[block_new]
				           ,[block_edit]
				           ,[production_date]
				           ,[customer_quantity]
				           ,[paper_in]
				           ,[machine_out]
				           ,[lon]
				           ,[paper_grade]
				           ,[width]
				           ,[length]
				           ,[production_minute]
				           ,[production_time]
				           ,[sent_to]
				           ,[forward]
				           ,[speed]
				           ,[change_order_time]
				           ,[mud]
				           ,[remark]
				           ,[machine_id]
				           ,[plan_date])
     					VALUES(";

			foreach ($array_col as $key2 => $col) {

				$val=$r[$col];
				$sql.="'$val',";
				echo "<td>".$r[$col]."</td>";
			}

			$sql.="'$machine_id','$plan_date')";

			echo '</tr>';

			echo $sql;

			$query = sqlsrv_query( $connect, $sql ) or die($sql);
			
		}
		
	}
	echo '</table>';

	echo '</td><td valign="top">';


	echo "<script type='text/javascript'>alert('upload success');window.location.href='../_plan_upload.php';</script>";

} else {
	echo SimpleXLSX::parseError();
}

?>


