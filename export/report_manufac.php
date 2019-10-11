<?php 
error_reporting(0);
$machine = $_POST["machine"];
$date_plan = $_POST["date_plan"];
$format_date = date("Y-d-m", strtotime($date_plan));
$report_manufacturing = "Report Machine: "."(".$machine.")".":"." Date: "."(".$format_date.")";

header("Content-Type: application/vnd.ms-excel"); // ประเภทของไฟล์
header("Content-Disposition: attachment; filename=\"$report_manufacturing.xls\"");
header("Content-Type: application/force-download"); // กำหนดให้ถ้าเปิดหน้านี้ให้ดาวน์โหลดไฟล์
header("Content-Type: application/octet-stream"); 
header("Content-Type: application/download"); // กำหนดให้ถ้าเปิดหน้านี้ให้ดาวน์โหลดไฟล์
header("Content-Transfer-Encoding: binary"); 
header("Content-Length: ".filesize("$report_manufacturing.xls"));   
 
@readfile($filename); 
include("../include/function.php");
include("../class/connect.php");
            $class_con_125 = new Sqlsrv();
            $class_con_125->getConnect();
            // Select
            $query=$class_con_125->getQuery("
                SELECT pl.id AS pl_id,pl.prod_order_no AS Pl_prod_order_no,pl.*,ot.* 
                FROM planning AS pl LEFT JOIN orderStartStopTime AS ot ON pl.prod_order_no=ot.prod_order_no
                WHERE pl.machine = '".$machine."' AND pl.plan_date = '".$format_date."'
            ");
            while($result=$class_con_125->getResult($query)){
                $pl_id[] = $result["pl_id"];
                $planning_id[] = $result["id"];
                $prod_order_no[] = $result["Pl_prod_order_no"];
                $customer_name[] = $result["customer_name"];
                $change_order_time[] = $result["change_order_time"];
                $paper_in[] = $result["paper_in"];
                $remark[] = $result["remark"];
                $machine_out[] = $result["machine_out"];
                $plan_date = $result["plan_date"];
                $production_minutes[] = $result['production_minute'];

                $production_time = number_format($result['production_time'],2);
                $plan_time[] = str_replace('.', ':', $production_time);

                $production_minute = number_format($result['production_minute'],0);
                $production_end_time[] = date('H:i',strtotime("+$production_minute minutes",strtotime("$plan_date $production_time")));

                // losstime
                $cause_production_time_late[] = $result["cause_production_time_late"];
                $image[] = $result["image"];
                $solutions[] = $result["solutions"];
                if($result["order_start_time"]==""){
                    $order_start_time[] = "-";
                }else{
                    $order_start_time[] = date('H:i',strtotime($result["order_start_time"]));
                }
                if($result["order_end_time"]==""){
                    $order_end_time[] = "-";
                }else{
                    $order_end_time[] = date('H:i',strtotime($result["order_end_time"]));
                }
            }
            foreach ($prod_order_no as $row => $id) {
                if($order_end_time[$row]=="-"){
                    $time_set_real[] = "-";
                }else{
                    $time_set_real[] = TimeMinuteDiff("$order_start_time[$row]","$order_end_time[$row]");
                }
            }
            // select losstime
            foreach ($pl_id as $row => $id) {
            $class_con_125_loss = new Sqlsrv();
            $class_con_125_loss->getConnect();
            // Select
            $query=$class_con_125_loss->getQuery("
                SELECT lt.planning_id AS planning_id_detail,
                SUM(CASE WHEN lt.cause_head_id = 1 THEN lt.losstime_minute END) AS 'production_time_late',
                SUM(CASE WHEN lt.cause_head_id = 2 THEN lt.losstime_minute END) AS 'losstime_paper',
                SUM(CASE WHEN lt.cause_head_id = 3 THEN lt.losstime_minute END) AS 'losstime_block',
                SUM(CASE WHEN lt.cause_head_id = 4 THEN lt.losstime_minute END) AS 'losstime_color',
                SUM(CASE WHEN lt.cause_head_id = 5 THEN lt.losstime_minute END) AS 'losstime_machine',
                SUM(CASE WHEN lt.cause_head_id = 6 THEN lt.losstime_minute END) AS 'losstime_other'
                FROM planning AS pl LEFT JOIN losstime AS lt ON pl.id=lt.planning_id
                LEFT JOIN losstimeCauseHead AS ltch ON lt.cause_head_id=ltch.cause_head_id
                LEFT JOIN losstimeCauseLine AS ltcl ON lt.cause_line_id=ltcl.cause_line_id
                WHERE pl.id = '".$id."'
                GROUP BY lt.planning_id
            ");
            while($result=$class_con_125_loss->getResult($query)){
                $production_time_late[] = $result["production_time_late"];
                $losstime_paper[] = $result["losstime_paper"];
                $losstime_block[] = $result["losstime_block"];
                $losstime_color[] = $result["losstime_color"];
                $losstime_machine[] = $result["losstime_machine"];
                $losstime_other[] = $result["losstime_other"];
                }
            }
            // Edit
            foreach ($prod_order_no as $key => $id) {
             $losstime_sum[] = $production_time_late[$key]+$losstime_paper[$key]+$losstime_block[$key]+$losstime_color[$key]+$losstime_machine[$key]+$losstime_other[$key];
            }
            foreach ($prod_order_no as $key => $value) {
             $real_time[] = $production_minutes[$key]+$losstime_sum[$key];
            }
            foreach ($prod_order_no as $key => $value) {
             $late_time[] = $production_minutes[$key]-$real_time[$key];
            }
            // select config
            $class_con_125_config = new Sqlsrv();
            $class_con_125_config->getConnect();
            $query=$class_con_125_config->getQuery("
                SELECT num_hour,meal_break,meeting,five_s,num_paper_for_day
                FROM config_oee 
                WHERE config_machine = '".$machine."' AND config_date = '".$format_date."'
            ");
            while($result=$class_con_125_config->getResult($query)){
                $num_hour = $result["num_hour"];
                $meal_break = $result["meal_break"];
                $meeting = $result["meeting"];
                $five_s = $result["five_s"];
                $num_paper_for_day = $result["num_paper_for_day"];
            }
            // select data 
            $class_con_om = new Sqlsrv_om();
            $class_con_om->getConnect();
            // Select
            foreach ($prod_order_no as $key => $value) {

            $query=$class_con_om->getQuery("
                SELECT      PDR.[No_] AS PDR,
            PDR.[Quantity] as 'Quantity',
            PDR.[Description] AS 'description',
            SUM(CASE WHEN ITEM.[Entry Type] = 5 THEN ITEM.[Quantity] END) AS 'input',
            SUM(CASE WHEN ITEM.[Entry Type] = 6 THEN ITEM.[Quantity] END) AS 'Output',
            (
                SUM(CASE WHEN ITEM.[Entry Type] = 5 THEN ITEM.[Quantity] END) + SUM(CASE WHEN ITEM.[Entry Type] = 6 THEN ITEM.[Quantity] END)
            ) as 'west'
            FROM        [OM-PS].[dbo].[โอเอ็ม แพ็คเกจจิ้ง โซลูชั่น\$Production Order]    PDR
            LEFT JOIN  [OM-PS].[dbo].[โอเอ็ม แพ็คเกจจิ้ง โซลูชั่น\$Item Ledger Entry] ITEM  ON  ITEM.[Prod_ Order No_]  =   PDR.[No_]
            WHERE       PDR.[No_] = '".$value."'
            GROUP BY    PDR.[No_],PDR.[Quantity],PDR.[Description]
            ");
            while($result=$class_con_om->getResult($query)){
                $PDR[] = $result["PDR"];
                $description[] = $result["description"];
                $Quantity[] = $result["Quantity"];
                $input[] = $result["input"];
                $Output[] = $result["Output"];
                $waste_paper[] = $result["west"];
            }
        }
        // $time_set_real = array("15","5","15","15","5","15","5","15","15","5","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","","","","15","","","");
        // $num_paper_for_day = "100";
        // $num_hour = "24";
        // $meal_break = "120";
        // $meeting = "20";
        // $five_t = "30";
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<link href='https://fonts.googleapis.com/css?family=Kanit:400,300&subset=thai,latin' rel='stylesheet' type='text/css'>
<style type="text/css">
	body {
          font-family: 'Kanit', sans-serif;
    }
    h1 {
        font-family: 'Kanit', sans-serif;
    }
</style>
</head>
<body>
<table border="1">
		<thead>
			<tr align="center" style="background-color: #191970;">
				<th colspan="24" style="color: #FFFFFF;font-size: 18px;">รายงานการผลิตเครื่อง <?php echo $machine." | ".$format_date  ?></th>
			</tr>
			<tr>
        <th>ลำดับ</th>
        <th>PDR</th>
        <th>ชื่อลูกค้า</th>
        <th>ชื่อกล่อง</th>
        <!-- <th>เวลาแผน(นาที)</th> -->
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
				foreach ($prod_order_no as $row => $id) {
			?>
			<tr>
        <td><?php echo $i ?></td>
        <td><?php echo $id ?></td>
        <td><?php echo $customer_name[$row] ?></td>
        <td><?php echo $description[$row] ?></td>
        <!-- <td><?php echo round($production_minutes[$row]) ?></td> -->
        <td><?php echo round($production_minutes[$row]) ?></td>
        <td><?php echo round($real_time[$row]) ?></td>
        <td><?php echo $late_time[$row] ?></td>
        <td><?php echo $production_time_late[$row] ?></td>
        <td><?php echo $losstime_paper[$row] ?></td>
        <td><?php echo $losstime_block[$row] ?></td>
        <td><?php echo $losstime_color[$row] ?></td>
        <td><?php echo $losstime_machine[$row] ?></td>
        <td><?php echo $losstime_other[$row] ?></td>
        <td><?php echo $losstime_sum[$row] ?></td>
        <td><?php echo $change_order_time[$row] ?></td>
        <td><?php echo $time_set_real[$row] ?></td>
        <td>
          <?php 
              $class_con_125_waste = new Sqlsrv();
              $class_con_125_waste->getConnect();
              // Select
              $query=$class_con_125_waste->getQuery("
                  SELECT ltcl.cause_line_name,lt.losstime_minute
                  FROM planning AS pl LEFT JOIN losstime AS lt ON pl.id=lt.planning_id
                  LEFT JOIN losstimeCauseHead AS ltch ON lt.cause_head_id=ltch.cause_head_id
                 LEFT JOIN losstimeCauseLine AS ltcl ON lt.cause_line_id=ltcl.cause_line_id
                  WHERE pl.prod_order_no = '".$id."'
              ");
          $y=0;    
              while($result=$class_con_125_waste->getResult($query)){
          $s = $y % 2;
          if($s == '1'){ 
              $color_bar = '#FFFFFF';
          }else{ 
              $color_bar = '#DFEFFF';
          }?>
          <?php if($result["cause_line_name"]!="" && $result["losstime_minute"]!=""){ ?>
          <div style="background-color: <?php echo $color_bar ?>"><?php echo "- ".$result["cause_line_name"]." ".$result["losstime_minute"]." นาที"; ?></div> 
          <?php 
          $cause_line_name[] = $result["cause_line_name"];
          $losstime_minute[] = $result["losstime_minute"];
          ?>
          <?php }else{?>  
          <div></div>     
          <?php } $y++;} ?>
        </td>
        <?php if($image[$row]==""){ ?>
        <td></td>
        <?php }else{ ?> 
        <td><img src="../losstime_image/<?php echo $image[$row] ?>" width="50px"></td>
        <?php } ?>
        <td><?php echo $solutions[$row] ?></td>
        <td><?php echo round($Quantity[$row]) ?></td>
        <td><?php echo $paper_in[$row] ?></td>
        <td><?php echo round(trim($input[$row],"-")) ?></td>
        <td><?php echo round($Output[$row]) ?></td>
        <td><?php echo round(trim($waste_paper[$row],"-")) ?></td>
      </tr>
			<?php $i++;} ?>
			<tr>
        <td colspan="4"></td>
        <!-- <td><?php echo round(trim(array_sum($production_minutes),"-")); ?></td> -->
        <td><?php echo round(trim(array_sum($production_minutes),"-")); ?></td>
        <td><?php echo round(trim(array_sum($real_time),"-")); ?></td>
        <td><?php echo array_sum($late_time); ?></td>
        <td><?php echo array_sum($production_time_late); ?></td>
        <td><?php echo array_sum($losstime_paper); ?></td>
        <td><?php echo array_sum($losstime_block); ?></td>
        <td><?php echo array_sum($losstime_color); ?></td>
        <td><?php echo array_sum($losstime_machine); ?></td>
        <td><?php echo array_sum($losstime_other); ?></td>
        <td><?php echo array_sum($losstime_sum); ?></td>
        <td><?php echo array_sum($change_order_time); ?></td>
        <td><?php echo array_sum($time_set_real); ?></td>
        <td colspan="3"></td>
        <td><?php echo array_sum($Quantity); ?></td>
        <td></td>
        <td><?php echo round(trim(array_sum($input),"-")); ?></td>
        <td><?php echo array_sum($Output); ?></td>
        <td><?php echo round(trim(array_sum($waste_paper),"-")); ?></td>
      </tr>
      <tr>
        <td colspan="21"></td>
        <td colspan="2">เปอร์เซ็นต์ของเสีย</td>
        <td><?php echo sprintf("%.4f%%", trim(array_sum($waste_paper),"-")/trim(array_sum($input),"-")); ?></td>
      </tr>
		</tbody>
	</table><br><br>
	<table border="1">
	 <tr align="center" style="background-color: #00FF00;"><th colspan="5">รายงานการผลิตประจำวันที่ <?php echo $format_date; ?></th></tr>	
     <tr align="center" style="background-color: #D3D3D3;"><th>จำนวน ORDER ทั้งหมด</th><th>จำนวน ORDER ที่ผลิตได้</th><th>รวมเวลาตามแผนการผลิต (นาที)</th><th>เวลาที่ใช้จริง(นาที)</th><th>เวลาเลทแผน(นาที)</th></tr>
     <tr align="center"><td><?php echo count($prod_order_no); ?></td><td><?php echo count($PDR); ?></td><td><?php echo round(array_sum($production_minutes)); ?></td><td><?php echo round(array_sum($real_time)); ?></td><td><?php echo array_sum($late_time); ?></td></tr>
     <tr align="center" style="background-color: #D3D3D3;"><th>PDRที่พบปัญหาคุณภาพ</th><th>ชื่อลูกค้า</th><th>ชื่อกล่อง</th><th>จำนวนต่อOrder</th><th>จำนวนที่พบปัญหา</th></tr>
     <tr align="center"><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td></tr>
     <tr align="center" style="background-color: #D3D3D3;"><th>จำนวนORDERที่ครบจำนวน</th><th>จำนวนORDERที่ขาดจำนวน</th><th colspan="3">สาเหตุ</th></tr>
     <tr align="center"><td></td><td></td><td colspan="3">-</td></tr>
    </table><br><br>

    <table width="100%">
    	<tr>
    		<td>
	<table border="1" width="100%">
      <tr><th width="50%">INPUT (แผ่น)</th><td align="right"><?php echo trim(array_sum($input),"-"); ?></td></tr>
      <tr><th width="50%">OUTPUT (แผ่น)</th><td align="right"><?php echo array_sum($Output); ?></td></tr>
      <tr><th width="50%">ของเสีย (แผ่น)</th><td align="right"><?php echo trim(array_sum($waste_paper),"-"); ?></td></tr>
      <tr><th width="50%">%ของเสีย (แผ่น)</th><td align="right"><?php echo sprintf("%.4f%%", trim(array_sum($waste_paper),"-")/trim(array_sum($input),"-")); ?></td></tr>
      <tr><th width="50%">จำนวน Order ที่วางแผน</th><td align="right"><?php echo count($prod_order_no); ?></td></tr>
      <!-- <tr><th width="50%">จำนวน Order ที่เดินงาน</th><td align="right"><?php echo count($prod_order_no)-count($count_out); ?></td></tr> -->
      <tr><th width="50%">จำนวน Order ที่เดินงาน</th><td align="right"><?php echo count($PDR); ?></td></tr>
      <!-- <tr><th width="50%">จำนวนแผ่นเฉลี่ยต่อOrder</th><td align="right"><?php echo number_format(array_sum($Output)/count($prod_order_no)-count($count_out)); ?></td></tr> -->
      <tr><th width="50%">จำนวนแผ่นเฉลี่ยต่อOrder</th><td align="right"><?php echo number_format(array_sum($Output)/count($PDR)); ?></td></tr>
      <tr><th width="50%">เวลาจากวางแผน (นาที)</th><td align="right"><?php echo round(array_sum($production_minutes)); ?></td></tr>
      <tr><th width="50%">เวลาเดินงานจริง (นาที)</th><td align="right"><?php echo round(array_sum($real_time)); ?></td></tr>
      <tr><th width="50%">Average Speed (แผ่น/นาที)</th><td align="right"><?php echo number_format(trim(array_sum($input),"-")/array_sum($real_time),2); ?></td></tr>
      <?php 
          $loss_time = array_sum($real_time)-array_sum($production_minutes);
      ?>
      <tr><th width="50%">Speed RUN TIME (แผ่น/นาที)</th><td align="right"><?php echo number_format(trim(array_sum($input),"-")/((array_sum($real_time))-($loss_time+array_sum($time_set_real))),2); ?></td></tr>
      <tr><th width="50%">Loss Time (นาที/วัน)</th><td align="right"><?php echo $loss_time; ?></td></tr>
      <tr><th width="50%">%Loss Time (นาที/วัน)</th><td align="right"><?php echo round(array_sum($real_time)-array_sum($production_minutes)/array_sum($real_time),2)."%"; ?></td></tr>
      <tr><th width="50%">Set up time (นาที/วัน)</th><td align="right"><?php echo array_sum($time_set_real); ?></td></tr>
      <!-- <tr><th width="50%">Set up time (นาที/รายการ)</th><td align="right"><?php echo number_format(array_sum($time_set_real)/count($prod_order_no)-count($count_out),2); ?></td></tr> -->
      <tr><th width="50%">Set up time (นาที/รายการ)</th><td align="right"><?php echo number_format(array_sum($time_set_real)/count($PDR)); ?></td></tr>
    </table><br><br>

    <table border="1" width="50%">
     <thead align="center">
      <tr align="left">
      	<th colspan="7"><u>งานขาดจำนวน</u></th>
      </tr>
      <tr>
       <th>เลขที่ PDR</th>
       <th>ชื่อลูกค้า</th>
       <th>ชื่อกล่อง</th>
       <th>จำนวนต้องการ</th>
       <th>ผลิตได้</th>
       <th>ขาดจำนวน</th>
       <th>สาเหตุ</th>
      </tr>
     </thead>
     <tbody align="center">
      <tr>
       <td>-</td>
       <td>-</td>
       <td>-</td>
       <td>-</td>
       <td>-</td>
       <td>-</td>
       <td>-</td>
      </tr>
     </tbody>
    </table>
    </td>
    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
    <td>
      <table border="1" width="50%">
          <tr>
            <th><u>สาเหตุไม่จบแผน</u></th>
          </tr>
            <tr><td></td></tr>
          <tr>
            <th><u>สาเหตุของเสีย</u></th>
          </tr>
            <tr><td></td></tr>
          <tr>
            <th><u>สาเหตุเวลาสูญเสีย</u></th>
          </tr>
          <?php 
          $i=1;
          $y=0;    
          foreach ($cause_line_name as $row => $id) {
          $s = $y % 2;
          if($s == '1'){ 
              $color_bar = '#FFFFFF';
          }else{ 
              $color_bar = '#DFEFFF';
          }?>
          <tr><td style="background-color: <?php echo $color_bar ?>"><?php echo $i.". ".$id." ".$losstime_minute[$row]." นาที"; ?></td></tr>  
          <?php $i++;$y++;} ?>
      </table>
    </td>
    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
    <td>
    <?php $sum_hour = $num_hour*60; ?>
    <?php $sum_time = $meal_break+$meeting+$five_s+array_sum($time_set_real); ?>
    <?php $sum_time_machine = $sum_hour-$sum_time; ?>
    <?php $sum_late_time = array_sum($production_time_late)+array_sum($losstime_paper)+array_sum($losstime_block)+array_sum($losstime_color)+array_sum($losstime_machine)+array_sum($losstime_other); ?>
    <?php $sum_late_time_machine = $sum_time_machine-$sum_late_time; ?>
	<?php $availability = sprintf("%.2f%%", $sum_late_time_machine * 100/$sum_time_machine) ?>
	<?php $num_paper_machine = $sum_late_time_machine*$num_paper_for_day ?>
	<?php $availability = sprintf("%.2f%%", $sum_late_time_machine * 100/$sum_time_machine) ?>
	<?php $performance = sprintf("%.2f%%", trim(array_sum($input),"-") * 100/$num_paper_machine) ?>
	<?php $quality_rate = sprintf("%.2f%%", array_sum($Output) * 100/trim(array_sum($input),"-")) ?>
	<table border="0">
		<tr align="center" style="background-color: #32CD32;">
			<th colspan="4">OEE (OVERALL EQUIPMENT EFFECTIVENESS)ของเครื่อง <?php echo $machine." | ".$format_date  ?></th>
		</tr>
		<tr>
			<th colspan="2" style="background-color: #FFFF00;">1. Availability (อัตราการเดินเครื่อง)</th>
			<th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
			<th colspan="1" style="background-color: #FFFF00;">2. Performance Efficiency (ประสิทธิภาพการเดินเครื่อง)</th>
	    </tr>
	    <tr>
	    	<td></td>
	    	<td><?php echo $num_hour ?> ชม X 60  นาที  <?php echo $sum_hour; ?> นาที</td>
	    	<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
	    	<td>Performance Rate (เวลาที่เครื่องเดินงาน) = <?php echo $sum_late_time_machine ?> นาที</td>
	    </tr>
	    <tr>
	    	<td></td>
	    	<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
	    	<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
	    	<td>เครื่อง <?php echo $machine ?> เดินงาน 1 นาที เท่ากับ <?php echo $num_paper_for_day  ?> แผ่น = <?php echo $num_paper_machine ?> แผ่น</td>
	    </tr>
	    <tr>
	    	<td></td>
	    	<td>Meal break (หักกินข้าว) <?php echo $meal_break ?> นาที</td>
	    	<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
	    	<td>ดังนั้น งานที่ผ่านเครื่อง (ของดี+ของเสีย) <?php echo trim(array_sum($input),"-"); ?> แผ่น</td>
	    </tr>
	    <tr>
	    	<td></td>
	    	<td>Meeting <?php echo $meeting ?> นาที</td>
	    	<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
	    	<th>Performance <?php echo $performance ?></th>
	    </tr>
	    <tr>
	    	<td>PLAN DOWN TIME</td>
	    	<td>การทำ 5 ส <?php echo $five_s ?> นาที</td>
	    	<td></td>
	    	<td></td>
	    </tr>
	    <tr>
	    	<td></td>
	    	<td>การตั้งเครื่อง SET UP <?php echo array_sum($time_set_real) ?> นาที</td>
	    	<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
	    	<th style="background-color: #FFFF00;">3. Quality Rate (อัตราคุณภาพ)</th>
	    </tr>
	    <tr>
	    	<td></td>
	    	<th>รวม <?php echo $sum_time; ?> นาที</th>
	    	<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
	    	<td>INPUT (ของดี+ของเสีย) <?php echo trim(array_sum($input),"-"); ?> แผ่น</td>
	    </tr>
	    <tr>
	    	<td></td>
	    	<th>เวลาในการใช้เครื่องจักร <?php echo $sum_time_machine ?> นาที</th>
	    	<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
	    	<td>OUTPUT (ของดี) <?php echo array_sum($Output); ?> แผ่น</td>
	    </tr>
	    <tr>
	    	<td>&nbsp;</td>
	    	<td>&nbsp;</td>
	    	<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
	    	<th>Quality Rate <?php echo $quality_rate ?></th>
	    </tr>
	    <tr>
	    	<td></td>
	    	<td>ผลิตเลท <?php echo array_sum($production_time_late) ?> นาที</td>
	    	<td>&nbsp;</td>
	    	<th>&nbsp;</th>
	    </tr>
	    <tr>
	    	<td></td>
	    	<td>เวลาสูญเสียกระดาษ <?php echo array_sum($losstime_paper) ?> นาที</td>
	    	<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
	    	<td>OEE = อัตราการเดินเครื่อง * ประสิทธิภาพการเดินเครื่อง * อัตราคุณภาพ</td>
	    </tr>
	    <tr>
	    	<td></td>
	    	<td>เวลาสูญเสียบล็อค <?php echo array_sum($losstime_block) ?> นาที</td>
	    	<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
	    	<th style="background-color: #FFE4E1;">OEE = <?php echo sprintf("%.2f%%", (($sum_late_time_machine * 100/$sum_time_machine)/100*(trim(array_sum($input),"-") * 100/$num_paper_machine)/100*(array_sum($Output) * 100/trim(array_sum($input),"-"))/100)*100) ?></th>
	    </tr>
	    <tr>
	    	<td></td>
	    	<td>เวลาสูญเสียสี <?php echo array_sum($losstime_color) ?> นาที</td>
	    </tr>
	    <tr>
	    	<td align="center">เวลาสูญเสีย(UNPLAN)</td>
	    	<td>เวลาสูญเสียเครื่องจักร <?php echo array_sum($losstime_machine) ?> นาที</td>
	    </tr>
	    <tr>
	    	<td></td>
	    	<td>เวลาสูญเสียอื่น <?php echo array_sum($losstime_other) ?> นาที</td>
	    </tr>
	    <tr>
	    	<td></td>
	    	<th>รวม <?php echo $sum_late_time;?> นาที</th>
	    </tr>
	    <tr>
	    	<td></td>
	    	<th>เวลาใช้เครื่องจักร-เวลาสูญเสีย <?php echo $sum_late_time_machine ?> นาที</th>
	    </tr>
	    <tr>
	    	<td></td>
	    	<th>Availability <?php echo $availability ?></th>
	    </tr>
	</table>
		</td>
	</tr>
</table>
</body>
</html>