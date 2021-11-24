<?php
/*
*	The MIT License (MIT)
*	Distribute, Modify and Contribute under MIT License
* Use this software under MIT License
*/

class PreDefineAdmin{
	
	private $tbl_name = false;
	private $content = false;
	private $idField = false;
	private $tbl_data = false;
	private $f_types = array(1 => 'Yes/No' , 3 => 'TextBox', 4 => 'Multi Options');//2 => 'TextBox' 
	private $d_types = array(1 => 'Single Select DropDown', 2 => 'Multi Select DropDown', 3 => 'Display All');
	
	public function showAdminHtml($data){
		
		if( !$data['content'] || !$data['table'] || !$data['idField'] ) return false;
		
		$this->tbl_name = $data['table'];
		$this->content = $data['content'];
		$this->idField = $data['idField'];
		
		$this->tbl_data = $this->getContent();
		
		$html = $this->contentHtml();
		
		return $html;
	}
	
	public function filter_options($optionStr) {
		
		$optionStr = trim($optionStr);
		$array = explode("\n",$optionStr);
		
		$return = array();
		if( is_array($array) && count($array) > 0 ) {
			foreach($array as $arrV) {
				$arrV = trim($arrV);
				if( $arrV ) { $return[] = $arrV; } 
			}
				
		}
		$return = implode("\n",$return);
		$return = addslashes($return);
		return $return;
		
	}
	
	public function saveContent(&$request){
		//echo '<pre>';print_r($request);echo '</pre>';
		global $objManageData;
		$return = array();
		$macQuesIdArr = $request['macQuesId'];
		if( is_array($macQuesIdArr) && count($macQuesIdArr) > 0 ) {
			foreach($macQuesIdArr as $key => $recordId ){
				$question = addslashes($request['question'][$key]);
				$f_type = $request['f_type'][$key];
				$d_type = isset($request['d_type'][$key]) ? $request['d_type'][$key] : '0';
				$ques_options = ($f_type == 4 && isset($request['ques_options'][$key]) )  ? $this->filter_options($request['ques_options'][$key]) : '';
				
				if( $question && $f_type ) {
					// Check if already exists
					unset($chkRecordArr);
					$chkRecordArr['question = '] =  $question;
					if( $recordId )
						$chkRecordArr['id <> '] =  $recordId;
					
					$chkRecords	=	$objManageData->getAllRecords('predefine_mac_regional_questions',array('id'),$chkRecordArr);
					
					if( $chkRecords ) {
						// Throw Error
						$return['error']	=	true;
						$return['message'] = 'Record(s) already exists';
					}
					else {
						unset($arrayRecord);
						$arrayRecord['question'] = $question;
						$arrayRecord['f_type'] = $f_type;
						$arrayRecord['d_type'] = $d_type;
						$arrayRecord['options'] = $ques_options;
						
						if( $recordId ){
							$arrayRecord['modified_on'] = date('Y-m-d H:i:s');	
							$arrayRecord['modified_by'] = $_SESSION['loginUserId'];	
							$c = $objManageData->UpdateRecord($arrayRecord, 'predefine_mac_regional_questions', 'id', $recordId);
							
							$return['update'] = $c ? true : 'false';
						} 
						else {
							
							$arrayRecord['created_on'] = date('Y-m-d H:i:s');	
							$arrayRecord['created_by'] = $_SESSION['loginUserId'];
							
							$d = $objManageData->addRecords($arrayRecord, 'predefine_mac_regional_questions');
							
							$uQry = "Update predefine_mac_regional_questions set sort_id = ".(int)$d." Where id = ".(int)$d." ";
							$uSql = imw_query($uQry);
							
							$return['insert'] = $d ? true : 'false';
						}
					}
					
				}
			}
		}
		
		return $return;
	}
	
	public function getContent($id = false) {
		
		$return = array();
		$s_qry = $id ? " And id = ".(int)$id : '';
		$qry = "Select * From ".$this->tbl_name." Where 1=1 ".$s_qry." Order by sort_id Asc";
		$sql = imw_query($qry) or die('Error found in query "'.$qry.'" @ line no. '.(__LINE__).': '.imw_error());
		$cnt = imw_num_rows($sql);
		if( $cnt > 0 ) {
			while($row = imw_fetch_assoc($sql))
				$return[] = $row;
		}
		
		return $return;
	}
	
