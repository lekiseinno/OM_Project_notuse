
<?php 
	require_once '_process/connect.php'; 
	$planning_id=$_POST['planning_id'];
	$sql = "SELECT losstime.*, losstimeCauseLine.cause_line_name,losstimeCauseHead.cause_head_name
		FROM
			losstime
		LEFT JOIN losstimeCauseLine ON losstime.cause_line_id = losstimeCauseLine.cause_line_id
		LEFT JOIN losstimeCauseHead ON losstime.cause_head_id = losstimeCauseHead.cause_head_id
	 	WHERE losstime.planning_id='$planning_id'";

?>
<form method="post" class="losstime_insert">
<input type="hidden" name="planning_id" value="<?php echo $planning_id ?>">

<table class="table table-bordered">
 	<caption style="caption-side:top" ><button class="btn btn-sm btn-success" type="button" onclick="row_losstime_add()">add</button></caption>
	<tr>
		
		<th>head</th>
		<th>line</th>
		<th>minute</th>
	</tr>
<?php 
 	$query = sqlsrv_query($connect, $sql) or die($sql);

    while($row = sqlsrv_fetch_array($query,SQLSRV_FETCH_ASSOC)) { 
 ?>
 	<tr>	
 		<td><?php echo $row['cause_head_name'] ?></td>
 		<td><?php echo $row['cause_line_name'] ?></td>
 		<td><?php echo $row['losstime_minute'] ?></td>
 	</tr>
<?php } ?>
	<tr class="tr-before"></tr>
</table>
<div align="right">
	<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
	<button type="button"  class="btn btn-primary" onclick="losstime_insert()">Save</button>
</div>
</form>


