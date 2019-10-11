<?php
error_reporting(0); 
include("include/head.php"); ?>
<link href='https://fonts.googleapis.com/css?family=Kanit:400,300&subset=thai,latin' rel='stylesheet' type='text/css'>
<style type="text/css">
    body {
        font-family: 'Kanit', sans-serif;
    }
    h1 {
        font-family: 'Kanit', sans-serif;
    }
    table{
        font-family: "open sans", "Helvetica Neue", Helvetica, Arial, sans-serif;
    }
    .select2-container--default 
    .select2-selection--single{
        height: 35px;
        margin-right: 3%;
    }
    .select2-container--default 
    .select2-selection--single 
    .select2-selection__rendered{
        line-height: 35px;
    }
    .select2-container--default 
    .select2-selection--single 
    .select2-selection__arrow b{
        margin-top: 0px;
        margin-left: -12px;
    }
    #report_excel{
        float: right;
        margin-left: 3%;
    }
    .btn-group span{
        color: #FFFFFF;
    }
    .nav-tabs .nav-item.show .nav-link, .nav-tabs .nav-link.active{
        color: #000000 !important;
    }
    .page-item .page-link{
        color: #000000 !important;
    }
    .page-item.active .page-link{
        color: #FFFFFF !important;
    }
    .pointer{cursor: pointer;}
</style>
<body onload="side_bar_unactive()">

    <div id="wrapper">

    <?php include("include/side_bar.php"); ?>

        <div id="page-wrapper" class="gray-bg">
        <?php include("include/top_nav.php"); ?>
        <div class="row wrapper border-bottom white-bg page-heading">
          <div class="col-lg-12">
                 <h2>OM Planning</h2>
                 <ol class="breadcrumb">
                     <li class="breadcrumb-item">
                         <a href="index.php">Home</a>
                     </li>
                     <li class="breadcrumb-item active">
                         <a href="production_report.php">Production Report</a>
                     </li>
                 </ol>
                <?php $arr_machine=array('WING','S and S','SUNRISE','LANGTON','ROTARY','SLEEVE','รอยต่อ','CENTURY','New Flexo Machine'); ?>
                <form action="export/report_manufac.php" method="POST">
                <div class="form-group" id="data_1" style="width: 50%;display: inline-flex;">
                 <select class="select2_machine form-control" id="machine" name="machine" style="width: 30%;">
                     <?php foreach ($arr_machine as $key => $value) {?>
                     <option value="<?php echo $value ?>" <?php if($value == $_POST["machine"]){ echo " selected=\"selected\""; } ?>><?php echo $value ?></option>
                     <?php } ?>
                 </select>
                 <?php if($_POST["date_plan"]==""){
                    $date_plan = date("d/m/Y");
                 }else{
                    $date_plan = $_POST["date_plan"];
                 } ?>
                     <div class="input-group date" style="width: 30%;margin-right: 1%;">
                         <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="text" id="date_plan" name="date_plan" class="form-control pointer" value="<?php echo $date_plan; ?>" readonly>
                     </div>
                   <!-- <a href="export/report_manufac.php?report_machine=<?php echo $_POST["machine"]; ?>&report_date_plan=<?php echo $_POST["date_plan"]; ?>" class="btn btn-primary" id="report_excel" target="_blank"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Export Excel</a>   -->
                     <button type="button" id="search_btn" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
                     <button type="submit" class="btn btn-primary" id="report_excel" title="Search For Report"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Export Excel</button>
                 </div>
                 </form>
                 <!-- <a class="btn btn-primary" data-toggle='modal' href='#modal-form-config-oee' title='คลิกที่นี่เพื่อตั้งค่า OEE'><i class="fa fa-cog"></i> Config OEE</a> -->
                <?php include("include/config_oee.php"); ?>
             </div>  
         </div>
         <div class="result"></div>
         <div id="Pload" style="text-align: center;margin-top: 3%;">
            <i class='fa fa-spinner fa-spin' style='font-size:100px' align="center"></i><br>
            <strong>Please Wait Page Loading.....</strong>
         </div>
        
    <script>
        $(document).ready(function(){
            $('.dataTables-example').DataTable({
                pageLength: 25,
                responsive: true,
                dom: '<"html5buttons"B>lTfgitp',
                buttons: [
                    { extend: 'copy'},
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
            var mem = $('#data_1 .input-group.date').datepicker({
                todayBtn: "linked",
                keyboardNavigation: false,
                forceParse: false,
                calendarWeeks: true,
                autoclose: true,
                format: "dd/mm/yyyy"
            });
        });
        $(document).ready(function(){
            var mem = $('#data_2 .input-group.date').datepicker({
                todayBtn: "linked",
                keyboardNavigation: false,
                forceParse: false,
                calendarWeeks: true,
                autoclose: true,
                format: "dd/mm/yyyy"
            });
        });
        $(document).ready(function(){
            $(".select2_machine").select2();
        });
        // $(document).ready(function(){
        //     $(".select2_config").select2();
        // });
      $(document).ready(function(){
      $('#Pload').hide();
      $("#report_excel").attr("disabled",true);
      $("#search_btn").click(function(){
      $('.result').hide();
      $('#Pload').show();
      $.post("ajax/report_data.php",
      {
        machine: $("#machine").val(),
        date_plan: $("#date_plan").val()
      },
      function(data){
        $('#Pload').hide();
        $('.result').show();
        if($("#machine")=="" && $("#date_plan")==""){
            $("#report_excel").attr("disabled",true);
        }
            $("#report_excel").attr("disabled",false);
            $(".result").html(data);
             });
        });
       });
      // $(document).ready(function(){
      // $("#search_btn").click(function(){
      // $.post("ajax/update_config.php",
      // {
      //   machine: $("#machine").val()
      // },
      // function(data){
      //   $('#Pload').hide();
      //   $('.result').show();
      //   if($("#machine")=="" && $("#date_plan")==""){
      //       $("#report_excel").attr("disabled",true);
      //   }
      //       $("#report_excel").attr("disabled",false);
      //       $(".result").html(data);
      //           });
      //       });
      //   });
    </script>
<script type="text/javascript">
  function side_bar_unactive(){
    $('body').addClass("mini-navbar");
  }
</script>

<?php include("include/footer.php"); ?>
