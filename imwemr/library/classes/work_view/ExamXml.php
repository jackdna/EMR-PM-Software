<?php
class ExamXml{

	public function __construct(){

	}

	function showArrVals($arr)
	{
		if(is_array($arr))
		{
			foreach($arr as $var => $val)
			{
				echo $var .":::". $val ."<BR>";
				if(is_array($val))
				{
					$this->showArrVals($val);
				}
			}
		}
	}

	function getXmlMenuArray($strMenuXml)
	{
		$parser = xml_parser_create();
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parse_into_struct($parser, $strMenuXml, $arrDirtyMenuVals, $index);
		xml_parser_free($parser);

		return $arrDirtyMenuVals;
	}

	function getMenuInputValueArray($arrDirtyMenuVals)
	{
		//echo "hello world<br>";
		// Create Menu and Input And Values
		$arrMenusInputsAndVals = array();
		if(count($arrDirtyMenuVals) > 0){
			foreach($arrDirtyMenuVals as $var => $val)
			{
				if($val["type"] == "complete"){

					echo $var." <br> ";
					print_r($val);
					echo "<br><br>";
				}
			}
		}
	}

	function getMenuXmlStringFromFile($menuFilePath)
	{
		$menuFile = $menuFilePath;
		//Reading File
		//$fileContents = file_get_contents($menuFile);
		$fileContents = file($menuFile);

		$strMenuXml = "";
		foreach($fileContents as $var => $val)
		{
			$strMenuXml .= trim($val);

		}

		return $strMenuXml;
	}

	function getAttributeString($arr)
	{
		$strAtt = "";
		foreach($arr as $var => $val)
		{
			$thisVar = strtolower($var);
			$thisVar = ($thisVar == "issingleselect") ? "isSingleSelect" : $thisVar;
			$strAtt .= $thisVar."=\"".$val."\" ";
		}
		return $strAtt;
	}

	function xmlRefineValue($str, $reverse=""){
		if(!empty($reverse)){
		$a1 = array("'","\"",">","<","&","'","\"",">","<","&");
		$a2 = array("&amp;apos;","&amp;quot;","&amp;gt;","&amp;lt;","&amp;amp;", "&apos;","&quot;","&gt;","&lt;","&amp;");
		return str_replace($a2, $a1, $str);
		}else{
		$a1 = array("'","\"",">","<","&");
		$a2 = array("&apos;","&quot;","&gt;","&lt;","&amp;");
		return str_replace($a1,$a2, $str);
		}
	}

	function processTag($arr,$menuName,$arrMenusInputsAndVals)
	{
		$tag = strtolower($arr["tag"]);
		$type = $arr["type"];
		$level = $arr["level"];
		$attributes = $arr["attributes"];
		$retStr = "";
		$elemName = $attributes["elem_name"];
		if(!empty($elemName)){
			$value = $this->xmlRefineValue($_POST[$elemName]);
		}

		if($type == "open")
		{
			$strAtt = (is_array($attributes)) ? $this->getAttributeString($attributes) : "";
			$retStr .= "<$tag $strAtt>";
		}
		else if($type == "close")
		{
			$retStr .= "</$tag>";
		}
		else if($type == "complete")
		{
			if(is_array($attributes))
			{
				unset($attributes["DATA"]);
				$strAtt = (is_array($attributes)) ? $this->getAttributeString($attributes) : "";
			}
			$retStr .= "<$tag $data $strAtt>$value</$tag>";
		}
		return $retStr;
	}

	function getMenuXmlStringRecreated($menuName,$arrDirtyMenuVals,$arrMenusInputsAndVals)
	{
		$newXml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" ?>";
		//$menuLevel = $menuName."-s1";
		$menuLevel = $menuName;
		foreach($arrDirtyMenuVals as $var => $val)
		{
			$newXml .= $this->processTag($val,$menuName,$arrMenusInputsAndVals);
		}
		return $newXml;
	}

	function getXmlValuesExtracted($arrDirtyMenuVals)
	{
		$tmp1 = array();
		$retV=false;
		if(count($arrDirtyMenuVals) > 0){
			foreach($arrDirtyMenuVals as $key => $val){
				$tmp = $val["attributes"]["elem_name"];
				if(isset($tmp) && !empty($tmp)){
					global $$tmp;
					$$tmp = $val["value"];
					$tmp1[$tmp] = $val["value"];
				}
			}
			$retV=true;
		}
		return array($retV, $tmp1);
	}

	function newXmlString($menuName,$strMenu,$menuFilePath){
		$strMenuXml = $this->getMenuXmlStringFromFile($menuFilePath);
		$arrDirtyMenuVals = $this->getXmlMenuArray($strMenuXml);
		return $this->getMenuXmlStringRecreated($menuName,$arrDirtyMenuVals,$arrMenusInputsAndVals);
	}

	function getXmlValuesExtracted_pr($arrDirtyMenuVals)
	{
		//$tmp1 = array();
		$retV=false;
		if(count($arrDirtyMenuVals) > 0){
			foreach($arrDirtyMenuVals as $key => $val){
				$tmp = $val["attributes"]["elem_name"];
				if(isset($tmp) && !empty($tmp)){
					global $$tmp;
					$$tmp = $val["value"];
					$v[]=$$tmp;
				}
			}
			$retV=true;
		}
		if($retV){
			return $v;
		}
		//return $retV;
	}

	function extractXmlValue($str_xml){
		$arr_xml = $this->getXmlMenuArray($str_xml);
		$ret_xml = $this->getXmlValuesExtracted($arr_xml);
		if($ret_xml[0]){
			return $ret_xml[1];
		}else{
			return array();
		}
	}

	function get_tab_code($tb){
		$tb = strtolower($tb);
		switch($tb){
			case "lid position":
				$tb = "lidpos";
			break;
			case "lacrimal system":
				$tb = "lacSys";
			break;
			case "conjunctiva";
				$tb = "conj";
			break;
			case "cornea":
				$tb = "corn";
			break;
			case "antChamber":
				$tb = "anch";
			break;
			case "iris":
				$tb = "iris";
			break;
		}
		return $tb;
	}

