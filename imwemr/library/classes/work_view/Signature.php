<?php
class Signature{	
	private $fid, $pid;
	private $path_sigs;
	public function __construct($fid, $pid=0){
		$this->fid = $fid;
		$this->pid = $pid;
		//$this->path_sigs=$GLOBALS['rootdir']."/main/uploaddir";
	}
	
	function getChartSigns(){
		$arrMultiSigns=array();
		$sql="	SELECT c1.sign_coords, c1.sign_path, c1.pro_id, c1.sign_type,
						c1.sign_coords_dateTime, 
					c2.id, c2.fname, c2.mname, c2.lname, c2.pro_suffix,
					c2.user_type, c2.sign,
					c3.user_type_name
				FROM chart_signatures c1
				LEFT JOIN users c2 ON c1.pro_id = c2.id
				LEFT JOIN user_type c3 ON c3.user_type_id = c2.user_type
				WHERE c1.form_id = '".$this->fid."' ORDER BY c1.sign_type 
			";
		//echo $sql; 
		$rez = sqlStatement($sql);
		for($i=0;$row=sqlFetchArray($rez);$i++){
			if(!empty($row["pro_id"])){	
				
				$oTmpUser = new User($row["pro_id"]);
				//stop tech to come in signatures
				if($row["user_type"]=="3"){
					$oRoleAs  = new RoleAs($row["pro_id"]);
					$ar_role = $oRoleAs->get_user_roles($this->fid);
					if(count($ar_role)>0){
						foreach($ar_role as $k => $v){
							if($v!="3"){ 
								$row["user_type"]=$v;
								$row["user_type_name"] = $oTmpUser->get_user_type_nm($row["user_type"]);
								break; 
							}
						}
					}
				}
				
				$arrMultiSigns["sign_coords"][]=$row["sign_coords"];
				$arrMultiSigns["sign_path"][]=$row["sign_path"];
				$arrMultiSigns["pro_id"][]=$row["pro_id"];
				
				//
				$tmp = trim($oTmpUser->getUNameFormatted(4,$row));
				$arrMultiSigns["pro_name"][]=$tmp;
				$arrMultiSigns["user_type"][]=$row["user_type"];
				$arrMultiSigns["sign_coords_dateTime"][]=$row["sign_coords_dateTime"];			
			}
		}

		return $arrMultiSigns;
	}
	
	function getChartPhysicians(){
		$arrMultiSigns=array();
		$sql=" SELECT c1.pro_id, c2.user_type FROM chart_signatures c1 
				INNER JOIN users c2 ON c1.pro_id = c2.id
				WHERE c1.form_id = '".$this->fid."' ORDER BY c1.sign_type 
				";
		$rez = sqlStatement($sql);
		for($i=0;$row=sqlFetchArray($rez);$i++){
			if(!empty($row["pro_id"])){				
				if(in_array($row["user_type"], $GLOBALS['arrValidCNPhy'])){
					$arrMultiSigns[] = $row["pro_id"];
				}
			}
		}
		return $arrMultiSigns;	
	}
	
