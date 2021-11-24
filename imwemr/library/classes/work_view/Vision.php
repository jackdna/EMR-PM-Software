<?php
/*
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
*/
?>
<?php
/*
File: Vision.php
Coded in PHP7
Purpose: This class file provides functions to manage vision exam in work view.
Access Type : Include file
*/
?>
<?php
//Vision.php
class Vision extends ChartNote{
	private $examName,$tbl,$xmlFileOd,$xmlFileOs,$divSE;
	public function __construct($pid,$fid=""){
		parent::__construct($pid,$fid);
		$this->tbl="chart_vis_master";
		$this->examName="Vision";
	}

	function isRecordExists($a="",$b="",$c="",$d=""){
		return parent::isRecordExists($this->tbl,"form_id", "patient_id");
	}

	function getRecord($sel=" * ",$a="",$b="",$c="",$d=""){
		return parent::getRecord($this->tbl,$sel,"form_id", "patient_id");
	}

	function getLastRecord($sel=" * ",$LF="0",$dt="", $a="", $b="", $c=""  ){
		return parent::getLastRecord($this->tbl,"form_id",$LF,$sel,$dt);
	}

	function insertNew(){
		if(!empty($this->pid) && !empty($this->fid)){
		$sql = "INSERT INTO ".$this->tbl." (id, form_id, patient_id)
				VALUES (NULL, '".$this->fid."','".$this->pid."') ";
		$return=sqlInsert($sql);
		}else{ $return=0; }
		return $return;
	}

