
<?php
		require_once 'connect.php';

		$prod_order_no = trim($_POST['prod_order_no']);
        $machine_id = trim($_POST['machine_id']);
		$order_end_time = date('Y-m-d H:i:s');

		
		$sql = "SELECT * FROM orderStartStopTime WHERE prod_order_no='$prod_order_no' AND machine_id='$machine_id'";
   		$query = sqlsrv_query($connect, $sql) or die($sql);

    	$rs = sqlsrv_fetch_array($query,SQLSRV_FETCH_ASSOC);


    	if($rs['prod_order_no']!=''&&$rs['order_start_time']!=''&&$rs['order_end_time']==null){	

    		$sql = "UPDATE orderStartStopTime SET order_end_time = '$order_end_time' WHERE prod_order_no = '$prod_order_no' AND machine_id='$machine_id'";
    		$query = sqlsrv_query($connect, $sql) or die($sql);

            if($query)echo 1;
    		else echo 0;

    	}else{
    		echo 0;
    	}
			sqlsrv_close($connect);
?>



