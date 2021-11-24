<?php
/*
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
*/
?>
<?php
/*
File: HPI.php
Coded in PHP7
Purpose: This class file provides functions to manage hpi exam in work view.
Access Type : Include file
*/
?>
<?php
class HPI{
	public function __construct(){}
	
	function get_custom_hpi(){	
		$ar = array();
		$ar_c = $this->get_hpi_categories();
		foreach($ar_c as $k=>$arcv){
			$cat = $arcv["hpi"];
			$cid = $arcv["id"];
			
			$ar_sc = $this->get_hpi_categories($cid);
			foreach($ar_sc as $k1=>$arscv){
				$subcat = $arscv["hpi"];
				$scid = $arscv["id"];
				
				$ar_chpi = $this->get_hpi_categories($scid);
				foreach($ar_chpi as $k2=>$arhpiv){
					//$hpi = $arhpiv["hpi"];
					//$id = $arhpiv["id"];
					$ar[$cat][$subcat][]=$arhpiv;
				}
			}
		}
		return $ar;
	}
	
	function get_hpi(){
		$ar_custom_hpi = $this->get_custom_hpi();
		$echo="";
		if(count($ar_custom_hpi)>0){
			foreach($ar_custom_hpi as $cat => $ar_c){
				$fc=1;
				foreach($ar_c as $sbcat => $ar_v){
					$fsc=1;
					foreach($ar_v as $k => $av){
						if(empty($fc)){ $cat="";  }
						if(empty($fsc)){ $sbcat="";  }
						$tid=$av["id"];
						$thpi=$av["hpi"];									
						$echo .= "
							<tr>
								<td><input type=\"checkbox\" name=\"el_hpi_id[]\" id=\"el_hpi_id".$k."\" value=\"".$tid."\" ></td>
								<td>".$cat."</td>
								<td>".$sbcat."</td>
								<td><a href=\"#\" onclick=\"edit(this)\">".$thpi."</a></td>
							</tr>
						";
						$fsc=0;
						$fc=0;
					}
				}
			}
			echo $echo;
		}
	}
	
	function get_hpi_categories($lvl=0){
		$ar=array();
		$sql = "SELECT id, hpi FROM chart_hpi WHERE del='0' ".
			" AND cat='".$lvl."' ";
		$rez = sqlStatement($sql);
		for($i=0; $row=sqlFetchArray($rez);$i++){
			$ar[] = $row;
		}
		return $ar; 	
	}
	
	function get_subcats_opts($cat=""){
		$opt="";
		$lvl=$_GET["lvl"];
		if(!empty($cat)){$lvl=$cat;}
		$ar = $this->get_hpi_categories($lvl);
		$l=count($ar);
		if($l>0){	
			foreach($ar as $k => $ar_sb){			
				$opt.="<option value=\"".$ar_sb["id"]."\">".$ar_sb["hpi"]."</option>";			
			}
			$opt = "<option value=\"\">-Select-</option>".$opt;	
		}
		if(!empty($cat)){return $opt;}
		else{	echo $opt; }
	}
	
	function is_duplicate($hpi, $cat, $eid){
		$ret=true;
		//Check of duplicate
		$sql = "SELECT count(id) as num FROM chart_hpi WHERE hpi='".sqlEscStr($hpi)."' AND cat='".$cat."' AND del='0'  ";
		if(!empty($eid)){
			$sql .= " AND id!='".$eid."' ";
		}
		$row = sqlQuery($sql);
		if($row==false || $row["num"]==0){
			$ret=false;
		}
		return $ret;
	}
	
	function save(){
		$task = $_POST["task"];
		$op = $_POST["op"];
		
		if(!empty($op) && $op=="del"){		
			$did = $_POST["did"];
			$did = trim($did);
			$did = trim($did,",");
			if(!empty($did)){
				$sql = "UPDATE `chart_hpi` SET del='1', op_by='".$_SESSION["authId"]."', op_time='".wv_dt("now")."' WHERE id IN (".$did.") ";
				$row=sqlQuery($sql);
			}
		}else{	
		
			$el_cat_name = $_POST["el_cat_name"];
			$el_subcat_name = $_POST["el_subcat_name"];		
			if(!empty($el_subcat_name) && !empty($el_cat_name)){
				$i=0;
				
				while(true){
					$i++;
					if(!isset($_POST["el_hpi".$i])||$i>50){ break; }					
					
					$el_edid = $_POST["el_edid".$i];				
					if(empty($_POST["el_hpi".$i])){ continue;}				
					
					//
					if($this->is_duplicate($_POST["el_hpi".$i], $el_subcat_name, $el_edid)){ continue; }
					
					if(!empty($el_edid)){
						//update
						//insert
						$sql = "UPDATE `chart_hpi` SET hpi='".sqlEscStr($_POST["el_hpi".$i])."', cat='".$el_subcat_name."', op_by='".$_SESSION["authId"]."', op_time='".wv_dt("now")."' WHERE id='".$el_edid."'";
						$row=sqlQuery($sql);
					}else{
						//insert
						$sql = "INSERT INTO `chart_hpi` (`id`, `hpi`, `cat`, `op_by`, `op_time`, `del`) VALUES (NULL, '".sqlEscStr($_POST["el_hpi".$i])."', '".$el_subcat_name."', '".$_SESSION["authId"]."', '".wv_dt("now")."', '0');";
						$row=sqlQuery($sql);
					}	
				}
			}//
		
		}
		
		echo "0";
	}
	
