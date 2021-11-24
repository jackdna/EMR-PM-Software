<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/
?>
<?php
include("../../../config/globals.php");
include_once($GLOBALS['fileroot']."/library/classes/SaveFile.php");
$upload_dir = "../../../data/".PRACTICE_PATH;
$browserIpad = 'no';
if(stristr($_SERVER['HTTP_USER_AGENT'], 'ipad') == true) {
	$browserIpad = 'yes';
}
$id = $_REQUEST['id'];
$library_path = $GLOBALS['webroot'].'/library';
$oSaveFile = new SaveFile($patient_id);
?>
<html>
<head>
	<meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
  <title> Documents Details :: imwemr ::</title>
  <!-- Bootstrap -->
  <link href="<?php echo $library_path;?>/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
  
  <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
  <script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js"></script>
  <!-- Bootstrap -->
  <script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap.js"></script>
  <script type="text/javascript" src="<?php echo $library_path; ?>/js/common.js"></script>
	<script type='text/javascript'> 
		if(parent.document.getElementById('btn_save')){
				 parent.document.getElementById('btn_save').className ='btn btn-success invisible';
		}
		function GetXmlHttpObject()
		{ 
			var objXMLHttp=null
			if (window.XMLHttpRequest)
			{
			objXMLHttp=new XMLHttpRequest()
			}
			else if (window.ActiveXObject)
			{
			objXMLHttp=new ActiveXObject("Microsoft.XMLHTTP")
			}
			return objXMLHttp;
		}
		window.onafterprint = function(){
			var xmlHttp;				
			xmlHttp = GetXmlHttpObject()				
			if(xmlHttp==null){
				alert ("Browser does not support HTTP Request")
				return;
			}
			var pk_id = "<?php echo $id;?>";
			var url = 'documents_reports.php?id='+pk_id;
			//alert(url)
			//Ajax Call For Audit Functionality	
			xmlHttp.onreadystatechange = function (){									
				//alert(xmlHttp.readyState);
				if(xmlHttp.readyState == 4){
				//alert(xmlHttp.responseText);						
					if(xmlHttp.responseText == "DONE"){						
						return true;
					}						
				}
			}
			xmlHttp.open("GET",url,true);
			xmlHttp.send(null);			
		}	
 	</script>
</head>
<body>
<div style="max-height:99%; display:none;" id="div_PdfContent">
	<iframe name="ifrm_PdfContent" width="100%" scrolling="yes" height="100%" src=""></iframe>
