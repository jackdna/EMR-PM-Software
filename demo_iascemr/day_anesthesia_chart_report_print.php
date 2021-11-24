<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
	$html2PdfDir = "new_html2pdf_reports";
	include("day_anesthesia_chart_report_print_export.php");
	$table_pdf = str_ireplace("new_html2pdf/","../new_html2pdf/",$table_pdf);
	if(trim($savePdfConfirmationId) && trim($table_pdf) != "") {
		//DO NOTHING
	}else {
			
?>
<!DOCTYPE html>
<html>
<head>
<title>Day Anethesia Chart Report</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="nofollow">
<meta name="googlebot" content="noindex">
</head>
<body>  
    <form name="printlocal_anes" action="<?php echo $html2PdfDir;?>/createPdf.php?op=p" method="post">
    </form>
    <script >
    function submitfn()
    {
        document.printlocal_anes.submit();
    }
	
	</script>
<?php		
		if(trim($table_pdf) != ""){
			$fp = fopen($html2PdfDir.'/pdffile.html','w');
			$intBytes = fputs($fp,$table_pdf);
			//die($table_pdf);
			fclose($fp);
			if($intBytes !== false){
			?>
			
					<script type="text/javascript">
						
						submitfn();
						//window.open('new_html2pdf/createPdf.php?op=p','pdfPrint','scrollbars=1,resizable=1,width='+parWidth+'height='+parHeight+'');
					</script>
			<?php
			}
		}else{
?>
		<script>
            if(document.getElementById("loader_tbl")) {
                document.getElementById("loader_tbl").style.display = "none";	
            }
        </script>	
        <table align="center" width="100%" border="0" cellpadding="1" cellspacing="1">
            <tr class="text_9" height="20" bgcolor="#EAF0F7" valign="top">
                <td align="center">No Result.</td>
            </tr>
        </table>
<?php
		}
?>
		</body>
</html>
<?php
	}
?>