	function get_wv_hpi($ar_wv_vals){
	
		$ar_wv_nm=array("Vision Problem"=>array("Distance"=>array("elem_vpDis","Vision Problem"),"Near"=>array("elem_vpNear","Vision Problem"),"Glare"=>array("elem_vpGlare","Glare Problem"),"Mid Distance"=>array("elem_vpMidDis","Vision Problem"),"Other"=>array("elem_vpOther","Other Vision Problem")),
						"Irritation"=>array("Lids - External"=>array("elem_irrLidsExt","Irritation Lids"), "Ocular"=>array("elem_irrOcu","Irritation Ocular")),
						"Post Segment"=>array("Flashing Lights"=>array("elem_postSegFL","Flashing Lights"), "Floaters"=>array("elem_postSegFloat","Floaters"), "Amsler Grid"=>array("elem_postSegAmsler","Amsler Grid")),
						"Neuro"=>array("Double Vision"=>array("elem_neuroDblVis","Double vision"), "Temporal Arteritis Symptoms"=>array("elem_neuroTAS","Temporal Arteritis symptoms"), "Headaches"=>array("elem_neuroHeadaches","Headaches"), "Migraine Headaches"=>array("elem_neuroMigHeadAura","Migraine Headaches"), "Loss of Vision"=>array("elem_neuroVisLoss","Loss of vision")),
						"Follow-up"=>array("Post-op"=>array("elem_fuPostOp","Post op"), "Follow-up"=>array("elem_fuFollowUp","Follow Up"))
					);
		
		
		$ar=array();
		$ar_custom_hpi = $this->get_custom_hpi();
	
		if(count($ar_custom_hpi)>0){
			foreach($ar_custom_hpi as $cat => $ar_c){
				
				foreach($ar_c as $sbcat => $ar_v){
					$el_nm=$ar_wv_nm[$cat][$sbcat][0];
					$arg_cat=$ar_wv_nm[$cat][$sbcat][1];
					$el_val=$ar_wv_vals[$cat][$sbcat];
					$ht="";
					foreach($ar_v as $k => $av){
						
						$tid=$av["id"];
						$thpi=$av["hpi"];									
						$el_id="chpi".$tid;				
						
						$chk = (in_array($thpi,$el_val)) ? " checked=\"checked\" " : ""; //Checked						
						$ht .= "
							<input id=\"".$el_id."\" type=\"checkbox\" name=\"".$el_nm."[]\" value=\"".$thpi."\" onclick=\"setRVS(this,'-".$arg_cat."')\" ".$chk." >
							<label for=\"".$el_id."\">".$thpi."</label>
						";
						
					}
					$ar[$cat][$sbcat]=$ht;
				}
			}
			
		}
		
		return $ar;
	}
	
	function get_record_inf(){
		$id = $_GET["id"];
		$ar=array();
		$sql = "SELECT id, hpi, cat FROM chart_hpi WHERE id='".$id."' ";			
		$row = sqlQuery($sql);
		if($row){
			$id=$row["id"];
			$hpi=$row["hpi"];
			$sub_cat=$row["cat"];
			
			//
			$sql = "SELECT cat FROM chart_hpi WHERE id='".$sub_cat."'";
			$row = sqlQuery($sql);
			if($row!=false){
				$cat=$row["cat"];			
			}
			
			//get cat options
			$opt = $this->get_subcats_opts($cat);
			
			$ar["id"] =$id;
			$ar["hpi"] =$hpi;
			$ar["sub_cat"] =$sub_cat;
			$ar["cat"] =$cat;
			$ar["opt"] =$opt;		
		}
		echo json_encode($ar);
	}
}
?>