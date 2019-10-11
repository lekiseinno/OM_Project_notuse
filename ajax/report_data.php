<?php
            error_reporting(0);
            $date_search =  date("Y-d-m", strtotime($_POST["date_plan"]));
            // echo $date_search;
            include("../include/function.php");
            include("../class/connect.php");
            $class_con_125 = new Sqlsrv();
            $class_con_125->getConnect();
            // Select
            $query=$class_con_125->getQuery("
                SELECT pl.id AS pl_id,pl.prod_order_no AS Pl_prod_order_no,pl.*,ot.* 
                FROM planning AS pl LEFT JOIN orderStartStopTime AS ot ON pl.prod_order_no=ot.prod_order_no
                LEFT JOIN machine AS ma ON ot.machine_id=ma.id
                WHERE ma.machine_name = '".$_POST["machine"]."' 
                AND pl.plan_date = '".$date_search."'
            ");
            while($result=$class_con_125->getResult($query)){
                $pl_id[] = $result["pl_id"];
                $planning_id[] = $result["id"];
                $prod_order_no[] = $result["Pl_prod_order_no"];
                $customer_name[] = $result["customer_name"];
                $description[] = $result["product_name"];
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
                WHERE config_machine = '".$_POST["machine"]."' AND config_date = '".$date_search."'
            ");
            while($result=$class_con_125_config->getResult($query)){
                $num_hour = $result["num_hour"];
                $meal_break = $result["meal_break"];
                $meeting = $result["meeting"];
                $five_s = $result["five_s"];
                $num_paper_for_day = $result["num_paper_for_day"];
            }
            // select data erp
            $class_con_om = new Sqlsrv_om();
            $class_con_om->getConnect();
            // Select
            $test_data = array(
            'PDR1906-0135',
            'PDR1906-0339',
            'PDW1906-0010',
            // 'PDR1905-0849',
            'PDR1906-0529',
            'PDR1906-0531',
            'PDR1906-0533',
            'PDR1906-0478',
            'PDR1906-0480',
            'PDR1906-0482',
            'PDR1905-1150',
            'PDR1905-1152',
            'PDR1905-1154',
            'PDW1906-0022',
            'PDR1906-0136',
            'PDR1906-0090',
            'PDR1906-0331',
            'PDR1906-0333',
            'PDW1906-0001',
            'PDR1905-0742',
            'PDR1905-0743',
            'PDR1905-0744',
            'PDR1905-0745',
            'PDR1906-0191',
            'PDR1906-0192',
            'PDR1905-0973',
            'PDR1905-1248',
            'PDR1906-0345',
            'PDR1906-0346',
            'PDR1906-0416',
            'PDW1905-0093');
            foreach ($test_data as $row => $value) {
            $query=$class_con_om->getQuery("
                EXEC [dbo].[OM_Quantity] @PDR = '".$value."'
            ");
            // SELECT      PDR.[No_] AS PDR,
            // PDR.[Quantity] as 'Quantity',
            // PDR.[Description] AS 'description',
            // SUM(CASE WHEN ITEM.[Entry Type] = 5 THEN ITEM.[Quantity] END) AS 'input',
            // SUM(CASE WHEN ITEM.[Entry Type] = 6 THEN ITEM.[Quantity] END) AS 'Output',
            // (
            //     SUM(CASE WHEN ITEM.[Entry Type] = 5 THEN ITEM.[Quantity] END) + SUM(CASE WHEN ITEM.[Entry Type] = 6 THEN ITEM.[Quantity] END)
            // ) as 'west'
            // FROM        [OM-PS].[dbo].[โอเอ็ม แพ็คเกจจิ้ง โซลูชั่น\$Production Order]    PDR
            // LEFT JOIN  [OM-PS].[dbo].[โอเอ็ม แพ็คเกจจิ้ง โซลูชั่น\$Item Ledger Entry] ITEM  ON  ITEM.[Prod_ Order No_]  =   PDR.[No_]
            // WHERE       PDR.[No_] = '".$value."'
            // GROUP BY    PDR.[No_],PDR.[Quantity],PDR.[Description]
            while($result=$class_con_om->getResult($query)){
                $PDR[] = $result["PDR"];
                $Quantity[] = $result["จำนวนที่ต้องการ"];
                $input[] = $result["input"];
                $Output[] = $result["Output"];
                $waste_paper[] = $result["west"];
            }
        }
?>
<?php if($prod_order_no == ""){?>
<div class="alert alert-danger">
    ไม่พบข้อมูลจากแผน ที่ท่านค้นหา!
</div>
<?php }else{ ?>
<div class="wrapper wrapper-content animated fadeInRight">
        	<div class="row">
                <div class="col-lg-4">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h3>INPUT</h3>
                    </div>
                    <div class="panel-body" style="min-height: 110px;">
                        <h1 class="no-margins">จำนวน กระดาษที่เข้า ทั้งหมด<b class="stat-percent text-success"><?php echo number_format(trim(array_sum($input),"-")); ?></b></h1>
                        <div class="stat-percent font-bold text-info"><?php echo number_format(trim(array_sum($input),"-")/count($prod_order_no))." SHEET"." / "."1 PDR"; ?> <i class="fa fa-files-o"></i></div>
                        <small>INPUT / PDR ORDER</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="panel panel-warning">
                    <div class="panel-heading">
                        <h3>OUTPUT</h3>
                    </div>
                    <div class="panel-body" style="min-height: 110px;">
                        <h1 class="no-margins">จำนวน ผลิตผล ทั้งหมด<b class="stat-percent text-success"><?php echo number_format(array_sum($Output)); ?></b></h1>
                        <div class="stat-percent font-bold text-warning"><?php echo number_format(array_sum($Output)/count($prod_order_no))." SHEET"." / "."1 PDR"; ?> <i class="fa fa-dropbox"></i></div>
                        <small>OUTPUT / PDR ORDER</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="panel panel-danger">
                     <div class="panel-heading">
                        <h3>WASTE</h3>
                     </div>
                    <div class="panel-body" style="min-height: 110px;">
                        <h1 class="no-margins">จำนวน ของเสีย ทั้งหมด<b class="stat-percent text-success"><?php echo trim(array_sum($waste_paper),"-"); ?></b></h1>
                        <div class="stat-percent font-bold text-danger"><?php echo sprintf("%.4f%%", trim(array_sum($waste_paper),"-")/trim(array_sum($input),"-")); ?> <i class="fa fa-trash"></i></div>
                        <small>PERCENT WASTE</small>
                    </div>
                </div>
            </div>
        </div>

            <div class="row">
            <div class="col-lg-8">
                <div class="ibox ">
                    <div class="ibox-content" style="min-height: 404px;">
                        <div>
                            <h3 class="font-bold no-margins">
                                Time graph
                            </h3>
                            <small>Detail time in graph.</small>
                        </div>

                        <div class="m-t-sm">

                            <div class="row">
                                <div class="col-md-10">
                                    <div>
                                        <canvas id="Time_barchart" class="chart" width="1000px" height="310px"></canvas>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <ul class="stat-list m-t-lg">
                                        <li>
                                            <h2 class="no-margins"><?php echo round(array_sum($production_minutes)); ?></h2>
                                            <small>รวมเวลาตามแผน</small>
                                            <div class="progress progress-mini">
                                                <div class="progress-bar progress-bar-striped progress-bar-animated progress-bar-danger" style="width: 100%" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </li>
                                        <li>
                                            <h2 class="no-margins "><?php echo round(array_sum($real_time)); ?></h2>
                                            <small>รวมเวลาเดินงานจริง</small>
                                            <div class="progress progress-mini">
                                                <div class="progress-bar progress-bar-striped progress-bar-animated progress-bar-Primary" style="width: 100%" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                        </div>

                        <div class="m-t-md" style="margin-top:20px;margin-bottom: 3%;">
                            <small class="float-right">
                                <i class="fa fa-clock-o"> </i>
                                Update on <?php echo $date_search; ?>
                            </small>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="panel panel-success">
                    <div class="panel-heading">
                        <?php $sum_hour = $num_hour*60; ?>
                        <?php $sum_time = $meal_break+$meeting+$five_s+array_sum($time_set_real); ?>
                        <?php $sum_time_machine = $sum_hour-$sum_time; ?>
                        <?php $sum_late_time = array_sum($production_time_late)+array_sum($losstime_paper)+array_sum($losstime_block)+array_sum($losstime_color)+array_sum($losstime_machine)+array_sum($losstime_other); ?>
                        <?php $sum_late_time_machine = $sum_time_machine-$sum_late_time; ?>
                        <?php $availability = sprintf("%.2f%%", $sum_late_time_machine * 100/$sum_time_machine) ?>
                        <?php $num_paper_machine = $sum_late_time_machine*$num_paper_for_day ?>
                        <?php $performance = sprintf("%.2f%%", trim(array_sum($input),"-") * 100/$num_paper_machine) ?>
                        <?php $quality_rate = sprintf("%.2f%%", array_sum($Output) * 100/trim(array_sum($input),"-")) ?>
                        <h3>OEE = <?php echo sprintf("%.2f%%", (($sum_late_time_machine * 100/$sum_time_machine)/100*(trim(array_sum($input),"-") * 100/$num_paper_machine)/100*(array_sum($Output) * 100/trim(array_sum($input),"-"))/100)*100) ?></h3>
                    </div>
                    <div class="panel-body">
                        <div class="content" style="padding: 15px 0px 10px 20px;border-bottom: solid 1px;border-color: #e7eaec;">
                        <h3>1. Availability (อัตราการเดินเครื่อง)</h3>
                        <div class="row">
                            <div class="col-4">
                                <small class="stats-label">เวลาในการใช้เครื่องจักร</small>
                                <h4><?php echo $sum_time_machine ?></h4>
                            </div>

                            <div class="col-4">
                                <small class="stats-label">เวลาในการใช้เครื่องจักร-เวลาสูญเสีย</small>
                                <h4><?php echo $sum_late_time_machine ?></h4>
                            </div>
                            <div class="col-4">
                                <small class="stats-label">Availability</small>
                                <h4><?php echo $availability ?></h4>
                            </div>
                        </div>
                    </div>
                    <div class="content" style="padding: 15px 0px 10px 20px;border-bottom: solid 1px;border-color: #e7eaec;">
                        <h3>2. Performance Efficiency (ประสิทธิภาพการเดินเครื่อง)</h3>
                        <div class="row">
                            <div class="col-4">
                                <small class="stats-label">Performance</small>
                                <h4><?php echo $performance ?></h4>
                            </div>
                        </div>
                    </div>
                    <div class="content" style="padding: 15px 0px 10px 20px;border-bottom: solid 1px;border-color: #e7eaec;">
                        <h3>3. Quality Rate (อัตราคุณภาพ)</h3>
                        <div class="row">
                            <div class="col-4">
                                <small class="stats-label">Quality Rate</small>
                                <h4><?php echo $quality_rate ?></h4>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>

                <div class="row">
                <div class="col-lg-12">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h5>
                            <ul class="nav nav-tabs">
                                <li><a class="nav-link active" data-toggle="tab" href="#tabhead-1">ตารางรายงานการผลิตรวม</a></li>
                                <li><a class="nav-link" data-toggle="tab" href="#tabhead-2"> ตารางรายงานเดินงานจริง</a></li>
                                <li><a class="nav-link" data-toggle="tab" href="#tabhead-3"> ตารางรายงานเครื่องจักร</a></li>
                            </ul>
                        </h5>
                        <div class="ibox-tools" style="text-align: left;">
                            <a class="btn btn-primary" data-toggle='modal' href='#modal-form-report-all' title='คลิกที่นี่เพื่อดูข้อมูลแบบเต็ม'><i class="fa fa-table" aria-hidden="true"></i> Full Table</a>
                            <div id="modal-form-report-all" class="modal fade" aria-hidden="true">
                                <div class="modal-dialog" style="max-width: 1000px;">
                                    <div class="modal-content">
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-sm-12"><h3 class="m-t-none m-b">Full Data Report</h3>
                                                     <div class="tabs-container">
                                                            <ul class="nav nav-tabs">
                                                                <li><a class="nav-link active" data-toggle="tab" href="#tab-1"> Data Detail And Time Plan</a></li>
                                                                <li><a class="nav-link" data-toggle="tab" href="#tab-2"> Time Planning</a></li>
                                                                <li><a class="nav-link" data-toggle="tab" href="#tab-3"> Set up time And Etc.</a></li>
                                                                <li><a class="nav-link" data-toggle="tab" href="#tab-4"> Data Paper</a></li>
                                                            </ul>
                                                            <div class="tab-content">
                                                                <div id="tab-1" class="tab-pane active">
                                                                    <div class="panel-body">
                                                                    <div class="table-responsive">
                                                                    <table class="table table-striped table-bordered table-hover dataTables-example" >
                                                                        <thead>
                                                                            <tr align="center">
                                                                                <th>ลำดับ</th>
                                                                                <th>PDR</th>
                                                                                <th>ชื่อลูกค้า</th>
                                                                                <th>ชื่อกล่อง</th>
                                                                                <th>เวลาแผน(นาที)</th>
                                                                                <th>เวลาเดินจริง(นาที)</th>
                                                                                <th>เลทแผน(นาที)</th> 
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <?php $i=1; foreach ($prod_order_no as $row => $id) {?>
                                                                            <tr>
                                                                                <td><?php echo $i ?></td>
                                                                                <td width="15%"><?php echo $id ?></td>
                                                                                <td><?php echo $customer_name[$row] ?></td>
                                                                                <td><?php echo $description[$row] ?></td>
                                                                                <td align="center"><?php echo round($production_minutes[$row]) ?></td>
                                                                                <td align="center"><?php echo round($real_time[$row]) ?></td>
                                                                                <td align="center"><?php echo $late_time[$row] ?></td>
                                                                            </tr>
                                                                            <?php $i++;} ?>
                                                                            <tr style="font-weight: bold;">
                                                                                <td><?php echo $i; ?></td>
                                                                                <td>Sum PDR</td>
                                                                                <td></td>
                                                                                <td></td>
                                                                                <td align="center"><?php echo round(trim(array_sum($production_minutes),"-")); ?></td>
                                                                                <td align="center"><?php echo round(trim(array_sum($real_time),"-")); ?></td>
                                                                                <td align="center"><?php echo array_sum($late_time); ?></td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                    </div>
                                                                    </div>
                                                                </div>
                                                                <div id="tab-2" class="tab-pane">
                                                                    <div class="panel-body">
                                                                         <div class="table-responsive">
                                                                            <table class="table table-striped table-bordered table-hover dataTables-example" >
                                                                            <thead>
                                                                            <tr align="center">
                                                                                <th>ลำดับ</th>
                                                                                <th width="20">PDR</th>
                                                                                <th>ผลิตเลท(นาที)</th>
                                                                                <th>เวลาสูญเสียกระดาษ(นาที)</th>
                                                                                <th>เวลาสูญเสียบล็อก(นาที)</th>
                                                                                <th>เวลาสูญเสียสี(นาที)</th>
                                                                                <th>เวลาสูญเสียเครื่องจักร(นาที)</th>
                                                                                <th>เวลาสูญเสียอื่นๆ(นาที)</th>
                                                                                <th>เวลาสูญเสียรวม</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <?php $i=1; foreach ($prod_order_no as $row => $id) {?>
                                                                            <tr>
                                                                                <td><?php echo $i ?></td>
                                                                                <td width="1000px"><?php echo $id ?></td>
                                                                                <td align="center"><?php echo $production_time_late[$row] ?></td>
                                                                                <td align="center"><?php echo $losstime_paper[$row] ?></td>
                                                                                <td align="center"><?php echo $losstime_block[$row] ?></td>
                                                                                <td align="center"><?php echo $losstime_color[$row] ?></td>
                                                                                <td align="center"><?php echo $losstime_machine[$row] ?></td>
                                                                                <td align="center"><?php echo $losstime_other[$row] ?></td>
                                                                                <td align="center"><?php echo $losstime_sum[$row] ?></td>
                                                                            </tr>
                                                                        <?php $i++;} ?>
                                                                            <tr style="font-weight: bold;">
                                                                                <td><?php echo $i; ?></td>
                                                                                <td>Sum PDR</td>
                                                                                <td align="center"><?php echo array_sum($production_time_late); ?></td>
                                                                                <td align="center"><?php echo array_sum($losstime_paper); ?></td>
                                                                                <td align="center"><?php echo array_sum($losstime_block); ?></td>
                                                                                <td align="center"><?php echo array_sum($losstime_color); ?></td>
                                                                                <td align="center"><?php echo array_sum($losstime_machine); ?></td>
                                                                                <td align="center"><?php echo array_sum($losstime_other); ?></td>
                                                                                <td align="center"><?php echo array_sum($losstime_sum); ?></td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                    </div>
                                                                    </div>
                                                                </div>
                                                                <div id="tab-3" class="tab-pane">
                                                                    <div class="panel-body">
                                                                        <div class="table-responsive">
                                                                            <table class="table table-stripped table-bordered dataTables-example">
                                                                                <thead>
                                                                                <tr align="center">
                                                                                    <th>ลำดับ</th>
                                                                                    <th>PDR</th>
                                                                                    <th>Set up Time ตามแผน(นาที)</th>
                                                                                    <th>Set up Time เดินงานจริง(นาที)</th>
                                                                                    <th>สาเหตุเลทแผน</th>
                                                                                    <th>รูปภาพ</th>
                                                                                    <th>วิเคราะห์/แนวทางการแก้ไข</th>
                                                                                </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                            <?php $i=1; foreach ($prod_order_no as $row => $id) {?>        
                                                                                <tr>
                                                                                    <td><?php echo $i ?></td>
                                                                                    <td width="15%"><?php echo $id ?></td>
                                                                                    <td align="center"><?php echo $change_order_time[$row] ?></td>
                                                                                    <td align="center"><?php echo $time_set_real[$row] ?></td>
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
                                                                                    <?php }else{?>  
                                                                                    <div></div>     
                                                                                    <?php } $y++;} ?>
                                                                                    </td>
                                                                                    <?php if($image[$row]==""){ ?>
                                                                                    <td></td>
                                                                                    <?php }else{ ?> 
                                                                                    <td><img src="losstime_image/<?php echo $image[$row] ?>" width="50px"></td>
                                                                                    <?php } ?>
                                                                                    <td><?php echo $solutions[$row] ?></td>
                                                                                </tr>
                                                                            <?php $i++; } ?>  
                                                                                <tr style="font-weight: bold;">
                                                                                    <td><?php echo $i; ?></td>
                                                                                    <td>Sum PDR</td>
                                                                                    <td align="center"><?php echo array_sum($change_order_time); ?></td>
                                                                                    <td align="center"><?php echo array_sum($time_set_real); ?></td>
                                                                                    <td></td>
                                                                                    <td></td>
                                                                                    <td></td>
                                                                                </tr>  
                                                                                </tbody>

                                                                            </table>
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                                <div id="tab-4" class="tab-pane">
                                                                    <div class="panel-body">
                                                                        <div class="table-responsive">
                                                                            <table class="table table-bordered table-stripped dataTables-example">
                                                                                <thead>
                                                                                <tr align="center">
                                                                                    <th>ลำดับ</th>
                                                                                    <th>PDR</th>
                                                                                    <th>จำนวนต้องการ</th>
                                                                                    <th>จำนวนที่กระดาษเข้า(PO+VA)</th>
                                                                                    <th>input(แผ่น)</th>
                                                                                    <th>output(แผ่น)</th>
                                                                                    <th>ของเสีย(แผ่น)</th>
                                                                                </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                            <?php $i=1; foreach ($prod_order_no as $row => $id) {?>
                                                                                <tr>
                                                                                    <td><?php echo $i ?></td>
                                                                                    <td width="15%"><?php echo $id ?></td>
                                                                                    <td align="center"><?php echo round($Quantity[$row]) ?></td>
                                                                                    <td align="center"><?php echo $paper_in[$row] ?></td>
                                                                                    <td align="center"><?php echo round(trim($input[$row],"-")) ?></td>
                                                                                    <td align="center"><?php echo round($Output[$row]) ?></td>
                                                                                    <td align="center"><?php echo round(trim($waste_paper[$row],"-")) ?></td>
                                                                                </tr>
                                                                            <?php $i++;} ?>
                                                                                <tr style="font-weight: bold;">
                                                                                    <td><?php echo $i; ?></td>
                                                                                    <td>Sum PDR</td>
                                                                                    <td align="center"><?php echo array_sum($Quantity); ?></td>
                                                                                    <td></td>
                                                                                    <td align="center"><?php echo round(trim(array_sum($input),"-")); ?></td>
                                                                                    <td align="center"><?php echo array_sum($Output); ?></td>
                                                                                    <td align="center"><?php echo round(trim(array_sum($waste_paper),"-")); ?></td>
                                                                                </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                <i class="fa fa-wrench"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-user">
                                <li><a href="#" class="dropdown-item">Config option 1</a>
                                </li>
                                <li><a href="#" class="dropdown-item">Config option 2</a>
                                </li>
                            </ul>
                            <a class="close-link">
                                <i class="fa fa-times"></i>
                            </a>
                        </div>
                    </div>
                    <div class="tab-content">
                    <!-- ตารางหลัก -->
                    <div id="tabhead-1" class="tab-pane active">
                    <div class="ibox-content">

                        <div class="table-responsive">
                  
                    	<div class="overflow">
                    	<table border="1" class="table table-striped table-bordered table-hover dataTables-example" style="width: 140%;">
                    		<thead>
                    			<tr bgcolor="LimeGreen">
                    				<th>ลำดับ</th>
                    				<th>PDR</th>
                    				<th>ชื่อลูกค้า</th>
                    				<th>ชื่อกล่อง</th>
                    				<th>เวลาแผน(นาที)</th>
                    				<th>เวลาเดินจริง(นาที)</th>
                    				<th>เลทแผน(นาที)</th> 
                                    <th>เวลาสูญเสียรวม</th>
                    				<th>Set up Time ตามแผน(นาที)</th>
                    				<th>Set up Time เดินงานจริง(นาที)</th>

                                    <th>เวลาเริ่มยิงบาร์โค้ด</th>
                                    <th>เวลาหยุดยิงบาร์โค้ด</th>
                                    <th>เวลา เอาต์พุต</th>
                                    <th>สถานะ</th>

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
                    				<td width="7%;"><?php echo $id ?></td>
                    				<td><?php echo $customer_name[$row] ?></td>
                    				<td><?php echo $description[$row] ?></td>
                    				<td><?php echo round($production_minutes[$row]) ?></td>
                    				<td><?php echo round($real_time[$row]) ?></td>
                                    <td><?php echo $late_time[$row] ?></td>
                                    <td><?php echo $losstime_sum[$row]; ?> </td>
                    				<td><?php echo $change_order_time[$row]; ?></td>
                    				<td><?php echo $time_set_real[$row]; ?></td>

                                    <td><?php echo $order_start_time[$row]; ?></td>
                                    <td><?php echo $order_end_time[$row]; ?></td>
                                    <td>-</td>
                                    <?php
                                        if($order_start_time[$row]=="-" && $order_end_time[$row]=="-"){
                                            $status = "<span class='pie_00'>1</span>";
                                        }elseif($order_start_time[$row]!="" && $order_end_time[$row]=="-"){
                                            $status = "<span class='pie_01'>1,1</span>";
                                        }elseif($order_start_time[$row]!="" && $order_end_time[$row]!=""){
                                            $status = "<span class='pie_02'>1</span>";
                                        }
                                    ?>
                                    <td><?php echo $status; ?></td>
                    				<td><?php echo round($Quantity[$row]) ?></td>
                    				<td><?php echo $paper_in[$row] ?></td>
                    				<td><?php echo round(trim($input[$row],"-")) ?></td>
                    				<td><?php echo round($Output[$row]) ?></td>
                    				<td><?php echo round(trim($waste_paper[$row],"-")) ?></td>
                    			</tr>
                    			<?php $i++;} ?>
                    			<tr style="font-weight: bold;">
                    				<td colspan="4"></td>
                    				<td><?php echo round(trim(array_sum($production_minutes),"-")); ?></td>
                    				<td><?php echo round(trim(array_sum($real_time),"-")); ?></td>
                    				<td><?php echo array_sum($late_time); ?></td>
                    				<td><?php echo array_sum($losstime_sum); ?></td>
                    				<td><?php echo array_sum($change_order_time); ?></td>
                    				<td><?php echo array_sum($time_set_real); ?></td>
                                    <td colspan="4"></td>
                    				<td><?php echo array_sum($Quantity); ?></td>
                    				<td></td>
                    				<td><?php echo round(trim(array_sum($input),"-")); ?></td>
                    				<td><?php echo array_sum($Output); ?></td>
                    				<td><?php echo round(trim(array_sum($waste_paper),"-")); ?></td>
                    			</tr>
                    		</tbody>
                        </table>
                            	           </div>
                                        </div>

                                    </div>
                                </div>
                            <!-- สิ้นสุดตารางหลัก -->
                            <!-- ตารางที่ 2 -->
                            <?php 
                                $class_con_125_machine = new Sqlsrv();
                                $class_con_125_machine->getConnect();
                                // Select machine
                                $query=$class_con_125_machine->getQuery("
                                    SELECT *
                                    FROM machine 
                                ");  
                                while($result=$class_con_125_machine->getResult($query)){
                                    $arr_machine_id[] = $result["id"];
                                    $arr_machine[] = $result["machine_name"];
                                }
                            ?>
                            <?php 
                            $class_con_125_machine_data = new Sqlsrv();
                            $class_con_125_machine_data->getConnect();
                            // Select
                            $query=$class_con_125_machine_data->getQuery("
                                SELECT pl.prod_order_no AS 'pdr_check',
                                ot.order_start_time AS 'order_start_time', 
                                ot.order_end_time AS 'order_end_time',
                                ma.id AS 'machine_id',
                                ma.machine_name AS 'machine_name' 
                                FROM planning AS pl LEFT JOIN orderStartStopTime AS ot ON pl.prod_order_no=ot.prod_order_no
                                LEFT JOIN machine AS ma ON ot.machine_id=ma.id
                                WHERE pl.plan_date = '".$date_search."' 
                            "); 
                                while($result=$class_con_125_machine_data->getResult($query)){
                                        $pdr_check = "";
                                        if($result["machine_id"]=="1"){
                                            $wing_start_time[$result["pdr_check"].','."1"] = date('H:i',strtotime($result["order_start_time"]));
                                            $wing_end_time[$result["pdr_check"].','."1"] = date('H:i',strtotime($result["order_end_time"]));
                                            if($result["order_end_time"]!=""){
                                            $wing_set_real[$result["pdr_check"].','."1"] = TimeMinuteDiff($wing_start_time[$result["pdr_check"].','."1"],$wing_end_time[$result["pdr_check"].','."1"]);
                                            }else{
                                            $wing_set_real[$result["pdr_check"].','."1"] = "0";    
                                            }
                                        }elseif($result["machine_id"]=="2"){
                                            $sas_start_time[$result["pdr_check"].','."2"] = date('H:i',strtotime($result["order_start_time"]));
                                            $sas_end_time[$result["pdr_check"].','."2"] = date('H:i',strtotime($result["order_end_time"]));
                                            if($result["order_end_time"]!=""){
                                            $sas_set_real[$result["pdr_check"].','."2"] = TimeMinuteDiff($sas_start_time[$result["pdr_check"].','."2"],$sas_end_time[$result["pdr_check"].','."2"]);
                                            }else{
                                            $sas_set_real[$result["pdr_check"].','."2"] = "0";    
                                            }
                                        }elseif($result["machine_id"]=="3"){
                                            $sunrise_start_time[$result["pdr_check"].','."3"] = date('H:i',strtotime($result["order_start_time"]));
                                            $sunrise_end_time[$result["pdr_check"].','."3"] = date('H:i',strtotime($result["order_end_time"]));
                                            if($result["order_end_time"]!=""){
                                            $sunrise_set_real[$result["pdr_check"].','."3"] = TimeMinuteDiff($sunrise_start_time[$result["pdr_check"].','."3"],$sunrise_end_time[$result["pdr_check"].','."3"]);
                                            }else{
                                            $sunrise_set_real[$result["pdr_check"].','."3"] = "0";    
                                            }
                                        }elseif($result["machine_id"]=="4"){
                                            $langton_start_time[$result["pdr_check"].','."4"] = date('H:i',strtotime($result["order_start_time"]));
                                            $langton_end_time[$result["pdr_check"].','."4"] = date('H:i',strtotime($result["order_end_time"]));
                                            if($result["order_end_time"]!=""){
                                            $langton_set_real[$result["pdr_check"].','."4"] = TimeMinuteDiff($langton_start_time[$result["pdr_check"].','."4"],$langton_end_time[$result["pdr_check"].','."4"]);
                                            }else{
                                            $langton_set_real[$result["pdr_check"].','."4"] = "0";    
                                            }
                                        }elseif($result["machine_id"]=="5"){
                                            $rotary_start_time[$result["pdr_check"].','."5"] = date('H:i',strtotime($result["order_start_time"]));
                                            $rotary_end_time[$result["pdr_check"].','."5"] = date('H:i',strtotime($result["order_end_time"]));
                                            if($result["order_end_time"]!=""){
                                            $rotary_set_real[$result["pdr_check"].','."5"] = TimeMinuteDiff($rotary_start_time[$result["pdr_check"].','."5"],$rotary_end_time[$result["pdr_check"].','."5"]);
                                            }else{
                                            $rotary_set_real[$result["pdr_check"].','."5"] = "0";    
                                            }
                                        }elseif($result["machine_id"]=="6"){
                                            $sleeve_start_time[$result["pdr_check"].','."6"] = date('H:i',strtotime($result["order_start_time"]));
                                            $sleeve_end_time[$result["pdr_check"].','."6"] = date('H:i',strtotime($result["order_end_time"]));
                                            if($result["order_end_time"]!=""){
                                            $sleeve_set_real[$result["pdr_check"].','."6"] = TimeMinuteDiff($sleeve_start_time[$result["pdr_check"].','."6"],$sleeve_end_time[$result["pdr_check"].','."6"]);
                                            }else{
                                            $sleeve_set_real[$result["pdr_check"].','."6"] = "0";    
                                            }
                                        }elseif($result["machine_id"]=="7"){
                                            $roytur_start_time[$result["pdr_check"].','."7"] = date('H:i',strtotime($result["order_start_time"]));
                                            $roytur_end_time[$result["pdr_check"].','."7"] = date('H:i',strtotime($result["order_end_time"]));
                                            if($result["order_end_time"]!=""){
                                            $roytur_set_real[$result["pdr_check"].','."7"] = TimeMinuteDiff($roytur_start_time[$result["pdr_check"].','."7"],$roytur_end_time[$result["pdr_check"].','."7"]);
                                            }else{
                                            $roytur_set_real[$result["pdr_check"].','."7"] = "0";    
                                            }
                                        }elseif($result["machine_id"]=="8"){
                                            $century_start_time[$result["pdr_check"].','."8"] = date('H:i',strtotime($result["order_start_time"]));
                                            $century_end_time[$result["pdr_check"].','."8"] = date('H:i',strtotime($result["order_end_time"]));
                                            if($result["order_end_time"]!=""){
                                            $century_set_real[$result["pdr_check"].','."8"] = TimeMinuteDiff($century_start_time[$result["pdr_check"].','."8"],$century_end_time[$result["pdr_check"].','."8"]);
                                            }else{
                                            $century_set_real[$result["pdr_check"].','."8"] = "0";    
                                            }
                                        }elseif($result["machine_id"]=="9"){
                                            $flexo_start_time[$result["pdr_check"].','."9"] = date('H:i',strtotime($result["order_start_time"]));
                                            $flexo_end_time[$result["pdr_check"].','."9"] = date('H:i',strtotime($result["order_end_time"]));
                                            if($result["order_end_time"]!=""){
                                            $flexo_set_real[$result["pdr_check"].','."9"] = TimeMinuteDiff($flexo_start_time[$result["pdr_check"].','."9"],$flexo_end_time[$result["pdr_check"].','."9"]);
                                            }else{
                                            $flexo_set_real[$result["pdr_check"].','."9"] = "0";    
                                            }
                                        }
                                        $machine_time_sum[$result["pdr_check"]] = $wing_set_real[$result["pdr_check"].','."1"]+$sas_set_real[$result["pdr_check"].','."2"]+$sunrise_set_real[$result["pdr_check"].','."3"]+$langton_set_real[$result["pdr_check"].','."4"]+$rotary_set_real[$result["pdr_check"].','."5"]+$sleeve_set_real[$result["pdr_check"].','."6"]+$roytur_set_real[$result["pdr_check"].','."7"]+$century_set_real[$result["pdr_check"].','."8"]+$flexo_set_real[$result["pdr_check"].','."9"];
                                }?>
                            <div id="tabhead-2" class="tab-pane">
                                <div class="ibox-content">

                                 <div class="table-responsive">
                  
                                    <div class="overflow">
                                    <table border="1" class="table table-striped table-bordered table-hover dataTables-example" style="width: 140%;">
                                        <thead>
                                            <tr bgcolor="LimeGreen">
                                                <th>ID</th>
                                                <th>PDR</th>
                                                <th>รวมเวลาแต่ละ PDR</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $i=1; foreach ($test_data as $key => $id) {?>
                                            <tr>
                                                <td><?php echo $i ?></td>
                                                <td><?php echo $id ?></td>
                                                <td><a class="btn btn-primary" data-toggle='modal' href='#modal-form-check-data' id="modal_form_check_data_<?php echo $i ?>" name="<?php echo $id ?>" title='คลิกที่นี่เพื่อดูรายละเอียดเพิ่มเติม'><?php echo round($machine_time_sum[$id]) ?></a>
                                                <input type="hidden" name="check-data-send-id-<?php echo $i ?>" id="check-data-send-id-<?php echo $i ?>" value="<?php echo $i ?>">
                                                <input type="hidden" name="check-data-send-pdr-<?php echo $i ?>" id="check-data-send-pdr-<?php echo $i ?>" value="<?php echo $id ?>">
                                                <input type="hidden" name="check-data-send-date-time-<?php echo $i ?>" id="check-data-send-date-time-<?php echo $i ?>" value="<?php echo $date_search ?>">
                                                <script type="text/javascript">
                                                    $(document).ready(function(){
                                                        $('#modal_form_check_data_<?php echo $i ?>').click(function(){
                                                              $.post("ajax/modal_machine_data.php",
                                                              {
                                                                i: $('#check-data-send-id-<?php echo $i ?>').val(),
                                                                id: $('#check-data-send-pdr-<?php echo $i ?>').val(),
                                                                date_search: $('#check-data-send-date-time-<?php echo $i ?>').val()
                                                              },
                                                              function(data){
                                                                    $("#result_machine").html(data);
                                                              });
                                                        });
                                                    });
                                                </script>
                                                </td>
                                            </tr>
                                            <?php $i++;} ?>
                                            <tr>
                                                <td><?php echo "รวมเวลาทุก PDR"; ?></td>
                                                <td><?php echo "-"; ?></td>
                                                <td><?php echo array_sum($machine_time_sum)." Minute"; ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                           </div>
                                        </div>

                                    </div>
                            </div>
                            <!-- สิ้นสุดตารางที่ 2 -->
                            <!-- ตารางที่ 3 -->
                            <div id="tabhead-3" class="tab-pane">
                                <div class="ibox-content">

                                 <div class="table-responsive">
                  
                                    <div class="overflow">
                                            <table border="1" class="table table-striped table-bordered table-hover dataTables-example" style="width: 140%;">
                                                <thead>
                                                    <tr bgcolor="LimeGreen">
                                                        <th rowspan="2">ID</th>
                                                        <th rowspan="2">PDR</th>
                                                        <?php foreach ($arr_machine as $value) {?>
                                                        <th colspan="2"><?php echo $value ?></th>
                                                        <?php }?>
                                                    </tr>
                                                    <tr>
                                                        <?php foreach ($arr_machine as $key => $value) {?>
                                                            <td>start</td><td>stop</td>
                                                        <?php } ?>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $i=1; foreach ($test_data as $key => $value) {?>
                                                    <tr>
                                                        <td><?php echo $i ?></td>
                                                        <td><?php echo $value ?></td>
                                                        <?php if(isset($wing_end_time[$value.','."1"])) {?>
                                                        <td><?php echo $wing_start_time[$value.','."1"] ?></td><td><?php echo $wing_end_time[$value.','."1"] ?></td>
                                                        <?php }else{ ?>
                                                        <td>-</td><td>-</td>
                                                        <?php } ?>
                                                        <?php if(isset($sas_end_time[$value.','."2"])) {?>
                                                        <td><?php echo $sas_start_time[$value.','."2"] ?></td><td><?php echo $sas_end_time[$value.','."2"] ?></td>
                                                        <?php }else{ ?>
                                                        <td>-</td><td>-</td>  
                                                        <?php } ?>
                                                        <?php if(isset($sunrise_end_time[$value.','."3"])) {?>
                                                        <td><?php echo $sunrise_start_time[$value.','."3"] ?></td><td><?php echo $sunrise_end_time[$value.','."3"] ?></td>
                                                        <?php }else{ ?>
                                                        <td>-</td><td>-</td>  
                                                        <?php } ?>
                                                        <?php if(isset($langton_end_time[$value.','."4"])) {?>
                                                        <td><?php echo $langton_start_time[$value.','."4"] ?></td><td><?php echo $langton_end_time[$value.','."4"] ?></td>
                                                        <?php }else{ ?>
                                                        <td>-</td><td>-</td>
                                                        <?php } ?>
                                                        <?php if(isset($rotary_end_time[$value.','."5"])) {?>
                                                        <td><?php echo $rotary_start_time[$value.','."5"] ?></td><td><?php echo $rotary_end_time[$value.','."5"] ?></td>
                                                        <?php }else{ ?>
                                                        <td>-</td><td>-</td>
                                                        <?php } ?>
                                                        <?php if(isset($sleeve_end_time[$value.','."6"])) {?>
                                                        <td><?php echo $sleeve_start_time[$value.','."6"] ?></td><td><?php echo $sleeve_end_time[$value.','."6"] ?></td>
                                                        <?php }else{ ?>
                                                        <td>-</td><td>-</td>
                                                        <?php } ?>
                                                        <?php if(isset($roytur_end_time[$value.','."7"])) {?>
                                                        <td><?php echo $roytur_start_time[$value.','."7"] ?></td><td><?php echo $roytur_end_time[$value.','."7"] ?></td>
                                                        <?php }else{ ?>
                                                        <td>-</td><td>-</td>
                                                        <?php } ?>
                                                        <?php if(isset($century_end_time[$value.','."8"])) {?>
                                                        <td><?php echo $century_start_time[$value.','."8"] ?></td><td><?php echo $century_end_time[$value.','."8"] ?></td>
                                                        <?php }else{ ?>
                                                        <td>-</td><td>-</td> 
                                                        <?php } ?>
                                                        <?php if(isset($flexo_end_time[$value.','."9"])) {?>
                                                        <td><?php echo $flexo_start_time[$value.','."9"] ?></td><td><?php echo $flexo_end_time[$value.','."9"] ?></td>
                                                        <?php }else{ ?>
                                                        <td>-</td><td>-</td> 
                                                        <?php } ?>
                                                    </tr>
                                                    <?php $i++;} ?>
                                                </tbody>
                                            </table>
                                           </div>
                                        </div>

                                    </div>
                            </div>
                            <!-- สิ้นสุดตารางที่ 3 -->
                        </div>
                    </div>
                </div>
            </div>
        </div> 
        <div id="modal-form-check-data" class="modal fade" aria-hidden="true">
            <div class="modal-dialog" style="max-width: 1000px;">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12"><h3 class="m-t-none m-b">PDR Data IN Machine</h3>
                                    <div class="tabs-container">
                                        <div class="tab-content">
                                            <div class="panel-body">
                                                <div class="table-responsive">
                                                     <div id="result_machine"></div>                                       
                                                </div>
                                            </div>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<?php } ?>
<script type="text/javascript">
    $(document).ready(function(){
        var ctx = document.getElementById('Time_barchart');

            var myBarChart = new Chart(ctx, {
                type: 'bar',
                data: {
                  labels: [
                  <?php
                        foreach ($prod_order_no as $key => $value) {
                          echo  '\''  . $value. '\',';
                        }
                     ?>
                  ],
                  datasets: [{
                    label: 'Time Plan',
                    backgroundColor: "#FE3939",
                    hoverBackgroundColor: "#FE3939",
                    borderColor: "#FE3939",
                    borderWidth: 1,
                    data: [
                      <?php
                        foreach ($prod_order_no as $key => $value) {
                          echo  '\''  . round($production_minutes[$key]). '\',';
                        }
                     ?>
                    ]
                }, {
                    label: 'Real Time',
                    backgroundColor: "#56d798",
                    hoverBackgroundColor: "#56d798",
                    borderColor: "#56d798",
                    borderWidth: 1,
                    data: [
                      <?php
                        foreach ($prod_order_no as $key => $value) {
                          echo  '\''  . round($real_time[$key]). '\',';
                        }
                     ?>
                    ]
                }]
                },
                options: {
                    scales: {
                        xAxes: [{
                            barPercentage: 0.5,
                            barThickness: 6,
                            maxBarThickness: 8,
                            minBarLength: 2,
                            gridLines: {
                                offsetGridLines: true
                            },
                            ticks: {
                                fontSize: 10
                            },
                        }]
                    }
                }
            });
    });
</script>
<script>
        $(document).ready(function(){
            $('.dataTables-example').DataTable({
                pageLength: 25,
                responsive: true,
                dom: '<"html5buttons"B>lTfgitp',
                buttons: [
                    {extend: 'copy'},
                    {extend: 'csv'},
                    {extend: 'excel', title: 'ExampleFile'},
                    {extend: 'pdf', title: 'ExampleFile'},

                    {extend: 'print',
                     customize: function (win){
                            $(win.document.body).addClass('white-bg');
                            $(win.document.body).css('font-size', '10px');

                            $(win.document.body).find('table')
                                    .addClass('compact')
                                    .css('font-size', 'inherit');
                        }
                    }
                ]

            });

        });
        $(document).ready(function(){
            $("span.pie_00").peity("pie", {
                fill: ['#d7d7d7']
            });
            $("span.pie_01").peity("pie", {
                fill: ['#ff9900']
            });
            $("span.pie_02").peity("pie", {
                fill: ['#1ab394']
            });
        });
</script>
<script type="text/javascript">
    $(document).ready(function(){
            $(".buttons-copy").addClass('btn btn-default');
            $(".buttons-copy").css({"background-color": "white"});
            $(".buttons-copy span").css({"color": "#000000"});
            $(".buttons-csv").addClass('btn btn-info');
            $(".buttons-csv").css({"background-color": "#23c6c8"});
            $(".buttons-excel").addClass('btn btn-primary');
            $(".buttons-excel").css({"background-color": "#1ab394"});
            $(".buttons-pdf").addClass('btn btn-danger');
            $(".buttons-pdf").css({"background-color": "#ed5565"});
            $(".buttons-print").addClass('btn btn-success');
            $(".buttons-print").css({"background-color": "#1c84c6"});
    });
</script>