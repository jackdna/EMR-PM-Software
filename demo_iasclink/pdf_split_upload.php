<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
session_start();
include_once("common/conDb.php");
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>PDF Management</title>
<script type="text/javascript" src="js/jquery.js"></script>	
<script>
function pdfSplitSubmitFun() {
	$("#pdfSplitAjaxLoadId",top.document).show();
	document.frmPdfSplitUpload.submit();	
}
</script>
<LINK HREF="css/style_surgery.css" TYPE="text/css" REL="stylesheet">
<?php
$spec= "
</head>
<body>";

include("common/link_new_file.php");
$selDos = $_REQUEST['selDos'];
//$path = getcwd()."\\";
$path = realpath(dirname(__FILE__));
$pdfFileTempNme = $_FILES['pdfFileNme']['tmp_name'];
$pdfFileNme = $_FILES['pdfFileNme']['name'];
$pdfFileNmeValue='';
$hiddPdfSplit = $_REQUEST['hiddPdfSplit'];
if($hiddPdfSplit=='yes') {
	if(strpos($pdfFileNme,'.pdf')!==false) {
		$pdfFileNme = urldecode($pdfFileNme);
		$pdfFileNme = str_ireplace(" ","-",$pdfFileNme);
		$pdfFileNme = str_ireplace(",","-",$pdfFileNme);
		
		$pdfFileNmeValue=str_ireplace('.pdf','',$pdfFileNme);
		$pdfFileNme = str_ireplace(".","",$pdfFileNme);
			
		//START CODE THAT SUPPORT LINUX ALSO
		if(!is_dir($path.'/pdfSplit')) {
			mkdir($path.'/pdfSplit', 0777);
		}
		$pathNew = $path.'/pdfSplit/'.$selDos;
		
		if(!is_dir($pathNew)) {
			mkdir($pathNew, 0777);
		}
		if(!is_dir($path.'/pdfSplit/tmp')) {
			mkdir($path.'/pdfSplit/tmp', 0777);
		}
		$pdfFileNmeValue = $pdfFileNmeValue.'_'.$_SESSION['iolink_loginUserId'].'_';
		@copy($pdfFileTempNme,'pdfSplit/tmp/'.$pdfFileNmeValue.'.pdf');
		//echo 'pdftk '.$path.'/pdfSplit/tmp/'.$pdfFileNmeValue.'.pdf  burst output '.$pathNew.'/'.$pdfFileNmeValue.'%03d.pdf';
		//echo 'pdftk '.$pdfFileTempNme.' burst output '.$pathNew.'/'.$pdfFileNmeValue.'%03d.pdf';
		exec('pdftk '.$path.'/pdfSplit/tmp/'.$pdfFileNmeValue.'.pdf  burst output '.$pathNew.'/'.$pdfFileNmeValue.'%03d.pdf');
		unlink('pdfSplit/tmp/'.$pdfFileNmeValue.'.pdf');
		//END CODE THAT SUPPORT LINUX ALSO

		/*
		$com = new COM("PDFSplitMerge.PDFSplitMerge.1");
		$nPage = $com->GetNumberOfPages($pdfFileTempNme, "");
		//$com->explode($path."1.pdf", "1;1;1", $path."sp1-%d.pdf");
		$pageloop='1'.';';
		if($nPage!='0') {
			for($i=2;$i<=$nPage;$i++) {
				$pageloop.=$i.';';
			}	
		}
		//$com->explode($path."1.pdf", "1;2;3;4;1-2,3-4;1", $path."sp%d.pdf");
		
		if(!is_dir($pathNew)) {
			mkdir($pathNew, 0777);
		}
		//$com->explode($pdfFileTempNme, $pageloop, $pathNew.$pdfFileNmeValue."%d.pdf");
		$com->explode($pdfFileTempNme, $pageloop, $pathNew."/".$pdfFileNmeValue."%d.pdf");
		
		if(file_exists($pathNew."/".$pdfFileNmeValue.$nPage.".pdf")) {
			unlink($pathNew."/".$pdfFileNmeValue.$nPage.".pdf");
		}
		$j=$nPage;
		for($i=$nPage-1;$i>=0;$i--) {
			if(file_exists($pathNew."/".$pdfFileNmeValue.$i.".pdf")) {
				rename($pathNew."/".$pdfFileNmeValue.$i.".pdf",$pathNew."/".$pdfFileNmeValue.$j.".pdf");
				$j--;
			}
		}
		
		$p=$nPage;
		for($q=$nPage-1;$q>=0;$q--) {
			$r = $p;
			$s=$q+1;
			if(strlen($p)==1) {
				$r = "000".$p;
			}else if(strlen($p)==2) {
				$r = "00".$p;
			}else if(strlen($p)==3) {
				$r = "0".$p;
			}
			//if(file_exists($pathNew.$pdfFileNmeValue.$i.".pdf")) {
			rename($pathNew."/".$pdfFileNmeValue.$s.".pdf",$pathNew."/".$pdfFileNmeValue.$r.".pdf");
			$p--;
			//}
		}

		*/
		?>
		<script>
			var selDos = '<?php echo $selDos;?>';
			var maxPg = '<?php echo $nPage;?>';
			if(selDos) {
				//if(opener) {
					//alert(top.document.getElementById('iframePdfSplitId').src);
					if(top.iframePdfSplitId) {
						top.iframePdfSplitId.location.href='pdf_split.php?selDos='+selDos;
					}
				//}
				//window.open('pdfSplit/'+selDos);
				//window.open('show_split_pdf.php?folderNme='+ptNme+'&maxPg='+maxPg);
			}	
		</script>
		<?php
		/*
		$com->Merge($path."sp0.pdf|".$path."sp1.pdf", $path."m1.pdf");
		$com->Merge($path."1.pdf?1-2|".$path."sp1.pdf", $path."m2.pdf");
		$com->Merge($path."1.pdf?1-2|".$path."sp1.pdf?2", $path."m1.pdf");
		*/
		
		//print "<br>".$pathNew."<br>page number of ".$pdfFileNme." is ".$nPage;
	}else {
		$msgSplitPdf = '<font color="#FF0000">Please select PDF file to upload/split</font>';
	}
}

?>

	<form name="frmPdfSplitUpload" enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
		<table style="border:none; padding:2px; width:100%;">
			<tr>
				<td colspan="2" class="text_10b alignCenter" style="height:20px;"><?php echo $msgSplitPdf;?></td>
			</tr>		
			<tr>
				<td class="alignCenter" style="width:250px; height:60px; padding-left:250px;">
						<input type="file" name="pdfFileNme">
						<input type="hidden" name="hiddPdfSplit" value="yes">
						<input type="hidden" name="selDos" value="<?php echo $selDos;?>">
						<!-- <input type="button" name="uploadPdf" value="Upload PDF" onClick="this.form.submit();"> -->
				</td>
				<td class="alignLeft" >
					<a style="width:120px; " href="#" onClick="MM_swapImage('uploadImgPdfSplit','','images/upload_click.gif',1)" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('uploadImgPdfSplit','','images/upload_hover.gif',1)">
						<img src="images/upload.gif" style="border:none;" id="uploadImgPdfSplit" alt="Upload Pdf" onClick="javascript:pdfSplitSubmitFun();" />
					</a>
				
				</td>
			</tr>
		</table>
	</form>				
</body>
</html>
