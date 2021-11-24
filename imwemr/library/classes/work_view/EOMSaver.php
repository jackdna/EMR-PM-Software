<?php

class EOMSaver extends ChartNoteSaver{
	private $arG; 

	public function __construct($pid, $fid){
		parent::__construct($pid,$fid);
		$this->arG = array();
	}
	
	public function save_form(){
		$patientid = $this->pid;
		$elem_formId = $this->fid;
		$arrRet=array();
		
		//check
		if(empty($patientid) || empty($elem_formId)){ echo json_encode($arrRet); exit();  }
		
		//Check if Chart is not Finalized or User is Finalizer	
		
		if(!$this->checkFinalizer()){			
			/*echo "<script>window.close();</script>";*/
			//exit();
		}else{
			//Check if Chart is not Finalized or User is Finalizer		
			$OBJDrawingData = new CLSDrawingData();
			$objImageManipulation = new CLSImageManipulation();
			$oSaveFile = new SaveFile($patientid);
			
			$elem_eomFull = $_POST["elem_eomFull"];
			$elem_eomOrtho = $_POST["elem_eomOrtho"];
			
			//update_custom_field('custom_field_xml','chart_eom',$patientid,$elem_formId,'eom');
			if(!empty($_POST["elem_eomAbnRightLeftAlter_check"])){
				$eom_abn_right_left_alter=$_POST["elem_eomAbnRightLeftAlter_check"];
			}else if(!empty($_POST["elem_eomAbnRightLeftAlter_check2"])){
				$eom_abn_right_left_alter=$_POST["elem_eomAbnRightLeftAlter_check2"];
			}else if(!empty($_POST["elem_eomAbnRightLeftAlter_check3"])){
				$eom_abn_right_left_alter=$_POST["elem_eomAbnRightLeftAlter_check3"];
			}
			if(!empty($_POST["elem_eomAbnNearFarBoth_check"])){
				$eom_abn_near_far_both=$_POST["elem_eomAbnNearFarBoth_check"];
			}else if(!empty($_POST["elem_eomAbnNearFarBoth_check2"])){
				$eom_abn_near_far_both=$_POST["elem_eomAbnNearFarBoth_check2"];
			}else if(!empty($_POST["elem_eomAbnNearFarBoth_check3"])){
				$eom_abn_near_far_both=$_POST["elem_eomAbnNearFarBoth_check3"];
			}

			if(!empty($_POST["elem_eomHoriEsoExo_check2"])){
				$eom_hori_eso_exo=$_POST["elem_eomHoriEsoExo_check2"];
			}else if(!empty($_POST["elem_eomHoriEsoExo_check"])){
				$eom_hori_eso_exo=$_POST["elem_eomHoriEsoExo_check"];
			}
			if(!empty($_POST["elem_eomHoriTrophiaPhoria_check2"])){
				$eom_hori_trophia_phoria=$_POST["elem_eomHoriTrophiaPhoria_check2"];
			}else if(!empty($_POST["elem_eomHoriTrophiaPhoria_check"])){
				$eom_hori_trophia_phoria=$_POST["elem_eomHoriTrophiaPhoria_check"];
			}

			if(!empty($_POST["elem_eomHoriNearFarBoth_check3"])){
				$eom_hori_near_far_both=$_POST["elem_eomHoriNearFarBoth_check3"];
			}else if(!empty($_POST["elem_eomHoriNearFarBoth_check2"])){
				$eom_hori_near_far_both=$_POST["elem_eomHoriNearFarBoth_check2"];
			}else if(!empty($_POST["elem_eomHoriNearFarBoth_check"])){
				$eom_hori_near_far_both=$_POST["elem_eomHoriNearFarBoth_check"];
			}

			if(!empty($_POST["elem_eomVertiHyperHypo_check"])){
				$eom_verti_hyper_hypo=$_POST["elem_eomVertiHyperHypo_check"];
			}else if(!empty($_POST["elem_eomVertiHyperHypo_check2"])){
				$eom_verti_hyper_hypo=$_POST["elem_eomVertiHyperHypo_check2"];
			}

			if(!empty($_POST["elem_eomVertiTrophiaPhoria_check"])){
				$eom_verti_trophia_phoria=$_POST["elem_eomVertiTrophiaPhoria_check"];
			}else if(!empty($_POST["elem_eomVertiTrophiaPhoria_check2"])){
				$eom_verti_trophia_phoria=$_POST["elem_eomVertiTrophiaPhoria_check2"];
			}
			if(!empty($_POST["elem_eomVertiNearFarBoth_check"])){
				$eom_verti_near_far_both=$_POST["elem_eomVertiNearFarBoth_check"];
			}else if(!empty($_POST["elem_eomVertiNearFarBoth_check2"])){
				$eom_verti_near_far_both=$_POST["elem_eomVertiNearFarBoth_check2"];
			}else if(!empty($_POST["elem_eomVertiNearFarBoth_check3"])){
				$eom_verti_near_far_both=$_POST["elem_eomVertiNearFarBoth_check3"];
			}
			
			$elem_eomAbnDesc = $_POST["elem_eomAbnDesc"];
			$elem_eomHoriDesc = $_POST["elem_eomHoriDesc"];
			$elem_eomVertiDesc = $_POST["elem_eomVertiDesc"];
			$elem_npcCm = $_POST["elem_npcCm"];
			
			$elem_examDate = $_POST["elem_examDate"];
			$elem_eomDrawing = $_POST["elem_eomDrawing"];

			$full_desc = $_POST['full_desc'];
			$ortho_desc = $_POST['ortho_desc'];
			$elem_descEom = sqlEscStr($_POST["elem_descEom"]);

			$rightHdTiltDis = sqlEscStr($_POST["elem_rightHeadTilt_Dis"]);
			$leftHdTiltDis = sqlEscStr($_POST["elem_leftHeadTilt_Dis"]);
			$commDis = ($_POST["elem_commentsDis"] != "Comments") ? sqlEscStr($_POST["elem_commentsDis"]) : "" ;
			$eomDrwNear = sqlEscStr($_POST["elem_eomDrawingNear"]);
			$rightHdTiltNear = sqlEscStr($_POST["elem_rightHeadTilt_Near"]);
			$leftHdTiltNear = sqlEscStr($_POST["elem_leftHeadTilt_Near"]);
			$commNear = ($_POST["elem_commentsNear"] != "Comments") ? sqlEscStr($_POST["elem_commentsNear"]) : "" ;
			
			$elem_noChange = $_POST["elem_examined_no_change"];
			
			$elem_isPositive = $_POST["elem_isPositive"];
			$elem_wnl = $_POST["elem_wnl"];

			$wnl_2 =$_POST["elem_wnl2"];
			$isPositive_2=$_POST["elem_isPositive2"];
			$examined_no_change2=$_POST["elem_noChange2"];
				
			$wnl_3	=$_POST["elem_wnl3"];
			$isPositive_3=$_POST["elem_isPositive3"];
			$examined_no_change3=$_POST["elem_noChange3"];

			$eomDrawing_2=$_POST["elem_eomDrawing_2"];
			$desc_draw_od=sqlEscStr($_POST["el_eom_od"]);
			$desc_draw_os=sqlEscStr($_POST["el_eom_os"]);
			
			$elem_stereo_SecondsArc=$_POST["elem_stereo_SecondsArc"];

			$statusElem = "";
			$statusElem .= "elem_chng_divEom=".$_POST["elem_chng_divEom"].",";
			$statusElem .= "elem_chng_divEom2=".$_POST["elem_chng_divEom2"].",";
			$statusElem .= "elem_chng_divEom3=".$_POST["elem_chng_divEom3"]." ";
			
			$elem_npcWnlAbn = sqlEscStr($_POST["elem_npcWnlAbn"]);
			

			$eomControl="";
			if(isset($_POST["elem_eomControl"]))$eomControl=implode(",",$_POST["elem_eomControl"]);

			$eomDrawing_Dis2 = sqlEscStr($_POST["elem_eomDrawing_Dis2"]);
			$eomDrwNear_2 = sqlEscStr($_POST["elem_eomDrawingNear_2"]);

			$commDis_1 = sqlEscStr($_POST["elem_commentsDis_1"]);
			if($commDis_1=="Comments")$commDis_1="";
			$commNear_1 = sqlEscStr($_POST["elem_commentsNear_1"]);
			if($commNear_1=="Comments")$commNear_1="";
			
			//
			$npa = sqlEscStr($_POST["elem_npaWnlAbn"]);
			$npa_cm = sqlEscStr($_POST["elem_npaCm"]);
			$npa_desc = $_POST["npa_desc"];
			
			//Av Patters
			if(!empty($_POST["elem_eomAvpAesoAexo_check"])){ 
				$avpat_eso = $_POST["elem_eomAvpAesoAexo_check"]; 
			}else if(!empty($_POST["elem_eomAvpAesoAexo_check2"])){ 
				$avpat_eso = $_POST["elem_eomAvpAesoAexo_check2"]; 
			}else if(!empty($_POST["elem_eomAvpAesoAexo_check3"])){ 
				$avpat_eso = $_POST["elem_eomAvpAesoAexo_check3"]; 
			}else if(!empty($_POST["elem_eomAvpAesoAexo_check4"])){ 
				$avpat_eso = $_POST["elem_eomAvpAesoAexo_check4"]; 
			}		
			
			/*
			if(!empty($_POST["elem_eomAvpVesoVexo_check"])){ 
				$avpat_exo = $_POST["elem_eomAvpVesoVexo_check"]; 
			}else if(!empty($_POST["elem_eomAvpVesoVexo_check2"])){ 
				$avpat_exo = $_POST["elem_eomAvpVesoVexo_check2"]; 
			}*/		
			
			//Randot
			/*
			Randot Test results - the ONLY part of the Randot test that should come forward is the seconds
			of arc - and that will go to the blue bar ONLY. Do not bring forward Fly, Butterfly, A, B, C etc.
			*/
			
			//$sumRandot="";		
			if(isset($_POST["elem_ranSt"])){
				$elem_ranSt = implode(", ",$_POST["elem_ranSt"]);
				//$sumRandot .= $elem_ranSt.", ";
			}
			
			$ranSt_Dots9 = $_POST["elem_ranSt_Dots9"];
			//if(!empty($ranSt_Dots9)){$sumRandot .= $ranSt_Dots9.", ";}
			//$ranSt_SecondsArc = $_POST["elem_ranSt_SecondsArc"];
			//if(!empty($elem_stereo_SecondsArc)){$sumRandot .= $elem_stereo_SecondsArc.", ";}
			//$sumRandot = trim($sumRandot);
			//$sumRandot = substr($sumRandot,0,-1); //remove last coma

			//
			
			//Grid values--
			$counterGrid = $_POST["elem_counterGrid"];		
			$arGridVal=array();
			$sumMotility="";
			$sumHTRight="";
			$sumHTLeft="";
			$sumHT="";
			$arHTVal=array();		
			for($i=1;$i<=$counterGrid;$i++){
				$elem_dis = $_POST["elem_gf_Distance_".$i];			
				$elem_near = $_POST["elem_gf_Near_".$i];		
				$elem_dis_comm = $_POST["elem_comm_Distance_".$i];
				$elem_near_comm = $_POST["elem_comm_Near_".$i];
				
				//apct
				$elem_dis_apct = $_POST["elem_apct_Distance_".$i];
				$elem_near_apct = $_POST["elem_apct_Near_".$i];
				
				//elem_sc_
				$elem_dis_sc = $_POST["elem_sc_Distance_".$i];
				$elem_near_sc = $_POST["elem_sc_Near_".$i];
				
				//elem_cc_
				$elem_dis_cc = $_POST["elem_cc_Distance_".$i];
				$elem_near_cc = $_POST["elem_cc_Near_".$i];
				
				
				//elem_ccprisms_
				$elem_dis_ccprisms = $_POST["elem_ccprisms_Distance_".$i];
				$elem_near_ccprisms = $_POST["elem_ccprisms_Near_".$i];
				
				//elem_bifocal_
				$elem_dis_bifocal = $_POST["elem_bifocal_Distance_".$i];
				$elem_near_bifocal = $_POST["elem_bifocal_Near_".$i];			
				
				$t = implode("",$elem_dis);
				$n = implode("",$elem_near);
				if(!empty($t)||!empty($n)||!empty($elem_dis_comm)||!empty($elem_near_comm)){
					$arGridVal[$i]["Distance"] = $elem_dis;
					$arGridVal[$i]["Distance"]["comments"] = $elem_dis_comm;
					
					$arGridVal[$i]["Distance"]["APCT"] = $elem_dis_apct;
					$arGridVal[$i]["Distance"]["SC"] = $elem_dis_sc;
					$arGridVal[$i]["Distance"]["CC"] = $elem_dis_cc;
					$arGridVal[$i]["Distance"]["CCPrisms"] = $elem_dis_ccprisms;
					$arGridVal[$i]["Distance"]["Bifocal"] = $elem_dis_bifocal;
					
					$arGridVal[$i]["Near"] = $elem_near;			
					$arGridVal[$i]["Near"]["comments"] = $elem_near_comm;
					
					$arGridVal[$i]["Near"]["APCT"] = $elem_near_apct;
					$arGridVal[$i]["Near"]["SC"] = $elem_near_sc;
					$arGridVal[$i]["Near"]["CC"] = $elem_near_cc;
					$arGridVal[$i]["Near"]["CCPrisms"] = $elem_near_ccprisms;
					$arGridVal[$i]["Near"]["Bifocal"] = $elem_near_bifocal;			
					
					if(!empty($elem_dis[16]) || !empty($elem_dis[17]) || !empty($elem_dis[18]) || !empty($elem_dis[19]) || 
						!empty($elem_near[16]) || !empty($elem_near[17]) || !empty($elem_near[18]) || !empty($elem_near[19]) ){
						$elem_dis[16] = trim($elem_dis[16]);
						$elem_dis[17] = trim($elem_dis[17]);
						$elem_dis[18] = trim($elem_dis[18]);
						$elem_dis[19] = trim($elem_dis[19]);
						
						$elem_near[16] = trim($elem_near[16]);
						$elem_near[17] = trim($elem_near[17]);
						$elem_near[18] = trim($elem_near[18]);
						$elem_near[19] = trim($elem_near[19]);					
						
						$tmp = "";
						if(!empty($elem_dis[16]) || !empty($elem_dis[17])) {$tmp .= trim($elem_dis[16]." ".$elem_dis[17]).",";}
						if(!empty($elem_dis[18]) || !empty($elem_dis[19])) {$tmp .= trim($elem_dis[18]." ".$elem_dis[19]).",";}
						
						if(!empty($elem_near[16]) || !empty($elem_near[17])) {$tmp .= trim($elem_near[16]." ".$elem_near[17]).",";}	
						if(!empty($elem_near[18]) || !empty($elem_near[19])) {$tmp .= trim($elem_near[18]." ".$elem_near[19]).",";}	
						
						$tmp = trim($tmp);
						if(!empty($tmp)){
							$tmp = substr($tmp,0,-1);					
							$tmp = "EOM=".$tmp."";						
							$sumMotility.= (!empty($sumMotility)) ? "; " : "";
							$sumMotility.= $tmp;
						}
					}
				}
				
				//Head Tilt Values -----------------
				
				$elem_right = $_POST["elem_ht_Right_".$i];			
				$elem_left = $_POST["elem_ht_Left_".$i];		
				$elem_right_comm = $_POST["elem_comm_Right_".$i];
				$elem_left_comm = $_POST["elem_comm_Left_".$i];			
				
				$t = implode("",$elem_right);
				$n = implode("",$elem_left);			
				
				if(!empty($t)||!empty($n)||!empty($elem_right_comm)||!empty($elem_left_comm)){
					
					$tmpR =implode(" ",$elem_right);
					$tmpL =implode(" ",$elem_left);
					
					$arHTVal[$i]["Right"] = $elem_right;
					$arHTVal[$i]["Right"]["comments"] = $elem_right_comm;
					$arHTVal[$i]["Left"] = $elem_left;			
					$arHTVal[$i]["Left"]["comments"] = $elem_left_comm;			
					
					//--
					//2.)    EOM Head Tilt “Orth” translates to “abnormal head tilt” in the Work view.
					//Recommendation= When “Ortho” is selected do not send anything to work view.
					$flg_ab_hd_t_od=$flg_ab_hd_t_os=0;
					$tmpR=trim($tmpR);
					$tmpL=trim($tmpL);
					if((!empty($tmpR) && strpos($tmpR, "ORTHO")===false)){ $flg_ab_hd_t_od=1; }
					if((!empty($tmpL) && strpos($tmpL, "ORTHO")===false)){ $flg_ab_hd_t_os=1; }
					
					if($flg_ab_hd_t_od==1){ //od
						if(!empty($elem_right_comm)){$tmpR.= " ".$elem_right_comm;}					
						$tmpR=trim($tmpR);					
						if(!empty($tmpR)){$sumHTRight.=$tmpR.", ";}				
					}
					
					if($flg_ab_hd_t_os==1){ //os
						if(!empty($elem_left_comm)){$tmpL.= " ".$elem_left_comm;}
						$tmpL=trim($tmpL);
						if(!empty($tmpL)){$sumHTLeft.=$tmpL.", ";}
					}
					//--
					
				}
				
				//Head Tilt Values -----------------			
			}	
			
			$sumMotility = trim($sumMotility);
			$strGridVal=sqlEscStr(serialize($arGridVal));		
			//Grid values--
			
			/*
			The numbers in the head tilt box SHOULD NOT APPEAR in the summary box. So in this case it
			says ET 14. That should not appear in the summary box. The only thing that should appear in
			the summary box is Abnormal Head Tilt OD
			*/
			
			//echo "<pre>";
			//print_r($arHTVal);
			
			//echo $tmpR." - ".$tmpL;
			
			//exit();
			
			$tmpeye="";
			if(!empty($sumHTRight)||!empty($sumHTLeft)){
				if(!empty($sumHTRight)&&!empty($sumHTLeft)){
					$tmpeye="OU";
				}else if(!empty($sumHTRight)){
					$tmpeye="OD";
				}else if(!empty($sumHTLeft)){
					$tmpeye="OS";
				}
				
				$sumHT ="Abnornal Head Tilt ".$tmpeye."";
			}
			
			//
			$strHTVal=sqlEscStr(serialize($arHTVal));
			///Head Title Values ---------
			
			// ductions-----------------------		
			$elem_duction_1 = $_POST["elem_duction_1_1"];			
			$elem_duction_2 = $_POST["elem_duction_1_2"];		
			$elem_comm_duction_1 = trim($_POST["elem_comments_duc_1_1"]);
			$elem_comm_duction_2 = trim($_POST["elem_comments_duc_1_2"]);
			
			$arrDuctionVals = array();
			$sumDuction1=$sumDuction2=$sumDuction1_val=$sumDuction2_val="";
			$tmp1=trim(implode("",$elem_duction_1));
			$tmp2=trim(implode("",$elem_duction_2));
			
			if(!empty($tmp1)||!empty($tmp2)||!empty($elem_comm_duction_1)||!empty($elem_comm_duction_2)){
				$arrDuctionVals["1"]["1"] = $elem_duction_1;
				$arrDuctionVals["1"]["1"]["comments"] = $elem_comm_duction_1;
				$arrDuctionVals["1"]["2"] = $elem_duction_2;			
				$arrDuctionVals["1"]["2"]["comments"] = $elem_comm_duction_2;
				
				if((!empty($tmp1)&& $tmp1!="00000000")||!empty($elem_comm_duction_1)){
					$tmp_lvl = array("RSR","UPGAZE","RIO","RLR","RMR","RIR","DOWNGAZE","RSO");
					foreach($elem_duction_1 as $key=>$val){
						if(!empty($val)){
							$sumDuction1_val .= $tmp_lvl[$key]." ".$val." ";	
						}
					}
					$sumDuction1_val =trim($sumDuction1_val);
					$sumDuction1.=trim(implode(" ",$elem_duction_1)." ".$elem_comm_duction_1).", ";				
				}
				
				if((!empty($tmp2)&&$tmp2!="00000000")||!empty($elem_comm_duction_2)){			
					$tmp_lvl = array("LIO","UPGAZE","LSR","LMR","LLR","LSO","DOWNGAZE","LIR");
					foreach($elem_duction_2 as $key=>$val){
						if(!empty($val)){
							$sumDuction2_val .= $tmp_lvl[$key]." ".$val." ";	
						}
					}
					$sumDuction2_val =trim($sumDuction2_val);
					$sumDuction2.=trim(implode(" ",$elem_duction_2)." ".$elem_comm_duction_2).", ";				
				}
			}
			
			//
			$tmpEye="";
			$sumDuction="";
			if(!empty($sumDuction1) || !empty($sumDuction2)){
				if(!empty($sumDuction1) && !empty($sumDuction2)){
					$tmpEye="OU";
				}else if(!empty($sumDuction1)){
					$tmpEye="OD";
				}else if(!empty($sumDuction2)){
					$tmpEye="OS";
				}
				
				$sumDuction = "Ductions ".$tmpEye." abnormal ";
			}
			
			if(!empty($sumDuction1_val)){
				$sumDuction .= " ".$sumDuction1_val;
			}
			
			if(!empty($sumDuction2_val)){
				$sumDuction .= " ".$sumDuction2_val;
			}		
			
			//
			$strDuctionVals=sqlEscStr(serialize($arrDuctionVals));	
			// ductions-----------------------	
			
			//Color Vision Test ------------
			$arrColorVis=array();
			$arrColorVis["elem_color_sign_od"] = $_POST["elem_color_sign_od"];
			$arrColorVis["elem_color_od_1"] = $_POST["elem_color_od_1"];
			$arrColorVis["elem_color_od_2"] = $_POST["elem_color_od_2"];
			$arrColorVis["elem_color_sign_os"] = $_POST["elem_color_sign_os"];
			$arrColorVis["elem_color_os_1"] = $_POST["elem_color_os_1"];
			$arrColorVis["elem_color_os_2"] = $_POST["elem_color_os_2"];
			$arrColorVis["elem_color_sign_ou"] = $_POST["elem_color_sign_ou"];
			$arrColorVis["elem_color_ou_1"] = $_POST["elem_color_ou_1"];
			$arrColorVis["elem_color_ou_2"] = $_POST["elem_color_ou_2"];
			$arrColorVis["elem_comm_colorVis"] = $_POST["elem_comm_colorVis"];
			
			/*$sumClrVis="";
			if(!empty($arrColorVis["elem_color_od_1"])||!empty($arrColorVis["elem_color_od_2"])){
				$sumClrVis .="".$arrColorVis["elem_color_sign_od"]." ".$arrColorVis["elem_color_od_1"]." / ".$arrColorVis["elem_color_od_2"]." ";		
			}
			
			if(!empty($arrColorVis["elem_color_os_1"])||!empty($arrColorVis["elem_color_os_2"])){
				$sumClrVis .="".$arrColorVis["elem_color_sign_os"]." ".$arrColorVis["elem_color_os_1"]." / ".$arrColorVis["elem_color_os_2"]." ";		
			}*/		
			//Color Vision Test ------------
			
			//WORTH 4 DOT ------------
			$arrW4Dot=array();
			$arrW4Dot["elem_w4dot_distance"]=$_POST["elem_w4dot_distance"];
			$arrW4Dot["elem_w4dot_near"]=$_POST["elem_w4dot_near"];
			$arrW4Dot["elem_comm_w4Dot"]=sqlEscStr($_POST["elem_comm_w4Dot"]);
			
			/*$sumW4Dot="";
			if(!empty($arrW4Dot["elem_w4dot_distance"])){$sumW4Dot .=$arrW4Dot["elem_w4dot_distance"]." ";}
			if(!empty($arrW4Dot["elem_w4dot_near"])){$sumW4Dot .=$arrW4Dot["elem_w4dot_near"]." ";}
			if(!empty($arrW4Dot["elem_comm_w4Dot"])){$sumW4Dot .=$arrW4Dot["elem_comm_w4Dot"]." ";}*/		
			//WORTH 4 DOT ------------

			//$commAnoHead
			$commAnoHead=sqlEscStr($_POST["elem_comments_AnoHead"]);
			$ahp_no = $_POST["elem_ahp_no"];
			
			//$commNystag
			$commNystag =sqlEscStr($_POST["elem_comments_Nystag"]);
			$nysta_no = $_POST["elem_nysta_no"];
			
			//Gen Comms
			$comments_gen_org =$_POST["elem_comments_gen"];
			$comments_gen =sqlEscStr($_POST["elem_comments_gen"]);

			// Summary --
			$eom_result = "";

			$tmp="";
			if(!empty($elem_npcWnlAbn)){
				$tmp .= "".$elem_npcWnlAbn." ";
			}
			if(!empty($elem_npcCm)){
				$tmp .= "".$elem_npcCm." ";//cm.
			}
			if(!empty($ortho_desc)){
				$tmp .= "".$ortho_desc." ";
			}
			if(!empty($tmp)){
				$eom_result .= "NPC = ".trim($tmp).", ";
			}
			
			//NPA
			$tmp="";
			if(!empty($elem_npaWnlAbn)){
				$tmp .= "".$elem_npaWnlAbn." ";
			}
			if(!empty($elem_npaCm)){
				$tmp .= "".$elem_npaCm." ";//cm.
			}
			if(!empty($npa_desc)){
				$tmp .= "".$npa_desc." ";
			}
			if(!empty($tmp)){
				$eom_result .= "NPA = ".trim($tmp).", ";
			}		
			
			$tmp = "";
			if($elem_eomFull == 1){
				$tmp .= "Full ";
			}
			if(!empty($full_desc)){
				$tmp .= " ".$full_desc." ";
			}
			if($elem_eomOrtho == 1 ){
				if(!empty($tmp)) $tmp .= " & ";
				$tmp .= " Ortho ";
			}
			if(!empty($tmp)){
				$eom_result .= "EOM = ".trim($tmp).", ";//EOM:
			}

			$tmp = "";
			if(!empty($eom_abn_right_left_alter)){
				$tmp .= "".$eom_abn_right_left_alter." ";
			}
			if(!empty($eom_abn_near_far_both)){
				$tmp .= "".$eom_abn_near_far_both." ";
			}
			if(!empty($elem_eomAbnDesc)){
				$tmp .= " ".$elem_eomAbnDesc." ";
			}
			if(!empty($tmp)){
				$eom_result .= "".trim($tmp).", ";//Abnormal:
			}

			$tmp = "";
			if(!empty($eom_hori_eso_exo)){
				$tmp .= "".$eom_hori_eso_exo." ";
			}
			if(!empty($eom_hori_trophia_phoria)){
				$tmp .= "".$eom_hori_trophia_phoria." ";
			}
			if(!empty($eom_hori_near_far_both)){
				$tmp .= "".$eom_hori_near_far_both." ";
			}
			if(!empty($elem_eomHoriDesc)){
				$tmp .= "".$elem_eomHoriDesc." ";
			}
			if(!empty($tmp)){
				$eom_result .= "".trim($tmp).", ";//Horizontal:
			}

			$tmp = "";
			if(!empty($eom_verti_hyper_hypo)){
				$tmp .= "".$eom_verti_hyper_hypo." ";
			}
			if(!empty($eom_verti_trophia_phoria)){
				$tmp .= "".$eom_verti_trophia_phoria." ";
			}
			if(!empty($eom_verti_near_far_both)){
				$tmp .= "".$eom_verti_near_far_both." ";
			}
			if(!empty($elem_eomVertiDesc)){
				$tmp .= "".$elem_eomVertiDesc;
			}
			if(!empty($tmp)){
				$eom_result .= "".trim($tmp).", ";//Vertical:
			}
			
			//AV Patters
			if(!empty($avpat_eso)){
				$eom_result .= "".$avpat_eso.", ";
			}
			

			//Control
			if(!empty($eomControl)){
				$eom_result .= "".$eomControl.", ";
			}
			
			//Motilility		
			if(!empty($sumMotility)){
				$eom_result .= "".$sumMotility.", ";
			}
			
			//Randot
			/*if(!empty($sumRandot)){
				$eom_result .= "".$sumRandot.", ";
			}*/

			//Distance
			$tmp="";
			/*
			if(!empty($rightHdTiltDis)){
				$tmp .= "".$rightHdTiltDis." ";
			}
			if(!empty($leftHdTiltDis)){
				$tmp .= "".$leftHdTiltDis." ";
			}
			if(!empty($commDis_1)){
				$tmp .= "".$commDis_1." ";
			}
			if(!empty($commDis)){
				$tmp .= "".$commDis." ";
			}
			if(!empty($tmp)){
				$eom_result .= "".trim($tmp).", ";
			}		
			if(!empty($sumHTRight)){
				$eom_result .= "".trim($sumHTRight)." ";
			}*/
			
			if(!empty($sumHT)){
				$eom_result .= "".$sumHT.", ";
			}
			

			//Near
			$tmp = "";
			/*
			if(!empty($rightHdTiltNear)){
				$tmp .= "".$rightHdTiltNear." ";
			}
			if(!empty($leftHdTiltNear)){
				$tmp .= "".$leftHdTiltNear." ";
			}
			if(!empty($commNear_1)){
				$tmp .= "".$commNear_1." ";
			}
			if(!empty($commNear)){
				$tmp .= "".$commNear." ";
			}
			if(!empty($tmp)){
				$eom_result .= "".trim($tmp).", ";
			}
			if(!empty($sumHTLeft)){
				$eom_result .= "".trim($sumHTLeft)." ";
			}
			*/
			
			/*
			//COLOR Vision Test
			if(!empty($sumClrVis)){
				$eom_result .= "".trim($sumClrVis).", ";	
			}	
			
			//Worth4Dot
			if(!empty($sumW4Dot)){
				$eom_result .= "".trim($sumW4Dot).", ";	
			}
			*/
			
			//Duction		
			if(!empty($sumDuction)){
				$eom_result .= "".trim($sumDuction).", ";
			}
			//Anomalous Head Position 
			/*
			6. ANOMALOUS HEAD POSITION
			a. IF ANYTHING IS FILLED IN - IN THE ANOMALOUS HEAD POSITION BOX THEN
			b. In summary box - enter Abnormal Anomalous Head Position.
			*/
			if(!empty($commAnoHead)){	
				$eom_result .= "Anomalous Head Position, ";		
			}		
			//Nystagmus 
			/*
			NYSTAGMUS
			a. IF ANYTHING IS FILLED IN IN THE NYSTAGMUS COMMENT FIELD THEN
			b. In summary box, enter Nystagmus present
			*/
			if(!empty($commNystag)){
				$eom_result .= "Nystagmus present, ";
			}
			//
			//Gen Comm
			if(!empty($comments_gen_org)){
				$eom_result .= "".$comments_gen_org.", ";
			}		

			$wnl = $elem_wnl;
			//if($wnl == "1"){$eom_result="WNL";}
			//$summary = ($elem_isPositive == "1") ? trim($eom_result) : "" ;		
			$summary = trim($eom_result);
			
			if($wnl=="1"){ $summary = ""; } //if WNL is set we display WNL values as summary.
			
			/* Stopped and wait for aproval
			//Check if Summary is of Default selected values and EOM exam is not touched.	
			$tmp = str_replace(" ","",$summary);
			if(trim($tmp) == trim("EOM=Full&Ortho,") && strpos($statusElem,"elem_chng_divEom=1")===false){
				$summary="";
			}
			*/	
			$summary = sqlEscStr(substr($summary, 0, -1));		
			// Summary --
			
			$elem_npcCm = sqlEscStr($elem_npcCm);
			$ortho_desc = sqlEscStr($ortho_desc);
			$npa_desc = sqlEscStr($npa_desc);
			$full_desc = sqlEscStr($full_desc);
			//$elem_eomHoriDesc = sqlEscStr($elem_eomHoriDesc);
			//$elem_eomVertiDesc = sqlEscStr($elem_eomVertiDesc);
			//$elem_eomAbnDesc = sqlEscStr($elem_eomAbnDesc);
			
			//ut_elems ----------------------
			$elem_utElems = $_POST["elem_utElems"];
			$elem_utElems_cur = $_POST["elem_utElems_cur"];
			$ut_elem = $this->getUTElemString($elem_utElems,$elem_utElems_cur);
			$ut_elem = $this->ut_removeEomFields($ut_elem);	
			//		
			//ut_elems ----------------------
			
			//Purge
			if(!empty($_POST["elem_purged"])){
				//$purgePhrse = " , purged = pupil_id ";
				//Update
				$sql = "UPDATE chart_eom
					  SET
					  purged=eom_id,
					  purgerId='".$_SESSION["authId"]."',
					  purgetime='".wv_dt('now')."'
					  WHERE form_id='".$elem_formId."' AND patient_id='".$patientid."' AND purged = '0'
					";			
				$row = sqlQuery($sql);
				
				//ICP
				$sql = "UPDATE chart_icp_color
					  SET
					  purged=id,
					  purgerId='".$_SESSION["authId"]."',
					  purgetime='".wv_dt('now')."'
					  WHERE form_id='".$elem_formId."' AND patient_id='".$patientid."' AND purged = '0'
					";			
				$row = sqlQuery($sql);
				
				//Stereopsis
				$sql = "UPDATE chart_steropsis 
					  SET
					  purged=id,
					  purgerId='".$_SESSION["authId"]."',
					  purgetime='".wv_dt('now')."'
					  WHERE form_id='".$elem_formId."' AND patient_id='".$patientid."' AND purged = '0'
					";			
				$row = sqlQuery($sql);			
				
				//W4Dot
				$sql = "UPDATE chart_w4dot  
					  SET
					  purged=id,
					  purgerId='".$_SESSION["authId"]."',
					  purgetime='".wv_dt('now')."'
					  WHERE form_id='".$elem_formId."' AND patient_id='".$patientid."' AND purged = '0'
					";			
				$row = sqlQuery($sql);			
				
			}else{
				$owv = new WorkView();
				//check EOM --------------
				$cQry = "select last_opr_id,uid,eom_id,sumEom,wnl,modi_note_Arr,wnl_value,exam_date  FROM chart_eom WHERE form_id='".$elem_formId."' AND patient_id='".$patientid."' AND purged='0' ";
				$row = sqlQuery($cQry);
				$last_opr_id = $owv->get_last_opr_id($row['last_opr_id'],$row["uid"]);
				if($row == false){
					$elem_editMode = "0";
				}else{
					$elem_eomId = $eomIDExam = $row["eom_id"];				
					$elem_editMode = "1";
					
					//Modifying Notes----------------
					$modi_note=$owv->getModiNotes($row["sumEom"],$row["wnl"],$summary,$elem_wnl,$row["uid"],$row["wnl_value"],$row['exam_date']);
					$seri_modi_note_eomArr = $owv->getModiNotesArr($row["sumEom"],$summary,$last_opr_id,'',$row["modi_note_Arr"],$row["wnl_value"],$row['exam_date']);
					//Modifying Notes----------------			
				}			
				
				if($elem_editMode == "0"){
					
					//save  wnl value
					$wnl_value = $this->getExamWnlStr("EOM");	

					$sql="insert into chart_eom set
						npc_wnl_abn='$elem_npcWnlAbn',
						npc_cm='$elem_npcCm',
						eom_full='$elem_eomFull',
						full_desc='$full_desc',
						eom_ortho='$elem_eomOrtho',
						ortho_desc='$ortho_desc',
						eom_abn_right_left_alter='$eom_abn_right_left_alter',
						eom_abn_near_far_both='$eom_abn_near_far_both',
						eom_abn_desc='".sqlEscStr($elem_eomAbnDesc)."',
						eom_hori_eso_exo='$eom_hori_eso_exo',
						eom_hori_trophia_phoria='$eom_hori_trophia_phoria',
						eom_hori_near_far_both='$eom_hori_near_far_both',
						eom_hori_desc='".sqlEscStr($elem_eomHoriDesc)."',
						eom_verti_hyper_hypo='$eom_verti_hyper_hypo',
						eom_verti_near_far_both='$eom_verti_near_far_both',
						eom_verti_trophia_phoria='$eom_verti_trophia_phoria',
						eom_verti_desc='".sqlEscStr($elem_eomVertiDesc)."',
						form_id='$elem_formId',
						patient_id='$patientid',
						exam_date='$elem_examDate',
						wnl='$elem_wnl',
						examined_no_change='$elem_noChange',
						isPositive = '$elem_isPositive',
						sumEom = '$summary',
						eomDrawing = '$elem_eomDrawing',
						rightHdTiltDis = '".$rightHdTiltDis."',
						leftHdTiltDis = '".$leftHdTiltDis."',
						commDis = '".$commDis."',
						eomDrwNear = '".$eomDrwNear."',
						rightHdTiltNear = '".$rightHdTiltNear."',
						leftHdTiltNear = '".$leftHdTiltNear."',
						commNear = '".$commNear."',
						descEom = '".$elem_descEom."',
						uid = '".$_SESSION["authId"]."',
						wnl_2='".$wnl_2."',
						isPositive_2='".$isPositive_2."',
						examined_no_change2='".$examined_no_change2."',
						wnl_3='".$wnl_3."',
						isPositive_3='".$isPositive_3."',
						examined_no_change3='".$examined_no_change3."',
						last_opr_id ='".$last_opr_id ."',";
						
						if(empty($_REQUEST["hidBlEnHTMLDrawing"]) == true){
							$sql .= "eomDrawing_2='".$eomDrawing_2."',";
							$sql .= "drawing_insert_update_from='0',";
						}
						else{
							$sql .= "drawing_insert_update_from='1',";
						}
						$sql .= "desc_draw_od='".$desc_draw_od."',
						desc_draw_os='".$desc_draw_os."',
						statusElem ='".$statusElem."',
						eomControl='".$eomControl."',
						eomDrawing_Dis2='".$eomDrawing_Dis2."',
						eomDrwNear_2='".$eomDrwNear_2."',
						commDis_1='".$commDis_1."',
						commNear_1='".$commNear_1."',
						ut_elem = '".$ut_elem."',
						gridvals = '".$strGridVal."',
						htvals = '".$strHTVal."',
						ductionvals = '".$strDuctionVals."',
						commAnoHead = '".$commAnoHead."', 
						commNystag = '".$commNystag."',
						npa = '".$npa."',
						npa_cm = '".$npa_cm."',
						npa_desc = '".$npa_desc."',
						avpat_eso = '".$avpat_eso."',
						avpat_exo = '".$avpat_exo."',
						randot_st = '".$elem_ranSt."',
						randot_dots ='".$ranSt_Dots9."',
						ahp_no='".$ahp_no."',
						nysta_no='".$nysta_no."',
						comments_gen='".$comments_gen."',
						wnl_value='".sqlEscStr($wnl_value)."'
						";

					$elem_eomId = sqlInsert($sql);			
					//$elem_eomId = mysql_insert_id();
					//$flagCFD=1;
					//$flagCFD_drw1=0;
					$arrCFD_ids=array();
					if(isset($_REQUEST["hidBlEnHTMLDrawing"]) == true && empty($_REQUEST["hidBlEnHTMLDrawing"]) == false && $_REQUEST["hidBlEnHTMLDrawing"] == "1" && (int)$elem_eomId > 0){
						for($intTempDrawCount = 0; $intTempDrawCount < 25; $intTempDrawCount++){
							if($_REQUEST["hidDrawingChangeYesNo".$intTempDrawCount] == "yes"){	
								$arrDrawingData = array();	
								$arrDrawingData["imagePath"] = $oSaveFile->getUploadDirPath(); //dirname(__FILE__)."/../main/uploaddir";
								$arrDrawingData["hidRedPixel"] = $_REQUEST["hidRedPixel".$intTempDrawCount];
								$arrDrawingData["hidGreenPixel"] = $_REQUEST["hidGreenPixel".$intTempDrawCount];
								$arrDrawingData["hidBluePixel"] = $_REQUEST["hidBluePixel".$intTempDrawCount];
								$arrDrawingData["hidAlphaPixel"] = $_REQUEST["hidAlphaPixel".$intTempDrawCount];
								$arrDrawingData["hidImageCss"] = $_REQUEST["hidImageCss".$intTempDrawCount];
								$arrDrawingData["hidDrawingTestImageP"] = $_REQUEST["hidDrawingTestImageP".$intTempDrawCount];
								$arrDrawingData["patId"] = $patientid;
								$arrDrawingData["hidCanvasImgData"] = $_REQUEST["hidCanvasImgData".$intTempDrawCount];
								$drawingFileName = "/EOM_idoc_drawing_".date("YmdHsi")."_".session_id()."_".$intTempDrawCount.".png";
								$arrDrawingData["drawingFileName"] = $drawingFileName;
								$arrDrawingData["drawingFor"] = "EOM";
								$arrDrawingData["drawingForMasterId"] = $elem_eomId;
								$arrDrawingData["formId"] = $elem_formId;
								$arrDrawingData["hidDrawingTestName"] = $_REQUEST["hidDrawingTestName".$intTempDrawCount];
								$arrDrawingData["hidDrawingTestId"] = $_REQUEST["hidDrawingTestId".$intTempDrawCount];
								$arrDrawingData["hidImagesData"] = $_REQUEST["hidImagesData".$intTempDrawCount];
								$arrDrawingData["hidDrawingId"] = (int)$_REQUEST["hidEOMDrawingId".$intTempDrawCount];
								$arrDrawingData["examMasterTable"] = "chart_eom";
								$arrDrawingData["examMasterTablePriColumn"] = "eom_id";
								$arrDrawingData["drwNE"] = $_REQUEST["elem_drwNE".$intTempDrawCount];
								$arrDrawingData["hidDrwDataJson"] = $_REQUEST["hidDrwDataJson".$intTempDrawCount];
								//pre($arrDrawingData);
								$OBJDrawingData->insertDrawingData($arrDrawingData);
								//$flagCFD=0;
							}else{
							
							//Check old drawing exists for carry forward
							if(!empty($_POST["hidEOMDrawingId".$intTempDrawCount])){
								$arrCFD_ids[] = $_POST["hidEOMDrawingId".$intTempDrawCount];
								//$flagCFD_drw1=1;
							}
							
							}
							
						}
						$arrIDocId = array();
						$strIDocId = "";
						$qryGetIDocIdInMasetr = "select id from ".constant("IMEDIC_SCAN_DB").".idoc_drawing where drawing_for = 'EOM' and drawing_for_master_id = '".$elem_eomId."' 
													and patient_id = '".$patientid."' and patient_form_id = '".$elem_formId."' ";
						$rsGetIDocIdInMasetr = imw_query($qryGetIDocIdInMasetr);
						if(imw_num_rows($rsGetIDocIdInMasetr) > 0){
							while($rowGetIDocIdInMasetr = imw_fetch_array($rsGetIDocIdInMasetr)){
								$arrIDocId[] = $rowGetIDocIdInMasetr["id"];
							}
						}
						if(count($arrIDocId) > 0){
							$strIDocId = implode(",", $arrIDocId);
						}
						$qryUpdateEomIDoc = "update chart_eom set idoc_drawing_id = '".$strIDocId."' where eom_id = '".$elem_eomId."' ";
						$rsUpdateEomIDoc = imw_query($qryUpdateEomIDoc);
						/*$arrDrawingData = array();	
						$arrDrawingData["imagePath"] = "../main/uploaddir";
						$arrDrawingData["hidRedPixel"] = $_REQUEST["hidRedPixel"];
						$arrDrawingData["hidGreenPixel"] = $_REQUEST["hidGreenPixel"];
						$arrDrawingData["hidBluePixel"] = $_REQUEST["hidBluePixel"];
						$arrDrawingData["hidAlphaPixel"] = $_REQUEST["hidAlphaPixel"];
						$arrDrawingData["hidImageCss"] = $_REQUEST["hidImageCss"];
						$arrDrawingData["hidDrawingTestImageP"] = $_REQUEST['hidDrawingTestImageP'];
						$arrDrawingData["patId"] = $patientid;
						$arrDrawingData["hidCanvasImgData"] = $_REQUEST['hidCanvasImgData'];
						$drawingFileName = "/EOM_idoc_drawing_".date("YmdHsi")."_".session_id().".png";
						$arrDrawingData["drawingFileName"] = $drawingFileName;
						$arrDrawingData["drawingFor"] = "EOM";
						$arrDrawingData["drawingForMasterId"] = $elem_eomId;
						$arrDrawingData["formId"] = $elem_formId;
						$arrDrawingData["hidDrawingTestName"] = $_REQUEST['hidDrawingTestName'];
						$arrDrawingData["hidDrawingTestId"] = $_REQUEST['hidDrawingTestId'];
						$arrDrawingData["hidImagesData"] = $_REQUEST['hidImagesData'];
						$arrDrawingData["examMasterTable"] = "chart_eom";
						$arrDrawingData["examMasterTablePriColumn"] = "eom_id";				
						$OBJDrawingData->insertDrawingData($arrDrawingData);*/				
					}

					//if(!empty($_POST["elem_eomId_LF"])&&!empty($flagCFD_drw1) && $flagCFD==1){ // Check if Last visit Drawing exists
					if(!empty($_POST["elem_eomId_LF"]) && count($arrCFD_ids)>0){ // Check if Last visit Drawing exists
						// Carry Forward iDOC Draw : This is done because drawing is not saved when not touched but we need to carry forward to display.Drawing status will be grey.			
						$arrIN = array();
						$arrIN["pid"]=$patientid;
						$arrIN["formId"]=$elem_formId;
						$arrIN["examId"]=$elem_eomId;
						$arrIN["exam"]="EOM";
						$arrIN["examIdLF"]=$_POST["elem_eomId_LF"];
						$arrIN["strDrwIdsLF"]=implode(",", $arrCFD_ids);
						$arrIN["examMasterTable"]="chart_eom";
						$arrIN["examMasterTablePriColumn"]="eom_id";
						$OBJDrawingData->carryForward($arrIN);
						//
					}			
					//header("location:eom.php?elem_editMode=1&$elem_eomId=".$elem_eomId);
				}else if($elem_editMode == "1"){
					//$elem_eomId = $row["eom_id"];
					$sql="update chart_eom set
						npc_wnl_abn='$elem_npcWnlAbn',
						npc_cm='$elem_npcCm',
						eom_full='$elem_eomFull',
						full_desc='$full_desc',
						eom_ortho='$elem_eomOrtho',
						ortho_desc='$ortho_desc',
						eom_abn_right_left_alter='$eom_abn_right_left_alter',
						eom_abn_near_far_both='$eom_abn_near_far_both',
						eom_abn_desc='".sqlEscStr($elem_eomAbnDesc)."',
						eom_hori_eso_exo='$eom_hori_eso_exo',
						eom_hori_trophia_phoria='$eom_hori_trophia_phoria',
						eom_hori_near_far_both='$eom_hori_near_far_both',
						eom_hori_desc='".sqlEscStr($elem_eomHoriDesc)."',
						eom_verti_hyper_hypo='$eom_verti_hyper_hypo',
						eom_verti_near_far_both='$eom_verti_near_far_both',
						eom_verti_trophia_phoria='$eom_verti_trophia_phoria',
						eom_verti_desc='".sqlEscStr($elem_eomVertiDesc)."',
						exam_date='$elem_examDate',
						wnl='$elem_wnl',
						examined_no_change='$elem_noChange',
						isPositive = '$elem_isPositive',
						sumEom = '$summary',
						eomDrawing = '$elem_eomDrawing',
						rightHdTiltDis = '".$rightHdTiltDis."',
						leftHdTiltDis = '".$leftHdTiltDis."',
						commDis = '".$commDis."',
						eomDrwNear = '".$eomDrwNear."',
						rightHdTiltNear = '".$rightHdTiltNear."',
						leftHdTiltNear = '".$leftHdTiltNear."',
						commNear = '".$commNear."',
						descEom = '".$elem_descEom."',
						uid = '".$_SESSION["authId"]."',
						wnl_2='".$wnl_2."',
						isPositive_2='".$isPositive_2."',
						examined_no_change2='".$examined_no_change2."',
						wnl_3='".$wnl_3."',
						isPositive_3='".$isPositive_3."',
						examined_no_change3='".$examined_no_change3."',";
						
						if(empty($_REQUEST["hidBlEnHTMLDrawing"]) == true){
							$sql .= "eomDrawing_2='".$eomDrawing_2."',";
							$sql .= "drawing_insert_update_from='0',";
						}
						else{
							$sql .= "drawing_insert_update_from='1',";
						}
						
						$sql .= "desc_draw_od='".$desc_draw_od."',
						desc_draw_os='".$desc_draw_os."',
						statusElem ='".$statusElem."',
						eomControl='".$eomControl."',
						eomDrawing_Dis2='".$eomDrawing_Dis2."',
						eomDrwNear_2='".$eomDrwNear_2."',
						commDis_1='".$commDis_1."',
						commNear_1='".$commNear_1."',
						ut_elem = '".$ut_elem."',
						gridvals = '".$strGridVal."',
						
						htvals = '".$strHTVal."',
						ductionvals = '".$strDuctionVals."',
						commAnoHead = '".$commAnoHead."', 
						commNystag = '".$commNystag."',
						
						npa = '".$npa."',
						npa_cm = '".$npa_cm."',
						npa_desc = '".$npa_desc."',
						avpat_eso = '".$avpat_eso."',
						avpat_exo = '".$avpat_exo."',
						randot_st = '".$elem_ranSt."',
						randot_dots ='".$ranSt_Dots9."',
						ahp_no='".$ahp_no."',
						nysta_no='".$nysta_no."',
						comments_gen='".$comments_gen."',
						modi_note_Arr='".sqlEscStr($seri_modi_note_eomArr)."',	
						last_opr_id = '".$last_opr_id."'
						WHERE form_id='".$elem_formId."' AND patient_id='".$patientid."' AND purged = '0' ";
					sqlQuery($sql);
					if($eomIDExam > 0){
						$elem_eomId = $eomIDExam;
					}
					if(isset($_REQUEST["hidBlEnHTMLDrawing"]) == true && empty($_REQUEST["hidBlEnHTMLDrawing"]) == false && $_REQUEST["hidBlEnHTMLDrawing"] == "1" && (int)$elem_eomId > 0){
						$recordModiDateDrawing=0;
						$arr_updrw_ids=array();
						for($intTempDrawCount = 0; $intTempDrawCount < 25; $intTempDrawCount++){
							if($_REQUEST["hidDrawingChangeYesNo".$intTempDrawCount] == "yes"){
								$arrDrawingData = array();
								$arrDrawingData["imagePath"] = $oSaveFile->getUploadDirPath(); //dirname(__FILE__)."/../main/uploaddir";
								$arrDrawingData["hidRedPixel"] = $_REQUEST["hidRedPixel".$intTempDrawCount];
								$arrDrawingData["hidGreenPixel"] = $_REQUEST["hidGreenPixel".$intTempDrawCount];
								$arrDrawingData["hidBluePixel"] = $_REQUEST["hidBluePixel".$intTempDrawCount];
								$arrDrawingData["hidAlphaPixel"] = $_REQUEST["hidAlphaPixel".$intTempDrawCount];
								$arrDrawingData["hidImageCss"] = $_REQUEST["hidImageCss".$intTempDrawCount];
								$arrDrawingData["hidDrawingTestImageP"] = $_REQUEST["hidDrawingTestImageP".$intTempDrawCount];
								$arrDrawingData["patId"] = $patientid;
								$arrDrawingData["hidCanvasImgData"] = $_REQUEST["hidCanvasImgData".$intTempDrawCount];
								$drawingFileName = "/EOM_idoc_drawing_".date("YmdHsi")."_".session_id()."_".$intTempDrawCount.".png";
								$arrDrawingData["drawingFileName"] = $drawingFileName;
								$arrDrawingData["drawingFor"] = "EOM";
								$arrDrawingData["drawingForMasterId"] = $elem_eomId;
								$arrDrawingData["formId"] = $elem_formId;
								$arrDrawingData["hidDrawingTestName"] = $_REQUEST["hidDrawingTestName".$intTempDrawCount];
								$arrDrawingData["hidDrawingTestId"] = $_REQUEST["hidDrawingTestId".$intTempDrawCount];
								$arrDrawingData["hidImagesData"] = $_REQUEST["hidImagesData".$intTempDrawCount];
								$arrDrawingData["hidDrawingId"] = (int)$_REQUEST["hidEOMDrawingId".$intTempDrawCount];
								$arrDrawingData["examMasterTable"] = "chart_eom";
								$arrDrawingData["examMasterTablePriColumn"] = "eom_id";
								$arrDrawingData["drwNE"] = $_REQUEST["elem_drwNE".$intTempDrawCount];
								$arrDrawingData["hidDrwDataJson"] = $_REQUEST["hidDrwDataJson".$intTempDrawCount];
								//pre($arrDrawingData,1);
								$arrDrawingData["hidDrawingId"] = $OBJDrawingData->updateDrawingData($arrDrawingData);
								
								if(!empty($arrDrawingData["hidDrawingId"])){
									$recordModiDateDrawing=$arrDrawingData["hidDrawingId"];
									$arr_updrw_ids[]=$arrDrawingData["hidDrawingId"];
								}
								
							}else{
								if(!empty($_POST["hidEOMDrawingId".$intTempDrawCount])){
									$arr_updrw_ids[] = $_POST["hidEOMDrawingId".$intTempDrawCount];							
								}
							}				
							
						}
						
						//delete records of previous visit if any --
						$OBJDrawingData->deleteNoSavedDrwing(array($patientid, $elem_formId,$elem_eomId,"EOM"), $arr_updrw_ids);
						//--
						
						//form Id
						list($strIDocId, $str_row_modify_Draw)=$OBJDrawingData->getExamDocids(array($patientid, $elem_formId,$elem_eomId,"EOM",$recordModiDateDrawing));
						
						/*
						$arrIDocId = array();
						$strIDocId = "";	
						$str_row_modify_Draw="";	
						$qryGetIDocIdInMasetr = "select id,row_modify_by, DATE_FORMAT(row_modify_date_time,'%m-%d-%y %H:%i') as modidate 
						from ".constant("IMEDIC_SCAN_DB").".idoc_drawing where drawing_for = 'EOM' and drawing_for_master_id = '".$elem_eomId."' 
													and patient_id = '".$patientid."' and patient_form_id = '".$elem_formId."' ";	
						$rsGetIDocIdInMasetr = mysql_query($qryGetIDocIdInMasetr);
						if(mysql_num_rows($rsGetIDocIdInMasetr) > 0){
							while($rowGetIDocIdInMasetr = mysql_fetch_array($rsGetIDocIdInMasetr)){
								$arrIDocId[] = $rowGetIDocIdInMasetr["id"];
								
								//GEt Drawing Modified Date
								if(!empty($recordModiDateDrawing) && $recordModiDateDrawing==$rowGetIDocIdInMasetr["id"]&&!empty($rowGetIDocIdInMasetr["row_modify_by"])){
									$str_row_modify_Draw=getModiNotes_drawing($rowGetIDocIdInMasetr["row_modify_by"],$rowGetIDocIdInMasetr["modidate"]);
									$str_row_modify_Draw=", ".$str_row_modify_Draw;								
								}

							}
						}
						if(count($arrIDocId) > 0){
							$strIDocId = implode(",", $arrIDocId);
						}					
						*/
						
						$qryUpdateEomIDoc = "update chart_eom set idoc_drawing_id = '".$strIDocId."' ".$str_row_modify_Draw." where eom_id = '".$elem_eomId."' ";
						$rsUpdateEomIDoc = sqlQuery($qryUpdateEomIDoc);
						/*$arrDrawingData = array();
						$arrDrawingData["imagePath"] = "../main/uploaddir";
						$arrDrawingData["hidRedPixel"] = $_REQUEST["hidRedPixel"];
						$arrDrawingData["hidGreenPixel"] = $_REQUEST["hidGreenPixel"];
						$arrDrawingData["hidBluePixel"] = $_REQUEST["hidBluePixel"];
						$arrDrawingData["hidAlphaPixel"] = $_REQUEST["hidAlphaPixel"];
						$arrDrawingData["hidImageCss"] = $_REQUEST["hidImageCss"];
						$arrDrawingData["hidDrawingTestImageP"] = $_REQUEST['hidDrawingTestImageP'];
						$arrDrawingData["patId"] = $patientid;
						$arrDrawingData["hidCanvasImgData"] = $_REQUEST['hidCanvasImgData'];
						$drawingFileName = "/EOM_idoc_drawing_".date("YmdHsi")."_".session_id().".png";
						$arrDrawingData["drawingFileName"] = $drawingFileName;
						$arrDrawingData["drawingFor"] = "EOM";
						$arrDrawingData["drawingForMasterId"] = $elem_eomId;
						$arrDrawingData["formId"] = $elem_formId;
						$arrDrawingData["hidDrawingTestName"] = $_REQUEST['hidDrawingTestName'];
						$arrDrawingData["hidDrawingTestId"] = $_REQUEST['hidDrawingTestId'];
						$arrDrawingData["hidImagesData"] = $_REQUEST['hidImagesData'];
						$arrDrawingData["hidDrawingId"] = $_REQUEST["hidEOMDrawingId"];
						$arrDrawingData["examMasterTable"] = "chart_eom";
						$arrDrawingData["examMasterTablePriColumn"] = "eom_id";
						$OBJDrawingData->updateDrawingData($arrDrawingData);*/
					}
					//header("location:eom.php?elem_editMode=1&$elem_eomId=".$elem_eomId);
				}
				//check EOM --------------
				
				if(strpos($statusElem, "Eom=1")!==false){
					// Stereopsis--------------					
					$this->saveStereopsis();
					// Stereopsis--------------		
					
					// Color Vision ------------					
					$this->saveColorVisionTest($arrColorVis,$elem_utElems_cur);
					// Color Vision ------------

					//W4Dot ----------				
					$this->saveW4Dot($arrW4Dot,$elem_utElems_cur);				
					//W4Dot ----------
				}

				// Make chart notes valid
				$this->makeChartNotesValid();

				//Set Change Date Arc Rec --
				$this->setChangeDtArcRec("chart_eom");
				//Set Change Date Arc Rec --
			
			}//End Purge block
			
			$fDraw = (!empty($elem_eomDrawing)) ? "1" : "0";
			//$AddExam = ($elem_editMode == "0") ? "window.opener.top.addActiveExam('EOM','".$elem_formId."');" : "";		
			
			//			
			$arrRet["Exam"] = "Eom";
			$arrRet["isPositive"] = $elem_isPositive;
			$arrRet["wnl"] = $elem_wnl;
			$arrRet["NC"] = $elem_noChange;
			$arrRet["Draw"] = $fDraw;
			$arrRet["SubSumOd"] = $summary;
			$arrRet["SubSumOs"] = "";
			$arrRet["AddExam"] = $elem_editMode;
			$arrRet["FormId"] = $elem_formId;

		}//
		
		//
		echo json_encode($arrRet);
	}
}

?>