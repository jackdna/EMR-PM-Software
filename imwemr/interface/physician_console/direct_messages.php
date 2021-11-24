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
require_once(dirname(__FILE__).'/../../config/globals.php');
require_once($GLOBALS['fileroot'].'/library/classes/ccd_xml_parser.php');
require_once($GLOBALS['fileroot'].'/library/classes/msgConsole.php');

//Upload modal
if(isset($_GET["get_upld_mdl"]) && $_GET["get_upld_mdl"]==1){
?>
<!-- Modal UPLOAD --->
<div id="upldModal" class="modal fade" role="dialog">
<div class="modal-dialog modal-lg">
	<!-- Modal content-->
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h4 class="modal-title">Upload file(s) to attach with message</h4>
		</div>
		<div class="modal-body">
			<div class="row">
				<div id="dvupload" >
					<?php
						//$upload_url=$GLOBALS['webroot']."/interface/physician_console/ajax_html.php?from=console&task=load_direct_messages&imwemr=".session_id()."&upld_attch=1&upType=".$opType;
						$upload_url=$GLOBALS['webroot']."/interface/physician_console/sync_direct.php?imwemr=".session_id()."&upType=u&upld_attch=1&sec=".$_GET["sec"];
						$upload_from='direct_msg';
						$scanUploadSrc = $GLOBALS['incdir']."/../library/upload/index.php";
						$zStopJqueryInc=1;
						echo "<script>
										var upload_url = '$upload_url';
									</script>";
						include($scanUploadSrc);
					?>
				</div>
			</div>
		</div>
		<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		</div>
	</div>
</div>
</div>
<?php
/*
<!--
<script src="<?php //echo $GLOBALS['webroot']; ?>/library/js/jquery-migrate-1.2.1.js"></script>
<script src="<?php //echo $GLOBALS['webroot']; ?>/library/js/jquery-ui.min.1.12.1.js"></script>
<! - - Include all compiled plugins (below), or include individual files as needed --!>
<script src="<?php //echo $GLOBALS['webroot']; ?>/library/js/bootstrap.min.js"></script>
<script src="<?php //echo $GLOBALS['webroot']; ?>/library/js/jquery.mCustomScrollbar.concat.min.js"></script>
<script src="<?php //echo $GLOBALS['webroot']; ?>/library/js/bootstrap-select.js"></script>
<script src="<?php //echo $GLOBALS['webroot']; ?>/library/js/bootstrap-typeahead.js"></script>
<script src="<?php //echo $GLOBALS['webroot']; ?>/library/js/sort_table.js"></script>
-->
*/
?>
<script>
	var file_attached = [], flg_auto_save=0;
	var countUploadSize=0;
	var sec4up = '<?php echo $_GET["sec"];?>';

	if(sec4up=='pt_msg'){
		$('#fileupload').on('fileuploadadd', function (e, data) {
				var maxFileSize = 3000000, flgAlert=0, tmpSize=countUploadSize;
				$.each(data.files, function (index, file) {
					tmpSize += file['size'];
					if(tmpSize > maxFileSize){
						setTimeout(function(){
								$("#fileupload .files .template-upload").last().hide().find("button.cancel").trigger("click");
						}, 1000);
						flgAlert+=1;
					}else{
						countUploadSize+=file['size'];
					}
				});
				if(flgAlert>0){
					alert('Total File size cannot exceed 3MB.');
				}
		});
	}

	$('#fileupload').bind('fileuploaddone', function (e, data) {
			if(data && data.textStatus){
					if(data.result && data.result.files && data.result.files.length>0){
							if(data.textStatus=="success" &&
									typeof(data.result.files[0]["curfile"])!="undefined" &&
									data.result.files[0]["curfile"]!=''
								){
								file_attached.push(data.result.files[0]);
							}
					}
			}

			//Auto Save
			if(flg_auto_save==1){
				var activeUploads = $('#fileupload').fileupload('active');
		    if(activeUploads <= 1) {
		        //console.info("All uploads done");
						check_patient_msg_frm(1);
		    }
			}
	});

	var filestoupload =0;
	$('#fileupload').on('fileuploadadded', function (e, data) {
			filestoupload++;
			$("#upldModal").find("button.start").hide();
	})

	function start_upload(){
			if(filestoupload > 0) {
				$("#fileupload .fileupload-buttonbar .start").click();
			}else{
				check_patient_msg_frm(1);
			}
	}

</script>
<!-- Modal UPLOAD --->
<?php
exit();
}//end if


