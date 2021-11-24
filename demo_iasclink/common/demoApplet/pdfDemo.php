<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
include_once("../../globals.php");
$scan_doc_id = $_SESSION['scan_doc_id'];
$table = '
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>R2 : Optical Order Form</title>
</head>


<body topmargin="0" rightmargin="0" leftmargin="0">

<table width="400" border="0" id="print_tbl1" align="center" cellpadding="1" cellspacing="1" style="border:1px solid #4684ab;">';
$patient_id = $_SESSION['patient'];
$path = "uploaddir/PatientId_".$patient_id."/uploaddir/"; //'//192.168.0.3/documents/test';
$ab =  opendir($path);
while(($filename = readdir($ab)) !== false)
	{
		$path1 = pathinfo($filename);
		if($path1['extension'] == 'jpg')
		{
			$table .='<tr><td align="center">
			<img src="'.$path.'\\'.$filename.'" height="876" width="564" align="center">
		
			</td></tr>';
		}
				
	}
	$table .='</table>
</body>
</html>';
//echo $table;
$file = fopen('pdfFile.html','w');
$data = fputs($file,$table);
fclose($file);
$folder_id=$_REQUEST['folder_id'];
 /* if($_REQUEST[comments]){
  $comment12 = $_REQUEST['comments'];
  if($comment12) {
        $chkCmntQry = "select * from ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl where patient_id='$patient_id' && folder_categories_id='$folder_id' && doc_upload_type='scan' && scan_doc_id = '$scan_doc_id'";
		$chkCmntRes =imw_query($chkCmntQry);
		if(imw_num_rows($chkCmntRes)>0){
			$chkCmntRow = imw_fetch_array($chkCmntRes);
			$chkDocUploadDate = $chkCmntRow['upload_date'];
			//$scanOrUploadDate = '$chkDocUploadDate';
           $explDtTm = explode(' ',$chkDocUploadDate);
           list($yr, $mnth, $dy) = explode('-',$explDtTm[0]);
           list($hr, $min, $scnd) = explode(':',$explDtTm[1]);
           $chkNewDt = date('y-m-d H:i:s', mktime($hr,$min,$scnd-20,$mnth,$dy,$yr));
           }
  $qry = "update ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl  set scandoc_comment ='$comment12' where patient_id='$patient_id' && folder_categories_id='$folder_id' && doc_upload_type='scan' && upload_date >= '$chkNewDt'";
  $res = imw_query($qry);
}
}*/
?>
<form name="frm" action="index.php">
	<input type="hidden" name="page" value="5">
	<input type="hidden" name="comments" value="<?php echo $_REQUEST['comments'];?>">
    <input type="hidden" name="font_size" value="10">
	<!--<input type="hidden" name="comment" value="<?php echo $_REQUEST['comments'];?>" />-->
	<input type="hidden" name="folder_id" value="<?php echo $_REQUEST['folder_id']; ?>"  />
	<input type="hidden" name="edit_id" value="<?php echo $_REQUEST['edit_id']; ?>"  />
</form>
<script language="javascript">


 document.frm.submit();

</script>