	function getExamXmlFiles($exm, $tab=""){
		$osv = new SaveFile();
		$up = $osv->cr_wvexams_db();
		$ar = array();
		switch($exm){
			case "CVF":
				$ar["od"] = $up."/confrontationField_od.xml";
				$ar["os"] = $up."/confrontationField_os.xml";
			break;
			case "Pupil":
				$ar["od"] = $up."/pupil_od.xml";
				$ar["os"] = $up."/pupil_os.xml";
			break;
			case "ExternalExam":
				$ar["od"] = $up."/external_od.xml";
				$ar["os"] = $up."/external_os.xml";
			break;
			case "LA":
			case "L&A":
				$ar["lids"]["od"] = $up."/lids_od.xml";
				$ar["lids"]["os"] = $up."/lids_os.xml";
				$ar["lesion"]["od"] = $up."/lesion_od.xml";
				$ar["lesion"]["os"] = $up."/lesion_os.xml";
				$ar["lidpos"]["od"] = $up."/lidposition_od.xml";
				$ar["lidpos"]["os"] = $up."/lidposition_os.xml";
				$ar["lacSys"]["od"] = $up."/lacrimalSys_od.xml";
				$ar["lacSys"]["os"] = $up."/lacrimalSys_os.xml";
			break;
			case "SLE":
				$ar["conjunctiva"]["od"] = $up."/conjunctiva_od.xml";
				$ar["conjunctiva"]["os"] = $up."/conjunctiva_os.xml";
				$ar["cornea"]["od"] = $up."/cornea_od.xml";
				$ar["cornea"]["os"] = $up."/cornea_os.xml";
				$ar["antChamber"]["od"] = $up."/antChamber_od.xml";
				$ar["antChamber"]["os"] = $up."/antChamber_os.xml";
				$ar["lens"]["od"] = $up."/lens_od.xml";
				$ar["lens"]["os"] = $up."/lens_os.xml";
				$ar["iris"]["od"] = $up."/iris_od.xml";
				$ar["iris"]["os"] = $up."/iris_os.xml";
			break;
			case "Fundus":
				$ar["vitreous"]["od"] = $up."/vitreous_od.xml";
				$ar["vitreous"]["os"] = $up."/vitreous_os.xml";
				$ar["retinal"]["od"] = $up."/retinal_od.xml";
				$ar["retinal"]["os"] = $up."/retinal_os.xml";
				$ar["macula"]["od"] = $up."/macula_od.xml";
				$ar["macula"]["os"] = $up."/macula_os.xml";
				$ar["periphery"]["od"] = $up."/periphery_od.xml";
				$ar["periphery"]["os"] = $up."/periphery_os.xml";
				$ar["bloodVessels"]["od"] = $up."/bloodVessels_od.xml";
				$ar["bloodVessels"]["os"] = $up."/bloodVessels_os.xml";
				$ar["opticNerveDisc"]["od"] = $up."/opticNerveDisc_od.xml";
				$ar["opticNerveDisc"]["os"] = $up."/opticNerveDisc_os.xml";
				$ar["cd"]["od"] = $up."/cd_od.xml";
				$ar["cd"]["os"] = $up."/cd_os.xml";
			break;
			case "RefSurg":
				$ar["od"] = $up."/ref_surg_od.xml";
				$ar["os"] = $up."/ref_surg_os.xml";
			break;
			case "Gonio":
				$ar["od"] = $up."/iopGon_od.xml";
				$ar["os"] = $up."/iopGon_os.xml";
			break;

			default:
			break;
		}

		if(!empty($tab)){
			$ret = array();
			if(isset($ar[$tab])){
				$ret = $ar[$tab];
			}else{
				$tab = strtolower($tab);
				if($tab=="lid position"){
					$tab = "lidpos";
				}else if($tab=="lacrimal system" || $tab== "lacsys"){
					$tab = "lacSys";
				}else if($tab=="ant. chamber" || $tab=="antchamber"){
					$tab = "antChamber";
				}else if($tab=="iris & pupil"){
					$tab = "iris";
				}else if($tab=="optic nerve"){
					$tab = "opticNerveDisc";
				}else if($tab=="retinal exam" || $tab=="retinalexam"){
					$tab = "retinal";
				}else if($tab=="corn"){
					$tab = "cornea";
				}else if($tab=="vessels"){
					$tab = "bloodVessels";
				}

				if(isset($ar[$tab])){
					$ret = $ar[$tab];
				}
			}
			$ar = $ret;
		}

		return $ar;
	}

	function get_ar_val_tag($opt_name){
		$ar = array("negative"=>"Absent",
				"pos4"=>"4+",
				"pos3"=>"3+",
				"pos2"=>"2+",
				"pos1"=>"1+",
				"pos_4"=>"4+",
				"pos_3"=>"3+",
				"pos_2"=>"2+",
				"pos_1"=>"1+",
				"text"=>"Comments",
				"med"=>"Medium",
				"horizontal_size"=>"Horizontal size",
				"vertical_size"=>"Vertical size",
				"punctal_involvement"=>"Punctal involvement",
				"loss_of_lashes"=>"Loss of lashes",
				"telangiatatic_vessels"=>"Telangiatatic vessels",
				"Tempo"=>"Temporal",
				"nochange"=>"No change",
				"mod"=>"Moderate",
				"suptemp"=>"Superotemporal",
				"inftemp"=>"Inferotemporal",
				"supnasal"=>"Superonasal",
				"infnasal"=>"Inferonasal"

				);

		if(isset($ar[$opt_name])){
			$opt_name = $ar[$opt_name];
		}else{
			if($opt_name=="rul"||$opt_name=="rll"||$opt_name=="lul"||$opt_name=="lll"){
				$opt_name = strtoupper($opt_name);
			}else{
				$opt_name = ucfirst($opt_name);
			}
		}
		return $opt_name;
	}

	function get_all_exam_options(){
		$arr_exams=array(	"L&A"=>array("Lids","Lesion", "Lid Position", "Lacrimal System"),
						"SLE"=>array( "Conjunctiva","Cornea","Ant. Chamber","Iris & Pupil","Lens"),
						"Fundus"=>array("Optic Nerve","Vitreous","Macula","Vessels","Periphery","Retinal Exam")); //"Blood Vessal",
		return $arr_exams;
	}

