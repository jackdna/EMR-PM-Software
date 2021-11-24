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
require_once("../../config/globals.php");
require_once("../../library/patient_must_loaded.php");
require_once("../../library/classes/class.tests.php");
require_once("../../library/classes/SaveFile.php");
$objTests				= new Tests;

$task 			= strip_tags(trim($_REQUEST['task']));
$patient_id		= strip_tags(trim($_REQUEST['patient_id']));
$objTests->patient_id 	= $patient_id;

//MAKING OBJECT TO SAVE IMAGE FILES
$oSaveFile = new SaveFile($patient_id);




switch($task){
	case 'provider_view_log':{
		$scan_id = trim(intval($_GET['scan_id']));
		$objTests->providerViewLogFun($scan_id,$_SESSION['authId'],$_SESSION['patient'],'tests');
		break;
	}
	case 'del_test_scan_upload':{
		$scan_id = trim(intval($_GET['scan_id']));
		$objTests->del_test_scan_upload($scan_id);
		break;
	}
	case 'load_this_test_images':{
		//patient_id="+patient_id+"&test_table="+test_table+"&test_scan_edit_id_scan="+test_id+"&test_type="+test_type+"&gen_thumb=
		$test_table		= strip_tags(trim($_REQUEST['test_table']));
		$test_id		= strip_tags(trim($_REQUEST['test_scan_edit_id_scan']));
		$test_type		= intval(strip_tags(trim($_REQUEST['test_type'])));
		$gen_thumb		= strip_tags(trim($_REQUEST['gen_thumb']));
		$container_id	= strip_tags(trim($_REQUEST['container_id']));
		/****CHECKING IF PICTUREs available TO SHOW***/
		//echo $patient_id.','.$test_table.','.$test_id.','.$test_type.','.$gen_thumb;
		$this_test_images = $objTests->get_test_images($patient_id,$test_table,$test_id,$test_type,$gen_thumb);
		//pre($this_test_images);
		$scan_id_array = array();
		foreach($this_test_images as $scan_doc_rs){
			$doc_date 		= $scan_doc_rs['docUploadDate'];
			$doc_file_type	= $scan_doc_rs['file_type'];
			$doc_scan_id	= $scan_doc_rs['scan_id'];
			$doc_file_path	= $scan_doc_rs['file_path'];
			$doc_file_name	= $scan_doc_rs['fileName'];
			$scan_uploads	= $scan_doc_rs['scan_uploads'];
			$scan_id_array[]= $doc_scan_id;
			//pre($scan_uploads);
			$tooltip_text	= '';
			$imgsite_arr	= array('1'=>'OS','2'=>'OD','3'=>'OU');
			if($scan_doc_rs['imgSite']>0 && $scan_doc_rs['imgSite']!=''){
				$tooltip_text = $imgsite_arr[$scan_doc_rs['imgSite']];
			}
			?>
			<div class="upldimg">
			<div class="fileName" title="<?php echo $scan_uploads['file_name'];?>"><?php echo $scan_uploads['file_name'];?></div>
            	<?php if($container_id){?>
                    <img style="max-width:280px;" id="scan_big_img<?php echo $doc_scan_id;?>" src="<?php echo $scan_uploads['mid'];?>" onclick="view_all_images('<?php echo $doc_scan_id;?>','<?php echo $test_table;?>','self')" class="link_cursor img-responsive" title="<?php echo $scan_uploads['file_name'];?>" /> 
                    <div class="siteName"><?php echo $tooltip_text;?></div>
                <?php }else{?>
                    <div class="siteName"><?php echo $tooltip_text;?></div>
                    <img alt="" src="<?php echo $scan_uploads['mid'];?>" onclick="view_all_images('<?php echo $doc_scan_id;?>','<?php echo $test_table;?>')" class="link_cursor" title="<?php echo $scan_uploads['file_name'];?>" /> 
                    <div class="dltvw link_cursor"><a onclick="del_test_scan_upload_image('<?php echo $doc_scan_id;?>')"><img src="../../library/images/close1.png" alt="Delete Image"/></a></div>
                    <div class="upldvw"><a onclick="log_view_log_image('<?php echo $doc_scan_id;?>')" href="<?php echo $scan_uploads['original'];?>" data-ob="lightbox[inline]"><img src="../../library/images/lgvides.png" alt="View Image"/></a></div>
                <?php }?>
			</div>
		<?php
		}?>
		<script type="text/javascript">var scan_id_array = JSON.parse('<?php echo json_encode($scan_id_array);?>');</script>
		<?php
		break;
	}
	case "resetTestForm":{
			$formId 		= $_POST["elem_formId"];
			$testName 		= $_POST["elem_testName"];
			$testId 		= $_POST["elem_testId"];
			$purge 			= $_POST["elem_purge"];
			$purgeStatus 	= $_POST["elem_purgeStatus"];
			$tests_master_id= $_POST["elem_tests_name_id"];
			$echo 			= 1;
			//
			if((!empty($formId) || !empty($testId)) && !empty($testName) ){
				$tblName = "";
				$arrtmp = $objTests->get_table_cols_by_test_table_name($tests_master_id,'id');
				//pre($arrtmp,1);
				$tblName = $arrtmp["test_table"];
				$formIdName = $arrtmp["formid_key"];
				$tblId = $arrtmp["test_table_pk_id"];
				if(!empty($tblName)){
					if(!empty($purge)){ //Purge --
						if(!empty($purgeStatus)){
							$purgeStatus=$_SESSION["authId"];
						}else{
							$purgeStatus=0;
						}
						$phraseFormID="";
						//Check For Other ACTIVE records with Same FormId
						// SET formId to ZERO AND UNPURGE OTHERWISE it will cause multiple tests with one formId
						
						if($purgeStatus==0){
							$sql = "SELECT COUNT(DISTINCT(c2.".$tblId.")) AS num FROM ".$tblName." c1
									LEFT JOIN ".$tblName." c2 ON c2.".$formIdName."=c1.".$formIdName." ".
									"WHERE c1.".$formIdName."!='0' AND c2.purged='0' AND c1.del_status='0' ";
							if(!empty($formId)){
								$sql .= "AND c1.".$formIdName."='".$formId."' ";
							}else if(!empty($testId)){
								$sql .= "AND c1.".$tblId."='".$testId."' ";
							}
							$row = sqlQuery($sql);
							if($row!=false && !empty($row["num"])){
								//exit("2");
								$phraseFormID = " ,".$formIdName."='0' ";
							}
						}					
						
						$sql = "UPDATE ".$tblName." SET ".
								"purged='".$purgeStatus."' ".$phraseFormID." WHERE ";
					}else{
						//$sql = "delete FROM ".$tblName." WHERE ";
						$sql = "update ".$tblName." SET del_status='1' WHERE ";
					}
					
					if(!empty($formId)){
						$sql .= $formIdName."='".$formId."'";
					}else if(!empty($testId)){
						$sql .= $tblId."='".$testId."'";
					}
					
					$row = sqlQuery($sql);
					$echo = 0;
				}
			}
			echo $echo;
		break;
	}
	default:
		echo 'no case defined';
}
?>