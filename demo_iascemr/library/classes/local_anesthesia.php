<?php
/*
*	The MIT License (MIT)
*	Distribute, Modify and Contribute under MIT License
* Use this software under MIT License
*/

class LocalAnesthesia{
	
	function mac_ques_html($data,$counter){
		
		$f_type = (int)$data['f_type'];
		$function = 'mac_ques_template_'.$f_type;
		$data['answer'] = stripslashes($data['answer']);
		$data['options'] = stripslashes($data['options']);
		$html = $f_type == 4 ? $this->$function($data['d_type']) : $this->$function();
		$yes_checked = $no_checked = '';
		if( $f_type == 1) {
			if( $data['answer'] == 'Yes') $yes_checked = 'checked';
			if( $data['answer'] == 'No') $no_checked = 'checked';
		}
		else if( $f_type == 4) {
			$list_options = $this->option_list_html($data['d_type'],$data['options'],$data['answer']);
		}
		
		$arr1 = array('{QUESTION}','{F_TYPE}','{D_TYPE}','{VALUE}','{OPTION_LIST}','{LIST_OPTIONS}','{YES_CHECKED}','{NO_CHECKED}','{PAT_QUES_ID}','{COUNTER}');
		$arr2 = array(stripslashes($data['question']),$f_type,$data['d_type'],$data['answer'],$data['options'],$list_options,$yes_checked,$no_checked,$data['pat_ques_id'],$counter);
		$html = str_replace($arr1,$arr2,$html);
		return $html ;
	}
	
	function mac_ques_template_1() {
		
		$onClickYes = "javascript:checkSingle('ques_fld_yes_{COUNTER}','ques_fld[{COUNTER}]');";
		$onClickNo = "javascript:checkSingle('ques_fld_no_{COUNTER}','ques_fld[{COUNTER}]');";
		
		$html = '';
		$html .= '<div class="inner_safety_wrap">';
		$html .= '<input type="hidden" name="pat_ques_id[{COUNTER}]" value="{PAT_QUES_ID}" />';
		$html .= '<input type="hidden" name="ques[{COUNTER}]" value="{QUESTION}" />';
		$html .= '<input type="hidden" name="ftype[{COUNTER}]" value="{F_TYPE}" />';
		$html .= '<input type="hidden" name="dtype[{COUNTER}]" value="{D_TYPE}" />';
		$html .= '<textarea class="hidden" name="list_options[{COUNTER}]">{OPTION_LIST}</textarea>';
		
		
		$html .= '<div class="row">';
		$html .= '<div class="col-md-8 col-sm-9 col-xs-8 col-lg-9">';
		$html .= '<label class="date_r">{QUESTION}</label>';
		$html .= '</div>';
		$html .= '<div class="col-md-4 col-sm-3 col-xs-4 col-lg-3 text-center">';
			$html .= '<div class="">';
				//$html .= '<div class="col-md-4 col-sm-4 col-xs-4 col-lg-4">&nbsp;</div>';
				$html .= '<div class="col-xs-6">';
					$html .= '<label for="ques_fld_yes_{COUNTER}"><span ><input type="checkbox" onClick="'.$onClickYes.'" {YES_CHECKED} value="Yes" name="ques_fld[{COUNTER}]" id="ques_fld_yes_{COUNTER}">&nbsp;Yes</span></label>';
				$html .= '</div>';
				$html .= '<div class="col-xs-6">';
					$html .= '<label for="ques_fld_no_{COUNTER}"><span><input type="checkbox" onClick="'.$onClickNo.'" {NO_CHECKED} value="No" name="ques_fld[{COUNTER}]" id="ques_fld_no_{COUNTER}" />&nbsp;No</span></label>';
				$html .= '</div>';
			$html .= '</div> ';
		$html .= '</div>';
		
		$html .= '</div>';
		$html .= '</div>';
		
		return $html;
	}
	
