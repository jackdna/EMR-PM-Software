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
require_once(dirname(__FILE__) . '/../../config/globals.php');
require_once($GLOBALS['fileroot'] . '/library/classes/msgConsole.php');

//error_reporting(E_ALL & ~E_NOTICE);
//ini_set("display_errors",1);

//Delete Checkbox html
function getDelChkBox($val = '', $postVal = 'chk_record[]', $identifier = ''){
    $returnStr = '';
    if(empty($val)) return $returnStr;

    if(empty($identifier)) $identifier = 'message_urgent'.$val;

    $delChkHtml = '
    <div class="checkbox pull-left" style="margin-top: -4px !important;">
        <input type="checkbox" name="{POST_VAL}" id="{ID}" value="{VALUE}" class="chk_record">
        <label for="{ID}"></label>
    </div>';

    $returnStr = str_replace(array('{POST_VAL}', '{ID}', '{VALUE}'), array($postVal, $identifier, $val), $delChkHtml);

    return $returnStr;

}

//
function add_pt_msg_into_usr_msgs($pt_msg_id){
  $sql = "SELECT msg_subject,receiver_id,msg_data,sender_id,
                  msg_date_time,message_urgent,delivery_date,
                  replied_id
          FROM patient_messages WHERE pt_msg_id='".$pt_msg_id."' ";
  $rw = sqlQuery($sql);
  if($rw!=false){
    $ptid = empty($rw["replied_id"]) ?  $rw["sender_id"] : $rw["receiver_id"];
    $usr_id = empty($rw["replied_id"]) ?  $rw["receiver_id"] : $rw["sender_id"];
    $sql = "INSERT INTO user_messages
                (message_subject, message_to, message_text,
                message_sender_id, message_send_date, message_urgent,
                patientId, Pt_Communication, delivery_date, pt_msg_id) VALUES (
                  '".sqlEscStr($rw["msg_subject"])."','".sqlEscStr($usr_id)."','".sqlEscStr($rw["msg_data"])."',
                  '".sqlEscStr($usr_id)."','".sqlEscStr($rw["msg_date_time"])."','".sqlEscStr($rw["message_urgent"])."',
                  '".sqlEscStr($ptid)."','1','".sqlEscStr($rw["delivery_date"])."','".$pt_msg_id."'
                )
                ";
    $row = sqlQuery($sql);

    //Update Pt messages
    $sql = "UPDATE patient_messages SET in_pt_comm='1' WHERE pt_msg_id='".$pt_msg_id."'  ";
    sqlQuery($sql);
  }
}

function get_pt_msg_attach($pt_msg_id, $flgArr=0){
  $pt_msg_id = trim($pt_msg_id);
  if(empty($pt_msg_id)){return "";}

  $oSaveFile = new SaveFile($_SESSION["authId"],1,"users");
  $dir_path=$GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH');
  $attach_btn_str = !empty($flgArr) ? array() : '';
  $sql = "SELECT * FROM patient_messages_attachment WHERE patient_messages_id='".$pt_msg_id."' AND del_by='0' ";

  $rez = sqlStatement($sql);
  for($i=1; $attachment_val = sqlFetchArray($rez); $i++){
      if(!empty($flgArr)){
  				//
  				$file_pointer_full = ""; $file_name_full= "";
  				if(isset($attachment_val['complete_path']) && !empty($attachment_val['complete_path'])){
  					$file_pointer_full = $oSaveFile->getFilePath($attachment_val['complete_path'],'i');
  					if(!file_exists($file_pointer_full)){	$file_pointer_full="";}
  					else{ $file_name_full = basename($file_pointer_full); }
  				}

  				$arrAttachment_tmp = array(
  					"complete_path"=>$file_pointer_full,
  					"mime"=> $attachment_val['mime'],
  					"file_name"=>$file_name_full,
  					"size"=>$attachment_val['size'],
  					"file_path"=>$attachment_val['complete_path']
  				);
  				$attach_btn_str[] = $arrAttachment_tmp;

      }else{
        $attach_file_path 	= $dir_path.'/users'.$attachment_val['complete_path'];
  			$attach_file_key 	= get_checksum_key_val($attach_file_path);
  			$download_tooltip 	= show_tooltip('Download','top');
  			$attachment_mime	= $attachment_val['mime'];
  			$attachment_extension= substr($attachment_val['complete_path'],-4);
  			$sha_key_button 	= '';
  			if(empty($attach_file_key) == false){
  				$sha_key_button = "<button type='button' class='btn btn-success btn-info' data-toggle='tooltip' title='".$attach_file_key."' data-trigger='focus' data-placement='top'>SHA2 Key</button>";
  			}

        //limit Name
        $tmp = basename($attachment_val['complete_path']);
        $tmp_show = substr($tmp, -20);

  			$attach_btn_str .= "
  				<div class='col-sm-4'>
  					<div class='btn-group'>
  						<button type='button' class='btn btn-success' data-toggle='tooltip' data-html='true' data-placement='top' title='Download ".$tmp."' onclick='download_ccda(\"".$attachment_val['complete_path']."\");'>".$tmp_show."</button>";
  			if(strtolower($attachment_mime)=='application/xml' || strtolower($attachment_mime)=='text/xml' || strtolower($attachment_extension)=='.xml' || strtolower($attachment_extension)=='.zip'){
  				$attach_btn_str .= "
  						<button type='button' class='btn btn-success purple_button' onclick='view_ccda(\"".$attachment_val['complete_path']."\",\"".$rq_data["id"]."\")'>View</button>".$sha_key_button;
  			}else if(!in_array(strtolower($attachment_extension),array('.doc','docx','.xls','xlsx'))){
  				$attach_btn_str .= "
  						<button type='button' class='btn btn-success purple_button' onclick='view_attachment(\"".$attachment_val['complete_path']."\",\"".$attachment_val["id"]."\",\"".$rq_data["id"]."\", \"pt_msg\")'>View</button>".$sha_key_button;
  			}
  			$attach_btn_str .= "
  					</div>
  				</div>";
      }
  }
  return $attach_btn_str;
}

