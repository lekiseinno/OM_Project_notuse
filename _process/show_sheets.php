
<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', true);



if($_FILES["plan_file"]["name"]!=''){
	unlink('../_file.xlsx');
	$target_dir			=	"../";
	$file				=	$_FILES['plan_file']['name'];
	$tmpFile			=	$_FILES['plan_file']['tmp_name'];
	$temp				=	explode(".",$_FILES['plan_file']['name']);
	$plan_file			=	'_file.'.end($temp);
	$target_file		= 	$target_dir.iconv('UTF-8','windows-874',$plan_file);
	#echo $target_file.'<br>';
	if (!file_exists($target_file)) {
		move_uploaded_file($tmpFile,$target_file);
		chmod(($target_file), 0777);
	}
}



	

require_once '../_lib_excel.php';

#echo '<h1>Read several sheets</h1>';
if ( $xlsx = SimpleXLSX::parse('../_file.xlsx')) {

	#echo '<pre>'.print_r( $xlsx->sheetNames(), true ).'</pre>';

	echo '<div class=row>';

	foreach ($xlsx->sheetNames() as $key => $sheetNames) {
		echo "<div class='col-2' style='border-bottom: 1px solid #e6e6e6 !important'><input type='radio' name='sheets' value='$key' > $sheetNames</div>";
	}

	echo '</div>';

} else {
	echo SimpleXLSX::parseError();
}

?>