$dir_path=$GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH');
$real_ccda_file_path = $ccda_file;
	$msgConsoleObj = new msgConsole();
	$filter = (isset($_REQUEST["filter"]) && $_REQUEST["filter"]!='')? trim($_REQUEST["filter"]) : 'direct_msg_inbox';
	if($filter == "view_ccda"){
		$contentArr = $return_arr = array();
		$ccda_file = $dir_path.'/users'.$ccda_file;
		$arrName = explode("/",$ccda_file);
		$file_name = end($arrName);
		if(strpos($file_name,".zip") !== false){
			$folder_name = str_replace(".zip","",$file_name);
			$zip = new ZipArchive;
			if($zip->open($ccda_file) == TRUE){
				for($i=0; $i<$zip->numFiles; $i++){
					$name = $zip->getNameIndex($i);
					if((strpos(strtolower($name),".xml") !== false || strpos(strtolower($name),".txt") !== false) && strpos(strtolower($name),"metadata.xml") === false && strpos(strtolower($name),"readme.txt") === false){
						$file_text = $zip->getFromIndex($i);
						if(stristr($file_text,'templateId ')){
							$check_xml_file = $msgConsoleObj->check_patient_details($file_text);
							//if($check_xml_file['fname']!="" || $check_xml_file['lname']!=""){
							$tmp_arr = array();
							$tmp_arr['name'] = '/users/UserId_'.$_SESSION['authUserID'].'/mails/'.$name;
							$tmp_arr['zip_name'] = str_replace($dir_path,'',$ccda_file);
							$tmp_arr['content'] = $file_text;
							$contentArr[] = $tmp_arr;
							//}
						}
					}
				}
			}
		}else{
			unset($tmp_arr);
			$tmp_arr = array();
			$tmp_arr['name'] = str_replace($dir_path,'',$ccda_file);
			$tmp_arr['content'] = utf8_encode(utf8_decode(trim(file_get_contents($ccda_file))));
			$contentArr[] = $tmp_arr;
		}
		$style_xsl = file_get_contents('CDA.xsl');
		//If Zip has XML
		$return_val = '';
		$direct_attach_id = $_REQUEST['direct_attach_id'];
		if(count($contentArr) > 0){
			foreach($contentArr as $obj){
				$xml_db_chk = $msgConsoleObj->check_patient_details($obj['content']);
				$tmp_val = $ptData = '';
				//Checking is current doc already in ccda table
				$db_pt_qry = imw_query("select * from `ccda_docs` where file_path = '".$obj['name']."' AND direct_msg_id = '".$direct_attach_id."'");
				if(imw_num_rows($db_pt_qry) > 0){
					$row_dt = imw_fetch_assoc($db_pt_qry);
					$db_pt_id = $row_dt['patient_id'];
					$sql = imw_query("SELECT `id`, `fname`, `lname`, `mname` FROM `patient_data` WHERE `id`='".$db_pt_id."'");
					if(imw_num_rows($sql) > 0){
						$ptData = imw_fetch_assoc($sql);
						$ptData['sch_id'] = $row_dt['sch_id'];
						$ptData['show_chk'] = 'yes';
					}
				}else{
					//Get Pt. details for XML check
					$xml_db_chk = $msgConsoleObj->check_patient_details($obj['content']);
					$sql = "SELECT `id`, `fname`, `lname`, `mname` FROM `patient_data` WHERE `fname`='".$xml_db_chk['fname']."' AND `lname`='".$xml_db_chk['lname']."' AND `sex`='".$xml_db_chk['sex']."' AND `DOB`='".$xml_db_chk['dob']."' AND `city`='".$xml_db_chk['city']."' AND `state`='".$xml_db_chk['state']."' AND `postal_code`='".$xml_db_chk['zip']."'";

					$xml_db_res = imw_query($sql);
					if($xml_db_res && imw_num_rows($xml_db_res)>0){
						$ptData = imw_fetch_assoc($xml_db_res);
						$ptData['sch_id'] = 0;
					}
				}
				if(count($ptData) > 0){
					$ptData = json_encode($ptData);
				}

				//Load XML content
				$proc = new XSLTProcessor();
				$dom = new DOMDocument();
				$proc->importStylesheet($dom->loadXML($style_xsl)); //load XSL script
				$xml_doc = $proc->transformToXML($dom->loadXML($obj['content']));
				$single_file_name = $obj['name'];
				$zip_name_val = '';
				if($obj['zip_name']){
					$single_file_name = str_replace('/users/UserId_'.$_SESSION['authUserID'].'/mails/','',$obj['name']);
					$zip_name_val = $obj['zip_name'];
				}

				//---GETTING SUGGESTED PATIENTS-----
				$patient_suggestions = $msgConsoleObj->get_patient_suggestions($xml_db_chk,$direct_attach_id,$real_ccda_file_path);
				$patient_appointments = array();
				foreach($patient_suggestions['pt_details'] as $temp_pt_info){
					//------GET PATIENT APPOINTMENTS DATA FOR CCDA
					$ptApptInfo = array();
					$sqlAppt ="	SELECT sc.id AS sch_id,
									DATE_FORMAT(sc.sa_app_start_date,'".get_sql_date_format()."') as appt_date,
									DATE_FORMAT(sc.sa_app_starttime,'%h:%i %p') as appt_time,
									CONCAT_WS(', ', us.lname, us.fname) as doctor_name,
									fac.name as fac_name,
									slp.proc as proc_name
								FROM
									schedule_appointments sc
									LEFT JOIN users us ON us.id = sc.sa_doctor_id
									LEFT JOIN facility fac ON fac.id = sc.sa_facility_id
									LEFT JOIN slot_procedures slp ON slp.id = sc.procedureid
								WHERE
									sa_patient_id = '".$temp_pt_info['id']."'
								AND
									sa_patient_app_status_id NOT IN(203,201,18,19,20,3)
								ORDER BY
									sa_app_start_date DESC, sa_app_starttime DESC
								";
					$res	=  imw_query($sqlAppt);
					if(imw_num_rows($res) > 0)
					{
						while($ptApptData = imw_fetch_assoc($res)){
							$ptApptInfo[] = $ptApptData['appt_date'].' '.$ptApptData['appt_time'].' - '.$ptApptData['doctor_name'].' - '.$ptApptData['fac_name'].' - '.$ptApptData['proc_name'].':~:'.$ptApptData['sch_id'];
						}
						$patient_appointments[$temp_pt_info['id']] = $ptApptInfo;
					}
					//---------APPT WORK ENDS HERE--------------
				}
				$tmp_val = $real_ccda_file_path.'^^!!^^!!^^'.$single_file_name.'^^!!^^!!^^'.$ptData.'^^!!^^!!^^'.$zip_name_val.'^^!!^^!!^^'.$direct_attach_id.'^^!!^^!!^^'.json_encode($patient_suggestions['pt_details']).'^^!!^^!!^^'.$patient_suggestions['common_name'].'^^!!^^!!^^'.json_encode($patient_appointments);

				$return_val .= $tmp_val.'^^^^!!^^^^!!^^^^';
			}
		}
		echo $return_val;
		die;
	}
	else if($filter == "direct_msg_inbox")
	{
		if(isset($_REQUEST['mode']) && $_REQUEST['mode'] == "delete"){
			foreach($_REQUEST['chk_inbox'] as $key=>$id){
				imw_query("UPDATE direct_messages SET del_status = 1 WHERE id = '".$id."'");
			}
		}

		/*User Id for which data queries*/
		$selUsrId = (int)$_REQUEST['userId'];
		$selUsrId = ($selUsrId>0) ? $selUsrId : (int)$_SESSION['authId'];

		$provider_email = $msgConsoleObj->pt_direct_credentials($selUsrId);
		$sort_by = isset($_REQUEST['sort_by'])?$_REQUEST['sort_by']:'local_datetime';
		$sort_order = isset($_REQUEST['sort_order'])?$_REQUEST['sort_order']:'desc';

		$rq_qry = "SELECT *, DATE_FORMAT(local_datetime,'".get_sql_date_format()." %h:%i %p') as local_datetime from direct_messages WHERE `to_email` = '".$provider_email["email"]."' and imedic_user_id = '".$selUsrId."' and del_status = 0 and folder_type=1 ORDER BY $sort_by $sort_order ";

		require_once($GLOBALS['fileroot'].'/library/classes/paging.inc.php');
		$page = !isset($_REQUEST['page'])?1:$_REQUEST['page'];
		$objPaging = new Paging(30,$page);
		$objPaging->sort_by = $sort_by;
		$objPaging->sort_order = $sort_order;
		$objPaging->query = $rq_qry;
		$objPaging->func_name = "load_direct_messages";
		$rq_obj = $objPaging->fetchLimitedRecords();
		//echo $objPaging->query ;

		/*Direct Tabs*/
		$directAccessList = $msgConsoleObj->getDirectAllowedUsers($_SESSION['authId']);

		//Get unread count
		$readCountArr = array();
		$totalCountArr = array();
		if(count($directAccessList) > 0){
			foreach($directAccessList as $directAccessId){
				$accessDirectId = $directAccessId['id'];

				//$sqlQry = imw_query("SELECT count(is_read) as readCount from direct_messages WHERE imedic_user_id = '".$accessDirectId."' and del_status = 0 and folder_type=1 and is_read = 0");
				$provider_emailDirect = $msgConsoleObj->pt_direct_credentials($accessDirectId);
				//$sqlQry = imw_query("SELECT count(is_read) as readCount from direct_messages WHERE imedic_user_id = '".$accessDirectId."' and del_status = 0 and folder_type=1 and is_read = 0 and `to_email` = '".$provider_emailDirect["email"]."' ");
				$sqlQry = imw_query("SELECT count(id) as totalCount, SUM(IF(is_read=0, 1, 0)) AS readCount from direct_messages WHERE imedic_user_id = '".$accessDirectId."' and del_status = 0 and folder_type=1 and `to_email` = '".$provider_emailDirect["email"]."' ");

				if($sqlQry && imw_num_rows($sqlQry) > 0){
					$rowFetchh = imw_fetch_assoc($sqlQry);
					$readCount = $rowFetchh['readCount'];
					$totalCount = $rowFetchh['totalCount'];
				}

				if(!$readCount) $readCount = 0;
				if(!$totalCount) $totalCount = 0;

				$readCountArr[$accessDirectId] = $readCount;
				$totalCountArr[$accessDirectId] = $totalCount;
			}
		}

$tableElem = '<div class="col-sm-12">
<div class="pt5 pdl_10 pdr_10">
	<ul class="nav nav-tabs" role="tablist">';

		foreach($directAccessList as $element):

			$active = '';
			if( (int)$element['id']===$selUsrId )
				$active = ' class="active"';

			$tableElem .='<li role="presentation"'.$active.'>';
				$tableElem .= '<a href="#procnote" aria-controls="procnote" role="tab" data-prev_user_id="'.$element['id'].'" data-toggle="tab" onclick="load_direct_messages(null, \'id\', \'desc\', \'direct_msg_inbox\', '.$element['id'].')" aria-expanded="true">'.$element['name'].' ( <span class="readCount">'.trim($readCountArr[$element['id']]).'</span>/'.$totalCountArr[$element['id']].' )</a>';
			$tableElem .= '<input type="hidden" name="user_prev_id_'.$element['id'].'" id="user_prev_id_'.$element['id'].'" value="'.$element['id'].'">';
			$tableElem .= '</li>';
		endforeach;

$tableElem .= '</ul>
</div>
</div>';

	/*End Direct Tabs*/

		$tableElem .= '<form name="frmDirect" id="frmDirect" onSubmit="submit_direct(\'inbox\');return false;">
					<input type="hidden" name="filter" value="direct_msg_inbox">
					<input type="hidden" name="mode" id="mode" value="delete">
					<input type="hidden" id="xml_file_path" value=""/>';
					//pt5 pdl_10 pdr_10
		$tableElem .= '<div class="pt5 pdl_10 pdr_10 scroll-content mCustomScrollbar dynamicRightPadding" style="height:'.($_SESSION['wn_height']-380).'px">
						<table class="table table-bordered" style="margin-bottom:0">';
		$tableElem .= '<thead>
							<tr class="purple_bar">
								<th class="text-center" style="width: 70px;">
									<div class="checkbox">
										<input id="checkbox" type="checkbox" name="chk_sel_all" class="chk_sel_all">
										<label for="checkbox">&nbsp;</label>
									</div>
								</th>
								<th class="dont-break-out" id="from_email" onClick="load_direct_messages(1,\'from_email\',\''.(($sort_by == 'from_email' && $sort_order == 'asc') ? 'desc' : 'asc').'\')" style="cursor:pointer">FROM</th>
								<th id="subject" onClick="load_direct_messages(1,\'subject\',\''.(($sort_by == 'subject' && $sort_order == 'asc') ? 'desc' : 'asc').'\')" style="cursor:pointer">SUBJECT</th>
								<th id="local_datetime" onClick="load_direct_messages(1,\'local_datetime\',\''.(($sort_by == 'local_datetime' && $sort_order == 'asc') ? 'desc' : 'asc').'\')" style="cursor:pointer">DATE</th>
								<th id="MID" onClick="load_direct_messages(1,\'MID\',\''.(($sort_by == 'MID' && $sort_order == 'asc') ? 'desc' : 'asc').'\')" style="cursor:pointer">MID</th>
								<th style="min-width:100px">PATIENT INFO</th>
							</tr>
						</thead>';
		$tableElem .= '<tbody><tr><td colspan="5" style="padding: 0 !important; border: 0;"><div class="row msgCount">';
		$tableElem .= 		'<div class="col-sm-7 text-left">'.$objPaging->getPagingString().'</div>';
		$tableElem .= 		'<div class="col-sm-5 text-right"><div class="row">'.$objPaging->buildComponentR8($page).'</div></div>';
		$tableElem .= '</div></td></tr>';

		foreach($rq_obj as $key=>$rq_data)
		{
			$class_bold = "";
			$class_bold_fn = "";
			if($rq_data["is_read"] == 0)
			{
				$class_bold = "bold";
				$class_bold_fn = 'make_unbold_direct_msg('.$rq_data["id"].', this);';
			}
			$attachments_arr = getDirectAttachment($rq_data["id"]);
			/****GET PATIENT DETAIL****/
			if(count($attachments_arr)>0) $attachments_arr[0]['patient_details'] = '';
			if(count($attachments_arr)>0 && $attachments_arr[0]["patient_id"]>0){
				$sql = imw_query("SELECT CONCAT(lname,', ',fname,' - ',id) as pt_details FROM `patient_data` WHERE `id`='".$attachments_arr[0]["patient_id"]."' LIMIT 1");
				if(imw_num_rows($sql)==1){
					$ptData = imw_fetch_assoc($sql);
					$attachments_arr[0]['patient_details'] = $ptData['pt_details'];
				}
			}
			/******PT DETAIL END****/

			$attach_link = '';
			if(count($attachments_arr) > 0){
				$attach_link = '<span class="glyphicon glyphicon-paperclip pull-right"></span>';
			}
			$tableElem .= '
			<tr id="direct_'.$rq_data["id"].'" style="cursor:pointer;" class="even-odd-resp-person '.$class_bold.'" onclick="'.$class_bold_fn.'open_next_row(this);">
					<td class="text-center">
					<div class="checkbox">
						<input id="checkbox'.$key.'" type="checkbox" name="chk_inbox[]" value="'.$rq_data["id"].'" class="chk_record">
						<label for="checkbox'.$key.'">&nbsp;</label>
					</div>
					</td>
					<td class="dont-break-out">'.$rq_data["from_email"].' '.$attach_link.'</td>
					<td>'.$rq_data["subject"].'</td>
					<td>'.$rq_data["local_datetime"].'</td>
					<td>'.$rq_data["MID"].'</td>
					<td>'.$attachments_arr[0]['patient_details'].'</td>
			</tr>
			<tr class="tr_pt_msg_details hide">
				<td></td>
				<td colspan="5">
					<table style="width:100%">';
			if(count($attachments_arr)>0){
				$attach_btn_str = '';
				foreach($attachments_arr as $attachment_val){
					$attach_file_path 	= $dir_path.'/users'.$attachment_val['complete_path'];
					$attach_file_key 	= get_checksum_key_val($attach_file_path);
					$download_tooltip 	= show_tooltip('Download','top');
					$attachment_mime	= $attachment_val['mime'];
					$attachment_extension= substr($attachment_val['complete_path'],-4);
					$sha_key_button 	= '';
					if(empty($attach_file_key) == false){
						$sha_key_button = "<button type='button' class='btn btn-success btn-info' data-toggle='tooltip' title='".$attach_file_key."' data-trigger='focus' data-placement='top'>SHA2 Key</button>";
					}
					$attach_btn_str .= "
						<div class='col-sm-4'>
							<div class='btn-group'>
								<button type='button' class='btn btn-success' data-toggle='tooltip' data-html='true' data-placement='top' title='Download' onclick='download_ccda(\"".$attachment_val['complete_path']."\");'>".basename($attachment_val['complete_path'])."</button>";
					if(strtolower($attachment_mime)=='application/xml' || strtolower($attachment_mime)=='text/xml' || strtolower($attachment_extension)=='.xml' || strtolower($attachment_extension)=='.zip'){
						$attach_btn_str .= "
								<button type='button' class='btn btn-success purple_button' onclick='view_ccda(\"".$attachment_val['complete_path']."\",\"".$rq_data["id"]."\")'>View</button>".$sha_key_button;
					}else if(!in_array(strtolower($attachment_extension),array('.doc','docx','.xls','xlsx'))){
						$attach_btn_str .= "
								<button type='button' class='btn btn-success purple_button' onclick='view_attachment(\"".$attachment_val['complete_path']."\",\"".$attachment_val["id"]."\",\"".$rq_data["id"]."\")'>View</button>".$sha_key_button;
					}
					$attach_btn_str .= "
							</div>
						</div>";
				}

				$tableElem .= '
					<tr>
						<td colspan="2">
							<div class="row">
								<div class="col-sm-1">
									<b>Attachment(s)</b>
								</div>
								<div class="col-sm-11">
									<div class="row">
										'.$attach_btn_str.'
									</div>
								</div>
							</div>
						</td>
					</tr>';
			}
			$tableElem .= '
						<tr>
							<td class="text-left"><b>Message</b></td>
							<td class="text-right"><b><button type="button" target="console_form" class="btn btn-success" id="replyBtn_'.$rq_data["id"].'" onClick="reply_direct(\''.$key.'\',\''.$rq_data["imedic_user_id"].'\');">Reply</button></b>
							</td>
						</tr>
						<tr>
							<td colspan="2"><div style="width:98%; margin:2px">'.nl2br($rq_data["message"]).'</div></td>
						</tr>
					</table>
				</td>
			</tr>
			';
		}
		if(count($rq_obj)<=0){
			$tableElem .= '<tr><td colspan="6" align="center">No records found.</td></tr>';
		}
		$tableElem .= '</tbody>';
		$tableElem .= '</table>';
		$tableElem .= '</div>';

		$buttons = '';
		if(count($rq_obj)>0){
			$buttons='
				<div class="clearfix"></div>
				<div class="row">
					<div class="col-sm-12 pt5 pdb5 text-center">
						<button type="submit" value="Delete" name="Delete" class="btn btn-danger" onClick="submit_direct(\'sent\');return false;">Delete</button>
					</div>
				</div>';
		}
		$tableElem .= $buttons;
		$tableElem .= '</form>';
		echo $tableElem;
	}
	//load_direct_messages(1,'".$sort_by."','".$next_sort_order."');
	else if($filter == "direct_msg_outbox")
	{

	}
	else if($filter == "direct_msg_sent")
	{
		if(isset($_REQUEST['mode']) && $_REQUEST['mode'] == "delete"){
			foreach($_REQUEST['chk_sent'] as $key=>$id){
			imw_query("UPDATE direct_messages SET del_status = 1 WHERE id = '".$id."'");
			}
		}
		/*User Id for which data queries*/
		$selUsrId = (int)$_REQUEST['userId'];
		$selUsrId = ($selUsrId>0) ? $selUsrId : (int)$_SESSION['authId'];

		$provider_email = $msgConsoleObj->pt_direct_credentials($selUsrId);

		$sort_by = isset($_REQUEST['sort_by'])?$_REQUEST['sort_by']:'id';
		$sort_order = isset($_REQUEST['sort_order'])?$_REQUEST['sort_order']:'desc';

		$rq_qry = "SELECT *, DATE_FORMAT(local_datetime,'".get_sql_date_format()." %h:%i %p') as local_datetime from direct_messages WHERE `from_email` = '".$provider_email["email"]."' and imedic_user_id = '".$selUsrId."' and del_status =0 and folder_type=3 ORDER BY $sort_by $sort_order ";

		require_once($GLOBALS['fileroot'].'/library/classes/paging.inc.php');
		$page = !isset($_REQUEST['page'])?1:$_REQUEST['page'];
		$objPaging = new Paging(30,$page);
		$objPaging->sort_by = $sort_by;
		$objPaging->sort_order = $sort_order;
		$objPaging->query = $rq_qry;
		$objPaging->func_name = "load_direct_messages";
		$objPaging->filter = "direct_msg_sent";
		$rq_obj = $objPaging->fetchLimitedRecords();

		/*Direct Tabs*/
		$directAccessList = $msgConsoleObj->getDirectAllowedUsers($_SESSION['authId']);

		//Get unread count
		$readCountArr = array();
		$totalCountArr = array();
		if(count($directAccessList) > 0){
			foreach($directAccessList as $directAccessId){
				$accessDirectId = $directAccessId['id'];

				//$sqlQry = imw_query("SELECT count(is_read) as readCount from direct_messages WHERE imedic_user_id = '".$accessDirectId."' and del_status = 0 and folder_type=1 and is_read = 0");
				$provider_emailDirect = $msgConsoleObj->pt_direct_credentials($accessDirectId);

				//$sqlQry = imw_query("SELECT count(is_read) as readCount from direct_messages WHERE imedic_user_id = '".$accessDirectId."' and del_status = 0 and folder_type=1 and is_read = 0 and `to_email` = '".$provider_emailDirect["email"]."' ");
				$qry = "SELECT count(id) as totalCount, SUM(IF(is_read=0, 1, 0)) AS readCount from direct_messages WHERE imedic_user_id = '".$accessDirectId."' and del_status = 0 and folder_type=3 and `from_email` = '".$provider_emailDirect["email"]."' ";
				$sqlQry = imw_query($qry);

				if($sqlQry && imw_num_rows($sqlQry) > 0){
					$rowFetchh = imw_fetch_assoc($sqlQry);
					$readCount = $rowFetchh['readCount'];
					$totalCount = $rowFetchh['totalCount'];
				}

				if(!$readCount) $readCount = 0;
				if(!$totalCount) $totalCount = 0;

				$readCountArr[$accessDirectId] = $readCount;
				$totalCountArr[$accessDirectId] = $totalCount;
			}
		}

$tableElem = '<div class="col-sm-12">
<div class="pt5 pdl_10 pdr_10">
	<ul class="nav nav-tabs" role="tablist">';

		foreach($directAccessList as $element):
			$active = '';
			if( (int)$element['id'] === $selUsrId)
				$active = ' class="active"';
			if( $element['name'] == 'My Inbox' )
				$element['name'] = 'Sent Box';

			$tableElem .='<li role="presentation"'.$active.'>';
				$tableElem .= '<a href="#procnote" aria-controls="procnote" role="tab" data-prev_user_id="'.$element['id'].'" data-toggle="tab" onclick="load_direct_messages(null, \'id\', \'desc\', \'direct_msg_sent\', '.$element['id'].')" aria-expanded="true">'.$element['name'].' ( <span class="readCount">'.trim($readCountArr[$element['id']]).'</span>/'.$totalCountArr[$element['id']].' )</a>';
			$tableElem .= '<input type="hidden" name="user_prev_id_'.$element['id'].'" id="user_prev_id_'.$element['id'].'" value="'.$element['id'].'">';
			$tableElem .= '</li>';
		endforeach;

$tableElem .= '</ul>
</div>
</div>';

	/*End Direct Tabs*/


		$tableElem .= '<form name="frmDirect" id="frmDirect" onSubmit="submit_direct(\'sent\');return false;">
					<input type="hidden" name="filter" value="direct_msg_sent">
					<input type="hidden" name="mode" id="mode" value="delete">';
		$tableElem .= '<div class="pt5 pdl_10 pdr_10 scroll-content mCustomScrollbar dynamicRightPadding" style="height:'.($_SESSION['wn_height']-344).'px">
						<table class="table table-bordered">';
		$tableElem .= '<thead>
							<tr class="purple_bar">
								<th class="text-center" style="width:70px">
									<div class="checkbox">
										<input id="checkbox" type="checkbox" name="chk_sel_all" class="chk_sel_all">
										<label for="checkbox">&nbsp;</label>
									</div>
								</th>

								<th><span id="to_email" onClick="load_direct_messages(1,\'to_email\',\''.(($sort_by == 'to_email' && $sort_order == 'asc') ? 'desc' : 'asc').'\',\'direct_msg_sent\')" style="cursor:pointer">TO</span> </th>
								<th><span id="subject" onClick="load_direct_messages(1,\'subject\',\''.(($sort_by == 'subject' && $sort_order == 'asc') ? 'desc' : 'asc').'\',\'direct_msg_sent\')" style="cursor:pointer">SUBJECT</span> </th>
								<th style="min-width:150px"><span id="local_datetime" onClick="load_direct_messages(1,\'local_datetime\',\''.(($sort_by == 'local_datetime' && $sort_order == 'asc') ? 'desc' : 'asc').'\',\'direct_msg_sent\')" style="cursor:pointer">DATE</span></th>
								<th style="min-width:100px"><span id="MID" onClick="load_direct_messages(1,\'MID\',\''.(($sort_by == 'MID' && $sort_order == 'asc') ? 'desc' : 'asc').'\',\'direct_msg_sent\')" style="cursor:pointer">MID </span> </th>
								<th style="min-width:100px">PATIENT INFO</th>
								<th style="min-width:100px">Status</th>
							</tr>
						</thead>
						<tbody>';
		$tableElem .= '<tr><td colspan="6"><div class="row">';
		$tableElem .= '<div class="col-sm-6 text-left">'.$objPaging->getPagingString().'</div>';
		$tableElem .= '<div style="col-sm-6 text-right">'.$objPaging->buildComponent($page).'</div>';
		$tableElem .= '</div></td></tr>';

		foreach($rq_obj as $key=>$rq_data)
		{
			$attachments_arr = getDirectAttachment($rq_data["id"]);
			/****GET PATIENT DETAIL****/
			$attachments_arr[0]['patient_details'] = '';
			if(count($attachments_arr)>0 && $attachments_arr[0]["patient_id"]>0){
				$sql = imw_query("SELECT CONCAT(lname,', ',fname,' - ',id) as pt_details FROM `patient_data` WHERE `id`='".$attachments_arr[0]["patient_id"]."' LIMIT 1");
				if(imw_num_rows($sql)==1){
					$ptData = imw_fetch_assoc($sql);
					$attachments_arr[0]['patient_details'] = $ptData['pt_details'];
				}
			}
			/******PT DETAIL END****/
			$attach_link = '';
			if(count($attachments_arr) > 0){
				$attach_link = '<span class="glyphicon glyphicon-paperclip pull-right"></span>';
			}

			/*Get MDN Status for Updox Fax*/
			$sqMDN = 'SELECT `status` FROM `direct_messages_log` WHERE `updox_message_id`='.(int)$rq_data["MID"].' ORDER BY `log_id` DESC';
			$respMDN = imw_query($sqMDN);
			$respMDN = imw_fetch_assoc($respMDN);
			$mdnStatus = trim($respMDN['status']);

			$tableElem .= '<tr class="even-odd-resp-person pointer" onclick="open_next_row(this);">
					<td class="text-center">
						<div class="checkbox">
							<input id="checkbox'.$key.'" type="checkbox" name="chk_sent[]" value="'.$rq_data["id"].'" class="chk_record">
							<label for="checkbox'.$key.'">&nbsp;</label>
						</div>
					</td>
					<td class="dont-break-out">'.$rq_data["to_email"].' '.$attach_link.'</td>
					<td>'.$rq_data["subject"].'</td>
					<td style="min-width:150px">'.$rq_data["local_datetime"].'</td>
					<td style="min-width:80px">'.$rq_data["MID"].'</td>
					<td style="min-width:80px">'.$attachments_arr[0]['patient_details'].'</td>
					<td style="min-width:80px">'.$mdnStatus.'</td>
			</tr>';
			$attachment_btn_str = $attachment_row = '';
				foreach($attachments_arr as $attachment_val){
					$attach_file_path = $dir_path.'/users'.$attachment_val['complete_path'];
					$attach_file_key = get_checksum_key_val($attach_file_path);
					$sha_key_button = '';
					if(empty($attach_file_key) == false){
						$sha_key_button = "<button type='button' class='btn btn-success btn-info' data-toggle='tooltip' title='".$attach_file_key."' data-trigger='focus' data-placement='top'>SHA2 Key</button>";
					}

					$attachment_mime	= $attachment_val['mime'];
					$attachment_extension= substr($attachment_val['complete_path'],-4);

					//
					$view_key_button = "";
					//<button type='button' class='btn btn-success purple_button' onclick='view_ccda(\"".$attachment_val['complete_path']."\",\"".$rq_data["id"]."\")'>View</button>
					if(strtolower($attachment_mime)=='application/xml' || strtolower($attachment_mime)=='text/xml' || strtolower($attachment_extension)=='.xml' || strtolower($attachment_extension)=='.zip'){
						$view_key_button .= "
								<button type='button' class='btn btn-success purple_button' onclick='view_ccda(\"".$attachment_val['complete_path']."\",\"".$rq_data["id"]."\")'>View</button>";
					}else if(!in_array(strtolower($attachment_extension),array('.doc','docx','.xls','xlsx'))){
						$view_key_button .= "
								<button type='button' class='btn btn-success purple_button' onclick='view_attachment(\"".$attachment_val['complete_path']."\",\"".$attachment_val["id"]."\",\"".$rq_data["id"]."\")'>View</button>";
					}

					$attachment_btn_str .= "
						<div class='col-sm-4'>
							<div class='btn-group'>
								<button type='button' class='btn btn-success'  onclick='download_ccda(\"".$attachment_val['complete_path']."\");'>".basename($attachment_val['complete_path'])."</button>
								".$view_key_button.$sha_key_button."
							</div>
						</div>";
				}

				$attachment_row .= '
					<tr>
						<td colspan="2">
							<div class="row">
								<div class="col-sm-1">
									<b>Attachment(s)</b>
								</div>
								<div class="col-sm-11">
									<div class="row">
										'.$attachment_btn_str.'
									</div>
								</div>
							</div>
						</td>
					</tr>';
			$tableElem .= '
			<tr class="tr_pt_msg_details hide">
				<td></td>
				<td colspan="5">
					<table style="width:100%">
						'.$attachment_row.'
						<tr>
							<td><b>Sent Time</b></td>
							<td>'.$rq_data["local_datetime_f"].'</td>
						</tr>
						<tr>
							<td colspan="2"><b>Message</b></td>
						</tr>
						<tr>
							<td style="line-height:18px;" colspan="2">'.nl2br($rq_data["message"]).'</td>
						</tr>
					</table>
				</td>
			</tr>';
		}
		if(count($rq_obj)<=0){
			$tableElem .= '<tr><td colspan="7" class="alert alert-danger">No records found.</td></tr>';
		}
		$tableElem .= '</tbody>';
		$tableElem .= '</table>';
		$tableElem .= '</div>';
		$buttons = '';
		if(count($rq_obj)>0){
			$buttons = '<div class="clearfix"></div>
				<div class="row pt5">
					<div class="col-sm-12 text-center">
						<button type="submit" value="Delete" name="Delete" class="btn btn-danger" onClick="submit_direct(\'sent\');return false;">Delete</button>
					</div>
				</div>';
		}
		$tableElem .= $buttons;
		$tableElem .= '</form>';
		echo $tableElem;
	}
	else if($filter == "ccda_docs_entry"){
		$return_arr = array();
		$file_name = $_REQUEST['ccda_file'];
		$pt_id = $_REQUEST['pt_id'];
		$pt_sch_id = $_REQUEST['pt_sch_id'];
		$zip_file = (empty($_REQUEST['zip_file']) == false) ? $_REQUEST['zip_file'] : '';
		$move_to_patient = (bool)$_REQUEST['move_ccda'];
		$move_status = false;
		$zip_file_dir = $dir_path.'/users/UserId_'.$_SESSION['authId'].'/mails/';
		$direct_attach_id = $_REQUEST['direct_attach_id_new'];
		if(empty($zip_file) == false){
			//Unlink file only if zip file is viewed
			if(file_exists($zip_file_dir.$file_name)){
				unlink($zip_file_dir.$file_name);
			}

			$ccda_file = $dir_path.$zip_file;
			$zip = new ZipArchive;
			if($zip->open($ccda_file) == TRUE){
				for($i=0; $i<$zip->numFiles; $i++){
					$name = $zip->getNameIndex($i);
					if(strpos(strtolower($name),".xml") !== false || strpos(strtolower($name),".txt") !== false){
						if($file_name == $name){
							$move_status = $zip->extractTo($zip_file_dir, $name);
							if(!$move_status){
								$return_arr['error'] = 'Problem occured. Please try again';
							}
						}
					}
				}
			}
		}

		if(!$return_arr['error']){
			$db_file_name = $file_name;
			if(empty($zip_file) == false){
				$db_file_name = str_replace($dir_path,'',$zip_file_dir.$file_name);
			}

			//Check whether it is new entry or already exist
			if($move_to_patient || 1){// updating in all case.  10.14.19
				$direct_msg_attachement_id = 0;
				if(isset($_SESSION['opened_attachment_id']) && !empty($_SESSION['opened_attachment_id'])){
					$direct_msg_attachement_id = $_SESSION['opened_attachment_id'];
					imw_query("UPDATE direct_messages_attachment SET patient_id = '".$pt_id."', sch_id='".$pt_sch_id."' WHERE id='".$direct_msg_attachement_id."' LIMIT 1");
				}
				$db_query = imw_query("select * from `ccda_docs` where file_path = '".$db_file_name."' and direct_msg_id = '".$direct_attach_id."'");
				if(imw_num_rows($db_query) > 0){
					$row_dt = imw_fetch_assoc($db_query);
					$rec_id = $row_dt['id'];
					$update_qry = imw_query("update ccda_docs set patient_id = '".$pt_id."', file_path = '".$db_file_name."',operator_id = '".$_SESSION['authUserID']."', direct_msg_id = '".$direct_attach_id."', direct_attach_id='".$direct_msg_attachement_id."',sch_id='".$pt_sch_id."' Where id = '".$rec_id."'");
					if($update_qry){
						$return_arr['status'] = 'done';
					}
				}else{
					$sql = "INSERT INTO `ccda_docs`(`patient_id`, `file_path`, `operator_id`, `direct_msg_id`, `direct_attach_id`, `sch_id`) VALUES('".$pt_id."', '".$db_file_name."', '".$_SESSION['authUserID']."','".$direct_attach_id."', '".$direct_msg_attachement_id."', '".$pt_sch_id."')";
					if(imw_query($sql)){
						$return_arr['status'] = 'done';
					}
				}
			}

		}
		echo json_encode($return_arr);
		exit;
	}else if($filter == 'chk_exist_ccda'){
		$return_arr = array();
		$return_arr['chk_exist'] = false;
		$file_name = $_REQUEST['ccda_file'];
		$pt_id = $_REQUEST['pt_id'];
		$zip_file = (empty($_REQUEST['zip_file']) == false) ? $_REQUEST['zip_file'] : '';
		$pt_name_arr = core_get_patient_name($pt_id);
		$return_arr['pt_name'] = core_name_format($pt_name_arr[2], $pt_name_arr[1], $pt_name_arr[3]);

		if(empty($zip_file) == false){
			$ccda_file = $dir_path.$zip_file;
			$zip = new ZipArchive;
			if($zip->open($ccda_file) == TRUE){
				for($i=0; $i<$zip->numFiles; $i++){
					$name = $zip->getNameIndex($i);
					if(strpos(strtolower($name),".xml") !== false || strpos(strtolower($name),".txt") !== false){
						if($file_name == $name){
							$zip_file_name = '/users/UserId_'.$_SESSION['authId'].'/mails/'.$name;
							$db_qry = imw_query("select * from `ccda_docs` where patient_id = '".$pt_id."' and file_path = '".$zip_file_name."'");
							if(imw_num_rows($db_qry) > 0){
								$return_arr['chk_exist'] = true;
							}
						}
					}
				}
			}
		}else{
			$db_qry = imw_query("select * from `ccda_docs` where patient_id = '".$pt_id."' and file_path = '".$file_name."'");
			if(imw_num_rows($db_qry) > 0){
				$return_arr['chk_exist'] = true;
			}
		}
		echo json_encode($return_arr);
		exit;
	}else if($filter == 'get_ccda_patient'){
		$return_arr = array();

		$zip_name = (isset($_REQUEST['zip_name']) && empty($_REQUEST['zip_name']) == false) ? $_REQUEST['zip_name'] : '';
		$file_name = (isset($_REQUEST['file_name']) && empty($_REQUEST['file_name']) == false) ? $_REQUEST['file_name'] : '';

		if(empty($zip_name) && empty($file_name)){
			$return_arr['error'] = 'No CCDA Found !';
		}

		if(count($return_arr) == 0){
			$ccda_patient_data = $msgConsoleObj->create_ccda_patient($zip_name,$file_name);
			if($ccda_patient_data['error']){
				$return_arr['error'] = $ccda_patient_data['error'];
			}else{
				$return_arr['patientData'] = $ccda_patient_data['PatientData'];
				$return_arr['FieldsMappedArr'] = $ccda_patient_data['FieldsMappedArr'];
				$return_arr['zip_name'] = $zip_name;
				$return_arr['file_name'] = $file_name;
			}
		}
		echo json_encode($return_arr);
		exit;
	}else if($filter == 'create_new_patient'){
		$insert_id = '';
		$zip_name = $_REQUEST['zip_name'];
		$file_name = $_REQUEST['file_name'];
		$direct_attach_id = $_REQUEST['direct_attach_id'];

		unset($_REQUEST['filter']);
		unset($_REQUEST['zip_name']);
		unset($_REQUEST['file_name']);
		unset($_REQUEST['direct_attach_id']);

		$return_arr = array();
		$return_arr['count'] = 0;
		if(empty($_REQUEST['fname']) == false && empty($_REQUEST['lname']) == false){
			$_REQUEST['DOB'] = getDateFormatDB($_REQUEST['DOB']);
			$_REQUEST['date'] = date('Y-m-d  H:i:s');
			$_REQUEST['created_by'] = $_SESSION['authId'];
			$_REQUEST['hipaa_mail'] = 1;
			$_REQUEST['hipaa_email'] = 1;
			$_REQUEST['hipaa_voice'] = 1;

			if($_REQUEST['lang_code']=='eng') $_REQUEST['lang_code']='en';

			$insert_id = AddRecords($_REQUEST,'patient_data');
			if(empty($insert_id) == false){
				$return_arr['count'] = $return_arr['count'] + imw_affected_rows();
				$update_id = imw_query('UPDATE `patient_data` SET pid = '.$insert_id.' where id = '.$insert_id.'');
				if($update_id){
					$return_arr['count'] = $return_arr['count'] + imw_affected_rows();
					$return_arr['file_name'] = str_replace('/users/','/',$file_name);
					$return_arr['zip_name'] = str_replace('/users/','/',$zip_name);
					$return_arr['patient_id'] = $insert_id;
					$return_arr['direct_attach_id'] = $direct_attach_id;
				}
			}
		}else{
			$return_arr['error'] = 'Incomplete Patient Data provided';
		}
		echo json_encode($return_arr);
		exit;
	}