	//*
	function getSignPads($arr, $curPhyId){

		global $elem_per_vo, $finalize_flag,
				$blEnableHTMLSignature,
				$isReviewable, $strFDate,$providerId,$patient_id,$form_id, 
				$isAutoFinalized,$elem_masterFinalDate;
		
		$cntr=0;	
		//$strPhyLeft=$strPhyRight="";	
		$arPhyLeft=$arPhyRight=array();	
		$elem_physicianId_1st = "";	
		
		//
		$len=count($arr["pro_id"]);
		
		
		$arrPhyW = array("1"=>5,"12"=>4,"19"=>3,"11"=>2,"13"=>1); //array of physician weightage
		$tmpPhyMw=0;
		$curPhyUsrType="";
		$flg_auto_fnlz=0;
		
		//get Finalize states
		$oClog = new ChartLog($patient_id, $form_id);
		$arr_finalize_dts = $oClog->get_finalize_dates();
		$i=0;
		while($i<$len){	
			
			$num=$cntr+1;		
			
			/*
			//Logged in should be Owner
			if($arr["pro_id"][$i]==$curPhyId){
				$num=1;	
			}*/
			
			$elem_physicianName=$arr["pro_name"][$i];
			$elem_physicianId=$arr["pro_id"][$i];
			$strpixls=$arr["sign_coords"][$i];
			$elem_sign_path=$arr["sign_path"][$i];		
			$user_type=$arr["user_type"][$i];
			$elem_sign_coords_dateTime=$arr["sign_coords_dateTime"][$i];
			
			$oUsr = new User($elem_physicianId);			
			$is_user_sign=$oUsr->getSign(1);
			
			if(!empty($elem_physicianId)){
				/*
				//<!-- Signature Physician -->
				$str.="
					
					<div class=\"divsign\" >
					<label >Signature</label>
						<span id=\"td_signature_applet".$num."\" class=\"appletCon\" onclick=\"getAssessmentSign(".$num.")\">
						$strpath_img
						</span>
						<span class=\"appletEraser\" onclick=\"getAssessmentSign('".$num."','3')\" title=\"Delete\" >&#9249;</span>
					<input type=\"hidden\" name=\"elem_signCoords".$num."\" id=\"elem_signCoords".$num."\" value=\"".$strpxl."\" onChange=\"setThisChangeStatus(this)\">
					<input type=\"hidden\" name=\"hdSignCoordsOriginal".$num."\" value=\"".$strpxl."\">
					<input type=\"hidden\" name=\"elem_sign_path".$num."\" value=\"".$strpath."\">
					<br/><br/>
					<label>Signer Name</label>
					<input type=\"text\" name=\"elem_physicianIdName".$num."\" value=\"".$elem_physicianName."\" readonly=\"readonly\" >
					<input type=\"hidden\" name=\"elem_physicianId".$num."\" value=\"".$elem_physicianId."\" onChange=\"setPhysician(this)\">
					</div>
					
					";
				//<!-- Signature Physician -->
				*/
				
				
				//----Chart Owner Physician decide logic----
				//hierarchy
				//phy 1> att 12> fellow 19> resi 11
				//Dt: 23-01-2013
				if($tmpPhyMw<=$arrPhyW[$user_type]){
					$tmpPhyMw = $arrPhyW[$user_type];
					
					if($user_type!="13"){
						$elem_physicianId_1st = $elem_physicianId;
					}
				}
				if($curPhyId == $elem_physicianId){ 
					if($user_type!="13"){
					$curPhyUsrType=$user_type;
					}	
				}
				//----Chart Owner Physician ----


	//TEST---------------------		

	//<!-- Signature Physician --> //

	$str="";
	$arr_ret_tmp=array();

	/*
	if($num == 1){
		$elem_physicianId_1st = $elem_physicianId;
	}*/

	//Sign Date
	$strFDate = "";
	if(!empty($elem_sign_coords_dateTime) && strpos($elem_sign_coords_dateTime,"0000")===false){
		//$tmpodate = new DateTime($elem_sign_coords_dateTime);
		//$tmpdate = $tmpodate->format('m-d-Y H:i:s');
		//getDateFormat(date('Y-m-d',strtotime($elem_sign_coords_dateTime)))." ".date("H:i:s",strtotime($elem_sign_coords_dateTime));
		$tmpdate = wv_formatDate($elem_sign_coords_dateTime,0,1); 
		//if($isAutoFinalized=="1") $strAutoFinal = " (AutoFinalized) ";
		/*
		$strFDate =  "<br/><label>&nbsp;</label>".
					 //"<i>Finalized on ".$tmpdate." ".$strAutoFinal."</i> ".
					 "<i>Signed on ".$tmpdate."</i> ".
					 "";
		*/
		//
		if(!empty($isAutoFinalized) && $elem_sign_coords_dateTime==$elem_masterFinalDate){
			$strFDate = "Finalized on ";
		}else{
			$strFDate = "Signed on ";
		}
		
		$strFDate .= "".$tmpdate."";
	}else{
		/*$strFDate =  "<br/><label>&nbsp;</label>".				 
					 "<i></i> ".
					 "";*/
					 
	}
	
	//Autofinalized
	if(!empty($isAutoFinalized) && empty($flg_auto_fnlz)){
		if(!empty($strFDate)){ $strFDate.="<br/>"; }
		$strFDate .= "<strong class=\"text-primary\">Auto-Finalized</strong>";
		$flg_auto_fnlz=1;//stop more print
	}
	if(!empty($strFDate)){ $strFDate ="<small>".$strFDate."</small>"; }
	
	//Get Finalize date and time more if finalized multiple times
	if(isset($arr_finalize_dts[$elem_physicianId]) && is_array($arr_finalize_dts[$elem_physicianId]) && count($arr_finalize_dts[$elem_physicianId])>0){
		foreach($arr_finalize_dts[$elem_physicianId] as $k_finalize_dts => $v_finalize_dts){
			if(!empty($v_finalize_dts) && strpos($v_finalize_dts, "0000")===false && $elem_sign_coords_dateTime!=$v_finalize_dts){				
				$strFDate .= "<br/><small>Re-Finalized on ".wv_formatDate($v_finalize_dts,0,1)."</small>";
			}
		}
	}	
	
	$arr_ret_tmp["strFDate"] = $strFDate;
	
	//
	$strClick ="";
	if( ($elem_per_vo != "1") && ($finalize_flag != 1) && (in_array($user_type, $GLOBALS['arrValidCNPhy'])  || $user_type=="13") && ($elem_physicianId == $providerId)){		
		$strClick = ($is_user_sign==1) ? 1 : "" ; //"class=\"clickable\" onclick=\"getPhySign_db($num);\" " : "";
	}
	$arr_ret_tmp["strClickDbSig"] = $strClick;
	
	/*
	$str.="<div id=\"sign_phy".$num."\" class=\"divsign\" >
		<label id=\"lbl_phy_sig".$num."\"  ".$strClick." >Signature</label>";
	*/
	//echo substr(trim($strpixls), 0, 6);
	//die;
	if($blEnableHTMLSignature == false){
	//if(substr(trim($strpixls), 0, 6) == "0-0-0:"){
	if(strpos(trim($strpixls), "0-0-0:") !== false){
		$strpixls = trim($strpixls);	
	}else{
		$strpixls = "";
	}

	//Check for link --
	if($finalize_flag != 1||($finalize_flag == 1 && ((empty($strpixls) || $strpixls=="0-0-0:;") && empty($elem_sign_path)) && $elem_physicianId == $providerId)){ 
		$link = 1; //"onclick=\"getAssessmentSign(".$num.")\"";
	}else{
		$link = "";
	}
	$arr_ret_tmp["strClickSigPopUp"] = $link;

	//$str.="<span id=\"td_signature_applet".$num."\" class=\"appletCon\" ".$link." > ";

	if(!empty($elem_sign_path)){
		
		$oSaveFile = new SaveFile($patient_id);
		$elem_sign_path_w = $oSaveFile->getFilePath($elem_sign_path, "w");
		
		$arr_ret_tmp["img_sign_path"] = $elem_sign_path_w;

		//$str.= "<img src=\"".checkUrl4Remote($GLOBALS['rootdir']."/main/uploaddir".$elem_sign_path)."\" alt=\"sign\" width=\"150\" height=\"30\">";
	}

	//$str.="</span>";

	//Show Add More Signature Button ----------------
	$str_btn_add="";
	if($elem_per_vo != "1" && ($finalize_flag != 1 || $isReviewable)){	
		if(($user_type==11 || $user_type==19 || $user_type==13) && empty($strPhyRight)){
			//$str_btn_add.="<label id=\"lbl_moreSign\"  title=\"Add More Signatures\" onclick=\"addMoreSigns(2)\" ><img src=\"img/add_icon.png\" alt=\"add\"/></label>";
			$str_btn_add=2;
		}else 	
		if(empty($strPhyLeft)){
			//$str_btn_add.="<label id=\"lbl_moreSign\"  title=\"Add More Signatures\" onclick=\"addMoreSigns(1)\" ><img src=\"img/add_icon.png\" alt=\"add\"/></label>";
			$str_btn_add=1;
		}
	}
	$arr_ret_tmp["add_btn"] = $str_btn_add;
	//-------------

	//
	//if($finalize_flag != 1 && $elem_physicianId == $_SESSION["authUserID"]){$tmp="inline"; }else{$tmp="none"; }
	$str_btn_del="";
	if($finalize_flag != 1 && ($user_type==1||$elem_physicianId == $providerId)){$str_btn_del="inline"; }else{$str_btn_del="none"; }
	
	$arr_ret_tmp["del_btn"] = $str_btn_del;
	
	//$str_btn_del.= " <span onclick=\"getAssessmentSign(".$num.",'3')\" title=\"Delete\" style=\"display:".$tmp."\"><img src=\"img/closerd.png\" alt=\"del\" /></span>";

	//$str.= $strFDate;//show Finalize date
	}
	elseif($blEnableHTMLSignature == true){
	$patId = $patient_id;
	$patFormId = $form_id;

	//$str.="<!--- This is where we draw our signature. --->";
	//$str.="<span id=\"td_signature_applet\">";
	//$str.="<span id=\"physicianSig\" style=\"width:150px; height:30px; background-color:#FFF;\">";

	if($strpixls != "" && strpos(trim($strpixls), "0-0-0:") === false){
	    //$str.= "<img src=\"data:image/jpeg;base64,".base64_encode($strpixls)."\" style=\"width:150px; height:30px; border: 1px solid #F60;\"/>";
	    $arr_ret_tmp["img_sign_path"] = "data:image/jpeg;base64,".base64_encode($strpixls);
	}
	$strpixls = "";
	$arr_ret_tmp["ipad_sign"] = 1;
	 
	/*
	$str.="</span>
	<span>
	<img style=\"cursor:pointer; margin-top:5px;\" src=\"../../images/pen.png\" id=\"physicianSigPen\" name=\"physicianSigPen\" onclick=\"OnSignIpadPhy('".$patId."','".$patFormId."','phy')\">
	</span>
	</span>";
	*/
	
	}

	//<!-- onclick=\"click2MoveSign(".$num.")\" -->
	
	$arr_ret_tmp["elem_physicianIdName"] = $elem_physicianName;
	$arr_ret_tmp["elem_physicianId"] = $elem_physicianId;
	$arr_ret_tmp["elem_is_user_sign"] = $is_user_sign;
	$arr_ret_tmp["elem_is_phy_sign"] = $is_phy_sign;
	$arr_ret_tmp["elem_sign_path"] = $elem_sign_path;
	
	/*
	$str.="
	<div class=\"form-group form-group-sm\" >
	<label class=\"signernm col-sm-4\" onclick=\"click2MoveSign(".$num.")\" for=\"elem_physicianIdName".$num."\" >Signer Name</label>
	<div class=\"col-sm-8\">
	<input type=\"text\" id=\"elem_physicianIdName".$num."\" name=\"elem_physicianIdName".$num."\" value=\"".$elem_physicianName."\" readonly=\"readonly\" class=\"form-control\" >
	</div>
	</div>
	<input type=\"hidden\" name=\"elem_physicianId".$num."\" value=\"".$elem_physicianId."\" onChange=\"setPhysician(this)\">

	<input type=\"hidden\" name=\"elem_signCoords".$num."\" id=\"elem_signCoords".$num."\" value=\"".trim($strpixls)."\" onChange=\"setThisChangeStatus(this)\">
	<input type=\"hidden\" name=\"hdSignCoordsOriginal".$num."\" value=\"".trim($strpixls)."\">
	<input type=\"hidden\" name=\"elem_is_user_sign".$num."\" value=\"".$is_user_sign."\">
	<input type=\"hidden\" name=\"elem_is_phy_sign".$num."\" value=\"".$is_phy_sign."\">
	<input type=\"hidden\" name=\"elem_sign_path".$num."\" value=\"".$elem_sign_path."\">
	";
	*/

	//add
	//if(!empty($str_btn_add)){ $str.=$str_btn_add;  }
	//del
	// if(!empty($str_btn_del)){ $str.=$str_btn_del;  }

	//add label
	if($blEnableHTMLSignature == false){/*$str.= $strFDate;*/}//show Finalize date

	/*
	//Provider Onclick change option
	if(($finalize_flag != 1) && ($user_type == 1 || $user_type == 12) && ($elem_physicianId != $_SESSION["authUserID"]) && (!empty($elem_cosignerId) && $elem_cosignerId != $_SESSION["authUserID"])) {
		echo "<br/><a href=\"javascript:void(0);\" onclick=\"setUserName('elem_physicianId');\" >Change Physician</a>";
	}
	*/

	/*$str.="
	</div>
	";*/
	//<!-- Signature Physician -->

	if($user_type==11 || $user_type==19 || $user_type==13){
		//$strPhyRight.=$str;
		$arPhyRight[]=$arr_ret_tmp;
	}else{
		$arPhyLeft[]=$arr_ret_tmp;
	}

	$cntr++;
	//Test-----------------------		
			
			}
			
			$i++;
		}; //end while

	/*
	//concat
	$str="";
	$str="<div class=\"phy_left  col-lg-12 col-md-12 col-sm-12\">".$strPhyLeft."</div>".
		"<div class=\"phy_right  col-lg-12 col-md-12 col-sm-12\">".$strPhyRight."</div>";
	*/	
		
		
		$ar_merge = array_merge($arPhyLeft, $arPhyRight);
		
		//*:23-01-2013
		//Logged in should be Owner--
		if(!empty($curPhyUsrType) && $curPhyUsrType!="13" ){
			$tmp = $arrPhyW[$curPhyUsrType];
			if(empty($tmp)){ $tmp = $arrPhyW["1"]; }
			
			if($tmpPhyMw <= $tmp){
				$elem_physicianId_1st = $curPhyId;
			}
		}	
		//*/---
		
		return array($ar_merge,$cntr+1,$elem_physicianId_1st);		
	}
	
