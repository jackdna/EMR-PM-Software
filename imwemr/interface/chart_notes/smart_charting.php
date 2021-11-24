<?php 
	require_once(dirname(__FILE__).'/../../config/globals.php');
	
	include_once($GLOBALS['srcdir'].'/classes/smart_charting.class.php');
	include_once($GLOBALS['incdir'].'/chart_notes/chart_globals.php');
	$pid = $_SESSION['patient'];
	$authId = $_SESSION["authId"];
	
	$smart_chart_obj = new Smart_chart($pid,$authId,$_REQUEST['dos']);
	//Get Task List and create Assessment arrays
	$smart_chart_obj->get_task_list();
	
	//Get Community Assessment HTML
	$strCommuAssess = $smart_chart_obj->get_community_html();
	
	//Get Physician Assessment HTML
	$strPhyAssess = $smart_chart_obj->get_physician_html();

	//Get Dynamic Assessment HTML 
	$strDynAssess = $smart_chart_obj->get_dynamic_assessment_html();
	
	
	//Add Header
	if(!empty($smart_chart_obj->strAssessonly)){
		$smart_chart_obj->strAssessonly = "<form name=\"frmsc\" action=\"saveCharts.php\" method=\"post\" >".
			   "<input type=\"hidden\" name=\"elem_saveForm\" value=\"Smart Charting\">".
			   "<div >".
			   "<table >".
			   "<tr >".
			   "<th>".
			   "Assessments without symptoms ".
			   //"<a href=\"javascript:void(0);\" class=\"txt_11b white_color\" onclick=\"document.frmsc.elem_btncancel.click();\">Close</a>".
			   "</th>".
			   "</tr>".
			   "<tr>".
			   "<td>".
				   "<div id=\"sc_con\" style='height:".($height-100)."px'>".
				   "<table class=\"tbl_smrtchart\" border=\"1\" >".
				   "<tr class=\"section_header\">".
				   "<th >Assessment</th>".
				   "<th >Site</th>".						   
				   "</tr>".
				   $smart_chart_obj->strAssessonly.
				//	$strAsses.
				   "</table>".
				   "</div>".
			   "</td>".
			   "</tr>".

			   "<tr>".
			   "<td align=\"center\">".
			   "<input name=\"elem_btnok\" type=\"button\"  class=\"dff_button\"  id=\"elem_btnok\"
						onClick=\"sc_userPress(1);\" value=\"Done\" align=\"bottom\" /> ".
			   "<input name=\"elem_btncancel\" type=\"button\"  class=\"dff_button\"  id=\"elem_btncancel\"
						onClick=\"sc_userPress(0);\" value=\"Cancel\" align=\"bottom\" />".
			   "</td>".
			   "</tr>".
			   "</table>".
			   "</div>".
			   "</form>";
	}
	
	$str = '';
	
	//Search Dropdown Arr
	$sql = "SELECT field_val  FROM chart_global_settings WHERE field_name ='exam_names_arr'";
	$row = imw_fetch_assoc(imw_query($sql));
	$dropdown_arr = unserialize(stripslashes(base64_decode($row['field_val'])));
	if(!empty($smart_chart_obj->strTasks) || !empty($smart_chart_obj->strAssessonly)){
		//Physician Block
		$name_arr = $smart_chart_obj->get_username_by_id(array($_SESSION['authId']));
		$physician_block = '
		<div class="panel panel-default">
			<div class="panel-heading">
				 <h4 class="panel-title pointer" data-toggle="collapse" data-parent="#accordion_smart_chart" href="#divPhyBlock">
					<span>Assessment - '.$name_arr[$_SESSION['authId']]['full'].'</span>
				</h4>	
			</div>
			<div id="divPhyBlock" class="panel-collapse collapse" style="max-height:150px;overflow-y:scroll">
				<div class="panel-body">
					'.$strPhyAssess.'	
				</div>
			</div>	
		</div>';
		
		//Community Block
		$community_block = '
		<div class="panel panel-default">
			<div class="panel-heading">
				 <h4 class="panel-title pointer" data-toggle="collapse" data-parent="#accordion_smart_chart" href="#divComBlock">
					<span>Assessment - Community</span>
				</h4>	
			</div>
			<div id="divComBlock" class="panel-collapse collapse" style="max-height:150px;overflow-y:scroll">
				<div class="panel-body">
					'.$strCommuAssess.'	
				</div>
			</div>	
		</div>';
		
		//Dynamic Assessment Block
		$dynamic_assessment_block = '
		<div class="panel panel-default">
			<div class="panel-heading">
				 <h4 class="panel-title pointer" data-toggle="collapse" data-parent="#accordion_smart_chart" href="#divDynBlock">
					<span>Assessment - Dynamic</span>
				</h4>	
			</div>
			<div id="divDynBlock" class="panel-collapse collapse" style="max-height:150px;overflow-y:scroll">
				<div class="panel-body">
					'.$strDynAssess.'	
				</div>
			</div>	
		</div>';
		
		//Left Sidebar
		$left_side_bar .= '
		<div class="col-sm-3">
			'.$smart_chart_obj->strTasks.'
		</div>';
		
		//Search Bar
		$search_bar = '
			<div class="smtchating">
			<div class="input-group">
				<input id="elem_search_symptopms" placeholder="Search" class="form-control">
				<div class="input-group-btn">
					<button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-menu-down"></span></button>
					'.$smart_chart_obj->sc_getExamMenuOptions($dropdown_arr, "sc_menu_exams").'	
				</div>	
			</div>
			</div>';
		
		//Right side content
		$right_side_content .= '
		<div class="col-sm-9">
			<div class="row">
					<div class="col-sm-6">
						'.$search_bar.'	
					</div>
					<div class="col-sm-12 pt10">
						<div class="panel-group" id="accordion_smart_chart">
							'.$physician_block.'
							'.$community_block.'
							'.$dynamic_assessment_block.'
						</div>	
					</div>
				</form>
			</div>
		</div>';
		
		$footer_buttons = '
		<div class="text-center col-sm-12">
			<input name="elem_btnok" type="button"  class="btn btn-success"  id="elem_btnok" onClick="sc_userPress(1);" value="Done" />
			<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
			<input name="elem_btncancel" type="button"  class="btn btn-danger"  id="elem_btncancel" onClick="sc_userPress(0);" value="Cancel" />
		</div>';
		
		
		//Modal Body
		$str .='<div id="div_sc_con_detail1" class="modal fade" role="dialog" >';
			$str .='<div class="modal-dialog modal-lg">';
				$str .= '<div class="modal-content">';
					$str .= '<form name="frmsc" action="saveCharts.php" method="post" autocomplete="off">';
						$str .= '<input type="hidden" name="elem_saveForm" value="Smart Charting">';
						$str .= '<input type="hidden" name="elem_sc_icd10" value="1">';
						$str .= '<div class="modal-header bg-primary">';
							$str .= ' <button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Smart Charting</h4>';
						$str .= '</div>';
						$str .= '<div class="modal-body" style="max-height:450px; overflow:hidden; overflow-y:auto;">';
							$str .= '<div class="row">';
								$str .= $left_side_bar.$right_side_content;
							$str .= '</div>';
						$str .= '</div>';
						$str .= '<div class="modal-footer ad_modal_footer" id="module_buttons">';
							$str .= $footer_buttons;
						$str .= '</div>';
					$str .= '</form>';		
				$str .= '</div>';
			$str .= '</div>';
		$str .= '</div>';
		
		$style = '
		<style>
			#div_sc_con_detail1 {font-family: Verdana, Arial, Helvetica, sans-serif;font-size:12px;font-weight: normal;color: #000000;}
			#div_sc_con_detail1 input[type=checkbox]{  display:none; }
			.greyAll_v2{background:Lightgrey;}
			#div_sc_con_detail1 label[for]{ cursor:pointer; text-align:left; border:2px solid transparent;} 
			#div_sc_con_detail1 input[type=checkbox]:checked + label { background-color:white; font-size:12px; font-weight:bold; color:Red; border:2px solid #1569C7}
			#div_sc_con_detail1 input[type=checkbox].greyAll_v2:checked+label{ background-color:lightgrey;border:2px solid #1569C7 }
			#div_sc_con_detail1 table.elems_tbl{ border-spacing: 0px;   border-collapse: separate;padding:0px;border:0px;}
			table.elems_tbl td{border:0px solid green;padding:0px;}
			#div_sc_con_detail1 .dropdown-submenu {
				position: relative;
			}
			.smtchating .dropdown-menu {
				max-height: inherit;
				overflow:inherit;
				width: 200px;
			  
			}
			.modal-lg {
				width: 90%;
			}
			
			#div_sc_con_detail1 .dropdown-submenu .dropdown-menu {
				top: 0;
				left: 100%;
				margin-top: -1px;
			}
		</style>';
		$str .= $style;
	}
		$sql = "SELECT field_val  FROM chart_global_settings WHERE field_name ='exam_search_arr'";
		$row = imw_fetch_assoc(imw_query($sql));
		$arr = unserialize(stripslashes(base64_decode($row['field_val'])));
		
		//exm findings
		$oExmXml = new ExamXml();
		list($ar_ee_find_full, $ar_ee_finding) = $oExmXml->get_exm_ext_findings('','All');
		if(count($ar_ee_find_full) > 0){ $arr = array_merge($arr, $ar_ee_find_full); }
		
		
	echo json_encode(array("str"=>$str, "armenu"=>array_values( $arr )));
	exit();
?>