	public function contentHtml() {
		$html = '';
		$counter = 0;
		$html = '
		<form name="macRegionalQuestionsFrm" action="predefineFrmForm.php" method="post" autocomplete="off">
			<input type="hidden" name="contentOf" value="'.$this->content.'">
			<input type="hidden" name="table" value="'.$this->tbl_name.'">
			<input type="hidden" name="idField" value="'.$this->idField.'">
			<input type="hidden" name="deleteSelected" value="">
			
			<div class="scheduler_table_Complete">
      	<div class="my_table_Checkall adj_tp_table" style="padding:0px; margin:0px">
        	<table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf  table-striped">
						<thead>
							<tr>
								<th class="text-center" style="width:2%"></th>
								<th class="text-center" style="width:2%"><input type="checkbox"  id="checkall" onClick="return checkAllFn(this);"> </th>
								<th class="text-left" style="width:31%">Question</th>
								<th class="text-left" style="width:7%">Field Type</th>
								<th class="text-left" style="width:10%">Display Type</th>
								<th class="text-left" style="width:48%">Options</th>
							</tr>
						</thead>
						<tbody id="MacRegionalQuesBody">';
						
						
						if( is_array($this->tbl_data) && count($this->tbl_data) > 0) {
							foreach($this->tbl_data as $d) { $counter++;
								$qid = $d['id'];
								$html .= '<tr id="record-'.$qid.'" tbl="predefine_mac_regional_questions">';
								// Record Status
								$html .= '<td class="'.($d['deleted']==1?'inactive-record':'active-record').'" data-record-id="'.$qid.'" data-table-name="'.$this->tbl_name.'" data-unique-field="id">&nbsp;</td>';
								// Check All Checkbox
								$html .= '<td>';
									$html .= '<input type="checkbox" name="chkBox[]" value="'.$qid.'" />';
									$html .= '<input type="hidden" name="macQuesId['.$counter.']" value="'.$qid.'" />';
								$html .= '</td>';
								// Question Field
								$html .= '<td><input type="text" class="form-control" name="question['.$counter.']" id="question_'.$counter.'" value="'.$d['question'].'" /></td>';
								// Field Type
								$f_type = $d['f_type'];
								$html .= '<td>';
									$html .= '<select class="form-control minimal" name="f_type['.$counter.']" id="f_type_'.$counter.'" onChange="doEnable(this);" data-index="'.$counter.'" >';
									foreach($this->f_types as $k=>$v) {
										$html .= '<option value="'.$k.'" '.(($f_type == $k || $k ==1)?'selected':'').' >'.$v.'</option>';
									}
									$html .= '</select>';
								$html .= '</td>';
								// Display type
								$html .= '<td>';
									$html .= '<select class="form-control minimal" name="d_type['.$counter.']" id="d_type_'.$counter.'" '.($f_type<>4?'disabled':'').'>';
										$html .= '<option value="" selected>Select</option>';
										foreach($this->d_types as $k=>$v) {
											$html .= '<option value="'.$k.'" '.(($d['d_type'] == $k)?'selected':'').' >'.$v.'</option>';
										}
									$html .= '</select>';
								$html .= '</td>';
								// options list box 
								$html .= '<td><textarea class="form-control" name="ques_options['.$counter.']" id="ques_options_'.$counter.'" placeholder="Options(Separated by Newline )" '.($f_type<>4?'disabled':'style="height:120px;"').' >'.stripslashes($d['options']).'</textarea></td>';
								$html .= '</tr>';
							}
						}
						else {
							$html .= '<tr><td class="text-center" colspan="7">No record found.</td></tr>';
						}
	$html .= '</tbody>
					</table>
				</div>
			</div>';
	
	// Model HTML
	$counter++;
	$html .= '
	<div class="modal fade" id="macQuesTr">
		<div class="modal-dialog modal-lg ">
			<div class="modal-content">
				
				<div class="modal-header text-center">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title rob">ADD NEW</h4>
				</div>
					
				<div class="modal-body">
				
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="form_inner_m">
							<div class="row">
								<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
									<label for="ps" class="text-left">Question</label>
               	</div>
								<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
									<input type="hidden" name="macQuesId['.$counter.']" value="0" />
									<input type="text" class="form-control" name="question['.$counter.']" id="question_'.$counter.'" value="" placeholder="Question" />
								</div>
							</div>
						</div>
					</div>
					
					<div class="clearfix"></div>
					
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
						<div class="form_inner_m">
							<div class="row">
								<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
									<label for="ps" class="text-left">Field Type</label>
								</div>
								<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
									<select class="form-control minimal" name="f_type['.$counter.']" id="f_type_'.$counter.'" onChange="doEnable(this);" data-index="'.$counter.'" >';
									foreach($this->f_types as $k=>$v) {
										$html .= '<option value="'.$k.'" '.(($k ==1)?'selected':'').' >'.$v.'</option>';
									}
									
		$html .= '		</select>
								</div>
							</div>
						</div>
					</div>
					
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
						<div class="form_inner_m">
							<div class="row">
								<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
									<label for="ps" class="text-left">Display Type</label>
								</div>
								<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
									<select class="form-control minimal" name="d_type['.$counter.']" id="d_type_'.$counter.'" disabled >
										<option value="" selected>Select</option>';
										foreach($this->d_types as $k=>$v) {
											$html .= '<option value="'.$k.'" >'.$v.'</option>';
										}
									
		$html .= '		</select>
								</div>
							</div>
						</div>
					</div>
					
					<div class="clearfix"></div>
					
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="form_inner_m">
							<div class="row">
								<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
									<label for="ps" class="text-left">Options(Separated by Newline)</label>
               	</div>
								<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
									<textarea class="form-control" name="ques_options['.$counter.']" id="ques_options_'.$counter.'" style="height:120px;" disabled placeholder="Options(Separated by Newline )" ></textarea>
								</div>
							</div>
						</div>
					</div>
					
					<div class="clearfix"></div>
					
				</div>
        
				<div class="modal-footer">
						<a class="btn btn-primary" href="javascript:void(0);" onClick="return top.frames[0].getPageSrc(\'Save\');"><b class="fa fa-save"></b>  Save </a>
						<a class="btn btn-danger" href="javascript:void(0)" data-dismiss="modal"><b class="fa fa-times"></b>Close</a>
				</div>
         
     	</div>
   	</div>
 	</div>
	
	</form>';
		
	$html .= '
		<script>
			function closeModal() {
				top.frames[0].frames[0].frames[0].$("#macQuesTr").modal({
					show: false,backdrop: true,keyboard: true });
			}
			function doEnable(obj){
				var indx = $(obj).data(\'index\');
				if($(obj).val() == 4 ) {
					$("#d_type_"+indx+", #ques_options_"+indx).prop(\'disabled\',false);
				}
				else {
					$("#d_type_"+indx+", #ques_options_"+indx).prop(\'disabled\',true);
				}
			}
			
			$(function(){
				$("tbody#MacRegionalQuesBody").sortable({
					// Cancel the drag when selecting contenteditable items, buttons, or input boxes
					cancel: ":input,button,[contenteditable]",
					// Set it so rows can only be moved vertically
					axis: "y",
					// Triggered when the user has finished moving a row
					update: function (event, ui) {
							// sortable() - Creates an array of the elements based on the element\'s id. 
							// The element id must be a word separated by a hyphen, underscore, or equal sign. For example, <tr id=\'item-1\'>
							var data = $(this).sortable(\'serialize\');
							//alert(data); //<-- Uncomment this to see what data will be sent to the server
							// AJAX POST to server
							$.ajax({
									data: data,
									type: \'POST\',
									url: \'set_order.php?task=upMacRegSort\',
									success: function(response) {
										 //alert(response); //<- Uncomment this to see the server\'s response
									}
							});
						}
				});
			});
		</script>';
	
		return $html;
	}
	
}


?>