	function getSignInfo(){
	
		global $user_type, $finalize_flag, $providerId,
			$user_name, $elem_masterProviderId,$elem_curPhysicianId,$elem_physicianId, $elem_signatureNum;
	
		$arrMultiSigns = $this->getChartSigns();
		
		//if Logged in user not in signature array, then add it
		if( (in_array($user_type, $GLOBALS['arrValidCNPhy']) || $user_type==13) && $finalize_flag != 1 ){
			if((!is_array($arrMultiSigns["pro_id"]) || (is_array($arrMultiSigns["pro_id"]) && count($arrMultiSigns["pro_id"])<=0)||!in_array($providerId,$arrMultiSigns["pro_id"]))){		
				$arrMultiSigns["pro_id"][]=$providerId;
				$arrMultiSigns["pro_name"][]=$user_name;
				$arrMultiSigns["user_type"][]=$user_type;	
			}	
		}
		
		//Check if chart note is finalized and Signature array is empty and Provider Id of masterTable is not empty, then use it
		if($finalize_flag == 1 && count($arrMultiSigns["pro_id"])<=0 && !empty($elem_masterProviderId)){	
			$otmpUser = new User($elem_masterProviderId);
			$arrMultiSigns["pro_id"][]=$elem_masterProviderId;
			$arrMultiSigns["pro_name"][]=$otmpUser->getName(4);
			$arrMultiSigns["user_type"][]=$otmpUser->getUType();
		}
		
		//Get Signature Pads
		list($arrSign,$elem_signatureNum,$elem_physicianId_tmp) = $this->getSignPads($arrMultiSigns,$providerId);
		
		//For Backward compatibiluty, : This is used as current Physician Id of the chart note
		if(!empty($elem_physicianId_tmp)){
			$elem_curPhysicianId=$elem_physicianId=$elem_physicianId_tmp;
		}else if(!empty($elem_masterProviderId)){ //else master Provider Id is Owner
			$elem_curPhysicianId=$elem_physicianId=$elem_masterProviderId;
		}		
	
		return $arrSign;
	
	}
	
