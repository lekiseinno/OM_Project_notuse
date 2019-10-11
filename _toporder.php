<?php 
	require_once '_process/connect.php';
	require_once('_barcode/lib/BarcodeGenerator.php');
	require_once('_barcode/lib/BarcodeGeneratorPNG.php');
	require_once('_barcode/lib/BarcodeGeneratorSVG.php');
	require_once('_barcode/lib/BarcodeGeneratorJPG.php');
	require_once('_barcode/lib/BarcodeGeneratorHTML.php');
	require_once('_barcode/barcode.php');

	$generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
	$machine_id=$_POST['machine_id'];
	$cate=$_POST['cate'];
	if($cate=='start'){
		$sql = "SELECT
				TOP 5 planning.prod_order_no
			FROM
				planning
			WHERE
				planning.machine_id = '$machine_id'
			AND planning.prod_order_no NOT IN(
				SELECT
					prod_order_no
				FROM
					orderStartStopTime
				WHERE
					machine_id = '$machine_id'
			)
			ORDER BY
				planning.id DESC";

	}else{
		$sql = "SELECT
				TOP 5 planning.prod_order_no
			FROM
				planning
			WHERE
				planning.machine_id = '$machine_id'
			AND planning.prod_order_no  IN(
				SELECT
					prod_order_no
				FROM
					orderStartStopTime
				WHERE
					machine_id = '$machine_id'
				AND
					order_end_time is null
			)
			ORDER BY
				planning.id DESC";


	}
	

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
				<th >Barcode</th>
			</tr>
		</thead>
		<tbody>
<?php while($row = sqlsrv_fetch_array($query,SQLSRV_FETCH_ASSOC)) {  ?>
			<tr>
				<td style="vertical-align: middle;"><?php echo $row['prod_order_no'] ?></td>
				<td style="background-color: white"><img style="height: 65px; width: 70%; margin-top: 10px" src="data:image/png;base64,<?php echo base64_encode($generator->getBarcode($row['prod_order_no'], $generator::TYPE_CODE_128)); ?>" alt=""></td>

			</tr>
<?php } ?>
		</tbody>
	</table>
</div>
<?php sqlsrv_close($connect); ?>