	function mac_ques_template_2() {
		
		$html = '';
		$html .= '<div class="inner_safety_wrap">';
		
		$html .= '<input type="hidden" name="pat_ques_id[{COUNTER}]" value="{PAT_QUES_ID}" />';
		$html .= '<input type="hidden" name="ques[{COUNTER}]" value="{QUESTION}" />';
		$html .= '<input type="hidden" name="ftype[{COUNTER}]" value="{F_TYPE}" />';
		$html .= '<input type="hidden" name="dtype[{COUNTER}]" value="{D_TYPE}" />';
		$html .= '<textarea class="hidden" name="list_options[{COUNTER}]">{OPTION_LIST}</textarea>';
		
		$html .= '<div class="row">';
			$html .= '<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">';
			$html .= '<label class="date_r">{QUESTION}</label>';
			$html .= '</div>';
			$html .= '<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">';
				$html .= '<input type="textbox" name="ques_fld[{COUNTER}]" class="form-control" id="ques_fld_{COUNTER}" value="{VALUE}" />';
			$html .= '</div>';
		
		$html .= '</div>';
		$html .= '</div>';
		
		return $html;
	}
	
	function mac_ques_template_3() {
		
		$html = '';
		$html .= '<div class="inner_safety_wrap">';
		$html .= '<input type="hidden" name="pat_ques_id[{COUNTER}]" value="{PAT_QUES_ID}" />';
		$html .= '<input type="hidden" name="ques[{COUNTER}]" value="{QUESTION}" />';
		$html .= '<input type="hidden" name="ftype[{COUNTER}]" value="{F_TYPE}" />';
		$html .= '<input type="hidden" name="dtype[{COUNTER}]" value="{D_TYPE}" />';
		$html .= '<textarea class="hidden" name="list_options[{COUNTER}]">{OPTION_LIST}</textarea>';
		
		$html .= '<div class="row">';
			$html .= '<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">';
			$html .= '<label class="date_r">{QUESTION}</label>';
			$html .= '</div>';
			$html .= '<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">';
				$html .= '<textarea name="ques_fld[{COUNTER}]" id="ques_fld_{COUNTER}" class="form-control">{VALUE}</textarea >';
			$html .= '</div>';
		
		$html .= '</div>';
		$html .= '</div>';
		
		return $html;
	}
	
	function mac_ques_template_4($d_type = false) {
		
		$html = '';
		
		$html = '';
		$html .= '<div class="inner_safety_wrap">';
		$html .= '<input type="hidden" name="pat_ques_id[{COUNTER}]" value="{PAT_QUES_ID}" />';
		$html .= '<input type="hidden" name="ques[{COUNTER}]" value="{QUESTION}" />';
		$html .= '<input type="hidden" name="ftype[{COUNTER}]" value="{F_TYPE}" />';
		$html .= '<input type="hidden" name="dtype[{COUNTER}]" value="{D_TYPE}" />';
		$html .= '<textarea class="hidden" name="list_options[{COUNTER}]">{OPTION_LIST}</textarea>';
		
		$html .= '<div class="row">';
		if( $d_type == 3 ) {
			$html .= '<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">';
				$html .= '<label class="date_r">{QUESTION}</label>';
			$html .= '</div>';
			
			$html .= '<div class="col-md-2 col-sm-2 col-xs-2 col-lg-2">&nbsp;</div>';
			$html .= '<div class="col-md-10 col-sm-10 col-xs-10 col-lg-10">';
				$html .= '{LIST_OPTIONS}';
			$html .= '</div>';
		
		} else{
			
			$name = 'ques_fld[{COUNTER}]'.($d_type==2?'[]':'');
			$multiple = ($d_type==2?'multiple':'');
			$class = ($d_type==2?'selectpicker':'form-control minimal');
			$html .= '<div class="col-md-8 col-sm-7 col-xs-7 col-lg-8">';
				$html .= '<label class="date_r">{QUESTION}</label>';
			$html .= '</div>';
			$html .= '<div class="col-md-4 col-sm-5 col-xs-5 col-lg-4">';
				$html .= '<select name="'.$name.'" id="ques_fld_{COUNTER}" class="'.$class.'" '.$multiple.' title="Select" '.($multiple?'data-header="Select"':'').'>';
				if( !$multiple ) $html .= '<option value="">Select</option>';
				$html .= '{LIST_OPTIONS}';
				$html .= '</select>';
			$html .= '</div>';
		}
		$html .= '</div>';	
		$html .= '</div>';
		
		return $html;
	}
	
