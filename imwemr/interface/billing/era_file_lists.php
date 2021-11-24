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
$without_pat="yes"; 
require_once("../accounting/acc_header.php");
require_once("../../library/classes/billing_functions.php");
require_once("../../library/classes/class.electronic_billing.php");
include_once(dirname(__FILE__)."/../../library/classes/SaveFile.php");

$pg_title = 'ERA Upload';
$obj_user_save	= new SaveFile();

$objEBilling 	= new ElectronicBilling();
$ClearingHouse	= $objEBilling->ClearingHouse();
$CL_mode 		= $ClearingHouse[0]['connect_mode'];
$CL		 		= $ClearingHouse[0]['abbr'];
$CL_url			= ($CL_mode=='T') ? $ClearingHouse[0]['test_url'] : $ClearingHouse[0]['prod_url'];
$valid_response = true;

set_time_limit(0);
$prac_path=$GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/";
$basePath = $prac_path.'BatchFiles';
if(!is_dir($basePath)){
	$obj_user_save->ptDir('BatchFiles');
}	
$eraFileName = date('m_d_y_h_i_s').'_get_ERA_file.zip';

$path = $basePath.'/'.$eraFileName;
$sharePath = $basePath.'/others/get_era';

if($CL_mode=='P'){
	$MessageType = 'ERA';
}else{
	$MessageType = 'ERT';
}
//----SQL DATE FORMAT---
$getSqlDateFormat= get_sql_date_format();

//------------------------ Groups Detail ------------------------//
$fet_groups=imw_query("select * from groups_new where del_status='0' order by name");
while($row_groups=imw_fetch_array($fet_groups)){
	$gro_data[]=$row_groups;
}
//------------------------ Groups Detail ------------------------//

if(count($_REQUEST['fileNames'])>0 && $_REQUEST['file_action']=="del"){
	foreach($_REQUEST['fileNames'] as $filename){
		if($_REQUEST['archive_file']=='yes'){
				$update_record = imw_query("update electronicfiles_tbl set archive_status='1' WHERE id = '$filename' and post_status='Posted'");
		}else{		
			$getElectroFileIdStr = "SELECT a.id, b.835_Era_Id FROM electronicfiles_tbl a,era_835_details b 
									WHERE a.id = '$filename' AND a.id = b.electronicFilesTblId";
			$getElectroFileIdQry = imw_query($getElectroFileIdStr);
			while($getElectroFileIdRow = imw_fetch_array($getElectroFileIdQry)){
				$delFileId = $getElectroFileIdRow['835_Era_Id'];
				$delELEFileId = $getElectroFileIdRow['id'];
				$getPatDetailsIdStr = "SELECT ERA_patient_details_id FROM era_835_patient_details WHERE `835_Era_Id` = '$delFileId'";
				$getPatDetailsIdQry = imw_query($getPatDetailsIdStr);
				while($getPatDetailsIdRows = imw_fetch_array($getPatDetailsIdQry)){
					$ERAPatientDetailsId = $getPatDetailsIdRows['ERA_patient_details_id'];
					$delNM1DetailsStr = "DELETE FROM era_835_nm1_details WHERE ERA_patient_details_id = '$ERAPatientDetailsId'";
					$delNM1DetailsQry = imw_query($delNM1DetailsStr);
				}
				$delPatientDetailsStr = "DELETE FROM era_835_patient_details WHERE 835_Era_Id = '$delFileId'";
				$delPatientDetailsQry = imw_query($delPatientDetailsStr);		
				$delProcDetailsStr = "DELETE FROM era_835_proc_details  WHERE 835_Era_Id = '$delFileId'";
				$delProcDetailsQry = imw_query($delProcDetailsStr);		
				$delRecordStr = "DELETE FROM electronicfiles_tbl WHERE id = '$delELEFileId'";
				$delRecordQry = imw_query($delRecordStr);
				$delERADetailsStr = "DELETE FROM era_835_details WHERE electronicFilesTblId = '$delELEFileId'";
				$delERADetailsQry = imw_query($delERADetailsStr);
			}	
		}
	}
}

