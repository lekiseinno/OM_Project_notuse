
<?php
			require_once 'connect.php';


			$image = $_POST['image_old'];

			if($_FILES["image"]["name"]!=''){
				$target_dir			=	"../_losstime_image";
				$file				=	$_FILES['image']['name'];
				$tmpFile			=	$_FILES['image']['tmp_name'];
				$temp				=	explode(".",$_FILES['image']['name']);
				$image			=	.$prod_order_no'.'.end($temp);
				$target_file		= 	$target_dir.iconv('UTF-8','windows-874',$image);
				#echo $target_file.'<br>';
				if (!file_exists($target_file)) {
					move_uploaded_file($tmpFile,$target_file);
					chmod(($target_file), 0777);
				}
			}



			$prod_order_no=$_POST['loss_prod'];
			$production_time_late=$_POST['production_time_late'];
			$losstime_paper=$_POST['losstime_paper'];
			$losstime_block=$_POST['losstime_block'];
			$losstime_color=$_POST['losstime_color'];
			$losstime_machine=$_POST['losstime_machine'];
			$losstime_other=$_POST['losstime_other'];
			$cause_production_time_late=$_POST['cause_production_time_late'];
			$solutions=$_POST['solutions'];


			$sql = "UPDATE [dbo].[losstime] SET 
				      ,[production_time_late] = '$production_time_late'
				      ,[losstime_paper] = '$losstime_paper'			
				      ,[losstime_block] = '$losstime_block'
				      ,[losstime_color] = '$losstime_color'
				      ,[losstime_machine] = '$losstime_machine'
				      ,[losstime_other] = '$losstime_other'
				      ,[cause_production_time_late] = '$cause_production_time_late'
				      ,[image] = '$image'
				      ,[solutions] = '$solutions'
     				WHERE prod_order_no = '$prod_order_no'";


			$query = sqlsrv_query( $connect, $sql ) or die($sql);

			if($query){
				echo 1 ;
			}else{
				echo 0;
			}
			
		
		

			sqlsrv_close($connect);


?>