	function captureSign($ret=0){
		$patient_id = $this->pid;			
		$form_id = $this->fid;
		
		$arr = array();
		$arr["flgSaveDef"]="1";
		//if(!empty($_POST["elem_cosignerId"])) {
		//	$coId = $_POST["elem_cosignerId"];
		//}else {
			$phyId_str = $_POST["elem_physicianId"];
		//}
		$num_str = $_POST["num"];
		
		//
		$ar_num = explode(",", $num_str);
		$ar_phyId = explode(",", $phyId_str);
		$len_ar_num = count($ar_num);
		
		$oSaveFile = new SaveFile($patient_id);
		
		for($i=0;$i<$len_ar_num;$i++){
			$phyId = $ar_phyId[$i];
			$num =  $ar_num[$i];
			
			if(!empty($phyId) && !empty($num)){
				$sql = "SELECT sign, sign_path, user_type FROM users WHERE id = '".$phyId."' ";
				$row = sqlQuery($sql);
				if($row != false){
					$strpixls = trim($row["sign"]);
					$str_sign_path = trim($row["sign_path"]);
					$usr_type = $row["user_type"];
					
					$chk1=$chk2=0;
					//if((!empty($strpixls) && $strpixls!="0-0-0:;")){  $chk1=1; }
					$oSaveFile2 = new SaveFile($phyId,1);
					$flg_file_exists = $oSaveFile2->isFileExists($str_sign_path);
					
					if((!empty($str_sign_path) && strpos($str_sign_path,"UserId") !== false && $flg_file_exists )){  $chk2=1; }	
					
					if($usr_type!="13"){$arr["flgSaveDef"]= "0";} //if not scribe
					
					if($chk1==1||$chk2==1){
						$tmpData = array();
						$tmpData["str"] = "";
						//COMPATIBILITY
						
						//-------------						
						//Make Image 			
						$img_nm = "sig".$num."_".time()."_".$form_id.".jpg";			
						//$tmp_sign_path1=$tmp_sign_path.$img_nm;						
						//global $gdFilename;
						if($chk2==1){
							$file_pntr = $oSaveFile->copySign($str_sign_path,1,$img_nm);
							if(!empty($file_pntr)){}else{$form_sign_path=$img_nm="";}
							
							//if(copy($GLOBALS['incdir']."/main/uploaddir".$str_sign_path, $tmp_sign_path1)){ }else{ $form_sign_path=$img_nm=""; }
						}else{						
							//drawOnImage_new($strpixls,"",$tmp_sign_path1);
						}
						//-------------	
						
						$http_file_pntr = $oSaveFile->getFilePath($file_pntr,'w');
						
						$tmpData["str"] = (!empty($http_file_pntr)) ?  "<img src=\"".$http_file_pntr."\" alt=\"sign\" width=\"225\" height=\"45\" >" : "" ;					
						$tmpData["strpixls"]=$strpixls;
						$tmpData["strsignpath"]=$file_pntr;
						$tmpData["num"] = $num;
						$arr["data"][$i] = $tmpData;	
					}
				}
			}
			/*
			else if(!empty($coId)){
				$sql = "SELECT sign FROM users WHERE id = '".$coId."' ";
				$row = sqlQuery($sql);
				if($row != false){
					$strpixls = $row["sign"];
					if(!empty($strpixls)){
						$arr["str"] = "";	
						//COMPATIBILITY
						//$arr["str"] .= "<img src=\"".drawOnImage_new($strpixls)."\" alt=\"sign\">";
						///$arr["str"] .= "<img src=\"".$GLOBALS['rootdir']."/main/mainGd_pixels.php?pixels=".$strpixls."\" alt=\"sign\">";
						
						//-------------
						//Make Image 			
						$img_nm = "/cosig_".time()."_".$form_id.".jpg";			
						$tmp_sign_path1=$tmp_sign_path.$img_nm;						
						//global $gdFilename;
						drawOnImage_new($strpixls,"",$tmp_sign_path1);
						//-------------
						$arr["str"] .= "<img src=\"".$GLOBALS['rootdir']."/main/uploaddir".$form_sign_path.$img_nm."\" alt=\"sign\" width=\"225\" height=\"45\">";
												
						$arr["strpixls"]=$strpixls;
						$arr["strsignpath"]=$form_sign_path.$img_nm;
					}
				}
			}
			*/
		}//end for
		if($ret==1){
			return $arr;
		}else{
			echo json_encode($arr);
		}	
	}
	
