<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
/*
FILE : FILE_SAVE_EXPORT.PHP
PURPOSE :  FORCE FILE DOWNLOAD
ACCESS TYPE : INCLUDED
*/
$filename=$_REQUEST['fn'];
$fileArr=explode('/',$filename);
$fileSoloName=$fileArr[sizeof($fileArr)-1];
$content_type = "application/force-download";
header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private",false);
header("Content-Description: File Transfer");
header("Content-Type: ".$content_type."; charset=utf-8");
header("Content-disposition:attachment; filename=\"".$fileSoloName."\"");
header("Content-Length: ".@filesize($filename));
@readfile($filename) or die("File not found.");
exit;
?>