function getDirectAttachment($direct_msg_id){
	$dir_path=$GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH');
	$direct_attachments_arr = array();
	$rq = "SELECT * FROM  direct_messages_attachment WHERE direct_message_id = '".$direct_msg_id."';";
	$rq_obj = imw_query($rq);
	while($direct_attachment = imw_fetch_assoc($rq_obj)){
		$file_loc = $dir_path.'/users'.$direct_attachment['complete_path'];
		if(file_exists($file_loc) > 0){
			$direct_attachments_arr[] = $direct_attachment;
		}
	}
	return $direct_attachments_arr;
}

$usersArr=array();
$qry = imw_query("select id,lname,fname,mname from users where id > 0 and delete_status = '0' order by lname,fname");
while ($userQryRes = imw_fetch_assoc($qry)) {
    $id = $userQryRes['id'];

    $phyName = $userQryRes['lname'] . ', ';
    $phyName .= $userQryRes['fname'] . ' ';
    $phyName .= $userQryRes['mname'];
    if ($phyName[0] == ',') {
        $phyName = preg_replace('/, /', '', $phyName);
    }

    $phyName = trim(ucwords($phyName));
    $usersArr[$id]=$phyName;
}
//unset($usersArr[$_SESSION['authId']]);

$directAccessUsersList = $msgConsoleObj->getDirectAllowedUsers($_SESSION['authId']);
$fromdd='';
foreach($directAccessUsersList as $directUser) {
    //if($_SESSION['authId']==$directUser['id'])continue;
    $fromdd.='<option value="'.$directUser['id'].'" >'.$usersArr[$directUser['id']].'</option>';
}
$slectStr='';
if($fromdd!='') {
    $slectStr='<select name="dd_from" id="dd_from" class="form-control minimal" onchange="change_from_email_send(this);"> ';
        $slectStr.='<option value="">select</option>'.$fromdd;
    $slectStr.='</select>';
}