	function carryForward(){
		$res = $this->getLastRecord(" c2.id ","1");
		if($res!=false){
			$Id_LF = $res["id"];
		}
		//Insert
		$insertId = $this->insertNew();
		//CopyLF
		$ignoreFlds = "form_id, status_elements, ut_elem";
		if(!empty($Id_LF)){
			$this->carryForwardExe($this->tbl,$insertId,$Id_LF,$ignoreFlds,$this->examName,'id');
			
			//chart_acuity
			$sql = "INSERT INTO chart_acuity SELECT NULL,'".$insertId."',exam_date,uid,sec_name,sec_indx,snellen,ex_desc,
							sel_od,txt_od,sel_os,txt_os,sel_ou,txt_ou FROM chart_acuity WHERE id_chart_vis_master = '".$Id_LF."' ";
			$row = sqlQuery($sql);
			
			//chart_ak
			$sql = "INSERT INTO chart_ak SELECT NULL,'".$insertId."',exam_date,uid,k_od,slash_od,x_od,k_os,slash_os,x_os,k_type,ex_desc FROM chart_ak WHERE id_chart_vis_master = '".$Id_LF."' ";			
			$row = sqlQuery($sql);
			
			//chart_sca
			$sql = "INSERT INTO chart_sca SELECT NULL,'".$insertId."',exam_date,uid,sec_name,s_od,c_od,a_od,sel_od,s_os,c_os,a_os,sel_os,ex_desc,ar_ref_place FROM chart_sca WHERE id_chart_vis_master = '".$Id_LF."' ";
			$row = sqlQuery($sql);
			
			//chart_exo
			$sql = "INSERT INTO chart_exo SELECT NULL,'".$insertId."', exam_date, uid, pd, pd_od, pd_os FROM chart_exo WHERE id_chart_vis_master = '".$Id_LF."' ";
			$row = sqlQuery($sql);
			
			//chart_bat
			$sql = " INSERT INTO chart_bat SELECT NULL,'".$insertId."',exam_date,uid,nl_od, l_od, m_od, h_od, nl_os, l_os, m_os, h_os, nl_ou, l_ou, m_ou, h_ou, ex_desc FROM chart_bat WHERE id_chart_vis_master = '".$Id_LF."'  ";
			$row = sqlQuery($sql);
			
			//chart_pam
			$sql = "INSERT INTO chart_pam SELECT NULL,'".$insertId."',exam_date,uid, txt1_od, txt2_od, txt1_os, txt2_os, txt1_ou, txt2_ou, sel1, sel2, ex_desc, pam  FROM chart_pam WHERE id_chart_vis_master = '".$Id_LF."'  ";
			$row = sqlQuery($sql);
			
			//chart_pc_mr
			$sql_in ="";
			$sql = "SELECT * FROM chart_pc_mr WHERE id_chart_vis_master = '".$Id_LF."'  ";
			$rez = sqlStatement($sql);
			for($i=1; $row=sqlFetchArray($rez);$i++){
				$id = $row["id"];
				$sql = "INSERT INTO chart_pc_mr SELECT NULL,form_id,patient_id,exam_date,provider_id,ex_type,ex_number,pc_distance,
								pc_near,mr_none_given, mr_cyclopegic, mr_pres_date, mr_ou_txt_1, mr_type, ex_desc, prism_desc,
									uid, delete_by, strhash, '".$insertId."' FROM chart_pc_mr WHERE id='".$id."' ";
				$tmp_insert = sqlInsert($sql);

				$sql = "SELECT * FROM chart_pc_mr_values WHERE chart_pc_mr_id = '".$id."'  ";
				$rez1 = sqlStatement($sql);
				for($i=1; $row1=sqlFetchArray($rez1);$i++){					
					$sql_in .= "(NULL,'".$tmp_insert."','".$row1["site"]."','".$row1["sph"]."','".$row1["cyl"]."','".$row1["axs"]."','".$row1["ad"]."','".$row1["prsm_p"]."','".$row1["prism"]."','".$row1["slash"]."',
							'".$row1["sel_1"]."','".$row1["sel_2"]."','".$row1["ovr_s"]."','".$row1["ovr_c"]."','".$row1["ovr_v"]."','".$row1["ovr_a"]."','".$row1["txt_1"]."','".$row1["txt_2"]."','".$row1["sel2v"]."'),";					
				}			
			}
			
			//chart_pc_mr_values
			if(!empty($sql_in)){
			$sql_in = trim($sql_in,",");
				$sql = "Insert INTO chart_pc_mr_values VALUES ".$sql_in;
				$row = sqlQuery($sql);
			}	
		}	
	}
	
	function isVisualAcuityDone(){
		$examVision = 0;
		$sql = "
			SELECT
			c2.status_elements,
			c3.sel_od, c3.txt_od, c3.sel_os, c3.txt_os,  c3.sec_indx, c3.sec_name, c3.ex_desc	
			FROM chart_vis_master c2			
			LEFT JOIN chart_acuity c3 ON c3.id_chart_vis_master = c2.id 
			WHERE c2.form_id = '".$this->fid."' 
			AND (c3.sec_name = 'Distance') AND sec_indx IN (1,2)
			ORDER BY c3.sec_indx
		";
		$rez = sqlStatement($sql); 
		for($i=1; $row=sqlFetchArray($rez); $i++){			
			$c=$row["sec_indx"];
			$statusElements = $row["status_elements"];
			if(!empty($row["sel_od"]) && (strpos($statusElements,"elem_visDisOdSel".$c."=1") !== false) ||
				 !empty($row["sel_os"]) && (strpos($statusElements,"elem_visDisOsSel".$c."=1") !== false) ||
				 (!empty($row["txt_od"]) && (trim($row["txt_od"]) != "20/") && (strpos($statusElements,"elem_visDisOdTxt".$c."=1") !== false) ) ||
				 !empty($row["ex_desc"]) 
			){
				$examVision = 1;
			}
		}		
		return $examVision;
	}
	
	function isRefraction($dx=0, $flggivenonly=false)
	{
		$flag = false;		
		$dxCode = "";
		$eye="";		
		
		$sql = "SELECT 
			c0.status_elements,
			c1.mr_none_given,  c1.provider_id, c1.ex_number,
			c2.sph as sph_r, c2.cyl as cyl_r, c2.axs as axs_r, c2.ad as ad_r, 						
			c3.sph as sph_l, c3.cyl as cyl_l, c3.axs as axs_l, c3.ad as ad_l			
			FROM  chart_vis_master c0
			LEFT JOIN chart_pc_mr c1 ON c1.id_chart_vis_master = c0.id
			LEFT JOIN chart_pc_mr_values c2 ON c1.id = c2.chart_pc_mr_id AND c2.site='OD'
			LEFT JOIN chart_pc_mr_values c3 ON c1.id = c3.chart_pc_mr_id AND c3.site='OS'	
			WHERE c0.form_id='".$this->fid."' AND c1.ex_type='MR' AND c1.delete_by='0'  
			Order By ex_number;
			"; 
			
		//exit($sql);
			 //"ORDER BY form_id DESC ".
			 //"LIMIT 0,1 ";		 		

		$rez = sqlStatement($sql); 
		for($i=1; $row=sqlFetchArray($rez);$i++){
			$statusElements = $row["status_elements"];
			if(
				(strpos($statusElements,"elem_providerId=1") !== false && !empty($row["provider_id"])) &&
				($flggivenonly!=true || strpos($row["mr_none_given"],"MR ".$row["ex_number"]) !== false) &&
				(!empty($row["sph_r"]) ||
				 !empty($row["cyl_r"]) ||
				 !empty($row["axs_r"]) ||
				 !empty($row["ad_r"]) ||
				 !empty($row["sph_l"]) ||
				 !empty($row["cyl_l"]) ||
				 !empty($row["axs_l"]) ||
				 !empty($row["ad_l"])
				)				
			){
				$flag = true;				
				
				if(!empty($row["sph_r"]) ||
				 !empty($row["cyl_r"]) ||
				 !empty($row["axs_r"]) ||
				 !empty($row["ad_r"])){
					$eye="OD";
				}

				if(!empty($row["sph_l"]) ||
				 !empty($row["cyl_l"]) ||
				 !empty($row["axs_l"]) ||
				 !empty($row["ad_l"])){
					$eye=($eye=="OD") ? "OU" : "OS" ;
				}
				
				
				$tOd = (trim($row["ad_r"]) != "+") ? $row["ad_r"] : "";
				$tOs = (trim($row["ad_l"]) != "+") ? $row["ad_l"] : "";				
				if(!empty($tOd) || (!empty($tOs))){
					$dxCode = "367.4";	
				}else if((strpos($row["sph_r"],"-") !== false) || (strpos($row["sph_l"],"-") !== false)){					
					$dxCode = "367.1";
				}else if(!empty($row["sph_r"]) || !empty($row["sph_l"])){
					$dxCode = "367.0";
				}	
				break;
			}		
		}
		
		return ($dx==0) ? $flag : array($flag,$dxCode,$eye);
	}
	
	static function getArrDropDown(){
		$arr = array();
		//*					  
		//Vision snellan
		$arr["Snellan"] = array('Snellen Letters'=>array('Snellen Letters',$arr,'Snellen Letters'),
						'Snellen Number'=>array('Snellen Number',$arr,'Snellen Number'),
						'Snellen L'=>array('Snellen L',$arr,'Snellen L'),
						'Snellen S'=>array('Snellen S',$arr,'Snellen S'),
						'HOTV'=>array('HOTV',$arr,'HOTV'),
						'HOTV match'=>array('HOTV match',$arr,'HOTV match'),
						'HOTV c CB'=>array('HOTV c CB',$arr,'HOTV c CB'),					
						'HOTV s CB'=>array('HOTV s CB',$arr,'HOTV s CB'),
						'Lea c CB'=>array('Lea c CB',$arr,'Lea c CB'),
						'Lea s CB'=>array('Lea s CB',$arr,'Lea s CB'),
						'Lea Symbols'=>array('Lea Symbols',$arr,'Lea Symbols'),
						'Lea Numbers'=>array('Lea Numbers',$arr,'Lea Numbers'),
						'Allen Card'=>array('Allen Card',$arr,'Allen Card'),
						'PLT-Teller'=>array('PLT-Teller',$arr,'PLT-Teller'),
						'Pics'=>array('Pics',$arr,'Pics'),
						'Pics match'=>array('Pics match',$arr,'Pics match'),
						
						'BVAT Letters'=>array('BVAT Letters',$arr,'BVAT Letters'),
						'BVAT Letters c CB'=>array('BVAT Letters c CB',$arr,'BVAT Letters c CB'),
						'BVAT Numbers'=>array('BVAT Numbers',$arr,'BVAT Numbers'),
						'BVAT Numbers c CB'=>array('BVAT Numbers c CB',$arr,'BVAT Numbers c CB'),
						'BVAT Allen'=>array('BVAT Allen',$arr,'BVAT Allen'),
						'BVAT Allen CB'=>array('BVAT Allen CB',$arr,'BVAT Allen CB'),
						'BVAT HOTV'=>array('BVAT HOTV',$arr,'BVAT HOTV'),
						'BVAT HOTV c CB'=>array('BVAT HOTV c CB',$arr,'BVAT HOTV c CB'),
						
						'Sloan Letters'=>array('Sloan Letters',$arr,'Sloan Letters'),
						'Toys'=>array('Toys',$arr,'Toys'),
						'TAC'=>array('TAC',$arr,'TAC'),
						'Intense Light'=>array('Intense Light',$arr,'Intense Light'),
						'Isolated E'=>array('Isolated E',$arr,'Isolated E'),
						'Isolated Letters'=>array('Isolated Letters',$arr,'Isolated Letters'),
						'Tumbling Es'=>array('Tumbling Es',$arr,'Tumbling Es'),		
						'Landolt C'=>array('Landolt C',$arr,'Landolt C'),
						'Other'=>array('Other',$arr,'Other'));
		
		//Acuities MR/ Dis
		$arr["AcuitiesMrDis"] = array("20/15"=>array("20/15",$arrEmpty,"20/15"),
					 "20/20"=>array("20/20",$arrEmpty,"20/20"),
					 "20/25"=>array("20/25",$arrEmpty,"20/25"),
					 "20/30"=>array("20/30",$arrEmpty,"20/30"),
					 "20/40"=>array("20/40",$arrEmpty,"20/40"),					 
					 "20/50"=>array("20/50",$arrEmpty,"20/50"),
					 "20/60"=>array("20/60",$arrEmpty,"20/60"),
					 "20/70"=>array("20/70",$arrEmpty,"20/70"),
					 "20/80"=>array("20/80",$arrEmpty,"20/80"),					 
					 "20/100"=>array("20/100",$arrEmpty,"20/100"),
					 "20/150"=>array("20/150",$arrEmpty,"20/150"),
					 "20/200"=>array("20/200",$arrEmpty,"20/200"),
					 "20/300"=>array("20/300",$arrEmpty,"20/300"),					 
					 "20/400"=>array("20/400",$arrEmpty,"20/400"),
					 "20/600"=>array("20/600",$arrEmpty,"20/600"),
					 "20/800"=>array("20/800",$arrEmpty,"20/800"),
					 "2/200"=>array("2/200",$arrEmpty,"2/200"),
					 "5/200"=>array("5/200",$arrEmpty,"5/200"),
					 "BTL"=>array("BTL",$arrEmpty,"BTL"),
					 "CF"=>array("CF",$arrEmpty,"CF"),
					 "CF 1ft"=>array("CF 1ft",$arrEmpty,"CF 1ft"),
					 "CF 2ft"=>array("CF 2ft",$arrEmpty,"CF 2ft"),
					 "CF 3ft"=>array("CF 3ft",$arrEmpty,"CF 3ft"),
					 "CF 4ft"=>array("CF 4ft",$arrEmpty,"CF 4ft"),
					 "CF 5ft"=>array("CF 5ft",$arrEmpty,"CF 5ft"),
					 "CF 6ft"=>array("CF 6ft",$arrEmpty,"CF 6ft"),
					 "CSFE"=>array("CSFE",$arrEmpty,"CSFE"),
					 "CSFF"=>array("CSFF",$arrEmpty,"CSFF"),
					 "CSM"=>array("CSM",$arrEmpty,"CSM"),
					 "CSM alt 20 BD"=>array("CSM alt 20 BD",$arrEmpty,"CSM alt 20 BD"),
					 "Enucleation"=>array("Enucleation",$arrEmpty,"Enucleation"),
					 "F&F"=>array("F&F",$arrEmpty,"F&F"),
					 "F/(F)"=>array("F/(F)",$arrEmpty,"F/(F)"),
					 "HM"=>array("HM",$arrEmpty,"HM"),
					 "LP"=>array("LP",$arrEmpty,"LP"),					 
					 "LP c p"=>array("LP c p",$arrEmpty,"LP c p"),
					 "LP s p"=>array("LP s p",$arrEmpty,"LP s p"),
					 "NLP"=>array("NLP",$arrEmpty,"NLP"),
					 "Prosthetic"=>array("Prosthetic",$arrEmpty,"Prosthetic"),
					 "Pt Uncoopera"=>array("Pt Uncoopera",$arrEmpty,"Pt Uncoopera"),
					 "Unable"=>array("Unable",$arrEmpty,"Unable")
					 );

	//Acuities Near
	$arr["AcuitiesNear"] = array('20/20(J1+)'=>array('20/20(J1+)',$arrEmpty,'20/20(J1+)'),
					  '20/25(J1)'=>array('20/25(J1)',$arrEmpty,'20/25(J1)'),
					  '20/30(J2)'=>array('20/30(J2)',$arrEmpty,'20/30(J2)'),
					  '20/40(J3)'=>array('20/40(J3)',$arrEmpty,'20/40(J3)'),
					  /*'20/32(J4)'=>array('20/32(J4)',$arrEmpty,'20/32(J4)'),*/
					  '20/50(J5)'=>array('20/50(J5)',$arrEmpty,'20/50(J5)'),
					  '20/60(J6)'=>array('20/60(J6)',$arrEmpty,'20/60(J6)'),
					  '20/70(J7)'=>array('20/70(J7)',$arrEmpty,'20/70(J7)'),
					  /*'20/63(J8)'=>array('20/63(J8)',$arrEmpty,'20/63(J8)'),*/
					  '20/80(J8)'=>array('20/80(J8)',$arrEmpty,'20/80(J8)'),
					  '20/100(J10)'=>array('20/100(J10)',$arrEmpty,'20/100(J10)'),
					  '20/200(J16)'=>array('20/200(J16)',$arrEmpty,'20/200(J16)'),
					  '20/400'=>array('20/400',$arrEmpty,'20/400'),
					  '20/800'=>array('20/800',$arrEmpty,'20/800'),
					  'APC 20/30'=>array('APC 20/30',$arrEmpty,'APC 20/30'),
					  'APC 20/40'=>array('APC 20/40',$arrEmpty,'APC 20/40'),
					  'APC 20/60'=>array('APC 20/60',$arrEmpty,'APC 20/60'),
					  'APC 20/80'=>array('APC 20/80',$arrEmpty,'APC 20/80'),
					  'APC 20/100'=>array('APC 20/100',$arrEmpty,'APC 20/100'),
					  'APC 20/160'=>array('APC 20/160',$arrEmpty,'APC 20/160'),
					  'APC 20/200'=>array('APC 20/200',$arrEmpty,'APC 20/200'),
					  'BTL'=>array('BTL',$arrEmpty,'BTL'),
					  'CSFE'=>array('CSFE',$arrEmpty,'CSFE'),
					  'CSFF'=>array('CSFF',$arrEmpty,'CSFF'),	
					  'CSM'=>array('CSM',$arrEmpty,'CSM'),
					  '(C)SM'=>array('(C)SM',$arrEmpty,'(C)SM'),
					  'C(S)M'=>array('C(S)M',$arrEmpty,'C(S)M'),
					  'CS(M)'=>array('CS(M)',$arrEmpty,'CS(M)'),
					  'C(S)(M)'=>array('C(S)(M)',$arrEmpty,'C(S)(M)'),
					  '(C)(S)M'=>array('(C)(S)M',$arrEmpty,'(C)(S)M'),
					  '(C)S(M)'=>array('(C)S(M)',$arrEmpty,'(C)S(M)'),
					  '(C)(S)(M)'=>array('(C)(S)(M)',$arrEmpty,'(C)(S)(M)'),
					  'CSM alt 20 BD'=>array('CSM alt 20 BD',$arrEmpty,'CSM alt 20 BD'),
					  'F&F'=>array('F&F',$arrEmpty,'F&F'),
					  'Unable'=>array('Unable',$arrEmpty,'Unable'));
					  
	$arr["w4dotOptions"] = array(
						"Fuses"=>array("Fuses",$arrEmpty,"Fuses"),
						"2 red"=>array("2 red",$arrEmpty,"2 red"),
						"3 green"=>array("3 green",$arrEmpty,"3 green"),
						"5 Lights"=>array("5 Lights",$arrEmpty,"5 Lights")
					);

		//*/
		return $arr;	
	}
	
	function vis_getStatus($nm) {
		global $elem_statusElements;
		return (strpos($elem_statusElements,$nm."=1,")!==false) ? " active " : "inact";
	}
	
	function isValFilled($arr,$chkCur=0,$strStatus=""){
		$retDone = false;
		$retDoneCur = false;
		if( count($arr) > 0 ){
			foreach( $arr as $key => $val ){
				$val = trim($val);
				if( !empty($val) && ($val != "20/") && ( $val != "BD" ) && ( $val != "BI" ) && $val != "+" ){
					$retDone = true;
					if( ($chkCur == 1) && ($strStatus != "") ){
						if((strpos($strStatus,$key."=1") !== false)){
							$retDoneCur = true;
							break;
						}else{
							//echo $key."=1";
						}
					}else{
						break;
					}
				}
			}
		}
		return ($chkCur == 1) ? array($retDone,$retDoneCur) : $retDone ;
	}
	
	function getVisDropDown($nm,$attr,$value){
		if($nm == "PrismQtr"){
			if(isset($GLOBALS["PRISM_QTR_VALUES"]) && !empty($GLOBALS["PRISM_QTR_VALUES"])){
			$arrOpts=array("0.25","0.5","0.75","1",
							"1.25","1.5","1.75","2",
							"2.25","2.5","2.75","3",
							"3.25","3.5","3.75","4",
							"4.25","4.5","4.75","5",
							"5.25","5.5","5.75","6",
							"6.25","6.5","6.75","7",
							"7.25","7.5","7.75","8",
							"8.25","8.5","8.75","9",
							"9.25","9.5","9.75","10",
							"10.25","10.5","10.75","11",
							"11.25","11.5","11.75","12",
							"12.25","12.5","12.75","13",
							"13.25","13.5","13.75","14",
							"14.25","14.5","14.75","15");
			}else{			
			$arrOpts=array("0.5","1",
							"1.5","2",
							"2.5","3",
							"3.5","4",
							"4.5","5",
							"5.5","6",
							"6.5","7",
							"7.5","8",
							"8.25","8.5","8.75","9",
							"9.25","9.5","9.75","10",
							"10.25","10.5","10.75","11",
							"11.25","11.5","11.75","12",
							"12.25","12.5","12.75","13",
							"13.25","13.5","13.75","14",
							"14.25","14.5","14.75","15");
			}
		}
		
		$str ="";
		if(!empty($arrOpts)){
			$str = "<select ".$attr.">".
					"<option value=\"\"></option>";
			foreach($arrOpts as $key=>$val){
				$tmp = ($value==$val)?"selected=selected":"";
				$str .= "<option value=\"".$val."\" ".$tmp.">".$val."</option>";
			}
			$str .= "</select>";
		}
		return $str;
	}
	
	function extractDate($str){
		$srch = "<~ED~>";
		$dt = "";
		$indx = strpos($str,$srch);
		if($indx !== false){
			$dt = str_replace($srch,"",substr($str,$indx));
			$str = substr($str,0,$indx);
		}
		return array(trim($str),trim($dt));
	}
	
	//--
	function getICP($flghtm=0){	
		$patient_id = $this->pid;
		$form_id = $this->fid;
		
		if(!empty($patient_id)&&!empty($form_id)){		
		
			$echo="";
				
			$sql = "SELECT * FROM chart_icp_color WHERE form_id= '".$form_id."' AND patient_id='".$patient_id."' AND purged='0' ";		
			$row = sqlQuery($sql);		
			if($row==false){
				//GET PAST values
				//Obj
				$oCN = new ChartNote($patient_id,$form_id);
				$elem_dos = $oCN->getDos();			
				$elem_dos = wv_formatDate($elem_dos,0,0,"insert");			
				$res = $oCN->getLastRecord("chart_icp_color","form_id",0," * ",$elem_dos); //
				if($res!=false){$row=$res;}else{$row=false;}
				$elem_edit_icp=0;
				
				$elem_control_css = "inact";
				$elem_controlValueOd_css = "inact";
				$elem_controlValueOd_denom_css = "inact";
				$elem_control_os_css = "inact";
				$elem_controlValueOs_css = "inact";
				$elem_controlValueOs_denom_css = "inact";
				$elem_control_ou_css = "inact";
				$elem_controlValueOu_css = "inact";
				$elem_controlValueOu_denom_css = "inact";
				$elem_comm_colorVis_css = "inact";
				
			}else{
				$elem_edit_icp=1;
				
				$elem_control_css = "active";
				$elem_controlValueOd_css = "active";
				$elem_controlValueOd_denom_css = "active";
				$elem_control_os_css = "active";
				$elem_controlValueOs_css = "active";
				$elem_controlValueOs_denom_css = "active";
				$elem_control_ou_css = "active";
				$elem_controlValueOu_css = "active";
				$elem_controlValueOu_denom_css = "active";	
				
				$elem_comm_colorVis_css = "active";
			}

			if($row!=false){
				$elem_color_sign_od=$row["control_od"];
				$elem_color_od_1 = $row["cval1_od"];
				$elem_color_od_2 = $row["cval2_od"];
				
				$elem_color_sign_os=$row["control_os"];
				$elem_color_os_1 = $row["cval1_os"];
				$elem_color_os_2 = $row["cval2_os"];
				
				$elem_color_sign_ou=$row["control_ou"];
				$elem_color_ou_1 = $row["cval1_ou"];
				$elem_color_ou_2 = $row["cval2_ou"];
				
				$elem_comm_colorVis = $row["icp_desc"];
				$ut_elem=($elem_edit_icp==1)?$row["ut_elem"]:"";
			}
		
		
			//GetColor
			$owv = new WorkView();
			$arBg = $owv->getUTBgColor($ut_elem);
			if(!empty($arBg["elem_color_sign_od"][0])){
				$elem_color_sign_od_style=" style=\"background-color:".$arBg["elem_color_sign_od"][0].";\" ";
			}
			if(!empty($arBg["elem_color_od_1"][0])){
				$elem_color_od_1_style=" style=\"background-color:".$arBg["elem_color_od_1"][0].";\" ";
			}
			if(!empty($arBg["elem_color_od_2"][0])){
				$elem_color_od_2_style=" style=\"background-color:".$arBg["elem_color_od_2"][0].";\" ";
			}
			if(!empty($arBg["elem_color_sign_os"][0])){
				$elem_color_sign_os_style=" style=\"background-color:".$arBg["elem_color_sign_os"][0].";\" ";
			}
			if(!empty($arBg["elem_color_os_1"][0])){
				$elem_color_os_1_style=" style=\"background-color:".$arBg["elem_color_os_1"][0].";\" ";
			}
			if(!empty($arBg["elem_color_os_2"][0])){
				$elem_color_os_2_style=" style=\"background-color:".$arBg["elem_color_os_2"][0].";\" ";
			}
			
			if(!empty($arBg["elem_color_sign_ou"][0])){
				$elem_color_sign_ou_style=" style=\"background-color:".$arBg["elem_color_sign_ou"][0].";\" ";
			}
			if(!empty($arBg["elem_color_ou_1"][0])){
				$elem_color_ou_1_style=" style=\"background-color:".$arBg["elem_color_ou_1"][0].";\" ";
			}
			if(!empty($arBg["elem_color_ou_2"][0])){
				$elem_color_ou_2_style=" style=\"background-color:".$arBg["elem_color_ou_2"][0].";\" ";
			}
			
			if(!empty($arBg["elem_comm_colorVis"][0])){
				$elem_comm_colorVis_style=" style=\"background-color:".$arBg["elem_comm_colorVis"][0].";\" ";
			}		
		}//
		
		//vis_getStatus("elem_control")
		//(strpos($elem_statusElements,$nm."=1,")!==false) ? " active " : "inact";
		/*
		$elem_control_css = vis_getStatus("elem_control");
		$elem_controlValueOd_css = vis_getStatus("elem_controlValueOd");
		$elem_controlValueOd_denom_css = vis_getStatus("elem_controlValueOd_denom");
		$elem_control_os_css = vis_getStatus("elem_control_os");
		$elem_controlValueOd_css = vis_getStatus("elem_controlValueOs");
		$elem_controlValueOd_denom_css vis_getStatus("elem_controlValueOs_denom");
		*/
		
		$sel_od_p = ($elem_color_sign_od == "+") ? "selected" : "" ;
		$sel_od_m = ($elem_color_sign_od == "-") ? "selected" : "" ;		
		$sel_os_p = ($elem_color_sign_os == "+") ? "selected" : "" ;
		$sel_os_m = ($elem_color_sign_os == "-") ? "selected" : "" ;
		$sel_ou_p = ($elem_color_sign_ou == "+") ? "selected" : "" ;
		$sel_ou_m = ($elem_color_sign_ou == "-") ? "selected" : "" ;
		
		
		//Set Background Color --
		//pending
		/////		
		$str="".
			"<div class=\"examsectbox\">
				<div class=\"header\">
					<div class=\"automan\">
						<h2>CP Control</h2>
						<span class=\"glyphicon glyphicon-ok-circle clickable\" data-toggle=\"tooltip\" title=\"No Change\"></span>
					</div>
				</div>
				<div class=\"clearfix\"></div>
				<div class=\"exampd default\">
					<table class=\"table borderless\">
					<tr>
						<td class=\" odcol\">OD</td>									
						<td class=\"  \">	
							<select name=\"elem_color_sign_od\" class=\"form-control m1inimal ".$elem_control_css."\" ".$elem_color_sign_od_style." title=\"Control\" >
								<option value=\"\"></option>
								<option value=\"+\" ".$sel_od_p." >+</option>
								<option value=\"-\" ".$sel_od_m." >-</option>
							</select>
						</td>	
							
						<td class=\"\">
							<input type=\"text\" name=\"elem_color_od_1\" value=\"".$elem_color_od_1."\" 
								class=\"form-control ".$elem_controlValueOd_css."\" ".$elem_color_od_1_style." >
						</td>
						<td class=\" poscenter\"><span>/</span></td>
						<td class=\"\">
							<input type=\"text\" name=\"elem_color_od_2\"
								value=\"".$elem_color_od_2."\" class=\"form-control ".$elem_controlValueOd_denom_css."\" ".$elem_color_od_2_style." >
						</td>
					</tr>
					<tr>
						<td class=\" oscol\">OS</td>
						<td class=\"  \">	
							<select name=\"elem_color_sign_os\" class=\"form-control mi1nimal ".$elem_control_css."\" ".$elem_color_sign_os_style." title=\"Control\" >
								<option value=\"\"></option>
								<option value=\"+\" ".$sel_os_p." >+</option>
								<option value=\"-\" ".$sel_os_m." >-</option>
							</select>
						</td>								
						<td class=\"\">
							<input type=\"text\" name=\"elem_color_os_1\" value=\"".$elem_color_os_1."\" 
								class=\"form-control ".$elem_controlValueOs_css."\" ".$elem_color_os_1_style." >
						</td>
						<td class=\" poscenter\"><span>/</span></td>
						<td class=\"\">
							<input type=\"text\" name=\"elem_color_os_2\"
								value=\"".$elem_color_os_2."\" class=\"form-control ".$elem_controlValueOs_denom_css."\" ".$elem_color_os_2_style." >
						</td>
					</tr>
					<tr>
						<td class=\" oucol\">OU</td>
						<td class=\"  \">	
							<select name=\"elem_color_sign_ou\" class=\"form-control mi1nimal ".$elem_control_css."\" ".$elem_color_sign_ou_style." title=\"Control\" >
								<option value=\"\"></option>
								<option value=\"+\" ".$sel_ou_p." >+</option>
								<option value=\"-\" ".$sel_ou_m." >-</option>
							</select>
						</td>								
						<td class=\"\">
							<input type=\"text\" name=\"elem_color_ou_1\" value=\"".$elem_color_ou_1."\" 
								class=\"form-control ".$elem_controlValueOu_css."\" ".$elem_color_ou_1_style." >
						</td>
						<td class=\" poscenter\"><span>/</span></td>
						<td class=\"\">
							<input type=\"text\" name=\"elem_color_ou_2\"
								value=\"".$elem_color_ou_2."\" class=\"form-control ".$elem_controlValueOu_denom_css."\" ".$elem_color_ou_2_style." >
						</td>
					</tr>
					<tr>
						<td class=\"\" colspan=\"5\">
							<textarea name=\"elem_comm_colorVis\" class=\"form-control ".$elem_comm_colorVis_css."\" rows=\"3\" ".$elem_comm_colorVis_style."  >".$elem_comm_colorVis."</textarea>
						</td>
					</tr>
					</table>
				</div>
			</div>	
			";
		
		$echo.= $str;
		if(!empty($flghtm)){
			return $echo;
		}else{
			$tmp = $elem_color_od_1.$elem_color_os_1.$elem_color_ou_1.$elem_color_od_2.$elem_color_os_2.$elem_color_ou_2;
			$is_pos = !empty($tmp) ? 1 : "";
			return array($echo, $is_pos);
		}
		
	}
	function getStereo($flghtm=0){		
	
		$patient_id = $this->pid;
		$form_id = $this->fid;
		
		if(!empty($patient_id)&&!empty($form_id)){	
		
		$echo="";
		
			$sql = "SELECT * FROM chart_steropsis WHERE form_id= '".$form_id."' AND patient_id='".$patient_id."' AND purged='0' ";		
			$row = sqlQuery($sql);		
			if($row==false){
				//GET PAST values
				//Obj
				$oCN = new ChartNote($patient_id,$form_id);
				$elem_dos = $oCN->getDos();			
				$elem_dos = wv_formatDate($elem_dos,0,0,"insert");			
				$res = $oCN->getLastRecord("chart_steropsis","form_id",0," * ",$elem_dos); //
				if($res!=false){$row=$res;}else{$row=false;}
				$elem_edit=0;				
				$elem_stereo_SecondsArc_css = "inact";			
				
			}else{
				$elem_edit=1;				
				$elem_stereo_SecondsArc_css = "active";				
			}

			if($row!=false){
				$elem_stereo_SecondsArc=$row["seconds_of_arc"];				
				$ut_elem=($elem_edit==1) ? $row["ut_elem"] : "" ;
			}			
			
			//GetColor
			$owv = new WorkView();
			$arBg = $owv->getUTBgColor($ut_elem);
			if(!empty($arBg["elem_stereo_SecondsArc"][0])){
				$elem_stereo_SecondsArc_style=" style=\"background-color:".$arBg["elem_stereo_SecondsArc"][0].";\" ";
			}
		
		}//
		
		$str = "<div class=\"examsectbox\">
				<div class=\"header\">
					<div class=\"automan\">
						<h2>Stereopsis</h2>
						<span class=\"glyphicon glyphicon-ok-circle clickable\" data-toggle=\"tooltip\" title=\"No Change\"></span>
					</div>
				</div>
				<div class=\"clearfix\"></div>
				<div class=\"exampd default\">
					<div class=\"row\">
						<div class=\"col-xs-12\">										
							<input type=\"text\" name=\"elem_stereo_SecondsArc\"
											value=\"".$elem_stereo_SecondsArc."\" class=\"form-control ".$elem_stereo_SecondsArc_css."\" ".$elem_stereo_SecondsArc_style." title=\"sec of arc\" >
						</div>
					</div>
					<div class=\"row\">
						<div class=\"col-xs-12 poscenter\">Sec of arc</div>
					</div>
				</div>
			</div>";
			
		$echo.= $str;
		if(!empty($flghtm)){
			return $echo;
		}else{
			$is_pos = !empty($elem_steropsis) ? 1 : "";		
			return array($echo, $is_pos);
		}
		
	}
	function getW4Dot($flghtm=0){

		$patient_id = $this->pid;
		$form_id = $this->fid;

		$arr_dd_menu = Vision::getArrDropDown();
		$arr_w4dotOptions = $arr_dd_menu["w4dotOptions"];
		
		$echo="";
		
		if(!empty($patient_id)&&!empty($form_id)){		
		
			$sql = "SELECT * FROM chart_w4dot WHERE form_id= '".$form_id."' AND patient_id='".$patient_id."' AND purged='0'  ";		
			$row = sqlQuery($sql);		
			if($row==false){
				//GET PAST values
				//Obj
				$oCN = new ChartNote($patient_id,$form_id);
				$elem_dos = $oCN->getDos();			
				$elem_dos = wv_formatDate($elem_dos,0,0,"insert");			
				$res = $oCN->getLastRecord("chart_w4dot","form_id",0," * ",$elem_dos); //
				if($res!=false){$row=$res;}else{$row=false;}
				$elem_edit=0;				
				
				$elem_w4dot_distance_css = "inact";
				$elem_w4dot_near_css = "inact";
				$elem_worth4dot_css = "inact";
				
			}else{
				$elem_edit=1;				
				$elem_w4dot_distance_css = "active";
				$elem_w4dot_near_css = "active";
				$elem_worth4dot_css = "active";		
			}

			if($row!=false){				
				$elem_w4dot_distance=$row["distance"];
				$elem_w4dot_near =$row["near"];
				$elem_worth4dot=$row["desc_w4dot"];				
				$ut_elem=($elem_edit==1)?$row["ut_elem"]:"";
			}
			
			//GetColor
			$owv = new WorkView();
			$arBg = $owv->getUTBgColor($ut_elem);
			if(!empty($arBg["elem_w4dot_distance"][0])){
				$elem_w4dot_distance_style="style=\"background-color:".$arBg["elem_w4dot_distance"][0].";\"";
			}
			
			if(!empty($arBg["elem_w4dot_near"][0])){
				$elem_w4dot_near_style="style=\"background-color:".$arBg["elem_w4dot_near"][0].";\"";
			}
			
			if(!empty($arBg["elem_comm_w4Dot"][0])){
				$elem_comm_w4Dot_style="style=\"background-color:".$arBg["elem_comm_w4Dot"][0].";\"";
			}
			
		
		}//
		
		//<?php echo vis_getStatus("elem_worth4dot");? >
		//class="<?php //echo $css_sptitle_w4dot." ".$ctmpW4Dot;
		/*wv_get_simple_menu($arr_w4dotOptions,"menu_w4dot","elem_w4dot_distance",255,0,array("pdiv"=>"elem_visDisOdTxt1"));*/
		/*wv_get_simple_menu($arr_w4dotOptions,"menu_w4dot","elem_w4dot_near",255,0,array("pdiv"=>"elem_visDisOsTxt1"));*/
		$menu_w4dot_distance = wv_get_simple_menu($arr_w4dotOptions,"menu_w4dot","elem_w4dot_distance");
		$menu_w4dot_near = wv_get_simple_menu($arr_w4dotOptions,"menu_w4dot","elem_w4dot_near");
		
		$str = "
			<div class=\"examsectbox\">
				<div class=\"header\">
					<div class=\"automan\" >
					<h2>Worth Four Dot</h2>
					<span class=\"glyphicon glyphicon-ok-circle clickable\" data-toggle=\"tooltip\" title=\"No Change\"></span>
					</div>
				</div>
				<div class=\"clearfix\"></div>
				<div class=\"exampd default\">
					<table class=\"table borderless\">
					<tr>
						<td class=\" poscenter\">Distance</td>									
						<td class=\"\">
							<div class=\"input-group\">
								<input type=\"text\" name=\"elem_w4dot_distance\" id=\"elem_w4dot_distance\" value=\"".$elem_w4dot_distance."\" class=\"form-control ".$elem_w4dot_distance_css."\" ".$elem_w4dot_distance_style." title=\"Distance\" />											
								".$menu_w4dot_distance."
							</div>
						</td>
						
					</tr>
					<tr>
						<td class=\" poscenter\">Near</td>									
						<td class=\"\">
							<div class=\"input-group\">
								<input type=\"text\" name=\"elem_w4dot_near\" id=\"elem_w4dot_near\" value=\"".$elem_w4dot_near."\" class=\"form-control ".$elem_w4dot_near_css."\" ".$elem_w4dot_near_style." title=\"Near\"  />											
								".$menu_w4dot_near."											
							</div>
						</td>
						
					</tr>								
					<tr>
						<td class=\"\" colspan=\"2\">										
							<textarea name=\"elem_comm_w4Dot\" class=\"form-control ".$elem_worth4dot_css."\" ".$elem_comm_w4Dot_style." rows=\"2\">".$elem_worth4dot."</textarea>
						</td>
					</tr>
					</table>
				</div>
			</div>
		";		
		
		$echo.= $str;
		if(!empty($flghtm)){
			return $echo;
		}else{
			$tmp = $elem_w4dot_distance.$elem_w4dot_near.$elem_worth4dot;
			$is_pos = !empty($tmp) ? 1 : "";		
			return array($echo, $is_pos);
		}
	}
	
	function getDistance($visId, $elem_editModeVis, $visIdLF){
		$arr = array();
		$sql = "SELECT * FROM chart_acuity where id_chart_vis_master = '".$visId."' ";
		
		$rez = sqlStatement($sql);
		for($i=1;$row=sqlFetchArray($rez);$i++){
		
			if($row["sec_name"] == "Distance"){
				if($row["sec_indx"] == "1"){
					$arr["elem_visDisOdSel1"] = $row["sel_od"];
					$arr["elem_visDisOdTxt1"] = $row["txt_od"];
					$arr["elem_visDisOsSel1"] = $row["sel_os"];
					$arr["elem_visDisOsTxt1"] = $row["txt_os"];
					$arr["elem_visDisOuSel1"] = $row["sel_ou"];
					$arr["elem_visDisOuTxt1"] = $row["txt_ou"];
					$arr["elem_disDesc"] = stripslashes($row["ex_desc"]);
					$arr["elem_visSnellan"] = (($elem_editModeVis == "0") && (empty($row["snellen"]))) ? "Snellen" : $row["snellen"];	
				}else if($row["sec_indx"] == "2"){
					$arr["elem_visDisOdSel2"] = $row["sel_od"];
					$arr["elem_visDisOdTxt2"] = $row["txt_od"];
					$arr["elem_visDisOsSel2"] = $row["sel_os"];
					$arr["elem_visDisOsTxt2"] = $row["txt_os"];
					$arr["elem_visDisOuSel2"] = $row["sel_ou"];
					$arr["elem_visDisOuTxt2"] = $row["txt_ou"];
				}else if($row["sec_indx"] == "4"){
					//More -----------------------------------
					$arr["elem_visDisOdSel4"] = $row["sel_od"];
					$arr["elem_visDisOdTxt4"] = $row["txt_od"];
					$arr["elem_visDisOsTxt4"] = $row["txt_os"];
					$arr["elem_visDisOuTxt4"] = $row["txt_ou"];
					$arr["elem_visDisAct4"] = stripslashes($row["ex_desc"]);
					//More -----------------------------------
				}				
			}else if($row["sec_name"] == "Near"){
				if($row["sec_indx"] == "1"){
					//$arr["elem_visNear"] = $row["vis_near"];
					$arr["elem_visNearOdSel1"] = $row["sel_od"];
					$arr["elem_visNearOdTxt1"] = $row["txt_od"];
					$arr["elem_visNearOsSel1"] = $row["sel_os"];					
					$arr["elem_visNearOsTxt1"] = $row["txt_os"];
					$arr["elem_visNearOuSel1"] = $row["sel_ou"];
					$arr["elem_visNearOuTxt1"] = $row["txt_ou"];
					$arr["elem_visNearDesc"] = stripslashes($row["ex_desc"]);
					$arr["elem_visSnellan_near"] = (($elem_editModeVis == "0") && (empty($row["snellen"]))) ? "Snellen" : $row["snellen"]; 
				}else if($row["sec_indx"] == "2"){
					$arr["elem_visNearOdSel2"] = $row["sel_od"];
					$arr["elem_visNearOdTxt2"] = $row["txt_od"];
					$arr["elem_visNearOsSel2"] = $row["sel_os"];
					$arr["elem_visNearOsTxt2"] = $row["txt_os"];
					$arr["elem_visNearOuSel2"] = $row["sel_ou"];
					$arr["elem_visNearOuTxt2"] = $row["txt_ou"];
				}			
			}else if($row["sec_name"] == "Ad. Acuity"){
				//More -----------------------------------
				$arr["elem_visDisOdSel3"] = $row["sel_od"];
				$arr["elem_visDisOdTxt3"] = $row["txt_od"];
				$arr["elem_visDisOsSel3"] = $row["sel_os"];
				$arr["elem_visDisOsTxt3"] = $row["txt_os"];
				$arr["elem_visDisOuSel3"] = $row["sel_ou"];
				$arr["elem_visDisOuTxt3"] = $row["txt_ou"];
				$arr["elem_visDisAct3"] = stripslashes($row["ex_desc"]);
				//More -----------------------------------
			}		
		}
		
		//Prev		
		if(!empty($visIdLF)){
		$sql = "SELECT * FROM chart_acuity where id_chart_vis_master = '".$visIdLF."' AND sec_name='Distance' AND sec_indx='1'  ";		
		$row = sqlQuery($sql); //sqlStatement($sql);
		if($row!=false){			
			if($row["sec_indx"] == "1"){
				$arr["elem_visDisOdSel1LF"] = $row["sel_od"];
				$arr["elem_visDisOdTxt1LF"] = $row["txt_od"];
				$arr["elem_visDisOsSel1LF"] = $row["sel_os"];
				$arr["elem_visDisOsTxt1LF"] = $row["txt_os"];
			}
		}	
		}
		
		return $arr;		
	}
	
	function getPam($visId, $elem_editModeVis){
		$arr = array();
		$sql = "SELECT * FROM chart_pam where id_chart_vis_master = '".$visId."' ";
		$row = sqlQuery($sql);
		if($row!=false){		
			//PAM
			$arr["elem_visPam"] = $row["pam"];
			$arr["elem_visPamOdTxt1"] = $row["txt1_od"];
			$arr["elem_visPamOsTxt1"] = $row["txt1_os"];
			$arr["elem_visPamOuTxt1"] = $row["txt1_ou"];
			$arr["elem_visPamOdsel2"] = $row["sel2"];
			$arr["elem_visPamOdTxt2"] = $row["txt2_od"];
			$arr["elem_visPamOsTxt2"] = $row["txt2_os"];
			$arr["elem_visPamOuTxt2"] = $row["txt2_ou"];
			$arr["elem_pamDesc"] = stripslashes($row["ex_desc"]);		
		}
		return $arr;
	}
	
	function getSCA($visId, $elem_editModeVis){
		$arr = array();
		$sql = "SELECT * FROM chart_sca where id_chart_vis_master = '".$visId."' ";		
		$rez = sqlStatement($sql);
		for($i=1;$row=sqlFetchArray($rez);$i++){	
			if($row["sec_name"] == "AR"){
				//AR--------------------------------------
				$arr["elem_visArOdS"] = $row["s_od"];
				$arr["elem_visArOdC"] = $row["c_od"];
				$arr["elem_visArOdA"] = $row["a_od"];
				$arr["elem_visArOdSel1"] = $row["sel_od"];
				$arr["elem_visArOsS"] = $row["s_os"];
				$arr["elem_visArOsC"] = $row["c_os"];
				$arr["elem_visArOsA"] = $row["a_os"];
				$arr["elem_visArOsSel1"] = $row["sel_os"];
		/*			$elem_visArOuS = $row["vis_ar_ou_s"];
				$elem_visArOuC = $row["vis_ar_ou_c"];
				$elem_visArOuA = $row["vis_ar_ou_a"];
				$elem_visArOuSel1 = $row["vis_ar_ou_sel_1"];
		*/		$arr["elem_visArDesc"] = stripslashes($row["ex_desc"]);
				$arr["elem_visArRefPlace"] = $row["ar_ref_place"];
				//AR--------------------------------------
			}else if($row["sec_name"] == "ARC"){		
				//Cycloplegic AR --------------------------------------
				$arr["elem_visCycArOdS"] = $row["s_od"];
				$arr["elem_visCycArOdC"] = $row["c_od"];
				$arr["elem_visCycArOdA"] = $row["a_od"];
				$arr["elem_visCycArOdSel1"] = $row["sel_od"];
				$arr["elem_visCycArOsS"] = $row["s_os"];
				$arr["elem_visCycArOsC"] = $row["c_os"];
				$arr["elem_visCycArOsA"] = $row["a_os"];
				$arr["elem_visCycArOsSel1"] = $row["sel_os"];
		/*		$elem_visCycArOuS = $row["visCycArOuS"];
				$elem_visCycArOuC = $row["visCycArOuC"];
				$elem_visCycArOuA = $row["visCycArOuA"];
				$elem_visCycArOuSel1 = $row["visCycArOuSel1"];*/
				$arr["elem_visCycArDesc"] = stripslashes($row["ex_desc"]);
				//Cycloplegic AR --------------------------------------
			}else if($row["sec_name"] == "RETINOSCOPY"){
				//Retinoscopy ----------------------------
				$arr["elem_visExoOdS"] = $row["s_od"];
				$arr["elem_visExoOdC"] = $row["c_od"];
				$arr["elem_visExoOdA"] = $row["a_od"];
				$arr["elem_visExoOsS"] = $row["s_os"];
				$arr["elem_visExoOsC"] = $row["c_os"];
				$arr["elem_visExoOsA"] = $row["a_os"];
				/*$arr["elem_visExoOuS"] = $row["vis_exo_ou_s"];
				$arr["elem_visExoOuC"] = $row["vis_exo_ou_c"];
				$arr["elem_visExoOuA"] = $row["vis_exo_ou_a"];*/
				//$arr["elem_retinoCL"] = $row["vis_retino_cl"];			
				//Retinoscopy ----------------------------
			}else if($row["sec_name"] == "CYCLOPLEGIC RETINO"){	
				//<!-- Cycloplegic Retino -------------------
				$arr["elem_visCycloOdS"] = $row["s_od"];
				$arr["elem_visCycloOdC"] = $row["c_od"];
				$arr["elem_visCycloOdA"] = $row["a_od"];
				$arr["elem_visCycloOsS"] = $row["s_os"]; 
				$arr["elem_visCycloOsC"] = $row["c_os"]; 
				$arr["elem_visCycloOsA"] = $row["a_os"]; 
				/*$arr["elem_visCycloOuS"] = $row["visCycloOuS"]; 
				$arr["elem_visCycloOuC"] = $row["visCycloOuC"]; 
				$arr["elem_visCycloOuA"] = $row["visCycloOuA"]; */
				//<!-- Cycloplegic Retino --------------------	
			}
			/*else if($row["sec_name"] == "Ad. Acuity"){	
				//CR --------------------------------------
				$elem_visCrOdS = $row["vis_cr_od_s"];
				$elem_visCrOdC = $row["vis_cr_od_c"];
				$elem_visCrOdA = $row["vis_cr_od_a"];
				$elem_visCrOsS = $row["vis_cr_os_s"];
				$elem_visCrOsC = $row["vis_cr_os_c"];
				$elem_visCrOsA = $row["vis_cr_os_a"];
				$elem_visCrOuS = $row["vis_cr_ou_s"];
				$elem_visCrOuC = $row["vis_cr_ou_c"];
				$elem_visCrOuA = $row["vis_cr_ou_a"];
				//CR --------------------------------------	
			}*/
		}
		return $arr;
	}
	
	function getAK($visId, $elem_editModeVis)
	{
		$arr = array();
		$sql = "SELECT * FROM chart_ak where id_chart_vis_master = '".$visId."' ";		
		$row = sqlQuery($sql);
		if($row!=false){	
			//AK--------------------------------------
			$arr["elem_visAkOdK"] = $row["k_od"];
			$arr["elem_visAkOdSlash"] = $row["slash_od"];
			$arr["elem_visAkOdX"] = $row["x_od"];
			$arr["elem_visAkOsK"] = $row["k_os"];
			$arr["elem_visAkOsSlash"] = $row["slash_os"];
			$arr["elem_visAkOsX"] = $row["x_os"];
	/*			$elem_visAkOuK = $row["vis_ak_ou_k"];
			$elem_visAkOuSlash = $row["vis_ak_ou_slash"];
			$elem_visAkOuX = $row["vis_ak_ou_x"];*/
			//$elem_disDesc = $row["vis_dis_near_desc"];
			$arr["elem_visAkDesc"] = stripslashes($row["ex_desc"]);
			$arr["elem_kType"] = $row["k_type"];
			//AK--------------------------------------
		}
		return $arr;
	}
	
	function getEXO($visId, $elem_editModeVis)
	{
		$arr = array();
		$sql = "SELECT * FROM chart_exo where id_chart_vis_master = '".$visId."' ";		
		$row = sqlQuery($sql);
		if($row!=false){
			$arr["elem_visRetPD"] = $row["pd"];
			$arr["elem_visRetOd"] = $row["pd_od"];
			$arr["elem_visRetOs"] = $row["pd_os"];
			//$elem_visRetOu = $row["vis_ret_pd_ou"];
		}
		return $arr;
	}
	
	function getBAT($visId, $elem_editModeVis)
	{
		$arr = array();
		$sql = "SELECT * FROM chart_bat where id_chart_vis_master = '".$visId."' ";		
		$row = sqlQuery($sql);
		if($row!=false){
			//BAT ------------------------------------
			$arr["elem_visBatNlOd"] = $row["nl_od"];
			$arr["elem_visBatLowOd"] = $row["l_od"];
			$arr["elem_visBatMedOd"] = $row["m_od"];
			$arr["elem_visBatHighOd"] = $row["h_od"];
			$arr["elem_visBatNlOs"] = $row["nl_os"];
			$arr["elem_visBatLowOs"] = $row["l_os"];
			$arr["elem_visBatMedOs"] = $row["m_os"];
			$arr["elem_visBatHighOs"] = $row["h_os"];
			$arr["elem_visBatNlOu"] = $row["nl_ou"];
			$arr["elem_visBatLowOu"] = $row["l_ou"];
			$arr["elem_visBatMedOu"] = $row["m_ou"];
			$arr["elem_visBatHighOu"] = $row["h_ou"];
			$arr["elem_visBatDesc"] = stripslashes($row["ex_desc"]);
			//BAT ------------------------------------
		}
		return $arr;
	}
	
	function getVisionSection(){
		global $elem_statusElements, $arrTempProc, $dos_ymd, $ctmpLasik;
		$arr_dd_menu = Vision::getArrDropDown();
		$arrSnellan = $arr_dd_menu["Snellan"];		
		$arrAcuitiesMrDis = $arr_dd_menu["AcuitiesMrDis"];
		$arrAcuitiesNear = $arr_dd_menu["AcuitiesNear"];
		$arr_w4dotOptions = $arr_dd_menu["w4dotOptions"];
		
		//Chart Vision Lasik
		
		$arr_dd_menu_lasik = VisLasik::getArrDropDown();
		$arrLasik_trgt_Excimer = $arr_dd_menu_lasik["Excimer"];
		$arrLasik_trgt_mode = $arr_dd_menu_lasik["Mode"];
		
		//--
		//Vision ------------------
		//Set Default Records
		$elem_providerId = $elem_providerIdOther = $elem_providerIdOther_3 = $elem_providerIdOther_4 = ""; //$_SESSION['authId'];
		//$elem_providerName = $elem_providerNameOther = $elem_providerNameOther_3 = $elem_providerNameOther_4 = showDoctorName($_SESSION['authId']);
		$elem_visNearOdSel1 = $elem_visNearOsSel1 = $elem_visNearOuSel1 = "";
		$elem_visNearOdSel2 = $elem_visNearOsSel2 = $elem_visNearOuSel2 = "";
		$elem_visDisOdTxt1 = $elem_visDisOdTxt2 = $elem_visDisOsTxt1 = $elem_visDisOsTxt2 = $elem_visNearOdTxt1= "20/";
		$elem_visDisOuTxt1 = $elem_visDisOuTxt2 = $elem_visNearOuTxt1= "20/";
		$elem_visNearOdTxt2 = $elem_visNearOsTxt1 = $elem_visNearOsTxt2 = $elem_visPcOdNearTxt2 = $elem_visPcOsNearTxt2 = "20/";
		$elem_visNearOuTxt2 = $elem_visPcOuNearTxt2 = "20/";
		$elem_visPcOdNearTxt3=$elem_visPcOsNearTxt3=$elem_visPcOuNearTxt3=$elem_visMrOdTxt1=$elem_visMrOdTxt2=$elem_visMrOsTxt1=$elem_visMrOuTxt1="20/";
		$elem_visMrOsTxt2=$elem_visMrOuTxt2=$elem_visMrOtherOdTxt1= $elem_visMrOtherOdTxt2=$elem_visMrOtherOsTxt1=$elem_visMrOtherOuTxt1=$elem_visMrOtherOsTxt2=$elem_visMrOtherOuTxt2="20/";
		$elem_visPcOdOverrefV=$elem_visPcOsOverrefV=$elem_visPcOuOverrefV=$elem_visPcOdOverrefV2=$elem_visPcOsOverrefV2=$elem_visPcOuOverrefV2=$elem_visPcOdOverrefV3="20/";
		$elem_visPcOsOverrefV3=$elem_visPcOuOverrefV3=$elem_visBatNlOd=$elem_visBatLowOd=$elem_visBatMedOd=$elem_visBatHighOd=$elem_visBatNlOs=$elem_visBatNlOu="20/";
		$elem_visBatLowOs=$elem_visBatMedOs=$elem_visBatHighOs=$elem_visBatLowOu=$elem_visBatMedOu=$elem_visBatHighOu="20/";
		$elem_mrNoneGiven1 = "None";
		$elem_visMrOtherOdTxt1_3 = $elem_visMrOtherOdTxt2_3 = $elem_visMrOtherOsTxt1_3 = $elem_visMrOtherOsTxt2_3= $elem_visMrOtherOuTxt1_3 = $elem_visMrOtherOuTxt2_3 = "20/";
		//$elem_visMrOtherOdTxt1_4 = $elem_visMrOtherOdTxt2_4 = $elem_visMrOtherOsTxt1_4 = $elem_visMrOtherOsTxt2_4 = "20/";
		$elem_visMrOdSel2Vision = $elem_visMrOtherOdSel2Vision = $elem_visMrOtherOdSel2Vision_3 = "20/";
		$elem_visMrOsSel2Vision = $elem_visMrOtherOsSel2Vision = $elem_visMrOtherOsSel2Vision_3 = "20/";
		$elem_visMrOuSel2Vision = $elem_visMrOtherOuSel2Vision = $elem_visMrOtherOuSel2Vision_3 = "20/";
		$elem_visPamOdTxt1 = $elem_visPamOsTxt1 = $elem_visPamOuTxt1 = "20/";
		$elem_visPamOdTxt2 = $elem_visPamOsTxt2 = $elem_visPamOuTxt2 = "20/";
		
		$todate = date('m-d-Y');
		//$elem_control = "plus";
		//$elem_control_os = "plus";
		$elem_visSnellan = $elem_visSnellan_near	= "Snellen";
		$len_pc=3;
		$len_mr=2;
		
		//variables
		$patient_id = $this->pid;
		$form_id = $this->fid;
		
		//check empty
		if(empty($patient_id) && empty($form_id)){ return "" ; }
		
		//Obj
		$oVis = $this; //new Vision($patient_id,$form_id);

		//Get Past Records
		//if(isset($_GET["visId"]) && !empty($_GET["visId"]))
		//{
			//Get Form id based on patient id
			$sql = "SELECT * FROM chart_vis_master WHERE patient_id = '".$patient_id."' AND form_id = '".$form_id."' ";
			$row = sqlQuery($sql);
			if(($row == false)){ /* Show Previous values in finalized chart also ~&& ($finalize_flag == 0)~ */
				// New
				$elem_editModeVis = "0";
				$vision_edid = "";
				//New Records
				//$row = valuesNewRecordsVision($patient_id);
				$res = $oVis->getLastRecord(" * ",0,$dos_ymd); /*$dos_ymd in getchartinfo.nic.php*/
				if($res!=false){$row=$res;}else{$row=false;}
				$isNewRecord = true;
			}else{
				// Update
				$elem_editModeVis = "1";
				//$elem_visId = $row["vis_id"];
				//Default
				if(isset($_POST["defaultValsVis"]) && ($_POST["defaultValsVis"] == 1)){
					//New Records
					//$row = valuesNewRecordsVision($patient_id);
					$res = $oVis->getLastRecord(" * ",1,$dos_ymd); /*$dos_ymd in getchartinfo.nic.php*/
					if($res!=false){$row=$res;}else{$row=false;}
					$isNewRecord = true;
				}
			}
			
			//Last Finalized Vision Id
			$resLF = $oVis->getLastRecord(" c2.id ",1,$dos_ymd); /*$dos_ymd in getchartinfo.nic.php*/
			if($resLF!=false){
				$elem_visMasIdLF = $resLF["id"];
			}
			
			//
			if($row != false){
			
				$elem_visMasId = $row["id"];
				$ar_dis = $this->getDistance($elem_visMasId, $elem_editModeVis, $elem_visMasIdLF);
				extract($ar_dis);	
				
				
				$ar_pam = $this->getPam($elem_visMasId, $elem_editModeVis);
				extract($ar_pam);	
				
				$ar_sca = $this->getSCA($elem_visMasId, $elem_editModeVis);
				extract($ar_sca);	
				
				$ar_ak = $this->getAK($elem_visMasId, $elem_editModeVis);
				extract($ar_ak);	
				
				$ar_exo = $this->getEXO($elem_visMasId, $elem_editModeVis);			
				extract($ar_exo);	
				
				$ar_bat = $this->getBAT($elem_visMasId, $elem_editModeVis);
				extract($ar_bat);	
				
				//Multiple MR --
				list($ar_multiple_mr,$tmp_mr_len)=$this->get_mutli_mr_pc("MR",$dos_ymd,$elem_visMasId, $elem_visMasIdLF);	
				extract($ar_multiple_mr);		
				if(!empty($tmp_mr_len)){ $len_mr = $tmp_mr_len; $inclss_mr="in";}					
				//End Multiple MR --				
				
				//Multiple PC --
				list($ar_multiple_pc,$tmp_pc_len)=$this->get_mutli_mr_pc("PC",$dos_ymd,$elem_visMasId, $elem_visMasIdLF);	
				extract($ar_multiple_pc);		
				if(!empty($tmp_pc_len)){ $len_pc = $tmp_pc_len; $inclss_pc = "in"; }		
				//End Multiple PC --				
				
				$elem_statusElements = (!isset($isNewRecord) || ($isNewRecord != true)) ? $row["status_elements"] : "" ;
				
				//$elem_noChangeVision = (($elem_editModeVis == "1") && ($isNewRecord != true)) ? $row["examinedNoChange"] : "0" ;
				//Comments
				//$elem_visComments = stripslashes($row["vis_comments"]);
				//Comments			
				//UT Elems //
				$elem_utElems = (!isset($isNewRecord) || ($isNewRecord != true)) ? $row["ut_elem"] : "" ; 
			}
			
			//Chart Vision Lasik	
			$oVisLasik = new VisLasik($patient_id, $form_id);
			$ar_vis_lasik = $oVisLasik->getLasikSection();
			extract($ar_vis_lasik);
			
			//print_r($ar_vis_lasik);
			//$tmp_lasik_trgt_method = "el_lasik_trgt_method";
			//echo "<br/>".$$tmp_lasik_trgt_method;exit();
			
			
			
		//}
		
		// Correct Old Data
		$elem_examDateDistance = $elem_examDateARAK = $elem_examDatePC = $elem_examDateMR = $elem_examDateCR = $todate;

		// Separate NoChange Value
		$isClubbed = strpos($elem_visAkDesc,"(*|*)");
		if($isClubbed !== false){
			$elem_examinedNoChange = substr($elem_visAkDesc,$isClubbed+5);
			$elem_visAkDesc = trim(substr($elem_visAkDesc,0,$isClubbed));
		}
		// Separate NoChange Value
		$isClubbed = strpos($elem_disDesc,"(*|*)");

		if($isClubbed !== false){
			$elem_disDesc = trim(substr($elem_disDesc,0,$isClubbed));
		}

		if(isset($isNewRecord) && ($isNewRecord == true)){
			$elem_noChangeVision = "0";
		}

		// Separate Dates
		list($elem_visNearDesc,$elem_examDateDistance) = $this->extractDate($elem_visNearDesc);
		list($elem_visArDesc,$elem_examDateARAK) = $this->extractDate($elem_visArDesc);
		list($elem_visPcDesc,$elem_examDatePC) = $this->extractDate($elem_visPcDesc);
		list($elem_visMrDesc,$elem_examDateMR) = $this->extractDate($elem_visMrDesc);
		list($elem_visBatDesc,$elem_examDateCR) = $this->extractDate($elem_visBatDesc);

		$elem_examDateDistance = (!empty($elem_examDateDistance)) ? wv_formatDate($elem_examDateDistance) : $todate;
		$elem_examDateARAK = (!empty($elem_examDateARAK)) ? wv_formatDate($elem_examDateARAK) : $todate;
		$elem_examDatePC = (!empty($elem_examDatePC)) ? wv_formatDate($elem_examDatePC) : $todate;
		$elem_examDateMR = (!empty($elem_examDateMR)) ? wv_formatDate($elem_examDateMR) : $todate;
		$elem_examDateCR = (!empty($elem_examDateCR)) ? wv_formatDate($elem_examDateCR) : $todate;

		// Desc Default --------
		if(!isset($GLOBALS["STOP_PRV_VISION_DESC"]) || empty($GLOBALS["STOP_PRV_VISION_DESC"])){
		
		//clear desc if desc of previous values is saved in it --
		if(!empty($elem_disDesc) && (strpos($elem_disDesc,"OD: ") !== false || strpos($elem_disDesc,"OS: ") !== false)){ $elem_disDesc=""; }
		if(!empty($elem_visPcDesc) && (strpos($elem_visPcDesc,"OD: ") !== false || strpos($elem_visPcDesc,"OS: ") !== false)){ $elem_visPcDesc=""; }
		if(!empty($elem_visPcDesc_2) && (strpos($elem_visPcDesc_2,"OD: ") !== false || strpos($elem_visPcDesc_2,"OS: ") !== false)){ $elem_visPcDesc_2=""; }
		if(!empty($elem_visPcDesc_3) && (strpos($elem_visPcDesc_3,"OD: ") !== false || strpos($elem_visPcDesc_3,"OS: ") !== false)){ $elem_visPcDesc_3=""; }
		if(!empty($elem_visMrDesc) && (strpos($elem_visMrDesc,"OD: ") !== false || strpos($elem_visMrDesc,"OS: ") !== false)){ $elem_visMrDesc=""; }
		if(!empty($elem_visMrDescOther) && (strpos($elem_visMrDescOther,"OD: ") !== false || strpos($elem_visMrDescOther,"OS: ") !== false)){ $elem_visMrDescOther=""; }
		if(!empty($elem_mr_desc_3) && (strpos($elem_mr_desc_3,"OD: ") !== false || strpos($elem_mr_desc_3,"OS: ") !== false)){ $elem_mr_desc_3=""; }
		if(!empty($elem_visMrDescOther_3) && (strpos($elem_visMrDescOther_3,"OD: ") !== false || strpos($elem_visMrDescOther_3,"OS: ") !== false)){ $elem_visMrDescOther_3=""; }
		//--
		
		// Distance 
		$elem_disDescLF="";
		if(empty($elem_disDesc)) // || ($isNewRecord == true)
		{
			$elem_disDesc = ""; //"Description: ";
			if(!empty($elem_visDisOdSel1LF)){
				//$elem_disDesc .= "Distance\r\n";
				$elem_disDesc .= "OD: ".$elem_visDisOdSel1LF;
				$elem_disDesc .= (!empty($elem_visDisOdTxt1LF) && ($elem_visDisOdTxt1LF != "20/")) ? ", ".$elem_visDisOdTxt1LF."" : "" ;//", ".$elem_visDisOdSel2LF.", ".$elem_visDisOdTxt2LF." ";
				$elem_disDesc .= " ";
			}

			if(!empty($elem_visDisOsSel1LF)){
				$elem_disDesc .= "OS: ".$elem_visDisOsSel1LF;
				$elem_disDesc .= (!empty($elem_visDisOsTxt1LF) && ($elem_visDisOsTxt1LF != "20/")) ? ", ".$elem_visDisOsTxt1LF." " : "" ;//", ".$elem_visDisOsSel2LF.", ".$elem_visDisOsTxt2LF." ";
			}

			/*
			if($elem_visNear == "1")
			{
				$elem_disDesc .="\r\n";
				$elem_disDesc .= "OD: ".$elem_visNearOdSel1LF.", ".$elem_visNearOdTxt1LF." "; //", ".$elem_visNearOdSel2LF.", ".$elem_visNearOdTxt2LF." ";
				$elem_disDesc .= "OS: ".$elem_visNearOsSel1LF.", ".$elem_visNearOsTxt1LF." "; //", ".$elem_visNearOsSel2LF.", ".$elem_visNearOsTxt2LF." ";
			}*/
			$elem_disDescLF = $elem_disDesc;
		}
		//Pc+MR Desc default
		if(empty($elem_visPcDesc) || empty($elem_visPcDesc_2) || empty($elem_visPcDesc_3) ||
			empty($elem_visMrDesc) || empty($elem_visMrDescOther) || empty($elem_visMrDescOther_3) ){
			for($i=1;$i<=3;$i++){
				
				//PC -----
				$j1 = ($i>1) ? "".$i : "";		
				$zDsc = "elem_visPcDesc".$j1;

				//if(empty($$zDsc) || ($isNewRecord == true)){ //($isNewRecord == true) ||
				if((empty($$zDsc) && (strpos($elem_statusElements, $zDsc."=1") === false))){
					$j2 = ($i>1) ? $i : "";
					
					$zDscLf = "elem_visPcDesc".$j1."LF";
					$$zDscLf = "";
				
					$$zDsc = "";
					$zsel1Lf = "elem_visPcOdS".$j2."LF";
					if(!empty($$zsel1Lf)){
						$$zDsc .= "OD: ".$$zsel1Lf.", ";
						//$zsLf = "elem_visPcOdS".$j2."LF";
						//$$zDsc .= (!empty($$zsLf)) ? $$zsLf.", " : "";
						$zcLf = "elem_visPcOdC".$j2."LF";
						$$zDsc .= (!empty($$zcLf)) ? $$zcLf.", " : "";
						$zaLf = "elem_visPcOdA".$j2."LF";
						$$zDsc .= (!empty($$zaLf)) ? $$zaLf.", " : "";
						$$zDsc = substr($$zDsc, 0, -2);
					}
					$zsel1Lf = "elem_visPcOsS".$j2."LF";
					if(!empty($$zsel1Lf)){
						$$zDsc .= " OS: ".$$zsel1Lf.", ";
						//$zsLf = "elem_visPcOsS".$j2."LF";
						//$$zDsc .= (!empty($$zsLf)) ? $$zsLf.", " : "";
						$zcLf = "elem_visPcOsC".$j2."LF";
						$$zDsc .= (!empty($$zcLf)) ? $$zcLf.", " : "";
						$zaLf = "elem_visPcOsA".$j2."LF";
						$$zDsc .= (!empty($$zaLf)) ? $$zaLf.", " : "";
						$$zDsc = substr($$zDsc, 0, -2);
					}
					$$zDscLf = $$zDsc;
				}
				
				//PC-----
				//MR -----
				
				$j2="";	
				if($i >= 2){
					$j2 = "Other";
				}

				//$zDsc = ($i==3) ? "elem_mr_desc_3" : "elem_visMrDesc".$j2;
				$zDsc = ($i==3) ? "elem_visMrDesc".$j2."_3" : "elem_visMrDesc".$j2;
				
				//$$zDscLf = "";

				//if(empty($$zDsc) || ($isNewRecord == true)){ //($isNewRecord == true) ||
				if((empty($$zDsc) && (strpos($elem_statusElements, $zDsc."=1") === false))){
					$$zDsc = "";
					$j3="";
					if($i == 3){
						$j3="_".$i;
					}
					$zDscLf = $zDsc."LF";
					
					
					//25june12 :: MR1 - If they type in the comment the only carry the comments forward do not show the previous MR values.
					// Notes : 26-10-2012 it will work now because isnewRecord check is removed
					/*
					if(!empty($$zDscLf)){
					
						$$zDsc = $$zDscLf;
					
					}else{
					*/				
						$tmpzDt="";
						$zsLf = "elem_visMr".$j2."OdS".$j3."LF";
						if(!empty($$zsLf)){
							$$zDsc .= wv_formatDate($elem_examDateMRLF)." ";
							$$zDsc .= "OD: ".$$zsLf.", ";
							$zcLf = "elem_visMr".$j2."OdC".$j3."LF";
							$$zDsc .= (!empty($$zcLf)) ? $$zcLf.", " : "";
							$zaLf = "elem_visMr".$j2."OdA".$j3."LF";
							$$zDsc .= (!empty($$zaLf)) ? $$zaLf.", " : "";
							$zaddLf = "elem_visMr".$j2."OdAdd".$j3."LF";
							$$zDsc .= (!empty($$zaddLf)) ? $$zaddLf.", " : "";
							$$zDsc = substr($$zDsc, 0, -2);
						}
						$zsLf = "elem_visMr".$j2."OsS".$j3."LF";
						if(!empty($$zsLf)){
							if(empty($$zDsc)){ $$zDsc .= wv_formatDate($elem_examDateMRLF)." ";  }
							$$zDsc .= " OS: ".$$zsLf.", ";
							$zcLf = "elem_visMr".$j2."OsC".$j3."LF";
							$$zDsc .= (!empty($$zcLf)) ? $$zcLf.", " : "";
							$zaLf = "elem_visMr".$j2."OsA".$j3."LF";
							$$zDsc .= (!empty($$zaLf)) ? $$zaLf.", " : "";
							$zaddLf = "elem_visMr".$j2."OsAdd".$j3."LF";
							$$zDsc .= (!empty($$zaddLf)) ? $$zaddLf.", " : "";
							$$zDsc = substr($$zDsc, 0, -2);
						}
						
						$$zDscLf = $$zDsc;
					//}
					
				}		
				
				//MR------		
				
			}
		}
		//
		}//end if
		// Desc Default --------

		//Mr1
		/*
		$arrTmp = array("elem_visMrOdS"=>$elem_visMrOdS,"elem_visMrOdC"=>$elem_visMrOdC,
						"elem_visMrOdA"=>$elem_visMrOdA,
						"elem_visMrOdAdd"=>$elem_visMrOdAdd,
						"elem_visMrOsS"=>$elem_visMrOsS,"elem_visMrOsC"=>$elem_visMrOsC,
						"elem_visMrOsA"=>$elem_visMrOsA,
						"elem_visMrOsAdd"=>$elem_visMrOsAdd);
		list($isMr1Done, $isMr1DoneCur) = $this->isValFilled($arrTmp,1,$elem_statusElements);

		//Mr2
		$arrTmp = array("elem_visMrOtherOdS"=>$elem_visMrOtherOdS,"elem_visMrOtherOdC"=>$elem_visMrOtherOdC,
						"elem_visMrOtherOdA"=>$elem_visMrOtherOdA,
						"elem_visMrOtherOdAdd"=>$elem_visMrOtherOdAdd,
						"elem_visMrOtherOsS"=>$elem_visMrOtherOsS,"elem_visMrOtherOsC"=>$elem_visMrOtherOsC,
						"elem_visMrOtherOsA"=>$elem_visMrOtherOsA,"elem_visMrOtherOsAdd"=>$elem_visMrOtherOsAdd);
		list($isMr2Done, $isMr2DoneCur) = $this->isValFilled($arrTmp,1,$elem_statusElements);

		//Mr3
		$arrTmp = array("elem_visMrOtherOdS_3"=>$elem_visMrOtherOdS_3,"elem_visMrOtherOdC_3"=>$elem_visMrOtherOdC_3,
						"elem_visMrOtherOdA_3"=>$elem_visMrOtherOdA_3,
						"elem_visMrOtherOdAdd_3"=>$elem_visMrOtherOdAdd_3,
						"elem_visMrOtherOsS_3"=>$elem_visMrOtherOsS_3,"elem_visMrOtherOsC_3"=>$elem_visMrOtherOsC_3,
						"elem_visMrOtherOsA_3"=>$elem_visMrOtherOsA_3,
						"elem_visMrOtherOsAdd_3"=>$elem_visMrOtherOsAdd_3);
		list($isMr3Done, $isMr3DoneCur) = $this->isValFilled($arrTmp,1,$elem_statusElements);

		//Mr
		$isMrDoneCur = ($isMr1DoneCur || $isMr2DoneCur || $isMr3DoneCur) ? true : false;
		*/


		//Set Visibility of Procedures based on template values ----------
		//Distance,Near,AR,AK,PC 1,PC 2,PC 3,MR 1,MR 2,MR 3,BAT,

		$ctmpDisWS = $ctmpDis = $ctmpNear = $ctmpAr = $ctmpAk = "";
		$ctmpPcWS = $ctmpPc1 = $ctmpPc2 = $ctmpPc3 = "";
		$ctmpMr1  = $ctmpMr2 = $ctmpMr3 = "";
		$ctmpBat = ""; $ctmpLasik="";
		$ctmpContLens = "";
		$ctmpVisWS = "";
		$ctmpCvfWS = $ctmpCvf = $ctmpAg = $ctmpIcpClr = $ctmpStereo = "";
		$ctmpDip = $ctmpW4Dot = $ctmpRetino = $ctmpExophth = $ctmpVisComm = "";
		$ctmpVisFullWS = $ctmpCycRetino = "";
		// --

		if(isset($arrTempProc)){
			//
			$arrTmp = array("Distance"=>"ctmpDis","Near"=>"ctmpNear","AR"=>"ctmpAr","AK"=>"ctmpAk",
						"PC 1"=>"ctmpPc1","PC 2"=>"ctmpPc2","PC 3"=>"ctmpPc3",
						"MR 1"=>"ctmpMr1","MR 2"=>"ctmpMr2","MR 3"=>"ctmpMr3",
						"BAT"=>"ctmpBat","PAM"=>"ctmpPam","LASIK"=>"ctmpLasik",
						"Contact Lens"=>"ctmpContLens",
						"CVF"=>"ctmpCvf","Amsler Grid"=>"ctmpAg","ICP Color Plate"=>"ctmpIcpClr",
						"Stereopsis"=>"ctmpStereo","Diplopia"=>"ctmpDip","W4Dot"=>"ctmpW4Dot",
						"Retinoscopy"=>"ctmpRetino","Exophthalmometer"=>"ctmpExophth","Cycloplegic Retinoscopy"=>"ctmpCycRetino",
						"Comments"=>"ctmpVisComm"
						);
			foreach($arrTmp as $key => $val){
				if(!in_array($key,$arrTempProc)){
					$$val = "clsProcHider";
				}
			}

			//Whole DisSec
			if(!empty($ctmpDis) && !empty($ctmpNear) && !empty($ctmpAr) && !empty($ctmpAk) && !empty($ctmpBat) && !empty($ctmpPam)){
				$ctmpDisWS = "clsProcHider";
			}

			//Whole PcSec
			if(!empty($ctmpPc1) && !empty($ctmpPc2) && !empty($ctmpPc3)){
				$ctmpPcWS = "clsProcHider";
			}

			//Whole MrSec
			if(!empty($ctmpMr1) && !empty($ctmpMr2) && !empty($ctmpMr3) && !empty($ctmpBat)){
				$ctmpMrWS = "clsProcHider";
			}

			//Whole Vision
			if(!empty($ctmpDisWS) && !empty($ctmpPcWS) && !empty($ctmpMrWS)&& !empty($ctmpContLens)){
				$ctmpVisWS = "clsProcHider";
			}

			//Whole CvfWS
			if(!empty($ctmpCvf) &&  !empty($ctmpAg) && !empty($ctmpIcpClr) &&  !empty($ctmpStereo) &&
				!empty($ctmpDip) && !empty($ctmpW4Dot) && !empty($ctmpRetino) && !empty($ctmpExophth) && !empty($ctmpVisComm)
			   ){
				$ctmpCvfWS = "clsProcHider";
			}

			//Whole VisFullWS
			if(!empty($ctmpVisWS) && !empty($ctmpCvfWS)){
				$ctmpVisFullWS = "clsProcHider";
			}
		}

		if(empty($ctmpDisWS)){
			$distancePopupCall=$pcPopupCall=$mrPopupCall=$arPopupCall='';
		}
		//--
		$elem_examDate = wv_formatDate(date('Y-m-d'));
		
		//
		//ADD. Acuity -- 
		$tmpAddAcu= (!empty($elem_visDisOdSel3) || !empty($elem_visDisOdTxt3) ||
			!empty($elem_visDisOsSel3) || !empty($elem_visDisOsTxt3) || !empty($elem_visDisAct3)) ? "" : "hideAll";
		
		//Cyclo AR
		if(!empty($elem_visCycArOdS) || !empty($elem_visCycArOdC) ||
			!empty($elem_visCycArOdA) || !empty($elem_visCycArOdSel1)){
				$posOptCycAR="positive";
		}else{
				$posOptCycAR="";
		}
		
		//PC
		//Pc1
		$arrTmp = array("elem_visPcOdSel1"=>$elem_visPcOdSel1,"elem_visPcOdS"=>$elem_visPcOdS,
						"elem_visPcOdC"=>$elem_visPcOdC,"elem_visPcOdA"=>$elem_visPcOdA,
						"elem_visPcOdAdd"=>$elem_visPcOdAdd,"elem_visPcOsSel1"=>$elem_visPcOsSel1,
						"elem_visPcOsS"=>$elem_visPcOsS,"elem_visPcOsC"=>$elem_visPcOsC,
						"elem_visPcOsA"=>$elem_visPcOsA,"elem_visPcOsAdd"=>$elem_visPcOsAdd );
		list($isPc1Done, $isPc1DoneCur)  = $this->isValFilled($arrTmp,1,$elem_statusElements);
		//Pc2
		$arrTmp = array("elem_visPcOdSel12"=>$elem_visPcOdSel12,"elem_visPcOdS2"=>$elem_visPcOdS2,
						"elem_visPcOdC2"=>$elem_visPcOdC2,"elem_visPcOdA2"=>$elem_visPcOdA2,
						"elem_visPcOdAdd2"=>$elem_visPcOdAdd2,"elem_visPcOsSel12"=>$elem_visPcOsSel12,
						"elem_visPcOsS2"=>$elem_visPcOsS2,"elem_visPcOsC2"=>$elem_visPcOsC2,
						"elem_visPcOsA2"=>$elem_visPcOsA2,"elem_visPcOsAdd2"=>$elem_visPcOsAdd2 );
		list($isPc2Done, $isPc2DoneCur)  = $this->isValFilled($arrTmp,1,$elem_statusElements);

		//Pc
		//$isPcDoneCur = ($isPc1DoneCur || $isPc2DoneCur) ? true : false;
		$isPcDone = ($isPc1Done || $isPc2Done) ? true : false;

		//if($isPcDoneCur){
		if($isPcDone){
			//$tArow_pc = $html_entity_show;
			$inclss_pc = "in";
		}else{
			$div_pc_css = "hideAll";
			//$tArow_pc = $html_entity_hide;
		}

		//Prism 1
		if(!empty($elem_visPcOdP) || !empty($elem_visPcOdSlash) ||
		!empty($elem_visPcOsP) || !empty($elem_visPcOsSlash)){
			$posPc1Prism="positive";
		}else{
			$posPc1Prism="";
		}

		//<!-- PC2 -->
		if(!empty($elem_visPcOdSel12)||!empty($elem_visPcOdS2)||!empty($elem_visPcOdC2)||
			!empty($elem_visPcOsSel12)||!empty($elem_visPcOsS2)||!empty($elem_visPcOsC2)){
			$cssPc2 = "";
		}else{
			$cssPc2 = "hideAll";
		}

		//Prism 2 --
		if(!empty($elem_visPcOdP2) || !empty($elem_visPcOdSlash2) ||
		!empty($elem_visPcOsP2) || !empty($elem_visPcOsSlash2)){
			$posPc2Prism = "positive";
		}else{
			$posPc2Prism = "";
		}

		//<!-- PC3 -->

		if(!empty($elem_visPcOdSel13) || !empty($elem_visPcOdS3) || !empty($elem_visPcOdC3) || !empty($elem_visPcOdA3)||
			!empty($elem_visPcOsSel13) || !empty($elem_visPcOsS3) || !empty($elem_visPcOsC3) || !empty($elem_visPcOsA3)){
			$posPc3="positive";
			$len_pc=3;
		}else{
			$posPc3="";
		}

		//<!-- PC1 Over Ref -->

		if(!empty($elem_visPcOdOverrefS) || !empty($elem_visPcOdOverrefC) ||
			!empty($elem_visPcOdOverrefA) || 
			!empty($elem_visPcOsOverrefS) || !empty($elem_visPcOsOverrefC) ||
			!empty($elem_visPcOsOverrefA)
		){	
			$cssPc1_ovrref = "";
		}else{
			$cssPc1_ovrref = "hideAll";
		}

		//<!-- PC2 Over Ref -->
		if(!empty($elem_visPcOdOverrefS2)|| !empty($elem_visPcOdOverrefC2) ||
			!empty($elem_visPcOsOverrefS2) || !empty($elem_visPcOsOverrefC2)){
			$cssPc2_ovrref = "";
		}else{
			$cssPc2_ovrref = "hideAll";
		}
		
		//mr--
		//Mr1
		$arrTmp = array("elem_visMrOdS"=>$elem_visMrOdS,"elem_visMrOdC"=>$elem_visMrOdC,
						"elem_visMrOdA"=>$elem_visMrOdA,"elem_visMrOdAdd"=>$elem_visMrOdAdd,
						"elem_visMrOsS"=>$elem_visMrOsS,"elem_visMrOsC"=>$elem_visMrOsC,
						"elem_visMrOsA"=>$elem_visMrOsA,"elem_visMrOsAdd"=>$elem_visMrOsAdd);
		list($isMr1Done, $isMr1DoneCur) = $this->isValFilled($arrTmp,1,$elem_statusElements);

		//Mr2
		$arrTmp = array("elem_visMrOtherOdS"=>$elem_visMrOtherOdS,"elem_visMrOtherOdC"=>$elem_visMrOtherOdC,
						"elem_visMrOtherOdA"=>$elem_visMrOtherOdA,"elem_visMrOtherOdAdd"=>$elem_visMrOtherOdAdd,
						"elem_visMrOtherOsS"=>$elem_visMrOtherOsS,"elem_visMrOtherOsC"=>$elem_visMrOtherOsC,
						"elem_visMrOtherOsA"=>$elem_visMrOtherOsA,"elem_visMrOtherOsAdd"=>$elem_visMrOtherOsAdd);
		list($isMr2Done, $isMr2DoneCur) = $this->isValFilled($arrTmp,1,$elem_statusElements);

		//Mr
		//$isMrDoneCur = ($isMr1DoneCur || $isMr2DoneCur) ? true : false;
		$isMrDone = ($isMr1Done || $isMr2Done) ? true : false;

		//if($isMrDoneCur){
		if($isMrDone){
			$tArow_mr = $html_entity_show;
			$inclss_mr = "in";
		}else{
			$div_mr_css = "hideAll";
			$tArow_mr = $html_entity_hide;
		}

		/*MR 1*/
		if(!empty($elem_visMrOdP)||!empty($elem_visMrOdSlash)||
		!empty($elem_visMrOsP) || !empty($elem_visMrOsSlash)){
			$posMr1Prism="positive";
		}else{
			$posMr1Prism="";
		}
		
		//<!-- MR 2 -->
		if(!empty($elem_visMrOtherOdS)|| !empty($elem_visMrOtherOdC)|| !empty($elem_visMrOtherOdA) ||
			!empty($elem_visMrOtherOsS) || !empty($elem_visMrOtherOsC) || !empty($elem_visMrOtherOsA)){
			$css_mr2="";
		}else{
			$css_mr2="hideAll";
		}

		//Prism --
		if(!empty($elem_visMrOtherOdP)|| !empty($elem_visMrOtherOdSlash) ||
			!empty($elem_visMrOtherOsP) || !empty($elem_visMrOtherOsSlash)){
			$posMr2Prism="positive";
		}else{
			$posMr2Prism="";
		}

		//MR 3 --
		if(!empty($elem_visMrOtherOdS_3) || !empty($elem_visMrOtherOdC_3) || !empty($elem_visMrOtherOdA_3) ||
			!empty($elem_visMrOtherOsS_3) || !empty($elem_visMrOtherOsC_3) || !empty($elem_visMrOtherOsA_3)){
			$posMr3="positive";
			if(empty($len_mr)){$len_mr=4;}
		}else{
			$posMr3="";
		}

		//BAT --
		if(empty($ctmpBat) && ((!empty($elem_visBatNlOd) && $elem_visBatNlOd != "20/") || (!empty($elem_visBatLowOd) && $elem_visBatLowOd != "20/") ||
			(!empty($elem_visBatNlOs)&&$elem_visBatNlOs!="20/") || (!empty($elem_visBatLowOs)&&$elem_visBatLowOs!="20/"))){
			$posBat="positive";
			$tmp_dis_bat="active in ";
		}else{
			$posBat=""; $tmp_dis_bat="";
			//BAT/PAM
			$elem_visPamOdsel2_tmp = (!empty($elem_visPamOdsel2) && $elem_visPamOdsel2!="CC") ? $elem_visPamOdsel2 : "";
			$tmp_dis_pam = trim($elem_visPam.$elem_visPamOdTxt1.$elem_visPamOsTxt1.$elem_visPamOuTxt1.
							$elem_visPamOdsel2_tmp.$elem_visPamOdTxt2.$elem_visPamOsTxt2.$elem_visPamOuTxt2.
							$elem_pamDesc);
			$tmp_dis_pam = str_replace("20/","",$tmp_dis_pam);
			if(empty($ctmpPam) && !empty($tmp_dis_pam)){ $tmp_dis_pam="active in ";  }else{ $tmp_dis_pam=""; $tmp_dis_bat="active in "; }
		}

		//<!-- MR GLPH -->
		if(!empty($elem_visMrOtherOdSel2) || !empty($elem_visMrOtherOdSel2Vision) ||
			!empty($elem_visMrOtherOsSel2) || !empty($elem_visMrOtherOsSel2Vision)){
			$css_mr2_glph = "";
		}else{
			$css_mr2_glph = "hideAll";
		}
		
		/*
		//CVF
		if(in_array("CVF",$arrTempProc)){
			$oCVF = new CVF($patient_id, $form_id);
			$data_cvf_section = $oCVF->getWorkViewSummery();
			$flg_temp_vision=1;		
		}
		
		//AmslerGrid
		if(in_array("Amsler Grid",$arrTempProc)){
			$oAmslerGrid = new AmslerGrid($patient_id, $form_id);
			$data_amsler_section = $oAmslerGrid->getWorkViewSummery();
			$flg_temp_vision=1;
		}
		*/
		
		//CP control
		list($htm_cp_control, $is_pos_icp) = $this->getICP();
		//Stereopsis
		list($htm_stereopsis, $is_pos_stereo) = $this->getStereo();		
		//worth for dot
		list($htm_worth_for_dot, $is_pos_w4dot) = $this->getW4Dot();		
		
		//Other:: if values exists, set open 
		$tmp = trim($elem_visExoOdS.$elem_visExoOdC.$elem_visExoOdA.$elem_visExoOsS.
		$elem_visExoOsC.$elem_visExoOsA.$elem_visExoOuS.$elem_visExoOuC.$elem_visExoOuA.$elem_visCycloOdS.$elem_visCycloOdC.$elem_visCycloOdA.
		$elem_visCycloOsS.$elem_visCycloOsC.$elem_visCycloOsA.$elem_visCycloOuS.$elem_visCycloOuC.$elem_visCycloOuA.
		$elem_visRetPD.$elem_visRetOd.$elem_visRetOs.$is_pos_icp.$is_pos_stereo.$is_pos_w4dot);
		if(!empty($tmp)){ $inclss_other_vis_exm=" in "; }
		
		//AR/ARC
		$tmp_dis_ar = trim($elem_visArOdS.$elem_visArOdC.$elem_visArOdA.$elem_visArOdSel1.
				$elem_visArOsS.$elem_visArOsC.$elem_visArOsA.$elem_visArOsSel1.
				$elem_visArDesc.$elem_visArRefPlace);
		$tmp_dis_ar = str_replace("20/","",$tmp_dis_ar);
		if(!empty($tmp_dis_ar)){ $tmp_dis_ar="active in ";  }
		else{ 
			$tmp_dis_ar=""; 
			//arc		
			$tmp_dis_arc = trim($elem_visCycArOdS.$elem_visCycArOdC.$elem_visCycArOdA.$elem_visCycArOdSel1.
							$elem_visCycArOsS.$elem_visCycArOsC.$elem_visCycArOsA.$elem_visCycArOsSel1.
							$elem_visCycArDesc);
			$tmp_dis_arc = str_replace("20/","",$tmp_dis_arc);
			if(!empty($tmp_dis_arc)){ $tmp_dis_arc="active in ";  }else{ $tmp_dis_arc=""; $tmp_dis_ar="active in "; }
		}

		
		//Lasik	
		$arrTmp = array("el_lasik_trgt_method"=>$el_lasik_trgt_method,"el_visLasikTrgtDate"=>$el_visLasikTrgtDate,
						"el_lasik_trgt_intervention"=>$el_lasik_trgt_intervention,"el_visLasikTrgtMicKera"=>$el_visLasikTrgtMicKera,
						"el_lasik_trgt_Excimer"=>$el_lasik_trgt_Excimer,"el_lasik_trgt_mode"=>$el_lasik_trgt_mode,
						"el_lasik_trgt_opti_zone"=>$el_lasik_trgt_opti_zone);
		list($isLasikDone, $isLasikDoneCur) = $this->isValFilled($arrTmp,1,$elem_statusElements);
		if($isLasikDone){
			$tArow_lasik = $html_entity_show;
			$inclss_lasik = "in";
		}else{
			$div_lasik_css = "hideAll";
			$tArow_mr = $html_entity_hide;
		}
		
		//exit($inclss_other_vis_exm);
		
		
		//---------------
		//Menu--
		$menu_visSnellan = wv_get_simple_menu($arrSnellan,"menu_snellan","elem_visSnellan", 1);
		$menu_visDisOdTxt1 = wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visDisOdTxt1", 1);
		$menu_visDisOdTxt2 = wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visDisOdTxt2", 1);
		$menu_visDisOsTxt1 = wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visDisOsTxt1", 1);
		$menu_visDisOsTxt2 = wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visDisOsTxt2", 1);
		$menu_visDisOuTxt1 = wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visDisOuTxt1", 1);
		$menu_visDisOuTxt2 = wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visDisOuTxt2", 1);
		$menu_visSnellan_near = wv_get_simple_menu($arrSnellan,"menu_snellan","elem_visSnellan_near", 1);
		$menu_visNearOdTxt1 = wv_get_simple_menu($arrAcuitiesNear,"menu_acuitiesNear","elem_visNearOdTxt1", 1);
		$menu_visNearOdTxt2 = wv_get_simple_menu($arrAcuitiesNear,"menu_acuitiesNear","elem_visNearOdTxt2", 1);
		$menu_visNearOsTxt1 = wv_get_simple_menu($arrAcuitiesNear,"menu_acuitiesNear","elem_visNearOsTxt1", 1);
		$menu_visNearOsTxt2 = wv_get_simple_menu($arrAcuitiesNear,"menu_acuitiesNear","elem_visNearOsTxt2", 1);
		$menu_visNearOuTxt1 = wv_get_simple_menu($arrAcuitiesNear,"menu_acuitiesNear","elem_visNearOuTxt1", 1);
		$menu_visNearOuTxt2 = wv_get_simple_menu($arrAcuitiesNear,"menu_acuitiesNear","elem_visNearOuTxt2", 1);
		/*
		$menu_visPcOdOverrefV = wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visPcOdOverrefV", 1);
		$menu_visPcOsOverrefV = wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visPcOsOverrefV", 1);
		$menu_visPcOdOverrefV2 = wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visPcOdOverrefV2", 1);
		$menu_visPcOsOverrefV2 = wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visPcOsOverrefV2", 1);
		$menu_visPcOdOverrefV3 = wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visPcOdOverrefV3", 1);
		$menu_visPcOsOverrefV3 = wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visPcOsOverrefV3", 1);
		*/
		
		
		for($i=1;$i<=$len_pc;$i++){
			$inx1 = "";
			if($i>1){$inx1 = "".$i;}
			$tmp = "menu_visPcOdOverrefV".$inx1;
			$$tmp = wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visPcOdOverrefV".$inx1, 1);
			$tmp = "menu_visPcOsOverrefV".$inx1;
			$$tmp = wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visPcOsOverrefV".$inx1, 1);	
			
		}
		
		for($i=1;$i<=$len_mr;$i++){
			$inx1 = $inx2 = "";
			if($i>1){
				$inx1 = "Other";
				if($i>2){
					$inx2 = "_".$i;
				}
			}
			$tmp = "menu_visMr".$inx1."OdTxt1".$inx2;
			$$tmp = wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visMr".$inx1."OdTxt1".$inx2, 1);
			$tmp = "menu_visMr".$inx1."OdTxt2".$inx2;
			$$tmp = wv_get_simple_menu($arrAcuitiesNear,"menu_acuitiesNear","elem_visMr".$inx1."OdTxt2".$inx2, 1);
			$tmp = "menu_visMr".$inx1."OsTxt1".$inx2;
			$$tmp = wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visMr".$inx1."OsTxt1".$inx2, 1);
			$tmp = "menu_visMr".$inx1."OuTxt1".$inx2;
			$$tmp = wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visMr".$inx1."OuTxt1".$inx2, 1);
			$tmp = "menu_visMr".$inx1."OsTxt2".$inx2;
			$$tmp = wv_get_simple_menu($arrAcuitiesNear,"menu_acuitiesNear","elem_visMr".$inx1."OsTxt2".$inx2, 1);
			$tmp = "menu_visMr".$inx1."OdSel2Vision".$inx2;
			$$tmp = wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visMr".$inx1."OdSel2Vision".$inx2, 1);
			$tmp = "menu_visMr".$inx1."OsSel2Vision".$inx2;
			$$tmp = wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visMr".$inx1."OsSel2Vision".$inx2, 1);
		}
		/*
		$menu_visMrOtherOdTxt1 = wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visMrOtherOdTxt1", 1);
		$menu_visMrOtherOdTxt2 = wv_get_simple_menu($arrAcuitiesNear,"menu_acuitiesNear","elem_visMrOtherOdTxt2", 1);
		$menu_visMrOtherOsTxt1 = wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visMrOtherOsTxt1", 1);
		$menu_visMrOtherOuTxt1 = wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visMrOtherOuTxt1", 1);
		$menu_visMrOtherOsTxt2 = wv_get_simple_menu($arrAcuitiesNear,"menu_acuitiesNear","elem_visMrOtherOsTxt2", 1);
		$menu_visMrOtherOdSel2Vision = wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visMrOtherOdSel2Vision", 1);
		$menu_visMrOtherOsSel2Vision = wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visMrOtherOsSel2Vision", 1);
		
		$menu_visMrOtherOdTxt1_3 = wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visMrOtherOdTxt1_3", 1);
		$menu_visMrOtherOdTxt2_3 = wv_get_simple_menu($arrAcuitiesNear,"menu_acuitiesNear","elem_visMrOtherOdTxt2_3", 1);
		$menu_visMrOtherOsTxt1_3 = wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visMrOtherOsTxt1_3", 1);
		$menu_visMrOtherOuTxt1_3 = wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visMrOtherOuTxt1_3", 1);
		$menu_visMrOtherOsTxt2_3 = wv_get_simple_menu($arrAcuitiesNear,"menu_acuitiesNear","elem_visMrOtherOsTxt2_3", 1);
		$menu_visMrOtherOdSel2Vision_3 = wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis ","elem_visMrOtherOdSel2Vision_3", 1);
		$menu_visMrOtherOsSel2Vision_3 = wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visMrOtherOsSel2Vision_3", 1);
		if($len_mr==4){
			$menu_visMrOtherOdTxt1_4 = wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visMrOtherOdTxt1_4", 1);
			$menu_visMrOtherOdTxt2_4 = wv_get_simple_menu($arrAcuitiesNear,"menu_acuitiesNear","elem_visMrOtherOdTxt2_4", 1);
			$menu_visMrOtherOsTxt1_4 = wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visMrOtherOsTxt1_4", 1);
			$menu_visMrOtherOuTxt1_4 = wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visMrOtherOuTxt1_4", 1);
			$menu_visMrOtherOsTxt2_4 = wv_get_simple_menu($arrAcuitiesNear,"menu_acuitiesNear","elem_visMrOtherOsTxt2_4", 1);
			$menu_visMrOtherOdSel2Vision_4 = wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis ","elem_visMrOtherOdSel2Vision_4", 1);
			$menu_visMrOtherOsSel2Vision_4 = wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visMrOtherOsSel2Vision_4", 1);
		}
		*/
		$menu_visDisOdTxt3 = wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visDisOdTxt3", 1);
		$menu_visDisOsTxt3 = wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visDisOsTxt3", 1);
		$menu_visDisOuTxt3 = wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visDisOuTxt3", 1);
		$menu_visDisOdTxt4 = wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visDisOdTxt4", 1);
		$menu_visDisOsTxt4 = wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visDisOsTxt4", 1);
		$menu_visDisOuTxt4 = wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visDisOuTxt4", 1);	
		
		
		$menu_visPam = wv_get_simple_menu($arrSnellan,"menu_snellan","elem_visPam", 1);
		$menu_visPamOdTxt1 = wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visPamOdTxt1", 1);
		$menu_visPamOdTxt2 = wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visPamOdTxt2", 1); 
		$menu_visPamOsTxt1 = wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visPamOsTxt1", 1);
		$menu_visPamOsTxt2 = wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visPamOsTxt2", 1); 
		$menu_visPamOuTxt1 = wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visPamOuTxt1", 1);
		$menu_visPamOuTxt = wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis","elem_visPamOuTxt2", 1); 

		//get elem's active status		
		
				
		
		$menu_lasik_trgt_Excimer = wv_get_simple_menu($arrLasik_trgt_Excimer,"menu_Lasik_trgt_Excimer","el_lasik_trgt_Excimer", 1);
		$menu_lasik_trgt_mode = wv_get_simple_menu($arrLasik_trgt_mode,"menu_Lasik_trgt_mode","el_lasik_trgt_mode", 1);
		
		//---------------	

		//Copy Drop Down Options--
		$str_opts_copy=$this->get_copy_drop_down($len_pc, $len_mr);	
		//Copy Drop Down Options--
		
		
		//include --
		ob_start();
		$tmp = str_replace("\x", "\\x", $GLOBALS['incdir']);		
		include($tmp."/chart_notes/view/vision.php");
		$out2 = ob_get_contents();
		ob_end_clean();
		return $out2; 
		
	}
	
	function get_copy_drop_down($lnpc, $lnmr){
		$str = "<option value=\"\">copy</option><option value=\"AR\">AR</option><option value=\"ARC\">ARC</option>";
		$ln = ($lnpc>$lnmr) ? $lnpc : $lnmr;
		$tmp_pc = $tmp_mr = "";
		for($cp=1; $cp<=$ln; $cp++){
			if($cp<=$lnpc){$tmp_pc .= "<option value=\"PC ".$cp."\">PC".$cp."</option>";}  
			if($cp<=$lnmr){$tmp_mr .= "<option value=\"MR ".$cp."\">MR".$cp."</option>";}  
		}
		$str .=$tmp_pc.$tmp_mr;
		return $str;
	}
	
	//Provide values in array format
	function get_mutli_mr_pc_v2($ex_type){
		$patient_id = $this->pid;
		$form_id = $this->fid;
		$arr_ret = array();
		if(empty($ex_type)){ return array($arr_ret, $c); } //
		
		//MR/PC
		$sql = "SELECT 
			c1.*,
			c2.sph as sph_r, c2.cyl as cyl_r, c2.axs as axs_r, c2.ad as ad_r, c2.prsm_p as prsm_p_r, c2.prism as prism_r, c2.slash as slash_r, c2.sel_1 as sel_1_r, c2.sel_2 as sel_2_r, 
			c2.txt_1 as txt_1_r, c2.txt_2 as txt_2_r, c2.sel2v as sel2v_r,
			c2.ovr_s as ovr_s_r, c2.ovr_c as ovr_c_r, c2.ovr_v as ovr_v_r, c2.ovr_a as ovr_a_r,			
			c3.sph as sph_l, c3.cyl as cyl_l, c3.axs as axs_l, c3.ad as ad_l, c3.prsm_p as prsm_p_l, c3.prism as prism_l, c3.slash as slash_l, c3.sel_1 as sel_1_l, c3.sel_2 as sel_2_l, 
			c3.txt_1 as txt_1_l, c3.txt_2 as txt_2_l, c3.sel2v as sel2v_l,
			c3.ovr_s as ovr_s_l, c3.ovr_c as ovr_c_l, c3.ovr_v as ovr_v_l, c3.ovr_a as ovr_a_l			
			FROM chart_vis_master c0
			LEFT JOIN chart_pc_mr c1 ON c1.id_chart_vis_master = c0.id	
			LEFT JOIN chart_pc_mr_values c2 ON c1.id = c2.chart_pc_mr_id AND c2.site='OD'
			LEFT JOIN chart_pc_mr_values c3 ON c1.id = c3.chart_pc_mr_id AND c3.site='OS'	
			WHERE c0.form_id='".$form_id."' AND c0.patient_id = '".$patient_id."' AND c1.ex_type='".$ex_type."' AND c1.delete_by='0'  
			Order By ex_number;
			";			
		
		
		
		$rez = sqlStatement($sql);
		for($i=0;$row=sqlFetchArray($rez);$i++){
			$c = $row["ex_number"];
			if($ex_type == "MR"){
				//MR $c -----------------------------------
				$arr_ret[$c]["elem_mr_type".$c] = $row["mr_type"];
				$arr_ret[$c]["elem_providerIdOther_".$c] = $row["provider_id"]; 
				if(!empty($arr_ret[$c]["elem_providerIdOther_".$c])){	
				$ousr = new User($arr_ret[$c]["elem_providerIdOther_".$c]);
				$arr_ret[$c]["elem_providerNameOther_".$c] = $ousr->getName(1);	
				}	
				//$arr_ret["elem_mrPrism3"] = $row["mrPrism3"];
				$arr_ret[$c]["elem_visMrOtherOdS_".$c] = $row["sph_r"];
				$arr_ret[$c]["elem_visMrOtherOdC_".$c] = $row["cyl_r"];
				$arr_ret[$c]["elem_visMrOtherOdA_".$c] = $row["axs_r"];
				if(!empty($row["txt_1_r"])) $arr_ret[$c]["elem_visMrOtherOdTxt1_".$c] = $row["txt_1_r"];
				$arr_ret[$c]["elem_visMrOtherOdSel2_".$c] = $row["sel_2_r"];
				if(!empty($row["txt_2_r"])) $arr_ret[$c]["elem_visMrOtherOdTxt2_".$c] = $row["txt_2_r"];
				if(trim($row["ad_r"])!="+"){$arr_ret[$c]["elem_visMrOtherOdAdd_".$c] = $row["ad_r"];}
				if(!empty($row["sel2v_r"])) $arr_ret[$c]["elem_visMrOtherOdSel2Vision_".$c] = $row["sel2v_r"];
				
				$arr_ret[$c]["elem_visMrOtherOdP_".$c] = $row["prsm_p_r"];
				$arr_ret[$c]["elem_visMrOtherOdPrism_".$c] = $row["prism_r"];
				$arr_ret[$c]["elem_visMrOtherOdSlash_".$c] = $row["slash_r"];
				$arr_ret[$c]["elem_visMrOtherOdSel1_".$c] = $row["sel_1_r"];
				
				$arr_ret[$c]["elem_visMrOtherOsS_".$c] = $row["sph_l"];
				$arr_ret[$c]["elem_visMrOtherOsC_".$c] = $row["cyl_l"];
				$arr_ret[$c]["elem_visMrOtherOsA_".$c] = $row["axs_l"];
				if(!empty($row["txt_1_l"])) $arr_ret[$c]["elem_visMrOtherOsTxt1_".$c] = $row["txt_1_l"];
				$arr_ret[$c]["elem_visMrOtherOsSel2_".$c] = $row["sel_2_r"];
				if(!empty($row["txt_2_r"])) $arr_ret[$c]["elem_visMrOtherOsTxt2_".$c] = $row["txt_2_r"];
				if(trim($row["ad_l"])!="+"){ $arr_ret[$c]["elem_visMrOtherOsAdd_".$c] = $row["ad_l"];}
				if(!empty($row["sel2v_l"])) $arr_ret[$c]["elem_visMrOtherOsSel2Vision_".$c] = $row["sel2v_l"];
				$arr_ret[$c]["elem_visMrOtherOsP_".$c] = $row["prsm_p_l"];
				$arr_ret[$c]["elem_visMrOtherOsPrism_".$c] = $row["prism_l"];
				$arr_ret[$c]["elem_visMrOtherOsSlash_".$c] = $row["slash_l"];
				$arr_ret[$c]["elem_visMrOtherOsSel1_".$c] = $row["sel_1_l"];
				
				if(!empty($row["mr_ou_txt_1"])){ $arr_ret[$c]["elem_visMrOtherOuTxt1_".$c] = $row["mr_ou_txt_1"]; }
				
				
				
				$arr_ret[$c]["elem_visMrDescOther_".$c] = stripslashes($row["ex_desc"]);
				if(!empty($row["mr_cyclopegic"]) && strpos($row["mr_cyclopegic"],$c) !== false){
					$arr_ret[$c]["elem_mrCyclopegic".$c] = "1";
				}
				
				$arr_ret[$c]["elem_visMrPrismDesc_".$c] = $row["prism_desc"];
				if(!empty($row["mr_pres_date"]) && $row["mr_pres_date"]!="0000-00-00"){
					$arr_ret[$c]["elem_mr_pres_dt_".$c] = wv_formatDate($row["mr_pres_date"]);
					$arr_ret[$c]["strtitle_lblMRGiven".$c]=" title=\"Prescription Date : ".$arr_ret["elem_mr_pres_dt_".$c]."\" "; 
				}else{ $arr_ret[$c]["elem_mr_pres_dt_".$c]=""; }
				$arr_ret[$c]["elem_mrNoneGiven".$c] = $row["mr_none_given"];
				$arr_ret[$c]["elem_mr_type".$c] = $row["mr_type"];
				//MR $c -----------------------------------
			}		
		}
		
		return $arr_ret;
	}
	
	function get_mutli_mr_pc($ex_type, $dos_ymd, $visId, $visIdLF){
		$patient_id = $this->pid;
		$form_id = $this->fid;
		
		$arr_ret = array();
		$c=0;
		
		//
		if(empty($ex_type)){ return array($arr_ret, $c); } //
		
		//	
		
		//
		$arr_dd_menu = Vision::getArrDropDown();
		$arrAcuitiesMrDis = $arr_dd_menu["AcuitiesMrDis"];
		$arrAcuitiesNear = $arr_dd_menu["AcuitiesNear"];
		//--
		
		//MR/PC
		$sql = "SELECT 
			c1.*,
			c2.sph as sph_r, c2.cyl as cyl_r, c2.axs as axs_r, c2.ad as ad_r, c2.prsm_p as prsm_p_r, c2.prism as prism_r, c2.slash as slash_r, c2.sel_1 as sel_1_r, c2.sel_2 as sel_2_r, 
			c2.txt_1 as txt_1_r, c2.txt_2 as txt_2_r, c2.sel2v as sel2v_r,
			c2.ovr_s as ovr_s_r, c2.ovr_c as ovr_c_r, c2.ovr_v as ovr_v_r, c2.ovr_a as ovr_a_r,			
			c3.sph as sph_l, c3.cyl as cyl_l, c3.axs as axs_l, c3.ad as ad_l, c3.prsm_p as prsm_p_l, c3.prism as prism_l, c3.slash as slash_l, c3.sel_1 as sel_1_l, c3.sel_2 as sel_2_l, 
			c3.txt_1 as txt_1_l, c3.txt_2 as txt_2_l, c3.sel2v as sel2v_l,
			c3.ovr_s as ovr_s_l, c3.ovr_c as ovr_c_l, c3.ovr_v as ovr_v_l, c3.ovr_a as ovr_a_l			
			FROM chart_pc_mr c1 
			LEFT JOIN chart_pc_mr_values c2 ON c1.id = c2.chart_pc_mr_id AND c2.site='OD'
			LEFT JOIN chart_pc_mr_values c3 ON c1.id = c3.chart_pc_mr_id AND c3.site='OS'	
			WHERE c1.id_chart_vis_master='".$visId."' AND c1.ex_type='".$ex_type."' AND c1.delete_by='0'  
			Order By ex_number;
			";
		
		$rez = sqlStatement($sql);
		
		$form_id_check=0;
		for($i=0;$row=sqlFetchArray($rez);$i++){
			$cexnum = $row["ex_number"];
			//$arr_ret[] = $row["x"];
			
			//For records of prev visits only
			if(empty($form_id_check)){ $form_id_check=$row["id_chart_vis_master"]; }
			if($form_id_check!=$row["id_chart_vis_master"]){break;}
			//--			
			
			if($ex_type == "MR"){
				
				if(empty($row["provider_id"]) && 
					empty($row["sph_r"]) && empty($row["cyl_r"]) && 
					empty($row["axs_r"]) && (empty($row["ad_r"]) || trim($row["ad_r"])=="20/") && 
					(empty($row["txt_1_r"]) || trim($row["txt_1_r"])=="20/") && 
					(empty($row["sel_2_r"]) || trim($row["sel_2_r"])=="20/") && 
					(empty($row["txt_2_r"]) || trim($row["txt_2_r"])=="20/") && 
					(empty($row["sel2v_r"]) || trim($row["sel2v_r"])=="20/") &&
					empty($row["prsm_p_r"]) && empty($row["prism_r"]) && empty($row["slash_r"]) && empty($row["sel_1_r"]) &&
					
					empty($row["sph_l"]) && empty($row["cyl_l"]) && 
					empty($row["axs_l"]) && (empty($row["ad_l"]) || trim($row["ad_l"])=="20/") && 
					(empty($row["txt_1_l"]) || trim($row["txt_1_l"])=="20/") && 
					(empty($row["sel_2_l"]) || trim($row["sel_2_l"])=="20/") && 
					(empty($row["txt_2_l"]) || trim($row["txt_2_l"])=="20/") && 
					(empty($row["sel2v_l"]) || trim($row["sel2v_l"])=="20/") &&
					empty($row["prsm_p_l"]) && empty($row["prism_l"]) && empty($row["slash_l"]) && empty($row["sel_1_l"]) &&
					empty($row["mr_ou_txt_1"]) &&					
					(empty($row["ex_desc"]) || trim($row["ex_desc"])=="Desc.") && 
					empty($row["mr_none_given"]) &&
					empty($row["prism_desc"]) &&
					empty($row["mr_type"])	
					){ continue; }
				
				
				$c = $cexnum;
				//
				$inx1=""; $inx2="";
				if($c > "1"){
					$inx1="Other";
					
					if($c > "2"){
						$inx2="_".$c;
					}
				}
			
				//MR $c -----------------------------------
				$arr_ret["elem_mr_type".$c] = $row["mr_type"];
				$arr_ret["elem_providerId".$inx1.$inx2] = $row["provider_id"];
				if(!empty($arr_ret["elem_providerId".$inx1.$inx2])){	
					$ousr = new User($arr_ret["elem_providerId".$inx1.$inx2]);
					$arr_ret["elem_providerName".$inx1.$inx2] = $ousr->getName(1);	
				}
				$arr_ret["elem_visMr".$inx1."OdS".$inx2] = $row["sph_r"];
				$arr_ret["elem_visMr".$inx1."OdC".$inx2] = $row["cyl_r"];
				$arr_ret["elem_visMr".$inx1."OdA".$inx2] = $row["axs_r"];
				
				if(!empty($row["txt_1_r"])) $arr_ret["elem_visMr".$inx1."OdTxt1".$inx2] = $row["txt_1_r"];
				$arr_ret["elem_visMr".$inx1."OdSel2".$inx2] = $row["sel_2_r"];
				if(!empty($row["txt_2_r"])) $arr_ret["elem_visMr".$inx1."OdTxt2".$inx2] = $row["txt_2_r"];
				if(trim($row["ad_r"])!="+"){$arr_ret["elem_visMr".$inx1."OdAdd".$inx2] = $row["ad_r"];}
				if(!empty($row["sel2v_r"])) $arr_ret["elem_visMr".$inx1."OdSel2Vision".$inx2] = $row["sel2v_r"];
				
				$arr_ret["elem_visMr".$inx1."OdP".$inx2] = $row["prsm_p_r"];
				$arr_ret["elem_visMr".$inx1."OdPrism".$inx2] = $row["prism_r"];
				$arr_ret["elem_visMr".$inx1."OdSlash".$inx2] = $row["slash_r"];
				$arr_ret["elem_visMr".$inx1."OdSel1".$inx2] = $row["sel_1_r"];
				
				$arr_ret["elem_visMr".$inx1."OsS".$inx2] = $row["sph_l"];
				$arr_ret["elem_visMr".$inx1."OsC".$inx2] = $row["cyl_l"];
				$arr_ret["elem_visMr".$inx1."OsA".$inx2] = $row["axs_l"];
				if(!empty($row["txt_1_l"])) $arr_ret["elem_visMr".$inx1."OsTxt1".$inx2] = $row["txt_1_l"];
				$arr_ret["elem_visMr".$inx1."OsSel2".$inx2] = $row["sel_2_r"];
				if(!empty($row["txt_2_l"])) $arr_ret["elem_visMr".$inx1."OsTxt2".$inx2] = $row["txt_2_l"];
				if(trim($row["ad_l"])!="+"){ $arr_ret["elem_visMr".$inx1."OsAdd".$inx2] = $row["ad_l"];}
				if(!empty($row["sel2v_l"])) $arr_ret["elem_visMr".$inx1."OsSel2Vision".$inx2] = $row["sel2v_l"];
				$arr_ret["elem_visMr".$inx1."OsP".$inx2] = $row["prsm_p_l"];
				$arr_ret["elem_visMr".$inx1."OsPrism".$inx2] = $row["prism_l"];
				$arr_ret["elem_visMr".$inx1."OsSlash".$inx2] = $row["slash_l"];
				$arr_ret["elem_visMr".$inx1."OsSel1".$inx2] = $row["sel_1_l"];
				
				if(!empty($row["mr_ou_txt_1"])){ $arr_ret["elem_visMr".$inx1."OuTxt1".$inx2] = $row["mr_ou_txt_1"]; }
				
				$arr_ret["elem_visMrDesc".$inx1.$inx2] = stripslashes($row["ex_desc"]);
				if(!empty($row["mr_cyclopegic"]) && strpos($row["mr_cyclopegic"],$c) !== false){
					$arr_ret["elem_mrCyclopegic".$c] = "1";
				}
				
				$arr_ret["elem_visMrPrismDesc_".$c] = $row["prism_desc"];
				if(!empty($row["mr_pres_date"]) && $row["mr_pres_date"]!="0000-00-00"){
					$arr_ret["elem_mr_pres_dt_".$c] = wv_formatDate($row["mr_pres_date"]);
					$arr_ret["strtitle_lblMRGiven".$c]=" title=\"Prescription Date : ".$arr_ret["elem_mr_pres_dt_".$c]."\" "; 
				}else{ $arr_ret["elem_mr_pres_dt_".$c]=""; }
				$arr_ret["elem_mrNoneGiven".$c] = $row["mr_none_given"];
				
				//MR $c -----------------------------------				
				
			}else if($ex_type == "PC"){
			
				if( empty($row["pc_distance"]) && empty($row["pc_near"]) && 
					empty($row["prsm_p_r"]) && (empty($row["sel_1_r"]) || trim($row["sel_1_r"])=="20/") && 
					(empty($row["sph_r"]) ) && 
					(empty($row["cyl_r"]) ) && 
					(empty($row["axs_r"]) ) && 
					(empty($row["ad_r"]) || trim($row["ad_r"])=="20/") &&

					empty($row["prsm_p_l"]) && (empty($row["sel_1_l"]) || trim($row["sel_1_l"])=="20/") && 
					(empty($row["sph_l"]) ) && 
					(empty($row["cyl_l"]) ) && 
					(empty($row["axs_l"]) ) && 
					(empty($row["ad_l"]) || trim($row["ad_l"])=="20/") &&
					(empty($row["ex_desc"]) || trim($row["ex_desc"])=="Desc.") 
					){ continue; }
				
				$c = $cexnum;
				
				$inx1="";
				if($c > "1"){$inx1=$c;}
			
				//PC $c ------------------------------------
				$arr_ret["elem_pcDis".$inx1] = (!empty($row["pc_distance"]) && strpos($row["pc_distance"],"1")!==false) ? "Distance" : "";				
				$arr_ret["elem_pcNear".$inx1] = (!empty($row["pc_near"]) && strpos($row["pc_near"],"1")!==false) ? "Near" : ""; "";
				
				$arr_ret["elem_visPcOdP".$inx1] = $row["prsm_p_r"];
				$arr_ret["elem_visPcOdPrism".$inx1] = $row["prism_r"];
				$arr_ret["elem_visPcOdSlash".$inx1] = $row["slash_r"];
				$arr_ret["elem_visPcOdSel2".$inx1] = $row["sel_2_r"];
				$arr_ret["elem_visPcOdNearTxt".$inx1] = $row["txt_1_r"];
				$arr_ret["elem_visPcOdOverrefS".$inx1] = $row["ovr_s_r"];
				$arr_ret["elem_visPcOdOverrefC".$inx1] = $row["ovr_c_r"];
				$arr_ret["elem_visPcOdOverrefA".$inx1] = $row["ovr_a_r"];
				if(!empty($row["ovr_v_r"])){ $arr_ret["elem_visPcOdOverrefV".$inx1] = $row["ovr_v_r"]; }
				$arr_ret["elem_visPcOdSel1".$inx1] = $row["sel_1_r"];
				$arr_ret["elem_visPcOdS".$inx1] = $row["sph_r"];
				$arr_ret["elem_visPcOdC".$inx1] = $row["cyl_r"];
				$arr_ret["elem_visPcOdA".$inx1] = $row["axs_r"];
				if(trim($row["ad_r"])!="+"){$arr_ret["elem_visPcOdAdd".$inx1] = $row["ad_r"];}
				
				$arr_ret["elem_visPcOsP".$inx1] = $row["prsm_p_l"];
				$arr_ret["elem_visPcOsPrism".$inx1] = $row["prism_l"];
				$arr_ret["elem_visPcOsSlash".$inx1] = $row["slash_l"];
				$arr_ret["elem_visPcOsSel2".$inx1] = $row["sel_2_l"];
				$arr_ret["elem_visPcOsNearTxt".$inx1] = $row["txt_1_l"];
				$arr_ret["elem_visPcOsOverrefS".$inx1] = $row["ovr_s_l"];
				$arr_ret["elem_visPcOsOverrefC".$inx1] = $row["ovr_c_l"];
				$arr_ret["elem_visPcOsOverrefA".$inx1] = $row["ovr_a_l"];
				if(!empty($row["ovr_v_l"])){ $arr_ret["elem_visPcOsOverrefV".$inx1] = $row["ovr_v_l"]; }
				$arr_ret["elem_visPcOsSel1".$inx1] = $row["sel_1_l"];
				$arr_ret["elem_visPcOsS".$inx1] = $row["sph_l"];
				$arr_ret["elem_visPcOsC".$inx1] = $row["cyl_l"];
				$arr_ret["elem_visPcOsA".$inx1] = $row["axs_l"];
				if(trim($row["ad_l"])!="+"){ $arr_ret["elem_visPcOsAdd".$inx1] = $row["ad_l"];}
				
				$arr_ret["elem_visPcDesc".$inx1] = stripslashes($row["ex_desc"]);				
				$arr_ret["elem_visPcPrismDesc_".$c] = $row["prism_desc"];
				//PC $c ------------------------------------	
				
			}
		}
		
		//MR
		if($ex_type == "MR"){
			if(!empty($c) && $c%2!=0){	$c++; }
		}else if($ex_type == "PC"){ //PC
			if(!empty($c)){ $x = $c%3; if($x!=0){$c+=3-$x;} }
		}
		
		$resLF = $this->get_mutli_mr_pc_prv($ex_type,$dos_ymd, $visIdLF);
		if($resLF!=false){
			//check Desc
			for($i=1;$i<=$c;$i++){			
				if($ex_type == "MR"){
					$dsc = "elem_visMrDescOther";
					$dscLF = $dsc."LF";	
					$dsc = $dsc."_".$i; $dscLF = $dscLF."_".$i;
				}else if($ex_type == "PC"){					
					$dsc = "elem_visPcDesc".$i; 
					$dscLF = $dsc."LF";	
				}
				
				if(empty($arr_ret[$dsc]) && !empty($resLF[$dscLF])){
					$arr_ret[$dsc] = stripslashes($resLF[$dscLF]);
				}
			}
			
			$arr_ret = array_merge($arr_ret, $resLF);
		}
		
		return array($arr_ret, $c);
	}
	
	/*prev multi MR/PC*/
	function get_mutli_mr_pc_prv($ex_type,$dos_ymd, $visIdLF){
		
		$patient_id = $this->pid;
		$form_id = $this->fid;
		$ar = array();
		
		$sql = "SELECT 
			c1.*,
			c2.sph as sph_r, c2.cyl as cyl_r, c2.axs as axs_r, c2.ad as ad_r, c2.prsm_p as prsm_p_r, c2.prism as prism_r, c2.slash as slash_r, c2.sel_1 as sel_1_r, c2.sel_2 as sel_2_r, 
			c2.txt_1 as txt_1_r, c2.txt_2 as txt_2_r, c2.sel2v as sel2v_r,
			c2.ovr_s as ovr_s_r, c2.ovr_c as ovr_c_r, c2.ovr_v as ovr_v_r, c2.ovr_a as ovr_a_r,			
			c3.sph as sph_l, c3.cyl as cyl_l, c3.axs as axs_l, c3.ad as ad_l, c3.prsm_p as prsm_p_l, c3.prism as prism_l, c3.slash as slash_l, c3.sel_1 as sel_1_l, c3.sel_2 as sel_2_l, 
			c3.txt_1 as txt_1_l, c3.txt_2 as txt_2_l, c3.sel2v as sel2v_l,
			c3.ovr_s as ovr_s_l, c3.ovr_c as ovr_c_l, c3.ovr_v as ovr_v_l, c3.ovr_a as ovr_a_l			
			FROM chart_vis_master c0			
			INNER JOIN chart_pc_mr c1 ON c1.id_chart_vis_master = c0.id
			INNER JOIN chart_pc_mr_values c2 ON c1.id = c2.chart_pc_mr_id AND c2.site='OD'
			INNER JOIN chart_pc_mr_values c3 ON c1.id = c3.chart_pc_mr_id AND c3.site='OS'	
			WHERE c0.id = '".$visIdLF."' 
			AND c1.ex_type='".$ex_type."' 
			AND c1.delete_by='0'  
			Order By c0.id DESC, c1.ex_number
			";
		$form_id_unique=0;	
		$rez = sqlStatement($sql);
		for($indx=0;$rowLF=sqlFetchArray($rez);$indx++){
			$c = $rowLF["ex_number"];
			
			//for previous formId only--
			if(empty($form_id_unique)){$form_id_unique=$rowLF["id_chart_vis_master"];}
			if($form_id_unique!=$rowLF["id_chart_vis_master"]){break;}
			//--
			
			if($ex_type == "MR"){	
				
				$inx1=""; $inx2="";
				if($c > "1"){
					$inx1="Other";
					
					if($c > "2"){
						$inx2="_".$c;
					}
				}

				$ar["elem_visMr".$inx1."OdS".$inx2."LF"] = $rowLF["sph_r"];
				$ar["elem_visMr".$inx1."OdC".$inx2."LF"] = $rowLF["cyl_r"];
				$ar["elem_visMr".$inx1."OdA".$inx2."LF"] = $rowLF["axs_r"];
				if(trim($rowLF["ad_r"])!="+"){$ar["elem_visMr".$inx1."OdAdd".$inx2."LF"] = $rowLF["ad_r"];}

				$ar["elem_visMr".$inx1."OsS".$inx2."LF"] = $rowLF["sph_l"];
				$ar["elem_visMr".$inx1."OsC".$inx2."LF"] = $rowLF["cyl_l"];
				$ar["elem_visMr".$inx1."OsA".$inx2."LF"] = $rowLF["axs_l"];
				if(trim($rowLF["ad_l"])!="+"){$ar["elem_visMr".$inx1."OsAdd".$inx2."LF"] = $rowLF["ad_l"];}
				$elem_examDateMRLF = $rowLF["exam_date"];
				//--
				
				$zDsc = "elem_visMrDesc".$inx1.$inx2;
				$zDscLf = "elem_visMrDesc".$inx1."LF".$inx2;
				
				/*$j2="";	
				if($c >= 2){ $j2 = "Other"; }
				$zDsc = "elem_visMrDesc".$j2;
				$zDscLf = $zDsc."LF";
				if($c>=3){$zDscLf = $zDscLf."_".$c ;$zDsc=$zDsc."_".$c ;}*/
				
				$zDsc = "";
				$tmpzDt="";
				
				if(!empty($rowLF["sph_r"])){
					$zDsc .= wv_formatDate($elem_examDateMRLF)." ";
					$zDsc .= "OD: ".$rowLF["sph_r"].", ";					
					$zDsc .= (!empty($rowLF["cyl_r"])) ? $rowLF["cyl_r"].", " : "";					
					$zDsc .= (!empty($rowLF["axs_r"])) ? $rowLF["axs_r"].", " : "";
					$zDsc .= (!empty($rowLF["ad_r"])) ? $rowLF["ad_r"].", " : "";
					$zDsc = substr($zDsc, 0, -2);
				}
				
				if(!empty($rowLF["sph_l"])){
					if(empty($zDsc)){ $zDsc .= wv_formatDate($elem_examDateMRLF)." ";  }
					$zDsc .= " OS: ".$rowLF["sph_l"].", ";					
					$zDsc .= (!empty($rowLF["cyl_l"])) ? $rowLF["cyl_l"].", " : "";					
					$zDsc .= (!empty($rowLF["axs_l"])) ? $rowLF["axs_l"].", " : "";					
					$zDsc .= (!empty($rowLF["ad_l"])) ? $rowLF["ad_l"].", " : "";
					$zDsc = substr($zDsc, 0, -2);
				}
				
				$ar[$zDscLf] = $zDsc;
				//--
				
			}else{
			
				$inx1=""; 
				if($c > "1"){
					$inx1="".$c;				
				}
			
				$ar["elem_visPcOdSel1".$inx1."LF"] = $rowLF["sel_1_r"];
				$ar["elem_visPcOdS".$inx1."LF"] = $rowLF["sph_r"];
				$ar["elem_visPcOdC".$inx1."LF"] = $rowLF["cyl_r"];
				$ar["elem_visPcOdA".$inx1."LF"] = $rowLF["axs_r"];

				$ar["elem_visPcOsSel1".$inx1."LF"] = $rowLF["sel_1_l"];
				$ar["elem_visPcOsS".$inx1."LF"] = $rowLF["sph_l"];
				$ar["elem_visPcOsC".$inx1."LF"] = $rowLF["cyl_l"];
				$ar["elem_visPcOsA".$inx1."LF"] = $rowLF["axs_l"];
				
						
				$zDscLf = "elem_visPcDesc".$inx1."LF";
				
				$zDsc = "";				
				if(!empty($rowLF["sph_r"])){
					$zDsc .= "OD: ".$rowLF["sph_r"].", ";
					$zDsc .= (!empty($rowLF["cyl_r"])) ? $rowLF["cyl_r"].", " : "";					
					$zDsc .= (!empty($rowLF["axs_r"])) ? $rowLF["axs_r"].", " : "";
					//$zDsc .= (!empty($rowLF["sel_1_r"])) ? $rowLF["sel_1_r"].", " : "";	
					$zDsc = substr($zDsc, 0, -2);
				}
				
				if(!empty($rowLF["sph_l"])){
					$zDsc .= " OS: ".$rowLF["sph_l"].", ";
					$zDsc .= (!empty($rowLF["cyl_l"])) ? $rowLF["cyl_l"].", " : "";
					$zDsc .= (!empty($rowLF["axs_l"])) ? $rowLF["axs_l"].", " : "";
					//$zDsc .= (!empty($rowLF["sel_1_l"])) ? $rowLF["sel_1_l"].", " : "";
					$zDsc = substr($zDsc, 0, -2);
				}
				
				$ar[$zDscLf] = $zDsc;
			}
		}
		return $ar;
	}
	
	//--
	function vision_pop_up(){
	
		$popName = $_GET["popName"];	
		
		//if($popName=="popDistance" || $popName=="popPC" || $popName=="popMR"){
			$arrAcuitiesMrDisOptions='';
			$arrAcuitiesNearOptions='';
			$arrSnellanOptions='';
			$arr_snellan=array();
			$arr_vis = Vision::getArrDropDown();
			
			$arrAcuitiesMrDis = $arr_vis["AcuitiesMrDis"];
			$arrAcuitiesNear = $arr_vis["AcuitiesNear"];
			$arrSnellan = $arr_vis["Snellan"];
			
			$menu_visSnellan = wv_get_simple_menu($arrSnellan,"menu_snellan","elem_visSnellan_input");
			$menu_visSnellan_near = wv_get_simple_menu($arrSnellan,"menu_snellan","elem_visSnellan_near_input");	
			$menu_visSnellan_pam = wv_get_simple_menu($arrSnellan,"menu_snellan","elem_visPam_input");	
			
			foreach($arrAcuitiesMrDis as $key => $subArray){
				$arrAcuitiesMrDisOptions.='<option value="'.$key.'" '.$sel.'>'.$key.'</option>';
			}

			foreach($arrAcuitiesNear as $key => $subArray){
				$arrAcuitiesNearOptions.='<option value="'.$key.'">'.$key.'</option>';
			}
			/*
			foreach($arrSnellan as $key => $subArray){
				$val=$key;
				if(strtolower($key)=='other'){ $key='';}
				$arrSnellanOptions.='<option value="'.$key.'">'.$val.'</option>';
				if(strtolower($val)!='other'){
					$arr_snellan[$key]=$key;
				}
			}
			*/
			
		//}
		
		//popExtMR CASE ADDED FOR PRS EXTERNAL VA WORK -WORKING FROM PRS ICON IN ICONBAR 
		if($popName=="popAR" || $popName=="popCycAR" || strpos($popName,"popPC")!==false || strpos($popName,"popMR")!==false || strpos($popName,"popExtMR")!==false){
			$sphereOptions=$cylinderOptions=$axisOptions='';
			$defCylSign=$GLOBALS["def_cylinder_sign"];
			if(empty($defCylSign)==true){ $defCylSign='+';}
			for($i=-10; $i<=10; $i=$i+0.25){
				$sign= $val='';
				if($i>0){ $sign='+';}
				if($i==0){ $val='PLANO';
				}else{ $val= $sign.number_format($i,2); }
				$sphereOptions.='<option value="'.$val.'">'.$val.'</option>';
			}
			//CYINDER
			for($i=-16; $i<=16; $i=$i+0.25){
				$sign= $val='';
				//if($i>0){ $sign=$defCylSign;}
				if($i>0){ $sign='+';}
				$val= $sign.number_format($i,2);
				$cylinderOptions.='<option value="'.$val.'">'.$val.'</option>';
			}
			//AXIS
			for($i=000; $i<=180; $i=$i+5){
				$len=strlen($i);
				if($len==1)$i='00'.$i;
				if($len==2)$i='0'.$i;
				$axisOptions.='<option value="'.$i.'">'.$i.'</option>';
			}			
		}
		//popExtMR CASE ADDED FOR PRS EXTERNAL VA WORK -WORKING FROM PRS ICON IN ICONBAR 
		if(strpos($popName,"popPC")!==false || strpos($popName,"popMR")!==false || strpos($popName,"popExtMR")!==false){
			//ADD
			$addOptions='<option value="0">0</option>';
			for($i=0.50; $i<=5; $i=$i+0.25){
				$sign=$val='';
				if($i>0){ $sign='+';}
				$val= $sign.number_format($i,2);
				$addOptions.='<option value="'.$val.'">'.$val.'</option>';
			}

			//PRISM
			$prismOptions='<option value="0">0</option>';
			for($i=0.50; $i<=15;){
				$prismOptions.='<option value="'.$i.'">'.$i.'</option>';
				if($i>=8){
					$i=$i+0.25;
				}else{
					$i=$i+0.50;
				}
			}
			
			//Copy from
			$ar_copy_frm=array("pc1","pc2","pc3","mr1","mr2","mr3");
		}		
		
		//
		if(strpos($popName,"popMR")!==false){
			$i = str_replace("popMR","",$popName);
			$i = trim($i);
			$mr_pop_lbl = !empty($_GET["mr_lbl"]) ? $_GET["mr_lbl"] : "MR ".$i;
		}
		
		//popExtMR CASE ADDED FOR PRS EXTERNAL VA WORK -WORKING FROM PRS ICON IN ICONBAR 
		if(strpos($popName,"popExtMR")!==false){
			$i = str_replace("popExtMR","",$popName);
			$i = trim($i);
			$ext_mr_pop_lbl = !empty($_GET["ext_mr_lbl"]) ? $_GET["ext_mr_lbl"] : "Ext_MR ".$i;
		}
		
		$tmp = str_replace("\x", "\\x", $GLOBALS['incdir']);
		include($tmp."/chart_notes/view/vision_pop_up.php");
	}
	
	function add_vision(){
		$indx_org = $_GET["indx"];
		$w = $_GET["w"];
		$len_pc = ($w=="pc") ? $indx_org+2 : $_GET["pcln"];
		$len_mr= ($w=="mr") ? $indx_org+1 : $_GET["mrln"];
			
		
		$arr_dd_menu = Vision::getArrDropDown();
		$arrSnellan = $arr_dd_menu["Snellan"];		
		$arrAcuitiesMrDis = $arr_dd_menu["AcuitiesMrDis"];
		$arrAcuitiesNear = $arr_dd_menu["AcuitiesNear"];
		$arr_w4dotOptions = $arr_dd_menu["w4dotOptions"];	
		
		//Copy Drop Down Options--
		$str_opts_copy=$this->get_copy_drop_down($len_pc, $len_mr);	
		//Copy Drop Down Options--
		
		if($w=="pc"){	
			for($i=0;$i<3;$i++){
				
				$indx = $indx_org + $i;
				
				$tmp_menu_visPcOdOverrefV="menu_visPcOdOverrefV".$indx;
				$tmp_menu_visPcOsOverrefV="menu_visPcOsOverrefV".$indx;
				$$tmp_menu_visPcOdOverrefV = wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis2","elem_visPcOdOverrefV".$indx);
				$$tmp_menu_visPcOsOverrefV = wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis2","elem_visPcOsOverrefV".$indx);		
				
				include($GLOBALS['incdir']."/chart_notes/view/vis_pc_inc.php");
			}	
		}else if($w=="mr"){
			$ctmpMr3 = $_GET["ctmpMr3"];
			for($i=0;$i<2;$i++){
				$indx = $indx_org + $i;
				
				$indx2 = ($indx>2) ? "_".$indx : "" ;
				$sfx = ($indx>=2) ? "Other" : "" ;
				
				$tmp_menu_visMrOdTxt1="menu_visMr".$sfx."OdTxt1".$indx2;
				$tmp_menu_visMrOdTxt2="menu_visMr".$sfx."OdTxt2".$indx2;
				$tmp_menu_visMrOsTxt1="menu_visMr".$sfx."OsTxt1".$indx2;
				$tmp_menu_visMrOuTxt1="menu_visMr".$sfx."OuTxt1".$indx2;
				$tmp_menu_visMrOsTxt2="menu_visMr".$sfx."OsTxt2".$indx2;
				$tmp_menu_visMrOdSel2Vision="menu_visMr".$sfx."OdSel2Vision".$indx2;
				$tmp_menu_visMrOsSel2Vision="menu_visMr".$sfx."OsSel2Vision".$indx2;
				
				$$tmp_menu_visMrOdTxt1 = wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis7","elem_visMr".$sfx."OdTxt1".$indx2);
				$$tmp_menu_visMrOdTxt2 = wv_get_simple_menu($arrAcuitiesNear,"menu_acuitiesMrNear7","elem_visMr".$sfx."OdTxt2".$indx2);
				$$tmp_menu_visMrOsTxt1 = wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis8","elem_visMr".$sfx."OsTxt1".$indx2);
				$$tmp_menu_visMrOuTxt1 = wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis8","elem_visMr".$sfx."OuTxt1".$indx2);
				$$tmp_menu_visMrOsTxt2 = wv_get_simple_menu($arrAcuitiesNear,"menu_acuitiesMrNear8","elem_visMr".$sfx."OsTxt2".$indx2);
				$$tmp_menu_visMrOdSel2Vision = wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis9","elem_visMr".$sfx."OdSel2Vision".$indx2);
				$$tmp_menu_visMrOsSel2Vision = wv_get_simple_menu($arrAcuitiesMrDis,"menu_acuitiesMrDis9","elem_visMr".$sfx."OsSel2Vision".$indx2);
				
				include($GLOBALS['incdir']."/chart_notes/view/vis_mr_inc.php");
			}	
		}
	}

	static function get_menu_html(){
		
		$ar = array();
		$wh = $_GET["wh"];
		$mid = $_GET["mid"];
		$eid = $_GET["eid"];
		$arr_dd_menu = Vision::getArrDropDown();
		if($wh == "menu_snellan"){
			$ar = $arr_dd_menu["Snellan"];	
		}else if($wh == "menu_acuitiesMrDis"){
			$ar = $arr_dd_menu["AcuitiesMrDis"];
		}else if($wh == "menu_acuitiesNear"){	
			$ar = $arr_dd_menu["AcuitiesNear"];
		}
		
		$s = wv_get_simple_menu($ar,$mid,$eid,2);
		echo $s;
	}
	
	function get_last_given_mr(){
		
		$id_vis_master = 0 ;
		
		$sql = "
			SELECT			
			chart_master_table.id AS form_id, chart_master_table.finalize, 
			chart_master_table.releaseNumber, chart_master_table.date_of_service,
			chart_vis_master.status_elements, chart_vis_master.id
			FROM chart_master_table
			INNER JOIN chart_vis_master ON chart_vis_master.form_id = chart_master_table.id			
			WHERE chart_master_table.patient_id='".$this->pid."' AND chart_master_table.delete_status='0' 
			AND chart_master_table.purge_status='0' 
			ORDER BY chart_master_table.date_of_service DESC, chart_master_table.id DESC
			LIMIT 0, 1
		";
		$row = sqlQuery($sql);
		if($row!=false){
			$se = $row["status_elements"];
			$date_of_service = $row["date_of_service"];
			$form_id = $row["form_id"];
			$finalize = $row["finalize"];
			$id_vis_master = $row["id"];
		}
		
		if(!empty($id_vis_master)){
		
			$sql = "
				SELECT
				c1.exam_date, c1.mr_none_given,  c1.provider_id, c1.ex_number,
				c2.sph as sph_r, c2.cyl as cyl_r, c2.axs as axs_r, c2.ad as ad_r, 
				c3.sph as sph_l, c3.cyl as cyl_l, c3.axs as axs_l, c3.ad as ad_l
				FROM chart_pc_mr c1
				LEFT JOIN chart_pc_mr_values c2 ON c1.id = c2.chart_pc_mr_id AND c2.site='OD'
				LEFT JOIN chart_pc_mr_values c3 ON c1.id = c3.chart_pc_mr_id AND c3.site='OS'
				WHERE c1.ex_type='MR' AND c1.id_chart_vis_master='".$id_vis_master."'
				ORDER BY c1.ex_number
			";
			$rez = sqlStatement($sql);
			for($i=1; $row=sqlFetchArray($rez);$i++){
				$mr_none_given = $row["mr_none_given"];
				$ex_num = $row["ex_number"];
				$ret = ""; $flg_done=0;
				
				if(
				(!empty($row["sph_r"]) && (strpos($se, "elem_visMrOdS=1") !== false)) || 
				(!empty($row["cyl_r"]) && (strpos($se, "elem_visMrOdC=1") !== false)) || 
				(!empty($row["axs_r"]) && (strpos($se, "elem_visMrOdA=1") !== false)) || 
				(!empty($row["ad_r"]) && (strpos($se, "elem_visMrOdAdd=1") !== false)) ||
				(!empty($row["sph_l"]) && (strpos($se, "elem_visMrOsS=1") !== false)) || 
				(!empty($row["cyl_l"]) && (strpos($se, "elem_visMrOsC=1") !== false)) || 
				(!empty($row["axs_l"]) && (strpos($se, "elem_visMrOsA=1") !== false)) || 
				(!empty($row["ad_l"]) && (strpos($se, "elem_visMrOsAdd=1") !== false))
				){
					$ret_od = ""; $ret_os = "";
					if(!empty($row["sph_r"])){ $ret_od .= "".$row["sph_r"].", "; }
					if(!empty($row["cyl_r"])){ $ret_od .= "".$row["cyl_r"].", "; }
					if(!empty($row["axs_r"])){ $ret_od .= "".$row["axs_r"].", "; }
					if(!empty($row["ad_r"])){ $ret_od .= "".$row["ad_r"].", "; }
					
					if(!empty($row["sph_l"])){ $ret_os .= "".$row["sph_l"].", "; }
					if(!empty($row["cyl_l"])){ $ret_os .= "".$row["cyl_l"].", "; }
					if(!empty($row["axs_l"])){ $ret_os .= "".$row["axs_l"].", "; }
					if(!empty($row["ad_l"])){ $ret_os .= "".$row["ad_l"].", "; }
					
					if(strpos($mr_none_given, "MR ".$ex_num)!==false){ $flg_done=1; break; }
						
				}
			}
		}
		
		return array($ret_od, $ret_os);		
	}
	
	function mk_numeric($str){
		$str = trim($str);	
		$ret = "";	
		if(!empty($str) && strpos($str,"20/")!==false && strpos($str,"APC 20/")===false && $str!="20/" ){
			$str = preg_replace("/20\/|\(.*\)/","",$str);
			if(!empty($str)){
				$arstr = explode("-",$str);
				$str = trim($arstr[0]); $str = floatval($str);
				if(!empty($str)){$str = preg_replace("/[^0-9]/", "", $str);}
			}
			$ret = $str;
		}
		return $ret;
	}
	
	function getVisionGraphAm(){
		$patient_id = $this->pid;
		if(empty($patient_id)){ exit("Please provide patient id."); }
		
		$mode=$_GET["mode"];
		
		$series = array();
		$seriesName = array();
		//$axisName = array("Date", "Vision");
		//$graphTitle = " Vision ";
		
		$seriesColor = array();		
		
		$arr_dis_od=$arr_dis_os=$arr_dis_ou=$arr_nr_od=$arr_nr_os=$arr_nr_ou=$arr_dates=array();
		
		$arr_uni=array();
		$sql = "
			SELECT
			c2.status_elements,
			c3.sel_od, c3.txt_od, c3.sel_os, c3.txt_os, c3.sel_ou, c3.txt_ou, c3.sec_indx, c3.sec_name,				
			c1.date_of_service, c1.id as form_id	
			FROM chart_master_table c1
			LEFT JOIN chart_vis_master c2 ON c1.id = c2.form_id
			LEFT JOIN chart_acuity c3 ON c3.id_chart_vis_master = c2.id 
			WHERE c1.patient_id = '".$patient_id."' AND c1.purge_status='0' AND c1.delete_status='0'
			AND (c3.sec_name = 'Distance' OR c3.sec_name = 'Near') AND sec_indx IN (1,2)
			ORDER BY c1.date_of_service, c1.id
		";			
		$rez = sqlStatement($sql);
		for($i=1;$row=sqlFetchArray($rez);$i++){
			
			$flg_date=0;
			$se = $row["status_elements"];
			$fid = $row["form_id"];
			
			if($row["sec_name"] == "Distance"){
				$tmp_dis_od="";				
				if($row["sec_indx"]=="1" && $row["sel_od"]=="SC" && (strpos($se, "elem_visDisOdSel1=1") !== false || strpos($se, "elem_visDisOdTxt1=1") !== false)){				
					$tval = trim($row["txt_od"]);	
					$tmp_dis_od= $this->mk_numeric($tval);
				}else	if($row["sec_indx"]=="2" && $row["sel_od"]=="SC" && (strpos($se, "elem_visDisOdSel2=1") !== false || strpos($se, "elem_visDisOdTxt2=1") !== false)){
					$tval = trim($row["txt_od"]);
					$tmp_dis_od= $this->mk_numeric($tval);
				}

				$tmp_dis_os="";
				if($row["sec_indx"]=="1" && $row["sel_os"]=="SC" && (strpos($se, "elem_visDisOsSel1=1") !== false || strpos($se, "elem_visDisOsTxt1=1") !== false)){
					$tval = trim($row["txt_os"]);
					$tmp_dis_os= $this->mk_numeric($tval);
				}
				if(empty($tmp_dis_os)){
					if($row["sec_indx"]=="2" && $row["sel_os"]=="SC" && (strpos($se, "elem_visDisOsSel2=1") !== false || strpos($se, "elem_visDisOsTxt2=1") !== false)){
						$tval = trim($row["txt_os"]);
						$tmp_dis_os= $this->mk_numeric($tval);
					}
				}
			}else if($row["sec_name"] == "Near"){			
				//Near
				$tmp_nr_od="";
				if($row["sec_indx"]=="1" && $row["sel_od"]=="SC" && (strpos($se, "elem_visNearOdSel1=1") !== false || strpos($se, "elem_visNearOdTxt1=1") !== false)){
					$tval = trim($row["txt_od"]);
					$tmp_nr_od= $this->mk_numeric($tval);
				}else	if($row["sec_indx"]=="2" &&  $row["sel_od"]=="SC" && (strpos($se, "elem_visNearOdSel2=1") !== false || strpos($se, "elem_visNearOdTxt2=1") !== false)){
					$tval = trim($row["txt_od"]);
					$tmp_nr_od= $this->mk_numeric($tval);
				}
				
				$tmp_nr_os="";
				if($row["sec_indx"]=="1" && $row["sel_os"]=="SC" && (strpos($se, "elem_visNearOsSel1=1") !== false || strpos($se, "elem_visNearOsTxt1=1") !== false)){
					$tval = trim($row["txt_os"]);
					$tmp_nr_os= $this->mk_numeric($tval);
				}else	if($row["sec_indx"]=="2" && $row["sel_os"]=="SC" && (strpos($se, "elem_visNearOsSel2=1") !== false || strpos($se, "elem_visNearOsTxt2=1") !== false)){
					$tval = trim($row["txt_os"]);
					$tmp_nr_os= $this->mk_numeric($tval);
				}
			}			
			
			if(!in_array($fid, $arr_uni["dis"]) && (!empty($tmp_dis_od) || !empty($tmp_dis_os)) ){ //|| !empty($tmp_dis_ou)
				$arr_uni["dis"][] = $fid;
				$arr_dates["dis"][]=wv_formatDate($row["date_of_service"]);					
				if(!empty($tmp_dis_od)){ $arr_dis_od[]=$tmp_dis_od; }else{$arr_dis_od[]="0";}
				if(!empty($tmp_dis_os)){ $arr_dis_os[]=$tmp_dis_os; }else{$arr_dis_os[]="0";}
				//if(!empty($tmp_dis_ou)){ $arr_dis_ou[]=$tmp_dis_ou; } //no ou				
			}
			
			if(!in_array($fid, $arr_uni["nr"]) && (!empty($tmp_nr_od) || !empty($tmp_nr_os)) ){ //|| !empty($tmp_nr_ou)
				$arr_uni["nr"][] = $fid;
				$arr_dates["nr"][]=wv_formatDate($row["date_of_service"]);				
				if(!empty($tmp_nr_od)){ $arr_nr_od[]=$tmp_nr_od; }else{$arr_nr_od[]="0";}
				if(!empty($tmp_nr_os)){ $arr_nr_os[]=$tmp_nr_os; }else{$arr_nr_os[]="0";}
				//if(!empty($tmp_nr_ou)){ $arr_nr_ou[]=$tmp_nr_ou; } //no ou
			}
		}
		
		
		//----------
		if(count($arr_dis_od)>0){
			$series["dis"][] = $arr_dis_od;
			$seriesName["dis"][] = "Distance OD";
			$seriesColor["dis"][] = "blue";
			//$ckd_disod="checked=\"checked\"";
		}
		
		if(count($arr_dis_os)>0){
			$series["dis"][] = $arr_dis_os;
			$seriesName["dis"][] = "Distance OS";
			$seriesColor["dis"][] = "green";
			//$ckd_disos="checked=\"checked\"";
		}
		
					
		
		if(count($arr_nr_od)>0){
			$series["nr"][] = $arr_nr_od;
			$seriesName["nr"][] = "Near OD";
			$seriesColor["nr"][] = "blue";
			//$ckd_nrod="checked=\"checked\"";
		}
		
		if(count($arr_nr_os)>0){
			$series["nr"][] = $arr_nr_os;
			$seriesName["nr"][] = "Near OS";
			$seriesColor["nr"][] = "green";
			//$ckd_nros="checked=\"checked\"";
		}
		
		
		
		if(count($series)>0){
			$owv = new WorkView();
			$len = count($series["dis"]);
			
			if($len>0){
				$series["dis"][] = $arr_dates["dis"];	//Dates			
				//$len = count($series);
				//$absLabel = "Serie".$len;
				$line_pay_graph_var_arr_js["dis"] = $line_payment_tot_arr_js["dis"] = array();
				if( $len > 0 ){					
					$line_chart_data=$owv->line_chart($seriesName["dis"],$series["dis"],$seriesColor["dis"]);
					$line_pay_graph_var_arr_js["dis"]=json_encode($line_chart_data['line_pay_graph_var_detail']);
					$line_payment_tot_arr_js["dis"]=json_encode($line_chart_data['line_payment_tot_detail']);
				}
			}
			
			$len = count($series["nr"]);
			if($len>0){
				$series["nr"][] = $arr_dates["nr"];	//Dates
				if( $len > 0 ){					
					$line_chart_data=$owv->line_chart($seriesName["nr"],$series["nr"],$seriesColor["nr"]);
					$line_pay_graph_var_arr_js["nr"]=json_encode($line_chart_data['line_pay_graph_var_detail']);
					$line_payment_tot_arr_js["nr"]=json_encode($line_chart_data['line_payment_tot_detail']);
				}
			}
			
		}else{
			$msg='Graph can not created becuase of insufficient data.';
		}
		
		$ajax_arr['line_pay_graph_var_detail']=$line_pay_graph_var_arr_js;
		$ajax_arr['line_payment_tot_detail']=$line_payment_tot_arr_js;
		echo json_encode($ajax_arr);
		
	}
}
?>