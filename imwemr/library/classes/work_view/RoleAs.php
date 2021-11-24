<?php
/*
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
*/
?>
<?php
/*
File: RoleAs.php
Coded in PHP7
Purpose: This is a class file for change Role of a user.
Access Type : Include file
*/
?>
<?php
//RoleAs.php
class RoleAs extends User{	
	public function __construct($uid=""){
		parent::__construct($uid);		
	}
	
	function change_role(){
		$cr2 = $_GET["cr2"];
		$pto = $_GET["pto"];		
		$ut = $this->getUType(0,1);
		
		if(!empty($pto)){
			if(isset($_SESSION["user_role"]) && !empty($_SESSION["user_role"])){
				$_SESSION["sess_user_role"]=$_SESSION["user_role"];	
			}else{
				$_SESSION["sess_user_role"]="0";
			}
		}	
		
		if($ut!=$cr2){
			$_SESSION["user_role"] = $cr2;			
		}else{
			if(isset($_SESSION["user_role"]) && !empty($_SESSION["user_role"])){
				$_SESSION["user_role"]="";
				unset($_SESSION["user_role"]);
			}			
		}
	}
	
	function reset_user_role_ptonly(){
	
		if(isset($_SESSION["sess_user_role"])){
			if(isset($_SESSION["sess_user_role"]) && !empty($_SESSION["sess_user_role"])){				
				$_SESSION["user_role"]=$_SESSION["sess_user_role"];
			}else{
				if(isset($_SESSION["user_role"]) && !empty($_SESSION["user_role"])){					
					$_SESSION["user_role"]="";
					unset($_SESSION["user_role"]);
				}
			}			
			
			$_SESSION["sess_user_role"]="";
			unset($_SESSION["sess_user_role"]);			
		}	
		
	}

	function save_role($fid){
		
		if($_SESSION["logged_user_type"] == "3" || $_SESSION["logged_user_type"] == "13"){
		
		if(!empty($fid)){
			$user_type = (isset($_SESSION["user_role"]) && !empty($_SESSION["user_role"])) ? $_SESSION["user_role"] : $_SESSION["logged_user_type"];
			
			$sql = "SELECT id from chart_usr_roles where del_by=0 and uid='".$_SESSION["authId"]."' AND role_type='".$user_type."' AND form_id='".$fid."' ";
			$row = sqlQuery($sql);
			if($row!=false){
				//
				$id = $row["id"]; 
				$sql = "UPDATE chart_usr_roles SET modi_dt='".wv_dt('now')."' WHERE id = '".$id."' ";
				sqlQuery($sql);
			}else{
				$sql = "INSERT INTO chart_usr_roles (id, uid, role_type, logged_usr_type, create_dt, form_id)  ".
						"VALUES(NULL, '".$_SESSION["authId"]."', '".$user_type."', '".$_SESSION["logged_user_type"]."', '".wv_dt('now')."', '".$fid."') ";
				sqlQuery($sql);				
			}
		}

		}		
	}
	
	function get_care_giver_colors($fid){
		$arr_ret = array();
		$sql = "SELECT c1.uid, c1.role_type, c3.user_type_name, 
					c3.color, c2.fname, c2.mname, c2.lname, c2.id 
				from chart_usr_roles c1 
				INNER JOIN users c2 ON c2.id = c1.uid
				INNER JOIN user_type c3 ON c3.user_type_id = c1.role_type
				where c1.del_by=0 and c1.form_id='".$fid."' ";
		$rez = sqlStatement($sql);
		for($i=0;$row=sqlFetchArray($rez);$i++){
			
			//$ret[] = array($row["uid"], $row["role_type"], $row["user_type_name"]);
			
			$nm="";
			if(!empty($row["fname"])){$nm.="".$row["fname"]." ";}
			if(!empty($row["mname"])){$nm.="".$row["mname"]." ";}
			if(!empty($row["lname"])){$nm.="".$row["lname"]." ";}
			$nm=trim($nm);
			
			$type = $row["user_type_name"];		
			$type_abb = substr($type,0,4).".";
			$color=$row["color"];
			
			$color1 = $color2 = "";
			if(strpos($color,",")!==false){ $tmp=explode(",", $color); $color1 = $tmp[0];$color2 = $tmp[1]; }else{ $color2=$color; }
			
			//clickable
			$cls = ( ($arr_ret[$i]["type"] == "3" || $arr_ret[$i]["type"] == "13") || $row["id"] == $_SESSION["authId"]) ? "clickable" : "";
			
			$arr_ret[$i]["id"]=$row["id"];
			$arr_ret[$i]["name"]=$nm;
			$arr_ret[$i]["type"]=$type_abb;
			$arr_ret[$i]["color1"]=$color1;
			$arr_ret[$i]["color2"]=$color2;
			$arr_ret[$i]["clickable"]=$cls;	
		}
		return $arr_ret;	
	}

	function get_user_roles($fid){
		$ret=array();
		if(!empty($this->uid) && !empty($fid)){
			$sql = "SELECT c1.role_type 
					from chart_usr_roles c1 
					INNER JOIN users c2 ON c2.id = c1.uid
					where c1.del_by=0 and c1.form_id='".$fid."' AND uid=".$this->uid." ";
			$rez = sqlStatement($sql);
			for($i=0;$row=sqlFetchArray($rez);$i++){
				$ret[] = $row["role_type"]; 
			}
		}
		return $ret;
	}
}

?>