	function option_list_html($type,$op_list,$sel_list) {
		
		$optionsArr = explode("\n",$op_list);
		$optionsSelArr = explode(";",$sel_list);
		
		$optionsArr = array_filter($optionsArr);//pre($optionsArr);
		$optionsSelArr = array_filter($optionsSelArr);//pre($optionsSelArr);
		
		$optionsHtml = '';
		if( is_array($optionsArr) && count($optionsArr) > 0 ) {
			foreach($optionsArr as $key => $val) {
				$val = trim($val);
				//echo '=='.$val . '=--='. (in_array($val,$optionsSelArr) ? 'TRUE' : 'FALSE');
				if( $type == 3 ) {
					$checked = in_array($val,$optionsSelArr) ? 'checked' : '';
					$onClick = "javascript:checkSingle('ques_fld_{COUNTER}_".$key."','ques_fld[{COUNTER}]');";
					$optionsHtml .= '<label for="ques_fld_{COUNTER}_'.$key.'">';
					$optionsHtml .= '<span><input type="checkbox" name="ques_fld[{COUNTER}]" onClick="'.$onClick.'" '.$checked.' id="ques_fld_{COUNTER}_'.$key.'" value="'.$val.'"></span>&nbsp;'.$val;
					$optionsHtml .= '</label><br>';
				}
				else {
					$sel = in_array($val,$optionsSelArr) ? 'selected' : '';
					$optionsHtml .= '<option value="'.$val.'" '.$sel.'>'.$val.'</option>';
				}
			}
			
		}
		return $optionsHtml;	
	}
	
	function mac_ques_print_html($pConfId) {
		
		$pConfId = (int)$pConfId;
		if( !$pConfId ) return '';
		
		$html = '';
		$query = "Select id as pat_ques_id,question, f_type, d_type, list_options as options, answer From patient_mac_regional_questions Where confirmation_id = ".$pConfId." Order By id";
		$sql = imw_query($query) or die(imw_error());
		$cnt = imw_num_rows($sql);
		if( $cnt > 0 ) {

			$html.=	
			'<tr>
				<td colspan="2" style="width:700px;" class="bgcolor bold bdrbtm">Additional Questions</td>
			</tr>
			<tr>
				<td colspan="2" style="width:700px;" valign="top" >
					<table style="width:100%;" cellpadding="0" cellspacing="0">
						<tr>';
						$looper = 0;
						while ($rowAQ = imw_fetch_assoc($sql)) {
							$looper++;
							$f_type = $rowAQ['f_type'];
							$answerAQ = htmlentities(stripslashes($rowAQ['answer']));

							$class = ($looper%2==0) ? 'bdrbtm' : 'bdrBtmRght';
							if( $f_type == 1 ) {
								$html.=	'
								<td class="'.$class.'" style="width:50%">
									<table style="width:100%;" cellpadding="0" cellspacing="0">
											<tr>
												<td style="width:85%;" class="bold">'.stripslashes($rowAQ['question']).'</td>
												<td style="width:15%;">'.($answerAQ?$answerAQ:'______').'</td>
											</tr>
										</table>
								</td>';
							}
							else {
								if( $f_type == 4) {
									$arr = explode(";",$answerAQ);
									$answerAQ = implode(",",$arr);
								}
								$html.=	'
									<td style="width:50%;" valign="top" class="'.$class.'">
										<table style="width:100%;" cellpadding="0" cellspacing="0">
											<tr><td style="width:100%;" class="bold">'.stripslashes($rowAQ['question']).'</td></tr>
											<tr><td style="width:100%;">'.($answerAQ?$answerAQ:'____________________________________').'</td></tr>
										</table>
									</td>';
							}
							
							if( $looper%2 == 0 )
								$html.= '</tr><tr>';
						}

			$html.=	
			'			</tr>
					</table>
				</td>
			</tr>';
		
		}	
		
		return $html;
	}
}
?>