if($_REQUEST['file_action']=="pat_srh"){
	$pat_name_val=trim($_REQUEST['search_pat']);
	if($pat_name_val!=""){
		if(is_numeric($pat_name_val) === true){
			$search_by = "id='$pat_name_val'";
		}
		else{
			$pat_name_arr = preg_split('/,/', $pat_name_val);
			if(trim($pat_name_arr[0]) != ''){
				$search_by = " lname like '%".$pat_name_arr[0]."%'";
			}
			if(trim($pat_name_arr[1]) != ''){
				$search_by .= " and fname like '%".trim($pat_name_arr[1])."%'";
			}	
		}
	
		//--- GET PATIENT ID ----
		$pat_qry = imw_query("select id from patient_data where 1=1 and $search_by");
		$patient_id_arr = array();
		while($patQryRes=imw_fetch_array($pat_qry)){
			$patient_id_arr[] = $patQryRes['id'];
		}
		$patient_id_str = join(',',$patient_id_arr);
		
		//---- GET ERA FILE ID FROM PATIENT DETAILS ----
		if(empty($patient_id_str) === false){
			$eraQry = imw_query("select 835_Era_Id from era_835_patient_details where CLP_claim_submitter_id in($patient_id_str)");
			$era_id_arr = array();
			while($eraQryRes=imw_fetch_array($eraQry)){	
				$era_id_arr[] = $eraQryRes['835_Era_Id'];
			}
		}
	}
	if(count($era_id_arr) > 0){
		$era_Id_str = join(',',$era_id_arr);
	}
	$era_pat_whr="and era_835_details.835_Era_Id in ($era_Id_str)"; 
}

