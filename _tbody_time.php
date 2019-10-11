
<?php require_once '_process/connect.php'; ?>

<?php 
	$plan_date=$_POST['plan_date'];
	$machine_id=$_POST['machine_id'];

	$sql = "SELECT
				planning.id,
				planning.prod_order_no,
				planning.production_time,
				planning.production_minute,
				SUM(losstime.losstime_minute)AS sum_losstime,
				orderStartStopTime.order_start_time,
				orderStartStopTime.order_end_time
			FROM
				planning
			LEFT JOIN losstime ON planning.id = losstime.planning_id
			LEFT JOIN orderStartStopTime ON planning.prod_order_no = orderStartStopTime.prod_order_no
			WHERE
				planning.plan_date = '$plan_date'
			AND planning.machine_id = '$machine_id'
			GROUP BY
				planning.id,
				planning.prod_order_no,
				planning.production_time,
				planning.production_minute,
				orderStartStopTime.order_start_time,
				orderStartStopTime.order_end_time";

    $query = sqlsrv_query($connect, $sql) or die($sql);
    $arr_data=array();
    $count=1;
    while($row = sqlsrv_fetch_array($query,SQLSRV_FETCH_ASSOC)) { 

    	$prod_order_no=$row['prod_order_no'];


    	$production_time=date('d/m/Y',strtotime($plan_date)).' '.str_pad(number_format($row['production_time'],2),5,"0",STR_PAD_LEFT);
    	$production_time = str_replace('.', ':', $production_time);

    	$production_minute=number_format($row['production_minute'],0);
    	$production_end_time = date('d/m/Y H:i',strtotime("+$production_minute minutes",strtotime("$production_time")));
    	

    	$losstime__zero="style='color:black'";
	    if($row['sum_losstime']==0){
	    	$losstime__zero="style='color:red'";

	    }
?>
	<tr class="form-losstime">
		<td class="prodtxt" <?php echo $losstime__zero ?> nowrap ><?php echo $prod_order_no ?></td>
		<!--<td><?php echo $production_time ?></td>
		<td><?php echo $production_end_time ?></td>
		<td><?php if($row['order_start_time']!=null) echo $row['order_start_time']->format('d/m/Y H:i') ?></td>
		<td><?php if($row['order_end_time']!=null) echo $row['order_end_time']->format('d/m/Y H:i') ?></td>
		<td></td>
		<td></td>-->
		<td><?php echo $row['sum_losstime'] ?></td>
		<td><button type="button" onclick="modal_losstime(<?php echo $row['id'] ?>,'<?php echo $prod_order_no ?>')" class="btn btn-sm btn-info">losstime <i class="fa fa-search"></i></button></td>
	</tr>
<?php $count++;} ?>

<?php sqlsrv_close($connect) ?>