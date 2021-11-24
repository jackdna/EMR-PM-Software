<?php
class Modifier{
	function getModifierCode($id)
	{
		$modCode = "";
		$sql = "SELECT modifier_code FROM modifiers_tbl
				WHERE modifiers_id='".$id."' AND delete_status = '0'";
		$row = sqlQuery($sql);
		if($row != false)
		{
			$modCode = $row["modifier_code"];
		}
		return $modCode;
	}
	
	function getModifierInfo($md){
		$arr =  array();
		$modDescStr="SELECT * FROM modifiers_tbl WHERE modifier_code='".$md."' AND delete_status = '0'";
		$row=sqlQuery($modDescStr);
		if($row!=false){
			$arr["desc"]=$row['mod_description'];
			$arr["prac_code"]=$row['mod_prac_code'];
		}
		return $arr; 
	}
	
	function find_md(){
		$_POST["elem_desc"] = urldecode($_POST["elem_desc"]);
		$_POST["elem_desc"]=sqlEscStr($_POST["elem_desc"]);
		$ar = array();
		$sql = "SELECT modifier_code,mod_description FROM modifiers_tbl ".
			 "WHERE (LOWER(modifier_code)=LOWER('".$_POST["elem_desc"]."') || 
				LOWER(REPLACE(mod_description,'\r\n',''))=LOWER('".$_POST["elem_desc"]."') || 
				LOWER(mod_prac_code)=LOWER('".$_POST["elem_desc"]."'))
			 AND delete_status = '0'";
		$row=sqlQuery($sql);
		if($row != false){
			$ar["code"] = $row["modifier_code"];
			$ar["desc"] = $row["mod_description"];
		}
		echo json_encode($ar);	
	}
	
	function get_mod_prac_code(){
		$q_mod='SELECT concat(mod_prac_code,"; ") as mod_prac_code FROM modifiers_tbl WHERE delete_status=0';
		$r_mod=sqlStatement($q_mod);
		$arr_mod=array();
		while($row_mod=sqlFetchArray($r_mod)){
			$arr_mod[]=$row_mod['mod_prac_code'];
		}
		return $arr_mod;
	}
	
	function get_menu_html(){
		$q_mod='SELECT mod_prac_code, mod_description FROM modifiers_tbl WHERE delete_status=0 ORDER BY mod_prac_code';
		$r_mod=sqlStatement($q_mod);
		$str="";
		while($row=sqlFetchArray($r_mod)){
			if(!empty($row["mod_prac_code"])){$str.="<li><a href=\"#\" data-val=\"".$row["mod_prac_code"]."\">".$row["mod_prac_code"]." - ".$row["mod_description"]."</a></li>";}
		}
		
		if(!empty($str)){
			$str = "<ul class=\"dropdown-menu dropdown-menu-right menu_mod_ul \">".$str."</ul>";
		}
		
		echo $str;	
	}
}
?>