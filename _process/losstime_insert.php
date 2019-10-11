
<?php
		require_once 'connect.php';

		$planning_id = trim($_POST['planning_id']);


		foreach ($_POST['losstime_minute'] as $key => $losstime_minute) {
			$losstime_cause=explode(',', $_POST['losstime_cause'][$key]);
			$cause_head_id = $losstime_cause[0];
			$cause_line_id = $losstime_cause[1];
			
			$sql = "INSERT INTO losstime(planning_id,losstime_minute,cause_head_id,cause_line_id) VALUES('$planning_id','$losstime_minute','$cause_head_id','$cause_line_id')";
			echo $sql;
			$query = sqlsrv_query($connect, $sql) or die($sql);
		}
			sqlsrv_close($connect);
?>



