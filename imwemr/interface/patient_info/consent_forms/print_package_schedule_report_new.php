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
/*	
File: print_package_schedule_report.php
Purpose: Print package schedule report
Access Type: Direct 
*/
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Always modified
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");  
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP 1.1
header("Cache-Control: post-check=0, pre-check=0", false); // HTTP 1.0header("Pragma: no-cache");

header("Cache-control: private, no-cache"); 
header("Pragma: no-cache");
include_once("../../../config/globals.php");
$library_path = $GLOBALS['webroot'].'/library';
$patient_id = $_SESSION['patient'];

//require_once('../../main/Functions.php');
require_once(dirname(__FILE__)."/../../../library/classes/SaveFile.php");
include_once(dirname(__FILE__)."/../../../library/classes/print_pt_key.php");
include_once(dirname(__FILE__).'/../../../library/bar_code/code128/code128.class.php');
$obj_print_pt_key=new print_pt_key;
$getSqlDateFormat = get_sql_date_format();
$getSqlDateFormatSmall = str_replace("Y","y",get_sql_date_format());
$browserIpad = 'no';
if(stristr($_SERVER['HTTP_USER_AGENT'], 'ipad') == true) {
	$browserIpad = 'yes';
}

$htmlFolder = "new_html2pdf";
$htmlV2Class=true;	
$htmlFilePth = "new_html2pdf/createPdf.php";

$patient_id_exp = array();
$patient_id_comma = $_REQUEST['patient_id_comma'];
$patient_id_exp = explode(",",$patient_id_comma);
$packageCategoryId = $_REQUEST['package_category_id'];

$patient_id='';
$totalPatient = count($patient_id_exp);
$limPt = 20;
$loopPt = ceil($totalPatient/$limPt);
$pckArr = array();
$k_pack=0;$l_pack=0;
for($i_pack=0;$i_pack<$loopPt;$i_pack++) {
	$boolPack=false;
	for($j_pack=$k_pack;$j_pack<count($patient_id_exp);$j_pack++) {
		if($boolPack==false) {
			$pckArr[$i_pack][] = $patient_id_exp[$j_pack];
			$k_pack++;
			$l_pack++;
		}
		if($l_pack == $limPt) {
			$boolPack=true;	
			$l_pack = 0;
		}
	}
	
}//pre($pckArr);die();
foreach($pckArr as $pcKey =>$pcVal) {
	$consent_form_content=$consent_form_content_new ='';	
	for($i_sch_pack=0;$i_sch_pack<count($pcVal);$i_sch_pack++) {
		$boolLim=false;
		$patient_id = $pcVal[$i_sch_pack];
		include('print_package_schedule_report.php');
		$consent_form_content_new.=$consent_form_content;
	}
	$htmlFileName = 'pdffile_'.$pcKey;
	//$fp = fopen('../../common/'.$htmlFolder.'/'.$htmlFileName.'.html','w');
	//$write_data = fwrite($fp,$consent_form_content_new);
	$file_path = write_html(utf8_decode(html_entity_decode(stripslashes($consent_form_content_new))));
	if($file_path){
	?>
		<script type="text/javascript" src="<?php echo $library_path; ?>/js/common.js"></script>
    	<script>
			window.focus();
			top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
		</script>
    <?php if($pcKey==(count($pckArr)-1)){ ?>
			<script>
				html_to_pdf('<?php echo $file_path; ?>','p');
			</script>
		<?php } else { ?>
			<script>
				html_to_pdf('<?php echo $file_path; ?>','p');
			</script>
        <?php
        }
	}
}
?>