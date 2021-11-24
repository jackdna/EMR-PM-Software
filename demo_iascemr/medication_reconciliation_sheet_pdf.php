<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
session_start();
include_once("common/conDb.php");
//print '<pre>';
$root=($_SERVER['DOCUMENT_ROOT']);
include("common_functions.php");
//include("common/linkfile.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
// allergies_status_reviewed(table field t be added)
include("new_header_print.php");
include_once("medication_reconciliation_sheet_pdf_content.php");
$table_printh=$table_main;
//die($table_main);

$fileOpen = fopen('new_html2pdf/pdffile.html','w+');
$filePut = fputs(fopen('new_html2pdf/pdffile.html','w+'),$table_printh);
fclose($fileOpen);
?>
<script language="javascript">
	function submitfn()
	{
		document.printFrm.submit();
	}
</script>
<?php 
if($reconSheetFormStatus =='completed' || $reconSheetFormStatus =='not completed') {
?>

<table bgcolor="#FFFFFF"  style="font:vetrdana; font-size:14;" width="100%" height="100%">
	<tr>
		<td width="100%" align="center" valign="middle"><img src="images/ajax-loader.gif"></td> 
	</tr>
</table>


<form name="printFrm" action="new_html2pdf/createPdf.php?op=p" method="post">
</form> 
	
<script type="text/javascript">
	submitfn();
</script>
<?php
}else {
	echo "<center>Please verify/save this form before print</center>";
}
?>	
