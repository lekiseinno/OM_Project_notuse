<?php include("include/head.php"); ?>
<body>
	<div class="wrapper">
    <?php //include("include/side_bar.php"); ?>
	<div class="container-fluid">
<?php 
		$pdr = array("0006","0221","0222","1065","1066","0455","1340","0110","0156","0128","0129","0130","0131","0132","0197","0099","0263","0295");
		$name = array("name","name","name","name","name","name","name","name","name","name","name","name","name","name","name","name","name","name");
		$name_box = array("name_box","name_box","name_box","name_box","name_box","name_box","name_box","name_box","name_box","name_box","name_box","name_box","name_box","name_box","name_box","name_box","name_box","name_box");
		$time = array("10","20","30","40","50","60","10","20","30","40","50","60","10","20","30","40","50","60");
		$real_time = array("10","20","30","40","55","60","10","20","30","40","55","60","10","20","30","40","55","60");
		foreach ($time as $key => $value) {
			$late_time[] = $value-$real_time[$key];
		}
		$time_null = array("","","","","","","","","","","","","","","","","","");
		$time_caution = array("","","","","","","15","","","","","","","","","","","");
		$time_set_plan = array("15","15","15","","15","15","","15","15","15","","15","15","15","","15","15","15");
		$time_set_real = array("15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15");
		$caution = array("","","","","","","บล็อคอ้า","","","","","","","","","","","");
		$img = array("","","","","","","box.jpg","","","","","","","","","","","");
		$analyze = array("","","","","","","","","","","","","","","","","","");
		$demand = array("3000","2000","2000","1000","1000","10000","550","2500","1000","1000","1000","1000","1000","1000","320","600","360","2000");
		$paper_quantity = array("3003","2003","2003","1003","1003","10005","557","2503","1005","1005","1005","1005","1005","1005","323","605","370","2005");
		$input = array("3003","2003","2003","1003","1003","10005","557","2503","1005","1005","1005","1005","1005","1005","323","605","370","2005");
		$output = array("3000","2000","2000","1000","1000","10000","550","2500","1000","1000","1000","1000","1000","1000","320","600","360","2000");
		foreach ($input as $key => $value) {
			$waste_paper[] = $value-$output[$key];
		}
	?>
	<h5>Test Report</h5>
	<div class="overflow">
	<table border="1" class="table table-bordered">
		<thead>
			<tr bgcolor="LimeGreen">
				<th>ลำดับ</th>
				<th>PDR</th>
				<th>ชื่อลูกค้า</th>
				<th>ชื่อกล่อง</th>
				<th>เวลาแผน(นาที)</th>
				<th>เวลาแผน(นาที)</th>
				<th>เวลาเดินจริง(นาที)</th>
				<th>เลทแผน(นาที)</th>
				<th>ผลิตเลท(นาที)</th>
				<th>เวลาสูญเสียกระดาษ(นาที)</th>
				<th>เวลาสูญเสียบล็อก(นาที)</th>
				<th>เวลาสูญเสียสี(นาที)</th>
				<th>เวลาสูญเสียเครื่องจักร(นาที)</th>
				<th>เวลาสูญเสียอื่นๆ(นาที)</th>
				<th>เวลาสูญเสียรวม</th>
				<th>Set up Time ตามแผน(นาที)</th>
				<th>Set up Time เดินงานจริง(นาที)</th>
				<th>สาเหตุเลทแผน</th>
				<th>รูปภาพ</th>
				<th>วิเคราะห์/แนวทางการแก้ไข</th>
				<th>จำนวนต้องการ</th>
				<th>จำนวนที่กระดาษเข้า(PO+VA)</th>
				<th>input(แผ่น)</th>
				<th>output(แผ่น)</th>
				<th>ของเสีย(แผ่น)</th>
			</tr>
		</thead>
		<tbody>
			<?php 
				$i=1;
				foreach ($pdr as $row => $id) {
			?>
			<tr>
				<td><?php echo $i ?></td>
				<td><?php echo $id ?></td>
				<td><?php echo $name[$row]."_".$i ?></td>
				<td><?php echo $name_box[$row]."_".$i ?></td>
				<td><?php echo $time[$row] ?></td>
				<td><?php echo $time[$row] ?></td>
				<td><?php echo $real_time[$row] ?></td>
				<td><?php echo $late_time[$row] ?></td>
				<td><?php echo $time_null[$row] ?></td>
				<td><?php echo $time_null[$row] ?></td>
				<td><?php echo $time_caution[$row] ?></td>
				<td><?php echo $time_null[$row] ?></td>
				<td><?php echo $time_null[$row] ?></td>
				<td><?php echo $time_null[$row] ?></td>
				<td><?php echo $time_caution[$row] ?></td>
				<td><?php echo $time_set_plan[$row] ?></td>
				<td><?php echo $time_set_real[$row] ?></td>
				<td><?php echo $caution[$row] ?></td>
				<?php if($img[$row]==""){ ?>
				<td></td>
				<?php }else{ ?>	
				<td><img src="picture/<?php echo $img[$row] ?>" width="50px"></td>
				<?php } ?>
				<td><?php echo $analyze[$row] ?></td>
				<td><?php echo $demand[$row] ?></td>
				<td><?php echo $paper_quantity[$row] ?></td>
				<td><?php echo $input[$row] ?></td>
				<td><?php echo $output[$row] ?></td>
				<td><?php echo $waste_paper[$row] ?></td>
			</tr>
			<?php $i++;} ?>
			<tr>
				<td colspan="4"></td>
				<td><?php echo array_sum($time); ?></td>
				<td><?php echo array_sum($time); ?></td>
				<td><?php echo array_sum($real_time); ?></td>
				<td><?php echo array_sum($late_time); ?></td>
				<td><?php echo array_sum($time_null); ?></td>
				<td><?php echo array_sum($time_null); ?></td>
				<td><?php echo array_sum($time_caution); ?></td>
				<td><?php echo array_sum($time_null); ?></td>
				<td><?php echo array_sum($time_null); ?></td>
				<td><?php echo array_sum($time_null); ?></td>
				<td><?php echo array_sum($time_caution); ?></td>
				<td><?php echo array_sum($time_set_plan); ?></td>
				<td><?php echo array_sum($time_set_real); ?></td>
				<td colspan="3"></td>
				<td><?php echo array_sum($demand); ?></td>
				<td></td>
				<td><?php echo array_sum($input); ?></td>
				<td><?php echo array_sum($output); ?></td>
				<td><?php echo array_sum($waste_paper); ?></td>
			</tr>
			<tr>
				<td colspan="22"></td>
				<td colspan="2">เปอร์เซ็นต์ของเสีย</td>
				<td><?php echo round(count($waste_paper)/array_sum($waste_paper),2); ?></td>
			</tr>
		</tbody>
	</table>
	</div>
 </div>
</div>
</body>
<?php include("include/footer.php"); ?>