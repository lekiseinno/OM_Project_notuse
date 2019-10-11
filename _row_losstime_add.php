<tr>
	<td colspan="2">
		<select name="losstime_cause[]" class="form-control selectpicker my-select" data-live-search="true">
<?php 
	require_once '_process/connect.php'; 
	$sql = "SELECT * FROM losstimeCauseLine LEFT JOIN losstimeCauseHead ON losstimeCauseLine.cause_head_id = losstimeCauseHead.cause_head_id ";
	$query = sqlsrv_query($connect, $sql) or die($sql);

    while($row = sqlsrv_fetch_array($query,SQLSRV_FETCH_ASSOC)) { 
?>
	<option value="<?php echo $row['cause_head_id'].','.$row['cause_line_id'] ?>"><?php echo $row['cause_head_name'].' : '.$row['cause_line_name'] ?></option>
	
<?php } ?>
		</select>
	</td>
	<td><input type="text" name="losstime_minute[]" class="form-control"></td>
</tr>

<script type="text/javascript">$('.my-select').selectpicker();</script>