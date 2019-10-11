
<?php
		require_once 'connect.php';
        $machine_id = trim($_POST['machine_id']);
		$prod_order_no = trim($_POST['prod_order_no']);
		$order_start_time = date('Y-m-d H:i:s');

		
		$sql = "SELECT COUNT(prod_order_no) AS count_order FROM orderStartStopTime WHERE prod_order_no='$prod_order_no' AND machine_id='$machine_id'";
   		$query = sqlsrv_query($connect, $sql) or die($sql);

    	$rs = sqlsrv_fetch_array($query,SQLSRV_FETCH_ASSOC);


    	if($rs['count_order']==0){	

    		$sql = "INSERT INTO orderStartStopTime(prod_order_no,order_start_time,machine_id) VALUES('$prod_order_no','$order_start_time','$machine_id')";
    		$query = sqlsrv_query($connect, $sql) or die($sql);

    		if($query)echo 1;
    		else echo 0;
    		
    	}else{
    		echo 0;
    	}
			sqlsrv_close($connect);
?>