function get_pt_docs_inf($pid,$flg,$op="1",$ar_ids=array()){

  $patient_id = $pid;

  $ar_pt_docs = array();  
  if($flg=="3"){
    //Test Manager
    include_once($GLOBALS['fileroot'] . "/library/classes/class.tests.php");
    $oTests = new Tests();
    $ar_pt_tests = $oTests->get_patient_saved_tests($patient_id);

    if(count($ar_pt_tests)>0){
      foreach($ar_pt_tests as $k => $ar_test){
        if(count($ar_test)>0){
          $tmp_ar_tst_rs = $ar_test["test_rs"];
          $tst_table = $ar_test["test_table"];
          $tst_type = $ar_test["test_type"];
          $tstNm = $ar_test["temp_name"];
          if(count($tmp_ar_tst_rs)>0){
            foreach($tmp_ar_tst_rs as $tst_type_id => $otest){
              $tstid = $otest["tId"];
              if(!empty($tstid) && !empty($tst_table)){
                $ar_tst_docs = $oTests->get_test_images($patient_id,$tst_table,$tstid);
                if(count($ar_tst_docs)>0){
                  foreach($ar_tst_docs as $k1 => $oDoc){
                    $tmp = ucwords($otest["dt"]." - ".$oDoc["fileName"]); //" - ".get_date_format($row["date_time"])." ".date("g:i A",strtotime($row["date_time"])
                    $ar_pt_docs[$tstNm][] = array($oDoc["scan_id"], $tmp, "el_pt_tst");
                  }
                }
              }
            }
          }
        }
      }
    }

  }else{
    //Pt Instrucktions
    $qrynew1 = "SELECT dp.id,dp.name,dp.date_time,dp.form_id,dp.operator_id,dp.scan_id,dp.doc_id,
  								concat(u.lname,', ',u.fname,' ',u.mname) AS operator_name
  								FROM document_patient_rel dp
  								LEFT JOIN users u ON(u.id = dp.operator_id)
  								WHERE dp.p_id='$patient_id'
  								AND dp.doc_id!='0'
  								AND dp.status = 0
  								ORDER BY dp.date_time DESC" ;
    $initPtDocRes = imw_query($qrynew1);
    if($initPtDocRes && imw_num_rows($initPtDocRes)>0){
      while($row = imw_fetch_assoc($initPtDocRes)){
        if($op=="1"){
          $tmp = ucwords($row["name"])." - ".get_date_format($row["date_time"])." ".date("g:i A",strtotime($row["date_time"]));
        	$ar_pt_docs["Patient Instruction Documents"][] = array($row["id"], $tmp, "el_pt_ins_docs");
        }
      }
    }

    //PT. DOCS - ptdocs
    $tmp_phrs_id="";
    if(isset($ar_ids["Saved DOCS"]) && count($ar_ids["Saved DOCS"])>0){
        $tmp_phrs_id=implode(",", $ar_ids["Saved DOCS"]);
        $tmp_phrs_id=" AND pd.pt_docs_patient_templates_id IN (".$tmp_phrs_id.") ";
    }
    $initPtDocQry = "select pd.pt_docs_patient_templates_id , pdt.pt_docs_template_name, pd.created_date,
                    pd.template_content
              FROM pt_docs_patient_templates pd
    					JOIN pt_docs_template pdt ON(pdt.pt_docs_template_id = pd.pt_doc_primary_template_id)
    					LEFT JOIN users u ON(u.id = pd.operator_id)
    					WHERE pd.patient_id = '".$patient_id."' '.$tmp_phrs_id.'
    					AND pd.delete_status = '0'
    					ORDER BY pd.pt_docs_patient_templates_id DESC";
    $initPtDocRes = imw_query($initPtDocQry);
    if($initPtDocRes && imw_num_rows($initPtDocRes)>0){
      while($row = imw_fetch_assoc($initPtDocRes)){
        if($op=="1"){
          $tmp = $row["pt_docs_template_name"]." - ".get_date_format($row["created_date"]);
        	$ar_pt_docs["Saved DOCS"][] = array($row["pt_docs_patient_templates_id"], $tmp, "el_pt_docs");
        }
      }
    }

    //PT. DOCS - collection letter
    $tmp_phrs_id="";
    if(isset($ar_ids["Collection letter"]) && count($ar_ids["Collection letter"])>0){
        $tmp_phrs_id=implode(",", $ar_ids["Collection letter"]);
        $tmp_phrs_id=" AND pd.id IN (".$tmp_phrs_id.") ";
    }
    $initPtDocCollectionLetterQry="SELECT pd.id, pd.created_date, clt.collection_name FROM  pt_docs_collection_letters pd
    								JOIN collection_letter_template clt ON (clt.id= pd.template_id)
    								LEFT JOIN users u ON(u.id = pd.operator_id)
    								WHERE pd.patient_id='".$patient_id."' '.$tmp_phrs_id.'
    								AND pd.delete_status = '0'
    								ORDER BY pd.created_date DESC";
    $initPtDocCollectionLetterRes=imw_query($initPtDocCollectionLetterQry);
    if($initPtDocCollectionLetterRes && imw_num_rows($initPtDocCollectionLetterRes)>0){
      while($row = imw_fetch_assoc($initPtDocCollectionLetterRes)){
        if($op=="1"){
          $tmp = $row["collection_name"]." - ".get_date_format($row["created_date"]);
      	  $ar_pt_docs["Collection letter"][] = array($row["id"], $tmp,  "el_col_ltrs");
        }
      }
    }

    //PT. DOCS - Insurance Cards
    $tmp_phrs_id="";
    if(isset($ar_ids["Insurance Cards"]) && count($ar_ids["Insurance Cards"])>0){
        $tmp_phrs_id=implode(",", $ar_ids["Insurance Cards"]);
        $tmp_phrs_id=" AND ins.id IN (".$tmp_phrs_id.") ";
    }
    $initPtDocInsQry="SELECT ins.id,ins.scan_card,ins.scan_label,ins.type, ins.ins_caseid, inct.case_name FROM insurance_data ins
    								JOIN insurance_case inc ON (ins.ins_caseid= inc.ins_caseid and inc.del_status = 0  )
                    JOIN insurance_case_types inct ON (inct.case_id= inc.ins_case_type )
    								WHERE ins.pid=".$patient_id." ".$tmp_phrs_id."
    								AND (ins.scan_card <> '' OR ins.scan_card2 <> '')
    								ORDER BY inc.ins_caseid Desc";
    $initPtDocInsRes=imw_query($initPtDocInsQry);
    if($initPtDocInsRes && imw_num_rows($initPtDocInsRes)>0){
      while($row = imw_fetch_assoc($initPtDocInsRes)){
        if($op=="1"){
          $tmp = $row["scan_label"];
          if(empty($tmp)){
            $tmp = $row["case_name"]."-".$row["ins_caseid"]."-".$row["type"];
          }
        	$ar_pt_docs["Insurance Cards"][] = array($row["id"], $tmp, "el_ins_data");
        }
      }
    }

    //PT. DOCS - Interpretation
    $tmp_phrs_id="";
    if(isset($ar_ids["Interpretation"]) && count($ar_ids["Interpretation"])>0){
        $tmp_phrs_id=implode(",", $ar_ids["Interpretation"]);
        $tmp_phrs_id=" AND c2.id IN (".$tmp_phrs_id.") ";
    }
    $initPtDocInsQry= "SELECT
            c1.patient_id,c1.form_id,c1.exam_name,
            c2.id,
            c2.order_by,
            c2.order_on,
            c2.test_type,
            c2.assessment,
            c2.dx, c2.dxid,
            c2.plan,
            c3.drawing_image_path, c4.date_of_service
              FROM chart_drawings c1
                    INNER JOIN chart_draw_inter_report c2 ON c1.id = c2.id_chart_draw
                    INNER JOIN ".constant("IMEDIC_SCAN_DB").".idoc_drawing c3 ON c3.id = c1.idoc_drawing_id
                    INNER JOIN chart_master_table c4 ON c4.id = c1.form_id
                    WHERE c1.patient_id = '".$patient_id."' AND c1.purged='0' AND c4.purge_status='0' AND c4.delete_status='0' ".$tmp_phrs_id."
                    AND c1.exam_name='FundusExam' AND c2.del_by='0'
                    ORDER BY c2.order_on DESC";
    $initPtDocInsRes=imw_query($initPtDocInsQry);
    if($initPtDocInsRes && imw_num_rows($initPtDocInsRes)>0){
      while($row = imw_fetch_assoc($initPtDocInsRes)){
        if($op=="1"){
          $tmp = get_date_format($row["date_of_service"]);
          $ar_pt_docs["Interpretation"][] = array($row["id"]."_".$row["form_id"], $tmp." - ".$row["assessment"],"el_drw_int_rep");
        }
      }
    }

    //PT. DOCS - Patient Orders
    $tmp_phrs_id="";
    if(isset($ar_ids["Patient Orders"]) && count($ar_ids["Patient Orders"])>0){
        $tmp_phrs_id=implode(",", $ar_ids["Patient Orders"]);
        $tmp_phrs_id=" AND po.print_orders_data_id IN (".$tmp_phrs_id.") ";
    }
    $initPtDocPtOrderQry ="SELECT po.print_orders_data_id, po.created_date FROM print_orders_data po
    						LEFT JOIN users u ON(u.id = po.created_by)
    						WHERE po.patient_id = '".$patient_id."' ".$tmp_phrs_id."
    						AND po.delete_status = '0'";
    $initPtDocPtOrderRes=imw_query($initPtDocPtOrderQry);
    if($initPtDocPtOrderRes && imw_num_rows($initPtDocPtOrderRes)>0){
      $tmp=1;
      while($row = imw_fetch_assoc($initPtDocPtOrderRes)){
        if($op=="1"){
          $ar_pt_docs["Patient Orders"][] = array($row["print_orders_data_id"], $row["created_date"]." - Order ".$tmp++,"el_pt_ordr_dt");
        }
      }
    }
  }
  if($op==1){
    return $ar_pt_docs;
  }
}

function get_mdl_list_pt_docs($flg){

  $patient_id = trim($_REQUEST["patientId"]);

  $ar_pt_docs = get_pt_docs_inf($patient_id,$flg,"1");

  //echo "<pre>";
  //print_r($ar_pt_docs);
  //echo "</pre>";

  //
  $data_html="";
  if(count($ar_pt_docs)>0){
    $tmpC=1;
    $chkCntr=0;

    $cntrTst=0;
    foreach($ar_pt_docs as $k => $arV){
      $title_pt_docs = $k;
      $tmp_ln = count($arV);
      if($tmp_ln>0){
        $tmp="";

        if($chkCntr<$tmp_ln){ $chkCntr=$tmp_ln; }

        foreach($arV as $kv => $vNm){
          $tid = $vNm[0];
          $tnm = $vNm[1];
          $tinpnm = $vNm[2];
          if(!empty($tnm)){
            $tmpC = $kv;

            //
            if($tinpnm == "el_pt_tst"){ $tmpC=$cntrTst; $cntrTst++; }

            $tmp.='<li class="list-group-item">
                    <div class="checkbox"><input type="checkbox" id="'.$tinpnm.$tmpC.'" name="'.$tinpnm.$tmpC.'" value="'.$tid.'" ><label for="'.$tinpnm.$tmpC.'">'.$tnm.'</label></div>
                    </li>';
          }
        }

        if(!empty($tmp)){

          $tmp = '<ul class="list-group">'.$tmp.'</ul>';
          $data_html.='<div class="panel panel-default">
            <div class="panel-heading">
              <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#acrdnPtDocs" href="#clspPtDocs'.$tmpC.'">
                '.$title_pt_docs.'
                </a>
              </h4>
            </div>
            <div id="clspPtDocs'.$tmpC.'" class="panel-collapse collapse">
              <div class="panel-body">'.$tmp.'</div>
            </div>
          </div>';
          $tmpC++;
        }
      }
    }

    //
    if($chkCntr<$cntrTst){ $chkCntr=$cntrTst; }

    //
    if(!empty($data_html)){
        $data_html='<div class="panel-group" id="acrdnPtDocs"><input type="hidden" name="el_chkCntr" value="'.$chkCntr.'">'.$data_html.'</div>';
    }
  }

  if(empty($data_html)){
    $data_html="No Record found.";
  }

  //
  $lbl = $_REQUEST["flg"] == 1 ? "Pt. Docs" : "Pt. Instruction Documents";
  $lbl = "";

  //Modal
  //<!-- Modal UPLOAD --->
  $str = '<div id="attchPtDocModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
  	<!-- Modal content-->
  	<div class="modal-content">
  		<div class="modal-header">
  			<button type="button" class="close" data-dismiss="modal">&times;</button>
  			<h4 class="modal-title">Attach'.$lbl.' file(s) with message</h4>
  		</div>
  		<div class="modal-body">
  			<div class="row">
  				<div id="dvptattch" >
          <form id="frm_pt_docs_atch">
          '.$data_html.'
          </form>
          </div>
  			</div>
  		</div>
  		<div class="modal-footer">
          <button type="button" class="btn btn-success" onClick="attch_pt_files()">Save Attachments</button>
          <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
  		</div>
  	</div>
  </div>
  </div>';

  echo $str;
}

function get_ptdoc_atch(){
  require_once($GLOBALS['fileroot'] . '/library/html_to_pdf/createPdf.inc.php');
  require_once($GLOBALS['fileroot'] . '/interface/chart_notes/scan_docs/load_pt_docs_inc.php');

  //print_r()
  $ar_atch_files=array();
  $c=0;
  $chkCntr = $_POST["el_chkCntr"];
  while(true){

    if($chkCntr<$c || $c>1000 ){ break; }

    $el_col_ltrs = $_POST["el_col_ltrs".$c];
    $el_pt_ordr_dt= $_POST["el_pt_ordr_dt".$c];
    $el_drw_int_rep= $_POST["el_drw_int_rep".$c];
    $el_ins_data= $_POST["el_ins_data".$c];
    $el_pt_docs= $_POST["el_pt_docs".$c];
    $el_pt_ins_docs= $_POST["el_pt_ins_docs".$c];
    $el_pt_tst= $_POST["el_pt_tst".$c];

    $des_pth = "pt_msg_mails";
    //Pt Test Managers
    if(!empty($el_pt_tst)){
      $ar_atch_files[] = create_html_file_4pdf($_REQUEST["patientId"], $mode="PtTstMngr", $el_pt_tst, $type="PtTstMngr", $des_pth  );
    }

    //PT. Instructions DOCS - ptInsDocs
    if(!empty($el_pt_ins_docs)){
      $ar_atch_files[] = create_html_file_4pdf($_REQUEST["patientId"], $mode="print", $el_pt_ins_docs, $type="PtInstructionsDocs", $des_pth  );
    }

    //PT. DOCS - ptdocs
    if(!empty($el_pt_docs)){
      $ar_atch_files[] = create_html_file_4pdf($_REQUEST["patientId"], $mode="print", $el_pt_docs, $type="", $des_pth  );
    }
    //PT. DOCS - collection letter
    if(!empty($el_col_ltrs)){
      $ar_atch_files[] = create_html_file_4pdf($_REQUEST["patientId"], $mode="print", $el_col_ltrs, $type="collection" , $des_pth );
    }
    //PT. DOCS - Insurance Cards
    if(!empty($el_ins_data)){
      $ar_atch_files[] = create_html_file_4pdf($_REQUEST["patientId"], $mode="ins", $el_ins_data, $type="", $des_pth );
    }
    //PT. DOCS - Interpretation
    if(!empty($el_drw_int_rep)){
      $ar_atch_files[] = create_html_file_4pdf($_REQUEST["patientId"], $mode="intrprttns", $el_drw_int_rep, $type="", $des_pth );
    }
    //PT. DOCS - Patient Orders
    if(!empty($el_pt_ordr_dt)){
      $ar_atch_files[] = create_html_file_4pdf($_REQUEST["patientId"], $mode="print", $el_pt_ordr_dt, $type="pt_orders", $des_pth );
    }

    $c++;
  }

  // Get file info
  $ar_file_info = array();
  $atch_lm = 3000000;$cntr_lm = 0;
  if(count($ar_atch_files) > 0){
    foreach($ar_atch_files as $k => $arfile_path){
      if(file_exists($arfile_path[0])){
        $tfile_pth = $arfile_path[0];
        $tsize = filesize($tfile_pth);
        $cntr_lm = $cntr_lm + $tsize;
        $arrAttachment_tmp = array(
					"complete_path"=>$tfile_pth,
					"mime"=> mime_content_type($tfile_pth),
					"file_name"=>basename($tfile_pth),
					"size"=>$tsize,
					"file_path"=>$arfile_path[1]
				);
        $ar_file_info[] = $arrAttachment_tmp;
      }
    }
  }

  if($cntr_lm>$atch_lm){
    //remove files
    foreach($ar_atch_files as $k => $arfile_path){
      if(file_exists($arfile_path[0])){
        unlink($arfile_path[0]);
      }
    }

    $ar_file_info["error"] = "Attachments exceeds the limit! Please attach than 3MB.";
  }

  echo json_encode($ar_file_info);
}