	function saveWvSign(){
		$strpixls=$_POST["strpixls"];
		$form_id=$this->fid;
		$signType =  $_POST["signType"];
		$patient_id=$this->pid;
		$save = $_POST["final_flg"];
		$proId = $_POST["proId"];
		$sData = $_POST["sData"];
		$sImg=$_POST["sImg"];
		
		//--
		if(empty($strpixls) && !empty($sData)){
			$strpixls = $sData;
		}
		//--			
		
		///
		$oSaveFile = new SaveFile($patient_id);	
		$signSavePath = $oSaveFile->createSignImages($strpixls,$form_id,$signType);
		if(!empty($signSavePath)){
			//and save sign Path WHEN chart is finalized
			if($save==1 && !empty($proId)){
				/*
				if($signType==2){
					$saveField = " cosign_path='".mysql_real_escape_string($signSavePath)."' ";
				}else{
					$saveField = " sign_path='".mysql_real_escape_string($signSavePath)."' ";
				}				
				
				$sql="UPDATE chart_assessment_plans SET".
					$saveField.
					"WHERE patient_id='".$patient_id."' AND form_id='".$form_id."' ";
				sqlQuery($sql);
				*/					
				
				$sql="UPDATE chart_signatures SET ".
					" sign_path='".sqlEscStr($signSavePath)."',
					  sign_coords ='".sqlEscStr($strpixls)."',
					  sign_coords_dateTime = '".date("Y-m-d H:i:s")."'	
					".
					"WHERE pro_id='".$proId."' AND form_id='".$form_id."' ";
				sqlQuery($sql);
				
			}
			
			$ar=array();
			$ar["src"] = $oSaveFile->getFilePath($signSavePath,"w");
			$ar["sign_path"] = $signSavePath;
			
			echo json_encode($ar);				
		}else{
			$ar=array();
			$ar["src"] = "";
			echo json_encode($ar);			
		}
	}
	
	function getFirstChartSign($docid=0){
		$ret="";
		$oSaveFile = new SaveFile();
		$ar = $this->getChartSigns();
		if(count($ar["sign_path"])>0){
			foreach($ar["sign_path"] as $k => $v){
				if(!empty($v) && $ar["user_type"][$k]!="13"){ //do show scribe signature into printing of rx					
					$path = $oSaveFile->getFilePath($v,"w");
					if(!empty($path)){
						if((!empty($docid) && $docid==$ar["pro_id"][$k])){
							$ret=$path; 
						}else if(empty($docid)){
							if(empty($ret)){$ret=$path; }
						}
					}
				}
			}
		}
		return $ret;
	}	
	
	function delSignHandler(){
		$proid=$_GET["proid"];
		$fid=$this->fid; //$_GET["fid"];		
		if(!empty($proid) && !empty($fid)){
			$sql = "DELETE FROM chart_signatures WHERE form_id = '".$fid."' AND pro_id  = '".$proid."' ";			
			$ret=sqlQuery($sql);		
		}
		echo 0;
	}