</div>
<?php 
	$qry_doc = imw_query("select * from document_patient_rel where id='$id'");
	$doc_fet = imw_fetch_array($qry_doc);
	$content = stripslashes($doc_fet['description']);
	
	$doc_scn_upload_from = $doc_fet['doc_scn_upload_from'];
	$scan_doc_file_path = $doc_fet['scan_doc_file_path'];
	$upload_doc_file_path = $doc_fet['upload_doc_file_path'];
	$upload_doc_type = $doc_fet['upload_doc_type'];
	$scan_doc_date = $doc_fet['scan_doc_date'];
	$upload_doc_date = $doc_fet['upload_doc_date'];
	$patient_id = $_SESSION['patient'];

	$regpattern='|<a class=\"cls_smart_tags_link\" href=(.*) id=(.*)>(.*)<\/a>|U'; 
	$content = preg_replace($regpattern, "\\3", $content);
	
	$physical_path=data_path();
	global $myExternalIP, $RootDirectoryName;
	if($protocol == ''){ $protocol=$_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://'; }
	
	
	$content = str_ireplace($GLOBALS['webroot'].'/data/'.PRACTICE_PATH.'/','../../data/'.PRACTICE_PATH.'/',$content);
	
	
	/*if($RootDirectoryName==PRACTICE_PATH){
	$content = str_ireplace($GLOBALS['webroot'].'/data/'.PRACTICE_PATH.'/',$protocol.$myExternalIP.'/'.$RootDirectoryName.'/data/'.PRACTICE_PATH.'/',$content);
	}else{
	$content = str_ireplace('/'.$RootDirectoryName.'/data/'.PRACTICE_PATH.'/',$protocol.$myExternalIP.'/'.$RootDirectoryName.'/data/'.PRACTICE_PATH.'/',$content);
	$content = str_ireplace($GLOBALS['webroot'].'/data/'.PRACTICE_PATH.'/',$physical_path.'/',$content);
	} */
	//$content = str_ireplace($GLOBALS['webroot'].'/data/'.PRACTICE_PATH.'/gn_images/','../data/'.PRACTICE_PATH.'/gn_images/',$content);
	$content = str_ireplace($GLOBALS['webroot'].'/library/images/','../../library/images/',$content);
	$content = str_ireplace('../../../library/images/','../../library/images/',$content);
	$content = str_ireplace('../../../data/'.PRACTICE_PATH.'/','../../data/'.PRACTICE_PATH.'/',$content);
	$content = str_ireplace($GLOBALS['webroot']."/interface/common/new_html2pdf/","",$content);
	$content = str_ireplace($protocol.$phpServerIP.$web_root."/redactor/images/",$web_root."/redactor/images/",$content);
	$content = str_ireplace($web_root."/redactor/images/","../../../redactor/images/",$content);
	$content = str_ireplace("&Acirc;","",$content);
	$content = str_ireplace("&nbsp;","",$content);
	$tmp = core_get_patient_name($patient_id);
	$patient_name = core_name_format($tmp[2],$tmp[1],$tmp[3]);
	
	$strHTML  = '
		<page backtop="10mm" backbottom="10mm">			
			<page_footer>
				<table style="width: 100%;">
					<tr>
						<td style="text-align: center;	width: 100%">Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>
			
			<page_header>
				<table style="background-color:#ccc; width:100%;" cellpadding="4" cellspacing="0">
					<tr>
						<td width="500" align="left">'.$patient_name.'</td>
						<td width="100" align="right" style="font-weight:bold;">Given: </td>
						<td width="150">'.get_date_format(date('Y-m-d'))." ".date('h:i A').'</td>
					</tr>
				</table>
			</page_header>
			<table cellpadding="4" cellspacing="0" width="100%" height="100%">';
				if(!$doc_scn_upload_from || $doc_scn_upload_from=='writeDoc') {
					$file_path = "";
					$strHTML .=	"<tr><td class='text_10' valign='top'>".$content."</td></tr></table></page>";
					if(trim($strHTML) != ""){
						$file_path = write_html(utf8_decode(html_entity_decode($strHTML)));
						//$fp = fopen('../../../library/html_to_pdf/pdffile.html','w');
						//$intBytes = fputs($fp,$strHTML);
						//fclose($fp);
?>
						<script type="text/javascript">
							top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
							var parWidth = parent.document.body.clientWidth;
							var parHeight = parent.document.body.clientHeight;
							document.getElementById('div_PdfContent').style.display='block';
							onafterprint();
							var browserIpad = "<?php echo $browserIpad;?>";
							if(browserIpad=="yes") {
								//window.open('../../../library/html_to_pdf/createPdf.php?op=p&font_size=10&page=4','_blank','');	
								html_to_pdf('<?php echo $file_path; ?>','p');
							}else {
								//window.frames['ifrm_PdfContent'].location.href = '../../../library/html_to_pdf/createPdf.php?op=p','pdfPrint','scrollbars=1,resizable=1,width='+parWidth+'height='+parHeight+'';
								html_to_pdf('<?php echo $file_path; ?>','p','',true);
							
							}
						</script>
<?php
					}
				}
				else if($doc_scn_upload_from=='scanDoc' && $scan_doc_file_path)
				{
?>
					<script>document.getElementById('div_PdfContent').style.display='none';</script>
<?php
					$strHTML =	'<table cellpadding="4" cellspacing="0" width="100%" height="100%"><tr><td class="text_10"valign="top"><img name="viewImgScn"  src="'.$upload_dir.$scan_doc_file_path.'" border="0" ></td></tr></table>';
					echo $strHTML;
				}
				else if($doc_scn_upload_from=='uploadDoc' && $upload_doc_file_path)
				{
					if($upload_doc_type=='pdf') {
?>
						<script>document.getElementById('div_PdfContent').style.display='none';</script>
  <?php
            $strHTML =	'<table cellpadding="4" cellspacing="0" width="100%" height="100%"><tr><td class="text_10" valign="top"><iframe src="'.$upload_dir.$upload_doc_file_path.'" width="100%" height="100%" scrolling="no"></iframe></td></tr></table>';
            echo $strHTML;
					}
					else
					{
?>
						<script> document.getElementById('div_PdfContent').style.display='none';</script>
 <?php
            $strHTML =	'<table cellpadding="4" cellspacing="0" width="100%" height="100%"><tr><td class="text_10" valign="top"><img name="viewImgScn"  src="'.$upload_dir.$upload_doc_file_path.'" border="0" ></td></tr></table>';
            echo $strHTML;
					}
				}
?>
</body>
</html>
