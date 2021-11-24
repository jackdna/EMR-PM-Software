<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
$fileName = $_POST['fileName'];
if($fileName){
	$filePath = 'pdf/'.$fileName;
	header("Content-Type: application/download");
	header("Content-Disposition: attachment; filename=".$fileName."");	
	header("Content-Transfer-Encoding: binary");
	header('Content-Length: '.filesize($filePath));
	readfile("$filePath");
}
?>