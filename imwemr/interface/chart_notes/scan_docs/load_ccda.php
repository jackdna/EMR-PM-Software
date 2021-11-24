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
File: load_ccda.php
Purpose: Load CCDA document.
Access Type: Direct	
*/
require_once("../../../config/globals.php");
$file_path = isset($_REQUEST['file_path'])?$_REQUEST['file_path']:false;
//require_once($GLOBALS['fileroot']."/library/classes/msgConsole.php");
$col_height = (int) ($_SESSION['wn_height'] - ($GLOBALS['gl_browser_name']=='ipad' ? 65 : 310)) ;
if($file_path){
	//$ccda_file = "../".trim($file_path,"'");
	$ccda_file = trim($file_path,"'");
		$arrName = explode("/",$ccda_file);
		$file_name = end($arrName);
		if(strpos($file_name,".zip") !== false){
			$folder_name = str_replace(".zip","",$file_name);
			$zip = new ZipArchive;
			if($zip->open($ccda_file) == TRUE){
				for($i=0; $i<$zip->numFiles; $i++){
					$name = $zip->getNameIndex($i);
					if(strpos(strtolower($name),".xml") !== false){
						$check_xml_file = check_patient_details($zip->getFromIndex($i));
						if($check_xml_file['fname']!="" || $check_xml_file['lname']!=""){
							$content = $zip->getFromIndex($i);
						}
					}
				}
			}
		}else{
			$content = file_get_contents($ccda_file);
		}
		
		$style_xsl_file = dirname(__FILE__).'/../../physician_console/CDA.xsl';
		//parsing CCR document
		if(stripos($content, 'ClinicalDocument') === false){
			$style_xsl_file = dirname(__FILE__).'/../../physician_console/CCR.xsl';
		}
		$style_xsl = file_get_contents($style_xsl_file);
		
		$proc=new XSLTProcessor();
		$dom = new DOMDocument;
		$proc->importStylesheet($dom->loadXML($style_xsl));//load XSL script
		$procVal =  $proc->transformToXML($dom->loadXML($content)); //load XML file and echo
		
		if(!$procVal && strpos($ccda_file,".xml") !== false){
			$proc=new XsltProcessor;
			$proc->importStylesheet(DOMDocument::load($style_xsl_file)); //load XSL script
			$procVal =  $proc->transformToXML(DOMDocument::load($ccda_file)); //load XML file and echo
		}
			
?>
        <div class=" col-xs-12 " style="height:<?php echo ($col_height);?>px; max-height:100%; width:100%; overflow:scroll; overflow-x:hidden">
			<?php 
				echo preg_replace('/[[:^print:]]/', '', $procVal);
				//echo utf8_decode($procVal); 
			?>
        </div>
<?php		
}

function check_patient_details($xml_file){
	$arr_pt_details_return=array();
	$arr_xml_file_content=array();
	if($xml_file){
		$arr_xml_file_content=simplexml_load_string($xml_file);
		if(count($arr_xml_file_content)>0){
			$arr_pt_details_return['fname']=(string)$arr_xml_file_content->recordTarget->patientRole->patient->name->given;
			$arr_pt_details_return['lname']=(string)$arr_xml_file_content->recordTarget->patientRole->patient->name->family;
			$arr_pt_details_return['sex']=(string)$arr_xml_file_content->recordTarget->patientRole->patient->administrativeGenderCode['displayName'];
			$arr_pt_details_return['dob']=(string)$arr_xml_file_content->recordTarget->patientRole->patient->birthTime['value'];
			$arr_pt_details_return['dob'] = date("Y-m-d", strtotime($arr_pt_details_return['dob']));
			
			$arr_pt_details_return['city']=(string)$arr_xml_file_content->recordTarget->patientRole->addr->city;
			$arr_pt_details_return['state']=(string)$arr_xml_file_content->recordTarget->patientRole->addr->state;
			$arr_pt_details_return['zip']=(string)$arr_xml_file_content->recordTarget->patientRole->addr->postalCode;
		}
		return $arr_pt_details_return;
	}
}
?>

<script type="text/javascript">
	//parent.parent.showLoadingImg('none');
	//parent.parent.document.getElementById("submit_btn").disabled = true;
	//parent.parent.document.getElementById("submit_btn").style.display = 'inline';
</script>