	function addMoreSign(){
		$num=$_GET["num"];
		if(empty($num)){$num=2;}
		
		$nm = "elem_physicianId".$num;
		$ev = " onchange=\"checkDupPhy(this)\" ";
		
		$oUsr = new User();
		$str_sel_phy = $oUsr->getUsersDropDown($nm, $ev);	
		
		$str="";			
		
		//<!-- Signature Physician -->
		$str.="				
			<div id=\"sign_phy".$num."\" class=\"row divsign\">
				<div class=\"col-md-2 col-sm-2  col-sm-2-sign\">
					<label id=\"lbl_phy_sig".$num."\" onclick=\"getPhySign_db(".$num.");\"  >Signature</label>
				</div>
				<div class=\"col-md-4 col-sm-4  col-sm-4-sign\">
					<span id=\"td_signature_applet".$num."\" class=\"appletCon\" onclick=\"getAssessmentSign(".$num.")\"></span>
				</div>
				<div class=\"col-md-2 col-sm-2\">
					<label onclick=\"click2MoveSign(".$num.")\">Sign. Name</label>
				</div>
				<div class=\"col-md-3 col-sm-3\">".
					$str_sel_phy.					
				"</div>
				<div class=\"col-md-1 col-sm-1\">
					<!--<img src=\"".$GLOBALS['webroot']."/library/images/closerd.png\" alt=\"Delete\" onclick=\"getAssessmentSign(".$num.",'3')\" >-->
					<span class=\"glyphicon glyphicon-remove\" alt=\"Delete\" onclick=\"getAssessmentSign(".$num.",'3')\" >
					<!--<img src=\"".$GLOBALS['webroot']."/library/images/add_icon.png\" alt=\"Add\" onclick=\"addMoreSigns(".$num.")\" />-->
					<input type=\"hidden\" name=\"elem_signCoords".$num."\" id=\"elem_signCoords".$num."\" value=\"\" onChange=\"setThisChangeStatus(this)\">
					<input type=\"hidden\" name=\"hdSignCoordsOriginal".$num."\" value=\"\">
					<input type=\"hidden\" name=\"elem_sign_path".$num."\" value=\"\">
					<input type=\"hidden\" name=\"elem_physicianIdName".$num."\" value=\"\" >
					<input type=\"hidden\" name=\"elem_is_user_sign".$num."\" value=\"\">
					<input type=\"hidden\" name=\"elem_is_phy_sign".$num."\" value=\"\">
				</div>			
			</div>				
		";
		//<!-- Signature Physician -->
		echo $str;	
	}
	