	function xml_to_table($pth, $exm, $tab){
		//$pth
		$reader = new XMLReader();
		$strxml = file_get_contents($pth);
		$reader->XML($strxml);
		$htm = "";$ar_obsrv=array();
		$flg_first_tag=0;

		$ar_open_tags=array();
		$ar_open_exam=array();

		$ar_close_tag_dv=array();

		//$arr
		list($arr_exm_ext_names, $arr_exm_ext_parents) = $this->get_exam_extension($exm, 1,0,$tab);

		//
		while ($reader->read()) {

			//if ($reader->hasValue) {
				//$htm .= ": " . $reader->value;
			//}

			$opt_name="";
			$opt_name=$reader->name;
			$tag_name=$opt_name;

			if($tag_name == "advanceoptions"){ continue; }

			if($reader->nodeType == XMLReader::ELEMENT){
				$flg_first_tag+=1;
				if($flg_first_tag>2){
					//$htm .= "\nSTART: ".$reader->name;

					//$htm .= $reader->name;
					//$htm .= "\n";

					//--
					if ($reader->hasAttributes) {
						$htm_opt_name="";
						while ($reader->moveToNextAttribute()) {
							//$htm .= $reader->name . "='" . $reader->value . "'\n";
							if($reader->name == "tabname"){ //tabname
								$htm .="<tr ><td >".html_entity_decode($reader->value)."</td><td class=\"sub\">";
								array_push($ar_open_tags,$tag_name);
								array_push($ar_open_exam,$reader->value);
							}else if($reader->name == "examname"){ //exam tag/ main tag

								if(count($ar_close_tag_dv)>0){
									$t = array_pop($ar_close_tag_dv);
									if($t=="2"){
										////$htm .="</div>";
										$htm .="</td></tr>";
										$ar_close_tag_dv[] = "1";
									}else if($t=="1"){
										//$htm .="</div></div>";
										$ar_close_tag_dv[] = "1";
									}
								}else{
									//$htm .="<div class=\"exm_op\"></div>";
								}

								//
								$parent_exm = "";
								if(count($ar_open_exam)>0){$parent_exm = implode("/", $ar_open_exam);}

								//serach id from finding name
								$id_exm_ext = array_search($reader->value, $arr_exm_ext_names);
								if($id_exm_ext!==false){ //check if multiple same name findings, then check with parents
									$ar_m_vals = array_keys($arr_exm_ext_names, $reader->value);
									if(count($ar_m_vals)>1 && !empty($parent_exm)){
										$id_exm_ext_prnt = array_search($parent_exm, $arr_exm_ext_parents);
										if($id_exm_ext_prnt!==false){$id_exm_ext = $id_exm_ext_prnt;}
									}
								}

								$del = "";
								$tr_exm_ext = "";
								$a_st=""; $a_ed="";

								if($id_exm_ext!==false){

									$tag_ok=1;
									if(!empty($parent_exm)){ //dbl check
										if($arr_exm_ext_parents[$id_exm_ext] != $parent_exm){ $tag_ok=0; }
									}

									if(!empty($tag_ok)){
										$a_st="<a href=\"javascript:void(0)\" onclick=\"editFormData(this, '".$id_exm_ext."')\" >"; $a_ed="</a>";
										$del = "<span class=\"glyphicon glyphicon-remove\" onclick=\"saveFormData('".$id_exm_ext."')\"></span>";
										$tr_exm_ext = " class=\"tr_exm_ext\" ";
										unset($arr_exm_ext_names[$id_exm_ext]);
									}
								}

								//
								$st_pad="";
								$pad_obsrv_lm=20;
								$pad_obsrv_len = count($ar_open_exam);
								$pad_obsrv_total = $pad_obsrv_len*$pad_obsrv_lm;
								if(!empty($pad_obsrv_total)){ $st_pad=" style=\"padding-left:".$pad_obsrv_total."px!important;\" "; }

								////$htm .="<div class=\"exm\"><h2>".html_entity_decode($reader->value)."".$del."</h2><div class=\"sub\">";
								$htm .="<tr ".$tr_exm_ext."><td ".$st_pad.">".$a_st.html_entity_decode($reader->value).$a_ed."".$del."</td><td class=\"sub\">";
								array_push($ar_open_tags,$tag_name);
								array_push($ar_open_exam,$reader->value);
								$open_obsrv = implode("/",$ar_open_exam);
								$ar_obsrv[] = $open_obsrv;

								$ar_close_tag_dv[] ="2";

							}else if($reader->name == "elem_name" && empty($htm_opt_name)){ //child Element
								$opt_name = $this->get_ar_val_tag($opt_name);
								$htm_opt_name = "<span class=\"opts\">".html_entity_decode($opt_name)."</span>";
							}else if($reader->name == "pv"){
								$htm_opt_name = "<span class=\"opts\">".html_entity_decode($reader->value)."</span>";
							}
						}

						if(!empty($htm_opt_name)){  $htm .= $htm_opt_name;	}
						//$htm .= "\n";
					}
					//--

				}
			}else if($reader->nodeType == XMLReader::END_ELEMENT){
				//$htm .= "\nEND: ".$reader->name;

				$chk = end($ar_open_tags);

				//$htm .= " <br>CHK:: $chk == $opt_name<br> ";
				//$htm .=print_r($ar_open_tags,1);
				//$htm .= $tag_name;
				//if(in_array($tag_name,$ar_open_tags)){
				if($chk == $tag_name){
					//$htm .= " BBC ";
					$tmp = array_pop($ar_open_tags);
					$tmp = array_pop($ar_open_exam);
					//$htm .=implode("", $ar_close_tag_dv);
					//echo $tag_name;
					//print_r($ar_close_tag_dv);
					$t = array_pop($ar_close_tag_dv);
					////if($t=="2"){ $htm .="</div></div>"; }else if($t=="1"){ $htm .="</div>"; }
					$htm .="</td></tr>";

					//$ar_close_tag_dv=array();
					//$htm .= "\n------\n";
				}

			}
		}


		//select obsrv
		$sel="<option value=\"Main\">Main</option>";
		if(count($ar_obsrv)>0){
			foreach($ar_obsrv as $k => $v){
				$sel.="<option value=\"".$v."\">".$v."</option>";
			}
		}



		return array($htm, $sel);
	}

	function get_exam_info(){
		$tmp = $_REQUEST['nm'];
		$ar_tmp = explode("--",$tmp);
		$exm = $ar_tmp[0];
		$sub_exm = $ar_tmp[1];

		$arr = $this->getExamXmlFiles($exm, $sub_exm);
		$pth_od = $arr["od"];
		//$pth_os = $arr[$sub_exm]["os"];


		list($htm, $sel_obsrv) = $this->xml_to_table($pth_od, $exm, $sub_exm);

		//$htm = "<tr><td colspan=\"2\">".$htm."</td></tr>";

		$htm = nl2br($htm);

		$ar_ret = array();
		$ar_ret["htm"] = $htm;
		$ar_ret["obsrv"] = $sel_obsrv;
		echo json_encode($ar_ret);


	}

	function maketag($str, $flgfull=""){ //xml tags
		$str = trim($str);
		$str = strtolower($str);
		$str = str_replace(" ","",$str);
		$str = preg_replace('/[^a-z0-9_]+/', '', $str);
		$tmp = substr($str, 0,3);
		if($tmp=="xml"){ $str = substr($str, 3);  }
		$tmp = substr($str, 0,1);
		if(is_numeric($tmp)){ $str = "a".$str; }
		if(empty($flgfull)){$str = substr($str, 0,5);}//max tag name
		return $str;
	}

