<?php 
	session_start();
	include("common/conDb.php");
	include_once("admin/classObjectFunction.php");
	include_once('patient_license_sheet_template.php');
	$objManageData = new manageData;
	$printSxPlanningSheet = "yes";
	$Html		=	'
		<style>
			table { border-collapse : collapse; }	
			td { border:0px; }
			.mainTable { border:0px; width:700px; font-size:14px; vertical-align:top;  margin-top:20px; }
			.font_12 { font-size:12px; }
			.font_14 { font-size:14px; }
			.font_16 { font-size:16px;}
			.font_30 { font-size:30px; }
			.height_10 { height:10px; min-height:10px; }
			.shadow { box-shadow:2px 2px 5px #ddd}
			.bordered { border:solid 1px #333; }
			.PL_10 { padding-left:10px;}
			.PR_10 { padding-right:10px;}
			.borderBottom{ border-bottom : solid 1px #333; }
			.borderRight{ border-right : solid 1px #333; }
			.borderLeft{ border-left : solid 1px #333; }
			.borderTop{ border-top: solid 1px #333; }
			.borderBottomLight{ border-bottom : solid 1px #eee; }
			.spacediff { word-spacing: 10px;}
		</style>
	
	
	' ;
	include_once('patient_license_sheet_common.php');

	if($Html)
	{
		$fp 					= fopen('new_html2pdf/pdffile.html','w+');
		$content				= fputs($fp,$Html);
		fclose($fp); 
		//die($Html);
	
?>
		<form name="print_patient_license_sheet" action="new_html2pdf/createPdf.php" method="post"></form>
        <div style="text-align:center;">Please Wait<br /><img src="images/ajax-loader5.gif" width="80" height="80"></div>
        <script language="javascript">
			window.focus();
			function submitfn()
			{
				document.print_patient_license_sheet.submit();
			}
		</script>
        <script type="text/javascript">
			submitfn();
		</script>
<?php 
	}
	else
	{
 ?>
		<table cellpadding="0" cellspacing="0" width="100%">
        		<tr valign="top" height="20" bgcolor="#F8F9F7" class="text_10b"  style="font-size:11px; ">
						<td  align="center">No Record Found</td>
              	</tr>
     	</table>
<?php
	}
	
?>