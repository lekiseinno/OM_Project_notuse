
<!DOCTYPE html>
<html>
<head>
	<title>	</title>

</head>
<body>
	<?php include("include/head.php"); ?>


	<div id="wrapper">

<?php include("include/side_bar.php"); ?>
<?php 
	require_once '_process/connect.php';
    $sql = "SELECT * FROM machine ";
    $query = sqlsrv_query($connect, $sql) or die($sql);
   
 ?>

        <div id="page-wrapper" class="gray-bg">
<?php include("include/top_nav.php"); ?>
	        <div class="row wrapper border-bottom white-bg page-heading">
	        	<div class="col-lg-10">
	                <h2>OM Planning</h2>
	                <ol class="breadcrumb">
	                    <li class="breadcrumb-item">
	                        <a href="index.php">Home</a>
	                    </li>
	                    <li class="breadcrumb-item active">
	                        <a href="_plan_upload.php">Plan Upload</a>
	                    </li>
	                </ol>
	            </div>  
	        </div>
	        <div class="wrapper wrapper-content animated fadeInRight">
	        	<div class="row">
	                <div class="col-lg-12">
	                	<form action="_process/plan_insert.php" id="form_plan_upload" method="post" enctype="multipart/form-data" onsubmit="return confirm('confirm to action')">
		                    <div class="ibox ">
		                        <div class="ibox-content">
		                        	<span>File :</span>
		                            <div class="custom-file">
									    <input id="logo" type="file" class="custom-file-input" name="plan_file" onchange="show_sheets()">
									    <label for="logo" class="custom-file-label">Choose file...</label>
									</div><br><br>
									<span>Plan Date :</span>
									<input type="date" class="form-control" name="plan_date" value="<?php echo date('Y-m-d') ?>"><br>
									<span>Machine :</span>
									<select name="machine_id" class="form-control">
							<?php while ($row = sqlsrv_fetch_array($query,SQLSRV_FETCH_ASSOC)) { ?>
										<option value="<?php echo $row['machine_id']?>"><?php echo $row['machine_name']?></option>
							<?php } ?>
									</select><br>
									<span>sheets :</span>
									<div class="show_sheets"></div><br>
									<button type="submit" class="btn btn-lg btn-primary">Upload</button>
		                        </div>
		                    </div>
		                </form>
	                </div>
	            </div>
			</div>
		</div>
	</div>


	<?php include("include/footer.php"); ?>


<script type="text/javascript">
	$('.custom-file-input').on('change', function() {
	   let fileName = $(this).val().split('\\').pop();
	   $(this).next('.custom-file-label').addClass("selected").html(fileName);
	}); 


	function show_sheets(value){
		var formData = new FormData($("#form_plan_upload")[0]);
		$.ajax({
            url: '_process/show_sheets.php',
            type: 'POST',
            dataType: 'TEXT',
            cache: false,
	        contentType: false,
	        processData: false,
            async: false,
            data: formData
        }).done(function(data) {
        	$('.show_sheets').html(data);
        }); 
	}
</script>
</body>
</html>







