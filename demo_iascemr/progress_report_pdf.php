<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
include_once("common/conDb.php");
include("common_functions.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
$pConfId = $_REQUEST['pConfId'];
//include_once("header_print.php");
include_once("new_header_print.php");
$lable="Amendments";
?>
<body  style="background-color:#FFFFFF; ">	
<?php
/***************************************** Progress Notes ******************************/
	
	$progress_notes_query = "SELECT tblprogress_report.intProgressID, tblprogress_report.txtNote,
	tblprogress_report.confirmation_id, users.fname, users.mname, users.lname, users.user_type,
	tblprogress_report.dtDateTime, tblprogress_report.tTime
	FROM tblprogress_report, users
	WHERE tblprogress_report.confirmation_id = '$pConfId'  AND users.usersId = tblprogress_report.usersId
	ORDER BY dtDateTime DESC, tTime DESC";
	$resourceProgressNotes = imw_query($progress_notes_query) or die(imw_error());
	$totalRows_Progress_notes = imw_num_rows($resourceProgressNotes);

	if($totalRows_Progress_notes > 0) {
		
		$lable="Progress Notes";
		$table_main4=$head_table."\n";
		
		$table_main4.='<table align="center" width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">';				
		$table_main4.='	<tr>
							<td colspan="5" align="center" height="7"><strong>'.$lable.'</strong></td>
						</tr>';
		while ($row_rsNotes = imw_fetch_array($resourceProgressNotes))
		{
			$ProgressNotesTime = $row_rsNotes['tTime'];
			//CODE TO SET $ProgressNotesTime 
			if($ProgressNotesTime=="00:00:00" || $ProgressNotesTime=="") {
				$ProgressNotesTime = $objManageData->getTmFormat(date("H:i:s"));
			}
			else{
				$ProgressNotesTime = $objManageData->getTmFormat($ProgressNotesTime);
			}
			$datestring= $row_rsNotes['dtDateTime']; 
			$d=explode("-",$datestring);
			$date =  $d[1]."/".$d[2]."/".$d[0];
				
			$table_main4.='
				<tr style=" height:100px;">
					<td style="margin-top:50px; width:150px;" align="left" valign="top" ><font face="Verdana, Arial, Helvetica, sans-serif"; size="-1"><strong>'.$date.'</strong></font></td>
					<td style="width:150px;"   align="left" valign="top" ><font face="Verdana, Arial, Helvetica, sans-serif"; size="-1"><strong>'.$ProgressNotesTime.'</strong></font></td>
					<td style="width:150px;"   align="left" valign="top" ><font face="Verdana, Arial, Helvetica, sans-serif"; size="-1"><strong>'.$row_rsNotes['user_type'].'</strong></font></td>
					<td style="width:150px;"   align="left" valign="top" ><font face="Verdana, Arial, Helvetica, sans-serif"; size="-1"><strong>'.$row_rsNotes['fname']." ".$row_rsNotes['lname'].'</strong></font></td>
					<td align="left" valign="top" ><font face="Verdana, Arial, Helvetica, sans-serif"; size="-1"><strong>&nbsp;</strong></font></td>
				</tr>
				<tr>
					<td colspan="5" align="left">'.htmlentities($row_rsNotes['txtNote']).'</td>
				</tr>
				<tr>
					<td colspan="5" align="left" height="10">&nbsp;</td>
				</tr>
				';
		}
		$table_main4.='</table>';	
		//$table.='<newpage>';
		$table.=$table_main4;
	}
/***************************************** Progress Notes ******************************/

  
$fileOpen = fopen('new_html2pdf/pdffile.html','w+');
$filePut  = fputs($fileOpen,$table);
fclose($fileOpen);

?>
<script language="javascript">
	function submitfn()
	{
		document.printFrm.submit();
	}
</script>
<table bgcolor="#FFFFFF"  style="font:vetrdana; font-size:14;" width="100%" height="100%">
	<tr>
		<td width="100%" align="center" valign="middle"><img src="images/ajax-loader.gif"></td> 
	</tr>
</table>
<form name="printFrm"  action="new_html2pdf/createPdf.php?op=p" method="post">
</form>		

<script language="javascript">
	function submitfn()
	{
		document.printFrm.submit();
	}
</script>

<script type="text/javascript">
	submitfn();
</script>


</body>