//Upload modal
if(isset($_GET["get_mdl_list_pt_docs"]) && $_GET["get_mdl_list_pt_docs"]==1){
  get_mdl_list_pt_docs($_GET["flg"]);
  exit();
}

//Attach Pt files
if(isset($_POST["attch_pt_files"]) && $_POST["attch_pt_files"]==1){
  get_ptdoc_atch();
  exit();
}

if ($_REQUEST['sync_type'] == "send_mail") {
    $patientId = trim($_REQUEST["patientId"]);
    $pt_msg_id = $_REQUEST["reply_of"];
    if ($patientId != "") {
        $curr_pt_arr = core_get_patient_name($patientId);
        $patName = $curr_pt_arr['2'] . ', ' . $curr_pt_arr['1'];
    } else {
        $patientId = isset($_SESSION['patient']) ? intval($_SESSION['patient']) : '';
    }

    //add uploaded files --
    $ar_attch_files = array();
		$attchd_files = $_POST["attchd_files"];
		$ar_attchd_files = json_decode($attchd_files, true);
		if(count($ar_attchd_files)>0){

			$oSaveFile = new SaveFile($_SESSION["authId"],1,"users");
			foreach($ar_attchd_files as $k => $o_attchd_files){
				//
				$file_pointer_full = ""; $file_name_full= "";
				if(isset($o_attchd_files['curfile']) && !empty($o_attchd_files['curfile'])){
					$file_pointer_full = $oSaveFile->getFilePath($o_attchd_files['curfile'],'i');
					if(!file_exists($file_pointer_full)){	$file_pointer_full="";}
					else{ $file_name_full = basename($file_pointer_full); }
				}

				$arrAttachment_tmp = array(
					"complete_path"=>$file_pointer_full,
					"mime"=> $o_attchd_files['type'],
					"file_name"=>$file_name_full,
					"size"=>$o_attchd_files['size'],
					"file_path"=>$o_attchd_files['curfile']
				);
				$ar_attch_files[] = $arrAttachment_tmp;
			}
		}
		//add uploaded files --

    //Pt Docs files
    $attchd_files_pt_docs = $_POST["attchd_files_pt_docs"];
		$tmp_attchd_files = json_decode($attchd_files_pt_docs, true);
    if(count($tmp_attchd_files)>0){
        $ar_attch_files = array_merge($ar_attch_files, $tmp_attchd_files);
    }

    //
    $qry_part = "";
    $message_urgent = isset($_REQUEST["message_urgent"]) ? 1 : 0;
    $msg_subject = $_REQUEST["subject"];
    $msg_data = $_REQUEST["body"];
    $msg_erp_ext_id = '';
	if ($pt_msg_id != "") {

    if(!empty($_REQUEST["forwardType"])){
      $ar_tmp_atch = get_pt_msg_attach($pt_msg_id, 1);
      $ar_attch_files = array_merge($ar_attch_files, $ar_tmp_atch);
    }

		$qry_part = ", replied_id = " . $pt_msg_id . " ";
        $replied_qry = "SELECT *, DATE_FORMAT(msg_date_time,'" . get_sql_date_format() . " %h:%i %p') AS msg_date_time FROM patient_messages WHERE pt_msg_id = '" . $pt_msg_id . "'";
        $replied_qry_obj = imw_query($replied_qry);
        $replied_qry_data = imw_fetch_assoc($replied_qry_obj);
        $msg_erp_ext_id = $replied_qry_data["iportal_msg_id"];
		//$curr_pt_arr = core_get_patient_name($replied_qry_data["sender_id"]);
        //$name_sendTo = $curr_pt_arr['2'] . ', ' . $curr_pt_arr['1'];
		$name_sendTo = getUserFirstName($replied_qry_data["sender_id"],1);
		$ORsenderName = "Patient Co-ordinator";
        $sentDate = $replied_qry_data["msg_date_time"];
        $originalSubject = $replied_qry_data["msg_subject"];
        $originalTextPrefix = '

			----ORIGINAL MESSAGE----
			From: ' . $name_sendTo . '
			To: ' . $ORsenderName . '
			Sent: ' . $sentDate . '
			Subject: ' . $originalSubject . '

		';
        $originalTextPrefix .= $replied_qry_data["msg_data"];
        $msg_data = $msg_data . $originalTextPrefix;
    }
	$name_sendTo = getUserFirstName($_SESSION['authId'],$flgFull=1);
	$msg_data_portal = $msg_data;
    $msg_subject = imw_real_escape_string($msg_subject);
    $msg_data = imw_real_escape_string($msg_data);
    $req_qry = "INSERT INTO patient_messages SET receiver_id = '" . $patientId . "', sender_id = '" . $_SESSION['authId'] . "', communication_type = 1, msg_subject = '" . $msg_subject . "', msg_data = '" . $msg_data . "', message_urgent='" . $message_urgent . "'" . $qry_part;
    $req_qry_obj = imw_query($req_qry);
    $pt_msg_id_new = imw_insert_id();
    if ($req_qry_obj && $pt_msg_id != "") {
        $req_up_qry = "UPDATE patient_messages SET msg_icon = 1 WHERE pt_msg_id = " . $pt_msg_id;
        imw_query($req_up_qry);
    }

    //save attachments
    $ar_atch_ptpr=array();
    $ar_not_atch_ptpr=array();

    $atch_lm = 3000000;
    if(count($ar_attch_files)>0 && $pt_msg_id_new>0){
        foreach($ar_attch_files as $k => $o_arrMail){
          $complete_path = $o_arrMail['file_path'];
          $file_name = $o_arrMail['file_name'];
          $mime = $o_arrMail['mime'];
          $size = $o_arrMail['size'];

          if($file_name != ""){
            $sql_ins = "INSERT INTO patient_messages_attachment SET
                  patient_messages_id = '".$pt_msg_id_new."',
                  file_name = '".$file_name."',
                  size = '".$size."',
                  mime = '".$mime."',
                  complete_path = '".imw_real_escape_string($complete_path)."',
                  patient_id = '".$patientId."',
                  op_time = '".date("Y-m-d H:i:s")."'
                  ";

            imw_query($sql_ins);

            $oSaveFile = new SaveFile($_SESSION["authId"],1,"users");
            if(isset($complete_path) && !empty($complete_path)){
      				$complete_path = $oSaveFile->getFilePath($complete_path,'i');
      				if(file_exists($complete_path)){
                if($atch_lm>0 && $atch_lm>=$size){
                  $atch_lm = $atch_lm - $size;
                  $ar_atch_ptpr[]=array("fileName"=>$file_name, "fileData"=>base64_encode(file_get_contents($complete_path)));
                }else{
                  $ar_not_atch_ptpr[]=array("fileName"=>$file_name, "fileData"=>base64_encode(file_get_contents($complete_path)));
                }
      				}
            }
          }
        }
    }

    //add into user_messages
    if(!empty($_REQUEST["message_pt_comm"])){
      if(!empty($pt_msg_id)){
        add_pt_msg_into_usr_msgs($pt_msg_id);
      }
      add_pt_msg_into_usr_msgs($pt_msg_id_new);
    }

	//START CODE TO SEND MESSAGE TO PATIENT PORTAL
	$erp_error=array();
    if($pt_msg_id_new && isERPPortalEnabled()) {
		try {
			include($GLOBALS['fileroot']."/library/erp_portal/rabbitmq_exchange.php");
			$OBJRabbitmqExchange = new Rabbitmq_exchange();

			$is_rsp = false;
			$rsp_id = '';
			$qry_rsp = "SELECT id FROM resp_party WHERE patient_id = '".$patientId."' LIMIT 0, 1";
			$res_rsp = imw_query($qry_rsp);
			if(imw_num_rows($res_rsp)>0) {
				$row_rsp = imw_fetch_assoc($res_rsp);
				$rsp_id = $row_rsp['id'];
				$is_rsp = true;
			}
			$msg_priority = $message_urgent ? 'High' : 'Normal';
			$i=0;
			$pt_cur_dt = date("Y-m-d");
			$pt_cur_tm = date("H:i:s");
			$pt_msg_arr['fromSecureRecipientExternalId'] 			= $_SESSION['authId'];
			$pt_msg_arr['fromSecureRecipientName'] 					= trim($_SESSION['authProviderName']);
			$pt_msg_arr['patients'][$i]['externalId'] 				= $patientId;
			$pt_msg_arr['patients'][$i]['isRepresentative'] 		= $is_rsp;
			$pt_msg_arr['patients'][$i]['representativeExternalId'] = $rsp_id;
			$pt_msg_arr['subject'] 									= $msg_subject;
			$pt_msg_arr['body'] 									= $msg_data_portal;
			$pt_msg_arr['status'] 									= 'sent';
			$pt_msg_arr['priority'] 								= $msg_priority; //normal by default, if urgnt then high
			$pt_msg_arr['creationDate'] 							= $pt_cur_dt.'T0'.$pt_cur_tm;
			$pt_msg_arr['sentDate'] 								= $pt_cur_dt.'T0'.$pt_cur_tm;
			$pt_msg_arr['fileData']									= $ar_atch_ptpr;
			//$pt_msg_arr['fileData'][$i]['fileName'] 				= '';
			//$pt_msg_arr['fileData'][$i]['fileData'] 				= '';
			$pt_msg_arr['id'] 										= $msg_erp_ext_id; //first time blank and then response will generte id patient_messages tbl update that id - erp_portal_id
			$pt_msg_arr['externalId'] 								= $pt_msg_id_new;

			$id 													= $patientId;
			$resource 												= 'OutgoingSecureMessages';
			$method													= 'POST';


			$response_encode= $OBJRabbitmqExchange->send_request($pt_msg_arr,$id,$resource,$method);
			$response 		= json_decode($response_encode);
			if($response && $response->id && $pt_msg_id_new) {
				$qry_msg_up = "UPDATE patient_messages SET iportal_msg_id = '".$response->id."' WHERE pt_msg_id = ".$pt_msg_id_new;
				imw_query($qry_msg_up);
			}
		} catch(Exception $e) {
			$erp_error[]='Unable to connect to ERP Portal';
		}
	}
	//END CODE TO SEND MESSAGE TO PATIENT PORTAL

	$target_send_msg = "Message has been sent successfully";
    echo $target_send_msg;
    die();
}
$filter = (isset($_GET['filter']) && $_GET['filter'] != '') ? trim($_GET['filter']) : 'load_pt_msg_inbox';
$load_buttons = '<button class="btn btn-danger" onClick="del_messages(\'del_pt_messages\');">Delete</button>';
$filter_pt_id = isset($_REQUEST["filter_pt_id"]) ? trim($_REQUEST["filter_pt_id"]) : '';
$filter_pt_fac = isset($_REQUEST["filter_pt_fac"]) ? trim($_REQUEST["filter_pt_fac"]) : '';
$filter_pt_str = '';
$filter_pt_fac_str = '';
$erp_error=array();
switch ($filter) {
    case 'load_pt_msg_inbox':
        if(isERPPortalEnabled()){
			try {
				include_once($GLOBALS['fileroot']."/library/erp_portal/rabbitmq_exchange.php");
				include_once($GLOBALS['srcdir'].'/erp_portal/incomingsecuremessages.php');
				$oIncSecMsg = new IncomingSecureMessages();
				$oIncSecMsg->getMsgs();

				include_once($GLOBALS['srcdir']."/erp_portal/portal_patients.php");
				$obj_patients = new Portal_patients;
				$obj_patients->getPortalPatients();
			} catch(Exception $e) {
				$erp_error[]='Unable to connect to ERP Portal';
			}
        }

        if ($filter_pt_id != "") {
            $filter_pt_str = " and pm.sender_id = " . $filter_pt_id . " ";
        }

		    if ($filter_pt_fac != "") {
            $filter_pt_fac_str = " and pd.default_facility = " . $filter_pt_fac . " ";
        }

        $sort_by_db = $sort_by = isset($_REQUEST['sort_by']) ? $_REQUEST['sort_by'] : 'pt_msg_id';
        if ($sort_by == "sender_id")
        //$sort_by_db = "from_name";
            $sort_by_db = "msg_date_time";
        $sort_order = isset($_REQUEST['sort_order']) ? $_REQUEST['sort_order'] : 'DESC';
        // query used new message in top showing//
			  $load_inbox_qry = "SELECT pm.*, DATE_FORMAT(pm.msg_date_time,'" . get_sql_date_format() . " %h:%i %p') AS msg_date_time,
                            CONCAT(pd.lname,', ',pd.fname,' ',pd.mname) as from_name
                            FROM patient_messages pm
                            JOIN patient_data pd ON pd.id = pm.sender_id
                            WHERE pm.communication_type = 2 and pm.del_status = 0 and pm.is_done = 0 " . $filter_pt_str . " " . $filter_pt_fac_str . "

                            ORDER BY $sort_by_db $sort_order ";

        require_once($GLOBALS['fileroot'] . '/library/classes/paging.inc.php');
        $page = !isset($_REQUEST['page']) ? 1 : $_REQUEST['page'];
        $objPaging = new Paging(25, $page);
        $objPaging->sort_by = $sort_by;
        $objPaging->sort_order = $sort_order;
        $objPaging->query = $load_inbox_qry;
        $objPaging->func_name = "load_patient_messages";
        $rq_obj = $objPaging->fetchLimitedRecords();

        $tableData = '';
        /* Scrollable Data List */
        $tableData .= '<div class="col-sm-4 messuser" id="ptmessagelist">';
        /* List Messsage Count */
        $tableData .= '<div class="row msgCount">';
        $tableData .= '<div class="col-sm-10">';
        $tableData .= $objPaging->getPagingString($page);
        $tableData .= '</div>';
        $tableData .= '<div class="col-sm-2" style="padding-right:10px !important;">' . $objPaging->buildComponentR8($page, $wout_btns = true) . '</div>';


        $tableData .= '<div class="scroll-content mCustomScrollbar dynamicRightPadding" style="height:' . ($_SESSION['wn_height'] - 396) . 'px;">';
        $tableData .= '<ul class="messageList">';
        if (count($rq_obj) > 0) {
            $curr_pt_arr = core_get_patient_name($msgConsoleObj->patient_id);
            $curr_pt_name = $curr_pt_arr['2'] . ', ' . $curr_pt_arr['1'];
            if (trim($curr_pt_arr['3']) != '')
                $curr_pt_name = $curr_pt_arr['2'] . ', ' . $curr_pt_arr['1'] . ' ' . $curr_pt_arr['3'];
            $curr_pt_name .= ' - ' . $curr_pt_arr['0'];


            foreach ($rq_obj as $key => $inbox_data) {
                $curr_pt_arr = core_get_patient_name($inbox_data["sender_id"]);
                $curr_pt_name = $curr_pt_arr['2'] . ', ' . $curr_pt_arr['1'];

                $flagged_icon = $inbox_data["flagged"] == 1 ? 'flagged' : 'unflagged';
                $msgicon_tip = $unread_msg_icon = $unread_msg_bold = $urgent_icon_img = '';
                if (intval($inbox_data['message_urgent']) == 1) {
                    $urgent_icon_img = '<span class="icons_ptComm msg_status_icon icon_msg_urgent">a</span>';
                }
                $arrISRead = explode(",", $inbox_data["is_read"]);
                if (!in_array(0, $arrISRead)) {
                    $unread_msg_bold = ' ';
                    $unread_msg_icon = 'read';
                } else {
                    $unread_msg_bold = ' text12b';
                    $unread_msg_icon = 'unread';
                }
                if ($inbox_data['msg_icon'] == 1) {
                    $unread_msg_icon = 'replied';
                    $msgicon_tip = ' You replied to this message';
                }
                $res = imw_query($read_msg);
                $row = imw_fetch_assoc($res);
                $total_unread = $row['total_unread'];

				$ptData = $msgConsoleObj->get_patient_more_info($inbox_data["sender_id"]);
				$def_facility = $ptData['default_facility'];
				$pat_fac = "";
				if ($def_facility != ''){
					$rs = imw_query("SELECT facility_name FROM pos_facilityies_tbl WHERE pos_facility_id='" . $def_facility . "'");
					$row = imw_fetch_assoc($rs);
					$def_fac = trim($row['facility_name']);
					if($def_fac != ""){
						$pat_fac = $def_fac;
					} else{
						$pat_fac = "N/A";
					}
				}

				/* Scrollable Data List */
                $tableData .= '<li id="msg_' . $inbox_data["pt_msg_id"] . '" onClick="loadPtMessgDetail(' . $inbox_data["pt_msg_id"] . ',this,' . $inbox_data["sender_id"] . ',0)" class="load_message_on_click ' . $unread_msg_bold . '">';
                $tableData .= '<h2>' . $curr_pt_name . ' - ' . $inbox_data["sender_id"] . ' <span class="pull-right"><small>'.$pat_fac.'</small></span></h2>';
                $tableData .= '<div class=" clearfix"></div>';
                $tableData .= '<div class="messsub">' . $inbox_data["msg_subject"] . '</div>';
                $tableData .= '<div class="mesgdate">' . $inbox_data["msg_date_time"] . '</div>';
                $tableData .= '</li>';
            }

            /* End Scrollable Data List */
            $tableData .= '</ul></div>';
            /* Paging */
            $tableData .= '<div class="clearfix"></div><div class="row msgCount countFooter pt_paging" style="min-height:40px;">';
            $tableData .= $objPaging->buildComponentR8($page);
            $tableData .= '</div>';
            $tableData .= '</div></div>';

            $tableData .= '
				<!--Container to load the message Details-->
				<div class="col-sm-8" >
					<div class="row">
						<div class="col-sm-12" id="ptmessageData">';

            $tableData .= '</div></div></div>';
        } else {
            $tableData .= '<div class="pt5 pdl_10 mCustomScrollbar dynamicRightPadding"><div class="alert alert-danger">No Records</div></div>
			<script type="text/javascript">update_link_status(\'#message_reminders_opt\',\'unread\',\'read\');</script>';
        }
        echo $tableData;
        break;

    case 'load_pt_msg_sent':
        $sent_li =1;
        if ($filter_pt_id != "") {
            $filter_pt_str = " and pm.receiver_id = " . $filter_pt_id . " ";
        }
		if ($filter_pt_fac != "") {
            $filter_pt_fac_str = " and pd.default_facility = " . $filter_pt_fac . " ";
        }
        $sort_by_db = $sort_by = isset($_REQUEST['sort_by']) ? $_REQUEST['sort_by'] : 'pt_msg_id';
        if ($sort_by == "receiver_id")
            $sort_by_db = "to_name";
        $sort_order = isset($_REQUEST['sort_order']) ? $_REQUEST['sort_order'] : 'DESC';
        $load_inbox_qry = "SELECT pm.*, DATE_FORMAT(pm.msg_date_time,'" . get_sql_date_format() . " %h:%i %p') AS msg_date_time ,
										CONCAT(pd.lname,', ',pd.fname,' ',pd.mname) as to_name
										FROM patient_messages pm
										JOIN patient_data pd ON pd.id = pm.receiver_id
										WHERE pm.communication_type = 1 and pm.del_status = 0 and pm.is_done = 0 " . $filter_pt_str . " " . $filter_pt_fac_str . "
										ORDER BY $sort_by_db $sort_order
										";

        require_once($GLOBALS['fileroot'] . '/library/classes/paging.inc.php');
        $page = !isset($_REQUEST['page']) ? 1 : $_REQUEST['page'];
        $objPaging = new Paging(25, $page);
        $objPaging->sort_by = $sort_by;
        $objPaging->sort_order = $sort_order;
        $objPaging->query = $load_inbox_qry;
        $objPaging->func_name = "load_patient_messages";
        $objPaging->filter = "load_pt_msg_sent";
        $rq_obj = $objPaging->fetchLimitedRecords();

        //$load_inbox_qry_obj = imw_query($load_inbox_qry);
        $tableData = '';
       // if (count($rq_obj) == 0) {
       //     $tableData .= '<div class="alert alert-danger">No Records</div>';
      //  }

        /* Scrollable Data List */
        $tableData .= '<div class="col-sm-4 messuser" id="ptmessagelist">';
        /* List Messsage Count */
        $tableData .= '<div class="row msgCount">';
        $tableData .= '<div class="col-sm-10">';
        $tableData .= $objPaging->getPagingString($page);
        $tableData .= '</div>';
        $tableData .= '<div class="col-sm-2" style="padding-right:10px !important;">' . $objPaging->buildComponentR8($page, $wout_btns = true) . '</div>';


        $tableData .= '<div class="scroll-content mCustomScrollbar" style="height:' . ($_SESSION['wn_height'] - 396) . 'px;">';
        $tableData .= '<ul class="messageList">';
		 if (count($rq_obj)>0) {

        foreach ($rq_obj as $key => $inbox_data) {
            $curr_pt_arr = core_get_patient_name($inbox_data["receiver_id"]);
            $curr_pt_name = $curr_pt_arr['2'] . ', ' . $curr_pt_arr['1'];

            /* Scrollable Data List */
            $tableData .= '<li id="msg_' . $inbox_data["pt_msg_id"] . '" onclick="loadPtMessgDetail(' . $inbox_data["pt_msg_id"] . ',this,' . $inbox_data["receiver_id"] . ',1)" class="load_message_on_click ' . $unread_msg_bold . '">';

            //Setting Delete Chk box
            $chkBoxStr = '';
            $chkBoxStr = getDelChkBox($inbox_data["pt_msg_id"]);
            if(empty($chkBoxStr) == false) $tableData .= $chkBoxStr;

			$ptData = $msgConsoleObj->get_patient_more_info($inbox_data["receiver_id"]);
			$def_facility = $ptData['default_facility'];
			$pat_fac = "";
			if ($def_facility != ''){
				$rs = imw_query("SELECT facility_name FROM pos_facilityies_tbl WHERE pos_facility_id='" . $def_facility . "'");
				$row = imw_fetch_assoc($rs);
				$def_fac = trim($row['facility_name']);
				if($def_fac != ""){
					$pat_fac = $def_fac;
				} else{
					$pat_fac = "N/A";
				}
			}
			$tableData .= '<h2>' . $curr_pt_name . ' - ' . $inbox_data["receiver_id"] . '<span class="pull-right"><small>'.$pat_fac.'</small></span></h2>';
            $tableData .= '<div class=" clearfix"></div>';
            $tableData .= '<div class="messsub">' . $inbox_data["msg_subject"] . '</div>';
            $tableData .= '<div class="mesgdate">' . $inbox_data["msg_date_time"] . '</div>';
            $tableData .= '</li>';
        }
        /* End Scrollable Data List */
        $tableData .= '</ul></div>';
        /* Paging */
        $tableData .= '<div class="clearfix"></div><div class="row msgCount countFooter pt_paging" style="min-height:50px;">';
        $tableData .= $objPaging->buildComponentR8($page);
        $tableData .= '</div>';
        $tableData .= '<div class="col-sm-2"><button class="btn btn-danger" onClick="del_messages(\'del_pt_messages\');">Delete</button></div>';
        $tableData .= '</div></div>';
        $tableData .= '
				<!--Container to load the message Details-->
				<div class="col-sm-8" >
					<div class="row">
						<div class="col-sm-12" id="ptmessageData">';

        $tableData .= '</div></div></div>';
		} else {
            $tableData .= '<div class="pt5 pdl_10 mCustomScrollbar dynamicRightPadding"><div class="alert alert-danger">No Records</div></div>';
        }
        echo $tableData;

        if (count($rq_obj) > 0) {
            $load_buttons = '<div class="col-sm-12 text-center pt5 pdb5">' . $load_buttons . '</div>';
        } else
            $load_buttons = "";
        break;
    case 'pt_changes_approval':

        $sent_li =2;
        $req_qry = "
          SELECT pt_id,is_approved,tb_name,id,title_msg,reqDateTime,
                DATE_FORMAT(reqDateTime,'" . get_sql_date_format() . " %h:%i %p') as reqDateTime2, '' AS app_can_req_id, '' AS can_reason
          FROM iportal_req_changes
          WHERE del_status=0
          ";

		$erp_error=array();
        if(isERPPortalEnabled()){
			try {
				include_once($GLOBALS['srcdir'].'/erp_portal/appointmentrequests.php');
				$oApReq = new AppointmentRequests();
				$sqlMsg = $oApReq->get_app_reqs_qry();
				if(!empty($sqlMsg)){
				$req_qry = $req_qry." UNION ".$sqlMsg;
				}
				//START QUERY FOR CANCEL REQUEST FROM PORTAL
				$req_qry .= "UNION SELECT cr.patient_id AS pt_id, cr.aprv_dec AS is_approved, 'iportal_app_reqs' AS tb_name, cr.id, 'Request to cancel the patient' AS title_msg,
							CONCAT(sa.sa_app_start_date,' ',sa.sa_app_starttime) AS reqDateTime,
							DATE_FORMAT( CONCAT(sa.sa_app_start_date,' ',sa.sa_app_starttime), '%m-%d-%Y %h:%i %p' ) AS reqDateTime2,
							cr.app_can_req_id, cr.can_reason
							FROM `iportal_app_reqs` cr
							INNER JOIN schedule_appointments sa ON(sa.id = cr.app_ext_id)
							WHERE cr.app_can_req_id != ''
							AND cr.appt_req_id = ''";

				//END QUERY FOR CANCEL REQUEST FROM PORTAL
                    /*
                    include_once($GLOBALS['srcdir'].'/erp_portal/pghd_requests.php');
                    $Pghd_requests = new Pghd_requests();
                    $pghdQry = $Pghd_requests->get_pghd_reqs_qry();
                    if(!empty($pghdQry)){
                        $req_qry = $req_qry." UNION ".$pghdQry;
                    }
                    */

					include_once($GLOBALS['srcdir'].'/erp_portal/portal_patients.php');
                    $Portal_patients = new Portal_patients();
                    $ptQry = $Portal_patients->get_pt_reqs_qry();
                    if(!empty($ptQry)){
                        $req_qry = $req_qry." UNION ".$ptQry;
                    }
				} catch(Exception $e) {
					$erp_error[]='Unable to connect to ERP Portal';
				}
        }
        $req_qry .= " order by reqDateTime DESC, id DESC "; //is_approved,

        require_once($GLOBALS['fileroot'] . '/library/classes/paging.inc.php');
        $page = !isset($_REQUEST['page']) ? 1 : $_REQUEST['page'];
        $objPaging = new Paging(40, $page);
        $objPaging->query = $req_qry;
        $objPaging->func_name = "load_patient_messages";
        $objPaging->filter = "pt_changes_approval";
        $rq_obj = $objPaging->fetchLimitedRecords();

        //check on
        if (count($rq_obj) == 0) {
            echo '<div class="alert alert-danger">No Records</div>';
            exit;
        }

        $tableData = '';
        /* Scrollable Data List */
        $tableData .= '<div class="col-sm-4 messuser" id="ptmessagelist">';
        /* List Messsage Count */
        $tableData .= '<div class="row msgCount">';
        $tableData .= '<div class="col-sm-10">';
        $tableData .= $objPaging->getPagingString($page);
        $tableData .= '</div>';
        $tableData .= '<div class="col-sm-2" style="padding-right:10px !important;">' . $objPaging->buildComponentR8($page, $wout_btns = true) . '</div>';

        $tableData .= '<div class="scroll-content mCustomScrollbar" style="height:' . ($_SESSION['wn_height'] - 396) . 'px;">';
        $tableData .= '<ul class="messageList">';

        foreach ($rq_obj as $key => $notification_data) {
          // while ($notification_data = imw_fetch_assoc($tb_qry_obj)) {
          $curr_pt_arr = core_get_patient_name($notification_data["pt_id"]);
          $curr_pt_name = $curr_pt_arr['2'] . ', ' . $curr_pt_arr['1'];
          $curr_pt_name .= ' - ' . $notification_data["pt_id"];
          //$changes_data = pt_changes_msg($notification_data);

		  if($notification_data["tb_name"]=='erp_iportal_patients_data') {
			$sqlRs = imw_query("SELECT CONCAT(lastName,', ',firstName,' ',middleName) as portal_pt_name FROM `erp_iportal_patients_data`
			WHERE id='".$notification_data["id"]."' ");
			$dtRow=imw_fetch_assoc($sqlRs);
			$curr_pt_name = $dtRow["portal_pt_name"];
		  }

          if ($notification_data["is_approved"] == 1 && $notification_data["tb_name"]!='patient_pre_payment') {
              $actionCol = '<div style="color:#090;font-weight:bold;">Approved</div>';
			  if($notification_data["tb_name"]=='erp_iportal_patients_data') {
				  $actionCol = '<div style="color:#090;font-weight:bold;">Reconciled</div>';
			  }
          } else if ($notification_data["is_approved"] == 2) {
              $actionCol = '<div style="color:#F00;font-weight:bold;">Declined</div>';
			  if($notification_data["tb_name"]=='erp_iportal_patients_data') {
				  $actionCol = '<div style="color:#F00;font-weight:bold;">Cancelled</div>';
			  }
          } else {
              $actionCol = '<button class="btn btn-success" value="Approve" onclick="approve_operation(\'' . $notification_data["id"] . '\',this, \''.$notification_data["tb_name"].'\', \''.$notification_data["app_can_req_id"].'\');" >Approve</button>
                  <button class="btn btn-danger" value="Decline" onclick="disapprove_operation(\'' . $notification_data["id"] . '\',this, \''.$notification_data["tb_name"].'\', \''.$notification_data["app_can_req_id"].'\');">Decline</button>';

				if($notification_data["tb_name"]=='erp_iportal_patients_data') {
					$actionCol = '<button class="btn btn-success" value="Approve" onclick="reconcile_operation(\'' . $notification_data["id"] . '\',this, \''.$notification_data["tb_name"].'\', \''.$notification_data["app_can_req_id"].'\');" >Reconcile</button>
					<button class="btn btn-danger" value="Decline" onclick="cancel_reconcile(\'' . $notification_data["id"] . '\');">Cancel</button>';
				}
          }



          //Hide button in case payment made from patient portal.
          if($notification_data["tb_name"]=='patient_pre_payment'){
              $actionCol='';
          }

          /* Scrollable Data List */
		  if($notification_data["tb_name"]=='erp_iportal_patients_data') {
			  $onclickli = ' ';
			  if($notification_data["is_approved"] != 0) {
				  $onclickli = 'onclick="loadPortalReconciledPt(this,\''.$notification_data["id"].'\', \''.$notification_data["tb_name"].'\');"';
			  }

			  $tableData .= '<li id="msg_' . $notification_data["id"] . '" '.$onclickli.'>';
		  } else {
			  $tableData .= '<li id="msg_' . $notification_data["pt_id"] . '" onclick="loadPtNotificationDetail(this,\'' . $notification_data["pt_id"] . '\',2,\''.$notification_data["id"].'\', \''.$notification_data["tb_name"].'\', \''.$notification_data["app_can_req_id"].'\');">';
		  }

          //Setting Delete Chk box
              $chkBoxStr = '';
              $chkBoxStr = getDelChkBox($notification_data["id"]);
              if(empty($chkBoxStr) == false) $tableData .= $chkBoxStr;

          $tableData .= '<h2>' . $curr_pt_name . '</h2>';
          $tableData .= '<div class=" clearfix"></div>';
          $tableData .= '<div class="messsub">' . $notification_data["title_msg"] . '</div>';
          $tableData .= '<div class="mesgdate">' . $notification_data["reqDateTime2"] . '</div>';
          $tableData .= '<div class="mesgdate">' . $actionCol . '</div>';
          $tableData .= '</li>';

        }
        /* End Scrollable Data List */
        $tableData .= '</ul></div>';
        /* Paging */
        $tableData .= '<div class="clearfix"></div><div class="row msgCount countFooter pt_paging" style="min-height:50px;">';
        $tableData .= $objPaging->buildComponentR8($page);
        $tableData .= '</div>';
        $tableData .= '<div class="col-sm-2"><button class="btn btn-danger" onClick="del_messages(\'delete_approvals\');">Delete</button></div>';
        $tableData .= '</div></div>';
        $tableData .= '
				<!--Container to load the message Details-->
				<div class="col-sm-8" >
					<div class="row">
						<div class="col-sm-12" id="ptmessageData">';
        $tableData .= '</div></div></div>';
        echo $tableData;

        $load_buttons = '<div class="col-sm-12 text-center pt5 pdb5"><button class="btn btn-danger" onClick="del_messages(\'delete_approvals\')">Delete</button></div>';
        break;

    case 'getPtMessageDetails':

        /* Pull Complete Message Data */
        $msgId = (int) $_POST['msgId'];
        $sent_li = (int) $_POST['sent_li'];
		    $rec_id = (int) $_POST['rec_id'];
        $tbl = $_POST['tbl'];
		$cancel_req_id = trim($_POST['cancel_req_id']);

        $rq_obj = array();
        switch ($sent_li) {
            case 0:
                $sqlMsg = "SELECT pm.*, DATE_FORMAT(pm.msg_date_time,'" . get_sql_date_format() . " %h:%i %p') AS msg_date_time,
										CONCAT(pd.lname,', ',pd.fname,' ',pd.mname) as from_name
										FROM patient_messages pm
										JOIN patient_data pd ON pd.id = pm.sender_id
										WHERE pt_msg_id =$msgId";
                break;
            case 1:
                $sqlMsg = "SELECT pm.*, DATE_FORMAT(pm.msg_date_time,'" . get_sql_date_format() . " %h:%i %p') AS msg_date_time ,
										CONCAT(pd.lname,', ',pd.fname,' ',pd.mname) as to_name
										FROM patient_messages pm
										JOIN patient_data pd ON pd.id = pm.receiver_id
										WHERE pt_msg_id =$msgId";
                break;
            case 2:
                if($tbl=="iportal_app_reqs"){
                  if($cancel_req_id) {
					  $sqlMsg = "SELECT patient_id as pt_id, DATE_FORMAT(created_on,'%m-%d-%Y %h:%i %p') as reqDateTime,
					  				'Request to cancel the patient' as new_val_lbl, app_can_req_id, can_reason
					  				FROM iportal_app_reqs
									WHERE app_can_req_id != '' AND patient_id = '".$msgId."' AND id = '".$rec_id."'";
				  }else {
					  include_once($GLOBALS['srcdir'].'/erp_portal/appointmentrequests.php');
					  $oApReq = new AppointmentRequests();
					  $sqlMsg = $oApReq->get_req_inf($rec_id, $msgId);
				  }
				} else if($tbl=="iportal_pghd_reqs") {
                    /*
					include_once($GLOBALS['srcdir'].'/erp_portal/pghd_requests.php');
					$Pghd_requests = new Pghd_requests();
					$sqlMsg = $Pghd_requests->get_req_inf($rec_id, $msgId);
                    */
				} else{
                  $sqlMsg = "SELECT *,DATE_FORMAT(reqDateTime,'" . get_sql_date_format() . " %h:%i %p') as reqDateTime FROM iportal_req_changes WHERE del_status=0 AND pt_id = $msgId AND id = $rec_id";
                }
                break;
        }
        $respMsg = imw_query($sqlMsg);

        $responseData = '';

        if ($respMsg && imw_num_rows($respMsg) > 0) {
            $row = imw_fetch_assoc($respMsg);

            $rq_obj[$msgId] = $row;
            $messageText = nl2br(html_entity_decode($row['msg_data']));
            $patientId = $row['sender_id'];
            switch ($sent_li) {
                case 0:
                    $patientId = $row['sender_id'];
                    break;
                case 1:
                    $patientId = $row['receiver_id'];
                    break;
                case 2:
                    $patientId = $row['pt_id'];
                    $messageText = pt_changes_msg($row);
                    break;
            }

            if(isERPPortalEnabled()) {
                if($tbl=="iportal_pghd_reqs") {
                    /*
                    include_once($GLOBALS['srcdir'].'/erp_portal/pghd_requests.php');
                    $Pghd_requests = new Pghd_requests();
                    $messageText = $Pghd_requests->get_message_text($rec_id, $msgId);
                     */
                }
            }

            $patientData = $msgConsoleObj->get_patient_more_info($patientId);

			$default_facility = $patientData['default_facility'];
			if ($default_facility != ''){
				$pt_fac = "";
				$res = imw_query("SELECT facility_name FROM pos_facilityies_tbl WHERE pos_facility_id='" . $default_facility . "'");
				$row = imw_fetch_assoc($res);
				$def_fac = trim($row['facility_name']);
				if($def_fac != ""){
					$pt_fac = $def_fac;
				} else{
					$pt_fac = "N/A";
				}
			}

			/* Patient Image */
            $image_path='';
            $dir_path = $GLOBALS['file_upload_dir'];
            if ($patientData['p_imagename'] != '') {
                $patientData['p_imagename'] = $dir_path . $patientData['p_imagename'];
                $image_path = data_path() . $patientData['p_imagename'];
            }
            if (trim($patientData['p_imagename']) == '' || !file_exists($image_path)) {
                $image_path = $GLOBALS['fileroot'] . '/library/images/no_image_found.png';
            }
            if (trim($patientData['p_imagename']) == '' || !file_exists($patientData['p_imagename']))
                $patientData['p_imagename'] = $GLOBALS['webroot'] . '/library/images/no_image_found.png';

            /* Address */
            $pt_address = $patientData['street'];
            if (trim($patientData['street2']) != '')
                $pt_address .= ', ' . trim($patientData['street2']);

            $csz = '';
            $csz .= $patientData['city'];
            if ($csz != ' ')
                $csz .= ', ' . $patientData['state'];
            else
                $csz = $patientData['state'];

            if ($csz != '' && trim($patientData['postal_code']) != '')
                $csz .= ' - ' . $patientData['postal_code'];
            else
                $csz = $patientData['postal_code'];

            if ($csz != '' && $patientData['zip_ext'] != '')
                $csz .= '-' . $patientData['zip_ext'];

            /* Phone */
            $home=$work=$cell='';
            if ($patientData['phone_home'] != '') {
                $home=str_replace(" ", "", core_phone_format($patientData['phone_home']));
                $patientData['phone_home'] = '<div class="col-sm-2 pt5"><strong>Home</strong></div><div class="col-sm-1 pt5"> : </div><div class="col-sm-9 pt5">' . $home . '</div><div class="clearfix"></div>';
            }
            if ($patientData['phone_biz'] != '') {
                $work=str_replace(" ", "", core_phone_format($patientData['phone_biz']));
                $patientData['phone_biz'] = '<div class="col-sm-2 pt5"><strong>Work</strong></div><div class="col-sm-1 pt5"> : </div><div class="col-sm-9 pt5">' . $work . '</div><div class="clearfix"></div>';
            }
            if ($patientData['phone_cell'] != ''){
                $cell=str_replace(" ", "", core_phone_format($patientData['phone_cell']));
                $patientData['phone_cell'] = '<div class="col-sm-2 pt5"><strong>Cell</strong></div><div class="col-sm-1 pt5"> : </div><div class="col-sm-9 pt5">' . $cell . '</div><div class="clearfix"></div>';
            }
			$curr_pt_rp_name = "";
			if ($row['pt_resp_party_id'] != 0){
					$id = $row['pt_resp_party_id'];
					$sqlRPMsg = "Select id, fname, 	lname from resp_party where id = $id";
					$respRPMsg = imw_query($sqlRPMsg);
					if ($respRPMsg && imw_num_rows($respRPMsg) > 0) {
					$rowRP = imw_fetch_assoc($respRPMsg);
					$curr_pt_rp_name = $rowRP['lname'] . ', ' . $rowRP['fname'];
					$curr_pt_rp_name .= ' - ' . $rowRP["id"].'<br />(Patient Representative)';
				}
			} else {
				$patient_RP_Data =   $patientData['lname'] . ', ' . $patientData['fname'];
				$patient_RP_Data .= ' - ' . $patientData["id"].'<br /> (Patient)';
				$curr_pt_rp_name = $patient_RP_Data;
			}

            /* Appointment Details */
            $pt_appt = $msgConsoleObj->get_pt_appt($patientData['id']);
            if ($pt_appt['appt_dt_time'] != '') {
                $facility_name = $pt_appt['facility_name'];
                if (str_word_count($facility_name) != 1) {
                    $arr_facility_name = str_word_count($facility_name, 1);
                    $tmp_arr_fac_name = '';
                    foreach ($arr_facility_name as $val) {
                        $tmp_arr_fac_name .= substr($val, 0, 1);
                    }
                    $facility_name = strtoupper($tmp_arr_fac_name);
                }
                $appt_data = $pt_appt['phy_init_name'] . ' / ' . $pt_appt['appt_dt_time'] . ' / ' . $facility_name;
            } else
                $appt_data = 'N/A';

            $reply = show_tooltip('Reply', 'top');
            $replyAll = show_tooltip('Reply All', 'top');
            $forward = show_tooltip('Forward', 'top');
            $completed = show_tooltip('Task Completed', 'top');

            $dataHeight = (int) $_SESSION['wn_height'] - 720;

            $subject = urlencode($row['message_subject']);
            if ($sent_li != 2) {
                $responseData .= '
			<!--Message Action icons-->
			<div class="mesoption">
                <img src="'.$GLOBALS['webroot'].'/library/images/mes1.jpg" '.$reply.' onClick="reply_direct(\''.$msgId.'\',\'reply\'); load_ptcomm_ptinfo(\''.$patientId.'\');"  alt=""/>
                <img src="'.$GLOBALS['webroot'].'/library/images/mes2.jpg" '.$replyAll.' onClick="reply_direct(\''.$msgId.'\',\'replyAll\'); load_ptcomm_ptinfo(\''.$patientId.'\');" alt=""/>
				<img src="'.$GLOBALS['webroot'].'/library/images/mes3.jpg" '.$forward.' onClick="reply_direct(\''.$msgId.'\',\'forward\', this); load_ptcomm_ptinfo(\''.$patientId.'\');" alt="" data-patientid="'.$patientId.'"/>
                <img src="'.$GLOBALS['webroot'].'/library/images/mes4.jpg" '.$completed.' onclick="pt_msg_completed(\''.$msgId.'\',\''.$msgConsoleObj->operator_id.'\',\''.$patientId.'\')" alt=""/>
			</div>';

            }

            //Get Pt Attachements
            $atch=get_pt_msg_attach($msgId);
            if(!empty($atch)){
              $responseData .= '
                <div class="clearfix"></div>
                <div class="ptmesatch">
                  <div class="row">
                    '.$atch.'
                  </div>
                </div>
              ';
            }

            /*PDF print data Starts*/
            $image_tag=(file_exists($image_path))?'<img src="'.$image_path.'" alt="" style="width:76px;" />':'';

            $pdf_responseData .= '
                <table style="width:760px;">
                    <tr><td>'.$image_tag.'</td></tr>
                    <tr><td><strong>Patient Name : </strong>'.$patientData['lname'].', '.$patientData['fname'].' '.$patientData['mname'].' - '.$patientData['id'].'</td></tr>
                    <tr><td><strong>Gender : </strong>'.$patientData['sex'].'</td></tr>
                    <tr><td><strong>DOB : </strong>'.$patientData['DOB'].'</td></tr>
                    <tr><td><strong>Address : </strong>'.$pt_address.' '.$csz.'</td></tr>
                    <tr><td><strong>Home : </strong>'.$home.'</td></tr>
                    <tr><td><strong>Work : </strong>'.$work.'</td></tr>
                    <tr><td><strong>Cell : </strong>'.$cell.'</td></tr>
                    <tr><td><strong>Email : </strong>'.$patientData['email'].'</td></tr>
                    <tr><td><strong>Appt : </strong>'.$appt_data.'</td></tr>
                    <tr><td><strong>Sender : </strong>'. strip_tags($curr_pt_rp_name).'</td></tr>
                    <tr><td><strong>Facility : </strong>'.$pt_fac.'</td></tr>
                    <tr><td style="width:730px;"><strong>Message Text : </strong><p>'.$messageText.'</p></td></tr>
                </table>';

            $final_data='<page backtop="5mm" backbottom="5mm">
                <page_footer>
                    <table style="width:760px;">
                        <tr>
                            <td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
                        </tr>
                    </table>
                </page_footer>
                <page_header>
                    <style>
                        .tb_headingHeader{
                            font-weight:bold;
                            color:#FFFFFF;
                            background-color:#4684ab;
                        }
                    </style>
                    <table style="width:760px;">
                        <tr><td style="width:755px;" class="tb_headingHeader">Patient Messages</td></tr>
                    </table>
                </page_header>';
            $final_data.=$pdf_responseData.'</page>';

            $filesPath = data_path()."/UserId_".$_SESSION['authId']."/tmp/pt_messages/*";
            $files = glob($filesPath);
            foreach($files as $file){
                if(is_file($file))@unlink($file);
            }
            $rand=rand(0,500);
            $print_file_name = 'pt_messages/patient_message_'.$_SESSION['authId']."_".$patientId."_".$msgId."_".$rand;
            $file_location = write_html($final_data,$print_file_name.".html");

            /*PDF print data ends*/

            $responseData .= '<div class="clearfix"></div>

			<!--Patient Details-->
			<div id="ptmsg_content" data-msg_id="'.$msgId.'"  data-patientId="'.$patientId.'">
			<div class="ptcommu">
				<div class="row">
					<div class="col-sm-5">
						<div class="ptdtl">
							<figure>
								<img src="'.$patientData['p_imagename'].'" alt="" style="width:76px;" />
							</figure>
							<div><strong><span class="text_purple pointer" onClick="LoadWorkView('.$patientId.');">'.$patientData['lname'].', '.$patientData['fname'].' '.$patientData['mname'].' - '.$patientData['id'].'</span></strong></div>
							 <div class="clearfix"></div>
							<div class="row">
								<div class="col-sm-6"><strong>Gender</strong>   :	'.$patientData['sex'].'</div>
								<div class="col-sm-6"><strong>DOB</strong>   :     '.$patientData['DOB'].'</div>
							</div>

							<div class="clearfix"></div>

							<div class="row">
								<div class="col-sm-3"><strong>Address</strong> :</div>
								<div class="col-sm-9">'.$pt_address.' '.$csz.'</div>
							</div>
						</div>
					</div>

					<div class="col-sm-7 ptcontdtl">
						<div class="row">
							<div class="col-sm-5">
								<div class="row continfo">
									'.$patientData['phone_home'].'
									'.$patientData['phone_biz'].'
									'.$patientData['phone_cell'].'
									<div class="col-sm-2 pt5">
										<strong>Email</strong>
									</div>
									<div class="col-sm-1 pt5"> : </div>
									<div class="col-sm-9 pt5">'.$patientData['email'].'</div>
								</div>
							</div>
							<div class="col-sm-7">
								<div class="row continfo" style="min-height:0px!important;">
									<div class="col-sm-2">
										<strong>Appt</strong>
									</div>
									<div class="col-sm-1"> : </div>
									<div class="col-sm-9">'.$appt_data.'</div>
								</div>
								<div class="row continfo">
									<div class="col-sm-2">
										<strong>Sender</strong>
									</div>
									<div class="col-sm-1"> : </div>
									<div class="col-sm-9">'.$curr_pt_rp_name.'</div>
									<div class="col-sm-2">
										<strong>Facility</strong>
									</div>
									<div class="col-sm-1"> : </div>
									<div class="col-sm-9">'.$pt_fac.'</div>
								</div>
							</div>
						</div>

					<!--

						<div class="ptinfara">
							<div class="row">
								<div class="col-sm-5 ptcomubut">
									<div class="checkbox">
										<label>
											<input type="checkbox"> Patient Verbal Communication
										</label>
									</div>
								</div>
							</div>
						</div>
						-->
						<div class="clearfix"></div>
					</div>
				</div>
			</div>

			<div class="clearfix"></div>

			<!--Complete Message Data-->
			<div>
				<div class="postmessage">
					<div class="scroll-content mCustomScrollbar" style="height:'.$dataHeight.'px;">
						<span class="ptmsgText">'.$messageText.'</span>
                        <input type="hidden" name="pt_msg_location" id="pt_msg_location" value="'.$file_location.'" />
					</div>
				</div>
			</div>
			</div>';

        }
//        if (count($rq_obj) > 0) {
//            $responseData .= '<div class="col-sm-12 text-center pt5 pdb5">' . $load_buttons . '</div>';
//        } else
//            $responseData .= "";

        print $responseData;

        break;
}

function pt_changes_msg($notification_data) {
    $changes_modification = pt_changes_modification($notification_data);
    $change_data_msg = "";
    if ($notification_data["action"] == "edit" && $notification_data["tb_name"] == "lists" && ($notification_data["col_name"] == "begdate" || $notification_data["col_name"] == "enddate")) {
        //$new_val_arr_date = explode("-", $notification_data["new_val"]);
		 $notification_data["new_val"] = get_date_format($notification_data["new_val"]);
        if (count($new_val_arr_date) == 3) {
            /* if (strlen($new_val_arr_date[2]) != 4) {
                $new_val_date_final = $new_val_arr_date[1] . '-' . $new_val_arr_date[2] . '-' . $new_val_arr_date[0];
                $notification_data["new_val"] = $new_val_date_final;
            } */
        }
    }
    if ($notification_data["action"] == "edit") {
        $old_val_label = $notification_data["old_val"];
        $new_val_label = $notification_data["new_val"];
        if ($notification_data["old_val_lbl"] != "" && $notification_data["new_val_lbl"] != "") {
            $old_val_label = $notification_data["old_val_lbl"];
            $new_val_label = $notification_data["new_val_lbl"];
        }

        $change_data_msg = "
		<div class='row'><div class='col-sm-12'>
		<table class='table table-bordered table-striped table-hover' width='100%'>
			<tr>
				<td colspan='2' style='font-weight:bold;'>" . $notification_data["col_lbl"] . " changed </td>
			</tr>
			<tr>
				<td>Old Value</td>
				<td>New Value</td>
			</tr>
			<tr>
				<td>" . $old_val_label . "</td>
				<td>" . $new_val_label . "</td>
			</tr>
			" . $changes_modification . "
		</table></div></div>";
    }else if($notification_data["tb_name"]=="user_messages" && $notification_data["col_lbl"]=="PGHD"){
		$change_data_msg= "<table cellpadding='3' cellspacing='0' style='border:1px solid #CCC;'>
								<tr>
									<td style='font-weight:bold;'>PGHD- Patient Health Information:</td>
								</tr>
								<tr><td>
								".$notification_data["new_val_lbl"]."
								</td></tr>
							</table>";
	}else if($notification_data["app_can_req_id"]!="" && $notification_data["can_reason"]){
		$change_data_msg= "<table cellpadding='3' cellspacing='0' style='border:0px solid #CCC;'>
								<tr>
									<td style='font-weight:bold;'>".$notification_data["new_val_lbl"].":</td>
								</tr>
								<tr><td>
								".$notification_data["can_reason"]."
								</td></tr>
							</table>";
	} else {
        $change_data_msg = "<div class='row notif_detail'><div class='col-sm-12'>" . $notification_data["new_val_lbl"] . "</div></div>";
    }
    return $change_data_msg;
}

function pt_changes_modification($notification_data) {
    $return_html = "";
    $tb_name = $notification_data["tb_name"];
    $col_name = $notification_data["col_name"];

    if ($tb_name == "insurance_data" && $col_name == "provider") {
        $changed_val = $notification_data["new_val"];
        $changed_val = imw_real_escape_string($changed_val);
        $req_qry = "SELECT id FROM insurance_companies WHERE name='" . $changed_val . "' and ins_del_status='0' LIMIT 1";
        $req_qry_obj = imw_query($req_qry);
        if (imw_num_rows($req_qry_obj) == 0) {
            $return_html = '<tr style="font-size:15px;padding:5px;color:#000;background-color:#ffbbbb;">
				<td colspan="2">The insurance company not exists in the database. Please add it from the Admin->Billing->Insurance before approving it.</td>
			</tr>';
        }
    }
    return $return_html;
}
$filter_type = 'load_pt_msg_inbox';
if (isset($sent_li) && $sent_li != '') {
    switch ($sent_li) {
        case '1':
            $filter_type = 'load_pt_msg_sent';
            break;
        case '2':
            $filter_type = 'pt_changes_approval';
            break;
    }
}
$up_dir_path=$GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH');
?>
<script>
    var up_dir_path = '<?php echo $up_dir_path; ?>';
    function check_patient_msg_frm(objVal, prevForm, flgSv)
    {
        var txt_patient_name = $("#txt_patient_name").val();
        var patientId = $("#patientId").val();
        var subject = $("#subject").val();
        var filter_type = $("#filter_type").val();
        var body_txt = $("#body").val();
        var selUser = $('#sent_to_groups').val();

        var forwardType = $('input[name=forwardType]:checked').val();
        var urlForAjax = "patient_messages.php";
        var queryvar = '';

        if(objVal){
            forwardType = 1;
        }

        if(forwardType == 1){
            if ($.trim(txt_patient_name) == "" || $.trim(patientId) == "")
            {
                alert("Please specify the patient name");
                return false;
            }
        }else{
            if ($.trim(selUser) == "" || $.trim(selUser) == "" || selUser.length == 0)
            {
                alert("Please select a user to continue");
                return false;
            }
            urlForAjax = "send_msg_frm.php";
            queryvar='&txt_sbmt="Send Message"';
        }

        if ($.trim(subject) == "")
        {
            alert("Please specify the message subject");
            return false;
        }
        if ($.trim(body_txt) == "")
        {
            alert("Please specify the message body");
            return false;
        }
        $('#loader').html('<div class="doing"></div>');
        if(typeof(flgSv)=="undefined" && typeof(file_attached)!="undefined" && file_attached.length<=0){
    			flg_auto_save=1;
    			start_upload();
    			return;
    		}

        flg_auto_save=0;
        var frm_data = $('#frmForm').serialize()+queryvar;
        if(prevForm) frm_data = prevForm;
        if(typeof(file_attached)!="undefined" && file_attached.length>0){
    			frm_data += "&attchd_files="+JSON.stringify(file_attached);
    			file_attached=[];
    		}else if($('#frm_pt_docs_atch').length>0 && typeof(ar_attch_pt_files)!="undefined" && ar_attch_pt_files.length>0){
          frm_data += "&attchd_files_pt_docs="+JSON.stringify(ar_attch_pt_files);
          ar_attch_pt_files=[];
        }

        $.ajax({
            type: "POST",
            url: urlForAjax,
            data: frm_data,
            success: function (r) {
                //console.log(r);return false;
                //$("#divContainer").modal('hide');
                if(!objVal && $('#divContainer .userForRow').hasClass('hide') == false){
                    //First time msg is sent to user and after tht it is entered in sent messages
                    check_patient_msg_frm(1, prevForm, flgSv);
                }else{
                    $(".modal-backdrop").removeClass('in').addClass('hide');
                    do_action('load_patient_messages', filter_type);
                }
                $("#upldModal,#attchPtDocModal").hide().data( 'bs.modal', null ).remove();

            }
        });
    }

    function reply_direct(key, reply, obj) {
        var arrObj = <?php echo json_encode($rq_obj); ?>;
        $("#reply_of").val(arrObj[key]['pt_msg_id']);
        $("#patientId").val(arrObj[key]['sender_id']);
        $("#txt_patient_name").val(arrObj[key]['from_name']);
        $("#subject").val("Re: " + arrObj[key]['msg_subject']);
        var dataPtId = $(obj).data('patientid');
        if(reply == 'forward') {
            $("#body").val($('.ptmsgText').text());
        } else {
            $("#body").val("");
        }
        $("#divContainer").unbind('shown.bs.modal');
        $("#divContainer").modal('show').on('shown.bs.modal', function(){
            if(reply == 'forward') {
                $('input[name=forwardType]').data('forward', 'true');
                if($('#divContainer .userForRow').hasClass('hide') == true){$('#divContainer .userForRow').removeClass('hide');}
            }else{
                if($('#divContainer .userForRow').hasClass('hide') == false){$('#divContainer .userForRow').addClass('hide');}
            }
            setForwardOption();
            if(dataPtId) getPatientNameManually(dataPtId);
        });
    }

    $(function(){
        $('input[name=forwardType]').unbind('click');
        $('input[name=forwardType]').on('click', function(){
            if($(this).data('forward') == 'true'){
                setForwardOption($(this).val());
            }
        });
    });

</script>
<div id="directLoader" style="position:absolute; top:70px;left:500px"></div>

<!--modal wrapper class is being used to control modal design-->
<div class="common_modal_wrapper">
    <!-- Modal -->
    <div id="divContainer" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg" style="width:70%">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" id="divHeader"> Send Patient Message </h4>
                </div>
                <div class="modal-body ptmessapopup">
                    <div id="loader" style="position:absolute"></div>
                    <div id="divForm"></div>
                    <form name="frmForm" id="frmForm">
                        <div class="row sendptmsg">
                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="col-sm-7 patientForRow">
                                        <div class="row">
                                            <div class="col-sm-10">
                                                <div class="radio">
                                                    <input type="radio" name="forwardType" id="patientForward" value=1 data-forward="false">
                                                    <label for="patientForward">To Patient</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                              <div class="dropdown">
                                                <button class="btn dropdown-toggle" type="button" data-toggle="dropdown">
                                                  <span class="glyphicon glyphicon-paperclip clickable pull-right font-18" id="dv_attch"  title="Attachments" ></span>
                                                </button> <!-- <span class="caret"></span> -->
                                                <ul class="dropdown-menu">
                                                  <li><a href="#" onclick="load_mdl_atch(1)">Local PC</a></li>
                                                  <li><a href="#" onclick="load_mdl_atch(2)">Pt. Docs</a></li>
                                                  <!--<li class="hidden"><a href="#" onclick="load_mdl_atch(3)">Pt Edu. Material</a></li>-->
                                                </ul>
                                              </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <input type="hidden" name="patientId" id="patientId" value="<?php print $patientId; ?>" />
                                                <input type="text" id="txt_patient_name" name="txt_patient_name" onKeyPress="{
                                                            if (event.keyCode == 13)
                                                                return searchPatient()
                                                        }" value="<?php print $patName; ?>" class="form-control" onBlur="chk_patient(this);
                                                                searchPatient()" onChange="document.getElementById('tdPatDOS').innerHTML = '';
                                                                        $('#trDOS').hide();"/>
                                            </div>
                                            <div class="col-sm-4">
                                                <select name="txt_findBy" id="txt_findBy" onChange="searchPatient2(this)" onkeypress="{
                                                            if (event.keyCode == 13)
                                                                return searchPatient()
                                                        }" class="form-control minimal">
                                                    <option value="Active">Active</option>
                                                    <option value="Inactive">Inactive</option>
                                                    <option value="Deceased">Deceased</option>
                                                    <option value="Resp.LN">Resp.LN</option>
                                                    <option value="Ins.Policy">Ins.Policy</option>
                                                    <?php print $searchOption; ?>
                                                </select>
                                            </div>
                                            <div class="col-sm-4">
                                                <a href="javascript:void(0);" onClick="searchPatient()" onKeyPress="{
                                                            if (event.keyCode == 13)
                                                                return searchPatient()
                                                        }" class="text_10b_purpule btn btn-success searchButton">Search</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-5">
                                        <div class="row userForRow">
                                        <div class="col-sm-12">
                                            <div class="radio">
                                                <input type="radio" name="forwardType" id="usertForward" value=2 data-forward="false">
                                                <label for="usertForward">To user</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                        <select name="sent_to_groups[]" id="sent_to_groups" class="selectpicker minimal selecicon" multiple="multiple" data-done-button="true" data-size="8" data-actions-box="true" data-live-search="true">
                                        <?php
                                                $message_to = $_SESSION['authId'];
                                                $newOption = '';
                                                $qry = imw_query("select id,lname,fname,mname from users where id > 0 and delete_status = '0' order by lname,fname");
                                                while ($userQryRes = imw_fetch_assoc($qry)) {
                                                    $id = $userQryRes['id'];
                                                    //if($message_sender_id==$id)
                                                    if (in_array($id, $arrReplyUserId)) {
                                                        $sel = "selected='selected'";
                                                    } else {
                                                        $sel = '';
                                                    }
                                                    $phyName = $userQryRes['lname'] . ', ';
                                                    $phyName .= $userQryRes['fname'] . ' ';
                                                    $phyName .= $userQryRes['mname'];
                                                    if ($phyName[0] == ',') {
                                                        $phyName = preg_replace('/, /', '', $phyName);
                                                    }
                                                    //	if($id == $_SESSION['authId']){
                                                    //		$newOption = "<option value='$id' selected>$phyName</option>";
                                                    //	}
                                                    $phyName = trim(ucwords($phyName));
                                                    //	$sel = $id == $message_to12 ? 'selected' : '';
                                                    $phyOption .= "<option " . $sel . "  value='$id'>$phyName</option>";
                                                }
                                                // print $newOption;
                                                //--- Get user groups -----
                                                $qry = imw_query("select name from user_groups where status = '1' order by display_order");
                                                while ($groupsQryRes = imw_fetch_assoc($qry)) {
                                                    $groupsOption .= '<option value="' . $groupsQryRes['name'] . '">' . $groupsQryRes['name'] . '</option>';
                                                }
                                                //--GET Provider Groups -----
                                                $opt_prov_grp = $msgConsoleObj->getProvGroupOpts();

                                                print $opt_prov_grp;
                                                print $groupsOption;
                                                print $phyOption;
                                            ?>
                                        </select>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="row pt10">
                                    <div class="col-sm-3">Subject</div>
                                    <div class="col-sm-9"><input type="text" id="subject" name="subject" value="" class="form-control" /></div>
                                </div>
                                 <div class="clearfix"></div>
                                <div class="row pt10">
                                    <div class="col-sm-3">Message</div>
                                    <div class="col-sm-9"><textarea class="form-control" id="body" name="body" rows="5"></textarea></div>
                                </div>
                                 <div class="clearfix"></div>
                                <div class="row pt10">
                                    <div class="col-sm-1">&nbsp;</div>
                                    <div class="col-sm-4">
                                        <div class="checkbox">
                                            <input id="message_urgent" type="checkbox" name="message_urgent" value="1" class="chk_record">
                                            <label for="message_urgent">Urgent Message</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <div class="checkbox">
                                            <input id="message_pt_comm" type="checkbox" name="message_pt_comm" value="1" class="chk_record">
                                            <label for="message_pt_comm">Include Patient Communication</label>
                                        </div>
                                    </div>
                                </div>
                                 <div class="clearfix"></div>
                            </div>
                            <div class="col-sm-6">
                                <div id="divPtDemo"><div id="pat_details_td"></div></div>
                            </div>
                             <div class="clearfix"></div>
                        </div>
                        <input type="hidden" name="sync_type"  id="sync_type"  value="send_mail" />
                        <input type="hidden" name="reply_of" id="reply_of" />
                        <input type="hidden" name="filter_type" id="filter_type" value="<?php echo $filter_type;?>"/>
                    </form>
                </div>
                <div class="modal-footer" id="divBtnCont">
                    <button name="send_message" id="send_message" class="btn btn-primary" onClick="return check_patient_msg_frm();">Send Message</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
<? //echo $load_buttons;?>