//'from_email_send';
?>
<?php
$patientId = isset($_SESSION['patient']) ? intval($_SESSION['patient']) : '';
if($patientId != "" && $patientId != 0){
	$patientId = intval($_SESSION['patient']);
	$ar_ptName = core_get_patient_name($patientId);
	$patName = $ar_ptName[1].', '.$ar_ptName[2];
}
?>
<style>
.font_nrml{
font-family: 'robotoregular';
}
</style>
<script>
		var up_dir_path = '<?php echo $dir_path; ?>';
    function change_from_email_send(obj) {
        var elid=$(obj).attr("id");
        if($('#'+elid).find("option:selected").length==0) {
            $('#from_email_send').val('');
        } else {
            $('#from_email_send').val($('#'+elid+' option:selected').val());
        }
    }

	function check_patient_msg_frm(flgSv){

		var to_email =  document.frmForm.to_email.value;
		if($.trim(to_email) == ""){
			top.fAlert("Please Specify the Email address");
			return false;
		}

		var subject = document.frmForm.subject.value;
		if($.trim(subject) == ""){
			top.fAlert("Please Specify the Message Subject");
			return false;
		}

		$("#div_loading_image").show();
		$('#loader').html('<div class="doing"></div>');
		if(typeof(flgSv)=="undefined" && typeof(file_attached)!="undefined" && file_attached.length<=0){
			flg_auto_save=1;
			start_upload();
			return;
		}

		flg_auto_save=0;
		frm_data = $('#frmForm,#ccda_opt_form').serialize();
		if(typeof(file_attached)!="undefined" && file_attached.length>0){
			frm_data += "&attchd_files="+JSON.stringify(file_attached);
			file_attached=[];
		}else if($('#frm_pt_docs_atch').length>0 && typeof(ar_attch_pt_files)!="undefined" && ar_attch_pt_files.length>0){
			frm_data += "&attchd_files_pt_docs="+JSON.stringify(ar_attch_pt_files);
			ar_attch_pt_files=[];
		}

		$.ajax({
			type: "POST",
			url: "sync_direct.php",
			data: frm_data,
			dataType:'JSON',
			success: function(d) {
				$("#div_loading_image").hide();
				if(d.error){
					fAlert(d.error);
					return false;
				}
				$('#divContainer .modal-header .close').modal('hide');
				$('#divContainer .modal-header .close').trigger("click");
				if($("#upldModal").length>0){
					$('#fileupload').fileupload('destroy');
					$("#upldModal").remove();
				}
				
				if($("#attchPtDocModal").length>0){
					$("#attchPtDocModal").remove();
				}

				setTimeout(function(){do_action('load_direct_messages','direct_msg_sent');}, 100);
			}
		});

	}