	function get_group_var($str, $exm){
		$ret="";
		$arr = array("Trauma"=>"grp_Trauma", "Cyst"=>"grp_Cyst", "Puncta"=>"grp_Puncta", "Lacrimal Duct"=>"grp_LD", "Tube Stent"=>"grp_LD", "Brow Ptosis"=>"grp_brwpto",
					"Orbital Fat"=>"grp_OrbFat",
					"Ptosis VF"=>"grp_PtoVF", "Upper Puncta"=>"grp_LacUpper", "Lower Puncta"=>"grp_LacLower", "Lacrimal Probing"=>"grp_LacProb", "Nasal Exam"=>"grp_NslExm",
					"Special Tests"=>"grp_SplTest",
					"Bleb"=>"grp_ConjBleb", "Dry Eyes"=>"grp_CorDE", "Dystrophy"=>"grp_Dyst", "Trauma"=>"grp_CornTruma", "Infection/Inflammation"=>"grp_Infect",
					"Edema"=>"grp_CornEdma", "Pigmentary deposits"=>"grp_PigDepo", "Surgery"=>"grp_CornSurgry",
					"AMD"=>"grp_ARMD", "DR"=>"grp_DRRet", "Vascular Occlusion"=>"grp_VasOcc", "Peripheral Degeneration"=>"grp_PeriDeg");

		// For giving grp of parent exam--
		if($str!="Infection/Inflammation" && strpos($str,"/")!==false){
			$tmp = explode("/", $str);
			if(count($tmp)>1){
				$str=$tmp[0];
			}
		}
		// For giving grp of parent exam--

		if(isset($arr[$str]) && !empty($arr[$str])){
			if($str=="Trauma"){
				$ret = ($exm=="Cornea") ? "grp_CornTruma" : "grp_Trauma";
			}else{
				$ret = $arr[$str];
			}
		}else{
			$ret=$this->maketag($str);
			$ret="grp_".$ret;
		}
		return $ret;
	}

	function bakup_file($od_xml_file){
		$od_xml_file_tmp = str_replace(".xml","_bak_".date("m_d_Y_H_i_s").".xml",$od_xml_file);
		rename($od_xml_file,$od_xml_file_tmp);
	}

	function reset_exam($exm, $sub_exm, $ar_files){

		$aey = array("od","os");
		foreach($aey as $k => $eye){
			$tmp_file = $ar_files[$eye];
			if(file_exists($tmp_file)){
				$this->bakup_file($tmp_file);
				$ar_tmp = explode("/",$tmp_file);
				$file_nm = end($ar_tmp);
				$osv = new SaveFile();
				$up = $osv->cr_wvexams_db($file_nm);
			}
		}

		//--
		$dt = wv_dt("now");
		$sql = "UPDATE chart_exam_ext SET opid='".$_SESSION["authId"]."', opdt='".$dt."', del='1' WHERE exam='".sqlEscStr($exm)."' AND tab='".$sub_exm."' AND del='0' ";
		$r=sqlQuery($sql);
	}