	function getAttestationDiv(){
		global $elem_masterProviderId, $elem_curPatientName, $elem_resiHxReviewd, $titleRxHxRvd,
		$isReviewable, $finalize_flag, $elem_per_vo;
		$form_id = $this->fid;
		$flgAtt=0;
		
		//make array of pt name
		$ar_pt_nm = explode("-", strip_tags($elem_curPatientName));
		$elem_curPatientName_tmp = trim($ar_pt_nm[0]);
		$pt_nm = preg_replace('/[^`~!<>@$?a-zA-Z0-9_{}:; ,"#%\[\]\.\(\)%&-\/\\r\\n\\\\]/s','',$elem_curPatientName_tmp);
		
		$str_scrb="";
		$str_attend_scrb="";
		$id_scribe_svd="";
		$atsd_msg="";
		$sql="SELECT c2.id, c2.user_type, c1.attsd, c1.atsd_msg FROM chart_signatures c1
			LEFT JOIN users c2 ON c1.pro_id = c2.id
			LEFT JOIN chart_usr_roles c3 ON c1.pro_id = c3.uid
			where c1.form_id = '".$form_id."' AND (c2.user_type='13' || c3.role_type='13') Order by atsd_msg DESC ";
		$row=sqlQuery($sql);
		if($row!=false){
			$id_scribe_svd=$row["id"];
			$atsd_msg=$row["atsd_msg"];
		}
		$logged_user_type = (isset($_SESSION["user_role"]) && !empty($_SESSION["user_role"])) ? $_SESSION["user_role"] : $_SESSION["logged_user_type"];
		if(!empty($logged_user_type) && $logged_user_type==13){$id_scribe_svd=$_SESSION['authId'];}
		
		if(!empty($id_scribe_svd)){
			// Scribe --
			$ousr = new User($id_scribe_svd);
			$scrb_nm = $ousr->getName(3);
			if(empty($atsd_msg)){
				$msg="";
				if($id_scribe_svd==$_SESSION['authId']){
					
					if(!empty($elem_masterProviderId)){ //last saved physician id
					$ousr = new User($elem_masterProviderId);
					$phy_nm = $ousr->getName(3);
					$scrb_dt = date("m-d-Y");
					
					$msg = "I <font color=\"red\">".$scrb_nm."</font> personally scribed the services dictated to me by <font color=\"red\">".$phy_nm."</font> in this documentation on this <font color=\"red\">".$scrb_dt."</font> for <font color=\"red\">".$pt_nm."</font> .";
					}
				}
				$attested="";
			}else{
				$msg = $atsd_msg;
				$attested="1";
			}
			
			$attested_css = ($attested=="1") ? " btn-success " : " btn-warning ";
			
			$flgAtt=1;
			$str_scrb="<input type=\"button\" class=\"btn ".$attested_css." btn-sm\"  id=\"btn_attest_scribe\" name=\"btn_attest_scribe\" value=\"Scribe\" onclick=\"chart_set_attestation('1')\" data-msg='".htmlentities($msg,ENT_QUOTES)."' data-attested=\"".$attested."\"  >
				";
			
			//--
			// Attending Scribe ( if scribe has worked and now physician has logged in  ) --
			//
			$id_atnd_scribe_svd="";
			$msg_atnd_scrib="";
			$attested_atnd_scrib="";
			$show_atnd_scrib_btn="";
			//get attend scribe saved in db
			$sql="SELECT c2.id, c2.user_type, c1.attsd, c1.atsd_msg FROM chart_signatures c1
				LEFT JOIN users c2 ON c1.pro_id = c2.id
				where form_id = '".$form_id."' AND c2.user_type IN (".implode(",",$GLOBALS['arrValidCNPhy']).")  ORDER BY attsd DESC ";
			$row=sqlQuery($sql);
			if($row!=false){
				if(!empty($row["attsd"])&&!empty($row["atsd_msg"])) { //AND c1.attsd='1' AND atsd_msg!=''
				$id_atnd_scribe_svd=$row["id"];
				$msg_atnd_scrib=$row["atsd_msg"];
				$attested_atnd_scrib=$row["attsd"];
				}
				$show_atnd_scrib_btn="1";
			}
			//if not saved, get newly logged in physician
			if(empty($id_atnd_scribe_svd)){
				if(in_array($_SESSION["logged_user_type"], $GLOBALS['arrValidCNPhy'])){
					$id_atnd_scribe_svd = $_SESSION['authId'];
					$ousr = new User($id_atnd_scribe_svd);					
					$nm_atnd_provdr =  $ousr->getName(3);
					$dt_atsd_atnd_scrib = date("m-d-Y");
					
					$msg_atnd_scrib = "I <font color=\"red\">".$nm_atnd_provdr."</font> personally performed the services described in this documentation on <font color=\"red\">".$dt_atsd_atnd_scrib."</font> for <font color=\"red\">".$pt_nm."</font> as scribed by <font color=\"red\">".$scrb_nm."</font> in my presence. I have reviewed and verified that all the information is accurate and true."; 
					$show_atnd_scrib_btn="1";
				}
			}		
			//if found one, show button
			if(!empty($show_atnd_scrib_btn)){
				$str_attend_scrb = "";
				$attested_atndscrb_css="";
				
				$attested_atndscrb_css = ($attested_atnd_scrib=="1") ? " btn-success " : " btn-warning ";
				$str_attend_scrb="<input type=\"button\" class=\"btn ".$attested_atndscrb_css." btn-sm\"  id=\"btn_attest_attend_scribe\" name=\"btn_attest_attend_scribe\" value=\"Attending - Scribe\" onclick=\"chart_set_attestation('2')\" data-msg='".htmlentities($msg_atnd_scrib,ENT_QUOTES)."' data-attested=\"".$attested_atnd_scrib."\"  >
					";
				
			}
			//--
			
		}
		
		//Teaching --
		if($GLOBALS["showResHxRevwd"] == "1"){
		$str_teach="";
		$msg_teach="";
		$attested_teach="";
		$attested_teach=$elem_resiHxReviewd;
		$sql="SELECT c2.id, c2.user_type, c1.attsd, c1.atsd_msg FROM chart_signatures c1
			LEFT JOIN users c2 ON c1.pro_id = c2.id
			where form_id = '".$form_id."' AND c2.user_type IN ('11','19') ";
		$row=sqlQuery($sql);
		if($row!=false){
			
			$flgAtt=1;
			if($elem_resiHxReviewd=="1" || $_SESSION["logged_user_type"]==1 || $_SESSION["logged_user_type"]==10 || $_SESSION["logged_user_type"]==12){
				$msg_teach=$titleRxHxRvd;
			}
			
			$attested_teach_css = ($attested_teach=="1") ? " btn-success " : " btn-warning ";
			$str_teach="<input type=\"button\" class=\"btn ".$attested_teach_css." btn-sm\"  id=\"btn_attest_teach\" name=\"btn_attest_teach\" value=\"Teaching\" onclick=\"chart_set_attestation('3')\" data-msg='".htmlentities($msg_teach,ENT_QUOTES)."' data-attested=\"".$attested_teach."\"  >
					";
		}
		}
		//--
		
		//Change Attending --
		$str_change_attending="";
		if(empty($elem_per_vo) && (!$finalize_flag || $isReviewable)){ //not view only and not finalize and is reviewable
		
			$str_op="";
			$sql="SELECT c2.id, c2.user_type, c2.fname,c2.lname,c2.mname  FROM chart_signatures c1
				LEFT JOIN users c2 ON c1.pro_id = c2.id
				where form_id = '".$form_id."' AND c2.user_type NOT in (3, 13, 11, 19) ";
			$rez=sqlStatement($sql);
			
			for($i=0;$row=sqlFetchArray($rez);$i++){
				$m = (!empty($row["mname"])) ? $row["mname"]." " : ""; 
				$nm = $row["fname"]." ".$m.$row["lname"];
				$str_op.="<option value=\"".$row["id"]."\">".$nm."</option>";
			}
			
			//if Follow physician, show change dropdown
			if(empty($str_op) && !empty($_SESSION['res_fellow_sess'])){
				if(!empty($atsd_msg)){ // if msg is saved, only then it will come
					$id_fp = $_SESSION['res_fellow_sess'];
					$ousr = new User($id_fp);					
					$nm_fp = $ousr->getName(3);
					$str_op.="<option value=\"".$id_fp."\">".$nm_fp."</option>";
				}
			}
			
			if(!empty($str_op)){		
				$str_change_attending = "<select name=\"el_change_attending\" onchange=\"change_attending_phy(this)\" class=\"form-control\" title=\"Change Attending\" data-toggle=\"tooltip\"><option value=\"\">Change Attending:</option>".$str_op."</select>";
			}
		}
		
		$ret="";
		if(!empty($flgAtt)){
			$ret = "<div class=\"col-sm-2\"><h2>Attestations:</h2> </div>".
				 "<div class=\"col-sm-2\">".$str_teach."</div>".
				 "<div class=\"col-sm-3\">".$str_attend_scrb."</div>".
				 "<div class=\"col-sm-2\">".$str_scrb."</div>".
				 "<div class=\"col-sm-3\">".$str_change_attending."</div>";
		}
		return $ret;
	}
	
