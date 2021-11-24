<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php
class SmartTags{

	private $pid;	
	
	public function __construct($pid=0){		
		$this->pid = (!empty($pid)) ? $pid : $_SESSION['patient'] ;
	}

	function get_smartTags_array($id=0){
		$query = "SELECT id, tagname FROM smart_tags WHERE under=".intval($id)." AND status=1";
		$result = imw_query($query);
		if($result && imw_num_rows($result)>0){
			$arrResult = array();
			while($rs = imw_fetch_array($result)){
				$id = $rs['id'];
				$tagname = $rs['tagname'];
				$arrResult[$id] = $tagname;
			}
			return $arrResult;		
		}else{
			return false;
		}
	}
	
	function getTagOptions($is_return = 0){
		$under = (isset($_GET['id']) &&  intval($_GET['id'])!= 0) ? intval($_GET['id']) : $_GET['id'];
		$head_title = '';
		$table_data = '<table class="table table-bordered table-striped" id="tableSubTags">';
		if((int)$under > 0){
			$tagname_rs = sqlQuery("SELECT tagname FROM smart_tags WHERE id=".$under);
			$tagname = html_entity_decode(stripslashes($tagname_rs['tagname']));
			$subTag_query = "SELECT id, tagname, under FROM smart_tags WHERE under = ".$under." AND status=1 ORDER BY tagname";
			$subTag_result = imw_query($subTag_query);
			
			$head_title = 'Option under '.$tagname;
			$subTag_found = imw_num_rows($subTag_result);
			$alt_class = ' class="alt"';
			
			if($subTag_result){			
				if($subTag_found>0){
					$nofound = 0;
					while($subTag_rs = imw_fetch_array($subTag_result)){
						$subTagname = trim(html_entity_decode(stripslashes($subTag_rs['tagname'])));
						$subTagid = stripslashes($subTag_rs['id']);
						$td_id = 'txtTagOpt_'.$under.'_'.$subTagid;
						$table_data .= '
						<tr><td>
								<div class="checkbox">
									<input value="'.$subTagname.'" type="checkbox" name="chkSmartTagOptions" id="'.$td_id.'">
									<label for="'.$td_id.'">'.$subTagname.'</label>
								</div>
						</td></tr>';
						if($alt_class==' class="alt"'){$alt_class='';}else{$alt_class=' class="alt"';}	
					}//end of while.			
				}
				else{
						$table_data .= '
						<tr><td class="text-center bg-warning">No options defined under this SmartTag.</td></tr>';
						$nofound = 1;
				}
			}
				
		}
		elseif($under == "aPatRefPhy"){
			
			$head_title = 'Please Select Patient Referring Physician';
			
			$qrySelpatRefPhy = "select refPhy.physician_Reffer_id, TRIM(CONCAT(refPhy.Title, ' ', refPhy.LastName, ', ', refPhy.FirstName, ' ', refPhy.MiddleName)) as refName
								from patient_multi_ref_phy pmrf INNER JOIN 
								refferphysician refPhy ON pmrf.ref_phy_id = refPhy.physician_Reffer_id 
								where pmrf.patient_id = '".$this->pid."' and pmrf.phy_type = '1' and pmrf.status = '0'";
			$rsSelpatRefPhy = imw_query($qrySelpatRefPhy);
			if(imw_num_rows($rsSelpatRefPhy) > 0){
				$nofound = 0;
				while($rowSelpatRefPhy = imw_fetch_array($rsSelpatRefPhy)){
					$subTagname = trim(html_entity_decode(stripslashes($rowSelpatRefPhy['refName'])));
					$subTagid = stripslashes($rowSelpatRefPhy['physician_Reffer_id']);
					$td_id = 'txtTagOpt_'.$under.'_'.$subTagid;
					$table_data .= '
					<tr><td>
							<div class="checkbox">
								<input value="'.$subTagname.'" type="checkbox" name="chkSmartTagOptions" id="'.$td_id.'"></td>
								<label for="'.$td_id.'"></label>
							</div>
					</td></tr>';
				}//end of while.
			}
			else{
				$table_data .= '
				<tr><td class="text-center bg-warning ">Multiple Patient Referring Physician are not found!</td></tr>';
				$nofound = 1;
			}
		}
		elseif($under == "aPatPCP"){
			$arrTemp = array();
			$head_title = 'Please Select Patient Primary Care Physician';
			
			$qrySelpatPCPMedHx = "select refPhy.physician_Reffer_id, TRIM(CONCAT(refPhy.Title, ' ', refPhy.LastName, ', ', refPhy.FirstName, ' ', refPhy.MiddleName)) as refName
								from patient_multi_ref_phy pmrf INNER JOIN 
								refferphysician refPhy ON pmrf.ref_phy_id = refPhy.physician_Reffer_id 
								where pmrf.patient_id = '".$this->pid."' and pmrf.phy_type IN (3,4) and pmrf.status = '0'";
			$rsSelpatPCPMedHx = imw_query($qrySelpatPCPMedHx);
			if(imw_num_rows($rsSelpatPCPMedHx) > 0){
				$nofound = 0;
				while($rowSelpatPCPMedHx = imw_fetch_array($rsSelpatPCPMedHx)){
					if(in_array((int)$rowSelpatPCPMedHx['physician_Reffer_id'], $arrTemp) == false){
						$subTagname = trim(html_entity_decode(stripslashes($rowSelpatPCPMedHx['refName'])));
						$subTagid = stripslashes($rowSelpatPCPMedHx['physician_Reffer_id']);
						$td_id = 'txtTagOpt_'.$under.'_'.$subTagid;
						$table_data .= '
						<tr><td>
								<div class="checkbox">
									<input value="'.$subTagname.'" type="checkbox" name="chkSmartTagOptions" id="'.$td_id.'">
									<label for="'.$td_id.'">'.$subTagname.'</label>
								</div>
						</td></tr>';
						$arrTemp[] = $rowSelpatPCPMedHx["physician_Reffer_id"];
					}
				}//end of while.
			}
			else{
				$table_data .= '
				<tr><td class="text-center bg-warning">Multiple Patient Primary Care Physician are not found!</td></tr>';
				$nofound = 1;
			}
			
		}
		elseif($under == "aPatCoManPhy"){
			$head_title = 'Please Select Patient Co-Managed Physician';
			$qrySelpatCoPhy = "select refPhy.physician_Reffer_id, TRIM(CONCAT(refPhy.Title, ' ', refPhy.LastName, ', ', refPhy.FirstName, ' ', refPhy.MiddleName)) as refName
								from patient_multi_ref_phy pmrf INNER JOIN 
								refferphysician refPhy ON pmrf.ref_phy_id = refPhy.physician_Reffer_id 
								where pmrf.patient_id = '".$this->pid."' and pmrf.phy_type = '2' and pmrf.status = '0'";
			$rsSelpatCoPhy = imw_query($qrySelpatCoPhy);
			if(imw_num_rows($rsSelpatCoPhy) > 0){
				$nofound = 0;
				while($rowSelpatCoPhy = imw_fetch_array($rsSelpatCoPhy)){
					$subTagname = trim(html_entity_decode(stripslashes($rowSelpatCoPhy['refName'])));
					$subTagid = stripslashes($rowSelpatCoPhy['physician_Reffer_id']);
					$td_id = 'txtTagOpt_'.$under.'_'.$subTagid;
					$table_data .= '
					<tr><td>
							<div class="checkbox">
								<input value="'.$subTagname.'" type="checkbox" name="chkSmartTagOptions" id="'.$td_id.'">
								<label for="'.$td_id.'">'.$subTagname.'</label>
					</td></tr>';
				}//end of while.
			}
			else{
				$table_data .= '
				<tr><td class="text-center bg-warning">Multiple Patient Co-Managed Physician are not found!</td></tr>';
				$nofound = 1;
			}
		}
		
		$table_data .= '</table>';
		$footer_btn_click = (!$nofound) ? 'replace_tag_with_options();' : '$(\'#div_smart_tags_options\').hide();"' ;
		$footer_btn_val 	= (!$nofound) ? 'Done' : 'Close' ;
		$footer_btn_class	=	(!$nofound) ? 'btn-success' : 'btn-danger' ;
		
		$footer_btn = '<input type="button" id="btn_subtag_save" class="btn '.$footer_btn_class.'" value="'.$footer_btn_val.'" onclick="'.$footer_btn_click.'">';
		
		if( $is_return )
		{
			$return = array('title' => $head_title, 'data'=>$table_data, 'footer_btn' => $footer_btn);
			echo json_encode($return);
		}
		else
		{
			echo '<div class="section_header"><span class="closeBtn" onClick="$(\'#div_smart_tags_options\').hide();"></span>'.$head_title.'</div><div style="height:200px; overflow:auto; overflow-x:hidden;">'.$table_data.'</div><div class="mt10 text-center">'.$footer_btn.'</div>';
		}
		
	}
	
	
}//end of class.
?>