	function save_exam_extension( $ar_cust_post_el=array() ){

		if(count($ar_cust_post_el)==0){ 	$ar_post_el = $_POST; }
		else{ $ar_post_el = $ar_cust_post_el; }

		$new_sec_file_up="";
		$tmp = $ar_post_el["el_exam"];
		if(isset($ar_post_el["delid"]) && !empty($ar_post_el["delid"])){
			$delid = $ar_post_el["delid"];
			$arr_exm_ext_info = $this->get_exam_extension('', '', $delid);
			//print_r($arr_exm_ext_info);
			//exit();
			$obsrv_wh_del = $arr_exm_ext_info["parent_obsrv"];
			$obsrv_name_del = $arr_exm_ext_info["full_obsrv"];
			$obsrv_wh_del_l=strtolower($obsrv_wh_del);
			$obsrv_name_del_l=strtolower($obsrv_name_del);
		}else if(isset($ar_post_el["reset"]) && !empty($ar_post_el["reset"])){
			//exit("TEST");


		}else{
			$ar_vals=array();

			$obsrv_wh = $ar_post_el["el_obsrv_wh"];
			$obsrv_name = trim($ar_post_el["el_obsrv_name"]);
			if(!preg_match("/[a-zA-Z0-9]+/", $obsrv_name)){$obsrv_name="";} //observation name must have alphbets in it
			if(empty($obsrv_name)){ echo 0; exit(); }// if observation name is empty, stop
			$obsrv_name = str_replace("/","",$obsrv_name); //removed "/" if present
			$obsrv_name_tag =  $this->maketag($obsrv_name, 1);
			$obsrv_name_tmp = $this->xmlRefineValue($obsrv_name);
			//
			$abs = $ar_post_el["el_grd_abs"]; //
			$pos4 = $ar_post_el["el_grd_4"];
			$pos3 = $ar_post_el["el_grd_3"];
			$pos2 = $ar_post_el["el_grd_2"];
			$pos1 = $ar_post_el["el_grd_1"]; //pos
			$pre = $ar_post_el["el_grd_pre"];
			$t = $ar_post_el["el_grd_t"];
			if(!empty($abs)){$ar_vals["grd"][]=$abs;}
			if(!empty($pre)){$ar_vals["grd"][]=$pre;}
			if(!empty($pos4)){$ar_vals["grd"][]=$pos4;}
			if(!empty($pos3)){$ar_vals["grd"][]=$pos3;}
			if(!empty($pos2)){$ar_vals["grd"][]=$pos2;}
			if(!empty($pos1)){$ar_vals["grd"][]=$pos1;}
			if(!empty($t)){$ar_vals["grd"][]=$t;}

			//others
			for($i=1;1==1;$i++){
				if(isset($ar_post_el["el_grd_othr".$i])){
					if(trim($ar_post_el["el_grd_othr".$i])!=""){
						$ar_vals["grd"][]=$this->xmlRefineValue($ar_post_el["el_grd_othr".$i]);
					}
				}else{
					break;
				}
			}

			$lst = $ar_post_el["el_loc_st"]; //Superotemporal
			$lit = $ar_post_el["el_loc_it"]; //Inferotemporal
			$lsn = $ar_post_el["el_loc_sn"]; //Superonasal
			$lin = $ar_post_el["el_loc_in"];	//Inferonasal

			if(!empty($lst)){$ar_vals["loc"][]=$lst;}
			if(!empty($lit)){$ar_vals["loc"][]=$lit;}
			if(!empty($lsn)){$ar_vals["loc"][]=$lsn;}
			if(!empty($lin)){$ar_vals["loc"][]=$lin;}

			//other
			for($i=1;1==1;$i++){
				if(isset($ar_post_el["el_loc_othr".$i])){
					if(trim($ar_post_el["el_loc_othr".$i])!=""){$ar_vals["loc"][]=$this->xmlRefineValue($ar_post_el["el_loc_othr".$i]);}
				}else{
					break;
				}
			}

			$comnt = $ar_post_el["el_comnt"];
			if(!empty($comnt)){$ar_vals["com"][]=$comnt;}
			$obsrv_wh_l=strtolower($obsrv_wh);

			$str_grade = $str_location = $str_comments = "";
		}

		//----

		$ar_tmp = explode("--",$tmp);
		$exm = $ar_tmp[0];
		$sub_exm = $ar_tmp[1];
		$arr_xml_files = $this->getExamXmlFiles($exm, $sub_exm);
		$colspan="8";
		if($sub_exm=="Optic Nerve"||$sub_exm=="Retinal Exam"||$sub_exm=="Ant. Chamber"||$sub_exm=="Iris & Pupil"||$sub_exm=="Macula"||$sub_exm=="Vessels" || $sub_exm=="Periphery"){$colspan="6";}
		else if($sub_exm=="Vitreous"||$sub_exm=="Lens"){$colspan="7";}
		else if($sub_exm=="Lesion"){$colspan="9";}

		//--
		if(isset($ar_post_el["reset"]) && !empty($ar_post_el["reset"])){
			//Reset
			$this->reset_exam($exm, $sub_exm, $arr_xml_files);
			exit(0);
		}
		//--

		//el
		$exm_l = strtolower($exm); //
		$sub_exm_l = $this->get_tab_code($sub_exm);
		$str_exm = $this->maketag($exm_l.$sub_exm_l,1);

		//


		$new_sec_htm=array();
		$new_sec_htm_od=""; $new_sec_htm_os="";
		//---

		//reading --
		$arr_eye = array("od","os");
		foreach($arr_eye as $k => $eye){

			$ar_obsrv=array();
			$ar_open_tags=array();
			$ar_open_exam=array();

			$open_obsrv_tag="";
			$ar_open_tags_del=array();
			$ar_open_exam_del=array();

			//
			$od_xml_file = $arr_xml_files[$eye];

			$reader = new XMLReader();
			$strxml = file_get_contents($od_xml_file);
			$reader->XML($strxml);

			$writer = new XMLWriter();
			$writer->openMemory();
			$writer->startDocument( '1.0', 'UTF-8' );

			while ($reader->read()) {

				$tag_name = $reader->name;//od
				//echo "\nB4: ".$tag_name."-".$reader->nodeType;

				//if DELETEis op ----------------
				if(!empty($obsrv_name_del)){
					switch($reader->nodeType){
						case XMLReader::ELEMENT:
							if ($reader->hasAttributes) {
								//while ($reader->moveToNextAttribute()) {
								$tabname_attr = $reader->getAttribute("tabname");
								if(!empty($tabname_attr)){
									if(!in_array($tag_name,$ar_open_tags_del)){array_push($ar_open_tags_del,$tag_name);}
									if(!in_array($examname_attr,$ar_open_exam_del)){array_push($ar_open_exam_del,$tabname_attr);}
								}

								$examname_attr = $reader->getAttribute("examname");
								if(!empty($examname_attr)){
									//if($reader->name == "examname"){
										if(!in_array($tag_name,$ar_open_tags_del)){
												array_push($ar_open_tags_del,$tag_name);
												array_push($ar_open_exam_del,$examname_attr);
											}
										//if(!in_array($examname_attr,$ar_open_exam_del)){}
										//$open_obsrv = implode("/",$ar_open_exam);
										//$ar_obsrv[] = $open_obsrv;

										//
										$open_obsrv_tmp = implode("/",$ar_open_exam_del);
										if(strtolower($open_obsrv_tmp)==$obsrv_name_del_l){
											$open_obsrv_tag = $tag_name;
										}
									//}
								}
							}
						break;

						case XMLReader::END_ELEMENT:
							$chk = end($ar_open_tags_del);
							if($chk == $tag_name){
								$tmp = array_pop($ar_open_tags_del);
								$tmp = array_pop($ar_open_exam_del);
							}
						break;

					}
					if(!empty($open_obsrv_tag)){
						$reader->next();
						$open_obsrv_tag="";
					}else{
						//if ($reader->hasAttributes) {
							//$reader->moveToElement(); //current element
						//}
						//echo ", A4: ".$tag_name."-".$reader->nodeType;
					}
				}//
				//if DELETEis op ----------------

				//$reader->next();//to test
				//continue;

				switch($reader->nodeType){
					case XMLReader::ELEMENT:
						//echo "<br/>Start element: ".$reader->name;

						$writer->startElement($reader->name);

						if ($reader->hasAttributes) {
							while ($reader->moveToNextAttribute()) {
								//$reader->name . "='" . $reader->value
								$tmp_attr_val = $reader->value;
								if($reader->name=="pv"){ $tmp_attr_val = $tmp_attr_val; }

								$writer->writeAttribute($reader->name, $reader->value);

								if($reader->name == "tabname"){
									array_push($ar_open_tags,$tag_name);
									array_push($ar_open_exam,$reader->value);
								}else	if($reader->name == "examname"){
									array_push($ar_open_tags,$tag_name);
									array_push($ar_open_exam,$reader->value);
									$open_obsrv = implode("/",$ar_open_exam);
									$ar_obsrv[] = $open_obsrv;

									//
									$open_obsrv_tmp = implode("/",$ar_open_exam);
									if(!empty($obsrv_wh) && $obsrv_wh != "Main" && strtolower($open_obsrv_tmp)==$obsrv_wh_l){
										$open_obsrv_tag = $tag_name;
									}

								}
							}
						}

					break;
					/*
					case XMLReader::TEXT:
						echo "<br/>Text node: ".$reader->name;
					break;
					case XMLReader::CDATA:
						echo "<br/>CDATA node: ".$reader->name;
					break;
					*/
					case XMLReader::COMMENT:
						//echo "<br/>Comment node: ".$reader->name;
						$writer->writeComment($reader->value);
					break;
					/*
					case XMLReader::SIGNIFICANT_WHITESPACE:
						echo "<br/>Significant Whitespace node: ".$reader->name;
					break;
					*/
					case XMLReader::END_ELEMENT:
						//echo "<br/>End Element: ".$reader->name;

						//create sub finding --
						//echo
						$open_obsrv_tmp = implode("/",$ar_open_exam); //get open exams
						if(($obsrv_wh == "Main" && ($reader->name=="od" || $reader->name=="os")) ||
							(!empty($obsrv_wh) && $obsrv_wh != "Main" && strtolower($open_obsrv_tmp)==$obsrv_wh_l) && $open_obsrv_tag == $reader->name){ //create element before od/os

							///make xml fields
							//$xml_od = "";
							//$xml_os = "";

							if(!in_array_nocase($obsrv_name, $ar_obsrv) && !in_array_nocase($open_obsrv_tmp."/".$obsrv_name, $ar_obsrv)){ //check for duplicate

								$writer->startElement($obsrv_name_tag);
								$writer->writeAttribute("examname", $obsrv_name_tmp);

								if(count($ar_vals)>0){
									foreach($ar_vals as $tp => $arvals2){
										if(count($arvals2)>0){
											foreach($arvals2 as $tp2 => $val3){
												if($val3!=""){
													$indx = $tp2+1;
													$val4tag = $this->maketag($val3);
													$tag = $tp.$indx;
													$tag = $tag.$val4tag;
													$tag = $tag."_".$this->maketag($obsrv_wh.$obsrv_name,1);

													$el_name="el_".$str_exm."_".$tag;
													$el_name = str_replace(array("od","os","Od","Os"),"",$el_name);
													$el_val=$val3;
													$el_name_full = $el_name."_".ucfirst($eye);
													$writer->startElement($tag);
													$writer->writeAttribute("elem_name", $el_name_full);
													$writer->writeAttribute("pv", $el_val);
													$writer->fullEndElement();

													// new section htm --
													if($el_val=="Comments"){
														$new_sec_htm[$eye][$el_val] = "<div class=\"checkbox-inline\"><textarea id=\"".$el_name_full."\" name=\"".$el_name_full."\" class=\"form-control\" onblur=\"checkAbsent(this)\"><?php echo ($".$el_name_full.");?></textarea></div>";
													}else{
														$new_sec_htm[$eye][$el_val] = "<div class=\"checkbox-inline\"><input id=\"".$el_name_full."\" type=\"checkbox\"  onclick=\"checkAbsent(this)\" name=\"".$el_name_full."\" value=\"".$el_val."\" <?php echo ($".$el_name_full." == \"".html_entity_decode($el_val)."\") ? \"checked=\\\"checked\\\"\" : \"\";?> ><label for=\"".$el_name_full."\"  >".html_entity_decode($el_val)."</label></div>";
													}
													// new section htm --

													//---
													$tmp = "";
													if(!empty($open_obsrv_tmp)){ $tmp .= $open_obsrv_tmp."/"; }
													$tmp .= $obsrv_name_tmp;
													$new_sec_htm["full_exam"] = $tmp;
													//---


												}
											}
										}
									}
								}
								$writer->fullEndElement();
								//


							}//
						}//

						//create sub finding --

						//
						$chk = end($ar_open_tags);
						if($chk == $tag_name){
							$tmp = array_pop($ar_open_tags);
							$tmp = array_pop($ar_open_exam);
						}

						$writer->fullEndElement(); //end main tag

					break;
				}
			}

			$writer->endDocument();
			$out_str = $writer->outputMemory();

			//Loop array new_sec_htm and order them --
			if(count($ar_vals["grd"])>0){
				$grd_order = array("Absent","Present", "T", "1+", "2+", "3+", "4+");
				foreach($grd_order as $k => $v){
					if(isset($new_sec_htm["od"][$v])){
						$new_sec_htm_od.=$new_sec_htm["od"][$v];
						unset($new_sec_htm["od"][$v]);
					}
					if(isset($new_sec_htm["os"][$v])){
						$new_sec_htm_os.=$new_sec_htm["os"][$v];
						unset($new_sec_htm["os"][$v]);
					}
				}

				foreach($ar_vals["grd"] as $k => $v){
					if(!in_array($v, $grd_order)){
						if(isset($new_sec_htm["od"][$v])){
							$new_sec_htm_od.=$new_sec_htm["od"][$v];
							unset($new_sec_htm["od"][$v]);
						}
						if(isset($new_sec_htm["os"][$v])){
							$new_sec_htm_os.=$new_sec_htm["os"][$v];
							unset($new_sec_htm["os"][$v]);
						}
					}
				}
				$str_grade = implode("!@!", $ar_vals["grd"]);
			}

			if(count($ar_vals["loc"])>0){
				foreach($ar_vals["loc"] as $k => $v){
					if(isset($new_sec_htm["od"][$v])){
						$new_sec_htm_od.=$new_sec_htm["od"][$v];
						unset($new_sec_htm["od"][$v]);
					}
					if(isset($new_sec_htm["os"][$v])){
						$new_sec_htm_os.=$new_sec_htm["os"][$v];
						unset($new_sec_htm["os"][$v]);
					}
				}
				$str_location = implode("!@!", $ar_vals["loc"]);
			}

			//Comments
			if(count($ar_vals["com"])>0){
				foreach($ar_vals["com"] as $k => $v){
					if(isset($new_sec_htm["od"][$v])){
						$new_sec_htm_od.=$new_sec_htm["od"][$v];
						unset($new_sec_htm["od"][$v]);
					}
					if(isset($new_sec_htm["os"][$v])){
						$new_sec_htm_os.=$new_sec_htm["os"][$v];
						unset($new_sec_htm["os"][$v]);
					}
				}
				$str_comments = !empty($ar_vals["com"]) ? 1 : 0 ;
			}
			//Loop array new_sec_htm and order them --

			if((!empty($new_sec_htm_od) || !empty($new_sec_htm_os)) || !empty($obsrv_name_del)){	//
				$this->bakup_file($od_xml_file);
				file_put_contents($od_xml_file,$out_str);
			}

		}//end for eye

		//create html --
		$dt = wv_dt("now");
		if(!empty($new_sec_htm_od) && !empty($new_sec_htm_os)){

			//Padding--
			$pd_lft=10;
			$tmp = count(explode("/",$new_sec_htm["full_exam"]));
			$total_pd = ($tmp)*$pd_lft;$pad_attr="";
			if(!empty($total_pd)){ $pad_attr= "style=\"padding-left:".$total_pd."px!important;\""; }
			//Padding--
			//Yellow---
			$cls_ylw_row="";
			if($obsrv_wh != "Main"){
				$cls_grp = $this->get_group_var($obsrv_wh, $sub_exm);
				$cls_ylw_row="".$cls_grp;
			}
			//Yellow---

			$htm_exam_section = "<tr id=\"d_".$obsrv_name_tag."\" class=\"fnd_exm_ext ".$cls_ylw_row." \" >
							<td align=\"left\" ".$pad_attr." >".$obsrv_name."</td>
							<td align=\"left\" colspan=\"".$colspan."\">".$new_sec_htm_od."</td>
							<td align=\"center\" class=\"bilat\" onClick=\"check_bl('".$obsrv_name_tag."')\">BL</td>
							<td align=\"left\" ".$pad_attr." >".$obsrv_name."</td>
							<td align=\"left\" colspan=\"".$colspan."\">".$new_sec_htm_os."</td>
							</tr>";
			$dt_str = str_replace(array("-",":"," "), "_", $dt);
			$file_name = $str_exm."_".$obsrv_name_tag."_".$dt_str.".php";
			$osv = new SaveFile();
			$osv->cr_exam_new_section_file($file_name, $htm_exam_section);

			////Save in db --
			$sql = "INSERT INTO chart_exam_ext (exam, tab, parent_obsrv, obsrv, htm_path, opid, opdt,full_obsrv, grade, location, comments)
						VALUES( '".sqlEscStr($exm)."', '".sqlEscStr($sub_exm)."', '".sqlEscStr($obsrv_wh)."', '".sqlEscStr($obsrv_name_tmp)."', ".
							  " '".sqlEscStr($file_name)."', '".sqlEscStr($_SESSION["authId"])."', '".$dt."', '".sqlEscStr($new_sec_htm["full_exam"])."', ".
							  " '".sqlEscStr($str_grade)."', '".sqlEscStr($str_location)."', '".sqlEscStr($str_comments)."' ) ";
			$row = sqlQuery($sql);

		}else if(!empty($obsrv_name_del) && !empty($delid)){
			//delete sub options
			$this->del_sub_findings($delid);
		}

		echo 0;
	}

	function del_sub_findings($delid){
		$dt = wv_dt("now");
		//Query to del
		$sql = "UPDATE chart_exam_ext SET del='1', opid='".$_SESSION["authId"]."', opdt='".$dt."' WHERE id='".$delid."'  ";
		$row = sqlQuery($sql);

		//--
		$sql = "SELECT parent_obsrv, obsrv, exam, tab FROM chart_exam_ext WHERE id='".$delid."'  ";
		$row = sqlQuery($sql);
		if($row!=false){
			$obsrv = $row["obsrv"];
			$exm = $row["exam"];
			$tb = $row["tab"];
			$pobsrv = $row["parent_obsrv"];
			if(!empty($pobsrv) && $pobsrv!="Main"){ $obsrv = $pobsrv."/".$obsrv;  }

			$sql = " SELECT id FROM chart_exam_ext WHERE parent_obsrv='".$obsrv."' AND exam='".$exm."' AND tab = '".$tb."'  AND del='0' ";
			$rez = sqlStatement($sql);
			for($u=0;$row=sqlFetchArray($rez);$u++){
				$sId = $row["id"];
				$this->del_sub_findings($sId);
			}
		}
	}

	function is_dynmic_finding($symp, $exm){
		$ret = 0;
		$sql = "SELECT id FROM chart_exam_ext where obsrv = '".sqlEscStr($symp)."' AND exam='".sqlEscStr($exm)."' AND del='0'  ";
		$row = sqlQuery($sql);
		if($row!=false && !empty($row["id"])){
			$ret = 1;
		}
		return $ret;
	}

	function get_exam_extension($exm, $flg_name_only=0, $id_info=0, $tab=""){

		$osv = new SaveFile();
		$up = $osv->get_exam_new_section_file_dir();

		$arr_ret=array();
		if($flg_name_only==1){$arr_ret[0]=array();$arr_ret[1]=array();}

		$phrase_wh="";
		if(!empty($id_info)){ $phrase_wh=" AND id='".$id_info."' "; }
		else{
			$phrase_wh_t = (!empty($tab)) ? " AND tab = '".sqlEscStr($tab)."' " : "" ;
			$phrase_wh=" AND exam='".sqlEscStr($exm)."' ".$phrase_wh_t." AND obsrv!='' ORDER BY full_obsrv, parent_obsrv, id ";
		}

		$sql = "SELECT * FROM chart_exam_ext WHERE del='0' ".$phrase_wh."   ";
		$rez = sqlStatement($sql);
		for($i=1; $row=sqlFetchArray($rez);$i++){
			if(!empty($id_info)){
				$arr_ret = $row;
			}else	if($flg_name_only==1){
				$arr_ret[0][$row["id"]] = $row["obsrv"];
				$arr_ret[1][$row["id"]] = $row["parent_obsrv"];
			}else{
				$exm = $row["exam"];
				$sub_exm = $row["tab"];
				$parent_obsrv = trim($row["parent_obsrv"]);
				$in = $up."/".$row["htm_path"];

				if(file_exists($in)){



					if($parent_obsrv!="Main"){
						//echo "<br>".$parent_obsrv." - ".$row["obsrv"];

						$sep="!@!";
						if(strpos($parent_obsrv, "Upper Lids/")!==false || strpos($parent_obsrv, "Lower Lids/")!==false || strpos($parent_obsrv, "Advanced Plastics/")!==false){
							$parent_obsrv = str_replace(array("Upper Lids/", "Lower Lids/","Advanced Plastics/"),array("Upper Lids".$sep, "Lower Lids".$sep, "Advanced Plastics".$sep),$parent_obsrv);
						}

						//All Sub exam will come after hard coded options upto 2 levels
						$tmp = explode("/", $parent_obsrv);
						$parent_obsrv = str_replace($sep,"/",$parent_obsrv);

						if(count($tmp)>1){

							if($this->is_dynmic_finding($tmp[0], $exm)){
								$parent_obsrv = "Main";
							}else{
								if($this->is_dynmic_finding($tmp[1], $exm)){
									$parent_obsrv = $tmp[0];
								}else{
									if(count($tmp)>2){
										$parent_obsrv=$tmp[0]."/".$tmp[1];
									}
								}
							}
						}else{
							if($this->is_dynmic_finding($parent_obsrv, $exm)){
								$parent_obsrv = "Main";
							}
						}
					}

					$arr_ret[$sub_exm][$parent_obsrv][] = $in;

					//"".file_get_contents($in);
					//$exm_l = strtolower($exm); //
					//$sub_exm_l = $this->get_tab_code($sub_exm);
					//$str_exm = $this->maketag($exm_l.$sub_exm_l,1);

				}
			}
		}

		return $arr_ret;
	}

	function get_exam_ext_findings($vedid="", $vexm=""){

		$edid = !empty($vedid) ? $vedid : $_GET["edid"];
		$exm_str = !empty($vexm) ? $vexm : $_GET["exm"];

		$ar_exm_str = explode("--",$exm_str);
		$exm = trim($ar_exm_str[0]);
		$sub_exm = $ar_exm_str[1];
		if(empty($edid) || empty($exm)){ return ; }

		$arr_exm_ext_info = $this->get_exam_extension('', '', $edid);

		$obsrv_wh = $arr_exm_ext_info["parent_obsrv"];
		$obsrv_name = $arr_exm_ext_info["full_obsrv"];
		$obsrv = $arr_exm_ext_info["obsrv"];
		$obsrv_wh_l=strtolower($obsrv_wh);
		$obsrv_name_l=strtolower($obsrv_name);

		//
		$arr_xml_files = $this->getExamXmlFiles($exm, $sub_exm);

		//TEST --
		$ar_obsrv=array();
		$ar_open_tags=array();
		$ar_open_exam=array();

		$ar_obsrv_findings=array();
		$ar_obsrv_findings_type=array();

		$open_obsrv_tag="";
		//
		$od_xml_file = $arr_xml_files["od"];
		$reader = new XMLReader();
		$strxml = file_get_contents($od_xml_file);
		$reader->XML($strxml);
		while ($reader->read()) {
			$tag_name = $reader->name;

			switch($reader->nodeType){
				case XMLReader::ELEMENT:
					if ($reader->hasAttributes) {
						$tabname_attr = $reader->getAttribute("tabname");
						if(!empty($tabname_attr)){
							if(!in_array($tag_name,$ar_open_tags)){array_push($ar_open_tags,$tag_name);}
							if(!in_array($examname_attr,$ar_open_exam)){array_push($ar_open_exam,$tabname_attr);}
						}

						$examname_attr = $reader->getAttribute("examname");
						if(!empty($examname_attr)){
							if(!in_array($tag_name,$ar_open_tags)){
								array_push($ar_open_tags,$tag_name);
								array_push($ar_open_exam,$examname_attr);
							}
							//if(!in_array($examname_attr,$ar_open_exam)){}
							$open_obsrv_tag="";
							$open_obsrv_tmp = implode("/",$ar_open_exam);
							if(strtolower($open_obsrv_tmp)==$obsrv_name_l){
								$open_obsrv_tag = $tag_name;
							}
						}

						if(!empty($open_obsrv_tag)){
							$elem_pv = $reader->getAttribute("pv");
							if(!empty($elem_pv)){
								$ar_obsrv_findings[] = $this->xmlRefineValue($elem_pv,1);
								$tmp_findings_type = substr($tag_name,0,3);
								$ar_obsrv_findings_type[] = $tmp_findings_type;
							}
						}
					}
				break;
				case XMLReader::END_ELEMENT:
					$chk = end($ar_open_tags);
					if($chk == $tag_name){
						$tmp = array_pop($ar_open_tags);
						$tmp = array_pop($ar_open_exam);
						$open_obsrv_tag="";
					}
				break;

			}
		}
		//TEST --

		$arr_ret=array();
		$arr_ret["parent_obsrv"] = $obsrv_wh;
		$arr_ret["obsrv"] = $obsrv;
		$arr_ret["finding"] = $ar_obsrv_findings;
		$arr_ret["finding_type"] = $ar_obsrv_findings_type;
		if(!empty($vedid)){ return  $arr_ret; }
		else{	echo json_encode($arr_ret); }
	}

	function edit_exam_extension(){
		$edid = $_POST["el_edid"];
		$exm_str = $_POST["el_exam"];

		$arr=array();
		$arr["el_exam"]=$exm_str;
		$arr["delid"]=$edid;
		$this->save_exam_extension( $arr ); //Delete old
		$this->save_exam_extension(); //add new
	}

	function get_exm_ext_findings($exm, $tab, $caseup=0){
		if($exm=="LA"){  $exm="L&A"; }
		if($tab=="Iris"){  $tab = "Iris & Pupil"; }
		$ar=array();
		$ar_obsrv=array();
		if(!empty($tab) && $tab!="CD"){

			$phrase_wh_t = (!empty($tab) && $tab!="All") ? " AND tab = '".sqlEscStr($tab)."' " : "" ;
			$phrase_wh_ex = (!empty($exm)) ? " AND exam = '".sqlEscStr($exm)."' " : "" ;

			$phrase_wh=" ".$phrase_wh_ex.$phrase_wh_t." AND obsrv!='' ORDER BY full_obsrv, parent_obsrv, id ";
			$sql = "SELECT id, obsrv, full_obsrv FROM chart_exam_ext WHERE del='0' ".$phrase_wh."   ";

			$rez = sqlStatement($sql);
			for($i=1; $row=sqlFetchArray($rez);$i++){
				if(!empty($row["full_obsrv"])){
					$ar[] = (!empty($caseup)) ? strtoupper($row["full_obsrv"]) : $row["full_obsrv"];
					$ar_obsrv[] = (!empty($caseup)) ? strtoupper($row["obsrv"]) : $row["obsrv"];
				}
			}
		}
		return array($ar, $ar_obsrv);
	}

	function get_ee_findings_html($exm, $tab, $obsv){
		$html="";

		if($tab=="Iris"){  $tab = "Iris & Pupil"; }

		$obsv=trim($obsv);
		if(!empty($obsv)){
			$osv = new SaveFile();
			$up = $osv->get_exam_new_section_file_dir();

			$str_tab = (!empty($tab)) ? " AND tab='".sqlEscStr($tab)."' " : "";
			$str_exm = (!empty($exm)) ? " AND exam='".sqlEscStr($exm)."' " : "";

			$sql = "SELECT * FROM chart_exam_ext WHERE del='0' AND full_obsrv LIKE '".sqlEscStr($obsv)."'  ".$str_tab." ".$str_exm;
			$row = sqlQuery($sql);
			if($row!=false){
				$htm_path = $row["htm_path"];

				if(!empty($row["htm_path"])){
					$in = $up."/".$row["htm_path"];
					if(file_exists($in)){
						$html = file_get_contents($in);
					}
				}
			}

		}
		return $html;
	}

	function check_ee_findings($obsv){
		$ret="";
		$ar_grade = $ar_location = array();
		$sql = "SELECT full_obsrv, obsrv, parent_obsrv, grade, location FROM chart_exam_ext WHERE del='0' AND full_obsrv = '".sqlEscStr($obsv)."' ";
		$row = sqlQuery($sql);
		if($row!=false){
			$full_obsrv = $row["full_obsrv"];
			$obsrv_db = $row["obsrv"];
			$parent_obsrv = $row["parent_obsrv"];
			$grade = $row["grade"];
			$location = $row["location"];
		}

		//--

		if(!empty($grade)){$ar_grade = explode("!@!", $grade);}
		if(!empty($location)){$ar_location = explode("!@!", $location);}

		return array($ar_grade, $ar_location);
	}
}

?>