function getPatDOS(){
	if(document.getElementById('patientId')){
		if(document.getElementById('patientId').value){
			getDOS(document.getElementById('patientId').value);
		}
		else{
			top.fAlert('Please select patient to proceed to Get DOS');
		}
	}
	else{
		top.fAlert('Please select patient to proceed to Get DOS');
	}
}

function getDOS(patId){
	$.ajax({
		url:URL+'/interface/physician_console/ajax_html.php',
		type:'POST',
		data:'from=console&task=get_pat_dos&pId='+patId,
		dataType:'JSON',
		success:function(response){
			if(response.dos_str){
				$('#tdPatDOS').html(response.dos_str);
			}else{
				fAlert("Patient does not have any DOS");
			}
		}
	});
}
function get_options_string(){
		var obj = document.getElementsByName("ccdDocumentOptions[]");
		arrOption = new Array();
		for(f=0;f<obj.length;f++){
			if(obj[f].checked){
				arrOption[arrOption.length] = obj[f].value;
			}
		}
		return arrOption;
	}
function create_ccda(){
		$("#tdAttach").html("");
		strOptions = get_options_string();

		arrData = {};
		arrData.pat_id = $("#patientId").val();
		arrData.form_id = $("#cmbxElectronicDOS").val();
		arrData.ccd_type = $("#ccd_type_ccd").prop('checked') ? 'ccd' : ($("#ccd_type_rn").prop('checked') ? 'rn' : '');
		arrData.pat_name = $("#txt_patient_name").val();
		arrTmp = {};
		arrTmp[0] = arrData;
		$('#tdAttach').html('<div class="doing"></div>');
		var form_data = 'arrData='+JSON.stringify(arrTmp)+'&create_type=attachment&ccdDocumentOptions='+strOptions;
		//a=window.open();a.document.write(URL+'/interface/reports/ccd/create_ccda_r2_xml.php?'+form_data);
		$.ajax({
			url:URL+'/interface/reports/ccd/create_ccda_r2_xml.php',
			data:form_data,
			type:'POST',
			success:function(response){
				var attach_val = $('#direct_attachment_type').val();
				if(attach_val != ''){
					$('#direct_attachment_value').val(attach_val);
				}
				ccda_log_id = response;
				if(ccda_log_id != ""){
					$("#ccda_log_id").val(ccda_log_id);
					if(attach_val == 'ccda'){
						if($("#ccda_attachment").hasClass('hide') == true){
							$('#ccda_button_attachment').find('button[data-attach]').prop('disabled',true);
							$("#ccda_attachment").removeClass('hide');
						}
						if($('#xml_button_attachment').hasClass('hide') == false){
							$("#xml_button_attachment").addClass('hide');
						}
					}else{
						if($("#xml_attachment").hasClass('hide') == true){
							$('#xml_button_attachment').find('button[data-attach]').prop('disabled',true);
							$("#xml_attachment").removeClass('hide');
						}
						if($('#ccda_button_attachment').hasClass('hide') == false){
							$("#ccda_button_attachment").addClass('hide');
						}
					}

					//$("#div_popup").modal('hide');
				}
			}
		});
	}

