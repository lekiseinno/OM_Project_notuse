
<?php require_once '_process/connect.php'; ?>

<?php 
	$plan_date=$_POST['plan_date'];
	$machine=$_POST['machine'];
	$sql = "SELECT planning.prod_order_no AS plan_prod_order_no,losstime.*,losstime.prod_order_no AS loss_prod 
			FROM planning  lEFT JOIN losstime ON planning.prod_order_no=losstime.prod_order_no
			WHERE planning.plan_date='$plan_date' AND planning.machine ='$machine'";
    $query = sqlsrv_query($connect, $sql) or die($sql);
    $arr_data=array();
    $count=1;
    while($row = sqlsrv_fetch_array($query,SQLSRV_FETCH_ASSOC)) { 
    	$loss_prod=$row['loss_prod'];

    	$nodata="style='color:black'";
	    if($row['loss_prod']==''){
	    	$nodata="style='color:red'";

	    }
?>
	<tr class="form-losstime<?php echo $count ?>">
		<input type="hidden" name="<?php echo $count ?>image_old" value="<?php $row['image'] ?>">
		<input type="hidden" class="<?php echo $count ?>prod_order_no" name="<?php echo $count ?>prod_order_no" value="<?php echo $row['plan_prod_order_no']?>">
		<input type="hidden" class="<?php echo $count ?>loss_prod" name="<?php echo $count ?>loss_prod" value="<?php echo $loss_prod?>">
		<td class="prodtxt" <?php echo $nodata ?> nowrap ><?php echo $row['plan_prod_order_no'] ?></td>
		<td><input type="text" name="<?php echo $count ?>production_time_late" value="<?php echo $row['production_time_late'] ?>"></td>
		<td><input type="text" name="<?php echo $count ?>losstime_paper" value="<?php echo $row['losstime_paper'] ?>"></td>
		<td><input type="text" name="<?php echo $count ?>losstime_block" value="<?php echo $row['losstime_block'] ?>"></td>
		<td><input type="text" name="<?php echo $count ?>losstime_color" value="<?php echo $row['losstime_color'] ?>"></td>
		<td><input type="text" name="<?php echo $count ?>losstime_machine" value="<?php echo $row['losstime_machine'] ?>"></td>
		<td><input type="text" name="<?php echo $count ?>losstime_other" value="<?php echo $row['losstime_other'] ?>"></td>
		<td><input type="text" name="<?php echo $count ?>cause_production_time_late" value="<?php echo $row['cause_production_time_late'] ?>"></td>
		<td><input type="file" name="<?php echo $count ?>image" ></td>
		<td><input type="text" name="<?php echo $count ?>solutions" value="<?php echo $row['solutions'] ?>"></td>
		<td class="load-rec<?php echo $count ?>"><button type="button" class="btn btn-sm btn-primary" onclick="losstime_record(<?php echo $count ?>)">Record</button><i style="display:none;height: 26px !important" class="fa fa-spinner"></i></td>

	</tr>


<?php $count++;} ?>




<?php sqlsrv_close($connect) ?>