	function setAttestation(){
		//print_r($_POST);
		$proId = $_POST["proId"];
		$msg = $_POST["msg"];
		$formId = $this->fid; //$_POST["formId"];
		$indx = $_POST["indx"];
		$patient_id = $this->pid;
		$new_pro_Id = $_POST["new_pro_Id"];
		
		if(empty($this->pid)){return;} //return		
		if(!empty($new_pro_Id)){ //Reset Attending
		
			//User Name		
			$ousr = new User($new_pro_Id);	
			$user_nm = $ousr->getName(3);
			$dt = date("m-d-Y");
			$arret=array();
			$sql = "SELECT c1.id, c1.atsd_msg, c2.user_type FROM chart_signatures c1 
					LEFT JOIN users c2 ON c2.id = c1.pro_id
					WHERE c1.attsd='1' AND c1.form_id='".$formId."' ";
			$rez = sqlStatement($sql);
			for($i=0;$row=sqlFetchArray($rez);$i++){
				$id = $row["id"];
				$msg = $row["atsd_msg"];
				if(!empty($msg)){
					$msg=preg_replace('/([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})/', $dt, $msg); //date change
					if($row["user_type"]=="13"){
						//scribe
						$ptrn = '/to me by \<font color\=\"red\"\>(.*?)\<\/font\>/';
						$rep = 'to me by <font color="red">'.$user_nm.'</font>';
						$btn_id = "btn_attest_scribe";
					}else{
						//attending
						$ptrn = '/\<font color\=\"red\"\>(.*?)\<\/font\> personally performed/';
						$rep = '<font color="red">'.$user_nm.'</font> personally performed';
						$btn_id = "btn_attest_attend_scribe";
					}
					//
					$msg=preg_replace($ptrn, $rep, $msg); //msg change
					$arret[$btn_id]=$msg;	
					//
					$sql = "UPDATE chart_signatures SET atsd_msg = '".imw_real_escape_string($msg)."' WHERE id = '".$id."' ";
					$r = sqlQuery($sql);
				}
			}
			echo json_encode($arret);
			exit();
		}
		
		if($indx=="3"){
			if(!empty($patient_id) && !empty($formId)){
			$sql=" UPDATE chart_assessment_plans SET resiHxReviewd='1' WHERE form_id = '".$formId."' AND patient_id = '".$patient_id."' ";
			$row = sqlQuery($sql);
			}
		}else{
			if(!empty($formId) && !empty($proId) ){
				
				$sql = "SELECT id FROM chart_signatures WHERE pro_id='".$proId."' AND form_id='".$formId."' ";
				$row = sqlQuery($sql);
				if($row!=false){
				
					$sql = "UPDATE chart_signatures SET attsd='1', atsd_msg='".imw_real_escape_string($msg)."' WHERE pro_id='".$proId."' AND form_id='".$formId."'  ";
					$row=sqlQuery($sql);	
				
				}else{
					$sql = "INSERT INTO chart_signatures SET form_id='".$formId."', pro_id='".$proId."', attsd='1', atsd_msg='".imw_real_escape_string($msg)."', sign_type='10'  ";
					$row=sqlQuery($sql);
				}
			}
		}	
	}
	
	function isUsrSignExists($uid){	
		$hasSign = 0;
		$num = 0;
		$last_num = 0;
		$edid = 0;	
		if(!empty($uid)){
		//check if usr sign in this chart
		$sql = "SELECT sign_path, pro_id, sign_type, id  FROM chart_signatures where form_id='".$this->fid."' ORDER BY sign_type, id ";
		$rez = sqlStatement($sql);
		for($i=1; $row=sqlFetchArray($rez); $i++){
			$last_num = $row["sign_type"];
			if($uid == $row["pro_id"]){
				$num = $row["sign_type"];
				$sign = $row["sign_path"];
				$edid= $row["id"];
				if(!empty($sign)){					
					$osavefile = new SaveFile();
					$hasSign = $osavefile->isFileExists($sign);
				}
			}
		}		
		if(empty($num) && !empty($last_num)){
			$num = $last_num+1;
		}
		}
		if(empty($num)){
		$num = $num + 1;
		}
		return array($hasSign, $num, $edid);
	}
	//This function checks existance of user signature and add them if they do not exists and exists in user account
	function setUsrSign($uid){	
		if(!empty($uid)){					 
			list($hasSign, $num, $editId) = $this->isUsrSignExists($uid);
			if(empty($hasSign)){
				$_POST["elem_physicianId"]=$uid;
				$_POST["num"]=$num;
				$sign_data_inf = $this->captureSign(1);
				$sign_data = (isset($sign_data_inf["data"])) ? $sign_data_inf["data"] : array();
					
				if(count($sign_data)>0){
					$var_signCoods = $sign_data[0]["strpixls"];
					$var_signPath = $sign_data[0]["strsignpath"];
					$var_signType = $sign_data[0]["num"];
					$sign_coords_dateTime_tmp = wv_dt("now");
					
					if(!empty($var_signPath)){ $var_signCoods=""; } //path or signcoords
					
					if(!empty($editId)){
						$sql = "UPDATE chart_signatures SET ".
						"sign_coords='".sqlEscStr($var_signCoods)."',
						sign_coords_dateTime='".$sign_coords_dateTime_tmp."',
						sign_path='".$var_signPath."',
						sign_type='".$var_signType."'".
						"WHERE form_id='".$this->fid."' AND pro_id='".$uid."' AND id='".$editId."' ";	
						$r=sqlQuery($sql);	
					}else{
						$sql="INSERT INTO chart_signatures 
								(id, form_id, pro_id,sign_coords,sign_coords_dateTime,sign_path,sign_type) ".
							"VALUES 
								(NULL, '".$this->fid."', '".$uid."', '".sqlEscStr($var_signCoods)."', 
								'".$sign_coords_dateTime_tmp."', '".$var_signPath."' , '".$var_signType."'  )";
						$r=sqlQuery($sql);
					}
				}
			}
		}
	}
}
?>