function reply_direct(key,imedic_user_id){
	if(!imedic_user_id) imedic_user_id = null;
	var arrObj = <?php echo json_encode($rq_obj);?>;
	var fromUserId = '<?php echo $selUsrId; ?>';
	<?php
	//this condition mesage this functionality is by default on
	if(!defined('REPLY_AS_PHYSICIAN') || constant('REPLY_AS_PHYSICIAN')==true){?>
	if(imedic_user_id) fromUserId = imedic_user_id;
	<?php }?>

	msgBody = "\n\n\n\n-----ORIGINAL MESSAGE---------------\n";
	msgBody += "From: "+arrObj[key]['from_email']+"\n";
	msgBody += "To: "+arrObj[key]['to_email']+"\n";
	msgBody += "Sent: "+arrObj[key]['local_datetime']+"\n";
	msgBody += "Subject: "+arrObj[key]['subject']+"\n\n";
	msgBody += arrObj[key]['message'];

	$('body').on('show.bs.modal','#divContainer',function(){
		$("input[id='reply_of']").val(arrObj[key]['id']);
		$("input[id='to_email']").val(arrObj[key]['from_email']);
		$("input[id='subject']").val("Re: "+arrObj[key]['subject']);
		$("input[id='from_email_send']").val(fromUserId);
		$("select[id='dd_from']").val(fromUserId);
		$("textarea[id='body']").val(msgBody);
	});

	load_ptcomm_ptinfo('<?php echo $patientId; ?>');
	$("#divContainer").modal('show');
}


