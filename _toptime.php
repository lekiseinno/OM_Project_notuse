<?php 
	require_once '_process/connect.php';
	$machine_id=$_POST['machine_id'];
	$sql = "SELECT TOP 10 * FROM orderStartStopTime WHERE machine_id = '$machine_id' ORDER BY id DESC";
    $query = sqlsrv_query($connect, $sql) or die($sql);

 ?>
<style type="text/css">
td{font-size: 20px;color: white;text-align: center;}
thead{font-size: 20px;text-align: center}
</style>
 <div class="table-responsive">
	<table class="table table-bordered table-hover table-striped">
		<thead>
			<tr>
				<th >Order</th>
				<th >start</th>
				<th >stop</th>
			</tr>
		</thead>
		<tbody>
<?php while($row = sqlsrv_fetch_array($query,SQLSRV_FETCH_ASSOC)) {  ?>
			<tr>
				<td><?php echo $row['prod_order_no'] ?></td>
				<td><?php echo $row['order_start_time']->format('d/m/Y H:i') ?></td>
				<td><?php if($row['order_end_time']!='') echo $row['order_end_time']->format('d/m/Y H:i') ?></td>
			</tr>
<?php } ?>
		</tbody>
	</table>
</div>
<?php sqlsrv_close($connect); ?>