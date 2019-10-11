<?php 
include("../class/connect.php");
$i = $_POST["i"];
$id = $_POST["id"];
$date_search = $_POST["date_search"];
?>
<table class="table table-stripped table-bordered dataTables-example">
    <thead>
    <tr align="center">
        <th>ลำดับ</th>
        <th>PDR</th>
        <th>Time</th>
        <th>Machine</th>
    </tr>
    </thead>
    <tbody>        
    <tr>
        <td><?php echo $i ?></td>
        <td width="15%"><?php echo $id; ?></td>
        <td>
        <?php 
            $class_con_125_data_time_machine = new Sqlsrv();
            $class_con_125_data_time_machine->getConnect();
            // Select
            $query=$class_con_125_data_time_machine->getQuery("
                SELECT ot.order_start_time AS 'order_start_time_machine', ot.order_end_time AS 'order_end_time_machine'
                FROM planning AS pl RIGHT JOIN orderStartStopTime AS ot ON pl.prod_order_no=ot.prod_order_no
                RIGHT JOIN machine AS ma ON ot.machine_id=ma.id
                AND pl.plan_date = '".$date_search."' 
                AND pl.prod_order_no = '".$id."'
            ");
            $y=0;    
                while($result=$class_con_125_data_time_machine->getResult($query)){
            $s = $y % 2;
            if($s == '1'){ 
                $color_bar = '#FFFFFF';
            }else{ 
                $color_bar = '#DFEFFF';
            }?>
        <?php if($result["order_start_time_machine"]!="" && $result["order_end_time_machine"]!=""){ ?>
            <div style="background-color: <?php echo $color_bar ?>"><?php echo date('H:i',strtotime($result["order_start_time_machine"]))."-".date('H:i',strtotime($result["order_end_time_machine"])); ?></div> 
        <?php }else{?>  
        <div></div>     
        <?php } $y++; } ?> 
        </td>
        <td>
        <?php 
            $class_con_125_data_machine = new Sqlsrv();
            $class_con_125_data_machine->getConnect();
            // Select
            $query=$class_con_125_data_machine->getQuery("
                SELECT ma.machine_name AS 'machine_name'
                FROM planning AS pl LEFT JOIN orderStartStopTime AS ot ON pl.prod_order_no=ot.prod_order_no
                LEFT JOIN machine AS ma ON ot.machine_id=ma.id
                AND pl.plan_date = '".$date_search."' 
                AND pl.prod_order_no = '".$id."' 
                ORDER BY ot.id ASC
            ");
        $y=0;    
            while($result=$class_con_125_data_machine->getResult($query)){
        $s = $y % 2;
        if($s == '1'){ 
            $color_bar = '#FFFFFF';
        }else{ 
            $color_bar = '#DFEFFF';
        }?>
        <?php if($result["machine_name"]!=""){ ?>
        <div style="background-color: <?php echo $color_bar ?>"><?php echo $result["machine_name"]; ?></div> 
        <?php }else{?>  
        <div></div>     
        <?php } $y++; } ?>
        </td>
    </tr>  
    </tbody>
</table>