</script>

<!-- CCDA View Modal -->
<div id="div_ccda_main" class="modal" role="dialog">
	<div class="modal-dialog modal_90">
		<div class="modal-content">
			<div class="modal-header bg-primary">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">imwemr CCDA viewer</h4>
			</div>
			<div id="div_ccda_container" class="modal-body"></div>
			<div id="module_buttons" class="ad_modal_footer modal-footer ">
				<div class="row">
					<div class="col-sm-12 text-center">
						<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<!-- Reply Box Modal -->
<div id="divContainer" class="modal" role="dialog">
	<div class="modal-dialog" style="width:85%;">
		<div class="modal-content">
			<div class="modal-header bg-primary">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Send Direct Message</h4>
			</div>
			<div id="div_ccda_container" class="modal-body">
				<form name="frmForm" id="frmForm" autocomplete="off">
					<input type="hidden" name="ccda_log_id" id="ccda_log_id" value="">
					<input type="hidden" value="" id="direct_attachment_type">
					<div class="row">
						<!-- Mail Form -->
						<div id="divForm" class="col-sm-6">
							<div class="adminbox">
								<div class="head">
									<div class="row">
										<div class="col-sm-10"><span>Mail Details</span></div>
										<div class="col-sm-2">
											<div class="dropdown">
												<button class="btn dropdown-toggle" type="button" data-toggle="dropdown">
													<span class="glyphicon glyphicon-paperclip clickable pull-right font-18" id="dv_attch"  title="Attachments" ></span>
												</button> <!-- <span class="caret"></span> -->
												<ul class="dropdown-menu font_nrml">
													<li><a href="#" onclick="load_mdl_atch(1)">Local PC</a></li>
													<li><a href="#" onclick="load_mdl_atch(3)">Pt. Docs</a></li>													
												</ul>
											</div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="pd10">
										<div class="col-sm-12">
											<div class="form-group">
												<div class="row">
                                                    <?php
                                                    $col2='col-sm-10';
                                                    if($slectStr!=''){
                                                        $col2='col-sm-4';
                                                        $col3='col-sm-1';
                                                        $col4='col-sm-5';
                                                    }
                                                    ?>
													<div class="col-sm-2">
														<label for="to_email">To</label>
													</div>
													<div class="<?php echo $col2; ?>">
														<input type="text" name="to_email" id="to_email" value="" class="form-control"/>
														<input type="hidden" name="from_email_send" id="from_email_send" value="" class="form-control"/>
													</div>
                                                    <?php if($slectStr!=''){ ?>
                                                        <div class="<?php echo $col3; ?>">
                                                            <label for="from_email">&nbsp;&nbsp;From</label>
                                                        </div>
                                                        <div class="<?php echo $col4; ?>">
                                                            <?php echo $slectStr; ?>
                                                        </div>
                                                    <?php } ?>
												</div>
											</div>
										</div>

										<div class="col-sm-12">
											<div class="form-group">
												<div class="row">
													<div class="col-sm-2">
														<label for="txt_patient_name">
															Patient
														</label>
													</div>
													<div class="col-sm-10">
														<div class="row">
															<!-- Pt. Search -->
															<div class="col-sm-4">
																<input type="hidden" name="patientId" id="patientId" value="<?php print $patientId; ?>" />
																<div class="input-group">

																	<input type="text" id="txt_patient_name" name="txt_patient_name" onKeyPress="{if (event.keyCode==13)return searchPatient()}" value="<?php print $patName; ?>" class="form-control" onBlur="chk_patient(this);searchPatient(this)" onChange="$('#tdPatDOS').html('');"/>
																</div>
															</div>
															<div class="col-sm-3">
																<select name="txt_findBy" id="txt_findBy" onChange="searchPatient2(this)" onkeypress="{if (event.keyCode==13)return searchPatient()}" class="form-control minimal">
																	<option value="Active">Active</option>
																	<option value="Inactive">Inactive</option>
																	<option value="Deceased">Deceased</option>
																	<option value="Resp.LN">Resp.LN</option>
																	<option value="Ins.Policy">Ins.Policy</option>
																	<?php print $searchOption; ?>
																</select>
															</div>
															<div class="col-sm-5 text-center">
																<button class="btn btn-success btn-sm" type="button" onclick="searchPatient();">Search</button>
																<button class="btn btn-success btn-sm" type="button" onclick="getPatDOS();">Get DOS</button>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>

										<!-- Pt. DOS Block -->
										<div id="tdPatDOS" class="col-sm-12"></div>

										<div class="col-sm-12">
											<div class="form-group">
												<div class="row">
													<div class="col-sm-2">
														<label for="subject">Subject</label>
													</div>
													<div class="col-sm-10">
														<input type="text" id="subject" name="subject" maxlength="150" value="" class="form-control"/>
													</div>
												</div>
											</div>
										</div>

										<div class="col-sm-12">
											<div class="form-group">
												<div class="row">
													<div class="col-sm-2">
														<label for="body">Message</label>
													</div>
													<div class="col-sm-10">
														<textarea id="body" name="body" class="form-control" rows="5"></textarea>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>

						</div>

						<!-- Pt. Info block -->
						<div id="divPtDemo" class="col-sm-6">
							<div class="pdl_10">
								<div class="adminbox">
									<div class="head">
										<span>Patient Details</span>
									</div>
									<div class="row">
										<div class="pd10">
											<!-- Pt. Detail Block-->
											<div id="pat_details_td" class="col-sm-12"></div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<input type="hidden" name="sync_type"  id="sync_type"  value="send_mail" />
					<input type="hidden" name="reply_of" id="reply_of" />
				</form>
			</div>
			<div id="module_buttons" class="ad_modal_footer modal-footer ">
				<div class="row">
					<div id="divBtnCont" class="col-sm-12 text-center">
						<button type="button" class="btn btn-success" onClick="return check_patient_msg_frm();">Send Message</button>
						<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Attachment CCDA Modal -->