if($_REQUEST['file_action']=="upload"){
	if ($_FILES["uploadFile"]["error"] > 0){
		$errorUpload = true;
		echo "Error: " . $_FILES["uploadFile"]["error"] . "<br />";
	}else{
		$errorUpload = false;
		if(strpos($_FILES["uploadFile"]["name"],'.zip')){
		}else{
			$fileName = $_FILES["uploadFile"]["name"];
			$fileType = $_FILES["uploadFile"]["type"];
			$file_size = ($_FILES["uploadFile"]["size"] / 1024) . " Kb";
			$chkFileName = $_FILES["uploadFile"]["name"];
			$file_temp_name = $_FILES["uploadFile"]["name"];
			if(strtolower(mime_content_type($_FILES["uploadFile"]["tmp_name"]))!='text/plain'){
				$msg = "File is invalid";
			}else{
				move_uploaded_file($_FILES["uploadFile"]["tmp_name"], $prac_path."tmp/".$_FILES["uploadFile"]["name"]);
				$fileContents = addslashes(file_get_contents($prac_path."tmp/".$_FILES["uploadFile"]["name"]));
				$fileContents = str_replace('<',':',$fileContents);
				$fileContents = str_replace('>',':',$fileContents);		
				$fileName = $_FILES["uploadFile"]["name"];
				unlink($prac_path."tmp/".$fileName);
				
				//----  CHANGE FILE FORMAT -----
				$fileContents = change_file_format($fileContents);
				
				$electronicFilesTblId = put_era_data($fileContents);
				if(is_numeric($electronicFilesTblId)){
					$msg = 'File Successfully Uploaded';
					$write_off_code=$_REQUEST['write_off_code'];
					require_once("createERA835DB.php");
				}
				else{
					$msg = $electronicFilesTblId;
				}
			}
		}
	}
}
if($_REQUEST['file_action']=="get_era"){
	set_time_limit(0);
	
	//-- GET EMDEON USER NAME AND PASSWORD ----
	$qry = imw_query("select user_id as EmdeonUserId ,user_pwd as EmdeonPassword from groups_new where gro_id = '$group_name'");
	$groupQryRes = imw_fetch_array($qry);
	$emdeonUserId = trim($groupQryRes['EmdeonUserId']);
	$EmdeonPassword = trim($groupQryRes['EmdeonPassword']);
	/*Download List of era files*/
	$era_files_list = array();
	$era_practice_path = '';
	if(dl_era() && !in_array($emdeonUserId,array("MWI_RK8496C3","MWI_HP8C6583"))){
		$era_files_data = file_get_contents(ERA_URL.'/interface/billing/share_era_files.php?file_type=era&userId='.$emdeonUserId.'&query=list');
		$era_files_data = json_decode($era_files_data);	
		if(isset($era_files_data->files) && isset($era_files_data->practice_path)){
			$era_files_list = $era_files_data->files;
			$dl_era_practice_path = $era_files_data->practice_path;
		}
	}
	else{
		if($CL=='EMD'){
			$cur = curl_init();
			$loginfields = array();
			$loginfields['wsUserID'] = $emdeonUserId;
			$loginfields['wsPassword'] = $EmdeonPassword;
			if(constant('ERA_DOWNLOAD_DELETE_IN_STEP')=='yes' && $MessageType=='ERA'){
				$MessageType='EDP';
			}
			$loginfields['wsMessageType'] = $MessageType;
			$loginfields['wsGetFile'] = $path;
			$emdeon_online_url = $CL_url.'ITS/post.aspx';
			curl_setopt($cur,CURLOPT_URL,$emdeon_online_url);
			curl_setopt($cur, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($cur, CURLOPT_POSTFIELDS, $loginfields); 
			$output = curl_exec($cur);
			$msg = curl_error($cur);
		}else if($CL=='PI'){
			$cur = curl_init($CL_url."transfer/list.php");
			curl_setopt($cur, CURLOPT_USERPWD, $emdeonUserId.":".$EmdeonPassword);
			curl_setopt($cur, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($cur, CURLOPT_SSL_VERIFYHOST, FALSE);
			curl_setopt($cur, CURLOPT_RETURNTRANSFER, TRUE);
			$output = curl_exec($cur);
			$msg = curl_error($cur);
			curl_close($cur);
			
			if($output != '' && $msg == ''){			
				$temp_ERA_dir = $basePath.'/tmp'.date('YmdHis');
				if(!is_dir($temp_ERA_dir)){
					mkdir($temp_ERA_dir,0777,true);
					chmod($temp_ERA_dir, 0777);
				}
				file_put_contents($temp_ERA_dir.'/LIST.txt',$output);
				$files = explode(';',$output);
				foreach($files as $ERAfile){
					$fileExt	= substr($ERAfile,-4);
					if($fileExt!='.835') continue;
					
					$cur = curl_init($CL_url."transfer/download.php?file=".$ERAfile);
					curl_setopt($cur, CURLOPT_USERPWD, $emdeonUserId.":".$EmdeonPassword);
					curl_setopt($cur, CURLOPT_SSL_VERIFYPEER, FALSE);
					curl_setopt($cur, CURLOPT_SSL_VERIFYHOST, FALSE);
					curl_setopt($cur, CURLOPT_RETURNTRANSFER, TRUE);
					$output	=  curl_exec($cur);
					$error	= curl_error($cur);
					curl_close($cur);
					if(!empty($output)) file_put_contents($temp_ERA_dir.'/OUTPUT_'.$ERAfile.'.txt',$output);
					if(!empty($error))	file_put_contents($temp_ERA_dir.'/ERROR_'.$ERAfile.'.txt',$error);					

					if(strtolower(substr($output,0,5))=='error'){//if text started with ERROR...
						$msg = $output;
						$valid_response = false;
					}else if(!empty($error)){//If any curl error found..
						$msg = $error;
						$valid_response = false;
					}else{
						if($output!='' && $error==''){
							$fileId = fopen($temp_ERA_dir.'/'.$ERAfile,'w');
							$data3 = fputs($fileId,$output);
							fclose($fileId);							
						}
					}
				}
								
				//----IF TEMP DIRECTORY HAVE 835 FILES, THEN ZIP IT.------
				$files_835 = glob($temp_ERA_dir.'/*.835');
				$zip = new ZipArchive;
				$zip->open($temp_ERA_dir.'/temp835.zip', ZipArchive::CREATE);
				foreach ($files_835 as $file){
				  $zip->addFile($file,basename($file));
				}
				$zip->close();
				$output = file_get_contents($temp_ERA_dir.'/temp835.zip');
				
				//------REMOVE TEMPORY DIRECTORY AND CONTENTS------
				$objects = scandir($temp_ERA_dir);
				foreach ($objects as $object) {
				  if ($object != "." && $object != "..") {
					 if (filetype($temp_ERA_dir."/".$object) == "dir") rmdir($temp_ERA_dir."/".$object); else unlink($temp_ERA_dir."/".$object);
				  }
				}
				reset($objects);
				rmdir($temp_ERA_dir);
				//-----------REMOVAL OF TEMPORARY DATA END---------
			}				
		}
	}
	
	do{
		if(dl_era() && !in_array($emdeonUserId,array("MWI_RK8496C3","MWI_HP8C6583"))){
			$era_file_name = array_pop($era_files_list);
			$http_code = 0;
			
			if($era_file_name !== NULL && !file_exists($basePath.'/'.$era_file_name) && $dl_era_practice_path!=''){
				$path = $basePath.'/'.$era_file_name;
				$era_file_name = rawurlencode($era_file_name);
				$era_url = ERA_URL.'/data/'.$dl_era_practice_path.'/BatchFiles/others/get_era/'.$emdeonUserId.'/'.$era_file_name;
				$cur = curl_init();
				curl_setopt($cur,CURLOPT_URL, $era_url);
				curl_setopt($cur, CURLOPT_SSL_VERIFYPEER, FALSE);
				curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
				$output = curl_exec($cur);
				$http_code = curl_getinfo($cur, CURLINFO_HTTP_CODE);
				$msg = curl_error($cur);
				curl_close($cur);
				if($http_code!==200){
					$output = 'The document has moved ';
				}
			}else{
				$output = 'The document has moved ';
			}
		}
		
		//--- Save File Content In DataBase -------
		$putData['ERA_file_data'] = base64_encode(addslashes($output));
		$putData['ERA_status'] = '1';
		$putData['operator_id'] = $_SESSION['authId'];
		$putData['group_id'] = $group_name;
		$putData['emdeon_username'] = $emdeonUserId;
		$insertId = AddRecords($putData,'get_era_file');
		$file = false;
		if($output) $file = true;
		preg_match('/The document has moved /',$output,$curl_res);
		if(count($curl_res)>0){ 
			$file = false;
			$msg = 'No Page Found, The document has moved';
		}
		
		if($file == true){
			if(preg_match('/ERROR:/',$output,$error)){
				$msg = $output;
				//--- Save File Content In DataBase -------
				$putData['ERA_file_data'] = addslashes($output);
				$putData['operator_id'] = $_SESSION['authId'];
				$putData['group_id'] = $group_name;
				$putData['emdeon_username'] = $emdeonUserId;
				$insertId = AddRecords($putData,'get_era_file');
			}
			else{
				$fileId = fopen($path,'w');
				$data3 = fputs($fileId,$output);
				fclose($fileId);
				
				/*Save file to be used by external practice - era shared*/
				if(is_era_shared()){
					copy_file($basePath, $sharePath.'/'.$emdeonUserId, $eraFileName);
				}
				
				$zip = zip_open($path);
				while($zip_entry = zip_read($zip)){
					$fileName2 = substr(zip_entry_name($zip_entry),strrpos(zip_entry_name($zip_entry),'/')+1);
					$fileName = $basePath.'/'.$fileName2.".txt";
					$fp = fopen($fileName, "w");
					$entryId = zip_entry_open($zip,$zip_entry,'r');
					if($entryId){
						$data = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
						$data = str_replace('<',':',$data);
						$data = str_replace('>',':',$data);
						fwrite($fp,"$data");
						zip_entry_close($zip_entry);
						fclose($fp);
					
						$fp = fopen($fileName, "r");
						$file_size = filesize($fileName);
						$fileType = filetype($fileName);
						$output = fread($fp,$file_size);
						fclose($fp);
						$fileName = $fileName2;
						
						//--- SAVE FILE CONTENT IN DATABASE -------
						$putData['ERA_file_data'] = addslashes($output);
						$putData['operator_id'] = $_SESSION['authId'];
						$putData['ERA_status'] = '0';
						$putData['group_id'] = $group_name;
						$putData['emdeon_username'] = $emdeonUserId;
						$insertId = AddRecords($putData,'get_era_file');
						$output = change_file_format($output);
						$electronicFilesTblId = put_era_data($output);				
						if($MessageType != 'EDP'){
							if(is_numeric($electronicFilesTblId)){
								$write_off_code=$_REQUEST['write_off_code'];
								require("createERA835DB.php");
								$msg = 'File Successfully Uploaded';					
							}
							else{
								$msg = $electronicFilesTblId;
							}
						}else{
							$msg = $electronicFilesTblId;
						}
					}
				}
				zip_close($zip);
				if($MessageType == 'EDP'){
					/** CURL call to remove ERA file from CHC**/
					$cur = curl_init();
					$loginfields = array();
					$loginfields['wsUserID'] = $emdeonUserId;
					$loginfields['wsPassword'] = $EmdeonPassword;
					$loginfields['wsMessageType'] = 'EAP';
					$loginfields['wsGetFile'] = $path;
					$emdeon_online_url = $CL_url.'ITS/post.aspx';
					curl_setopt($cur,CURLOPT_URL,$emdeon_online_url);
					curl_setopt($cur, CURLOPT_SSL_VERIFYPEER, FALSE);
					curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($cur, CURLOPT_POSTFIELDS, $loginfields); 
					$RemERA = curl_exec($cur);
					/***removing ERA end***/
					
					unset($electronicFilesTblId);
					$write_off_code=$_REQUEST['write_off_code'];
					$getFileContentsRes = imw_query("SELECT id FROM electronicfiles_tbl WHERE read_status=0");
					if($getFileContentsRes && imw_num_rows($getFileContentsRes)>0){
						while($getFileContentsRow = imw_fetch_array($getFileContentsRes)){
							$electronicFilesTblId = $getFileContentsRow['id'];	
							require("createERA835DB.php");
						}
						$msg = 'File Successfully Uploaded';
					}
				}
			}
		}
		
		/*Unlink File on remote server* /
		if( dl_era() && $era_file_name !== NULL && $http_code===200){
			file_get_contents(ERA_URL.'/interface/Billing/share_era_files.php?file_type=era&query=del&userId='.$emdeonUserId.'&file_name='.$era_file_name);
		}*/
	}while(count($era_files_list)>0);
}

/******GETTING IF UNREAD FILES REMAINNING ON PAGE LOAD******/
$chkUnreadIDs = '';
$chkUnreadRes = imw_query("SELECT GROUP_CONCAT(id) AS unreadERAids FROM electronicfiles_tbl WHERE read_status=0 GROUP BY read_status");
if($chkUnreadRes && imw_num_rows($chkUnreadRes)>0){
	$chkUnreadRS = imw_fetch_assoc($chkUnreadRes);
	$chkUnreadIDs = $chkUnreadRS['unreadERAids'];
}
?>
<script type="text/javascript">
	var UnreadERA_IDs = '<?php echo $chkUnreadIDs;?>';
	if(UnreadERA_IDs!=''){
		var arrUnreadERA_IDs = UnreadERA_IDs.split(',');
		$(document).ready(function(e) {
			msgtext = 'There are '+arrUnreadERA_IDs.length+' new ERA files.';
			if(parseInt(arrUnreadERA_IDs.length)==1) msgtext = 'There is '+arrUnreadERA_IDs.length+' new ERA file.';
			top.fancyConfirm(msgtext+' Do you want to process now?','Process ERA',"top.fmain.processUnreadERAs()");
		});
	}
	
	function processUnreadERAs(){
		html = '<div style="width:600px;"><iframe frameborder=0 framespacing=0 style="width:600px; height:150px;" src="../billing/processUnreadERAs.php?unreadERAids=<?php echo $chkUnreadIDs;?>"></iframe></div>';
		fancyModal(html,'Process ERA','650px','150px');
	}
	
	function del_file(cnfrm){		
		var flag = false;
		var chkObj = document.getElementsByName("fileNames[]");
		for(i=0;i<chkObj.length;i++){	
			if(chkObj[i].checked == true){
				flag = true;
				break;
			}
		}
		
		if(flag == false){
			top.fAlert('Please select any file to delete.');
			return false;
		}else if(cnfrm){
			top.show_loading_image("show","150");
			$("#file_action").val('del');
			document.era_form.submit();
		}else{
			top.fancyConfirm("Are you sure you want to delete the selected file?","","window.top.fmain.del_file(true)");
		}
	}
	function get_file(val){
		if(val == 'upload_file'){
			if($("#uploadFile").val()==''){
				top.fAlert('Please select any file to upload.');
				return false;
			}
			top.show_loading_image("show","150");
			$("#file_action").val('upload');
		}
		else{
			top.show_loading_image("show","150");
			$("#file_action").val('get_era');
		}
		document.era_form.submit();
	}
	function download_file_name(file_id){
		var url = "downloadFile.php?era_id="+file_id;
		window.location=url;
	}
	function search_patient(){
		var search_pat_val = $("#search_pat").val();
		if(search_pat_val == ''){
			top.fAlert("Please enter patient search field.");
			return false;
		}
		else{
			$("#file_action").val('pat_srh');
			document.era_form.submit();
		}
	}
</script>
<div class="row">
	<form name="era_form" action="era_file_lists.php" method="post" enctype="multipart/form-data" onSubmit="return chkFn();">
    	<input type="hidden" name="new_file" id="new_file" value="">
        <input type="hidden" name="archive_file" id="archive_file" value="">
        <input type="hidden" name="file_action" id="file_action" value="">
        <div class="col-sm-12 purple_bar">
        	<div class="row">
                <div class="col-sm-2 form-inline">
                    <label for="group_name">Group</label>
                    <select id="group_name" name="group_name" class="selectpicker">
                        <?php
                        foreach($gro_data as $key=>$val){
                            $sel="";
                            if($_REQUEST['group_name']==$gro_data[$key]['gro_id']){
                                $sel="selected";
                            }
                        ?>
                            <option value="<?php echo $gro_data[$key]['gro_id']; ?>" <?php echo $sel; ?>><?php echo $gro_data[$key]['name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-sm-3 form-inline">
                    <label>File Name</label>
                    <input type="file" name="uploadFile" id="uploadFile" class="btn btn-primary form-control" autocomplete="off">
                </div>
                <div class="col-sm-4 form-inline">
                	<strong><?php echo $msg; ?></strong>
                </div>
                <div class="col-sm-3 form-inline text-right">
                    <label>Patient Search</label>
                    <div class="input-group">
                    	<input type="text" name="search_pat" id="search_pat" value="<?php echo $_REQUEST['search_pat']; ?>" class="form-control" onKeyPress="{if (event.keyCode==13) return search_patient();}" />
                        <label for="search_pat" class="input-group-addon"><span class="glyphicon glyphicon-search" onclick="search_patient();"></span></label>
                    </div>
                </div>
            </div>  
        </div>
        <div class="table-responsive" id="mainFileListData" style="width:100%;height:<?php print $_SESSION['wn_height']-370;?>px;overflow:auto;">
            <table class="table table-bordered table-hover table-striped">
                <tr class='grythead'>
                    <td class="text-center">
                    	<div class="checkbox">
							<input type="checkbox" name="chkbx_all" id="chkbx_all" onClick="return chk_all();">
							<label for="chkbx_all"></label>	
						</div>
                    </td>
                    <td>S. No.</td>
                    <td>Name</td>
                    <td>Size</td>
                    <td>Check/EFT Date</td>					
                    <td>Status</td>
                </tr>
                <?php
                //-----------------	GET ELECTRONIC FILE DETAILS
                $qry = "select electronicfiles_tbl.id,electronicfiles_tbl.file_temp_name,
                                electronicfiles_tbl.file_size,electronicfiles_tbl.post_status,
                                electronicfiles_tbl.file_name,electronicfiles_tbl.file_contents,
                                date_format(era_835_details.chk_issue_EFT_Effective_date,'".$getSqlDateFormat."') as chk_date, 
								electronicfiles_tbl.read_status 
                                from electronicfiles_tbl join era_835_details on
                                era_835_details.electronicFilesTblId = electronicfiles_tbl.id
                                where electronicfiles_tbl.archive_status='0' $era_pat_whr
                                group by era_835_details.electronicFilesTblId 
                                order by electronicfiles_tbl.id desc";
                $res = imw_query($qry);
				$ec=1;
                while($row=imw_fetch_array($res)){
                    $id = $row['id'];
                    $fileName = $row['file_name'];
                    $file_Contents = $row['file_contents'];
                    $file_temp_name = $row['file_temp_name'];					
                    $fileSize = $row['file_size'];
                    $status = $row['post_status'];
                    $EFTChkDate = $row['chk_date'];
              ?>
                    <tr>
                        <td class="text-center">
                        	<div class="checkbox">
                                <input type="checkbox" name="fileNames[]" id="fileNameId<?php echo $id; ?>" class="chk_box_css" value="<?php echo $id; ?>">	
                                <label for='fileNameId<?php echo $id; ?>'></label>
                            </div>
                        </td>
                        <td><?php echo $id; ?></td>    
                        <td class="text_purple" style="cursor:pointer;" onClick="download_file_name('<?php echo $id;?>');"><?php echo $file_temp_name; ?></td>
                        <td><?php echo $fileSize; ?></td>
                        <td><?php echo $EFTChkDate; ?></td>						
                        <td><?php echo $status; ?></td>
                    </tr>
              <?php
                }
                if(imw_num_rows($res) == 0){
                ?>
                    <tr><td colspan="6" class="text-center lead"><?php echo imw_msg('no_rec'); ?></td></tr>
                <?php
                }
                ?>
            </table>
        </div>
    </form>
</div>        
<script type="text/javascript">
	top.$('#acc_page_name').html('<?php echo $pg_title; ?>');
	var mainBtnArr = new Array();
	mainBtnArr[0] = new Array("upload_file","Save","top.fmain.get_file('upload_file');");
	mainBtnArr[1] = new Array("get_file","Get ERA","top.fmain.get_file('get_file');");
	mainBtnArr[2] = new Array("del_file","Delete","top.fmain.del_file();");
	top.btn_show("PPR",mainBtnArr);
	
</script>	
</div>
</body>
</html>