<div id="div_popup" class="modal" role="dialog">
	<div class="modal-dialog" style="width:84%;">
		<div class="modal-content">
			<div class="modal-header bg-primary">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Select options to exclude</h4>
			</div>
			<div id="div_ccda_container" class="modal-body">
				<form id="ccda_opt_form" name="ccda_opt_form">
				<div class="row">
					<div class="col-sm-5">
						<div class="adminbox">
							<div class="head">
								<span>Common MU Data Set</span>
							</div>
							<div class="pt5 pdl_10">
								<div class="row">
									<div class="col-sm-4">
										<div class="checkbox checkbox-inline">
											<input type="checkbox" title="Medications" id="ccdDocumentOptions_med" name="ccdDocumentOptions[]" value="mu_data_set_medications">
											<label for="ccdDocumentOptions_med">Medications</label>
										</div>
									</div>
									<div class="col-sm-4">
										<div class="checkbox checkbox-inline">
											<input type="checkbox" title="Allergies" id="ccdDocumentOptions_allergy" name="ccdDocumentOptions[]" value="mu_data_set_allergies">
											<label for="ccdDocumentOptions_allergy">Allergies List</label>
										</div>
									</div>
									<div class="col-sm-4">
										<div class="checkbox checkbox-inline">
											<input type="checkbox" title="Problem List" id="ccdDocumentOptions_prob_list" name="ccdDocumentOptions[]" value="mu_data_set_problem_list">
											<label for="ccdDocumentOptions_prob_list">Problem List</label>
										</div>
									</div>
									<div class="col-sm-4">
										<div class="checkbox checkbox-inline">
											<input type="checkbox" title="Smoking Status" id="ccdDocumentOptions_smoke_stat" name="ccdDocumentOptions[]" value="mu_data_set_smoking">
											<label for="ccdDocumentOptions_smoke_stat">Smoking Status</label>
										</div>
									</div>
									<div class="col-sm-4">
										<div class="checkbox checkbox-inline">
											<input type="checkbox" title="Care Plan Field" id="ccdDocumentOptions_cpf" name="ccdDocumentOptions[]" value="mu_data_set_ap">
											<label for="ccdDocumentOptions_cpf">Care Plan Field</label>
										</div>
									</div>
									<div class="col-sm-4">
										<div class="checkbox checkbox-inline">
											<input type="checkbox" title="Procedures" id="ccdDocumentOptions_proc" name="ccdDocumentOptions[]" value="mu_data_set_superbill">
											<label for="ccdDocumentOptions_proc">Procedures</label>
										</div>
									</div>
									<div class="col-sm-4">
										<div class="checkbox checkbox-inline">
											<input type="checkbox" id="ccdDocumentOptions_vital_sg" title="Vital Sign" name="ccdDocumentOptions[]" value="mu_data_set_vs" >
											<label for="ccdDocumentOptions_vital_sg">Vital Sign</label>
										</div>
									</div>
									<div class="col-sm-4">
										<div class="checkbox checkbox-inline">
											<input type="checkbox" id="ccdDocumentOptions_ctm" title="Care Team Members" name="ccdDocumentOptions[]" value="mu_data_set_care_team_members">
											<label for="ccdDocumentOptions_ctm">Care Team Members</label>
										</div>
									</div>
									<div class="col-sm-4">
										<div class="checkbox checkbox-inline">
											<input type="checkbox" id="ccdDocumentOptions_lab" title="Lab" name="ccdDocumentOptions[]" value="mu_data_set_lab"  class="mudata">
											<label for="ccdDocumentOptions_lab">Lab</label>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="col-sm-7">
						<div class="pdl_10">
							<div class="adminbox">
								<div class="head">
									<span>Other</span>
								</div>
								<div class="pt5 pdl_10">
									<div class="row">
										<div class="col-sm-4">
											<div class="checkbox checkbox-inline">
												<input type="checkbox" name="ccdDocumentOptions[]" id="ccdDocumentOptions_prov_info" value="provider_info" />
												<label for="ccdDocumentOptions_prov_info">Provider's Information</label>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="checkbox checkbox-inline">
												<input type="checkbox" name="ccdDocumentOptions[]" id="ccdDocumentOptions_loc_info" value="location_info" />
												<label for="ccdDocumentOptions_loc_info">Date and Location of visit</label>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="checkbox checkbox-inline">
												<input type="checkbox" name="ccdDocumentOptions[]" id="ccdDocumentOptions_res_visit"  value="reason_for_visit" />
												<label for="ccdDocumentOptions_res_visit">Reason for visit</label>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="checkbox checkbox-inline">
												<input type="checkbox" name="ccdDocumentOptions[]" id="ccdDocumentOptions_diag_test"  value="diagnostic_tests_pending" />
												<label for="ccdDocumentOptions_diag_test">Diagnostioc Tests Pending</label>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="checkbox checkbox-inline">
												<input type="checkbox" name="ccdDocumentOptions[]" id="ccdDocumentOptions_clinic_inst"  value="clinical_instruc" />
												<label for="ccdDocumentOptions_clinic_inst">Clinical Instructions</label>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="checkbox checkbox-inline">
												<input type="checkbox" name="ccdDocumentOptions[]" id="ccdDocumentOptions_fut_appt" value="future_appointment" />
												<label for="ccdDocumentOptions_fut_appt">Future Appointments</label>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="checkbox checkbox-inline">
												<input type="checkbox" name="ccdDocumentOptions[]" id="ccdDocumentOptions_ref_others" value="provider_referrals" />
												<label for="ccdDocumentOptions_ref_others">Referrals to other providers</label>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="checkbox checkbox-inline">
												<input type="checkbox" name="ccdDocumentOptions[]" id="ccdDocumentOptions_fut_sch_test"  value="future_sch_test" />
												<label for="ccdDocumentOptions_fut_sch_test">Future Scheduled Tests</label>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="checkbox checkbox-inline">
												<input type="checkbox" name="ccdDocumentOptions[]" id="ccdDocumentOptions_rec_pt_dec"  value="recommended_patient_decision_aids" />
												<label for="ccdDocumentOptions_rec_pt_dec">Recommended patient decision aids</label>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="checkbox checkbox-inline">
												<input type="checkbox" name="ccdDocumentOptions[]" id="ccdDocumentOptions_imm_visit"  value="visit_medication_immu" />
												<label for="ccdDocumentOptions_imm_visit">Immunizations and Medications during visit</label>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				</form>
			</div>
			<div id="module_buttons" class="ad_modal_footer modal-footer ">
				<div class="row">
					<div class="col-sm-12 text-center">
						<button type="button" class="btn btn-success" onClick="create_ccda();">Done</button>
						<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>

	function toggle_attach_modal(obj){
		var obj = $(obj);
		var data_attach = obj.data();
		if(data_attach.attach != ''){
			if($('#direct_attachment_type').length){
				$('#direct_attachment_type').val(data_attach.attach);
			}
            create_ccda();
		}

		//$('#div_popup').modal('show');
		return false;
	}

</script>
