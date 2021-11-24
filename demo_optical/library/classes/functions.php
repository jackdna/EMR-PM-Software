<?php
// last updated : 8/1/2018 4:12PM G
	function query($qry)
	{
		return imw_query($qry);	
	}
	function saveDateFormat($date)
	{
		$mdate = array();
		$mdate = explode("-",$date);
		$year_val=$mdate[2];
		$month_val=$mdate[0];
		$date_val=$mdate[1];		
		return $year_val."-".$month_val."-".$date_val;
	}
	function getDateFormat($date)
	{
		$mdate = array();
		$mdate = explode("-",$date);
		$year_val=$mdate[0];
		$month_val=$mdate[1];
		$date_val=$mdate[2];		
		return $month_val."-".$date_val."-".$year_val;
	}	
	function data($query)
	{
		$r = array(); 
		$q = query($query);	
		while($row = imw_fetch_array($q))
		{
			$r[] = $row;
		}
		return $r;
	}
	
	function getPatient_Data($id)
	{
		$sql = "SELECT * FROM patient_data WHERE id = '$id' LIMIT 1";
		$row = sqlQuery($sql);
		if($row != false)
		{
			return $row;
		}	   
		else
		{
			return false;
		}
	}
	
	function getResp_Data($id)
	{
		$sql = "SELECT * FROM resp_party WHERE patient_id = '$id' LIMIT 1";
		$row = sqlQuery($sql);
		if($row != false)
		{
			return $row;
		}	   
		else
		{
			return false;
		}
	}
	
	function get_insurance_company($id)
	{
		$qry_ins_name= "select * from insurance_companies where id='".$id."' and ins_del_status='0' LIMIT 1";
		$row_ins_name = sqlQuery($qry_ins_name);
		if($row_ins_name != false)
		{
			return $row_ins_name;
		}	   
		else
		{
			return false;
		}
	}
	
	function format_phone($phone)
	{
		$phone = trim($phone);
		$phone = preg_replace("/[^0-9]/", "", $phone);
	
		if(strlen($phone) == 7) 
		{			
			return preg_replace("/([0-9]{3})([0-9]{4})/", "$1-$2", $phone);
		}
		elseif(strlen($phone) == 10)
		{
			return preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "($1) $2-$3", $phone);
		}
		elseif(strlen($phone) == 11)
		{
			return preg_replace("/([0-9]{1})([0-9]{3})([0-9]{3})([0-9]{4})/", "$1 ($2) $3-$4", $phone);
		}
		else
		{
			return $phone;
		}
	}
	
	function uploadfile($img,$path,$id,$table)
	{
			$allowedExts = array("gif", "jpeg", "jpg", "png");
			$temp = explode(".", $img["name"]);
			$extension = end($temp);
			
			if ((($img["type"] == "image/gif")
			|| ($img["type"] == "image/jpeg")
			|| ($img["type"] == "image/jpg")
			|| ($img["type"] == "image/pjpeg")
			|| ($img["type"] == "image/x-png")
			|| ($img["type"] == "image/png"))
			&& in_array($extension, $allowedExts))
			  {
				  if ($img["error"] > 0)
				  {
					echo "Return Code: " . $img["error"] . "<br>";
				  }
			  	  else
				  {
					$upc_code_qry=data("select upc_code from in_item where id = '".$id."'");
					foreach($upc_code_qry as $upc_code_name)
					{
						$upcCodeName = $upc_code_name['upc_code'];
					}  
					  
				    //$imgg = explode(".".$extension,$img["name"]);
					
					$iname = $upcCodeName."_".$id.".".$extension;
					move_uploaded_file($img["tmp_name"],$path.$iname);			
					imw_query("update ".$table." set stock_image = '".$iname."' where id = '".$id."' ");
				  }
			  }
			else
			  {
			  	echo "Invalid file";
			  }
			  return $iname;
	}

function contact_lens_stock($id,$manufacturer,$upc_name,$item_prac_code,$module_type,$name,$vendor,$bc,$diameter,$sphere_min,$cyl_min,$axis_min,$sphere_max,$cyl_max,$axis_max,$r_check,$l_check,$cat_name,$brand_name,$type,$replacement,$supply,$color,$dot,$style,$qty_on_hand,$amount,$retail_price,$discount,$disc_date,$trial_check,$lens_type,$lens_packaging,$formula_save,$retailpriceFlag)
{
	
	$opr_id = $_SESSION['authId'];
	$date = date("Y-m-d");
	$time = date("h:i:s");
	
	$lens_type = implode(",",$lens_type);
	$cat_name = implode(",",$cat_name);
	$replacement = implode(",",$replacement);
	$type = implode(",",$type);
	$supply = implode(",",$supply);
	
	$item_prac_code_qry="";
	$procedureId = back_prac_id($item_prac_code, false, $module_type);
	$item_prac_code_qry=",item_prac_code='".$procedureId."'";
		
		if($r_check=="on")
		{ $r_check="1"; }
		if($l_check=="on")
		{ $l_check="1"; }
		if($dot=="on")
		{ $dot="1"; }
		if($trial_check=="on")
		{ $trial_check="1"; }
		if($hazardous=="on")
		{ $hazardous="1"; }
		if($id=="")
		{
			$qry = "insert into in_item set
			manufacturer_id = '".imw_real_escape_string($manufacturer)."',
			module_type_id	= '".imw_real_escape_string($module_type)."',
			upc_code = '".imw_real_escape_string($upc_name)."',
			name = '".imw_real_escape_string($name)."',
			vendor_id = '".imw_real_escape_string($vendor)."',
			brand_id = '".imw_real_escape_string($brand_name)."',			
			bc = '".imw_real_escape_string($bc)."',
			diameter = '".imw_real_escape_string($diameter)."',
			sphere_positive = '".imw_real_escape_string($sphere_min)."',
			cylindep_positive = '".imw_real_escape_string($cyl_min)."',
			axis = '".imw_real_escape_string($axis_min)."',
			sphere_positive_max = '".imw_real_escape_string($sphere_max)."',
			cylindep_positive_max = '".imw_real_escape_string($cyl_max)."',
			axis_max = '".imw_real_escape_string($axis_max)."',
			other = '".imw_real_escape_string($other)."',
			r_check = '".imw_real_escape_string($r_check)."',
			l_check = '".imw_real_escape_string($l_check)."',
			cl_wear_schedule = '".imw_real_escape_string($cat_name)."',
			cl_replacement = '".imw_real_escape_string($replacement)."',
			type_id = '".imw_real_escape_string($type)."',
			supply_id = '".imw_real_escape_string($supply)."',
			color = '".imw_real_escape_string($color)."',
			dot = '".imw_real_escape_string($dot)."',
			trial_chk = '".imw_real_escape_string($trial_check)."',
			style = '".imw_real_escape_string($style)."',
			qty_on_hand = '".imw_real_escape_string($qty_on_hand)."',
			amount = '".imw_real_escape_string($amount)."',
			retail_price = '".imw_real_escape_string($retail_price)."',
			discount = '".imw_real_escape_string($discount)."',
			discount_till = '".imw_real_escape_string(saveDateFormat($disc_date))."',
			cl_type = '".imw_real_escape_string($lens_type)."',
			cl_packaging = '".imw_real_escape_string($lens_packaging)."',
			formula = '".imw_real_escape_string($formula_save)."',
			retail_price_flag = '".((bool)$retailpriceFlag)."',
			entered_date='$date', entered_time='$time', entered_by='$opr_id'
			$item_prac_code_qry";
			imw_query($qry);
			$id = imw_insert_id();
			
			if(!empty($_FILES['file']['name']))
			{
				uploadfile($_FILES['file'],"../../../images/contact_lens_stock/",$id,"in_item");
			}
		}
		else
		{
			$qry = "update in_item set
			manufacturer_id = '".imw_real_escape_string($manufacturer)."',
			module_type_id	= '".imw_real_escape_string($module_type)."',
			upc_code = '".imw_real_escape_string($upc_name)."',
			name = '".imw_real_escape_string($name)."',
			vendor_id = '".imw_real_escape_string($vendor)."',
			brand_id = '".imw_real_escape_string($brand_name)."',			
			bc = '".imw_real_escape_string($bc)."',
			diameter = '".imw_real_escape_string($diameter)."',
			sphere_positive = '".imw_real_escape_string($sphere_min)."',
			cylindep_positive = '".imw_real_escape_string($cyl_min)."',
			axis = '".imw_real_escape_string($axis_min)."',
			sphere_positive_max = '".imw_real_escape_string($sphere_max)."',
			cylindep_positive_max = '".imw_real_escape_string($cyl_max)."',
			axis_max = '".imw_real_escape_string($axis_max)."',
			other = '".imw_real_escape_string($other)."',
			r_check = '".imw_real_escape_string($r_check)."',
			l_check = '".imw_real_escape_string($l_check)."',
			cl_wear_schedule = '".imw_real_escape_string($cat_name)."',
			cl_replacement = '".imw_real_escape_string($replacement)."',
			type_id = '".imw_real_escape_string($type)."',
			supply_id = '".imw_real_escape_string($supply)."',
			color = '".imw_real_escape_string($color)."',
			dot = '".imw_real_escape_string($dot)."',
			trial_chk = '".imw_real_escape_string($trial_check)."',
			style = '".imw_real_escape_string($style)."',
			qty_on_hand = '".imw_real_escape_string($qty_on_hand)."',
			amount = '".imw_real_escape_string($amount)."',
			retail_price = '".imw_real_escape_string($retail_price)."',
			discount = '".imw_real_escape_string($discount)."',
			discount_till = '".imw_real_escape_string(saveDateFormat($disc_date))."',
			cl_type = '".imw_real_escape_string($lens_type)."',
			cl_packaging = '".imw_real_escape_string($lens_packaging)."',
			formula = '".imw_real_escape_string($formula_save)."',
			retail_price_flag = '".((bool)$retailpriceFlag)."',
			modified_date='$date', modified_time='$time', modified_by='$opr_id'
			$item_prac_code_qry where id = '".$id."'
			";
			imw_query($qry);
			
			if(!empty($_FILES['file']['name']))
			{
				uploadfile($_FILES['file'],"../../../images/contact_lens_stock/",$id,"in_item");
			}			
		}
		if(trim($upc_name)=="")
		{
			$sel_upc_num = imw_query("select id, upc_num from in_upc_no");
			$fetch_upc_no = imw_fetch_array($sel_upc_num);
			$upc_num = $fetch_upc_no['upc_num'];
			$prt=0;
			for($pr=0;$prt==0;$pr++)
			{
				$sel_frm_item = imw_query("select id from in_item where upc_code='$upc_num'");
				if(imw_num_rows($sel_frm_item)>0)
				{
					$upc_num=$upc_num+1;
				}
				else
				{
					$prt=1;
				}
			}
			$upd_itm = imw_query("update in_item set upc_code='$upc_num', modified_date='$date', modified_time='$time', modified_by='$opr_id' where id='$id'");
			
			$new_upc_no = $upc_num+1;
			$upd_upc_no = imw_query("update in_upc_no set upc_num='$new_upc_no' where id='".$fetch_upc_no['id']."'");
		}
	return $id;
}


function supply_stock($id,$manufacturer,$upc_name,$item_prac_code,$module_type,$name,$vendor,$type_desc,$num_size,$measurement,$char_size,$other,$hazardous,$qty_on_hand,$amount,$retail_price,$discount,$disc_date,$formula_save,$retailpriceFlag)
{
	$opr_id = $_SESSION['authId'];
	$date = date("Y-m-d");
	$time = date("h:i:s");
	$item_prac_code_qry="";
	$procedureId = back_prac_id($item_prac_code);
	$item_prac_code_qry=",item_prac_code='".$procedureId."'";
		
	if($hazardous=="on")
	{ $hazardous="1"; }
	
	if($id=="")
	{
		$qry = "insert into in_item set
		manufacturer_id = '".imw_real_escape_string($manufacturer)."',
		module_type_id	= '".imw_real_escape_string($module_type)."',
		upc_code = '".imw_real_escape_string($upc_name)."',
		name = '".imw_real_escape_string($name)."',
		vendor_id = '".imw_real_escape_string($vendor)."',
		num_size = '".imw_real_escape_string($num_size)."',
		measurment = '".imw_real_escape_string($measurement)."',
		char_size = '".imw_real_escape_string($char_size)."',
		other = '".imw_real_escape_string($other)."',
		retail_price = '".imw_real_escape_string($retail_price)."',
		discount = '".imw_real_escape_string($discount)."',
		discount_till = '".imw_real_escape_string(saveDateFormat($disc_date))."',
		type_desc = '".imw_real_escape_string($type_desc)."',
		harcardous = '".imw_real_escape_string($hazardous)."',
		qty_on_hand = '".imw_real_escape_string($qty_on_hand)."',
		amount = '".imw_real_escape_string($amount)."',
		formula = '".imw_real_escape_string($formula_save)."',
		retail_price_flag = '".((bool)$retailpriceFlag)."',
		entered_date='$date', entered_time='$time', entered_by='$opr_id'
		$item_prac_code_qry ";
		
		imw_query($qry);
		$id = imw_insert_id();
		
		if(!empty($_FILES['file']['name']))
		{
			uploadfile($_FILES['file'],"../../../images/supply_stock/",$id,"in_item");
		}			
	}
	else
	{
		$qry = "update in_item set
		manufacturer_id = '".imw_real_escape_string($manufacturer)."',
		module_type_id	= '".imw_real_escape_string($module_type)."',
		upc_code = '".imw_real_escape_string($upc_name)."',
		name = '".imw_real_escape_string($name)."',
		vendor_id = '".imw_real_escape_string($vendor)."',
		num_size = '".imw_real_escape_string($num_size)."',
		measurment = '".imw_real_escape_string($measurement)."',
		char_size = '".imw_real_escape_string($char_size)."',
		other = '".imw_real_escape_string($other)."',
		retail_price = '".imw_real_escape_string($retail_price)."',
		discount = '".imw_real_escape_string($discount)."',
		discount_till = '".imw_real_escape_string(saveDateFormat($disc_date))."',
		type_desc = '".imw_real_escape_string($type_desc)."',
		harcardous = '".imw_real_escape_string($hazardous)."',
		qty_on_hand = '".imw_real_escape_string($qty_on_hand)."',
		amount = '".imw_real_escape_string($amount)."',
		formula = '".imw_real_escape_string($formula_save)."',
		retail_price_flag = '".((bool)$retailpriceFlag)."',
		modified_date='$date', modified_time='$time', modified_by='$opr_id'
		$item_prac_code_qry 
		where id = '".imw_real_escape_string($id)."'";
		imw_query($qry);

		
		if(!empty($_FILES['file']['name']))
		{
			uploadfile($_FILES['file'],"../../../images/supply_stock/",$id,"in_item");
		}			
	}
	if(trim($upc_name)=="")
	{
		$sel_upc_num = imw_query("select id, upc_num from in_upc_no");
		$fetch_upc_no = imw_fetch_array($sel_upc_num);
		$upc_num = $fetch_upc_no['upc_num'];
		$prt=0;
		for($pr=0;$prt==0;$pr++)
		{
			$sel_frm_item = imw_query("select id from in_item where upc_code='$upc_num'");
			if(imw_num_rows($sel_frm_item)>0)
			{
				$upc_num=$upc_num+1;
			}
			else
			{
				$prt=1;
			}
		}
		$upd_itm = imw_query("update in_item set upc_code='$upc_num', modified_date='$date', modified_time='$time', modified_by='$opr_id' where id='$id'");
		
		$new_upc_no = $upc_num+1;
		$upd_upc_no = imw_query("update in_upc_no set upc_num='$new_upc_no' where id='".$fetch_upc_no['id']."'");
	}
	return $id;
}

/*function medicine_stock($id,$manufacturer,$upc_name,$item_prac_code,$module_type,$name,$vendor,$type_desc,$num_size,$measurement,$char_size,$other,$hazardous,$qty_on_hand,$amount,$wholesale_cost,$retail_price,$discount,$disc_date,$units,$dosage,$med_typ,$ndc,$pay_by,$fee)
{
	$opr_id = $_SESSION['authId'];
	$date = date("Y-m-d");
	$time = date("h:i:s");
	$item_prac_code_qry="";
	$procedureId = back_prac_id($item_prac_code);
	$item_prac_code_qry=",item_prac_code='".$procedureId."'";
		
		if($hazardous=="on")
		{ $hazardous="1"; }
		
		$med_typ=implode(',',$med_typ);
		
		if($id=="")
		{
			$qry = "insert into in_item set
			manufacturer_id = '".imw_real_escape_string($manufacturer)."',
			module_type_id	= '".imw_real_escape_string($module_type)."',
			upc_code = '".imw_real_escape_string($upc_name)."',
			name = '".imw_real_escape_string($name)."',
			vendor_id = '".imw_real_escape_string($vendor)."',
			num_size = '".imw_real_escape_string($num_size)."',
			measurment = '".imw_real_escape_string($measurement)."',
			char_size = '".imw_real_escape_string($char_size)."',
			other = '".imw_real_escape_string($other)."',
			wholesale_cost = '".imw_real_escape_string($wholesale_cost)."',
			retail_price = '".imw_real_escape_string($retail_price)."',
			discount = '".imw_real_escape_string($discount)."',
			discount_till = '".imw_real_escape_string(saveDateFormat($disc_date))."',
			type_desc = '".imw_real_escape_string($type_desc)."',
			harcardous = '".imw_real_escape_string($hazardous)."',
			qty_on_hand = '".imw_real_escape_string($qty_on_hand)."',
			amount = '".imw_real_escape_string($amount)."',
			units = '".imw_real_escape_string($units)."',
			dosage = '".imw_real_escape_string($dosage)."',
			med_typ = '".imw_real_escape_string($med_typ)."',
			ndc = '".imw_real_escape_string($ndc)."',
			pay_by = '".imw_real_escape_string($pay_by)."',
			fee = '".imw_real_escape_string($fee)."',
			entered_date='$date', entered_time='$time', entered_by='$opr_id'
			$item_prac_code_qry
			";
			
			imw_query($qry);
			$id = imw_insert_id();
	
			if(!empty($_FILES['file']['name']))
			{
				uploadfile($_FILES['file'],"../../../images/medicine_stock/",$id,"in_item");
			}
					
		}else{
			$qry = "update in_item set
			manufacturer_id = '".imw_real_escape_string($manufacturer)."',
			module_type_id	= '".imw_real_escape_string($module_type)."',
			upc_code = '".imw_real_escape_string($upc_name)."',
			name = '".imw_real_escape_string($name)."',
			vendor_id = '".imw_real_escape_string($vendor)."',
			num_size = '".imw_real_escape_string($num_size)."',
			measurment = '".imw_real_escape_string($measurement)."',
			char_size = '".imw_real_escape_string($char_size)."',
			other = '".imw_real_escape_string($other)."',
			wholesale_cost = '".imw_real_escape_string($wholesale_cost)."',
			retail_price = '".imw_real_escape_string($retail_price)."',
			discount = '".imw_real_escape_string($discount)."',
			discount_till = '".imw_real_escape_string(saveDateFormat($disc_date))."',
			type_desc = '".imw_real_escape_string($type_desc)."',
			harcardous = '".imw_real_escape_string($hazardous)."',
			qty_on_hand = '".imw_real_escape_string($qty_on_hand)."',
			amount = '".imw_real_escape_string($amount)."',
			units = '".imw_real_escape_string($units)."',
			dosage = '".imw_real_escape_string($dosage)."',
			med_typ = '".imw_real_escape_string($med_typ)."',
			ndc = '".imw_real_escape_string($ndc)."',
			pay_by = '".imw_real_escape_string($pay_by)."',
			fee = '".imw_real_escape_string($fee)."',
			modified_date='$date', modified_time='$time', modified_by='$opr_id'
			$item_prac_code_qry
			where id = '".imw_real_escape_string($id)."'";
		
			imw_query($qry);
			$lastID = imw_insert_id();
	
			if(!empty($_FILES['file']['name']))
			{
				uploadfile($_FILES['file'],"../../../images/medicine_stock/",$id,"in_item");
			}			
		}
		if(trim($upc_name)=="")
		{
			$sel_upc_num = imw_query("select id, upc_num from in_upc_no");
			$fetch_upc_no = imw_fetch_array($sel_upc_num);
			$upc_num = $fetch_upc_no['upc_num'];
			$prt=0;
			for($pr=0;$prt==0;$pr++)
			{
				$sel_frm_item = imw_query("select id from in_item where upc_code='$upc_num'");
				if(imw_num_rows($sel_frm_item)>0)
				{
					$upc_num=$upc_num+1;
				}
				else
				{
					$prt=1;
				}
			}
			$upd_itm = imw_query("update in_item set upc_code='$upc_num', modified_date='$date', modified_time='$time', modified_by='$opr_id' where id='$id'");
			
			$new_upc_no = $upc_num+1;
			$upd_upc_no = imw_query("update in_upc_no set upc_num='$new_upc_no' where id='".$fetch_upc_no['id']."'");
		}
}*/

function medicine_stock()
{
	$opr_id = $_SESSION['authId'];
	$date = date("Y-m-d");
	$time = date("h:i:s");
	if($_POST['hazardous']=="on")
	{ $hazardous="1"; }
	$med_typ=implode(',',$_POST['med_typ']);
		
	$parent_id = false;
	$parent_qry = "";
	//common fields
	$strQ=" manufacturer_id = '".imw_real_escape_string($_POST['manufacturer'])."',
			module_type_id	= '".imw_real_escape_string($_POST['module_type'])."',
			name = '".imw_real_escape_string($_POST['name'])."',
			vendor_id = '".imw_real_escape_string($_POST['vendor'])."',
			med_typ = '".imw_real_escape_string($_POST['med_typ'])."',
			pay_by = '".imw_real_escape_string($_POST['pay_by'])."',
			ndc = '".imw_real_escape_string($_POST['ndc'])."',
			fee = '".imw_real_escape_string($_POST['fee'])."',
			discount = '".imw_real_escape_string($_POST['discount'])."',
			discount_till = '".imw_real_escape_string(saveDateFormat($_POST['disc_date']))."',
			type_desc = '".imw_real_escape_string($_POST['type_desc'])."',
			harcardous = '".imw_real_escape_string($hazardous)."',
			formula = '".imw_real_escape_string($_POST['formula_save'])."'";
	
	for($i=1;$i<=$_POST['totRows'];$i++)
	{
		if($_POST["item_prac_code_$i"] || $_POST["upc_name_$i"])
		{
			if($_POST["edit_item_id_$i"]=="")
			{
				$procedureId = '';
				$procedureId = back_prac_id($_POST["item_prac_code_$i"]);
				
				$qry = "insert into in_item set
				upc_code = '".imw_real_escape_string($_POST["upc_name_$i"])."',
				item_prac_code='".$procedureId."',
				dx_code = '".imw_real_escape_string($_POST["dx_code_$i"])."',
				dosage = '".imw_real_escape_string($_POST["dosage_$i"])."',
				units = '".imw_real_escape_string($_POST["units_$i"])."',
				threshold = '".imw_real_escape_string($_POST["threshold_$i"])."',
				retail_price = '".imw_real_escape_string($_POST["retail_price_$i"])."',
				qty_on_hand = '".imw_real_escape_string($_POST["qty_on_hand_$i"])."',
				amount = '".imw_real_escape_string($_POST["amount_$i"])."',
				expiry_date = '".imw_real_escape_string(saveDateFormat($_POST["expiry_date_$i"]))."',
				del_status = '".imw_real_escape_string($_POST["del_item_id_$i"])."',
				retail_price_flag = '".imw_real_escape_string($_POST["retail_price_flag_$i"])."',
				entered_date='$date', 
				entered_time='$time', 
				entered_by='$opr_id',
				$parent_qry
				$strQ";
				
				imw_query($qry)or die(imw_error());
				$id = imw_insert_id();
				
				if($i==1 || !$parent_id){
					$parent_id = 1;
					$parent_qry = "parent_id=$id, ";
				}
				
				/*if(!empty($_FILES['file']['name']))
				{
					$uploadedFileName=uploadfile($_FILES['file'],"../../../images/medicine_stock/",$id,"in_item");
				}
				imw_query("update in_item set stock_image = '".$uploadedFileName."' where id = '".$id."' ");
				*/
			}else{
				
				$procedureId = '';
				$procedureId = back_prac_id($_POST["item_prac_code_$i"]);
				
				$qry = "update in_item set upc_code = '".imw_real_escape_string($_POST["upc_name_$i"])."',
				item_prac_code='".$procedureId."',
				dx_code = '".imw_real_escape_string($_POST["dx_code_$i"])."',
				dosage = '".imw_real_escape_string($_POST["dosage_$i"])."',
				units = '".imw_real_escape_string($_POST["units_$i"])."',
				retail_price = '".imw_real_escape_string($_POST["retail_price_$i"])."',
				threshold = '".imw_real_escape_string($_POST["threshold_$i"])."',
				qty_on_hand = '".imw_real_escape_string($_POST["qty_on_hand_$i"])."',
				amount = '".imw_real_escape_string($_POST["amount_$i"])."',
				expiry_date = '".imw_real_escape_string(saveDateFormat($_POST["expiry_date_$i"]))."',
				del_status = '".imw_real_escape_string($_POST["del_item_id_$i"])."',
				retail_price_flag = '".imw_real_escape_string($_POST["retail_price_flag_$i"])."',
				modified_date = '$date', 
				modified_time = '$time', 
				modified_by = '$opr_id',
				$strQ
				where id = '".imw_real_escape_string($_POST["edit_item_id_$i"])."'";
				imw_query($qry);
				$id = $_POST["edit_item_id_$i"];
				
				/*
				if(!empty($_FILES['file']['name']))
				{
					$uploadedFileName=uploadfile($_FILES['file'],"../../../images/medicine_stock/",$id,"in_item");
				}
				imw_query("update in_item set stock_image = '".$uploadedFileName."' where id = '".$id."' ");	
				*/
			}
			
			if(trim($_POST["upc_name_$i"])=="")
			{
				$sel_upc_num = imw_query("select id, upc_num from in_upc_no");
				$fetch_upc_no = imw_fetch_array($sel_upc_num);
				$upc_num = $fetch_upc_no['upc_num'];
				$prt=0;
				for($pr=0;$prt==0;$pr++)
				{
					$sel_frm_item = imw_query("select id from in_item where upc_code='$upc_num'");
					if(imw_num_rows($sel_frm_item)>0)
					{
						$upc_num=$upc_num+1;
					}
					else
					{
						$prt=1;
					}
				}
				$upd_itm = imw_query("update in_item set upc_code='$upc_num', modified_date='$date', modified_time='$time', modified_by='$opr_id' where id='$id'");
				
				$new_upc_no = $upc_num+1;
				$upd_upc_no = imw_query("update in_upc_no set upc_num='$new_upc_no' where id='".$fetch_upc_no['id']."'");
			}
		}
	}
}

function accessories_stock($id,$manufacturer,$upc_name,$item_prac_code,$module_type,$name,$vendor,$type_desc,$num_size,$measurement,$char_size,$other,$hazardous,$qty_on_hand,$amount,$retail_price,$discount,$disc_date)
{
	$opr_id = $_SESSION['authId'];
	$date = date("Y-m-d");
	$time = date("h:i:s");
	$item_prac_code_qry="";
	$procedureId = back_prac_id($item_prac_code);
	$item_prac_code_qry=",item_prac_code='".$procedureId."'";
		
		if($hazardous=="on")
		{ $hazardous="1"; }
		
		if($id=="")
		{
			$qry = "insert into in_item set
			manufacturer_id = '".imw_real_escape_string($manufacturer)."',
			module_type_id	= '".imw_real_escape_string($module_type)."',
			upc_code = '".imw_real_escape_string($upc_name)."',
			name = '".imw_real_escape_string($name)."',
			vendor_id = '".imw_real_escape_string($vendor)."',
			num_size = '".imw_real_escape_string($num_size)."',
			measurment = '".imw_real_escape_string($measurement)."',
			char_size = '".imw_real_escape_string($char_size)."',
			other = '".imw_real_escape_string($other)."',
			retail_price = '".imw_real_escape_string($retail_price)."',
			discount = '".imw_real_escape_string($discount)."',
			discount_till = '".imw_real_escape_string(saveDateFormat($disc_date))."',
			type_desc = '".imw_real_escape_string($type_desc)."',
			harcardous = '".imw_real_escape_string($hazardous)."',
			qty_on_hand = '".imw_real_escape_string($qty_on_hand)."',
			amount = '".imw_real_escape_string($amount)."',
			entered_date='$date', entered_time='$time', entered_by='$opr_id'
			$item_prac_code_qry ";
			imw_query($qry);
			$id = imw_insert_id();
	
			if(!empty($_FILES['file']['name']))
			{
				uploadfile($_FILES['file'],"../../../images/accessories_stock/",$id,"in_item");
			}					
		}else{
			$qry = "update in_item set
			manufacturer_id = '".imw_real_escape_string($manufacturer)."',
			module_type_id	= '".imw_real_escape_string($module_type)."',
			upc_code = '".imw_real_escape_string($upc_name)."',
			name = '".imw_real_escape_string($name)."',
			vendor_id = '".imw_real_escape_string($vendor)."',
			num_size = '".imw_real_escape_string($num_size)."',
			measurment = '".imw_real_escape_string($measurement)."',
			char_size = '".imw_real_escape_string($char_size)."',
			other = '".imw_real_escape_string($other)."',
			retail_price = '".imw_real_escape_string($retail_price)."',
			discount = '".imw_real_escape_string($discount)."',
			discount_till = '".imw_real_escape_string(saveDateFormat($disc_date))."',
			type_desc = '".imw_real_escape_string($type_desc)."',
			harcardous = '".imw_real_escape_string($hazardous)."',
			qty_on_hand = '".imw_real_escape_string($qty_on_hand)."',
			amount = '".imw_real_escape_string($amount)."',
			modified_date='$date', modified_time='$time', modified_by='$opr_id'
			$item_prac_code_qry 
			where id = '".imw_real_escape_string($id)."'";
		
			imw_query($qry);
			$lastID = imw_insert_id();
	
			if(!empty($_FILES['file']['name']))
			{
				uploadfile($_FILES['file'],"../../../images/accessories_stock/",$id,"in_item");
			}			
		}
		if(trim($upc_name)=="")
		{
			$sel_upc_num = imw_query("select id, upc_num from in_upc_no");
			$fetch_upc_no = imw_fetch_array($sel_upc_num);
			$upc_num = $fetch_upc_no['upc_num'];
			$prt=0;
			for($pr=0;$prt==0;$pr++)
			{
				$sel_frm_item = imw_query("select id from in_item where upc_code='$upc_num'");
				if(imw_num_rows($sel_frm_item)>0)
				{
					$upc_num=$upc_num+1;
				}
				else
				{
					$prt=1;
				}
			}
			$upd_itm = imw_query("update in_item set upc_code='$upc_num', modified_date='$date', modified_time='$time', modified_by='$opr_id' where id='$id'");
			
			$new_upc_no = $upc_num+1;
			$upd_upc_no = imw_query("update in_upc_no set upc_num='$new_upc_no' where id='".$fetch_upc_no['id']."'");
		}
	return $id;
}

function frame_stock($id,$manufacturer,$upc_name,$item_prac_code,$module_type,$name,$vendor,$brand,$frame_shape,$frame_style,$style_other,$a,$b,$ed,$dbl,$temple,$fpd,$bridge,$color_code,$color,$wholesale_cost,$purchase_price,$retail_price,$threshold,$qty_on_hand,$amount,$discount,$disc_date,$gender,$type,$formula_save,$retailpriceFlag,$frame_style_name='')
{
	$opr_id = $_SESSION['authId'];
	$date = date("Y-m-d");
	$time = date("h:i:s");
	$item_prac_code_qry="";
	$procedureId = back_prac_id($item_prac_code, false, $module_type);
	$item_prac_code_qry=",item_prac_code='".$procedureId."'";
	if($style_other!="" || ($frame_style=='' && $frame_style_name!=''))
	{
		$other_style_name=($style_other)?$style_other:$frame_style_name;
		$mq = imw_query("select id from in_frame_styles where LOWER(style_name) = '".trim(strtolower($other_style_name))."'  and del_status!=2");
		if(imw_num_rows($mq)>0)
		{
			$mqr=imw_fetch_assoc($mq);
			$frame_style=$mqr['id'];
			$brandst = imw_query("insert into in_style_brand set style_id = '".$frame_style."' , brand_id = '".$brand."' ");
		}
		else
		{
			$othrfrmins=imw_query("insert into in_frame_styles set style_name = '".trim($other_style_name)."' ");	
			$frame_style=imw_insert_id();
			$brandst = imw_query("insert into in_style_brand set style_id = '".$frame_style."' , brand_id = '".$brand."' ");
		}
	}
	else
	{
		if($frame_style && $frame_style_name)
		{
			$mq = imw_query("select id from in_frame_styles where LOWER(style_name) = '".trim(strtolower($frame_style_name))."'  and del_status!=2");
			$is_found=imw_num_rows($mq);
			
			$mqr=imw_fetch_assoc($mq);
			if($is_found>0 && $mqr['id']!=$frame_style)
			{
				$frame_style=$mqr['id'];
				//validate entry in style brand
				$q=imw_query("select id from in_style_brand where style_id = '".$frame_style."' , brand_id = '".$brand."' ");
				if(imw_num_rows($q)==0)
				{
					 imw_query("insert into in_style_brand set style_id = '".$frame_style."' , brand_id = '".$brand."' ");
				}
			}
		}
	}
	
	if($id=="")
	{
		$qry = "insert into in_item set
		manufacturer_id = '".imw_real_escape_string($manufacturer)."',
		upc_code = '".imw_real_escape_string($upc_name)."',
		module_type_id	= '".imw_real_escape_string($module_type)."',		
		name = '".imw_real_escape_string(htmlentities($name, ENT_QUOTES, 'UTF-8'))."',
		vendor_id = '".imw_real_escape_string($vendor)."',
		brand_id = '".imw_real_escape_string($brand)."',
		frame_style = '".imw_real_escape_string($frame_style)."',
		style_other = '',
		frame_shape = '".imw_real_escape_string($frame_shape)."',
		a = '".imw_real_escape_string($a)."',
		b = '".imw_real_escape_string($b)."',
		ed = '".imw_real_escape_string($ed)."',
		dbl = '".imw_real_escape_string($dbl)."',
		temple = '".imw_real_escape_string($temple)."',
		fpd = '".imw_real_escape_string($fpd)."',
		bridge = '".imw_real_escape_string($bridge)."',
		color_code = '".imw_real_escape_string($color_code)."',
		color = '".imw_real_escape_string($color)."',
		wholesale_cost = '".imw_real_escape_string($wholesale_cost)."',
		purchase_price = '".imw_real_escape_string($purchase_price)."',
		retail_price = '".imw_real_escape_string($retail_price)."',
		qty_on_hand = '".imw_real_escape_string($qty_on_hand)."',
		threshold = '".imw_real_escape_string($threshold)."',
		amount = '".imw_real_escape_string($amount)."',
		discount = '".imw_real_escape_string($discount)."',
		discount_till = '".imw_real_escape_string(saveDateFormat($disc_date))."',
		gender = '".imw_real_escape_string($gender)."',
		type_id = '".imw_real_escape_string($type)."',
		formula = '".imw_real_escape_string($formula_save)."',
		retail_price_flag = '".((bool)$retailpriceFlag)."',
		entered_date='$date', entered_time='$time', entered_by='$opr_id'
		 $item_prac_code_qry
		";	

		imw_query($qry);
		$id = imw_insert_id();	
		if(!empty($_FILES['file']['name']))
		{
			$size_path="../../../images/frame_stock/xl";
			if( !is_dir($size_path) ){
				mkdir( $size_path, 0755, true );
				chown( $size_path, 'apache' );
			}
			uploadfile($_FILES['file'],$size_path.'/',$id,"in_item");  
		}
	}
	else
	{

		$qry = "update in_item set
		manufacturer_id = '".imw_real_escape_string($manufacturer)."',
		upc_code = '".imw_real_escape_string($upc_name)."',
		module_type_id	= '".imw_real_escape_string($module_type)."',		
		name = '".imw_real_escape_string(htmlentities($name, ENT_QUOTES, 'UTF-8'))."',
		vendor_id = '".imw_real_escape_string($vendor)."',
		brand_id = '".imw_real_escape_string($brand)."',
		frame_style = '".imw_real_escape_string($frame_style)."',
		style_other = '',
		frame_shape = '".imw_real_escape_string($frame_shape)."',
		a = '".imw_real_escape_string($a)."',
		b = '".imw_real_escape_string($b)."',
		ed = '".imw_real_escape_string($ed)."',
		dbl = '".imw_real_escape_string($dbl)."',
		temple = '".imw_real_escape_string($temple)."',
		fpd = '".imw_real_escape_string($fpd)."',
		bridge = '".imw_real_escape_string($bridge)."',
		color_code = '".imw_real_escape_string($color_code)."',
		color = '".imw_real_escape_string($color)."',
		wholesale_cost = '".imw_real_escape_string($wholesale_cost)."',
		purchase_price = '".imw_real_escape_string($purchase_price)."',
		retail_price = '".imw_real_escape_string($retail_price)."',
		qty_on_hand = '".imw_real_escape_string($qty_on_hand)."',
		threshold = '".imw_real_escape_string($threshold)."',
		amount = '".imw_real_escape_string($amount)."',
		discount = '".imw_real_escape_string($discount)."',
		discount_till = '".imw_real_escape_string(saveDateFormat($disc_date))."',
		gender = '".imw_real_escape_string($gender)."',
		type_id = '".imw_real_escape_string($type)."',
		formula = '".imw_real_escape_string($formula_save)."',
		retail_price_flag = '".((bool)$retailpriceFlag)."',
		modified_date='$date', modified_time='$time', modified_by='$opr_id'
		 $item_prac_code_qry
		where id = '".imw_real_escape_string($id)."'
		";

		imw_query($qry);
		if(!empty($_FILES['file']['name']))
		{  
			$size_path="../../../images/frame_stock/xl";
			if( !is_dir($size_path) ){
				mkdir( $size_path, 0755, true );
				chown( $size_path, 'apache' );
			}
			uploadfile($_FILES['file'],$size_path.'/',$id,"in_item");  
		}
	}

	if(trim($upc_name)=="")
	{
		$sel_upc_num = imw_query("select id, upc_num from in_upc_no");
		$fetch_upc_no = imw_fetch_array($sel_upc_num);
		$upc_num = $fetch_upc_no['upc_num'];
		$prt=0;
		for($pr=0;$prt==0;$pr++)
		{
			$sel_frm_item = imw_query("select id from in_item where upc_code='$upc_num'");
			if(imw_num_rows($sel_frm_item)>0)
			{
				$upc_num=$upc_num+1;
			}
			else
			{
				$prt=1;
			}
		}
		$upd_itm = imw_query("update in_item set upc_code='$upc_num', modified_date='$date', modified_time='$time', modified_by='$opr_id' where id='$id'");

		$new_upc_no = $upc_num+1;
		$upd_upc_no = imw_query("update in_upc_no set upc_num='$new_upc_no' where id='".$fetch_upc_no['id']."'");
	}
	return $id;
}

function lense_stock($id,$manufacturer,$upc_name,$module_type,$name,$vendor,$lens_type,$lens_progresive,$lens_design,$lens_material,$lens_air,$lens_transition,$polarized_name,$edge_name,$tint_type,$color_name,$sphere_positive_min,$sphere_negative_min,$cylindep_positive_min,$cylindep_negative_min,$sphere_positive_max,$sphere_negative_max,$cylindep_positive_max,$cylindep_negative_max,$min_segment,$diameter,$th,$r_check,$l_check,$finish_type,$finish_type_other,$qty_on_hand,$amount,$uv_check,$pgx_check,$wholesale_cost,$purchase_price, $retail_price,$discount,$disc_date,$labs,$type_prac_code,$material_prac_code,$ar_prac_code,$transition_prac_code,$polarized_prac_code,$tint_prac_code,$uv_prac_code,$progress_prac_code,$design_prac_code,$edge_prac_code,$color_prac_code,$pgx_prac_code,$bc)
{
	$opr_id = $_SESSION['authId'];
	$date = date("Y-m-d");
	$time = date("h:i:s");
		if($uv_check=="on")
		{ $uv_check="1"; }
		if($pgx_check=="on")
		{ $pgx_check="1"; }
		if($r_check=="on")
		{ $r_check="1"; }
		if($l_check=="on")
		{ $l_check="1"; }
		
			$min_segment_arr=explode('~:~',$min_segment);
		if($id=="")
		{
			$qry = "insert into in_item set
			manufacturer_id = '".imw_real_escape_string($manufacturer)."',
			upc_code	= '".imw_real_escape_string($upc_name)."',
			module_type_id = '".imw_real_escape_string($module_type)."',
			name = '".imw_real_escape_string($name)."',
			vendor_id = '".imw_real_escape_string($vendor)."',
			type_id = '".imw_real_escape_string($lens_type)."',
			progressive_id = '".imw_real_escape_string($lens_progresive)."',
			design_id = '".imw_real_escape_string($lens_design)."',
			material_id = '".imw_real_escape_string($lens_material)."',
			a_r_id = '".imw_real_escape_string($lens_air)."',
			transition_id = '".imw_real_escape_string($lens_transition)."',
			polarized_id = '".imw_real_escape_string($polarized_name)."',
			edge_id = '".imw_real_escape_string($edge_name)."',			
			tint_id = '".imw_real_escape_string($tint_type)."',
			color = '".imw_real_escape_string($color_name)."',
			lab_id = '".imw_real_escape_string($labs)."',
			sphere_positive = '".imw_real_escape_string($sphere_positive_min)."',
			sphere_negative = '".imw_real_escape_string($sphere_negative_min)."',
			cylindep_positive = '".imw_real_escape_string($cylindep_positive_min)."',
			cylindep_negative = '".imw_real_escape_string($cylindep_negative_min)."',
			sphere_positive_max = '".imw_real_escape_string($sphere_positive_max)."',
			sphere_negative_max = '".imw_real_escape_string($sphere_negative_max)."',
			cylindep_positive_max = '".imw_real_escape_string($cylindep_positive_max)."',
			cylindep_negative_max = '".imw_real_escape_string($cylindep_negative_max)."',
			minimum_segment = '".imw_real_escape_string($min_segment_arr[0])."',
			minimum_segment_id = '".imw_real_escape_string($min_segment_arr[1])."',
			diameter = '".imw_real_escape_string($diameter)."',
			th = '".imw_real_escape_string($th)."',
			r_check = '".imw_real_escape_string($r_check)."',
			l_check = '".imw_real_escape_string($l_check)."',
			finish_type = '".imw_real_escape_string($finish_type)."',
			finish_type_other = '".imw_real_escape_string($finish_type_other)."',
			qty_on_hand = '".imw_real_escape_string($qty_on_hand)."',
			amount = '".imw_real_escape_string($amount)."',
			uv_check = '".imw_real_escape_string($uv_check)."',
			pgx_check = '".imw_real_escape_string($pgx_check)."',
			wholesale_cost = '".imw_real_escape_string($wholesale_cost)."',
			purchase_price = '".imw_real_escape_string($purchase_price)."',
			retail_price = '".imw_real_escape_string($retail_price)."',
			discount = '".imw_real_escape_string($discount)."',		
			discount_till = '".imw_real_escape_string(saveDateFormat($disc_date))."',
			bc = '".imw_real_escape_string($bc)."',	
			entered_date='$date', entered_time='$time', entered_by='$opr_id'
			";
			imw_query($qry);
			$lastID = $id = imw_insert_id();
			if(!empty($_FILES['file']['name']))
			{
				uploadfile($_FILES['file'],"../../../images/lense_stock/",$id,"in_item");
			}
		}
		else
		{
			$qry = "update in_item set
			manufacturer_id = '".imw_real_escape_string($manufacturer)."',
			upc_code	= '".imw_real_escape_string($upc_name)."',
			module_type_id = '".imw_real_escape_string($module_type)."',
			name = '".imw_real_escape_string($name)."',
			vendor_id = '".imw_real_escape_string($vendor)."',
			type_id = '".imw_real_escape_string($lens_type)."',
			progressive_id = '".imw_real_escape_string($lens_progresive)."',
			design_id = '".imw_real_escape_string($lens_design)."',
			edge_id = '".imw_real_escape_string($edge_name)."',			
			material_id = '".imw_real_escape_string($lens_material)."',
			a_r_id = '".imw_real_escape_string($lens_air)."',
			transition_id = '".imw_real_escape_string($lens_transition)."',
			polarized_id = '".imw_real_escape_string($polarized_name)."',
			tint_id = '".imw_real_escape_string($tint_type)."',
			color = '".imw_real_escape_string($color_name)."',
			lab_id = '".imw_real_escape_string($labs)."',
			sphere_positive = '".imw_real_escape_string($sphere_positive_min)."',
			sphere_negative = '".imw_real_escape_string($sphere_negative_min)."',
			cylindep_positive = '".imw_real_escape_string($cylindep_positive_min)."',
			cylindep_negative = '".imw_real_escape_string($cylindep_negative_min)."',
			sphere_positive_max = '".imw_real_escape_string($sphere_positive_max)."',
			sphere_negative_max = '".imw_real_escape_string($sphere_negative_max)."',
			cylindep_positive_max = '".imw_real_escape_string($cylindep_positive_max)."',
			cylindep_negative_max = '".imw_real_escape_string($cylindep_negative_max)."',
			minimum_segment = '".imw_real_escape_string($min_segment_arr[0])."',
			minimum_segment_id = '".imw_real_escape_string($min_segment_arr[1])."',
			diameter = '".imw_real_escape_string($diameter)."',
			th = '".imw_real_escape_string($th)."',
			r_check = '".imw_real_escape_string($r_check)."',
			l_check = '".imw_real_escape_string($l_check)."',
			finish_type = '".imw_real_escape_string($finish_type)."',
			finish_type_other = '".imw_real_escape_string($finish_type_other)."',
			qty_on_hand = '".imw_real_escape_string($qty_on_hand)."',
			amount = '".imw_real_escape_string($amount)."',
			uv_check = '".imw_real_escape_string($uv_check)."',
			pgx_check = '".imw_real_escape_string($pgx_check)."',
			wholesale_cost = '".imw_real_escape_string($wholesale_cost)."',
			purchase_price = '".imw_real_escape_string($purchase_price)."',
			retail_price = '".imw_real_escape_string($retail_price)."',
			discount = '".imw_real_escape_string($discount)."',		
			discount_till = '".imw_real_escape_string(saveDateFormat($disc_date))."',
			bc = '".imw_real_escape_string($bc)."',	
			modified_date='$date', modified_time='$time', modified_by='$opr_id'
			where id = '".$id."'
			";
			
			imw_query($qry);
			$lastID = $id;
	
			if(!empty($_FILES['file']['name']))
			{
				uploadfile($_FILES['file'],"../../../images/lense_stock/",$id,"in_item");
			}			
		}
		
		if(trim($upc_name)=="")
		{
			$sel_upc_num = imw_query("select id, upc_num from in_upc_no");
			$fetch_upc_no = imw_fetch_array($sel_upc_num);
			$upc_num = $fetch_upc_no['upc_num'];
			$prt=0;
			for($pr=0;$prt==0;$pr++)
			{
				$sel_frm_item = imw_query("select id from in_item where upc_code='$upc_num'");
				if(imw_num_rows($sel_frm_item)>0)
				{
					$upc_num=$upc_num+1;
				}
				else
				{
					$prt=1;
				}
			}
			$upd_itm = imw_query("update in_item set upc_code='$upc_num', modified_date='$date', modified_time='$time', modified_by='$opr_id' where id='$id'");
			
			$new_upc_no = $upc_num+1;
			$upd_upc_no = imw_query("update in_upc_no set upc_num='$new_upc_no' where id='".$fetch_upc_no['id']."'");
		}
		
		$sel_qry = imw_query("select * from in_item_price_details where item_id='$lastID'");
		if(imw_num_rows($sel_qry) > 0)
		{
			$act_qry = "update";
			$sel_id = imw_fetch_array($sel_qry);
			$whr = ", modified_date='$date', modified_time='$time', modified_by='$opr_id' where id='".$sel_id['id']."'";
		} 
		else
		{
			$act_qry = "insert into";
			$whr = ", entered_date='$date', entered_time='$time', entered_by='$opr_id'";
		}
		
		$type_prac_id = back_prac_id($type_prac_code);
		$material_prac_id = back_prac_id($material_prac_code, true);
		$ar_prac_id = back_prac_id($ar_prac_code,true);
		$transition_prac_id = back_prac_id($transition_prac_code);
		$polarized_prac_id = back_prac_id($polarized_prac_code);
		$tint_prac_id = back_prac_id($tint_prac_code);
		$uv_prac_id = back_prac_id($uv_prac_code);
		$progress_prac_id = back_prac_id($progress_prac_code);
		$design_prac_id = back_prac_id($design_prac_code);
		$edge_prac_id = back_prac_id($edge_prac_code);
		$color_prac_id = back_prac_id($color_prac_code);
		$pgx_prac_id = back_prac_id($pgx_prac_code);
		
		$wholesale_fields = "";
		
		$lens_retail = get_rprice_by_prac_id($type_prac_id,$sel_id['type_prac_code'],$type_prac_code,$sel_id['lens_retail']);
		if($lens_type==""){$wholesale_fields .=", lens_wholesale='0.00'";}
		
		$mat_retail = get_rprice_by_prac_id($material_prac_id,$sel_id['material_prac_code'],$material_prac_code,$sel_id['material_retail'], true);
		$wholesale_fields_material = "";
		if($lens_material==""){$wholesale_fields_material =", material_wholesale='0.00'";}
		
		$mat_retail = explode(";", $mat_retail);
		
		if($_REQUEST['lens_material']!="" && $_REQUEST['lens_material']!="0"){
			
			$resp_mat_prices = imw_query("SELECT `wholesale_price`, `retail_price` FROM `in_lens_material` WHERE `id`='".$_REQUEST['lens_material']."'");
			$rtp_dt = array();
			$whp_dt = array();
			if($resp_mat_prices && imw_num_rows($resp_mat_prices)>0){
				$price_details = imw_fetch_assoc($resp_mat_prices);
				$prac_codes = explode(";",$material_prac_code);
				$rt_prices = explode(";",$price_details['retail_price']);
				$wh_prices = explode(";",$price_details['wholesale_price']);
				foreach($prac_codes as $key=>$prac){
					array_push($rtp_dt, (isset($rt_prices[$key]))?$rt_prices[$key]:"0.00");
					array_push($whp_dt, (isset($wh_prices[$key]))?$wh_prices[$key]:"0.00");
				}
			}
			if(count($rtp_dt)>0){
				foreach($rt_prices as $key=>$val){
					
					if(!isset($mat_retail[$key]) || $mat_retail[$key]=="0.00" || $mat_retail[$key]==""){
						
						$mat_retail[$key] = $val;
					}
				}
				$mat_retail = implode(";", $mat_retail);
			}
			
			if(count($wh_prices)>0){
				
				if($act_qry=="update"){
					$wholesale_prices = array();
					$item_wholesale = imw_query("SELECT `material_wholesale` FROM `in_item_price_details`
													WHERE id='".$sel_id['id']."'");
					if($item_wholesale && imw_num_rows($item_wholesale)>0){
						$wholesale_prices = imw_fetch_assoc($item_wholesale);
						$wholesale_prices = explode(";", $wholesale_prices['material_wholesale']);
					}
					
					if(count($wholesale_prices)>0){
						foreach($wh_prices as $key=>$val){
							if(!isset($wholesale_prices[$key]) || $wholesale_prices[$key]=="0.00" || $wholesale_prices[$key]==""){
								$wholesale_prices[$key] = $val;
							}
						}
					}
				}
				
				$wholesale_fields_material = ", material_wholesale='".implode(";", $wholesale_prices)."'";
			}
		}
		$wholesale_fields .= $wholesale_fields_material;
		
		$ar_retail = get_rprice_by_prac_id($ar_prac_id,$sel_id['ar_prac_code'],$ar_prac_code,$sel_id['a_r_retail'], true);
		if($lens_air==""){$wholesale_fields .=", a_r_wholesale='0.00'";}
		
		$trans_retail = get_rprice_by_prac_id($transition_prac_id,$sel_id['transition_prac_code'],$transition_prac_code,$sel_id['transition_retail']);
		if($lens_transition==""){$wholesale_fields .=", transition_wholesale='0.00'";}
		
		$pol_retail = get_rprice_by_prac_id($polarized_prac_id,$sel_id['polarized_prac_code'],$polarized_prac_code,$sel_id['polarization_retail']);
		if($polarized_name==""){$wholesale_fields .=", polarization_wholesale='0.00'";}
		
		$tint_retail = get_rprice_by_prac_id($tint_prac_id,$sel_id['tint_prac_code'],$tint_prac_code,$sel_id['tint_retail']);
		if($tint_type==""){$wholesale_fields .=", tint_wholesale='0.00'";}
		
		$uv_retail = get_rprice_by_prac_id($uv_prac_id,$sel_id['uv_prac_code'],$uv_prac_code,$sel_id['uv400_retail']);
		if($uv_check!="1"){$wholesale_fields .=", uv400_wholesale='0.00'";}
		
		$prog_retail = get_rprice_by_prac_id($progress_prac_id,$sel_id['progressive_prac_code'],$progress_prac_code,$sel_id['progressive_retail']);
		if($lens_progresive=="" || $lens_progresive=="0"){$wholesale_fields .=", progressive_wholesale='0.00'";}
		
		$design_retail = get_rprice_by_prac_id($design_prac_id,$sel_id['design_prac_code'],$design_prac_code,$sel_id['design_retail']);
		if($lens_design==""){$wholesale_fields .=", design_wholesale='0.00'";}
		
		$edge_retail = get_rprice_by_prac_id($edge_prac_id,$sel_id['edge_prac_code'],$edge_prac_code,$sel_id['edge_retail']);
		if($edge_name==""){$wholesale_fields .=", edge_wholesale='0.00'";}
		
		$color_retail = get_rprice_by_prac_id($color_prac_id,$sel_id['color_prac_code'],$color_prac_code,$sel_id['color_retail']);
		if($color_name==""){$wholesale_fields.=", color_wholesale='0.00'";}
		
		$pgx_retail = get_rprice_by_prac_id($pgx_prac_id,$sel_id['pgx_prac_code'],$pgx_prac_code,$sel_id['pgx_retail']);
		if($pgx_check!="1"){$wholesale_fields .=", pgx_wholesale='0.00'";}
		
		$ipd_qry = imw_query("$act_qry in_item_price_details set module_type_id='2', item_id='$lastID', lens_retail='$lens_retail', material_retail='$mat_retail', a_r_retail='$ar_retail', transition_retail='$trans_retail', polarization_retail='$pol_retail', tint_retail='$tint_retail', uv400_retail='$uv_retail', progressive_retail='$prog_retail', design_retail='$design_retail', edge_retail='$edge_retail', color_retail='$color_retail', pgx_retail='$pgx_retail', type_prac_code='$type_prac_id', material_prac_code='$material_prac_id', ar_prac_code='$ar_prac_id', transition_prac_code='$transition_prac_id', polarized_prac_code='$polarized_prac_id', tint_prac_code='$tint_prac_id', uv_prac_code='$uv_prac_id', progressive_prac_code='$progress_prac_id', design_prac_code='$design_prac_id', edge_prac_code='$edge_prac_id', color_prac_code='$color_prac_id', pgx_prac_code='$pgx_prac_id' $wholesale_fields $whr");
		
		$sel_other_price = imw_query("select other_retail from in_item_price_details where item_id='$lastID'");
		$get_other = imw_fetch_assoc($sel_other_price);
		$other_ret = $get_other['other_retail'];
		$ar_retail = explode(";",$ar_retail);	/*Price for multiple AR/Coating values*/
		$mat_retail = explode(";",$mat_retail);	/*Price for multiple Material Prac Codes*/
		
		$total_retail = $lens_retail+array_sum($mat_retail)+array_sum($ar_retail)+$trans_retail+$pol_retail+$tint_retail+$uv_retail+$prog_retail+$edge_retail+$color_retail+$pgx_retail+$other_ret;
		
		/*Fix to update wholesale price*/
			$sqlwholesale = imw_query("SELECT lens_wholesale, material_wholesale, transition_wholesale, polarization_wholesale, tint_wholesale, uv400_wholesale, pgx_wholesale, other_wholesale, progressive_wholesale, design_wholesale, edge_wholesale, color_wholesale, a_r_wholesale FROM in_item_price_details where id='".$sel_id['id']."'");
			$wholesale_price = array();
			if($sqlwholesale && imw_num_rows($sqlwholesale)>0){
				$wholesale_price = imw_fetch_assoc($sqlwholesale);
			}
			$ar_wholesale_price = (isset($wholesale_price['a_r_wholesale']))?explode(";",$wholesale_price['a_r_wholesale']):array("0.00");
			unset($wholesale_price['a_r_wholesale']);
			$ar_wholesale_price = array_sum($ar_wholesale_price);
			
			$material_wholesale_price = (isset($wholesale_price['material_wholesale']))?explode(";",$wholesale_price['material_wholesale']):array("0.00");
			unset($wholesale_price['material_wholesale']);
			$material_wholesale_price = array_sum($material_wholesale_price);
			
			$wholesale_price = array_sum($wholesale_price);
			$wholesale_price = $wholesale_price+$ar_wholesale_price+$material_wholesale_price;
		/*End Fix to update wholesale price*/
		
		$total_amount = $total_retail*$qty_on_hand;
		$up_item_retail = imw_query("update in_item set retail_price='$total_retail', modified_date='$date', modified_time='$time', modified_by='$opr_id', amount='$total_amount', wholesale_cost='$wholesale_price' where id='$lastID'");
		return $lastID;
}
	
function get_rprice_by_prac_id($new_prac_id,$exist_prac_id,$prac_code,$old_price,$multi=false)
{
	
	if($new_prac_id!=$exist_prac_id && $prac_code!="")
	{
		if($multi){
			$new_price = array();
			
			$array_match_prac = explode(";",$prac_code);
			$str = str_replace(";", "','",$prac_code);
			$str = "'".$str."'";
			$getCPTPriceQry = "SELECT b.cpt_prac_code,a.cpt_fee FROM cpt_fee_table a,
												cpt_fee_tbl b
												WHERE 
												b.cpt_prac_code IN($str)
												AND a.cpt_fee_id = b.cpt_fee_id
												AND a.fee_table_column_id = '1'
												AND delete_status = '0' order by FIELD(b.cpt_prac_code, $str)";
			$getCPTPriceQry = imw_query($getCPTPriceQry);
			while($getCPTPriceRow = imw_fetch_assoc($getCPTPriceQry)){
				
				$procId = $getCPTPriceRow['cpt_prac_code'];
				$array_match_prac1 = $array_match_prac;
				array_walk($array_match_prac1, 'filterPrac_code', $procId);
				foreach($array_match_prac1 as $akey=>$aval){
					if(trim($aval)==""){
						unset($array_match_prac1[$akey]);
					}
					else{
						/*array_push($new_price, $getCPTPriceRow['cpt_fee']);*/
						array_push($new_price, '0.00');
					}
				}
			}
			if(imw_num_rows($getCPTPriceQry)==0){
				$getCPTPriceQry = "SELECT b.cpt_prac_code,a.cpt_fee FROM cpt_fee_table a,
												cpt_fee_tbl b
												WHERE 
												(b.cpt4_code IN($str) or b.cpt_desc IN($str))
												AND a.cpt_fee_id = b.cpt_fee_id
												AND a.fee_table_column_id = '1'
												AND delete_status = '0' order by FIELD(b.cpt4_code, $str), FIELD(b.cpt_desc, $str)";
				$getCPTPriceQry = imw_query($getCPTPriceQry);
				while($getCPTPriceRow = imw_fetch_assoc($getCPTPriceQry)){
					
					$procId = $getCPTPriceRow['cpt4_code'];
					$array_match_prac1 = $array_match_prac;
					array_walk($array_match_prac1, 'filterPrac_code', $procId);
					foreach($array_match_prac1 as $akey=>$aval){
						if(trim($aval)==""){
							unset($array_match_prac1[$akey]);
						}
						else{
							/*array_push($new_price, $getCPTPriceRow['cpt_fee']);*/
							array_push($new_price, '0.00');
						}
					}
				}
			}
			$new_price = implode(";", $new_price);
		}
		else{
			$str = $prac_code;
			$getCPTPriceQry = imw_query("SELECT b.cpt_prac_code,a.cpt_fee FROM cpt_fee_table a,
												cpt_fee_tbl b
												WHERE 
												b.cpt_prac_code='$str'
												AND a.cpt_fee_id = b.cpt_fee_id
												AND a.fee_table_column_id = '1'
												AND delete_status = '0' order by status asc");
			$getCPTPriceRow = imw_fetch_assoc($getCPTPriceQry);
			/*$cpt_fee = $getCPTPriceRow['cpt_fee'];*/
			$cpt_fee = '0.00';
			$cpt_prac_code=$getCPTPriceRow['cpt_prac_code'];
			if(imw_num_rows($getCPTPriceQry)==0){
				$getCPTPriceQry = imw_query("SELECT b.cpt_prac_code,a.cpt_fee FROM cpt_fee_table a,
												cpt_fee_tbl b
												WHERE 
												(b.cpt4_code='$str' or b.cpt_desc='$str')
												AND a.cpt_fee_id = b.cpt_fee_id
												AND a.fee_table_column_id = '1'
												AND delete_status = '0' order by status asc");
				$getCPTPriceRow = imw_fetch_assoc($getCPTPriceQry);
				/*$cpt_fee = $getCPTPriceRow['cpt_fee'];*/
				$cpt_fee = '0.00';
				$cpt_prac_code=$getCPTPriceRow['cpt_prac_code'];
			}
			$new_price = $cpt_fee;
		}
	}
	else
	{
		if($prac_code==""){
			$new_price = "0.00";
		}
		else{
			$new_price = $old_price;
		}
	}
	return $new_price;
}

function get_price_details_by_cpt_id($cpt_id){
	
	$returnData = array();
	$str=$cpt_id;
	$getCPTPriceQry = imw_query("SELECT b.cpt_desc,b.cpt_prac_code,a.cpt_fee FROM cpt_fee_table a,
										cpt_fee_tbl b
										WHERE 
										b.cpt_fee_id='$str'
										AND a.cpt_fee_id = b.cpt_fee_id
										AND a.fee_table_column_id = '1'
										AND delete_status = '0' order by status asc");
	$getCPTPriceRow = imw_fetch_assoc($getCPTPriceQry);
	$returnData['cpt_fee']=$getCPTPriceRow['cpt_fee'];
	$returnData['prac_code']=$getCPTPriceRow['cpt_prac_code'];
	$returnData['prac_code_desc']=$getCPTPriceRow['cpt_desc'];
	if(imw_num_rows($getCPTPriceQry)==0){
		$getCPTPriceQry = imw_query("SELECT b.cpt_desc,b.cpt_prac_code,a.cpt_fee FROM cpt_fee_table a,
										cpt_fee_tbl b
										WHERE 
										(b.cpt_fee_id='$str' or b.cpt_desc='$str')
										AND a.cpt_fee_id = b.cpt_fee_id
										AND a.fee_table_column_id = '1'
										AND delete_status = '0' order by status asc");
		$getCPTPriceRow = imw_fetch_assoc($getCPTPriceQry);
		$returnData['cpt_fee']=$getCPTPriceRow['cpt_fee'];
		$returnData['prac_code']=$getCPTPriceRow['cpt_prac_code'];
		$returnData['prac_code_desc']=$getCPTPriceRow['cpt_desc'];
	}
	return($returnData);
}

function order_action($action,$post_val)
{
	extract($post_val);
	$order_id="";
	if($_SESSION['order_id']!="")
	{
		$order_id=$_SESSION['order_id'];
	}

	$patient_id=$_SESSION['patient_session_id'];
	$operator_id=$_SESSION['authId'];
	$entered_date=date('Y-m-d');
	$entered_time=date('H:i:s');
	$frame_order_detail_id=$_POST['frame_order_detail_id'];
	$lens_order_detail_id=$_POST['lens_order_detail_id'];
	$cl_order_detail_id=$_POST['cl_order_detail_id'];
	
	if($lens_module_type_id==2){
	$a=1;

	
	for($r=0;$r<count($_POST['lens_prescription_count']);$r++)
	{		
	
			$qryWhere='';	
			$qryPrefix="Insert INTO ";
			$qryWhere = ", entered_date='$entered_date', entered_time='$entered_time', entered_by='$operator_id'";
			if($_POST['order_rx_lens_id_'.$a]>0){ 
				$qryPrefix="Update";
				$qryWhere=", modified_date='$entered_date', modified_time='$entered_time', modified_by='$operator_id' WHERE id='".$_POST['order_rx_lens_id'.$a]."' and det_order_id='$lens_order_detail_id' and order_id='$order_id' and patient_id='$patient_id'";
			}
			$qry=$qryPrefix." in_optical_order_form SET 
			patient_id='".$patient_id."',
			order_id='".$order_id."',
			det_order_id='".$lens_order_detail_id."',
			operator_id='".$_SESSION['authId']."',
			sphere_od='".$_POST['lens_sphere_od']."',
			cyl_od='".$_POST['lens_cylinder_od']."',
			axis_od='".$_POST['lens_axis_od']."',
			add_od='".$_POST['lens_add_od']."',
			base_od='".$_POST['lens_base_od']."',
			dist_pd_od='".$_POST['lens_dpd_od']."',
			near_pd_od='".$_POST['lens_npd_od']."',
			mr_od_p='".$_POST['lens_mr_od_p']."',
			mr_od_prism='".$_POST['lens_mr_od_prism']."',
			mr_od_splash='".$_POST['lens_mr_od_splash']."',
			mr_od_sel='".$_POST['lens_mr_od_sel']."',
			sphere_os='".$_POST['lens_sphere_os']."',
			cyl_os='".$_POST['lens_cylinder_os']."',
			axis_os='".$_POST['lens_axis_os']."',
			add_os='".$_POST['lens_add_os']."',
			prism_os='".$_POST['lens_prism_os']."',
			base_os='".$_POST['lens_base_os']."',
			dist_pd_os='".$_POST['lens_dpd_os']."',
			near_pd_os='".$_POST['lens_npd_os']."',
			mr_os_p='".$_POST['lens_mr_os_p']."',
			mr_os_prism='".$_POST['lens_mr_os_prism']."',
			mr_os_splash='".$_POST['lens_mr_os_splash']."',
			mr_os_sel='".$_POST['lens_mr_os_sel']."',
			outside_rx='".$_POST['lens_outside_rx']."',
			neutralize_rx='".$_POST['lens_neutralize_rx']."',
			telephone='".$_POST['lens_telephone']."'
			".$qryWhere;
			
			//echo $qry."<br>";
			
			$rs=imw_query($qry) or die(imw_error());
			$a++;
			
		}

	}
	if($cl_module_type_id==3){
	$b=1;	
	for($w=0;$w<count($_POST['cl_prescription_count']);$w++)
	{
		$qryWhere='';
		$qryPrefix="Insert INTO ";
		$qryWhere = ", entered_date='$entered_date', entered_time='$entered_time', entered_by='$operator_id'";
		if($_POST['order_rx_cl_id_'.$b]>0){ 
			$qryPrefix="Update";
			$qryWhere=", modified_date='$entered_date', modified_time='$entered_time', modified_by='$operator_id' WHERE id='".$_POST['order_rx_cl_id_'.$b]."'	and det_order_id='$cl_order_detail_id' and order_id='$order_id' and patient_id='$patient_id'";
		}
		$qry=$qryPrefix." in_cl_prescriptions SET 
		patient_id='".$patient_id."',
		order_id='".$order_id."',
		det_order_id='".$cl_order_detail_id."',
		physician_id='".$_POST['physician_id']."',
		operator_id='".$_SESSION['authId']."',
		sphere_od='".$_POST['cl_sphere_od']."',
		cylinder_od='".$_POST['cl_cylinder_od']."',
		axis_od='".$_POST['cl_axis_od']."',
		add_od='".$_POST['cl_add_od']."',
		base_od='".$_POST['cl_base_od']."',
		diameter_od='".$_POST['cl_diameter_od']."',
		sphere_os='".$_POST['cl_sphere_os']."',
		cylinder_os='".$_POST['cl_cylinder_os']."',
		axis_os='".$_POST['cl_axis_os']."',
		add_os='".$_POST['cl_add_os']."',
		base_os='".$_POST['cl_base_os']."',
		diameter_os='".$_POST['cl_diameter_os']."',
		date_added='".date('Y-m-d')."',
		outside_rx='".$_POST['cl_outside_rx']."',
		neutralize_rx='".$_POST['cl_neutralize_rx']."',
		telephone='".$_POST['cl_telephone']."'
		".$qryWhere;	
		$rs=imw_query($qry) or die(imw_error());
		$b++;
	}
	
	}
}

function change_order_status($order_id, $status, $pager="", $redc_stock="", $reason="")
{
	$operator_id = $_SESSION['authId'];
	$date = date("Y-m-d");
	$time = date("H:i:s");
	$chk_blank_status="";
	if($pager!='all' && $pager!='pt_hst'){
		if(strtolower($pager)=="pending"){
			$chk_blank_status=" or order_status=''";
		}
		$whr_status=" and (order_status='$pager' $chk_blank_status)";
	}
	
	$sel_item = "select id, order_id, patient_id, item_id, item_id_os, qty, qty_right, operator_id, module_type_id from in_order_details where order_id='".$order_id."' $whr_status and del_status='0'";
	$res_item = imw_query($sel_item);
	$num_item = imw_num_rows($res_item);
	if($num_item > 0)
	{
		while($sel_rows = imw_fetch_array($res_item))
		{
			//$sel_deatil_status = "select id from in_order_detail_status where patient_id='".$sel_rows['patient_id']."' and item_id='".$sel_rows['item_id']."' and order_id='".$sel_rows['order_id']."' and order_detail_id='".$sel_rows['id']."' and order_status='".$status."' and order_date='".$date."'";
			//$res_deatil_status = imw_query($sel_deatil_status);
			//$num_deatil_status = imw_num_rows($res_deatil_status);
			//if($num_deatil_status == 0)
			//{
			$ins = "insert in_order_detail_status set patient_id='".$sel_rows['patient_id']."', item_id='".$sel_rows['item_id']."', order_id='".$sel_rows['order_id']."', order_detail_id='".$sel_rows['id']."', order_qty='".($sel_rows['qty']+$sel_rows['qty_right'])."', order_status='".$status."', order_date='".$date."', order_time='".$time."', operator_id='".$operator_id."', order_notes='". imw_real_escape_string($reason) ."'";
			$execut = imw_query($ins);
			
			if($status=="ordered")
			{
				$ordered_qry= ", ordered='$date'";
			}
			elseif($status=="received")
			{
				$ordered_qry= ", received='$date'";
			}
			elseif($status=="notified")
			{
				$ordered_qry= ", notified='$date'";
			}
			elseif($status=="dispensed")
			{
				$ordered_qry= ", dispensed='$date'";
				if($redc_stock=="yes")
				{
					
					if($sel_rows['module_type_id'] == 3){
						if($sel_rows['item_id']>0){
							deduct_item_qty($sel_rows['order_id'],$sel_rows['id'],$sel_rows['patient_id'],$sel_rows['item_id'],$sel_rows['qty_right']);
						}
						if($sel_rows['item_id_os']>0){
							deduct_item_qty($sel_rows['order_id'],$sel_rows['id'],$sel_rows['patient_id'],$sel_rows['item_id_os'],$sel_rows['qty']);
						}
					}
					else{
						deduct_item_qty($sel_rows['order_id'],$sel_rows['id'],$sel_rows['patient_id'],$sel_rows['item_id'],$sel_rows['qty']+$sel_rows['qty_right']);
					}
				}
			}
			else{
				$ordered_qry = '';
			}
			
			imw_query("update in_order_details set order_status='".$status."' $ordered_qry , modified_date='$date', modified_time='$time', modified_by='$operator_id' where id='".$sel_rows['id']."'");
			//}
		}
	}
}

function deduct_item_qty($ord_id,$ord_det_id,$pat_id,$item_id,$tot_qty)
{
	$operator_id = $_SESSION['authId'];
	$date = date("Y-m-d");
	$time = date("H:i:s");
	//get default location id
	

	$select_fac = imw_query("select loc.id as locid from in_order_fac as ord_fac 
								left join facility as fac on fac.id=ord_fac.facility_id 
								left join in_location as loc on loc.pos=fac.fac_prac_code 
								where ord_fac.del_status='0' 
								and ord_fac.order_id='$ord_id' 
								and ord_fac.order_det_id='$ord_det_id' 
								and ord_fac.patient_id='$pat_id' 
								and ord_fac.item_id='$item_id' order by ord_fac.id asc limit 1");
	$data_fac=imw_fetch_object($select_fac);
	$df_ord_fac_id = $data_fac->locid;

	if($df_ord_fac_id =="")
	{	
		$df_ord_fac_id = $_SESSION['pro_fac_id'];
	}


	//check is default location record exist in lo_total table
	if(imw_num_rows(imw_query("select * from in_item_loc_total where item_id='$item_id' and loc_id=$df_ord_fac_id"))==0)
	imw_query("insert into in_item_loc_total set item_id='$item_id', loc_id=$df_ord_fac_id");
	
	$where='';
	if(defined('ADJACENT_QTY_DEDUCTION') && defined('ADJACENT_QTY_DEDUCTION')=='FALSE')
	{$where=" AND loc_id=$df_ord_fac_id	";}
	$sel_qty_fac = imw_query("select * from in_item_loc_total where item_id='$item_id' and stock>0 $where");
	while($data=imw_fetch_object($sel_qty_fac))
	{
		$stockArr[$data->loc_id]=$data->stock;
	}
	
	if($stockArr[$df_ord_fac_id]>=$tot_qty)
	{
		$deduct_arr[$df_ord_fac_id]=$tot_qty;
		$restQty=0;	
	}
	else
	{
		$deduct_arr[$df_ord_fac_id]=$stockArr[$df_ord_fac_id];
		$restQty=$tot_qty-$stockArr[$df_ord_fac_id];
	}
	
	unset($stockArr[$df_ord_fac_id]);//clear default location record
	
	if($restQty>0 && sizeof($stockArr)>0)
	{
		foreach($stockArr as $loc=>$stock)
		{
			if($stock>=$restQty)
			{
				$deduct_arr[$loc]=$restQty;
				$restQty=0;	
			}
			else
			{
				$deduct_arr[$loc]=$stock;
				$restQty=$restQty-$stock;
			}
			if($restQty==0)break;	
		}
	}
	
	//check rest qty
	if($restQty>0 && sizeof($stockArr)>0)$deduct_arr[$df_ord_fac_id]+=$restQty;
	
	//deduct qty
	if(sizeof($deduct_arr)>0){
	foreach($deduct_arr as $loc_id=>$qty)
	{
		if($qty)imw_query("update in_item_loc_total set stock=IF( (stock-$qty)>0, (stock-$qty), 0 ) where item_id='$item_id' and loc_id=$loc_id");
		$red_qty = $qty;
		$pending_lot_red_qty=$red_qty;
		
		$sel_lot = imw_query("select * from in_item_lot_total where item_id='$item_id' and loc_id='$loc_id' order by id asc");
		if(imw_num_rows($sel_lot) > 0)
		{
			$last_lot=0;
			while($get_lot = imw_fetch_array($sel_lot))
			{
				$last_lot=$last_lot+1;
				if($pending_lot_red_qty>0)
				{
					if($get_lot['stock']>0)
					{
						if($get_lot['stock']>$pending_lot_red_qty)
						{
							$lot_red_qty=$pending_lot_red_qty;
							$pending_lot_red_qty=0;
						}else
						{
							$lot_red_qty=$get_lot['stock'];
							$pending_lot_red_qty=$pending_lot_red_qty-$get_lot['stock'];
						}
						imw_query("update in_item_lot_total set stock=IF( (stock-$lot_red_qty)>0, (stock-$lot_red_qty), 0 ) where id='".$get_lot['id']."'");
						
						/*Lot Qty reduced for order Detail*/
						imw_query("INSERT INTO `in_order_lot_details` SET `order_id`='".$ord_id."',
									`order_detail_id`='".$ord_det_id."', `lot_no`='".$get_lot['id']."',
									`qty`='".$lot_red_qty."', `ordered_date`='".$date."'");
						/*Inset Stock Modification History*/
						imw_query("INSERT INTO `in_stock_detail` SET `item_id`='".$item_id."', `loc_id`='".$loc_id."',
									`stock`='".$lot_red_qty."', `trans_type`='minus', `operator_id`='".$operator_id."',
									`entered_date`='".$date."', `entered_time`='".$time."', `lot_id`='".$get_lot['id']."',
									`order_id`='".$ord_id."', `order_detail_id`='".$ord_det_id."', source='Order'");
					}
					else
					{
						if(imw_num_rows($sel_lot)==$last_lot)
						{
							imw_query("update in_item_lot_total set stock=IF( (stock-$pending_lot_red_qty)>0, (stock-$pending_lot_red_qty), 0 ) where id='".$get_lot['id']."'");
							
							/*Lot Qty reduced for order Detail*/
							imw_query("INSERT INTO `in_order_lot_details` SET `order_id`='".$ord_id."',
										`order_detail_id`='".$ord_det_id."', `lot_no`='".$get_lot['id']."',
										`qty`='".$pending_lot_red_qty."', `ordered_date`='".date("Y-m-d")."'");
							/*Inset Stock Modification History*/
							imw_query("INSERT INTO `in_stock_detail` SET `item_id`='".$item_id."', `loc_id`='".$loc_id."',
										`stock`='".$pending_lot_red_qty."', `trans_type`='minus', `operator_id`='".$operator_id."',
										`entered_date`='".$date."', `entered_time`='".$time."', `lot_id`='".$get_lot['id']."',
										`order_id`='".$ord_id."', `order_detail_id`='".$ord_det_id."', source='Order'");
						}
					}
				}
			}
		}
	}
	}
	
	$sel_item_qry = imw_query("select retail_price, id, qty_on_hand from in_item where id='$item_id'");
	$get_item_qry = imw_fetch_array($sel_item_qry);
	$new_qty = $get_item_qry['qty_on_hand']-$tot_qty;
	if($new_qty<0) $new_qty = 0;	/*Do make stock value less than 0*/
	
	$new_amt=0;
	if($new_qty>0)
	{
		$new_amt = $get_item_qry['retail_price']*$new_qty;
	}
	
	$deduct_qty = imw_query("update in_item set qty_on_hand='$new_qty', amount='$new_amt', modified_date='$date', modified_time='$time', modified_by='$operator_id' where id='$item_id'");
}

function change_item_status($item_id, $status, $order_date="", $redc_stock="")
{
	$operator_id = $_SESSION['authId'];
	$entered_date = date("Y-m-d");
	$entered_time = date("h:i:s");
	
	if($order_date!="")
	{
		$date = $order_date;
	}
	else
	{
		$date = date("Y-m-d");	
	}
	
	$time = date("H:i:s");
	
	$sel_item = "select id, order_id, patient_id, item_id, item_id_os, qty, qty_right, operator_id, module_type_id from in_order_details where id='".$item_id."' and del_status='0'";
	$res_item = imw_query($sel_item);
	$num_item = imw_num_rows($res_item);
	if($num_item > 0)
	{
		$sel_rows = imw_fetch_array($res_item);
		
		$sel_deatil_status = "select id from in_order_detail_status where patient_id='".$sel_rows['patient_id']."' and item_id='".$sel_rows['item_id']."' and order_id='".$sel_rows['order_id']."' and order_detail_id='".$sel_rows['id']."' and order_status='".$status."' and order_date='".$date."'";
		$res_deatil_status = imw_query($sel_deatil_status);
		$num_deatil_status = imw_num_rows($res_deatil_status);
		if($num_deatil_status == 0)
		{
			$ins = "insert in_order_detail_status set patient_id='".$sel_rows['patient_id']."', item_id='".$sel_rows['item_id']."', order_id='".$sel_rows['order_id']."', order_detail_id='".$sel_rows['id']."', order_qty='".($sel_rows['qty']+$sel_rows['qty_right'])."', order_status='".$status."', order_date='".$date."', order_time='".$time."', operator_id='".$sel_rows['operator_id']."'";
			$execut = imw_query($ins);
		
			if($status=="ordered")
			{
				$ordered_qry= ", ordered='$date'";
			}
			elseif($status=="received")
			{
				$ordered_qry= ", received='$date'";
			}
			elseif($status=="notified")
			{
				$ordered_qry= ", notified='$date'";
			}
			elseif($status=="dispensed")
			{
				$ordered_qry= ", dispensed='$date'";
				if($redc_stock=="yes")
				{
					if($sel_rows['module_type_id'] == 3){
						if($sel_rows['item_id']>0){
							deduct_item_qty($sel_rows['order_id'],$sel_rows['id'],$sel_rows['patient_id'],$sel_rows['item_id'],$sel_rows['qty_right']);
						}
						if($sel_rows['item_id_os']>0){
							deduct_item_qty($sel_rows['order_id'],$sel_rows['id'],$sel_rows['patient_id'],$sel_rows['item_id_os'],$sel_rows['qty']);
						}

					}
					else{
						deduct_item_qty($sel_rows['order_id'],$sel_rows['id'],$sel_rows['patient_id'],$sel_rows['item_id'],$sel_rows['qty']+$sel_rows['qty_right']);
					}
				}
			}
			
			imw_query("update in_order_details set order_status='$status' $ordered_qry , modified_date='$entered_date', modified_time='$entered_time', modified_by='$operator_id' where id='".$sel_rows['id']."'");
		}
	}
}

function update_in_order_status($id)
{
	$operator_id = $_SESSION['authId'];
	$entered_date = date("Y-m-d");
	$entered_time = date("h:i:s");
	$select_item = "select id, order_id, order_status from in_order_details where order_id='".$id."' and del_status='0'";
	$result = imw_query($select_item);
	$num_rows = imw_num_rows($result);
	if($num_rows > 0)
	{
		while($get_row = imw_fetch_array($result))
		{
			$status[] = $get_row['order_status'];
		}
		
		if (in_array("pending", $status) || in_array("", $status)) {
			$set_status = "pending";
		}
		elseif (in_array("ordered", $status)) {
			$set_status = "ordered";
		}
		elseif (in_array("received", $status)) {
			$set_status = "received";
		}
		elseif (in_array("dispensed", $status)) {
			$set_status = "dispensed";
		}
		elseif (in_array("notified", $status)) {
			$set_status = "notified";
		}
		else
		{
			$set_status = "pending";
		}
		
		imw_query("update in_order set order_status='".$set_status."', modified_date='$entered_date', modified_time='$entered_time', modified_by='$operator_id' where id='".$id."'");
		
		return true;
	}
}


function update_qty_price_order($id)
{
	$operator_id = $_SESSION['authId'];
	$entered_date = date("Y-m-d");
	$entered_time = date("h:i:s");
	$select_item = "select order_id, SUM(qty+qty_right) as total_qty, SUM(total_amount+total_amount_os) as total_price, module_type_id from in_order_details where order_id='$id' and del_status='0'";
	$result = imw_query($select_item);
	$num_rows = imw_num_rows($result);
	if($num_rows > 0)
	{
		$data = imw_fetch_array($result);
		/*Update details for contact lens Line tems other the CL*/
		if($data['module_type_id']="3"){
			$result1 = imw_query("SELECT SUM(`qty`) AS 't_qty', SUM(`price`) AS 't_price' FROM `in_order_cl_detail` WHERE `order_id`='".$id."' AND `del_status`=0");
			if($result1 && imw_num_rows($result1)>0){
				$data1 = imw_fetch_assoc($result1);
				$data['total_qty'] = $data['total_qty']+$data1['t_qty'];
				$data['total_price'] = $data['total_price']+$data1['t_price'];
			}
		}
		
		imw_query("update in_order set total_qty='".$data['total_qty']."', total_price='".$data['total_price']."', modified_date='$entered_date', modified_time='$entered_time', modified_by='$operator_id' where id='$id'");
	}
	return true;
}

function changeDateSelection(){
	$arrSearchDates=array();
	$day = date('w')-1;				
	if($day < 0){
		$StartDay = 6;
	}
	else{
		$StartDay = $day;
	}
	$newDate  = date('m-d-Y',mktime(0, 0, 0, date("m")  , date("d")-$StartDay, date("Y")));
	$monthDate  = date('m-d-Y',mktime(0, 0, 0, date("m")  , '01', date("Y")));
	
	$arr_quaMon = array('1' => 1, '2' => 4, '3' => 7, '4' => 10);
	$quarter = $arr_quaMon[ceil(date('n')/3)];
	
	$quarter = $quarter < 10 ? '0'.$quarter : $quarter;
	$quater_month_start = $quarter.'-01-'.date('Y');		
	$quater_month_end = date('m-d-Y',mktime(0,0,0,$quarter+3,1-1,date('Y')));		
	
	
	$arrSearchDates['WEEK_DATE']=$newDate;
	$arrSearchDates['MONTH_DATE']=$monthDate;
	$arrSearchDates['QUARTER_DATE_START']=$quater_month_start;
	$arrSearchDates['QUARTER_DATE_END']=$quater_month_end;
	return $arrSearchDates;
}

function update_ordre_del_status($order_id)
{
	$opr_id = $_SESSION['authId'];
	$date = date("Y-m-d");
	$time = date("h:i:s");
	$ord_arr = array();
	$or_sel = imw_query("select del_status from in_order_details where order_id='$order_id'");
	while($get_ord = imw_fetch_array($or_sel))
	{
		$ord_arr[] = $get_ord['del_status'];
	}
	if(!in_array("0",$ord_arr))
	{
		$or_st = imw_query("update in_order set del_status='1',del_date='$date',del_time='$time',del_operator_id='$opr_id' where id='$order_id'");
		
		return true;
	}
}

function other_order_action($action,$post_val)
{
	// echo"<pre>";
	// print_r($post_val);
	// echo"</pre>";
	// die('----');
	extract($post_val);
	$order_id="";
	if(isset($_SESSION['order_id']) && $_SESSION['order_id']!=""){
		$order_id=$_SESSION['order_id'];
	}
	$dataInsertFlag = false;
	$rec_counter=(isset($last_cont_lensD) && $last_cont_lensD>$last_cont)?$last_cont_lensD:$last_cont;
	
	$patient_id=$_SESSION['patient_session_id'];
	$operator_id=$_SESSION['authId'];
	
	if($_POST['order_date']){
		list($order_date_m, $order_date_d, $order_date_y)=explode('-',$_POST['order_date']);
		$entered_date="$order_date_y-$order_date_m-$order_date_d";
	}else $entered_date=date('Y-m-d');
	
	$entered_time=date('H:i:s');
	list($due_date_m, $due_date_d, $due_date_y)=explode('-',$_POST['due_date']);
	$due_date="$due_date_y-$due_date_m-$due_date_d";
	$en_date = date("Y-m-d H:i:s");
	if($action=="cancel"){
		if($cancel_order_detail_id>0){
			$order_detail_id=$cancel_order_detail_id;
			imw_query("update in_order_details set del_status='1',del_date='$entered_date',del_time='$entered_time',del_operator_id='$operator_id' where id='$order_detail_id'");
			$or_st = update_ordre_del_status($order_id);
		}else{
			for($i=1;$i<=$rec_counter;$i++){
				if($_POST['order_detail_id_'.$i]>0){
					$order_detail_id=$_POST['order_detail_id_'.$i];
					imw_query("update in_order_details set del_status='1',del_date='$entered_date',del_time='$entered_time',del_operator_id='$operator_id' where id='$order_detail_id'");
					
					if($_POST['module_type_id_'.$i]==3)
					{	
						imw_query("update in_cl_prescriptions set del_status='1',del_date='$entered_date',del_time='$entered_time',del_operator_id='$operator_id' where det_order_id='$order_detail_id'");
					}
					
					if($_POST['module_type_id_'.$i]==2)
					{
						imw_query("update in_optical_order_form set del_status='1',del_date='$entered_date',del_time='$entered_time',del_operator_id='$operator_id' where det_order_id='$order_detail_id'");
					
						imw_query("update in_order_lens_price_detail set del_status='1',del_date='$entered_date',del_time='$entered_time',del_operator_id='$operator_id' where order_detail_id='$order_detail_id'");
							
					}

					$or_st = update_ordre_del_status($order_id);
					
					if($or_st==true)
					{
						unset($_SESSION['order_id']);
					}			
				}
			}
			if($_POST['page_name']=="pos")
			{
				imw_query("update in_order_tax set del_status='1', del_date='$entered_date', del_time='$entered_time', del_by='$operator_id' where order_id='$order_id'");
			}
		}	
	}else{ 
		$order_facility_id = '0';
		if($order_id==""){
			$sel_qry="insert into in_order set patient_id='$patient_id',due_date='$due_date',entered_date='$entered_date',entered_time='$entered_time',operator_id='$operator_id',loc_id='".$_SESSION['pro_fac_id']."'";
			imw_query($sel_qry);
			$order_id=imw_insert_id();
			$order_facility_id = $_SESSION['pro_fac_id'];
			$_SESSION['order_id']=$order_id;
		}
		else{
			imw_query("update in_order set due_date='$due_date' WHERE `id`='".$_SESSION['order_id']."'");
			$fac_qry = imw_query("SELECT `loc_id` FROM `in_order` WHERE `id`='".$_SESSION['order_id']."'");
			if($fac_qry){
				$order_facility_id = imw_fetch_assoc($fac_qry);
				$order_facility_id = $order_facility_id['loc_id'];
			}
		}
		$all_dx_arr_seriz="";
		$all_dx_arr=array();
		$all_unq_dx_arr=array();
		for($k=1;$k<=$rec_counter;$k++){
			if($_POST['dx_code_'.$k]!=""){
				$dxx_exp_arr = explode(';',$_POST['dx_code_'.$k]);
				for($dg=0;$dg<count($dxx_exp_arr);$dg++)
				{
					$dx_arr_sin = trim($dxx_exp_arr[$dg]);
					$all_unq_dx_arr[$dx_arr_sin]=$dx_arr_sin;
				}
			}else if($_POST['dx_code_'.$k.'_lensD']!=""){
				$dxx_exp_arr = explode(';',$_POST['dx_code_'.$k.'_lensD']);
				for($dg=0;$dg<count($dxx_exp_arr);$dg++)
				{
					$dx_arr_sin = trim($dxx_exp_arr[$dg]);
					$all_unq_dx_arr[$dx_arr_sin]=$dx_arr_sin;
				}
			}
		}
		$h=1;
		for($g=1;$g<=12;$g++){
			$all_dx_arr[$g]='';
		}
		foreach($all_unq_dx_arr as $dx_val){
			$all_dx_arr[$h]=$dx_val;
			$h++;
		}
		$all_dx_arr_seriz = serialize($all_dx_arr);
		for($i=1;$i<=$rec_counter;$i++){
			$upc_name_qry="";
			$item_name_qry="";
			$item_name_other_qry="";
			$item_id_qry="";
			$module_type_id_qry="";
			$price_qry="";
			$allowed_qry="";
			$discount_qry="";
			$item_overall_discount_qry="";
			$total_amount_qry="";
			$qty_qry="";
			$pt_payed_qry="";
			$pt_resp_qry="";
			$ins_amount_qry="";
			$ins_id_qry="";
			$lens_frame_id_qry="";
			$order_detail_id_qry="";
			$show_defult_qry="";
			$ordered="";
			$received="";
			$notified="";
			$dispensed="";
			$status_update="";
			$lab_id_qry="";
			$order_detailid="";
			$total_amount_proc = "";
			$discounts = "";
			$totqty="";
			$diagnosis="";
			$dx_code_qry="";
			$discount_code_qry="";
			$lens_grand_price="";
			$item_lens_grand_price_qry="";
			$item_lens_grand_total_qry="";
			$item_lens_grand_disc_qry="";
			$overall_discount_qry="";
			$pt_paid = "";
			$paymentMode="";
			$checkNo="";
			$creditCardCo="";
			$cCNo="";
			$expireDate="";
			$item_pay_qry="";
			$pt_resp="";
			$ins_amount="";
			$allowed="";
			$item_disc_qry="";
			$tax_qry="";
			$grand_total_qry="";
			
			if(isset($_POST['tax_prac_code'])){
				$tax_qry=",tax_prac_code='".$_POST['tax_prac_code']."', tax_payable='".$_POST['tax_payable']."', tax_pt_paid='".$_POST['tax_pt_paid']."', tax_pt_resp='".$_POST['tax_pt_resp']."', `tax_custom`='".$_POST['tax_custom']."'";
			}
			if(isset($_POST['grand_total'])){
				$grand_total_qry=", grand_total='".$_POST['grand_total']."'";
			}
			if(isset($_POST['paymentMode']) && $_POST['paymentMode']!=""){
				$pt_paid=$_POST['pt_paid_'.$i];
				$paymentMode=$_POST['paymentMode'];
				if($paymentMode == 'Check' or $paymentMode == 'EFT' or $paymentMode == 'Money Order'){
					$checkNo=$_POST['checkNo'];
				}else if($paymentMode == 'Credit Card'){
					$cCNo=$_POST['cCNo'];
					$creditCardCo=$_POST['creditCardCo'];
					$expireDate=$_POST['expireDate'];
				}
				$item_pay_qry=",payment_mode='".$paymentMode."',checkNo='".$checkNo."',creditCardNo='".$cCNo."',creditCardCo='".$creditCardCo."',expirationDate='".$expireDate."'";
			}
			
			if(isset($_POST['total_overall_discount'])){
				$overall_discount =  preg_replace("/[^\d]/", "", $_POST['overall_discount']);
				$total_overall_discount = $_POST['total_overall_discount'];
				if($overall_discount<=0){
					$total_overall_discount = 0;
				}
				$overall_discount =  preg_replace("/[^\d\%\.]/", "", $_POST['overall_discount']);
				if($total_overall_discount!="" || $total_overall_discount==0){
					$item_disc_qry=", overall_discount='".$overall_discount."', overall_discount_code=".((int)$_POST['overall_discount_code']).", total_overall_discount='".$total_overall_discount."', overall_discount_prac_code='".$_POST['overall_discount_prac_code']."'";
				}
			}
			
			if(isset($_POST['del_status_'.$i]) && $_POST['del_status_'.$i]=="1" && $_POST['order_detail_id_'.$i]!=""){
				imw_query("UPDATE `in_order_details` SET
							 del_status='1',
							 del_date='$entered_date',
							 del_time='$entered_time',
							 del_operator_id='$operator_id'
							 WHERE
								`order_id`='".$order_id."' AND
								`id`='".$_POST['order_detail_id_'.$i]."'
							");
				if($_POST['module_type_id_'.$i]==2){
					imw_query("UPDATE `in_order_lens_price_detail` SET
								 del_status='1',
								 del_date='$entered_date',
								 del_time='$entered_time',
								 del_operator_id='$operator_id'
								 WHERE
									`order_id`='".$order_id."' AND
									`order_detail_id`='".$_POST['order_detail_id_'.$i]."'
								");
				}
				continue;
			}
			
			if(isset($_POST['upc_name_'.$i])){
				$upc_name_qry=",upc_code='".$_POST['upc_name_'.$i]."'";
			}
			if(isset($_POST['item_name_'.$i])){
				if($_POST['module_type_id_'.$i]==1)
				{
					$item_name_other_qry=",item_name_other='".imw_real_escape_string(htmlentities($_POST['item_name_'.$i.'_other'], ENT_QUOTES, 'UTF-8'))."'";
				}
				
				$item_name_qry=",item_name='".imw_real_escape_string(htmlentities($_POST['item_name_'.$i], ENT_QUOTES, 'UTF-8'))."'";
			}
			if(isset($_POST['item_id_'.$i])){
				$item_id_qry=",item_id='".$_POST['item_id_'.$i]."'";
			}
			if(isset($_POST['module_type_id_'.$i])){
				$module_type_id_qry=",module_type_id='".$_POST['module_type_id_'.$i]."'";
			}
			if(isset($_POST['rtl_price_'.$i])){
				$price_qry=",price_retail='".$_POST['rtl_price_'.$i]."'";
			}
			if(isset($_POST['price_'.$i])){
				$price_qry1=",price='".$_POST['price_'.$i]."'";
			}
			if(isset($_POST['allowed_'.$i])){
				if($_POST['allowed_'.$i]<=0){
					$allowed_qry=",allowed='".$_POST['total_amount_'.$i]."'";
					$allowed=$_POST['price_'.$i];
				}else{
					
					if($_POST['module_type_id_'.$i]==3)
					{
						$allowed_qry=",allowed='".$_POST['pos_allowed_'.$i]."'";
						$allowed=$_POST['pos_allowed_'.$i];	
					}
					else
					{
						$allowed_price  = $_POST['price_'.$i] * $_POST['qty_'.$i];
						$allowed_qry=",allowed='".$allowed_price."'";
						$allowed=$allowed_price;
					}
				}
			}
			if(isset($_POST['discount_'.$i])){
				$discount_qry=",discount='".$_POST['discount_'.$i]."'";
			}
			if($_POST['module_type_id_'.$i]!=2 && isset($_POST['item_overall_discount_'.$i])){
				$item_overall_discount_qry=",overall_discount='".$_POST['item_overall_discount_'.$i]."'";
			}
			if($_POST['module_type_id_'.$i]==2){
				$discount_qry=",discount='".$_POST['discount_hidden_'.$i]."'";
			}
			
			if(isset($_POST['discount_code_'.$i])){
				$discount_code_qry=",discount_code='".$_POST['discount_code_'.$i]."'";
			}
			if(isset($_POST['total_amount_'.$i])){
				$total_amount_qry=",total_amount='".$_POST['total_amount_'.$i]."'";
			}
			if(isset($_POST['qty_'.$i])){
				$qty_qry=",qty='".$_POST['qty_'.$i]."'";
			}
			if(isset($_POST['qty_right_'.$i])){
				$qty_right_qry=",qty_right='".$_POST['qty_right_'.$i]."'";
			}
			if(isset($_POST['pt_paid_'.$i]) && $_POST['module_type_id_'.$i]!=2){
				$pt_paid_qry=",pt_paid='".$_POST['pt_paid_'.$i]."'";
			}
			
			
			if(isset($_POST['pt_resp_'.$i])){
				$pt_resp_qry=",pt_resp='".$_POST['pt_resp_'.$i]."'";
				$pt_resp=$_POST['pt_resp_'.$i];
			}
			if(isset($_POST['ins_amount_'.$i])){
				$ins_amount_qry=",ins_amount='".$_POST['ins_amount_'.$i]."'";
				$ins_amount=$_POST['ins_amount_'.$i];
			}
			$ord_ins_case_id="";
			$ord_ins_id="";
			if(isset($_POST['ins_case_id_'.$i])){
				$ord_ins_case_id=$_POST['ins_case_id_'.$i];
				$case_ins_id_qry=",ins_case_id='".$_POST['ins_case_id_'.$i]."'";
			}
			if(isset($_POST['ins_id_'.$i])){
				$ord_ins_id=$_POST['ins_id_'.$i];
				$ins_id_qry=",ins_id='".$_POST['ins_id_'.$i]."'";
			}
			
			if($_POST['page_name']=="pt_frame_selection")
			{
				$use_on_hand_chk_qry=",use_on_hand_chk='".(isset($_POST['use_on_hand_chk_'.$i])?$_POST['use_on_hand_chk_'.$i]:0)."' , order_chk='".(isset($_POST['order_chk_'.$i])?$_POST['order_chk_'.$i]:0)."',pof_check='".(isset($_POST['in_add_'.$i])?$_POST['in_add_'.$i]:0)."'";				
			}
			
			if($_POST['page_name']=="contact_selection")
			{
				$cont_use_on_hand_chk_qry=",use_on_hand_chk='".$_POST['use_on_hand_chk_'.$i]."' , order_chk='".$_POST['order_chk_'.$i]."'";				
			}
			
			if($_POST['page_name']=="lens_selection")
			{
				if($_POST['uv400_'.$i]=="on"){
					$uv400_qry=",uv400='1'";
				}
				else
				{
					$uv400_qry=",uv400='0'";
				}
				if($_POST['pgx_'.$i]=="on"){
					$pgx_qry=",pgx='1'";
				}
				else
				{
					$pgx_qry=",pgx='0'";
				}
				if($_POST['tap_'.$i]=="on"){
					$tap_qry=",tap='1'";
				}
				else
				{
					$tap_qry=",tap='0'";
				}
			}
			
			if(isset($_POST['item_comment_'.$i])){
				$item_comment_qry=",item_comment='".imw_real_escape_string($_POST['item_comment_'.$i])."'";
			}
			if(isset($_POST['lens_frame_id_'.$i])){
				$lens_frame_id_qry=",lens_frame_id='".$_POST['lens_frame_id_'.$i]."'";
			}
			if($_POST['order_detail_id_'.$i]>0){
				$order_detail_id_qry=" and id='".$_POST['order_detail_id_'.$i]."'";
			}
			
			if($_POST['module_type_id_'.$i]!=""){
				
				/*Commented beacuse bug due to this code. -- Dual saving or Rx*/
				/*if($_POST['module_type_id_'.$i]=="2"){ 
					update_lens_pres($_POST['order_rx_lens_id_'.$i],$order_detail_id,$order_id,$patient_id,$post_val);
				}*/
				
				if( $_POST['upc_name_'.$i]!="" || $_POST['item_name_'.$i]!="" || ( ($_POST['upc_name_'.$i.'_os']!='' || $_POST['item_name_'.$i.'_os']!='') && $_POST['module_type_id_'.$i] == "3" ) ){
					$order_index = "";
					if($order_detail_id_qry!=""){
						$sel_detail_qry="update in_order_details ";
						$whr_qry=", modified_date='$entered_date', modified_time='$entered_time', modified_by='$operator_id' Where del_status='0' and order_id='$order_id' and patient_id='$patient_id'".$order_detail_id_qry;
					}else{
						$dataInsertFlag = true;
						$sel_detail_qry="insert into in_order_details ";
						$whr_qry="";
						$order_index = "order_index=".$i.", ";
						$add_new_rec_date=",entered_date='$entered_date',entered_time='$entered_time',operator_id='$operator_id',loc_id='$order_facility_id'";
					}
					if($_POST['order_detail_id_'.$i]>0){
						$chk_ord_qry=imw_query("select item_id from in_order_details $whr_qry");
						$chk_ord_row=imw_fetch_array($chk_ord_qry);
					}
					$chk_item_id="";
					if(isset($chk_ord_row['item_id']) && $_POST['item_id_'.$i]!=$chk_ord_row['item_id']){
						$chk_item_id=$_POST['item_id_'.$i];
						$chk_item_qry=imw_query("select * from in_item where id='$chk_item_id'");
						$chk_item_row=imw_fetch_array($chk_item_qry);
					}
					if($_POST['module_type_id_'.$i]=="2")
					{
						if(isset($_POST['item_lens_grand_price'])){
							$lens_grand_price = $_POST['item_lens_grand_price'];
							$item_lens_grand_price_qry=",price='".$lens_grand_price."'";
							$price_qry = "";
						}		
						if(isset($_POST['item_lens_grand_total'])){
							$lens_grand_total = $_POST['item_lens_grand_total'];
							$item_lens_grand_total_qry=",total_amount='".$lens_grand_total."'";
							$total_amount_qry = "";
						}				
						if(isset($_POST['item_lens_grand_disc'])){
							$lens_grand_discount = $lens_grand_price-$lens_grand_total;
							$item_lens_grand_disc_qry=",discount='".$lens_grand_discount."'";
							$discount_qry = "";
						}					
					}
					if($_POST['page_name']!="pos" || $_POST['module_type_id_'.$i]=="3")
					{
						if(isset($_POST['manufacturer_id_'.$i])){
							$manufacturer_id_qry=",manufacturer_id='".$_POST['manufacturer_id_'.$i]."'";
						}else{
							if($chk_item_id>0){
								$manufacturer_id_qry=",manufacturer_id='".$chk_item_row['manufacturer_id']."'";
							}
						}
						if(isset($_POST['brand_id_'.$i])){
							$brand_id_qry=",brand_id='".$_POST['brand_id_'.$i]."'";
						}else{
							if($chk_item_id>0){
								$brand_id_qry=",brand_id='".$chk_item_row['brand_id']."'";
							}
						}
					}
					
					if(isset($_POST['shape_id_'.$i])){
						if($_POST['shape_id_'.$i]=="other"){
							$shape_id_qry=",shape_id=0, shape_other='".$_POST['shape_other_'.$i]."'";
						}
						else
							$shape_id_qry=",shape_id='".$_POST['shape_id_'.$i]."', shape_other=''";
					}else{
						if($chk_item_id>0){
							$shape_id_qry=",shape_id='".$chk_item_row['frame_shape']."'";
						}
					}
					
					if(isset($_POST['style_id_'.$i]) && $_POST['module_type_id_'.$i]=="1"){
						if($_POST['style_id_'.$i]=="other"){
							$style_id_qry=",style_id=0, style_other='".$_POST['style_other_'.$i]."'";
						}
						else
							$style_id_qry=",style_id='".$_POST['style_id_'.$i]."', style_other=''";
					}else{
						if($chk_item_id>0){
							$style_id_qry=",style_id='".$chk_item_row['frame_style']."'";
						}
					}
					
					if(isset($_POST['other_style_'.$i])){						
						
					if($_POST['other_style_'.$i]!="")
					{
						$mq = imw_query("select id from in_frame_styles where style_name = '".trim($_POST['other_style_'.$i])."' and del_status!=2");
						if(imw_num_rows($mq)>0)
						{
							$style_id_qry="";
							$mqr=imw_fetch_assoc($mq);
							$frame_style_val=$mqr['id'];
							$style_id_qry=",style_id='".$frame_style_val."'";
							
						$brandst = imw_query("insert into in_style_brand set style_id = '".$frame_style_val."' , brand_id = '".$_POST['brand_id_'.$i]."' ");
							
						}
						else
						{
							$style_id_qry="";
							$othrfrmins=imw_query("insert into in_frame_styles set style_name = '".trim($_POST['other_style_'.$i])."' ");	
							$frame_style_val=imw_insert_id();
							
							
							$brandst = imw_query("insert into in_style_brand set style_id = '".$frame_style_val."' , brand_id = '".$_POST['brand_id_'.$i]."' ");
							
							$style_id_qry=",style_id='".$frame_style_val."'";
						}
					}

						//$style_other_qry=",style_other='".$_POST['other_style_'.$i]."'";
					}else{
						if($chk_item_id>0){
							//$style_other_qry=",style_other='".$chk_item_row['style_other']."'";
						}
					}					

					if(isset($_POST['cl_for_od_'.$i])){
						$cl_for_od_qry=",cl_for_od='".$_POST['cl_for_od_'.$i]."'";
					}

					if(isset($_POST['cl_for_os_'.$i])){
						$cl_for_os_qry=",cl_for_os='".$_POST['cl_for_os_'.$i]."'";
					}
					
					$color_id_qry = '';
					if( $_POST['module_type_id_'.$i]=='1' && isset($_POST['color_id_'.$i]) ){
						$colorName = $_POST['color_id_'.$i];
						$sqlColorId = 'SELECT `id` FROM `in_frame_color` WHERE `color_name`=\''.addslashes($colorName).'\'';
						$sqlColorId = imw_query($sqlColorId);
						if($sqlColorId && imw_num_rows($sqlColorId)>0 ){
							$sqlColorId = imw_fetch_assoc($sqlColorId);
							$color_id_qry .= ",color_id=".$sqlColorId['id'].", color_other=''";
						}
						else{
							$color_id_qry=",color_id=0, color_other='".addslashes($colorName)."'";
						}
					}
					else{
						if(isset($_POST['color_id_'.$i])){
							if($_POST['color_id_'.$i]=="other"){
								$color_id_qry=",color_id=0, color_other='".$_POST['color_other_'.$i]."'";
							}
							else
								$color_id_qry=",color_id='".$_POST['color_id_'.$i]."', color_other=''";
						}else{
							if($chk_item_id>0){
								if($_POST['page_name']!="pos")
								{
									$color_id_qry=",color_id='".$chk_item_row['color']."', color_other=''";
								}
							}
						}
					}
					
					/*Color Code for Frame and Job Type*/
					if($_POST['module_type_id_'.$i]=="1"){
						$color_id_qry .= ",color_code='".$_POST['color_code_'.$i]."'";
					}
					
					$job_type_qry = '';
					if($_POST['module_type_id_'.$i]=="1" || $_POST['module_type_id_'.$i]=="2" ){
						if(isset($_POST['job_type_'.$i])){
							$job_type_qry=",job_type='".$_POST['job_type_'.$i]."'";
						}else{
							if($chk_item_id>0){
								$job_type_qry=",job_type='".$chk_item_row['job_type']."'";
							}
						}
					}
					
					if($_POST['page_name']!="pos" && $_POST['module_type_id_'.$i]!=3)
					{
						if(isset($_POST['color_id_os_'.$i])){
							$color_id_os_qry=",color_id_os='".$_POST['color_id_os_'.$i]."'";
						}else{
							if($chk_item_id>0){
								$color_id_os_qry=",color_id_os='".$chk_item_row['color']."'";
							}
						}
	
						if(isset($_POST['manufacturer_id_os_'.$i])){
							$manufacturer_id_os_qry=",manufacturer_id_os='".$_POST['manufacturer_id_os_'.$i]."'";
						}else{
							if($chk_item_id>0){
								$manufacturer_id_os_qry=",manufacturer_id_os='".$chk_item_row['manufacturer_id']."'";
							}
						}
	
						if(isset($_POST['brand_id_os_'.$i])){
							$brand_id_os_qry=",brand_id_os='".$_POST['brand_id_os_'.$i]."'";
						}else{
							if($chk_item_id>0){
								$brand_id_os_qry=",brand_id_os='".$chk_item_row['brand_id']."'";
							}
						}
					}
					if(isset($_POST['style_id_os_'.$i])){
						$style_id_os_qry=",style_id_os='".$_POST['style_id_os_'.$i]."'";
					}else{
						if($chk_item_id>0){
							$style_id_os_qry=",style_id_os='".$chk_item_row['style']."'";
						}
					}
										
					if(isset($_POST['a_'.$i])){
						$a_qry=",a='".$_POST['a_'.$i]."'";
					}else{
						if($chk_item_id>0){
							$a_qry=",a='".$chk_item_row['a']."'";
						}
					}
					
					if(isset($_POST['b_'.$i])){
						$b_qry=",b='".$_POST['b_'.$i]."'";
					}else{
						if($chk_item_id>0){
							$b_qry=",b='".$chk_item_row['b']."'";
						}
					}
					
					if(isset($_POST['ed_'.$i])){
						$ed_qry=",ed='".$_POST['ed_'.$i]."'";
					}else{
						if($chk_item_id>0){
							$ed_qry=",ed='".$chk_item_row['ed']."'";
						}
					}

					if(isset($_POST['dbl_'.$i])){
						$dbl_qry=",dbl='".$_POST['dbl_'.$i]."'";
					}else{
						if($chk_item_id>0){
							$dbl_qry=",dbl='".$chk_item_row['dbl']."'";
						}
					}

					if(isset($_POST['temple_'.$i])){
						$temple_qry=",temple='".$_POST['temple_'.$i]."'";
					}else{
						if($chk_item_id>0){
							$temple_qry=",temple='".$chk_item_row['temple']."'";
						}
					}
					
					if(isset($_POST['bridge_'.$i])){
						$bridge_qry=",bridge='".$_POST['bridge_'.$i]."'";
					}else{
						if($chk_item_id>0){
							$bridge_qry=",bridge='".$chk_item_row['bridge']."'";
						}
					}
					
					if(isset($_POST['fpd_'.$i])){
						$fpd_qry=",fpd='".$_POST['fpd_'.$i]."'";
					}else{
						if($chk_item_id>0){
							$fpd_qry=",fpd='".$chk_item_row['fpd']."'";
						}
					}
					
					if(isset($_POST['contact_cat_id_'.$i])){
						$contact_cat_id_qry=",contact_cat_id='".$_POST['contact_cat_id_'.$i]."'";
					}else{
						if($chk_item_id>0){
							$contact_cat_id_qry=",contact_cat_id='".$chk_item_row['contact_cat_id']."'";
						}
					}
					
					if(isset($_POST['cl_replacement_'.$i])){
						$cont_replac_qry=",cl_replacement_id='".$_POST['cl_replacement_'.$i]."'";
					}else{
						if($chk_item_id>0){
							$cont_replac_qry=",cl_replacement_id='".$chk_item_row['cl_replacement_id']."'";
						}
					}
					
					if(isset($_POST['cl_packaging_'.$i])){
						$cont_package_qry=",cl_packaging_id='".$_POST['cl_packaging_'.$i]."'";
					}else{
						if($chk_item_id>0){
							$cont_package_qry=",cl_packaging_id='".$chk_item_row['cl_packaging_id']."'";
						}
					}
					
					if(isset($_POST['cl_wear_sch_'.$i])){
						$cont_wear_sch_qry=",cl_wear_sch_id='".$_POST['cl_wear_sch_'.$i]."'";
					}else{
						if($chk_item_id>0){
							$cont_wear_sch_qry=",cl_wear_sch_id='".$chk_item_row['cl_wear_sch_id']."'";
						}
					}
					
					if(isset($_POST['supply_id_'.$i])){
						$supply_id_qry=",supply_id='".$_POST['supply_id_'.$i]."'";
					}else{
						if($chk_item_id>0){
							$supply_id_qry=",supply_id='".$chk_item_row['supply_id']."'";
						}
					}
					
					if(isset($_POST['type_id_'.$i])){
						if($_POST['module_type_id_'.$i]!=2){
							$type_id_qry=",type_id='".$_POST['type_id_'.$i]."'";
						}
					}else{
						if($chk_item_id>0){
							if($_POST['page_name']!="pos")
							{
								$type_id_qry=",type_id='".$chk_item_row['type_id']."'";
							}
						}
					}
					
					if(isset($_POST['progressive_id_'.$i])){
						$progressive_id_qry=",progressive_id='".$_POST['progressive_id_'.$i]."'";
					}else{
						if($chk_item_id>0){
							if($_POST['page_name']!="pos")
							{
								$progressive_id_qry=",progressive_id='0'";
							}
						}
					}
					
					if(isset($_POST['material_id_'.$i])){
						$material_id_qry=",material_id='".$_POST['material_id_'.$i]."'";
					}else{
						if($chk_item_id>0){
							if($_POST['page_name']!="pos")
							{
								$material_id_qry=",material_id='".$chk_item_row['material_id']."'";
							}
						}
					}
					
					if(isset($_POST['transition_id_'.$i])){
						$transition_id_qry=",transition_id='".$_POST['transition_id_'.$i]."'";
					}else{
						if($chk_item_id>0){
							if($_POST['page_name']!="pos")
							{
								$transition_id_qry=",transition_id='".$chk_item_row['transition_id']."'";
							}
						}
					}
					
					if(isset($_POST['a_r_id_'.$i])){
						//$a_r_id_qry=",a_r_id='".implode(";",$_POST['a_r_id_'.$i])."'";
					}
					else{
						if($_POST['module_type_id_'.$i]==2 && $_POST['page_name']=="lens_selection"){
							//$a_r_id_qry=",a_r_id=''";
						}
						
						if($chk_item_id>0){
							if($_POST['page_name']!="pos")
							{
								//$a_r_id_qry=",a_r_id='".$chk_item_row['a_r_id']."'";
							}
						}
					}
					
					if(isset($_POST['tint_id_'.$i])){
						$tint_id_qry=",tint_id='".$_POST['tint_id_'.$i]."'";
					}else{
						if($chk_item_id>0){
							if($_POST['page_name']!="pos")
							{
								$tint_id_qry=",tint_id='".$chk_item_row['tint_id']."'";
							}
						}
					}
					
					if(isset($_POST['polarized_id_'.$i])){
						$polarized_id_qry=",polarized_id='".$_POST['polarized_id_'.$i]."'";
					}else{
						if($chk_item_id>0){
							if($_POST['page_name']!="pos")
							{
								$polarized_id_qry=",polarized_id='".$chk_item_row['polarized_id']."'";
							}
						}
					}
					
					/*if(isset($_POST['trial_chk_'.$i])){
						if($chk_item_id>0){
							$trial_id_qry=",trial_chk='".$chk_item_row['trial_chk']."'";
						}
					}*/
					
					if(isset($_POST['edge_id_'.$i])){
						$edge_id_qry=",edge_id='".$_POST['edge_id_'.$i]."'";
					}else{
						if($chk_item_id>0){
							if($_POST['page_name']!="pos")
							{
								$edge_id_qry=",edge_id='".$chk_item_row['edge_id']."'";
							}
						}
					}
					
					if($_POST['page_name']=="lens_selection")
					{
						if(isset($_POST['other_'.$i])){
							$lens_other_qry=",lens_other='". imw_real_escape_string($_POST['other_'.$i])."'";
						}
						else
						{
							$lens_other_qry=",lens_other=''";
						}
						
						if(isset($_POST['overall_lens_discount_1'])){
							$overall_discount_qry=",overall_discount='".$_POST['overall_lens_discount_1']."'";
						}
					}

					if(isset($_POST['item_lens_selections_'.$i])){
						$item_lens_selections_qry=",lens_selection_id='".$_POST['item_lens_selections_'.$i]."'";
					}
					
					if($_POST['page_name']=="pos")
					{
						$order_detailid = $_POST['order_detail_id_'.$i];
						if($_POST['ordered_chk_'.$i]=="1"){
							$ordered=saveDateFormat($_POST['ordered_'.$i]);
							$status_update = "ordered";
							change_item_status($order_detailid, $status_update, $ordered);
						}
						if($_POST['received_chk_'.$i]=="1"){
							$received=saveDateFormat($_POST['received_'.$i]);
							$status_update = "received";
							change_item_status($order_detailid, $status_update, $received);
						}
						if($_POST['notified_chk_'.$i]=="1"){
							$notified=saveDateFormat($_POST['notified_'.$i]);
							$status_update = "notified";
							change_item_status($order_detailid, $status_update, $notified);
						}
						if($_POST['dispensed_chk_'.$i]=="1"){
							$dispensed=saveDateFormat($_POST['dispensed_'.$i]);
							$status_update = "dispensed";
							change_item_status($order_detailid, $status_update, $dispensed);
						}
						update_in_order_status($order_id);				
					}
					
					/*if(isset($_POST['lab_id_chk_'.$i])){
						if($_POST['lab_id_chk_'.$i]=="on"){
							$lab_id_qry=",lab_id='".$_POST['lab_id_'.$i]."'";
						}else{
							$lab_id_qry=",lab_id=''";
						}
					}*/
					if($_POST['module_type_id_'.$i]==2 && isset($_POST['lab_id_'.$i])){
						$lab_id_qry=",lab_id='".$_POST['lab_id_'.$i]."'";
					}
					else{
						$lab_id_qry=",lab_id=''";
					}
					
					if(isset($_POST['pt_wear_pic_'.$i])){
						$pt_wear_pic_qry=",pt_wear_pic='".$_POST['pt_wear_pic_'.$i]."'";
					}
					
					if(isset($_POST['item_prac_code_'.$i])){
						$item_prac_code=$_POST['item_prac_code_'.$i];
						if($_POST['module_type_id_'.$i]==2)
						{
							if($item_prac_code>0){
								$procedureId = $item_prac_code;
							}else{
								$procedureId = back_prac_id($item_prac_code,false,$_POST['module_type_id_'.$i]);
							}
						}
						else
						{
							$procedureId = back_prac_id($item_prac_code,false,$_POST['module_type_id_'.$i]);
						}
						$item_prac_code_qry=",item_prac_code='".$procedureId."'";
						if($_POST['module_type_id_'.$i]==1){	/*Fix for saving default Practice code*/
							$item_prac_code = $_POST['pos_item_prac_code_'.$i];
							$procedureId = back_prac_id($item_prac_code,false,$_POST['module_type_id_'.$i]);
							
							$item_prac_code_qry =",item_prac_code='".$procedureId."',item_prac_code_default='".$item_prac_code."'";
						}
					}else{
						if($chk_item_id>0){
							$item_prac_code_qry=",item_prac_code='".$chk_item_row['item_prac_code']."'";
						}
					}
					if($_POST['page_name']=="pt_frame_selection" || $_POST['page_name']=="lens_selection" || $_POST['page_name']=="contact_selection" || $_POST['otherTempPage']=='other_selPage')
					{
						if(isset($_POST['dx_code_'.$i])){
							$dx_expl = explode(';',$_POST['dx_code_'.$i]);
							$dx_code = array();
							for($df=0;$df<count($dx_expl);$df++)
							{
								if(in_array($_POST['module_type_id_'.$i], array(2,3))){	/*ICD 10 Codes*/
									$dx_code[]=trim($dx_expl[$df]);
								}
								else{
									$dx_code[]=back_dx_id(trim($dx_expl[$df]));
								}
							}
							$dx_all_code = join(',',$dx_code);
							$dx_code_qry=",dx_code='".$dx_all_code."'";
						}
					}
					if(isset($_POST['otherTempPage']) && $_POST['otherTempPage']=='other_selPage')
					{
						if($chk_item_id>0){
							$uv400_qry=",uv400='".$chk_item_row['uv_check']."'";
							$pgx_qry=",pgx='".$chk_item_row['pgx_check']."'";
						}
					}
					
					if($_POST['page_name']!="pos" && isset($_POST['otherTempPage']) && $_POST['otherTempPage']!='other_selPage'){
						$module_type_id=$_POST['module_type_id_'.$i];
						imw_query("update in_order_details set show_default='0', modified_date='$entered_date', modified_time='$entered_time', modified_by='$operator_id' where order_id='$order_id' and module_type_id='$module_type_id'");
						$show_default_qry=",show_default='1'";
					}
					
					$contact_lens_fields = "";
					if($_POST['module_type_id_'.$i]==3){
						$contact_lens_fields .= ", `contact_sphere_min_od`='".$_POST['cl_sphere_min_'.$i]."'";
						$contact_lens_fields .= ", `contact_sphere_max_od`='".$_POST['cl_sphere_max_'.$i]."'";
						$contact_lens_fields .= ", `contact_cylinder_min_od`='".$_POST['cl_cyl_min_'.$i]."'";
						$contact_lens_fields .= ", `contact_cylinder_max_od`='".$_POST['cl_cyl_max_'.$i]."'";
						$contact_lens_fields .= ", `contact_axis_min_od`='".$_POST['cl_axis_min_'.$i]."'";
						$contact_lens_fields .= ", `contact_bc_od`='".$_POST['cl_bc_min_'.$i]."'";
						$contact_lens_fields .= ", `contact_diameter_od`='".$_POST['cl_dia_min_'.$i]."'";
						$contact_lens_fields .= ", `contact_sphere_min_os`='".$_POST['cl_sphere_min_os_'.$i]."'";
						$contact_lens_fields .= ", `contact_sphere_max_os`='".$_POST['cl_sphere_max_os_'.$i]."'";
						$contact_lens_fields .= ", `contact_cylinder_min_os`='".$_POST['cl_cyl_min_os_'.$i]."'";
						$contact_lens_fields .= ", `contact_cylinder_max_os`='".$_POST['cl_cyl_max_os_'.$i]."'";
						$contact_lens_fields .= ", `contact_axis_min_os`='".$_POST['cl_axis_min_os_'.$i]."'";
						$contact_lens_fields .= ", `contact_bc_os`='".$_POST['cl_bc_min_os_'.$i]."'";
						$contact_lens_fields .= ", `contact_diameter_os`='".$_POST['cl_dia_min_os_'.$i]."'";
						$contact_lens_fields .= ", `contact_usage`='".$_POST['cl_usage_'.$i]."'";
						$contact_lens_fields .= ", `contact_type`='".$_POST['cl_type_'.$i]."'";
						$contact_lens_fields .= ", `trial_chk`='".$_POST['trial_chk_'.$i]."'";
						$contact_lens_fields .= ", `contact_disinfecting`='".$_POST['cl_disinfecting_'.$i]."'";
						$contact_lens_fields .= ", `vendor_id`='".$_POST['item_vendor_'.$i]."'";
						
						/*OS Fields*/
						$contact_lens_fields .= ", `item_id_os`='".$_POST['item_id_'.$i.'_os']."'";
						$contact_lens_fields .= ", `upc_code_os`='".$_POST['upc_name_'.$i.'_os']."'";
						$contact_lens_fields .= ", `total_amount_os`='".$_POST['total_amount_'.$i.'_os']."'";
						$contact_lens_fields .= ", `item_name_os`='".imw_real_escape_string(htmlentities($_POST['item_name_'.$i.'_os'], ENT_QUOTES, 'UTF-8'))."'";
						$contact_lens_fields .= ", `price_retail_os`='".$_POST['rtl_price_'.$i.'_os']."'";
						$contact_lens_fields .= ", `price_os`='".$_POST['price_'.$i.'_os']."'";
						$contact_lens_fields .= ", `discount_os`='".$_POST['discount_'.$i.'_os']."'";
						$contact_lens_fields .= ", `tax_rate_os`='".$_POST['tax_p_'.$i.'_os']."'";
						$contact_lens_fields .= ", `tax_paid_os`='".$_POST['tax_v_'.$i.'_os']."'";
						$contact_lens_fields .= ", `tax_applied_os`='".$_POST['tax_applied_'.$i.'_os']."'";
						$contact_lens_fields .= ", `ins_amount_os`='".$_POST['ins_amount_'.$i.'_os']."'";
						$contact_lens_fields .= ", `discount_code_os`='".$_POST['discount_code_'.$i.'_os']."'";
						$contact_lens_fields .= ", `ins_case_id_os`='".$_POST['ins_case_id_'.$i.'_os']."'";
						$contact_lens_fields .= ", `pt_paid_os`='".$_POST['pt_paid_'.$i.'_os']."'";
						$contact_lens_fields .= ", `pt_resp_os`='".$_POST['pt_resp_'.$i.'_os']."'";
						$contact_lens_fields .= ", `overall_discount_os`='".$_POST['item_overall_discount_'.$i.'_os']."'";
						
						$contact_lens_fields .= ", `manufacturer_id_os`='".$_POST['manufacturer_id_'.$i.'_os']."'";
						$contact_lens_fields .= ", `brand_id_os`='".$_POST['brand_id_'.$i.'_os']."'";
						$contact_lens_fields .= ", `vendor_id_os`='".$_POST['item_vendor_'.$i.'_os']."'";
						$contact_lens_fields .= ", `item_prac_code_os`='".back_prac_id($_POST['item_prac_code_'.$i.'_os'])."'";
						$contact_lens_fields .= ", `allowed_os`='".$_POST['pos_allowed_'.$i.'_os']."'";
						$contact_lens_fields .= ", `trial_chk_os`='".$_POST['trial_chk_'.$i.'_os']."'";
						
						if(isset($_POST['dominant_eye_'.$i])){
							$contact_lens_fields .= ", `dominant_eye`='".$_POST['dominant_eye_'.$i]."'";
						}
						if(isset($_POST['fit_type_'.$i])){
							$contact_lens_fields .= ", `fit_type`='".$_POST['fit_type_'.$i]."'";
						}
						//$contact_lens_fields .= ", `order_chld_id_os`='".$_POST[''.$i.'_os']."'";
						//$contact_lens_fields .= ", `discount_val_os`='".$_POST[''.$i.'_os']."'";
					}
					
					$lens_data = '';
					$visionweb_fields = "";
					if($_POST['module_type_id_'.$i]==2){
						//$visionweb_fields .= ", `design_id_od`='".$_POST['design_id_'.$i]."'";
						$visionweb_fields .= ", `he_coeff`='".$_POST['he_coeff_'.$i]."'";
						$visionweb_fields .= ", `st_coeff`='".$_POST['st_coeff_'.$i]."'";
						$visionweb_fields .= ", `nhp_cape`='".$_POST['nhp_cape_'.$i]."'";
						$visionweb_fields .= ", `progression_Len`='".$_POST['progression_Len_'.$i]."'";
						$visionweb_fields .= ", `wrap_angle`='".$_POST['wrap_angle_'.$i]."'";
						$visionweb_fields .= ", `panto_angle`='".$_POST['panto_angle_'.$i]."'";
						$visionweb_fields .= ", `rv_distance`='".$_POST['rv_distance_'.$i]."'";
						$visionweb_fields .= ", `lv_distance`='".$_POST['lv_distance_'.$i]."'";
						$visionweb_fields .= ", `re_rotation`='".$_POST['re_rotation_'.$i]."'";
						$visionweb_fields .= ", `le_rotation`='".$_POST['le_rotation_'.$i]."'";
						$visionweb_fields .= ", `reading_distance`='".$_POST['reading_distance_'.$i]."'";
						$visionweb_fields .= ", `lab_detail_id`='".$_POST['lab_detail_id_'.$i]."'";
						$visionweb_fields .= ", `lab_ship_detail_id`='".$_POST['lab_ship_detail_id_'.$i]."'";
						
						
						$lens_data .= ", `seg_type_od`='".$_POST['seg_type_id_'.$i.'_od']."'";
						$lens_data .= ", `seg_type_os`='".$_POST['seg_type_id_'.$i.'_os']."'";
						$lens_data .= ", `material_id_od`='".$_POST['material_id_'.$i.'_od']."'";
						$lens_data .= ", `material_id_os`='".$_POST['material_id_'.$i.'_os']."'";
						$lens_data .= ", `design_id_od`='".$_POST['design_id_'.$i.'_od']."'";
						$lens_data .= ", `design_id_os`='".$_POST['design_id_'.$i.'_os']."'";
						$lens_data .= ", `a_r_id_od`='".implode(';', $_POST['a_r_id_'.$i.'_od'])."'";
						$lens_data .= ", `a_r_id_os`='".implode(';', $_POST['a_r_id_'.$i.'_os'])."'";
						
						/*Lens Vision*/
						$lens_data .= ", `lens_vision`='".$_POST['lens_vision_'.$i]."'";
					}
					
					/*Safety Glasses Check*/
					$safety_glasses = "";
					if($_POST['module_type_id_'.$i]==1){
						if(isset($_POST['safety_glass_'.$i])){
							$safety_glasses=", `safety_glass`='1'";
						}
						else{
							$safety_glasses=", `safety_glass`='0'";
						}
					}
					
					$tax_query = "";
					if($_POST['module_type_id_'.$i]!=2){
						$tax_query = ", `tax_rate`='".$_POST['tax_p_'.$i]."', `tax_paid`='".$_POST['tax_v_'.$i]."' ";
						if(isset($_POST['tax_applied_'.$i])){
							$tax_query .=", tax_applied='1' ";
						}
						else{
							$tax_query .=", tax_applied='0' ";
						}
					}
					
					$glasses_fields = "";
					if($_POST['module_type_id_'.$i]==2){
						/*using Fields labelled for Contact (Contact Lens) beacuese same kind of value in frames and lenses*/
						$glasses_fields .= ", `contact_usage`='".$_POST['glasses_usage_'.$i]."'";
						$glasses_fields .= ", `contact_type`='".$_POST['glasses_type_'.$i]."'";
					}
					//echo "<pre>";
					//print_r($_POST); die;
					if($_POST['module_type_id_'.$i]==2){
						$price_qry1="";
					}
						$sel_detail_qry=$sel_detail_qry." set $order_index order_id='$order_id',patient_id='$patient_id'
					 $item_id_qry $item_name_qry $item_name_other_qry $upc_name_qry $module_type_id_qry $price_qry $price_qry1 $allowed_qry $discount_qry $item_overall_discount_qry $qty_qry $qty_right_qry $total_amount_qry $discount_code_qry
					$item_lens_grand_price_qry $item_lens_grand_disc_qry $item_lens_grand_total_qry $manufacturer_id_qry $brand_id_qry $pof_chk_qry $shape_id_qry $style_id_qry $style_other_qry   $cl_for_od_qry $cl_for_os_qry $color_id_qry $color_id_os_qry $manufacturer_id_os_qry $brand_id_os_qry $style_id_os_qry $a_qry $b_qry $ed_qry $dbl_qry $temple_qry $bridge_qry $job_type_qry $fpd_qry $contact_cat_id_qry $cont_package_qry $cont_wear_sch_qry $cont_replac_qry
					 $supply_id_qry $type_id_qry $progressive_id_qry $material_id_qry $transition_id_qry $a_r_id_qry $tint_id_qry $polarized_id_qry $edge_id_qry $trial_id_qry
					 $lens_other_qry $item_lens_selections_qry $uv400_qry $pgx_qry $tap_qry $use_on_hand_chk_qry $cont_use_on_hand_chk_qry $order_chk_qry $item_comment_qry
					 $pt_paid_qry $pt_resp_qry $ins_amount_qry $ins_id_qry $case_ins_id_qry $lens_frame_id_qry $item_prac_code_qry $dx_code_qry $overall_discount_qry
					 $add_new_rec_date $show_default_qry $lab_id_qry $pt_wear_pic_qry $contact_lens_fields $visionweb_fields $lens_data $glasses_fields $tax_query $safety_glasses $whr_qry ";
					// echo $sel_detail_qry;
					// exit();
					//print_r($sel_detail_qry);die;
					//echo $sel_detail_qry;die;
					imw_query($sel_detail_qry);
					$getoid = imw_insert_id();
					
					if($_FILES['trace_file_'.$i]['name']!="" && $_POST['module_type_id_'.$i]==1)
					{
						$trace_type = pathinfo ($_FILES['trace_file_'.$i]['name'],PATHINFO_EXTENSION);
						if($_POST['order_detail_id_'.$i]>0){
							$target="trace_file_".$_POST['order_detail_id_'.$i].'.'.$trace_type;
							$trace_ord_id=$_POST['order_detail_id_'.$i];
						}else{
							$target="trace_file_".$getoid.'.'.$trace_type;
							$trace_ord_id=$getoid;
						}
						$path="uploaddir/trace_file/";
						move_uploaded_file($_FILES['trace_file_'.$i]['tmp_name'],$path.$target);
						imw_query("update in_order_details set trace_file='$target' where id='$trace_ord_id'");
					}
					
					/*Update remake details*/
					if(isset($_POST['remake_id']) && $_POST['remake_id']>0){
						$remake_prac_code = $_POST['remake_prac_code'];
						if($remake_prac_code>0){
							$remake_procedure_id = back_prac_id($remake_prac_code);
						}
						$_POST['remake_tax_p'] = "0";
						$_POST['remake_tax_v'] = "0.00";
						$_POST['remake_tax_applied'] = "0";
						
						$remake_qry = "UPDATE `in_order_remake_details` SET
										`prac_code_id`='".$remake_procedure_id."',
										`price`='".$_POST['remake_price']."',
										`qty`='".$_POST['remake_qty']."',
										`allowed`='".$_POST['remake_allowed']."',
										`discount`='".$_POST['remake_discount']."',
										`overall_discount`='".$_POST['remake_overall_discount']."',
										`total_amount`='".$_POST['remake_total']."',
										`ins_amount`='".$_POST['remake_ins_amount']."',
										`pt_paid`='".$_POST['remake_pt_paid']."',
										`pt_resp`='".$_POST['remake_pt_resp']."',
										`discount_code`='".$_POST['remake_discount_code']."',
										`ins_case_id`='".$_POST['remake_ins_case']."',
										`tax_rate`='".$_POST['remake_tax_p']."',
										`tax_paid`='".$_POST['remake_tax_v']."',
										`tax_applied`='".$_POST['remake_tax_applied']."',
										`modified_by`='".$operator_id."',
										`modified_date`='".$entered_date."',
										`modified_time`='".$entered_time."'
										WHERE
										`id`='".$_POST['remake_id']."' AND
										`order_id`='".$order_id."'";
						imw_query($remake_qry);
					}
					/*End update remake details*/
					
					if($order_detail_id_qry=="" && $_POST['module_type_id_'.$i]==1){
						$_POST['lens_frame_id_'.$i.'_lensD'] = $getoid;
					}
					
					if($_POST['order_detail_id_'.$i]>0)
					{
						$order_detail_id=$_POST['order_detail_id_'.$i];
					}
					else
					{
					
						$order_detail_id=$getoid;
					}
					
					if($_POST['module_type_id_'.$i]==3){
						pos_cl_details($i, $order_detail_id, $order_id);
					}
					
					if($_POST['module_type_id_'.$i]=="1" && $_POST['in_add_'.$i]=="1")
					{
					
						pof_checking($order_detail_id,$post_val,$i);
					}
					
					if($_POST['module_type_id_'.$i]=="2" && $_POST['otherTempPage']=='other_selPage' && $_POST['for_other_lens_'.$i]=="2"){
						$item_sel_price = imw_query("select lens_retail,material_retail,a_r_retail,transition_retail,polarization_retail,tint_retail,uv400_retail,other_retail,progressive_retail,edge_retail,color_retail,pgx_retail from in_item_price_details where module_type_id='2' and item_id='".$_POST['item_id_'.$i]."'");
						$get_item_price_arr = imw_fetch_array($item_sel_price);
						
						$item_sel_prac_code = imw_query("select type_prac_code, material_prac_code, ar_prac_code, transition_prac_code, polarized_prac_code, tint_prac_code, uv_prac_code, other_prac_code, progressive_prac_code, edge_prac_code, color_prac_code, pgx_prac_code from in_item_price_details where module_type_id='2' and item_id='".$_POST['item_id_'.$i]."'");
						$get_item_prac_arr = imw_fetch_array($item_sel_prac_code);
						//print_r($get_item_price_arr);
						$names_items = array("lens","material","a_r","transition","polarization","tint","uv400","other","progressive","edge","color","pgx");
						for($l=0;$l<count($names_items);$l++)
						{
							$it_id = $l+1;
							$sel_price_qry="insert into in_order_lens_price_detail set itemized_id='$it_id', itemized_name='".$names_items[$l]."', item_id='".$_POST['item_id_'.$i]."', order_id='$order_id', order_detail_id='$order_detail_id', patient_id='$patient_id', module_type_id='2', wholesale_price='".$get_item_price_arr[$l]."',allowed='".$get_item_price_arr[$l]."', discount='".$_POST['discount_'.$i]."', discount_code='".$_POST['discount_code_'.$i]."', qty='".$_POST['qty_'.$i]."', total_amt='".$get_item_price_arr[$l]."', item_prac_code='".$get_item_prac_arr[$l]."', entered_date='$entered_date', entered_time='$entered_time', entered_by='$operator_id'";
							imw_query($sel_price_qry);
							//exit();
						}
					}
					elseif($_POST['module_type_id_'.$i]=="2" && $_POST['otherTempPage']=='other_selPage')
					{
						$sel_price_qry=imw_query("update in_order_lens_price_detail set qty='".$_POST['qty_'.$i]."', modified_date='$entered_date', modified_time='$entered_time', modified_by='$operator_id' where order_detail_id='$order_detail_id'");
					}
							
					if($_POST['module_type_id_'.$i]=="2" && $order_detail_id>0){
						if($_POST['module_type_id_'.$i]!="1"){ /*Fix for combined Frames and Lenses Save*/
							update_order_lens_price($_POST['price_order_id'],$order_detail_id,$order_id,$patient_id,$post_val,$i);
							update_lens_pres($_POST['order_rx_lens_id_'.$i],$order_detail_id,$order_id,$patient_id,$post_val,$i);
						}
						$sel_lens_frame = imw_query("select id,dx_code from in_order_details where lens_frame_id='$dx_frame_order_id' and id!='$order_detail_id' and order_id='$order_id' and patient_id='$patient_id'");
						if(imw_num_rows($sel_lens_frame)>0)
						{
							$fetch_frame_lens=imw_fetch_array($sel_lens_frame);
							$up_dx_frame_qry=imw_query("update in_order_details set dx_code='".$fetch_frame_lens['dx_code']."', modified_date='$entered_date', modified_time='$entered_time', modified_by='$operator_id' where id='$dx_frame_order_id' and order_id='$order_id' and patient_id='$patient_id'");
						}
						else
						{
							$up_dx_frame_qry=imw_query("update in_order_details set dx_code='0', modified_date='$entered_date', modified_time='$entered_time', modified_by='$operator_id' where id='$dx_frame_order_id' and order_id='$order_id' and patient_id='$patient_id'");
						}
						/*$up_frame_qry="update in_order_details set price='$frame_price',discount='$frame_disc',qty='$frame_qty',total_amount='$frame_total', modified_date='$entered_date', modified_time='$entered_time', modified_by='$operator_id' $dx_code_qry where id='$frame_order_id' and order_id='$order_id' and patient_id='$patient_id'";
						imw_query($up_frame_qry);*/
					}
					if($_POST['module_type_id_'.$i]=="3" && $order_detail_id>0){
						update_cl_pres($_POST['order_rx_cl_id'],$order_detail_id,$order_id,$patient_id,$post_val,$i);
					}
					if($_POST['page_name']!="pos")
					{
						$totqty=$_POST['qty_'.$i]+$_POST['qty_right_'.$i];
						update_order_faciliy($order_id,$order_detail_id,$patient_id,$_POST['item_id_'.$i],$totqty);
					}
				}
			}
			$mn = $i;
			update_tax($_POST['module_type_id_'.$i],$order_id); 
			if(isset($_POST['use_on_hand_chk_'.$i]) && $_POST['reduce_qty_'.$i]==="true" && ($_POST['module_type_id_'.$i]==1 || $_POST['module_type_id_'.$i]==2 || $_POST['module_type_id_'.$i]==6 || $_POST['module_type_id_'.$i]==5 || $_POST['module_type_id_'.$i]==7)){ 			
				$qty_resp = imw_query("SELECT `qty_reduced` FROM `in_order_details` WHERE `id`='$order_detail_id' AND `order_id`='$order_id' AND `qty_reduced`=1");				
				if(imw_num_rows($qty_resp)==0){
					deduct_item_qty($order_id, $order_detail_id, $patient_id, $_POST['item_id_'.$i], $_POST['qty_'.$i]);
					imw_query("UPDATE `in_order_details` SET `qty_reduced`=1 WHERE `id`='$order_detail_id' AND `order_id`='$order_id'");
				}
			}
		}
		
		/*Add/Update Custom Charge Data*/
		list($item_pay_qry, $item_disc_qry, $tax_qry, $grand_total_qry) = update_order_custom_charges($order_id, $_POST['cs'], $order_facility_id);
		/*End Add/Update Custom Charge Data*/
		
		if($_POST['page_name']!="pos")
		{
			$sqlOrderTotal = "update in_order set main_default_ins_case='".$_POST['main_ins_case_id_1']."', main_default_discount_code='".$_POST['main_discount_code_1']."', comment='".imw_real_escape_string($_POST['charge_comment_1'])."', modified_date='$entered_date', modified_time='$entered_time', modified_by='$operator_id' $item_pay_qry $item_disc_qry $tax_qry $grand_total_qry where id='$order_id'";
			imw_query($sqlOrderTotal);
		}
	}
	if($action=="order_post" || $action=="dispensed_post"){
		post_charges($order_id);
	}
	//exit();
	update_qty_price_order($order_id);
	
	if($_POST['page_name']=="pos")
	{
		if($action=="order_post")
		{
			if($_POST['other_page_name']!='other_selection'){
				change_order_status($order_id, "ordered");
			}	
				update_in_order_status($order_id);
			
		}
		if($action=="dispensed_post")
		{
			change_order_status($order_id, "dispensed", '', $_POST['reduc_stock']);
			update_in_order_status($order_id);
		}
	}
	
	if($dataInsertFlag){
		change_order_status($order_id, "pending");
		update_in_order_status($order_id);
	}
}

function numberFormat($value,$format,$show_zero=''){
	$value = number_format($value, $format);
	if($value > 0){
		$value = '$'.$value;
	}
	else if($value < 0){
		$value = str_replace('-', '-$', $value);
	}
	else{
		if(empty($show_zero) === true){
			$value = NULL;
		}
		else{
			$value = preg_replace("/,/","",$value);
		}
	}
	return $value;
}

function pof_checking($order_detail_id,$post_val,$count)
{	
	extract($post_val);
	
	$fields = " set order_detail_id = '".$order_detail_id."' , manufacturer = '".$_POST['pof_manufacturer_id_'.$count]."' , brand = '".$_POST['pof_brand_id_'.$count]."' , style = '".$_POST['pof_style_id_'.$count]."' , shape = '".$_POST['pof_shape_id_'.$count]."' , color = '".$_POST['pof_color_id_'.$count]."' , a = '".$_POST['a_'.$count]."' , b = '".$_POST['b_'.$count]."' , ed = '".$_POST['ed_'.$count]."' , dbl = '".$_POST['dbl_'.$count]."' , temple = '".$_POST['temple_'.$count]."' , bridge = '".$_POST['bridge_'.$count]."' , fpd = '".$_POST['fpd_'.$count]."' ";
	
	$checkord = imw_query("select id from in_frame_pof where order_detail_id = '".$order_detail_id."' ");
	if(imw_num_rows($checkord)>0)
	{
		$checkordrow = imw_fetch_assoc($checkord);
		$pofID = $checkordrow['id'];		
		$query = " update ";
		$where = " where id = '".$pofID."'";		
	}
	else
	{
		$query = " insert into ";
			
	}
	
	$qry = "$query in_frame_pof $fields $where ";

	imw_query($qry);
}

function update_order_lens_price($price_order_id,$order_detail_id,$order_id,$patient_id,$post_val,$loopid)
{
	extract($post_val);
	$opr_id = $_SESSION['authId'];
	$date = date("Y-m-d");
	$time = date("h:i:s");
	$price_tot = 0;
	$allowed_tot = 0;
	$pt_resp_tot = 0;
	$ins_tot = 0;
	$dis_tot = 0;
	$total_amt_tot = 0;
	$pt_paid_tot = 0;
	$final_discount=array();
	for($q=1;$q<=$_POST['lens_item_count_'.$loopid];$q++){
		
		/*If Record marked as deleted from front end before saving it*/
		if( isset($_POST['del_status_'.$loopid.'_'.$q]) && $_POST['del_status_'.$loopid.'_'.$q] == '1' && (!isset($_POST['lens_price_detail_id_'.$loopid.'_'.$q]) || $_POST['lens_price_detail_id_'.$loopid.'_'.$q] == '') ){
			continue;
		}
		/*End if Record marked as deleted from front end before saving it*/
		
		/*Prevent Blank Entries*/
		if(!isset($_POST['lens_item_detail_id_'.$loopid.'_'.$q])){
			continue;
		}
		
		$wholesale_price="";
		$allowed="";
		$discount="";
		$overall_discount = "";
		$total_amt="";
		$itemized_id="";
		$itemized_name="";
		$item_description="";
		$ins_amt="";
		$pt_paid="";
		$pt_resp="";
		$prac_code="";
		$procedureId="";
		$ins_id="";
		$case_ins_id="";
		$price_order_id="";
		$item_prac_code="";
		$discount_code="";
		
		$wholesale_price = $_POST['lens_item_price_'.$loopid.'_'.$q];
		$discount = $_POST['lens_item_discount_'.$loopid.'_'.$q];
		$overall_discount = $_POST['lens_item_overall_discount_'.$loopid.'_'.$q];
		$discount_code = $_POST['discount_code_'.$loopid.'_'.$q];
		$qty = $_POST['lens_qty_'.$loopid.'_'.$q];
		$total_amt = $_POST['lens_item_total_'.$loopid.'_'.$q];
		$itemized_id = $_POST['lens_item_detail_id_'.$loopid.'_'.$q];
		$itemized_name = $_POST['lens_item_detail_name_'.$loopid.'_'.$q];
		$item_description = $_POST['pos_lens_item_name_disp_'.$loopid.'_'.$q];
		
		$item_ids = $_POST['item_id_'.$loopid];
		$pt_paid = $_POST['pt_paid_'.$loopid.'_'.$q];
		if(isset($_POST['lens_item_allowed_'.$loopid.'_'.$q])){
			if($_POST['lens_item_allowed_'.$loopid.'_'.$q]<=0){
				$allowed = $_POST['lens_item_price_'.$loopid.'_'.$q];
			}else{
				$allowed = $_POST['lens_item_allowed_'.$loopid.'_'.$q];
			}
		}
		
		$dis_tot += $total_amt_price - $wholesale_price_1;
		if(isset($_POST['ins_amount_'.$loopid.'_'.$q])){
			$ins_amt = ", ins_amount='".$_POST['ins_amount_'.$loopid.'_'.$q]."'";
		}
		if(isset($_POST['pt_resp_'.$loopid.'_'.$q])){
			$pt_resp = ", pt_resp='".$_POST['pt_resp_'.$loopid.'_'.$q]."'";
		}
		if(isset($_POST['item_prac_code_'.$loopid.'_'.$q])){
			$item_prac_code = $_POST['item_prac_code_'.$loopid.'_'.$q];
			
			if($_POST['page_name']=="pos" || $_POST['page_name']=="lens_selection")
			{
				$procedureId = back_prac_id($item_prac_code, false, 2);
			}
			else
			{
				$procedureId = $item_prac_code;
			}
			$prac_code = ", item_prac_code='".$procedureId."'";
			
			$sql = "SELECT `prac_code` FROM `in_prac_codes` WHERE `module_id`='2'";
			$flag = false;
			if($itemized_name=="material"){
				$sql .= " AND `sub_module`='material'";
				$flag = true;
			}
			elseif($itemized_name=="lens"){
				$sql .= " AND `sub_module`='type'";
				$flag = true;
			}
			elseif($itemized_name=="a_r"){
				$sql .= " AND `sub_module`='coating'";
				$flag = true;
			}
			elseif($itemized_name=="transition"){
				$sql .= " AND `sub_module`='transition'";
				$flag = true;
			}
			elseif($itemized_name=="polarization"){
				$sql .= " AND `sub_module`='polarized'";
				$flag = true;
			}
			elseif($itemized_name=="tint"){
				$sql .= " AND `sub_module`='tint'";
				$flag = true;
			}
			elseif($itemized_name=="progressive"){
				$sql .= " AND `sub_module`='progressive'";
				$flag = true;
			}
			elseif($itemized_name=="edge"){
				$sql .= " AND `sub_module`='edge'";
				$flag = true;
			}
			elseif($itemized_name=="color"){
				$sql .= " AND `sub_module`='color'";
				$flag = true;
			}
			
			if($flag){
				$resp = imw_query($sql);
				if($resp && imw_num_rows($resp)>0){
					$resp = imw_fetch_assoc($resp);
					$prac_code .= ", item_prac_code_default='".$resp['prac_code']."'";
				}
			}
		}
		if(isset($_POST['ins_id_'.$loopid.'_'.$q])){
			$ins_id = ", ins_id='".$_POST['ins_id_'.$loopid.'_'.$q]."'";
		}
		if(isset($_POST['ins_case_id_'.$loopid.'_'.$q])){
			$case_ins_id = ", ins_case_id='".$_POST['ins_case_id_'.$loopid.'_'.$q]."'";
		}
		$price_order_id = $_POST['lens_price_detail_id_'.$loopid.'_'.$q];
		if($price_order_id>0)
		{
			$query = "update";
			$whr = ", modified_date='$date', modified_time='$time', modified_by='$opr_id' where id='$price_order_id' and order_detail_id='$order_detail_id' and order_id='$order_id' and patient_id='$patient_id'";
		}
		else
		{
			$query = "insert into ";
			$whr=", entered_date='$date', entered_time='$time', entered_by='$opr_id'";
		}
		$del_status = "";
		if(isset($_POST['del_status_'.$loopid.'_'.$q]) && $_POST['del_status_'.$loopid.'_'.$q]=="1"){
			$del_status = ", del_status='1', del_operator_id='".$opr_id."', del_date='".$date."', del_time='".$time."'";
		}
		else{
			$pt_paid_tot += $pt_paid;
			$price_tot += $wholesale_price;
			$allowed_tot += $allowed;
			$ins_tot += $_POST['ins_amount_'.$loopid.'_'.$q];
			$total_amt_tot += $total_amt;
			$pt_resp_tot += $_POST['pt_resp_'.$loopid.'_'.$q];
			
			$exp_discount="";
			$exp_discount=explode('%',$discount);
			if(count($exp_discount)>1){
				$final_discount[]=($allowed*$exp_discount[0])/100;
			}else{
				$final_discount[]=$exp_discount[0];
			}
		}
		
		if(isset($_POST['pt_paid_'.$loopid.'_'.$q])){
			$pt_paid = ", pt_paid='".$_POST['pt_paid_'.$loopid.'_'.$q]."'";
		}
		
		$tax_query = ", `tax_rate`='".$_POST['tax_p_'.$loopid.'_'.$q]."', `tax_paid`='".$_POST['tax_v_'.$loopid.'_'.$q]."' ";
		if(isset($_POST['tax_applied_'.$loopid.'_'.$q])){
			$tax_query .=", tax_applied='1' ";
		}
		else{
			$tax_query .=", tax_applied='0' ";
		}
		
		if(isset($_POST['pos_lens_item_vision_'.$loopid.'_'.$q]) && $_POST['pos_lens_item_vision_'.$loopid.'_'.$q] != ''){
			$vision = ", vision='".$_POST['pos_lens_item_vision_'.$loopid.'_'.$q]."'";
		}
		else{
			$vision = ", vision=''";
		}
		
		
		$sel_price_qry="$query in_order_lens_price_detail set itemized_id='$itemized_id', itemized_name='$itemized_name', item_description='$item_description', item_id='$item_ids', order_id='$order_id', order_detail_id='$order_detail_id', patient_id='$patient_id', module_type_id='2', wholesale_price='$wholesale_price',allowed='$allowed', discount='$discount', overall_discount='$overall_discount', qty='$qty',  total_amt='$total_amt', comment='', discount_code='$discount_code' $ins_amt $pt_paid $pt_resp $prac_code $ins_id $case_ins_id $del_status $tax_query $vision $whr";
		//echo $sel_price_qry; //die();
		
		imw_query($sel_price_qry);
	}
	if(isset($_POST['lens_item_count_'.$loopid])){
		//$dis_tot=str_replace('-','',$dis_tot);
		$dis_tot=str_replace(',','',number_format(array_sum($final_discount),2));
		$up_ord_det_tb = imw_query("update in_order_details set price='$price_tot', discount_val='$dis_tot', total_amount='$total_amt_tot',allowed='$allowed_tot',pt_paid='$pt_paid_tot',ins_amount='$ins_tot',pt_resp='$pt_resp_tot' where id='$order_detail_id'");
	}
}

function update_lens_pres($order_rx_lens_id,$order_detail_id,$order_id,$patient_id,$post_val,$y=1)
{
	extract($post_val);
	$opr_id = $_SESSION['authId'];
	$date = date("Y-m-d");
	$time = date("h:i:s");
	/*for($e=0;$e<count($_POST['lens_prescription_count']);$e++)
	{*/
	$qryWhere='';
	$lens_telephone=preg_replace('/[^0-9]/','',$lens_telephone);
	if($_POST['page_name']=="pos")
	{
		if($_POST['order_rx_lens_id_'.$y]>0)
		{
			$qryWhere=", modified_date='$date', modified_time='$time', modified_by='$opr_id' WHERE id='".$_POST['order_rx_lens_id_'.$y]."' 
		and order_id='".$order_id."' and patient_id='".$patient_id."'";				
			
			if($_POST['lens_pu_loc_'.$y]=="1")
			{
				$lens_ship_home_qry = "ship_home_chk='".trim(imw_real_escape_string($_POST['lens_pu_loc_'.$y]))."',location_id=0";
			}
			else
			{
				$lens_ship_home_qry = "location_id='".trim(imw_real_escape_string($_POST['lens_location_id_'.$y]))."',ship_home_chk=0";
			}
				imw_query("update in_optical_order_form SET $lens_ship_home_qry $qryWhere");
		}
	}
			$qryPrefix="Insert INTO ";
			$qryWhere=", entered_date='$date', entered_time='$time', entered_by='$opr_id'";
			if($_POST['order_rx_lens_id_'.$y]>0)
			{ 
				$qryPrefix="Update";
				$qryWhere=", modified_date='$date', modified_time='$time', modified_by='$opr_id' WHERE id='".$_POST['order_rx_lens_id_'.$y]."' 
				and det_order_id='".$order_detail_id."' and order_id='".$order_id."' and patient_id='".$patient_id."'";
			}
			else
			{
				$ship_chk=", location_id='".$_SESSION['pro_fac_id']."', ship_home_chk=0";
			}
			
			if($_POST['page_name']=="lens_selection")
			{
				$lens_outside_rx=$lens_outside_rx;
				$lens_neutralize_rx=$lens_neutralize_rx;
			}
			
			if(trim($_POST['lens_sphere_od_'.$y])!="" || trim($_POST['lens_sphere_os_'.$y])!="")
			{
				if(isset($_POST['lens_physician_name_'.$y]))
				{
					$phyname = "physician_name='".addslashes($_POST['lens_physician_name_'.$y])."', ";
				}
				if(isset($_POST['lens_telephone']))
				{
					$telphone = " ,telephone='".$_POST['lens_telephone']."' ";
				}
				if(isset($lens_last_exam_1))
				{
					$last_exam = " ,last_exam='".saveDateFormat($lens_last_exam_1)."' ";				
				}				
				if(isset($_POST['lens_base_od_'.$y]))
				{
					$lens_base_od = " ,base_od='".trim(imw_real_escape_string($_POST['lens_base_od_'.$y]))."' ";
				}
				if(isset($_POST['lens_base_os_'.$y]))
				{
					$lens_base_os = " ,base_os='".trim(imw_real_escape_string($_POST['lens_base_os_'.$y]))."' ";
				}
				if(isset($_POST['lens_seg_od_'.$y]))
				{
					$lens_seg_od = " ,seg_od='".trim(imw_real_escape_string($_POST['lens_seg_od_'.$y]))."' ";
				}
				if(isset($_POST['lens_seg_os_'.$y]))
				{
					$lens_seg_os = " ,seg_os='".trim(imw_real_escape_string($_POST['lens_seg_os_'.$y]))."' ";
				}
				if(isset($_POST['lens_sphere_od_'.$y]))
				{
			$lens_sphere_od = " ,sphere_od='".trim(imw_real_escape_string($_POST['lens_sphere_od_'.$y]))."' ";
				}
				if(isset($_POST['lens_cylinder_od_'.$y]))
				{
			$lens_cylinder_od = " ,cyl_od='".trim(imw_real_escape_string($_POST['lens_cylinder_od_'.$y]))."' ";
				}
				if(isset($_POST['lens_axis_od_'.$y]))
				{
					$lens_axis_od = " ,axis_od='".trim(imw_real_escape_string($_POST['lens_axis_od_'.$y]))."' ";
				}
				if(isset($_POST['lens_axis_od_va_'.$y]))
				{
					$lens_axis_od_va = " ,axis_od_va='".trim(imw_real_escape_string($_POST['lens_axis_od_va_'.$y]))."' ";
				}
				if(isset($_POST['lens_add_od_'.$y]))
				{
					$lens_add_od = " ,add_od='".trim(imw_real_escape_string($_POST['lens_add_od_'.$y]))."' ";
				}
				if(isset($_POST['lens_add_od_va_'.$y]))
				{
					$lens_add_od_va = " ,add_od_va='".trim(imw_real_escape_string($_POST['lens_add_od_va_'.$y]))."' ";
				}
				if(isset($_POST['lens_dpd_od_'.$y]))
				{
					$lens_dpd_od = " ,dist_pd_od='".trim(imw_real_escape_string($_POST['lens_dpd_od_'.$y]))."' ";
				}
				if(isset($_POST['lens_npd_od_'.$y]))
				{
					$lens_npd_od = " ,near_pd_od='".trim(imw_real_escape_string($_POST['lens_npd_od_'.$y]))."' ";
				}
				if(isset($_POST['lens_oc_od_'.$y]))
				{
					$lens_oc_od = " ,oc_od='".trim(imw_real_escape_string($_POST['lens_oc_od_'.$y]))."' ";
				}
				if(isset($_POST['lens_mr_od_p_'.$y]))
				{
					$lens_mr_od_p = " ,mr_od_p='".trim(imw_real_escape_string($_POST['lens_mr_od_p_'.$y]))."' ";
				}
				if(isset($_POST['lens_mr_od_prism_'.$y]))
				{
					$lens_mr_od_prism = " ,mr_od_prism='".trim(imw_real_escape_string($_POST['lens_mr_od_prism_'.$y]))."' ";
				}
				if(isset($_POST['lens_mr_od_splash_'.$y]))
				{
					$lens_mr_od_splash = " ,mr_od_splash='".trim(imw_real_escape_string($_POST['lens_mr_od_splash_'.$y]))."' 	";
				}
				if(isset($_POST['lens_mr_od_sel_'.$y]))
				{
					$lens_mr_od_sel = " ,mr_od_sel='".trim(imw_real_escape_string($_POST['lens_mr_od_sel_'.$y]))."' ";
				}
				if(isset($_POST['lens_sphere_os_'.$y]))
				{
					$lens_sphere_os = " ,sphere_os='".trim(imw_real_escape_string($_POST['lens_sphere_os_'.$y]))."' ";
				}
				if(isset($_POST['lens_cylinder_os_'.$y]))
				{
					$lens_cylinder_os = " ,cyl_os='".trim(imw_real_escape_string($_POST['lens_cylinder_os_'.$y]))."' ";
				}				
				if(isset($_POST['lens_axis_os_'.$y]))
				{
					$lens_axis_os = " ,axis_os='".trim(imw_real_escape_string($_POST['lens_axis_os_'.$y]))."' ";
				}
				if(isset($_POST['lens_axis_os_va_'.$y]))
				{
					$lens_axis_os_va = " ,axis_os_va='".trim(imw_real_escape_string($_POST['lens_axis_os_va_'.$y]))."' ";
				}
				if(isset($_POST['lens_add_os_'.$y]))
				{
					$lens_add_os = " ,add_os='".trim(imw_real_escape_string($_POST['lens_add_os_'.$y]))."' ";
				}
				if(isset($_POST['lens_add_os_va_'.$y]))
				{
					$lens_add_os_va = " ,add_os_va='".trim(imw_real_escape_string($_POST['lens_add_os_va_'.$y]))."' ";
				}
				if(isset($_POST['lens_prism_os_'.$y]))
				{
					$lens_prism_os = " ,prism_os='".trim(imw_real_escape_string($_POST['lens_prism_os_'.$y]))."' ";
				}
				if(isset($_POST['lens_dpd_os_'.$y]))
				{
					$lens_dpd_os = " ,dist_pd_os='".trim(imw_real_escape_string($_POST['lens_dpd_os_'.$y]))."' ";
				}
				if(isset($_POST['lens_npd_os_'.$y]))
				{
					$lens_npd_os = " ,near_pd_os='".trim(imw_real_escape_string($_POST['lens_npd_os_'.$y]))."' ";
				}
				if(isset($_POST['lens_oc_os_'.$y]))
				{
					$lens_oc_os = " ,oc_os='".trim(imw_real_escape_string($_POST['lens_oc_os_'.$y]))."' ";
				}
				if(isset($_POST['lens_mr_os_p_'.$y]))
				{
					$lens_mr_os_p = " ,mr_os_p='".trim(imw_real_escape_string($_POST['lens_mr_os_p_'.$y]))."'				 ";
				}
				if(isset($_POST['lens_mr_os_prism_'.$y]))
				{
					$lens_mr_os_prism = " ,mr_os_prism='".trim(imw_real_escape_string($_POST['lens_mr_os_prism_'.$y]))."' ";
				}
				if(isset($_POST['lens_mr_os_splash_'.$y]))
				{
					$lens_mr_os_splash = " ,mr_os_splash='".trim(imw_real_escape_string($_POST['lens_mr_os_splash_'.$y]))."' ";
				}
				if(isset($_POST['lens_mr_os_sel_'.$y]))
				{
					$lens_mr_os_sel = " ,mr_os_sel='".trim(imw_real_escape_string($_POST['lens_mr_os_sel_'.$y]))."' ";
				}
				
				if(isset($_POST['lens_rx_dos_'.$y])){
					$lens_rx_dos = " ,rx_dos='".trim(imw_real_escape_string($_POST['lens_rx_dos_'.$y]))."' ";
				}
				
				if($_POST['page_name']!="pos"){
				if($_POST['lens_outside_rx']=="1")
				{
					$lens_outside_rx_check = " ,outside_rx='".trim(imw_real_escape_string($_POST['lens_outside_rx']))."' ";
				}	
				else
				{
					$lens_outside_rx_check = " ,outside_rx='0' ";					
				}
				if($_POST['lens_neutralize_rx']=="1")
				{
					$lens_neutralize_rx_check = " ,neutralize_rx='".trim(imw_real_escape_string($_POST['lens_neutralize_rx']))."' ";
				}		
				else
				{
					$lens_neutralize_rx_check = " ,neutralize_rx='0' ";
				}
				
				}
				
				$qry=$qryPrefix." in_optical_order_form SET 
				patient_id='".$patient_id."',
				physician_id='".$_POST['lens_physician_id_'.$y]."',
				$phyname
				order_id='".$order_id."',
				det_order_id='".$order_detail_id."',
				operator_id='".$_SESSION['authId']."'
				$lens_sphere_od	
				$lens_cylinder_od	
				$lens_axis_od
				$lens_axis_od_va
				$lens_add_od
				$lens_add_od_va
				$lens_base_od
				$lens_dpd_od
				$lens_npd_od
				$lens_oc_od
				$lens_seg_od
				$lens_mr_od_p
				$lens_mr_od_prism
				$lens_mr_od_splash
				$lens_mr_od_sel
				$lens_sphere_os
				$lens_cylinder_os
				$lens_axis_os
				$lens_axis_os_va
				$lens_add_os
				$lens_add_os_va
				$lens_prism_os
				$lens_base_os
				$lens_dpd_os
				$lens_npd_os
				$lens_oc_os
				$lens_seg_os
				$lens_mr_os_p
				$lens_mr_os_prism
				$lens_mr_os_splash
				$lens_mr_os_sel
				$lens_outside_rx_check
				$telphone
				$last_exam
				$ship_chk
				$lens_rx_dos
				$lens_neutralize_rx_check
				".$qryWhere;
				$rs=imw_query($qry) or die(imw_error());
			}
		/*$y++; 
	}*/
}

function update_cl_pres($order_rx_cl_id,$order_detail_id,$order_id,$patient_id,$post_val,$i)
{
	
	$post_val['order_id'] = $order_id;
	//print_r($post_val);
	//die();
	extract($post_val);
	$opr_id = $_SESSION['authId'];
	$date = date("Y-m-d");
	$time = date("h:i:s");
	$d=$i;
	
	//for($g=0;$g<count($_POST['cl_prescription_count']);$g++)
	//{		
	
	if($_POST['page_name']=="pos")
	{
		if($_POST['order_rx_cl_id_'.$d]>0)
		{
				$qryWhere=", modified_date='$date', modified_time='$time', modified_by='$opr_id' WHERE id='".$_POST['order_rx_cl_id_'.$d]."'
				and det_order_id='".$order_detail_id."' and order_id='".$order_id."' and patient_id='".$patient_id."'";
							
				if($_POST['cl_pu_loc_'.$d]=="1")
				{
					$cl_ship_home_qry = "ship_home_chk='".$_POST['cl_pu_loc_'.$d]."',location_id=0";
				}
				else
				{
					$cl_ship_home_qry = "location_id='".$_POST['cl_location_id_'.$d]."',ship_home_chk=0";
				}
			
			imw_query("update in_cl_prescriptions SET $cl_ship_home_qry $qryWhere");
				
		}
	}
	
			$qryPrefix="Insert INTO ";
			$qryWhere=", entered_date='$date', entered_time='$time', entered_by='$opr_id'";
			if($_POST['order_rx_cl_id_'.$d]!=""){
				$qryPrefix="Update";
				$qryWhere=", modified_date='$date', modified_time='$time', modified_by='$opr_id' WHERE id='".$_POST['order_rx_cl_id_'.$d]."' and det_order_id='".$order_detail_id."' and order_id='".$order_id."' and patient_id='".$patient_id."'";
			}
			else
			{
				$ship_chk1=", location_id='".$_SESSION['pro_fac_id']."', ship_home_chk=0";
			}
			/*commented because order and pos sections are now combined*/
			//if($_POST['page_name']!="pos"){
			
				if($_POST['cl_outside_rx_'.$d]=="1")
				{
					$cl_outside_rx = " ,outside_rx='1' ";
				}
				else
				{
					$cl_outside_rx = " ,outside_rx='0' ";	
				}
				
				if($_POST['cl_neutralize_rx']=="1")
				{
					$cl_neutralize_rx = " ,neutralize_rx='1'";
				}
				else
				{
					$cl_neutralize_rx = " ,neutralize_rx='0'";
				}
			//}
			if(trim($_POST['cl_sphere_od_'.$d])!="" || trim($_POST['cl_sphere_os_'.$d])!="")
			{	
				if(isset(${'cl_telephone_'.$d}))
				{			
					$cltelephone = ", telephone='".preg_replace('/[^0-9]/','',${'cl_telephone_'.$d})."'";
				}
				
				if(isset(${'cl_physician_name_'.$d}))
				{
					$cl_physician_name = " physician_name = '".${'cl_physician_name_'.$d}."', ";
				}
				
				$qry=$qryPrefix." in_cl_prescriptions SET 
				patient_id='".$patient_id."',
				order_id='".$order_id."',
				det_order_id='".$order_detail_id."',
				physician_id='".${'cl_physician_id_'.$d}."',
				$cl_physician_name
				operator_id='".$_SESSION['authId']."',
				sphere_od='".trim(imw_real_escape_string($_POST['cl_sphere_od_'.$d]))."',
				cylinder_od='".trim(imw_real_escape_string($_POST['cl_cylinder_od_'.$d]))."',
				axis_od='".trim(imw_real_escape_string($_POST['cl_axis_od_'.$d]))."',
				add_od='".trim(imw_real_escape_string($_POST['cl_add_od_'.$d]))."',
				base_od='".trim(imw_real_escape_string($_POST['cl_base_od_'.$d]))."',
				diameter_od='".trim(imw_real_escape_string($_POST['cl_diameter_od_'.$d]))."',
				sphere_os='".trim(imw_real_escape_string($_POST['cl_sphere_os_'.$d]))."',
				cylinder_os='".trim(imw_real_escape_string($_POST['cl_cylinder_os_'.$d]))."',
				axis_os='".trim(imw_real_escape_string($_POST['cl_axis_os_'.$d]))."',
				add_os='".trim(imw_real_escape_string($_POST['cl_add_os_'.$d]))."',
				base_os='".trim(imw_real_escape_string($_POST['cl_base_os_'.$d]))."',
				diameter_os='".trim(imw_real_escape_string($_POST['cl_diameter_os_'.$d]))."',
				date_added='".date('Y-m-d')."',
				rx_dos='".trim(imw_real_escape_string($_POST['rx_dos_'.$d]))."',
				rx_make_od='".trim(imw_real_escape_string($_POST['rx_make_od_'.$d]))."',
				rx_make_os='".trim(imw_real_escape_string($_POST['rx_make_os_'.$d]))."'
				$cltelephone
				$cl_outside_rx
				$ship_chk1
				$cl_neutralize_rx
				".$qryWhere;
				$rs=imw_query($qry) or die(imw_error());
			}
		//$d++;
	//}die();
}

function getHqFacility()
{
	$sql = "SELECT id FROM facility WHERE facility_type = '1' LIMIT 0,1 ";
	$row = sqlQuery($sql);
	if($row != false)
	{
		return $row["id"];
	}
	else
	{
		// Fix if No Hq. is selected
		$sql = "SELECT id FROM facility LIMIT 0,1 ";
		$row = sqlQuery($sql);
		if($row != false)
		{
			return $row["id"];
		}
	}
}

function getEncounterId()
{
	
	$facilityId = getHqFacility();
	$sql = "SELECT encounterId FROM facility WHERE id='".$facilityId."' ";
	$row = sqlQuery($sql);

	if($row != false)
	{
		$encounterId = $row["encounterId"];
	}
	
	//get from policies
	$sql = "select Encounter_ID from copay_policies WHERE policies_id = '1' ";
	$row = sqlQuery($sql);
	if($row != false){
		$encounterId_2 = $row["Encounter_ID"];		
	}
	//bigg
	if($encounterId<$encounterId_2){
		$encounterId = $encounterId_2;
	}
	
	//--
	$flgbreak=0;
	$counter=0; //check only 100 times
	do{
	
	//check in superbill
	if($flgbreak==0){
		$sql = "select count(*) as num FROM superbill WHERE encounterId='".$encounterId."' ";
		$row = sqlQuery($sql);
		if($row!=false && $row["num"]==0){
			$flgbreak=1;
		}	
	}
	
	//check in chart_master_table--
	if($flgbreak==0){
		$sql = "select count(*) as num FROM chart_master_table WHERE encounterId='".$encounterId."' ";
		$row = sqlQuery($sql);
		if($row!=false && $row["num"]==0){
			$flgbreak=1;
		}
	}
	
	$encounterId=$encounterId+1;
	$counter++;
	}while($flgbreak==0 && $counter<100);
	//--	
	
	$sql = "UPDATE copay_policies SET Encounter_ID = '".($encounterId+1)."' WHERE policies_id='1' ";
	$row = sqlQuery($sql);
	

	$sql = "UPDATE facility SET encounterId = '".($encounterId+1)."' WHERE id='".$facilityId."' ";
	$row = sqlQuery($sql);
	return $encounterId;
}


function core_phone_format($phone_number)
{
	$return = "";
	$refined_phone = preg_replace('/[^0-9]/','',$phone_number);
	$default_format = $GLOBALS['phone_format'];
	
	switch($default_format)
	{
		case "###-###-####":
		$return = preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "$1-$2-$3", $refined_phone);
		break;
		case "(###) ###-####":
		$return = preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "($1) $2-$3", $refined_phone);
		break;
		default:
		$return = preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "$1-$2-$3", $refined_phone);
		break;
	}
	return $return;
}

function remLineBrk($str){
	return str_replace(array("\r","\n"),array("\\r","\\n"),$str);
}

function get_upc_name_id($type=0)
{
	if($type>0)
	{
		$whr = " and module_type_id='$type'";
	}
	$sel_item_rec = imw_query("select id,upc_code,name from in_item where del_status='0' $whr");
	if(imw_num_rows($sel_item_rec)>0)
	{
		while($sel_item_row = imw_fetch_array($sel_item_rec))
		{
			/*if($sel_item_row["name"]!="")
			{*/
				$upc_code_name =$sel_item_row["upc_code"]."-:".$sel_item_row["name"]; 
			/*}
			else
			{
				$upc_code_name =$sel_item_row["upc_code"];
			}*/
	
			$stringAllUpc[$sel_item_row['id']]=str_replace("'","",$upc_code_name);		
		}
		
	}
	return $stringAllUpc;	
}

function replace_spl_char($str)
{
	$string = preg_replace('/[^A-Za-z0-9\+\/\-]/', ' ', $str);
	return $string;
}

function convertUcfirst($inStr){

	if(trim($inStr)!="")
	{
		//$tempStr=ucfirst((strtolower($inStr)));
		$tempStr=ucwords($inStr);
	}else{
	$tempStr="";
	}
	return $tempStr;

}
function core_phone_unformat($phone_number){
	return preg_replace('/[^0-9]/','',$phone_number);
}

function filterPrac_code(&$ary, $key, $m){
	if($ary!=$m){
		$ary="";
	}
	else{
		$ary = trim($ary);
	}
}

function back_prac_id($item_prac_code,$multi=false,$module_type_id='')
{
	if($item_prac_code!="")
	{
		$cat_where='';
		if( $module_type_id=='1' || $module_type_id=='2' || $module_type_id=='3'){
			$sql_cpt_category = 'SELECT `cpt_cat_id` AS \'cat_id\' FROM `cpt_category_tbl` WHERE LOWER(`cpt_category`)=';
			/*Get cpt category for the item type*/
			if( $module_type_id==3 )
				$sql_cpt_category .= '\'contact lens\'';
			else
				$sql_cpt_category .= '\'optical\'';
			
			$cpt_category_id = false;
			$cpt_category_resp = imw_query( $sql_cpt_category );
			
			if( $cpt_category_resp && imw_num_rows($cpt_category_resp) > 0 )
				$cpt_category_id = imw_fetch_object( $cpt_category_resp );
			if($cpt_category_id)$cat_where=" and cpt_cat_id='$cpt_category_id->cat_id'";
		}
		if($multi){
			$array_match_prac = explode(";",$item_prac_code);
			$item_prac_code = str_replace(";","','",$item_prac_code);
			$item_prac_code = "'".$item_prac_code."'";
			
			$procedureId = array();
			$getProcIdStr = "SELECT cpt_fee_id,cpt4_code,cpt_prac_code FROM cpt_fee_tbl WHERE cpt_prac_code IN($item_prac_code) AND delete_status = '0' ORDER BY FIELD(cpt_prac_code, $item_prac_code)";
			
			$getProcIdQry = imw_query($getProcIdStr);
			while($getProcIdRow = imw_fetch_array($getProcIdQry)){
				
				$procId = $getProcIdRow['cpt_prac_code'];
				
				$array_match_prac1 = $array_match_prac;
				array_walk($array_match_prac1, 'filterPrac_code', $procId);
				foreach($array_match_prac1 as $akey=>$aval){
					if(trim($aval)==""){
						unset($array_match_prac1[$akey]);
					}
					else{
						array_push($procedureId, $getProcIdRow['cpt_fee_id']);
					}
				}
			}
			
			if(imw_num_rows($getProcIdQry)==0){
				$getProcIdStr = "SELECT cpt_fee_id,cpt4_code,cpt_prac_code FROM cpt_fee_tbl WHERE (cpt4_code IN ($item_prac_code) or cpt_desc IN($item_prac_code)) AND delete_status = '0' ORDER BY FIELD(cpt_prac_code, $item_prac_code)";
				$getProcIdQry = imw_query($getProcIdStr);
				while($getProcIdRow = imw_fetch_array($getProcIdQry)){
					$procId = $getProcIdRow['cpt4_code'];
					$array_match_prac1 = $array_match_prac;
					
					array_walk($array_match_prac1, 'filterPrac_code', $procId);
					foreach($array_match_prac1 as $akey=>$aval){
						if(trim($aval)==""){
							unset($array_match_prac1[$akey]);
						}
						else{
							array_push($procedureId, $getProcIdRow['cpt_fee_id']);
						}
					}
				}
			}
			$procedureId = implode(';',$procedureId);
		}
		else{
			$getProcIdStr = "SELECT cpt_fee_id,cpt4_code,cpt_prac_code FROM cpt_fee_tbl WHERE (cpt_prac_code='$item_prac_code') $cat_where AND delete_status = '0'";
			$getProcIdQry = imw_query($getProcIdStr);
			$getProcIdRow = imw_fetch_array($getProcIdQry);
			$procedureId = $getProcIdRow['cpt_fee_id'];
			$cpt4_code = $getProcIdRow['cpt4_code'];
			$cpt_prac_code = $getProcIdRow['cpt_prac_code'];
			if(imw_num_rows($getProcIdQry)==0){
				$getProcIdStr = "SELECT cpt_fee_id,cpt4_code,cpt_prac_code FROM cpt_fee_tbl WHERE (cpt4_code='$item_prac_code' or cpt_desc='$item_prac_code') AND delete_status = '0'";
				$getProcIdQry = imw_query($getProcIdStr);
				$getProcIdRow = imw_fetch_array($getProcIdQry);
				$procedureId = $getProcIdRow['cpt_fee_id'];
				$cpt4_code = $getProcIdRow['cpt4_code'];
				$cpt_prac_code = $getProcIdRow['cpt_prac_code'];
			}
		}
		
		$module_type_id = trim($module_type_id);
		/*Add New Prac Code for the category if Prac code not found in the database*/
		
		if( ($procedureId === NULL || $procedureId=='') && ($module_type_id=='1' || $module_type_id=='2' || $module_type_id=='3') ){
			
			$sql_cpt_category = 'SELECT `cpt_cat_id` AS \'cat_id\' FROM `cpt_category_tbl` WHERE LOWER(`cpt_category`)=';
			/*Get cpt category for the item type*/
			if( $module_type_id==3 )
				$sql_cpt_category .= '\'contact lens\'';
			else
				$sql_cpt_category .= '\'optical\'';
			
			$cpt_category_id = false;
			$cpt_category_resp = imw_query( $sql_cpt_category );
			
			if( $cpt_category_resp && imw_num_rows($cpt_category_resp) > 0 )
				$cpt_category_id = imw_fetch_object( $cpt_category_resp );
			
			/*Add new Prac Code*/
			if( $cpt_category_id ){
				$sql_add_cpt_code = 'INSERT INTO `cpt_fee_tbl`(`cpt_cat_id`, `cpt4_code`, `cpt_prac_code`, `cpt_desc`, `units`, `commonlyUsed`, `status`) VALUES ('.$cpt_category_id->cat_id.', \''.$item_prac_code.'\', \''.$item_prac_code.'\', \''.$item_prac_code.'\', 1, 1, \'Active\')';
				if( imw_query( $sql_add_cpt_code ) ){
					$procedureId = imw_insert_id();
					
					/*CPT Fee*/
					$cpt_fee_sql= 'INSERT INTO `cpt_fee_table`(`cpt_fee_id`, `fee_table_column_id`, `cpt_fee`) VALUES';
					$fee_values	= '';
					$resp_fee_table_columns = imw_query('SELECT `fee_table_column_id` FROM `fee_table_column`');
					while( $fee_tablc_column = imw_fetch_object($resp_fee_table_columns) ){
						$fee_values .= '('.$procedureId .', '.$fee_tablc_column->fee_table_column_id.', \'0.00\'),';
					}
					
					if( $fee_values!='' ){
						$fee_values = rtrim($fee_values, ',');
						$cpt_fee_sql = $cpt_fee_sql.$fee_values;
						imw_query($cpt_fee_sql);
					}
				}
			}
		}
	}
	else
	{
		$procedureId='';
	}
	return $procedureId;
}

function back_dx_id($dx_code)
{
	if($dx_code!="")
	{
		//$sel_dx = imw_query("select diagnosis_id from diagnosis_code_tbl where (d_prac_code='$dx_code' or diag_description='$dx_code') and delete_status='0'");
		$sel_dx = imw_query("select id as diagnosis_id from icd10_data where (icd10='$dx_code' or icd10_desc='$dx_code') and deleted='0'");
		$get_dx_id = imw_fetch_array($sel_dx);
		$diagnosisId=$get_dx_id['diagnosis_id'];
	}
	else
	{
		$diagnosisId='';
	}
	
	return $diagnosisId;
}



function update_order_faciliy($order_id,$order_detail_id,$patient_id,$item_id,$total_qty)
{
	$opr_id = $_SESSION['authId'];
	$date = date("Y-m-d");
	$time = date("h:i:s");
	//$facility_id_qty = get_facility_ids($item_id,$total_qty);
	$sel_ex_order_fac = imw_query("select id from in_order_fac where order_id='$order_id' and order_det_id='$order_detail_id' and patient_id='$patient_id' and item_id='$item_id' and del_status='0'");
	if(imw_num_rows($sel_ex_order_fac) > 0)
	{
		while($get_ex_fac = imw_fetch_array($sel_ex_order_fac))
		{
			$update_ex_fac = imw_query("update in_order_fac set qty='0', modified_date='$date', modified_time='$time', modified_by='$opr_id' where id='".$get_ex_fac['id']."'");
		}
	}
	
	$loc_id = "";
	$sel_order_fac = imw_query("select id from in_order_fac where order_id='$order_id' and order_det_id='$order_detail_id' and patient_id='$patient_id' and item_id='$item_id' and loc_id='".$_SESSION['pro_fac_id']."' and del_status='0' LIMIT 1");
	if(imw_num_rows($sel_order_fac) > 0)
	{
		$order_fac_row = imw_fetch_array($sel_order_fac);
		$act = "update";
		$qryWhere=", modified_date='$date', modified_time='$time', modified_by='$opr_id' WHERE id='".$order_fac_row['id']."'";
	}
	else
	{
		$act = "insert into";
		$loc_id = " `loc_id`='".$_SESSION['pro_fac_id']."', ";
		$qryWhere=", entered_date='$date', entered_time='$time', entered_by='$opr_id'";
	}
	
	$sel_fac = imw_query("select fac.id from facility as fac 
								left join pos_facilityies_tbl as pos_fac on pos_fac.pos_facility_id=fac.fac_prac_code 
								left join in_location as loc on loc.pos=pos_fac.pos_facility_id  
								where loc.id='$_SESSION[pro_fac_id]' LIMIT 1");
	$fac_data=imw_fetch_array($sel_fac);
	//this variable is being overwrite below
	$facility_id=$fac_data['id'];
	imw_free_result($sel_fac);
	
	//overwrite linked facility id with newly configured linking function
	$qry_fac=imw_query("select idoc_fac_id from in_location where id='$_SESSION[pro_fac_id]'");
	$row_fac = imw_fetch_array($qry_fac);
	if($row_fac['idoc_fac_id']>0)$facility_id=$row_fac['idoc_fac_id'];
	
	$query = imw_query("$act in_order_fac set $loc_id order_id='$order_id', order_det_id='$order_detail_id', patient_id='$patient_id', item_id='$item_id', facility_id='$facility_id', qty='$total_qty' $qryWhere");
	
}

function get_facility_ids($item_id,$tot_qty)
{
	$fac_id_qty = array();
	$df_ord_fac_id = $_SESSION['pro_fac_id'];
	$fac_stock = 0;
	$locid=0;
	$sel_qty_fac = imw_query("select fac.id, loc_tot.id as locid, loc_tot.stock from facility as fac left join pos_facilityies_tbl as pos_fac on pos_fac.pos_facility_id=fac.fac_prac_code left join in_location as loc on loc.pos=pos_fac.pos_facility_id left join in_item_loc_total as loc_tot on loc_tot.loc_id=loc.id where fac.id='$df_ord_fac_id' and loc_tot.item_id='$item_id' and loc_tot.stock>0 LIMIT 1");
	if(imw_num_rows($sel_qty_fac)>0)
	{
		$get_fac_stock = imw_fetch_array($sel_qty_fac);
		$fac_stock = $get_fac_stock['stock'];
		$df_ord_fac_id = $get_fac_stock['id'];
		$locid = $get_fac_stock['locid'];
	}
	
	if($tot_qty > $fac_stock)
	{
		if($fac_stock > 0)
		{
			$fac_id_qty[$df_ord_fac_id] = $fac_stock;
		}
		$rest_qty = $tot_qty - $fac_stock;
		$gt=1;
		for($i=0;$gt!=0;$i++)
		{
			$sel_grt_qty = imw_query("select fac.id, loc_tot.id as locid, loc_tot.stock from in_item_loc_total as loc_tot left join in_location as loc on loc.id=loc_tot.loc_id left join facility as fac on fac.fac_prac_code=loc.pos where loc_tot.item_id='$item_id' and loc_tot.stock>0 and loc_tot.id not in ($locid) group by loc_tot.stock desc LIMIT 1");
			if(imw_num_rows($sel_grt_qty)>0)
			{
				$get_grt_stock = imw_fetch_array($sel_grt_qty);
				if($rest_qty > $get_grt_stock['stock'])
				{
					$rest_qty = $rest_qty - $get_grt_stock['stock'];
					$ord_fac_id = $get_grt_stock['id'];
					$fac_id_qty[$ord_fac_id] = $get_grt_stock['stock'];
					$locid = $locid.','.$get_grt_stock['locid'];
				}
				else
				{
					$fac_stock = $rest_qty;
					$ord_fac_id = $get_grt_stock['id'];
					$fac_id_qty[$ord_fac_id] = $fac_stock;
					$gt=0;
				}
			}
			else
			{
				$fac_id_qty[$df_ord_fac_id] = $fac_stock+$rest_qty;
				$gt=0;
			}
		}
	}
	else
	{
		$fac_id_qty[$df_ord_fac_id] = $tot_qty;
	}
	
	return $fac_id_qty;
}

function update_item_loc_qty($orderid,$single_item,$patient,$item_id,$ret_qty,$module_id,$reason,$status)
{
	$operator_id = $_SESSION['authId'];
	$date = date("Y-m-d");
	$time = date("H:i:s");
	$red_qty = "";
	$get_qty=0;
	$select_fac = imw_query("select ord_fac.id, ord_fac.facility_id, loc_tot.stock, ord_fac.qty, loc_tot.id as locid, loc_tot.loc_id as location_id from in_order_fac as ord_fac left join facility as fac on fac.id=ord_fac.facility_id left join in_location as loc on loc.pos=fac.fac_prac_code left join in_item_loc_total as loc_tot on loc_tot.loc_id=loc.id where loc_tot.item_id='$item_id' and ord_fac.del_status='0' and ord_fac.order_id='$orderid' and ord_fac.order_det_id='$single_item' and ord_fac.patient_id='$patient' and ord_fac.item_id='$item_id' order by ord_fac.id asc");
	if(imw_num_rows($select_fac) > 0)
	{
		while($get_faclty = imw_fetch_array($select_fac))
		{
			$bk_qty = 0;
			$return_id = 0;
			if($ret_qty>0)
			{
				$sel_return = imw_query("select id, return_qty, facility_id from in_order_return where order_id='$orderid' and order_detail_id='$single_item' and patient_id='$patient' and item_id='$item_id' and facility_id='".$get_faclty['facility_id']."' and del_status='0' LIMIT 1");
				if(imw_num_rows($sel_return)>0)
				{
					$get_row = imw_fetch_array($sel_return);
					$bk_qty = $get_row['return_qty'];
					$return_id = $get_row['id'];
					$act="update";
					$whr=", modified_date='$date', modified_time='$time', modified_by='$operator_id' where id='".$get_row['id']."'";
				}
				else
				{
					$act="insert into";
					$whr=", entered_date='$date', entered_time='$time', entered_by='$operator_id'";
				}
				if($bk_qty==$get_faclty['qty'])
				{
				}
				elseif(($ret_qty+$bk_qty)>$get_faclty['qty'])
				{	
					$get_qty += $get_faclty['qty']-$bk_qty;
					$red_qty = $get_faclty['stock']+($get_faclty['qty']-$bk_qty);
					
					$ret_qry = imw_query("$act in_order_return set order_id='$orderid', patient_id='$patient', item_id='$item_id', order_detail_id='$single_item', module_type_id='$module_id', facility_id='".$get_faclty['facility_id']."',  return_qty='".$get_faclty['qty']."', reason='$reason', status='$status' $whr");
					$getoid = imw_insert_id();
					
					imw_query("update in_item_loc_total set stock='$red_qty' where id='".$get_faclty['locid']."'");
					
					/*Update Lot respective to Qty reduced from Lot while order dispensing*/
						$order_lot = imw_query("SELECT `lot_no`, `qty` FROM `in_order_lot_details`
												  WHERE `order_detail_id`='".$single_item."'");
						if($order_lot && imw_num_rows($order_lot)>0){
							
							$pending_qty_return = $bk_qty;
							while($order_lot_row = imw_fetch_object($order_lot)){
								
								$qty_returned = 0;
								if($pending_qty_return>=$order_lot_row->qty){
									$qty_returned = $order_lot_row->qty;
									$pending_qty_return = $pending_qty_return - $qty_returned;
								}
								elseif($pending_qty_return>0){
									$qty_returned = $pending_qty_return;
									$pending_qty_return = 0;
								}
								
								if($qty_returned>0){
									imw_query("UPDATE `in_item_lot_total` SET `stock`=`stock`+".$qty_returned."
												WHERE `id`='".$order_lot_row->lot_no."'");
									/*Inset Stock Modification History*/
									imw_query("INSERT INTO `in_stock_detail` SET `item_id`='".$item_id."',
												`loc_id`='".$get_faclty['locid']."', `stock`='".$qty_returned."', `trans_type`='add',
												`operator_id`='".$operator_id."', `entered_date`='".$date."', `entered_time`='".$time."',
												`lot_id`='".$order_lot_row->lot_no."', `order_id`='".$orderid."',
												`order_detail_id`='".$single_item."', `is_return`=1, source='Order'");
								}
							}
						}
						else{
							/*Fallback Condition*/
							imw_query('UPDATE `in_item_lot_total` SET `stock`=`stock`+'.$bk_qty.' WHERE `item_id`='.$item_id.'
										AND `loc_id`='.$get_faclty['location_id'].' LIMIT 1');
							/*Inset Stock Modification History*/
							imw_query("INSERT INTO `in_stock_detail` SET `item_id`='".$item_id."',
										`loc_id`='".$get_faclty['locid']."', `stock`='".$bk_qty."', `trans_type`='add',
										`operator_id`='".$operator_id."', `entered_date`='".$date."', `entered_time`='".$time."',
										`lot_id`='0', `order_id`='".$orderid."',
										`order_detail_id`='".$single_item."', `is_return`=1, source='Order'");
						}
					/*End Update Lot*/
					
					if($return_id==0)
					{
						$return_id = $getoid;
					}
					$ins_modifier = imw_query("insert into in_return_modifier set ord_return_id='$return_id', facility_id='".$get_faclty['facility_id']."', qty='".$get_faclty['qty']."', ret_reason='$reason', ret_status='$status', modified_date='$date', modified_time='$time', modified_by='$operator_id'");
					
					$ret_qty = ($ret_qty+$bk_qty)-$get_faclty['qty'];
				}
				else
				{
					$get_qty += $ret_qty;
					$red_qty = $get_faclty['stock']+$ret_qty;
					$ret_qry = imw_query("$act in_order_return set order_id='$orderid', patient_id='$patient', item_id='$item_id', order_detail_id='$single_item', module_type_id='$module_id', facility_id='".$get_faclty['facility_id']."',  return_qty='".$ret_qty."', reason='$reason', status='$status' $whr");
					$getoid = imw_insert_id();
					imw_query("update in_item_loc_total set stock='$red_qty' where id='".$get_faclty['locid']."'");
					
					/*Update Lot respective to Qty reduced from Lot while order dispensing*/
						$order_lot = imw_query("SELECT `lot_no`, `qty` FROM `in_order_lot_details`
												  WHERE `order_detail_id`='".$single_item."'");
						if($order_lot && imw_num_rows($order_lot)>0){
							
							$pending_qty_return = $ret_qty;
							while($order_lot_row = imw_fetch_object($order_lot)){
								
								$qty_returned = 0;
								if($pending_qty_return>=$order_lot_row->qty){
									$qty_returned = $order_lot_row->qty;
									$pending_qty_return = $pending_qty_return - $qty_returned;
								}
								elseif($pending_qty_return>0){
									$qty_returned = $pending_qty_return;
									$pending_qty_return = 0;
								}
								
								if($qty_returned>0){
									imw_query("UPDATE `in_item_lot_total` SET `stock`=`stock`+".$qty_returned."
												WHERE `id`='".$order_lot_row->lot_no."'");
									/*Inset Stock Modification History*/
									imw_query("INSERT INTO `in_stock_detail` SET `item_id`='".$item_id."',
												`loc_id`='".$get_faclty['locid']."', `stock`='".$qty_returned."', `trans_type`='add',
												`operator_id`='".$operator_id."', `entered_date`='".$date."', `entered_time`='".$time."',
												`lot_id`='".$order_lot_row->lot_no."', `order_id`='".$orderid."',
												`order_detail_id`='".$single_item."', `is_return`=1, source='Order'");
								}
							}
						}
						else{
							/*Fallback Condition*/
							imw_query('UPDATE `in_item_lot_total` SET `stock`=`stock`+'.$ret_qty.' WHERE `item_id`='.$item_id.' AND `loc_id`='.$get_faclty['location_id'].' LIMIT 1');
							/*Inset Stock Modification History*/
							imw_query("INSERT INTO `in_stock_detail` SET `item_id`='".$item_id."',
										`loc_id`='".$get_faclty['locid']."', `stock`='".$ret_qty."', `trans_type`='add',
										`operator_id`='".$operator_id."', `entered_date`='".$date."', `entered_time`='".$time."',
										`lot_id`='0', `order_id`='".$orderid."',
										`order_detail_id`='".$single_item."', `is_return`=1, source='Order'");
						}
					/*End Update Lot*/
					if($return_id==0)
					{
						$return_id = $getoid;
					}
					$ins_modifier = imw_query("insert into in_return_modifier set ord_return_id='$return_id', facility_id='".$get_faclty['facility_id']."', qty='$ret_qty', ret_reason='$reason', ret_status='$status', modified_date='$date', modified_time='$time', modified_by='$operator_id'");
					$ret_qty = 0;
				}
			}
			else
			{
				break;
			}
		}
		
		$item_qry = imw_query("select retail_price, id, qty_on_hand from in_item where id='$item_id'");
		$fch_item_qry = imw_fetch_array($item_qry);
		$new_amt=0;
		$new_qty = $fch_item_qry['qty_on_hand']+$get_qty;
		if($new_qty>0)
		{
			$new_amt = $fch_item_qry['retail_price']*$new_qty;
		}
		$act_qty = imw_query("update in_item set qty_on_hand='$new_qty', amount='$new_amt', modified_date='$date', modified_time='$time', modified_by='$operator_id' where id='$item_id'");
	}
}

function delete_stock_item($del_id)
{
	$operator_id = $_SESSION['authId'];
	$date = date("Y-m-d");
	$time = date("H:i:s");
	
	$upd_item_stat = imw_query("update in_item set del_status='2', del_date='$date', del_time='$time', del_by='$operator_id' where id='$del_id'");

}
function mkfloat($val){
	return str_replace(",", "", $val);
}

function getLensDetailValues($vals){
	
	$rtData = array();
	if(count($vals)>0){
		
		foreach($vals as $key=>$val){
			$sql = "";
			switch($key){
				//array('lens','progressive','material','transition','a_r','tint','polarization','edge','color','uv400','other','pgx');
				case "lens":
					$sql = "select type_name AS 'elem_val' from in_lens_type where del_status='0' and id='".$val."'";
				break;
				case "design":
					$sql = "select design_name AS 'elem_val' from in_lens_design where del_status='0' and id='".$val."'";
				break;
				case "progressive":
					$sql = "select progressive_name AS 'elem_val' from in_lens_progressive where del_status='0' and id='".$val."'";
				break;
				case "material":
					$sql = "select material_name AS 'elem_val' from in_lens_material where del_status='0' and id='".$val."'";
				break;
				case "transition":
					$sql = "select transition_name AS 'elem_val' from in_lens_transition where del_status='0' and id='".$val."'";
				break;
				case "a_r":
					$sql = "select ar_name AS 'elem_val' from in_lens_ar where del_status='0' and id='".$val."'";
				break;
				case "tint":
					$sql = "select tint_type AS 'elem_val' from in_lens_tint where del_status='0' and id='".$val."'";
				break;
				case "polarization":
					$sql = "select polarized_name AS 'elem_val' from in_lens_polarized where del_status='0' and id='".$val."'";
				break;
				case "edge":
					$sql = "select edge_name AS 'elem_val' from in_lens_edge where del_status='0' and id='".$val."'";
				break;
				case "color":
					$sql = "select color_name AS 'elem_val' from in_lens_color where del_status='0' and id='".$val."'";
				break;
			}
			
			$rtData[$key] = "";
			if($sql!=""){
				$sql = imw_query($sql);
				if($sql){
					$sql = imw_fetch_assoc($sql);
					$rtData[$key] = $sql['elem_val'];
				}
			}
			
		}
	}
	return($rtData);
}
function currency_symbol($return = false){
	if(!$return){
		echo ($GLOBALS['CURRENCY_SYMBOL'])?$GLOBALS['CURRENCY_SYMBOL']:"$";
	}
	else{
		return (($GLOBALS['CURRENCY_SYMBOL'])?$GLOBALS['CURRENCY_SYMBOL']:"$");
	}
}
function make_reorder($ord_id, $ord_detail_id=array(), $action="reorder"){
	
	$operator_id=$_SESSION['authId'];
	$entered_date=date('Y-m-d');
	$entered_time=date('H:i:s');
	$taxRates = get_tax_rates();
	$tax_payable = 0;
	
	$ord_detail_id_qry="";
	$ord_lens_detail_id_qry="";
	$ord_rx_detail_id_qry="";
	$ord_cl_disinfectent_id_qry="";
	if(count($ord_detail_id)>0){
		$ord_detail_id_imp=implode(',',$ord_detail_id);
		$ord_detail_id_qry=" and id in($ord_detail_id_imp)";
		$ord_lens_detail_id_qry=" and order_detail_id in($ord_detail_id_imp)";
		$ord_rx_detail_id_qry=" and det_order_id in($ord_detail_id_imp)";
		$ord_cl_disinfectent_id_qry=" and order_detail_id in($ord_detail_id_imp)";
	}
	
	//in order detail 
	$ord_dt_skip_arr=array("id","order_id", "order_chld_id", "order_chld_id_os", "dispatch_qty", "pending_qty", "return_qty", "pt_paid", "pt_paid_os", "ins_amount", "ins_amount_os", "ins_id", "ins_id_os", "ins_case_id", "ins_case_id_os", "entered_date", "entered_time", "operator_id", "modified_date", "modified_time", "modified_by", "order_status", "ordered", "received", "notified", "dispensed", "discount_code", "discount_code_os", "overall_discount", "overall_discount_os", "wholesale_price", "vw_exchange_id", "vw_order_id", "vw_sent_date", "vw_status");
	//contact lens
	$ord_disinfect_skip_arr=array("id","order_id","order_detail_id","order_chld_id","pt_paid","ins_amount","ins_case_id","entered_date",
	"entered_time","entered_by","modified_date","modified_time","modified_by","del_status","discount","discount_code","del_operator_id",
	"del_date","del_time");
	//in order lens price vision
	$ord_lens_dt_skip_arr=array("id","order_id","order_detail_id","order_enc_id","order_chld_id",
	"ins_amount","pt_paid","ins_id","ins_case_id","discount_code", "comment");
	/*"discount"	//Commented because this was preventing application of default discount on line item.*/
	
	$ord_lens_rx_skip_arr=array("id","order_id","det_order_id","modified_date","modified_time","modified_by");
	
	$order_loc_id = ($action=="remake")?$_REQUEST['remake_fac']:$_SESSION['pro_fac_id'];
	
	$ord_qry="insert into in_order set entered_date='$entered_date',entered_time='$entered_time',operator_id='$operator_id',modified_date='$entered_date',modified_time='$entered_time',modified_by='$operator_id',order_status='pending',loc_id='".$order_loc_id."'";
	
	$remakeWithoutCharges = false;
	
	if($action=="remake"){
		
		$remakeWithoutCharges = (boolean)$_REQUEST['remake_without_charges'];
		if( $remakeWithoutCharges ){
			array_push($ord_dt_skip_arr, "price", "price_os", "total_amount", "total_amount_os", "pt_resp", "pt_resp_os", "tax_paid", "tax_paid_os", "tax_applied", "tax_applied_os", "allowed", "allowed_os", "discount_val", "discount_val_os", "discount", "discount_os");
			array_push($ord_disinfect_skip_arr, "price", "total_amount", "pt_resp", "tax_paid", "tax_applied", "allowed");
			array_push($ord_lens_dt_skip_arr, "wholesale_price", "allowed", "tax_paid", "tax_applied", "total_amt", "pt_resp", "discount");
		}
		$ord_qry .= ", re_make_id='$ord_id'";
	}
	elseif($action=='reorder')
	{
		array_push($ord_dt_skip_arr, "tax_applied");
		array_push($ord_disinfect_skip_arr, "tax_applied");
		array_push($ord_lens_dt_skip_arr, "tax_applied");
	}
	else{
		$ord_qry .= ", re_order_id='$ord_id'";
	}
	
	imw_query($ord_qry);
	$new_order_id=imw_insert_id();
	
	$ord_dt_field_qry=imw_query("select * from in_order_details where order_id='$ord_id' and del_status='0' $ord_detail_id_qry"); 
	while($ord_dt_field_row=imw_fetch_assoc($ord_dt_field_qry)){
		$ord_dt_arr[]=$ord_dt_field_row;
		$ord_item_dt_arr[$ord_dt_field_row['item_id']]=$ord_dt_field_row['item_id'];
		if($ord_dt_field_row['item_id_os']!=0){
			$ord_item_dt_arr[$ord_dt_field_row['item_id_os']]=$ord_dt_field_row['item_id_os'];
		}
		$patient_id=$ord_dt_field_row['patient_id'];
	}
	
	$ord_item_dt_imp=implode(', ',$ord_item_dt_arr);
	
	$item_qry=imw_query("select id,discount,discount_till from in_item where id in($ord_item_dt_imp)"); 
	while($item_row=imw_fetch_assoc($item_qry)){
		if($item_row['discount_till']>=$entered_date || $item_row['discount_till']=='0000-00-00'){
			$item_dis_arr[$item_row['id']]=$item_row['discount'];
		}
	}
	
	$ord_lens_dt_field_qry=imw_query("select * from in_order_lens_price_detail where order_id='$ord_id' and del_status='0' $ord_lens_detail_id_qry"); 
	while($ord_lens_dt_field_row=imw_fetch_assoc($ord_lens_dt_field_qry)){
		$ord_lens_dt_arr[$ord_lens_dt_field_row['order_detail_id']][]=$ord_lens_dt_field_row;
	}
	
	$ord_lens_rx_field_qry=imw_query("select * from in_optical_order_form where order_id='$ord_id' and del_status='0' $ord_rx_detail_id_qry"); 
	while($ord_lens_rx_field_row=imw_fetch_assoc($ord_lens_rx_field_qry)){
		$ord_lens_rx_arr[$ord_lens_rx_field_row['det_order_id']][]=$ord_lens_rx_field_row;
	}
	
	$ord_cl_lens_rx_field_qry=imw_query("select * from in_cl_prescriptions where order_id='$ord_id' and del_status='0' $ord_rx_detail_id_qry"); 
	while($ord_cl_lens_rx_field_row=imw_fetch_assoc($ord_cl_lens_rx_field_qry)){
		$ord_cl_lens_rx_arr[$ord_cl_lens_rx_field_row['det_order_id']][]=$ord_cl_lens_rx_field_row;
	}
	
	/*contact lens disinfectent data*/
	$ord_cl_disinfect_field_qry=imw_query("select * from in_order_cl_detail where order_id='$ord_id' and del_status='0' $ord_cl_disinfectent_id_qry"); 
	while($ord_cl_disinfect_field_row=imw_fetch_assoc($ord_cl_disinfect_field_qry)){
		$ord_cl_disinfect_arr[$ord_cl_disinfect_field_row['order_detail_id']][]=$ord_cl_disinfect_field_row;
	}
	
	$ord_tot_qty_arr=array();
	$ord_tot_price_arr=array();
	//print_r($ord_lens_rx_arr);
	$total_qty_fac = $item_id_fac = $patient_id_fac = "";
	foreach($ord_dt_arr as $key=>$val){
		$insert_arr=array();
		$insert_imp="";
		$ins_lens_dt_arr=array();
		$ins_lens_rx_arr=array();
		$ins_cl_lens_rx_arr=array();
		$ins_cl_disinfect_arr=array();
		$lens_discount = array();
		$ins_lens_dt_imp="";
		$lens_frame_id=$ord_dt_arr[$key]['lens_frame_id'];	
		foreach($ord_dt_arr[$key] as $key2=>$val2){
			$tax_paid  = $total_amt_tax_cal = $tax_rate = "";
			
			$item_dis=$item_dis_arr[$ord_dt_arr[$key]['item_id']];
			$exp_discount=explode('%',$item_dis);
			$exp_discount_os = 0;
			$item_dis_os = 0;
			if($ord_dt_arr[$key]['module_type_id'] == 3 && isset($item_dis_arr[$ord_dt_arr[$key]['item_id_os']])){
				$item_dis_os = $item_dis_arr[$ord_dt_arr[$key]['item_id_os']];
				$exp_discount_os =explode('%',$item_dis_os);
			}
			
			$ord_tot_qty_arr[$ord_dt_arr[$key]['id']]=($ord_dt_arr[$key]['qty']+$ord_dt_arr[$key]['qty_right']);
			if($key2=="id"){
				foreach($ord_lens_dt_arr[$val2] as $key3=>$val3){
					foreach($val3 as $key4=>$val4){
						if(!in_array($key4,$ord_lens_dt_skip_arr)){
							$ord_lens_item_allow=$ord_lens_dt_arr[$val2][$key3]['wholesale_price']*($ord_lens_dt_arr[$val2][$key3]['qty']);
							if($key4=="itemized_id"){
								$itemized_id=$val4;
							}
							if(count($exp_discount)>1){
								$final_discount=($ord_lens_item_allow*$exp_discount[0])/100;
							}else{
								if($item_dis>$ord_lens_item_allow){
									$final_discount=$ord_lens_item_allow;
								}
								else{
									$final_discount=$exp_discount[0];
								}
							}
							if($ord_lens_item_allow<=0){
								$final_discount=0;
							}
							//$ord_lens_dt_arr[$val2][$key3]['discount']=$final_discount;
							if($ord_lens_item_allow>0){
								if(count($exp_discount)==1 && $item_dis>$ord_lens_item_allow){
									$ord_lens_dt_arr[$val2][$key3]['discount']=$ord_lens_item_allow;
								}
								else{
									$ord_lens_dt_arr[$val2][$key3]['discount']=$item_dis;
								}
							}
							else{
								$ord_lens_dt_arr[$val2][$key3]['discount'] = "0";
							}
							if($key4=="discount"){
								array_push($lens_discount, $final_discount);
							}
							$ord_lens_dt_arr[$val2][$key3]['allowed']=str_replace(',','',number_format($ord_lens_item_allow,2));
							if($key4=="total_amt"){
								$ord_lens_dt_arr[$val2][$key3]['total_amt']=str_replace(',','',number_format(($ord_lens_item_allow-$final_discount),2));
								$ord_lens_dt_arr[$val2][$key3]['pt_resp']=$ord_lens_dt_arr[$val2][$key3]['total_amt'];
								//$ord_tot_price_arr[]=$ord_lens_dt_arr[$val2][$key3]['total_amt'];
								$ord_tot_price_arr[]=str_replace(',','',number_format($ord_lens_item_allow,2));
								
								$ord_lens_prc_dt_arr[$key]['discount'][]=$ord_lens_dt_arr[$val2][$key3]['discount'];
								$ord_lens_prc_dt_arr[$key]['total_amount'][]=$ord_lens_dt_arr[$val2][$key3]['total_amt'];
								$ord_lens_prc_dt_arr[$key]['allowed'][]=$ord_lens_dt_arr[$val2][$key3]['allowed'];
					
							}
							elseif($key4=="tax_rate"){
								$tax_rate = $taxRates[$ord_lens_dt_arr[$val2][$key3]['module_type_id']];
								$total_amt_tax_cal = $ord_lens_dt_arr[$val2][$key3]['total_amt'];
								$tax_paid = str_replace(',','',number_format((($total_amt_tax_cal*$tax_rate)/100),2));
								
								$ord_lens_dt_arr[$val2][$key3]['tax_rate'] = $tax_rate;
								$ord_lens_dt_arr[$val2][$key3]['tax_paid'] = $tax_paid;
								$ord_lens_dt_arr[$val2][$key3]['tax_applied'] = "1";
								
								$tax_payable += (float)$tax_paid;
								$ord_lens_prc_dt_arr[$key]['tax'][]=$ord_lens_dt_arr[$val2][$key3]['allowed'];
							}
							$ins_lens_dt_arr[$itemized_id][]=$key4."='".$ord_lens_dt_arr[$val2][$key3][$key4]."'";
						}
					}
				}
				foreach($ord_lens_rx_arr[$val2] as $key6=>$val6){
					foreach($ord_lens_rx_arr[$val2][$key6] as $key7=>$val7){
						if(!in_array($key7,$ord_lens_rx_skip_arr)){
							$ins_lens_rx_arr[]=$key7."='".$ord_lens_rx_arr[$val2][$key6][$key7]."'";
						}
					}
				}
				foreach($ord_cl_lens_rx_arr[$val2] as $key8=>$val8){
					foreach($ord_cl_lens_rx_arr[$val2][$key8] as $key9=>$val9){
						if(!in_array($key9,$ord_lens_rx_skip_arr)){
							$ins_cl_lens_rx_arr[]=$key9."='".$ord_cl_lens_rx_arr[$val2][$key8][$key9]."'";
						}
					}
				}
				
				/*contact lens Disinfectent*/
				foreach($ord_cl_disinfect_arr[$val2] as $key10=>$val10){
					$ord_tot_qty_arr[$val2."_disinf"] = $val10['qty'];
					foreach($ord_cl_disinfect_arr[$val2][$key10] as $key11=>$val11){
						if(!in_array($key11,$ord_disinfect_skip_arr)){
							if($key11=="allowed"){
								$price =  (float)$ord_cl_disinfect_arr[$val2][$key10]['price'];
								$qty = (int)$ord_cl_disinfect_arr[$val2][$key10]['qty'];
								$allowed = number_format(($price*$qty),2);
								$ord_cl_disinfect_arr[$val2][$key10][$key11] = $allowed;
								$ord_cl_disinfect_arr[$val2][$key10]['total_amount'] = $allowed;
								$ord_cl_disinfect_arr[$val2][$key10]['pt_resp'] = $allowed;
								
								/*No functionality of entering default discount for Disinfectent*/
								$ord_cl_disinfect_arr[$val2][$key10]['discount'] = "";
								$ord_cl_disinfect_arr[$val2][$key10]['discount_code'] = "0";
								$ord_tot_price_arr[] = $allowed;
							}
							elseif($key11=="tax_rate"){
								$tax_rate = $taxRates[$ord_cl_disinfect_arr[$val2][$key10]['module_type_id']];
								$total_amt_tax_cal = $ord_cl_disinfect_arr[$val2][$key10]['total_amount'];
								$tax_paid = str_replace(',','',number_format((($total_amt_tax_cal*$tax_rate)/100),2));
								
								$ord_cl_disinfect_arr[$val2][$key10]['tax_rate'] = $tax_rate;
								$ord_cl_disinfect_arr[$val2][$key10]['tax_paid'] = $tax_paid;
								$ord_cl_disinfect_arr[$val2][$key10]['tax_applied'] = "1";
								$tax_payable += (float)$tax_paid; 
							}
							$ins_cl_disinfect_arr[]=$key11."='".$ord_cl_disinfect_arr[$val2][$key10][$key11]."'";
						}
					}
				}
			}
			
			if(!in_array($key2,$ord_dt_skip_arr)){
				
				if($ord_dt_arr[$key]['module_type_id']==3){
					$ord_item_allow = $ord_dt_arr[$key]['price'] * $ord_dt_arr[$key]['qty_right'];
					$ord_item_allow_os = $ord_dt_arr[$key]['price_os'] * $ord_dt_arr[$key]['qty'];
				}
				else{
					$ord_item_allow=$ord_dt_arr[$key]['price']*($ord_dt_arr[$key]['qty']+$ord_dt_arr[$key]['qty_right']);
					$ord_item_allow_os = 0;
				}
				
				if(count($exp_discount)>1){
					$final_discount = ($ord_item_allow*$exp_discount[0])/100;
				}else{
					$final_discount=$exp_discount[0];
				}
				
				if(count($exp_discount_os)>1){
					$final_discount_os = ($ord_item_allow_os * $exp_discount_os[0])/100;
				}else{
					$final_discount_os = (isset($exp_discount_os[0])) ? $exp_discount_os[0] : 0;
				}
				
				if($ord_item_allow<=0){
					$final_discount=0;
				}
				if($ord_item_allow_os <= 0){
					$final_discount_os = 0;
				}
				
				if($ord_dt_arr[$key]['module_type_id']!=2){
					if($key2=="discount"){
						$ord_dt_arr[$key]['discount']=$item_dis;
					}
					elseif($key2=="discount_os"){
						$ord_dt_arr[$key]['discount_os']=$item_dis_os;
					}
					
					if($key2=="total_amount"){
						$ord_dt_arr[$key]['total_amount']=$ord_item_allow-$final_discount;
						//$ord_tot_price_arr[]=$ord_dt_arr[$key]['total_amount'];
						$ord_tot_price_arr[]=$ord_item_allow;
					}
					elseif($key2=="total_amount_os"){
						$ord_dt_arr[$key]['total_amount_os']=$ord_item_allow_os-$final_discount_os;
						//$ord_tot_price_arr[]=$ord_dt_arr[$key]['total_amount'];
						$ord_tot_price_arr[]=$ord_item_allow_os;
					}
					
					if($key2=="allowed"){
						$ord_dt_arr[$key]['allowed']=$ord_item_allow;
					}
					elseif($key2=="allowed_os"){
						$ord_dt_arr[$key]['allowed_os']=$ord_item_allow_os;
					}
					
					if($key2=="pt_resp"){
						$ord_dt_arr[$key]['pt_resp']=$ord_dt_arr[$key]['total_amount'];
					}
					elseif($key2=="pt_resp_os"){
						$ord_dt_arr[$key]['pt_resp_os']=$ord_dt_arr[$key]['total_amount_os'];
					}
					
					if($key2=="tax_rate"){
						$tax_rate = $taxRates[$ord_dt_arr[$key]['module_type_id']];
						$total_amt_tax_cal = $ord_item_allow-$final_discount;
						$tax_paid = str_replace(',','',number_format((($total_amt_tax_cal*$tax_rate)/100),2));


						
						$ord_dt_arr[$key]['tax_rate'] = $tax_rate;
						$ord_dt_arr[$key]['tax_paid'] = $tax_paid;
						$ord_dt_arr[$key]['tax_applied'] = "1";
						$tax_payable += (float)$tax_paid; 
					}
					elseif($key2=="tax_rate_os"){
						$tax_rate = $taxRates[$ord_dt_arr[$key]['module_type_id']];
						$total_amt_tax_cal_os = $ord_item_allow_os-$final_discount_os;
						$tax_paid_os = str_replace(',','',number_format((($total_amt_tax_cal_os*$tax_rate)/100),2));
						
						$ord_dt_arr[$key]['tax_rate_os'] = $tax_rate;
						$ord_dt_arr[$key]['tax_paid_os'] = $tax_paid_os;
						$ord_dt_arr[$key]['tax_applied_os'] = "1";
						$tax_payable += (float)$tax_paid_os; 
					}
				}else{
					$ord_dt_arr[$key]['discount']=$item_dis;
					$ord_dt_arr[$key]['discount_val']=number_format(array_sum($lens_discount), 2);
					$ord_dt_arr[$key]['total_amount']=str_replace(',','',number_format(array_sum($ord_lens_prc_dt_arr[$key]['total_amount']),2));
					$ord_dt_arr[$key]['allowed']=str_replace(',','',number_format(array_sum($ord_lens_prc_dt_arr[$key]['allowed']),2));
					$ord_dt_arr[$key]['pt_resp']=str_replace(',','',number_format(array_sum($ord_lens_prc_dt_arr[$key]['total_amount']),2));
					$ord_dt_arr[$key]['lens_frame_id']=$new_lens_frame_id_arr[$lens_frame_id];
				}
				
				$total_qty_fac = $ord_dt_arr[$key]['qty']+$ord_dt_arr[$key]['qty_right'];
				$item_id_fac = $ord_dt_arr[$key]['item_id'];
				$patient_id_fac = $ord_dt_arr[$key]['patient_id'];
				
				$insert_arr[]=$key2."='".$ord_dt_arr[$key][$key2]."'";
			}
		}
		//echo "<pre>";
		//print_r($ins_lens_dt_arr);
		$insert_imp=implode(', ',$insert_arr);
		$ord_dt_qry="insert into in_order_details set order_id='$new_order_id',entered_date='$entered_date',entered_time='$entered_time',operator_id='$operator_id', $insert_imp";
		imw_query($ord_dt_qry);
		$new_order_dt_id=imw_insert_id();
		$new_lens_frame_id_arr[$ord_dt_arr[$key]['id']]=$new_order_dt_id;
		update_order_faciliy($new_order_id,$new_order_dt_id,$patient_id_fac,$item_id_fac,$total_qty_fac);
		
		if(count($ins_lens_dt_arr)>0){
			foreach($ins_lens_dt_arr as $key5=>$val5){
				$ins_lens_dt_imp=implode(', ',$ins_lens_dt_arr[$key5]);
				$ord_lens_dt_qry="insert into in_order_lens_price_detail set order_id='$new_order_id',order_detail_id='$new_order_dt_id', $ins_lens_dt_imp";
				imw_query($ord_lens_dt_qry);
			}
		}
		
		if(count($ins_lens_rx_arr)>0){
			$ins_lens_rx_imp=implode(', ',$ins_lens_rx_arr);
			$ord_lens_rx_qry="insert into in_optical_order_form set order_id='$new_order_id',det_order_id='$new_order_dt_id', $ins_lens_rx_imp";
			imw_query($ord_lens_rx_qry);
		}
		
		if(count($ins_cl_lens_rx_arr)>0){
			$ins_cl_lens_rx_imp=implode(', ',$ins_cl_lens_rx_arr);
			$ord_cl_lens_rx_qry="insert into in_cl_prescriptions set order_id='$new_order_id',det_order_id='$new_order_dt_id', $ins_cl_lens_rx_imp";
			imw_query($ord_cl_lens_rx_qry);
		}
		
		/*contact lens Disinfectent*/
		if(count($ins_cl_disinfect_arr)>0){
			$ins_cl_disinfect_imp=implode(', ',$ins_cl_disinfect_arr);
			$ord_cl_disinfect_qry="insert into in_order_cl_detail set order_id='$new_order_id',order_detail_id='$new_order_dt_id', entered_date='$entered_date', entered_time='$entered_time', entered_by='$operator_id', $ins_cl_disinfect_imp";
			imw_query($ord_cl_disinfect_qry);
		}
	}

	$ord_tot_qty=array_sum($ord_tot_qty_arr);
	$ord_tot_price=str_replace(',','',number_format(array_sum($ord_tot_price_arr),2));
	
	/*Commented because now we are calculating tax as sum of tax applied on each line item*/
	/*$loc_qry=imw_query("select tax from in_location where id='".$_SESSION['pro_fac_id']."'");
	$loc_row=imw_fetch_array($loc_qry);
	$tax=$loc_row['tax'];
	$tax_payable=str_replace(',','',number_format((($ord_tot_price*$tax)/100),2));
	*/
	
	if($tax>0){
		$loc_prac_qry=imw_query("select prac_code from in_prac_codes where id='13'");
		$loc_prac_row=imw_fetch_array($loc_prac_qry);
		$tax_prac_code=$loc_prac_row['prac_code'];
	}
	
	$grand_total=$tax_payable+$ord_tot_price;
	if($new_order_id>0){
		$ord_up_qry="update in_order set patient_id='$patient_id',total_qty='$ord_tot_qty',total_price='$ord_tot_price',
		tax_prac_code='$tax_prac_code',tax_payable='$tax_payable',grand_total='$grand_total' where id='$new_order_id'";
		imw_query($ord_up_qry);
	}
	
	if($action=="remake"){
		
		/*$cptData=get_price_details_by_cpt_id(trim($_POST['remake_prac_code_id']));
		$tax = (float)$taxRates[8];*/
		$tax = 0;
		//$cptData['cpt_fee'] = (float)$cptData['cpt_fee'];
		$remake_price = (float)$_POST['remake_price'];
		
		/*$tax_paid = ($cptData['cpt_fee']*$tax)/100;*/
		$tax_paid = 0;
		$total_price =$remake_price + $tax_paid;
		
		$remake_price = number_format($remake_price, 2);
		$tax_paid = number_format($tax_paid, 2);
		$total_price = number_format($total_price, 2);
		
		$remake_comment = imw_real_escape_string(trim($_POST['remake_comments']));
		
		$sql = "INSERT INTO `in_order_remake_details` SET
				`order_id`='".$new_order_id."',
				`prac_code_id`='".trim($_POST['remake_prac_code_id'])."',
				`price`='".$remake_price."',
				`qty`='1',
				`allowed`='".$remake_price."',
				`total_amount`='".$remake_price."',
				`pt_resp`='".$remake_price."',
				`entered_date`='".$entered_date."',
				`entered_time`='".$entered_time."',
				`entered_by`='".$operator_id."',
				`tax_rate`='".$tax."',
				`tax_paid`='".$tax_paid."',
				`tax_applied`='0',
				`remake_reason_id`='".trim($_POST['remake_reason_id'])."',
				`remake_reason`='".imw_real_escape_string(trim($_POST['remake_reason']))."',
				`remake_doctor`='".trim($_POST['remake_doctor'])."',
				`remake_optician`='".trim($_POST['remake_optician'])."',
				`remake_lab`='".trim($_POST['remake_lab'])."'
				";
		if(imw_query($sql)){
			/*Calculate Total Price of the order if it is Remake with Charges*/
			if( !$remakeWithoutCharges ){
				$total_price1	= $ord_tot_price + $total_price;
				$tax_paid		= $tax_payable + $tax_paid;
				$total_price	= $grand_total + $total_price;
			}
			$ord_up_qry="update in_order set total_price='".$total_price1."', tax_payable='".$tax_paid."', grand_total='".$total_price."', comment='".imw_real_escape_string($remake_comment)."' where id='".$new_order_id."'";
			imw_query($ord_up_qry);
		}
	}
	
	$_SESSION['order_id']=$new_order_id;
}

function pos_cl_details($i, $order_detail_id, $order_id){
	
	$opr_id = $_SESSION['authId'];
	$date = date("Y-m-d");
	$time = date("h:i:s");
	
	if(isset($_POST['di_item_id_'.$i]) && $_POST['di_item_id_'.$i]!=""){
		$sql = "";
		$where = "";
		if($_POST['di_id_'.$i]==""){
			$sql .= "INSERT INTO `in_order_cl_detail` SET `order_detail_id`='".$order_detail_id."', `order_id`='".$order_id."', ";
			$sql .= "`entered_by`='".$opr_id."',
					 `entered_date`='".$date."',
					 `entered_time`='".$time."',";
		}
		else{
			$sql .= "UPDATE `in_order_cl_detail` SET";
			$where = " WHERE `id`='".$_POST['di_id_'.$i]."' AND `order_detail_id`='".$order_detail_id."' AND `order_id`='".$order_id."'";
			$sql .= "`modified_by`='".$opr_id."',
					 `modified_date`='".$date."',
					 `modified_time`='".$time."',";
		}
		
		$tax_applied = "0";
		if(isset($_POST['di_tax_applied_'.$i])){
			$tax_applied = "1";
		}
		
		$del_qry = "";
		if(isset($_POST['di_del_item_'.$i]) && $_POST['di_del_item_'.$i]==1){
			$sql .= "`del_status`='1',
						`del_operator_id`='".$opr_id."',
					 	`del_date`='".$date."',
					 	`del_time`='".$time."'";
			$sql .= $where;
			if($_POST['di_id_'.$i]!=""){imw_query($sql);}
			return;
		}
				
		$sql .= " `module_type_id`='".$_POST['di_module_type_id_'.$i]."',
				`item_type`='".$_POST['di_item_type_'.$i]."',
				`prac_code_id`='".$_POST['di_prac_code_id_'.$i]."',
				`item_id`='".$_POST['di_item_id_'.$i]."',
				`price`='".$_POST['di_price_'.$i]."',
				`qty`='".$_POST['di_qty_'.$i]."',
				`allowed`='".$_POST['di_allowed_'.$i]."',
				`discount`='".$_POST['di_discount_'.$i]."',
				`overall_discount`='".$_POST['di_overall_discount_'.$i]."',
				`total_amount`='".$_POST['di_total_amount_'.$i]."',
				`ins_amount`='".$_POST['di_ins_amount_'.$i]."',
				`pt_paid`='".$_POST['di_pt_paid_'.$i]."',
				`pt_resp`='".$_POST['di_pt_resp_'.$i]."',
				`discount_code`='".$_POST['di_discount_code_'.$i]."',
				`ins_case_id`='".$_POST['di_ins_case_id_'.$i]."',
				`tax_rate`='".$_POST['di_tax_p_'.$i]."',
				`tax_paid`='".$_POST['di_tax_v_'.$i]."',
				`tax_applied`='".$tax_applied."'
				".$where;
		imw_query($sql);
	}
}

function get_tax_rates(){
	$taxRates = array_fill(0,7,0); /*Default tax rate = 0*/
	$tax = imw_query("SELECT `tax` FROM `in_location` WHERE `id`='".$_SESSION['pro_fac_id']."'");
	if($tax && imw_num_rows($tax)>0){
		$tax = imw_fetch_assoc($tax);
		if($tax['tax']!="" && strpos($tax['tax'], "~~~")){
			$taxR = $tax['tax'];
			$taxR = explode("~~~", $taxR);
			$taxRates = array_replace($taxRates, $taxR);
		}
		elseif($tax['tax']!=""){
			$taxRates = array_fill(0,7,$tax['tax']);
		}
	}
	
	$taxRate[1] = $taxRates[0];	/*Frames*/
	$taxRate[2] = $taxRates[1];	/*Lenses*/
	$taxRate[3] = $taxRates[2];	/*Contact Lens*/
	$taxRate[5] = $taxRates[4];	/*Supplies*/
	$taxRate[6] = $taxRates[3];	/*Mecication*/
	$taxRate[7] = $taxRates[5];	/*Accessories/Others*/
	$taxRate[8] = $taxRates[6];	/*Remake*/
	unset($taxRates);
	return($taxRate);
}

/*
 * Function: update_tax
 * Purpose: Update tax, total_price and grand_total value in_order in case any modification in the order
 * Coded in PHP7
 * Agguments:1) ordertype, 2) order_id
 * Values:	1)	1 || 2 = Frames & Lenses, 3= Contact Lens
 * 			2)	Id of the order
 */
function update_tax($order_type, $order_id){
	if((!$order_id || $order_id=="") && (!$order_typ || $order_typ=="")){return false;}
	
	/*Fix for Overall Discount Case*/
	$overall_disocunt = 0;
	$overall_disc_resp = imw_query("SELECT `overall_discount` FROM `in_order` WHERE `id`=".$order_id);
	if($overall_disc_resp && imw_num_rows($overall_disc_resp)>0){
		$overall_disocunt = imw_fetch_assoc($overall_disc_resp);
		$overall_disocunt = $overall_disocunt['overall_discount'];
	}
	
	$dicount_float_flag = (strpos($overall_disocunt, '%')!=false)?true:false;
	
	if($overall_disocunt>0){
		
		$overall_disocunt = (float)$overall_disocunt;
		$overall_disc = array();
		$order_item_types = array();
		$overall_disc_details = array();
		
		/*Order totals*/
		$detail_total = array();
		$resp_total = imw_query("SELECT SUM(`total_amount`+`total_amount_os`) AS 'allowed', SUM(`ins_amount`+`ins_amount_os`) AS 'ins' FROM `in_order_details` WHERE `order_id`='".$order_id."' AND `del_status`=0");
		if($resp_total && imw_num_rows($resp_total)){
			$resp_total = imw_fetch_object($resp_total);
			$detail_total['allowed'] = $resp_total->allowed;
			$detail_total['ins'] = $resp_total->ins;
		}
		
		/*CL disinfectant Total*/
		$cl_resp_total = imw_query("SELECT SUM(`total_amount`) AS 'allowed', SUM(`ins_amount`) AS 'ins' FROM `in_order_cl_detail` WHERE `order_id`='".$order_id."' AND `del_status`=0");
		if( $cl_resp_total && imw_num_rows($cl_resp_total) > 0 ){
			$cl_resp_total = imw_fetch_object($cl_resp_total);
			$detail_total['allowed'] = $detail_total['allowed'] + $cl_resp_total->allowed;
			$detail_total['ins'] = $detail_total['ins'] + $cl_resp_total->ins;
		}
		
		/*End Order Totals*/
		if( !$dicount_float_flag && ($overall_disocunt > ($detail_total['allowed'] - $detail_total['ins'])) ){
			$overall_disocunt = $detail_total['allowed'] - $detail_total['ins'];
			imw_query("UPDATE `in_order` SET `overall_discount`='".$overall_disocunt."', `total_overall_discount`='".$overall_disocunt."' WHERE `id`=".$order_id);
		}
		elseif($dicount_float_flag){
			$overall_disocunt = ($detail_total['allowed'] - $detail_total['ins']) * ( (float)$overall_disocunt / 100);
			$overall_disocunt = number_format($overall_disocunt, 2);
		}
		
		$items = imw_query("SELECT `id`, `module_type_id`, `total_amount`, `ins_amount`, `tax_rate` FROM `in_order_details` WHERE `order_id`='".$order_id."' AND `del_status`=0");
		while($row = imw_fetch_object($items)){
			
			array_push($order_item_types, $row->module_type_id);
			
			/*Update lens details if item is Lens*/
			if($row->module_type_id==2){
				$lens_items = imw_query("SELECT `id`, `total_amt`, `ins_amount`, `tax_rate` FROM `in_order_lens_price_detail` WHERE `order_id`='".$order_id."' AND `order_detail_id`='".$row->id."' AND `del_status`=0");
				while($lrow = imw_fetch_object($lens_items)){
					
					/*Share of discount % for line Item from Overall Discount*/
					$l_disc_p = (($lrow->total_amt-$lrow->ins_amount)/($detail_total['allowed']-$detail_total['ins']))*100;
					$l_disc = ($overall_disocunt*$l_disc_p)/100;
					$l_disc = number_format($l_disc, 2);
					array_push($overall_disc, $l_disc);
					
					$temp_details = array();
					$temp_details['mod']= $row->module_type_id;
					$temp_details['id']	= $lrow->id;
					array_push($overall_disc_details, $temp_details);
					
					/*Tax %applied to line Item*/
					$l_tax_p = $lrow->tax_rate;
					
					/*Actual cost of line Item after discount*/
					$l_amt = $lrow->total_amt-$l_disc;
					$l_tax_v = ($l_amt*$l_tax_p)/100;
					
					$l_tax_v = number_format((float)$l_tax_v, 2);
					
					imw_query("UPDATE `in_order_lens_price_detail` SET `tax_paid`='".$l_tax_v."', `overall_discount`='".$l_disc."' WHERE `id`=".$lrow->id."");
				}
			}
			elseif($row->module_type_id==3){
				$cl_items = imw_query("SELECT `id`, `total_amount`, `ins_amount`, `tax_rate` FROM `in_order_cl_detail` WHERE `order_id`='".$order_id."' AND `order_detail_id`='".$row->id."' AND `del_status`=0");
				while($clrow = imw_fetch_object($cl_items)){
					
					/*Share of discount % for line Iem from Overall Discount*/
					$cl_disc_p = (($clrow->total_amount-$clrow->ins_amount)/($detail_total['allowed']-$detail_total['ins']))*100;
					$cl_disc = ($overall_disocunt*$cl_disc_p)/100;
					$cl_disc = number_format($cl_disc, 2);
					array_push($overall_disc, $cl_disc);
					
					$temp_details = array();
					$temp_details['mod']= $row->module_type_id;
					$temp_details['id']	= $clrow->id;
					$temp_details['tbl']= 'cldt';
					array_push($overall_disc_details, $temp_details);
					
					/*Tax %applied to line Item*/
					$cl_tax_p = $clrow->tax_rate;
					
					/*Actual cost of line Item after discount*/
					$cl_amt = $clrow->total_amount-$cl_disc;
					$cl_tax_v = ($cl_amt*$cl_tax_p)/100;
					
					$cl_tax_v = number_format((float)$cl_tax_v, 2);
					
					imw_query("UPDATE `in_order_cl_detail` SET `tax_paid`='".$cl_tax_v."', `overall_discount`='".$cl_disc."' WHERE `id`=".$clrow->id."");
				}
				
				/*Contact Lens OS Values*/
				$cl_os = "SELECT `id`, `total_amount_os`, `ins_amount_os`, `tax_rate_os` FROM `in_order_details` WHERE `order_id`='".$order_id."' AND `del_status`=0 AND `item_id_os`!=0 AND `module_type_id`=3";
				$cl_os = imw_query($cl_os);
				if($cl_os && imw_num_rows($cl_os)>0){
					
					while($cl_os_row = imw_fetch_object($cl_os)){
						/*Share of discount % for line Iem from Overall Discount*/
						$item_disc_p = (($cl_os_row->total_amount_os-$cl_os_row->ins_amount_os)/($detail_total['allowed']-$detail_total['ins']))*100;
						$item_disc = ($overall_disocunt*$item_disc_p)/100;
						$item_disc = number_format($item_disc, 2);
						array_push($overall_disc, $item_disc);
						
						$temp_details = array();
						$temp_details['mod']= $row->module_type_id;
						$temp_details['id']	= $cl_os_row->id;
						$temp_details['vis']= 'os';
						array_push($overall_disc_details, $temp_details);
						
						/*Tax %applied to line Item*/
						$item_tax_p = $cl_os_row->tax_rate_os;
						
						/*Actual cost of line Item after discount*/
						$item_amt = $cl_os_row->total_amount_os-$item_disc;
						$item_tax_v = ($item_amt*$item_tax_p)/100;
						$item_tax_v = number_format((float)$item_tax_v, 2);
						
						imw_query("UPDATE `in_order_details` SET `tax_paid_os`='".$item_tax_v."', `overall_discount_os`='".$item_disc."' WHERE `id`=".$cl_os_row->id."");
					}
				}
				/*End Contact Lens OS Values*/
			}
			
			if($row->module_type_id!=2){
				
				/*Share of discount % for line Iem from Overall Discount*/
				$item_disc_p = (($row->total_amount-$row->ins_amount)/($detail_total['allowed']-$detail_total['ins']))*100;
				$item_disc = ($overall_disocunt*$item_disc_p)/100;
				$item_disc = number_format($item_disc, 2);
				array_push($overall_disc, $item_disc);
				
				$temp_details = array();
				$temp_details['mod']= $row->module_type_id;
				$temp_details['id']	= $row->id;
				array_push($overall_disc_details, $temp_details);
				
				/*Tax %applied to line Item*/
				$item_tax_p = $row->tax_rate;
				
				/*Actual cost of line Item after discount*/
				$item_amt = $row->total_amount-$item_disc;
				$item_tax_v = ($item_amt*$item_tax_p)/100;
				
				$item_tax_v = number_format((float)$item_tax_v, 2);
				
				imw_query("UPDATE `in_order_details` SET `tax_paid`='".$item_tax_v."', `overall_discount`='".$item_disc."' WHERE `id`=".$row->id."");
			}
		}
		
		/*Update Order's Overall Discount*/
		if($dicount_float_flag){
			$overall_disc = array_sum($overall_disc);
			$overall_disc = number_format($overall_disc, 2);
			imw_query("UPDATE `in_order` SET `total_overall_discount`='".$overall_disc."' WHERE `id`='".$order_id."'");
		}
		
		/*Fix for difference in overall discount*/
		$order_item_types = array_unique($order_item_types);
		if( count($order_item_types) > 0 ){
			
			/*Total Overall Discount*/
			$calcOADisc = (float)array_sum($overall_disc);
			
			$saved_oa_disc = 0.00;
			$sql_total_oa_disc = 'SELECT `total_overall_discount` FROM `in_order` WHERE `id`='.$order_id;
			$sql_total_oa_disc = imw_query($sql_total_oa_disc);
			if( $sql_total_oa_disc ){
				$saved_oa_disc = imw_fetch_assoc($sql_total_oa_disc);
				$saved_oa_disc = (float)$saved_oa_disc['total_overall_discount'];
			}
			
			if( $calcOADisc != $saved_oa_disc ){
				
				//print_r($order_item_types);
				$max_disc_key = array_keys($overall_disc, max($overall_disc));
				$max_disc_key = $max_disc_key[0];
				
				/*Difference in Discount Total*/
				$disc_diff = $calcOADisc - $saved_oa_disc;
				
				$overall_disc[$max_disc_key] = ($overall_disc[$max_disc_key]) - ($disc_diff);
				
				/*Record to be updated in database for adjustment*/
				$detail_data = $overall_disc_details[$max_disc_key];
				
				$sql = '';
				if( $detail_data['mod'] == 2 ){
					$sql = 'UPDATE `in_order_lens_price_detail` SET `overall_discount`='.((float)$overall_disc[$max_disc_key]).' WHERE `id`='.$detail_data['id'];
				}
				elseif( $detail_data['mod'] == 3 && isset($detail_data['tbl']) && $detail_data['tbl'] == 'cldt' ){
					$sql = 'UPDATE `in_order_cl_detail` SET `overall_discount`='.((float)$overall_disc[$max_disc_key]).' WHERE `id`='.$detail_data['id'];
				}
				elseif( $detail_data['mod'] == 3 && isset($detail_data['vis']) && $detail_data['vis'] == 'os' ){
					$sql = 'UPDATE `in_order_details` SET `overall_discount_os`='.((float)$overall_disc[$max_disc_key]).' WHERE `id`='.$detail_data['id'];
				}
				else{
					$sql = 'UPDATE `in_order_details` SET `overall_discount`='.((float)$overall_disc[$max_disc_key]).' WHERE `id`='.$detail_data['id'];
				}
				
				if( $sql != ''  ){
					imw_query($sql);
				}
			}
		}
		/*End fix for difference in overall disocunt*/
	}
	/*End Fix for Overall Discount Case*/
	
	
	/*Update Tax*/
	$tax_paid = array();
	$total_amt = array();
	if($order_type==1 || $order_type==2){
		/*Total Tax for Lenses*/
		$sql1 = "SELECT 
					SUM(`tax_paid`) AS 'total_tax',
					SUM(`total_amt`) AS 'total_amt'
				FROM
					`in_order_lens_price_detail`
				WHERE
					`tax_applied`=1
					AND `del_status`=0
					AND `order_id`='".$order_id."'";
		$resp1 = imw_query($sql1);
		if($resp1 && imw_num_rows($resp1)>0){
			$resp1 = imw_fetch_assoc($resp1);
			array_push($tax_paid, $resp1['total_tax']);
			array_push($total_amt, $resp1['total_amt']);
		}
	}
	if($order_type==3){
		/*Total Tax for Lenses*/
		$sql1 = "SELECT 
					SUM(`tax_paid`) AS 'total_tax',
					SUM(`total_amount`) AS 'total_amt'
				FROM
					`in_order_cl_detail`
				WHERE
					`tax_applied`=1
					AND `del_status`=0
					AND `order_id`='".$order_id."'";
		$resp1 = imw_query($sql1);
		if($resp1 && imw_num_rows($resp1)>0){
			$resp1 = imw_fetch_assoc($resp1);
			array_push($tax_paid, $resp1['total_tax']);
			array_push($total_amt, $resp1['total_amt']);
		}
		/*Contact Lens OS Values*/
		$sql1 = "SELECT 
					SUM(`tax_paid_os`) AS 'total_tax',
					SUM(`total_amount_os`) AS 'total_amt'
				FROM
					`in_order_details`
				WHERE
					`tax_applied_os`=1
					AND `del_status`=0
					AND `item_id_os`!=0
					AND `order_id`='".$order_id."'";
		
		$resp1 = imw_query($sql1);
		if($resp1 && imw_num_rows($resp1)>0){
			$resp1 = imw_fetch_assoc($resp1);
			array_push($tax_paid, $resp1['total_tax']);
			array_push($total_amt, $resp1['total_amt']);
		}
		/*End Contact Lens OS Values*/
	}
	
	/*Total tax for Frames in the Order*/
	$sql2 = "SELECT
				SUM(`tax_paid`) AS 'total_tax',
				SUM(`total_amount`) AS 'total_amt'
			FROM
				`in_order_details`
			WHERE
				`tax_applied`=1
				AND `del_status`=0
				AND `module_type_id`!=2
				AND `order_id`='".$order_id."'";
	$resp2 = imw_query($sql2);
	if($resp2 && imw_num_rows($resp2)>0){
		$resp2 = imw_fetch_assoc($resp2);
		array_push($tax_paid, $resp2['total_tax']);
		array_push($total_amt, $resp2['total_amt']);
	}
	
	/*Update Order*/
	if(count($tax_paid)>0){
		$tax_paid = array_sum($tax_paid);
		$total_amt = array_sum($total_amt);
		$grand_total = $total_amt + $tax_paid;
		$sql6 = "UPDATE
					`in_order`
				SET 
					`tax_payable`='".$tax_paid."',
					`tax_pt_paid` = CASE WHEN `tax_pt_paid`>= ".$tax_paid." THEN ".$tax_paid." ELSE `tax_pt_paid` END,
					`tax_pt_resp` = '`tax_payable` - ".($tax_paid)."',
					`total_price`='".$total_amt."',
					`grand_total`='".$grand_total."'
				WHERE
					`id`='".$order_id."'";
		imw_query($sql6);
	}
}

/*
 * Function: cancel_item
 * Purpose: Cacel an ordered Item along with all it's attributes and linked items.
 * Coded in PHP7
 * Agguments:1) order_details_id, 2) order_id
 * Values:	1)	id from in_order_details
 * 			2)	Id of the order
 */
function cancel_item($order_detail_id, $order_id){
	
	$patient_id=$_SESSION['patient_session_id'];
	$operator_id=$_SESSION['authId'];
	$date=date('Y-m-d');
	$time=date('H:i:s');
	
	/*Confirmation of Item Type and Order Id*/
	$sql = "SELECT `module_type_id`, `order_id`
			FROM 
				`in_order_details`
			WHERE
				`id`='".$order_detail_id."'
				AND `patient_id`='".$patient_id."'";
	$resp = imw_query($sql);
	$resp = ($resp)?imw_fetch_assoc($resp):0;
	
	if(!$resp || ($resp && $resp['order_id']!=$order_id)){
		return false;
	}
	$module_type_id = $resp['module_type_id'];
	
	
	/*If Item to be deleted is frame*/
	if($module_type_id==1){
		/*Get Lens Id associated with the Frame*/
		$sqlId = "SELECT `id`
				  FROM `in_order_details`
				  WHERE
				  	`lens_frame_id`='".$order_detail_id."'
					AND `order_id`='".$order_id."'
					AND `patient_id`='".$patient_id."'";
		$lensId = false;
		$sqlId = imw_query($sqlId);
		if($sqlId && imw_num_rows($sqlId)){
			$sqlId = imw_fetch_assoc($sqlId);
			$lensId = $sqlId['id'];
		}
		
		/*Cancel Lens attributes and Rx for the Lens if not custom*/
		if($lensId){cancel_lens_attributes($lensId, $order_id);}
		
		/*Mark Lens as Deleted*/
		$sql3 = "UPDATE `in_order_details`
				SET
					`del_status`='1',
					`del_date`='".$date."',
					`del_time`='".$time."',
					`del_operator_id`='".$operator_id."'
				WHERE
					`lens_frame_id`='".$order_detail_id."'
					AND `order_id`='".$order_id."'
					AND `patient_id`='".$patient_id."'";
		imw_query($sql3);
	}
	/*If Item to be deleted is Lens*/
	elseif($module_type_id==2){
		
		/*Cancel Lens attributes and Rx for the Lens if not custom*/
		cancel_lens_attributes($order_detail_id, $order_id);
	}
	/*If Item to be deleted is Contact lens*/
	if($module_type_id==3){
		
		/*Cancel / Delete Rx. associated with the Contact Lens*/
		$sql2 = "UPDATE `in_cl_prescriptions`
				SET
					`del_status`='1',
					`del_date`='".$date."',
					`del_time`='".$time."',
					`del_operator_id`='".$operator_id."'
				WHERE
					`det_order_id`='".$order_detail_id."'
					AND `order_id`='".$order_id."'
					AND `patient_id`='".$patient_id."'";
		imw_query($sql2);
		
		/*Cancel Disindectant associated with the Contact Lens*/
		$sql3 = "UPDATE `in_order_cl_detail`
				SET
					`del_status`='1',
					`del_date`='".$date."',
					`del_time`='".$time."',
					`del_operator_id`='".$operator_id."'
				WHERE
					`order_detail_id`='".$order_detail_id."'
					AND `order_id`='".$order_id."'
					AND `item_type`='DI'";
		imw_query($sql3);
				
	}
	
	/*Mark Item as Deleted*/
	$sql4 = "UPDATE `in_order_details`
			SET
				`del_status`='1',
				`del_date`='".$date."',
				`del_time`='".$time."',
				`del_operator_id`='".$operator_id."'
			WHERE
				`id`='".$order_detail_id."'
				AND `order_id`='".$order_id."'
				AND `patient_id`='".$patient_id."'";
	imw_query($sql4);
	
	//update qty and price in in_order table
	update_qty_price_order($order_id);
	
	/*Update Tax Calculated for the Order*/
	update_tax($module_type_id, $order_id);
}

/*
 * Function: cancel_lens_attributes
 * Purpose: Mark as deleted / Cancel Lens attributes and Rx for the lens
 * Coded in PHP7
 * Agguments:1) lensId, 2) order_id
 * Values:	1)	Id for the Lens ordered from in_order_details 
 * 			2)	Id of the order
 */
function cancel_lens_attributes($lensId, $order_id){
	
	if(!$lensId || $lensId=="" || !$order_id || $order_id==""){return false;}
	
	$patient_id=$_SESSION['patient_session_id'];
	$operator_id=$_SESSION['authId'];
	$date=date('Y-m-d');
	$time=date('H:i:s');
	
	/*Delete Lense Attributes*/
	$sql1 = "UPDATE `in_order_lens_price_detail`
			SET 
				`del_status`='1',
				`del_date`='".$date."',
				`del_time`='".$time."',
				`del_operator_id`='".$operator_id."'
			WHERE
				`order_detail_id`='".$lensId."'
				AND `order_id`='".$order_id."'
				AND `patient_id`='".$patient_id."'";
	imw_query($sql1);
	
	/*Mark Rx. as deleted if not Custom*/
	$sql2 = "UPDATE `in_optical_order_form`
			SET
				`del_status`='1',
				`del_date`='".$date."',
				`del_time`='".$time."',
				`del_operator_id`='".$operator_id."'
			WHERE
				`det_order_id`='".$lensId."'
				AND `custom_rx`='0'
				AND `order_id`='".$order_id."'
				AND `patient_id`='".$patient_id."'";
	imw_query($sql2);
}

/*
 * Function: get_total_order_discount
 * Purpose: Get total discount applied on a order by order_id
 * Coded in PHP7
 * Agguments:1) order_id
 * Values:	1)	Order Id from in_order table
 */
function get_total_order_discount($order_id){
	$order_id = (int)$order_id;
	$discount = array();
	if($order_id!=""){
		/*Get Overall discount*/
		$sql0 = "SELECT `total_overall_discount` FROM `in_order` WHERE `id`='".$order_id."'";
		$resp0 = imw_query($sql0);
		if($resp0 && imw_num_rows($resp0)>0){
			$data0 = imw_fetch_assoc($resp0);
			if($data0['total_overall_discount']!="" && $data0['total_overall_discount']>0){
				array_push($discount, $data0['total_overall_discount']);
			}
		}
		/*Get Item Details*/
		$sql = "SELECT `id`, `item_id`, `module_type_id`, `price`, `price_os`, `qty`, `qty_right`, `discount`, `discount_os`, `discount_val`
				FROM
					`in_order_details`
				WHERE
					`order_id`='".$order_id."'
					AND `del_status`='0'";
		$resp = imw_query($sql);
		if($resp && imw_num_rows($resp)>0){
			
			while($details = imw_fetch_object($resp)){
				
				if($details->module_type_id==3){
					$allowed	= $details->price * $details->qty_right;
					$allowed_os	= $details->price_os * $details->qty;
				}
				else{
					$allowed=$details->price*($details->qty + $details->qty_right);
				}
				
				$exp_discount="";
				$exp_discount=explode('%',$details->discount);
				$final_discount= 0;
				
				/*Total Discount applied for Lens*/
				if($details->module_type_id==2){
					$final_discount = $details->discount_val;
				}
				else{
					/*Discount applied for Items except lens and contact lens*/
					if(count($exp_discount)>1){
						$final_discount=($allowed*$exp_discount[0])/100;
					}else{
						$final_discount=$exp_discount[0];
					}
					$final_discount = ($final_discount=="")?0:$final_discount;
				}
				array_push($discount, $final_discount);
				
				/*Discount for Contact Lens Disinfectant*/
				if($details->module_type_id==3){
					
					/*OS item discount for CL*/
					$exp_discount=explode('%',$details->discount_os);
					$final_discount_os = 0;
					if(count($exp_discount)>1){
						$final_discount_os	= ( $allowed_os * $exp_discount[0] ) / 100;
					}else{
						$final_discount_os	= $exp_discount[0];
					}
					$final_discount_os	= ( $final_discount_os == '' ) ? 0 : $final_discount_os;
					array_push($discount, $final_discount_os);
					
					$sql2 = "SELECT `id`, `price`, `qty`, `discount`
							FROM
								`in_order_cl_detail`
							WHERE
								`order_detail_id`='".$details->id."'
								AND `order_id`='".$order_id."'
								AND `del_status`='0'
								AND `item_type`='DI'
							ORDER BY 
								`id`
							DESC LIMIT 1";
					$resp2 = imw_query($sql2);
					if($resp2 && imw_num_rows($resp2)>0){
						$di_details = imw_fetch_object($resp2);
						
						$di_allowed = $di_details->price*$di_details->qty;
						$di_exp_discount="";
						$di_exp_discount=explode('%',$di_details->discount);
						$di_final_discount=0;
						if(count($di_exp_discount)>1){
							$di_final_discount=($di_allowed*$di_exp_discount[0])/100;
						}else{
							$di_final_discount=$di_exp_discount[0];
						}
						$di_final_discount = ($di_final_discount=="")?0:$di_final_discount;
						array_push($discount, $di_final_discount);
					}
				}
			}
		}
	}
	$discount = array_sum($discount);
	return($discount);
}

function send_to_visionweb($order_id,$order_detail_ids,$patient_id){
	
	$vw_qry=imw_query("select * from in_vision_web where vw_loc_id>0");
	while($vw_row=imw_fetch_array($vw_qry)){
		$username_arr[$vw_row['vw_loc_id']]  = $vw_row['vw_user'];
		$password_arr[$vw_row['vw_loc_id']]  = $vw_row['vw_pass'];
	}
	
	$type_qry = imw_query("SELECT `id`, `vw_code` FROM `in_lens_type` WHERE `del_status`='0' and vw_code!=''");
	while($type_rows = imw_fetch_array($type_qry))
	{
		$lens_type_arr[$type_rows['id']]=$type_rows['vw_code'];
	}
	
	$pat_qry=imw_query("select fname,lname,mname from patient_data where id='$patient_id'");
	$pat_row=imw_fetch_array($pat_qry);
	$pat_fname  = $pat_row['fname'];
	$pat_lname  = $pat_row['lname'];
	$pat_mname  = $pat_row['mname'];
	$pat_init 	= substr($pat_fname,0,1).substr($pat_mname,0,1).substr($pat_lname,0,1);
	
	$prism_direction_arr['BI']="In";
	$prism_direction_arr['BO']="Out";
	$prism_direction_arr['BD']="Down";
	$prism_direction_arr['BU']="Up";
	
	$order_detail_ids_str=str_replace(",","','",$order_detail_ids);
	$order_detail_ids_hyp=str_replace(",","-",$order_detail_ids);
	$p=0;
	$ord_det=imw_query("select id,lab_id,module_type_id,temple,a,b,dbl,ed,item_id,color_id,manufacturer_id,he_coeff,pof_check,
	st_coeff,nhp_cape,progression_Len,wrap_angle,panto_angle,rv_distance,lv_distance,re_rotation,le_rotation,reading_distance,
	lens_other,upc_code,order_index,lab_detail_id,lab_ship_detail_id,qty,loc_id,type_id,lens_vision,style_id,
	material_id_od,a_r_id_od,design_id_od,seg_type_od,material_id_os,a_r_id_os,design_id_os,seg_type_os,trace_file, job_type
	from in_order_details where order_id='$order_id' and id in('$order_detail_ids_str') and del_status='0' 
	and module_type_id in(1,2) and vw_order_id='' order by lab_id desc");
	while($ord_row=imw_fetch_array($ord_det)){
		$lab_id=$ord_row['lab_id'];
		$det_order_id=$ord_row['id'];
		$module_type_id=$ord_row['module_type_id'];
		$item_id=$ord_row['item_id'];
		$color_id=$ord_row['color_id'];
		$manufacturer_id=$ord_row['manufacturer_id'];
		$qty=$ord_row['qty'];
		$pos_order_id=$order_id.'-'.$ord_row['order_index'];
		$return_arr['item_id'][$det_order_id]=$item_id;
		$return_arr['qty'][$det_order_id]=$qty;
		$type_id=$ord_row['type_id'];
		$style_id=$ord_row['style_id'];
		$lens_vision=$ord_row['lens_vision'];
		$material_id_od=$ord_row['material_id_od'];
		$a_r_id_od=$ord_row['a_r_id_od'];
		$design_id_od=$ord_row['design_id_od'];
		$seg_type_od=$ord_row['seg_type_od'];
		$material_id_os=$ord_row['material_id_os'];
		$a_r_id_os=$ord_row['a_r_id_os'];
		$design_id_os=$ord_row['design_id_os'];
		$seg_type_os=$ord_row['seg_type_os'];
		if($lens_vision=="ou"){
			$eye_side="B";
		}
		if($lens_vision=="od"){
			$eye_side="R";
		}
		if($lens_vision=="os"){
			$eye_side="L";
		}
		if($p==0){
			
			$labs_sql = "SELECT id,lab_name,vw_lab_id FROM in_lens_lab WHERE id='$lab_id' limit 0,1";
			$labs = imw_query($labs_sql);
			$lab_row = imw_fetch_array($labs);
			$vw_lab_id=$lab_row['vw_lab_id'];
			$lab_detail_id=$ord_row['lab_detail_id'];
			$lab_shipping_id=$ord_row['lab_ship_detail_id'];

			$lab_det_qry=imw_query("select vw_billing_number,vw_shipping_number from in_lens_lab_detail where lab_id='$lab_id' and (id='$lab_detail_id' or id='$lab_shipping_id')");
			while($lab_det_row=imw_fetch_array($lab_det_qry)){
				if($lab_det_row['vw_billing_number']!="" && $billing_account==""){
					$billing_account=$lab_det_row['vw_billing_number'];
				}
				if($lab_det_row['vw_shipping_number']!="" && $shipping_account==""){
					$shipping_account=$lab_det_row['vw_shipping_number'];
				}
			}
			$create_order_xml = '<?xml version="1.0" encoding="UTF-8"?>';
			$create_order_xml .= "<VWOrder>";
			$create_order_xml .= "<Item><FieldName>OrderId</FieldName><FieldValue>".$pos_order_id."</FieldValue></Item>";
			$create_order_xml .= "<Item><FieldName>Username</FieldName><FieldValue>".$username_arr[$ord_row['loc_id']]."</FieldValue></Item>";
			$create_order_xml .= "<Item><FieldName>Password</FieldName><FieldValue>".$password_arr[$ord_row['loc_id']]."</FieldValue></Item>";
			$create_order_xml .= "<Item><FieldName>SupplierName</FieldName><FieldValue>".$vw_lab_id."</FieldValue></Item>";
			$create_order_xml .= "<Item><FieldName>BillAccount</FieldName><FieldValue>".$billing_account."</FieldValue></Item>";
			$create_order_xml .= "<Item><FieldName>ShipAccount</FieldName><FieldValue>".$shipping_account."</FieldValue></Item>";
			$create_order_xml .= "<Item><FieldName>Eyes</FieldName><FieldValue>".$eye_side."</FieldValue></Item>";
			$create_order_xml .= "<Item><FieldName>PONumber</FieldName><FieldValue></FieldValue></Item>";
			$create_order_xml .= "<Item><FieldName>PatLastName</FieldName><FieldValue>".$pat_lname."</FieldValue></Item>";
			$create_order_xml .= "<Item><FieldName>PatFirstName</FieldName><FieldValue>".$pat_fname."</FieldValue></Item>";
		}
	
		$ord_lens_det=imw_query("select * from in_optical_order_form where order_id='$order_id' and det_order_id='$det_order_id' and del_status='0'");
		while($lensRes=imw_fetch_array($ord_lens_det)){
			
			if($lens_type_arr[$type_id]=="SV"){
				$lensRes['add_od']=$lensRes['add_os']=$lensRes['seg_od']=$lensRes['seg_os']="";
			}
			
			if( strtoupper($lensRes['sphere_od']) == 'PLANO' )
				$lensRes['sphere_od'] = '0.00';
			if( strtoupper($lensRes['sphere_os']) == 'PLANO' )
				$lensRes['sphere_os'] = '0.00';
			
			if($eye_side=="R" || $eye_side=="B"){
				$create_order_xml .= "<Item><FieldName>RESph</FieldName><FieldValue>".$lensRes['sphere_od']."</FieldValue></Item>";
				$create_order_xml .= "<Item><FieldName>RECyl</FieldName><FieldValue>".$lensRes['cyl_od']."</FieldValue></Item>";
				$create_order_xml .= "<Item><FieldName>REAxis</FieldName><FieldValue>".$lensRes['axis_od']."</FieldValue></Item>";
				$create_order_xml .= "<Item><FieldName>REAdd</FieldName><FieldValue>".$lensRes['add_od']."</FieldValue></Item>";
				
				$create_order_xml .= "<Item><FieldName>REHorizPrismValue</FieldName><FieldValue>".$lensRes['mr_od_p']."</FieldValue></Item>";
				$create_order_xml .= "<Item><FieldName>REHorizPrismDirection</FieldName><FieldValue>".$prism_direction_arr[$lensRes['mr_od_prism']]."</FieldValue></Item>";
				$create_order_xml .= "<Item><FieldName>REVerticalPrismValue</FieldName><FieldValue>".$lensRes['mr_od_splash']."</FieldValue></Item>";
				$create_order_xml .= "<Item><FieldName>REVerticalPrismDirection</FieldName><FieldValue>".$prism_direction_arr[$lensRes['mr_od_sel']]."</FieldValue></Item>";
				
				$create_order_xml .= "<Item><FieldName>REDistPD</FieldName><FieldValue>".$lensRes['dist_pd_od']."</FieldValue></Item>";
				$create_order_xml .= "<Item><FieldName>RENearPD</FieldName><FieldValue>".$lensRes['near_pd_od']."</FieldValue></Item>";
				$create_order_xml .= "<Item><FieldName>RESegHeight</FieldName><FieldValue>".$lensRes['seg_od']."</FieldValue></Item>";
				$create_order_xml .= "<Item><FieldName>REOpticalCenter</FieldName><FieldValue>".$lensRes['oc_od']."</FieldValue></Item>";
				$create_order_xml .= "<Item><FieldName>REBaseCurve</FieldName><FieldValue>".$lensRes['base_od']."</FieldValue></Item>";
			}
			if($eye_side=="L" || $eye_side=="B"){
				$create_order_xml .= "<Item><FieldName>LESph</FieldName><FieldValue>".$lensRes['sphere_os']."</FieldValue></Item>";
				$create_order_xml .= "<Item><FieldName>LECyl</FieldName><FieldValue>".$lensRes['cyl_os']."</FieldValue></Item>";
				$create_order_xml .= "<Item><FieldName>LEAxis</FieldName><FieldValue>".$lensRes['axis_os']."</FieldValue></Item>";
				$create_order_xml .= "<Item><FieldName>LEAdd</FieldName><FieldValue>".$lensRes['add_os']."</FieldValue></Item>";
				
				$create_order_xml .= "<Item><FieldName>LEHorizPrismValue</FieldName><FieldValue>".$lensRes['mr_os_p']."</FieldValue></Item>";
				$create_order_xml .= "<Item><FieldName>LEHorizPrismDirection</FieldName><FieldValue>".$prism_direction_arr[$lensRes['mr_os_prism']]."</FieldValue></Item>";
				$create_order_xml .= "<Item><FieldName>LEVerticalPrismValue</FieldName><FieldValue>".$lensRes['mr_os_splash']."</FieldValue></Item>";
				$create_order_xml .= "<Item><FieldName>LEVerticalPrismDirection</FieldName><FieldValue>".$prism_direction_arr[$lensRes['mr_os_sel']]."</FieldValue></Item>";
				
				$create_order_xml .= "<Item><FieldName>LEDistPD</FieldName><FieldValue>".$lensRes['dist_pd_os']."</FieldValue></Item>";
				$create_order_xml .= "<Item><FieldName>LENearPD</FieldName><FieldValue>".$lensRes['near_pd_os']."</FieldValue></Item>";
				$create_order_xml .= "<Item><FieldName>LESegHeight</FieldName><FieldValue>".$lensRes['seg_os']."</FieldValue></Item>";
				$create_order_xml .= "<Item><FieldName>LEOpticalCenter</FieldName><FieldValue>".$lensRes['oc_os']."</FieldValue></Item>";
				$create_order_xml .= "<Item><FieldName>LEBaseCurve</FieldName><FieldValue>".$lensRes['base_os']."</FieldValue></Item>";
			}
		}
	
		if($p==0){
						
			$create_order_xml .= "<Item><FieldName>JobType</FieldName><FieldValue>".$ord_row['job_type']."</FieldValue></Item>";
			$create_order_xml .= "<Item><FieldName>SpecialInstructions1</FieldName><FieldValue>".$ord_row['lens_other']."</FieldValue></Item>";
			
			if($eye_side=="R" || $eye_side=="B"){
				
				$met_qry=imw_query("select vw_code from in_lens_material where id='$material_id_od'");
				$met_row=imw_fetch_array($met_qry);
				
				$dsn_qry=imw_query("select vw_code from in_lens_design where id='$design_id_od'");
				$dsn_row=imw_fetch_array($dsn_qry);
				
				$trt_arr=array();
				$a_r_id_str=str_replace(";","','",$a_r_id_od);
				$trt_qry=imw_query("select vw_code from in_lens_ar where id in('$a_r_id_str')");
				while($trt_row=imw_fetch_array($trt_qry)){
					$trt_arr[$trt_row['vw_code']]=$trt_row['vw_code'];
				}
				
				$create_order_xml .= "<Item><FieldName>RELensDesign</FieldName><FieldValue>".$dsn_row['vw_code']."</FieldValue></Item>";
				$create_order_xml .= "<Item><FieldName>RELensMaterial</FieldName><FieldValue>".$met_row['vw_code']."</FieldValue></Item>";
			
				$tf=0;
				foreach($trt_arr as $trt_val){
					$tf=$tf+1;
					$create_order_xml .= "<Item><FieldName>RETreatment".$tf."</FieldName><FieldValue>".$trt_val."</FieldValue></Item>";
				}
				
				$create_order_xml .= "<Item><FieldName>RETreatmentComments</FieldName><FieldValue></FieldValue></Item>";
			}
			if($eye_side=="L" || $eye_side=="B"){
				
				$met_qry=imw_query("select vw_code from in_lens_material where id='$material_id_os'");
				$met_row=imw_fetch_array($met_qry);
				
				$dsn_qry=imw_query("select vw_code from in_lens_design where id='$design_id_os'");
				$dsn_row=imw_fetch_array($dsn_qry);
				
				$trt_arr=array();
				$a_r_id_str=str_replace(";","','",$a_r_id_os);
				$trt_qry=imw_query("select vw_code from in_lens_ar where id in('$a_r_id_str')");
				while($trt_row=imw_fetch_array($trt_qry)){
					$trt_arr[$trt_row['vw_code']]=$trt_row['vw_code'];
				}
				
				$create_order_xml .= "<Item><FieldName>LELensDesign</FieldName><FieldValue>".$dsn_row['vw_code']."</FieldValue></Item>";
				$create_order_xml .= "<Item><FieldName>LELensMaterial</FieldName><FieldValue>".$met_row['vw_code']."</FieldValue></Item>";
				
				$tf=0;
				foreach($trt_arr as $trt_val){
					$tf=$tf+1;
					$create_order_xml .= "<Item><FieldName>LETreatment".$tf."</FieldName><FieldValue>".$trt_val."</FieldValue></Item>";
				}
				
				$create_order_xml .= "<Item><FieldName>LETreatmentComments</FieldName><FieldValue></FieldValue></Item>";
			
			}
			
		}
		if($module_type_id=="1"){
			/*$item_qry=imw_query("select type_id from in_item where id='$item_id'");
			$item_row=imw_fetch_array($item_qry);
			$type_id=$item_row['type_id'];*/
			
			$item_type_qry=imw_query("select type_name,vw_code from in_frame_types where id='$type_id'");
			$item_type_row=imw_fetch_array($item_type_qry);
			$f_type_name=$item_type_row['type_name'];
			$f_vw_code=$item_type_row['vw_code'];
			
			/*If Patient`s Frame*/
			if( $ord_row['pof_check'] == '1' ){
				$pof_data = imw_query("SELECT `manufacturer`, `style`, `color` FROM in_frame_pof WHERE order_detail_id='$det_order_id'");
				if( $pof_data && imw_num_rows($pof_data)>0){
					$pof_row = imw_fetch_assoc($pof_data);
					$color_name = $pof_row['color'];
					$manufacturer_name = $pof_row['manufacturer'];
					$style_name = $pof_row['style'];
				}
			}
			/*End if Patient`s Frame*/
			else{
				$col_sql = imw_query("SELECT color_name FROM in_frame_color WHERE id='$color_id'");
				$col_row = imw_fetch_array($col_sql);
				$color_name = $col_row['color_name'];
				
				$manu_sql = imw_query("SELECT manufacturer_name FROM in_manufacturer_details WHERE id='$manufacturer_id'");
				$manu_row = imw_fetch_array($manu_sql);
				$manufacturer_name = $manu_row['manufacturer_name'];
				
				$style_sql = imw_query("SELECT style_name FROM in_frame_styles WHERE id='$style_id'");
				$style_row = imw_fetch_array($style_sql);
				$style_name = $style_row['style_name'];
			}
			
			$create_order_xml .= "<Item><FieldName>FrameType</FieldName><FieldValue>".$f_vw_code."</FieldValue></Item>
								<Item><FieldName>FrameManufacturer</FieldName><FieldValue>".spec_char($manufacturer_name)."</FieldValue></Item>
								<Item><FieldName>FrameModel</FieldName><FieldValue>".$style_name."</FieldValue></Item>
								<Item><FieldName>FrameColor</FieldName><FieldValue>".htmlentities($color_name)."</FieldValue></Item>
								<Item><FieldName>FrameTempleLength</FieldName><FieldValue>".$ord_row['temple']."</FieldValue></Item>
								<Item><FieldName>FrameSKU</FieldName><FieldValue>".$ord_row['upc_code']."</FieldValue></Item>
								<Item><FieldName>ABox</FieldName><FieldValue>".$ord_row['a']."</FieldValue></Item>
								<Item><FieldName>BBox</FieldName><FieldValue>".$ord_row['b']."</FieldValue></Item>
								<Item><FieldName>Dbl</FieldName><FieldValue>".$ord_row['dbl']."</FieldValue></Item>
								<Item><FieldName>ED</FieldName><FieldValue>".$ord_row['ed']."</FieldValue></Item>";
								if($ord_row['trace_file']!=""){
									$trace_file_path="../../patient_interface/uploaddir/trace_file/".$ord_row['trace_file'];
									$fp = fopen($trace_file_path, "r");
									$file_size = filesize($trace_file_path);
									$trace_type = pathinfo ($trace_file_path,PATHINFO_EXTENSION);
									$trace_data = fread($fp,$file_size);
									fclose($fp);
									/*$create_order_xml .= "<Item><FieldName>TraceType</FieldName><FieldValue>".$trace_type."</FieldValue></Item>
														  <Item><FieldName>Trace</FieldName><FieldValue>".$trace_data."</FieldValue></Item>";*/
									  $create_order_xml .= "<Item><FieldName>Trace</FieldName><FieldValue><DATA Type='OMA' EncType='none'>".$trace_data."</DATA></FieldValue></Item>";
								}
		}
		if($module_type_id=="2"){
			$create_order_xml .= "<Item><FieldName>Patient_initials</FieldName><FieldValue>".$pat_init."</FieldValue></Item>
								<Item><FieldName>HE_coeff</FieldName><FieldValue>".$ord_row['he_coeff']."</FieldValue></Item>
								<Item><FieldName>ST_coeff</FieldName><FieldValue>".$ord_row['st_coeff']."</FieldValue></Item>
								<Item><FieldName>Progression_Length</FieldName><FieldValue>".$ord_row['progression_Len']."</FieldValue></Item>
								<Item><FieldName>WrapAngle</FieldName><FieldValue>".$ord_row['wrap_angle']."</FieldValue></Item>
								<Item><FieldName>PantoAngle</FieldName><FieldValue>".$ord_row['panto_angle']."</FieldValue></Item>
								<Item><FieldName>RightVertexDistance</FieldName><FieldValue>".$ord_row['rv_distance']."</FieldValue></Item>
								<Item><FieldName>LeftVertexDistance</FieldName><FieldValue>".$ord_row['lv_distance']."</FieldValue></Item>
								<Item><FieldName>ReadingDistance</FieldName><FieldValue>".$ord_row['reading_distance']."</FieldValue></Item>
								<Item><FieldName>RightERCD</FieldName><FieldValue>".$ord_row['re_rotation']."</FieldValue></Item>
								<Item><FieldName>LeftERCD</FieldName><FieldValue>".$ord_row['le_rotation']."</FieldValue></Item>
								<Item><FieldName>CAPE</FieldName><FieldValue>".$ord_row['nhp_cape']."</FieldValue></Item>";
		}
		$p++;
	}
	if($p>0){
		$create_order_xml .= "</VWOrder>";
	}
	$return_arr['create_order_xml']=$create_order_xml;
	return $return_arr;
}
function error_msg($msg){
	$msg_error_arr=explode('Parsing error:',$msg);
	//print_r($msg_error_arr);
	if(count($msg_error_arr)>1){
		foreach($msg_error_arr as $val){
			if(strstr($val,'cvc')){
				$val_arr=explode(':',$val);
				if($val_arr[1]!=""){
					$final_msg_arr[$val_arr[1]]=$val_arr[1];
				}
			}
		}
	}else{
		$msg=preg_replace("(\d\.)", "<br>", $msg);
		$msg_error_arr=explode('.',$msg);
		$final_msg_arr[]=implode('<br>',$msg_error_arr);
	}
	$final_msg_str=implode('<br>',$final_msg_arr);
	$final_msg_str=str_replace('"',"",$final_msg_str);
	return $final_msg_str;
}
function vw_lab_status($id){
	$status_arr=array("0"=>"Sent to Lab","2"=>"Sent to Supplier","5"=>"Received by the supplier","7"=>"Order In Process","15"=>"Waiting for frame","20"=>"Rx Launch",
	"25"=>"Surfacing","30"=>"Treatment","40"=>"Finishing","45"=>"Inspection","50"=>"Breakage-Redo","60"=>"Shipped");
	return $status_arr[$id];
}
function recalculate_stock($item_id)
{
	$loc_stock=0;
	$loc_st_qry=imw_query("select sum(stock) as loc_stock from in_item_loc_total where item_id='$item_id' group by item_id");
	$loc_st_row=imw_fetch_array($loc_st_qry);
	if($loc_st_row['loc_stock']>0){
		$loc_stock=$loc_st_row['loc_stock'];
	}
	
	$item_qry = imw_query("select retail_price,id,qty_on_hand from in_item where id='$item_id'");
	$fch_item_qry = imw_fetch_array($item_qry);
	$new_amt=0;
	if($loc_stock>0)
	{
		$new_amt = $fch_item_qry['retail_price']*$loc_stock;
	}
	$act_qty = imw_query("update in_item set qty_on_hand='$loc_stock',amount='$new_amt' where id='$item_id'");
}

function back_prac_id_catg($category, $prac_codes, $addnew=false){
	
	if($category =="" || $prac_codes=="")
		return false;
	
	$procedureIds = array();

	$rec_prac_code = rtrim($prac_codes, ";");
	$rec_prac_code = explode(';', $rec_prac_code);
	/*$rec_prac_code = str_replace(";", "','", $rec_prac_code);
	$rec_prac_code = "'".$rec_prac_code."'";*/
	
	foreach($rec_prac_code as $rec_prac){
	
		$sql_prac = 'SELECT
				`fee`.`cpt_fee_id`
				FROM `cpt_category_tbl` `cat` INNER JOIN `cpt_fee_tbl` `fee` ON(`cat`.`cpt_cat_id` = `fee`.`cpt_cat_id`)
				WHERE `cat`.`cpt_category` LIKE \'%'.$category.'%\' AND
					  `fee`.`cpt_prac_code`=\''.$rec_prac.'\' AND
					  `fee`.`status` = \'active\' AND
					  `fee`.`delete_status` = 0';
		$prac_resp = imw_query($sql_prac);
		if($prac_resp){
			if( imw_num_rows($prac_resp)>0 ){
				$prac_resp = imw_fetch_object($prac_resp);
				array_push($procedureIds, $prac_resp->cpt_fee_id);
			}
			else{
				
			/*Add New Prac Code*/
				if($addnew){
					$sql_cpt_category = 'SELECT `cpt_cat_id` AS \'cat_id\' FROM `cpt_category_tbl` WHERE LOWER(`cpt_category`)=\''.$category.'\'';
					$cpt_category_id = false;
					$cpt_category_resp = imw_query( $sql_cpt_category );
					
					if( $cpt_category_resp && imw_num_rows($cpt_category_resp) > 0 )
						$cpt_category_id = imw_fetch_object( $cpt_category_resp );
					
					/*Add new Prac Code*/
					if( $cpt_category_id ){
						$sql_add_cpt_code = 'INSERT INTO `cpt_fee_tbl`(`cpt_cat_id`, `cpt4_code`, `cpt_prac_code`, `cpt_desc`, `units`, `commonlyUsed`, `status`) VALUES ('.$cpt_category_id->cat_id.', \''.$rec_prac.'\', \''.$rec_prac.'\', \''.$rec_prac.'\', 1, 1, \'Active\')';
						if( imw_query( $sql_add_cpt_code ) ){
							$procedureId = imw_insert_id();
							array_push($procedureIds, $procedureId); 
							
							/*CPT Fee*/
							$cpt_fee_sql= 'INSERT INTO `cpt_fee_table`(`cpt_fee_id`, `fee_table_column_id`, `cpt_fee`) VALUES';
							$fee_values	= '';
							$resp_fee_table_columns = imw_query('SELECT `fee_table_column_id` FROM `fee_table_column`');
							while( $fee_tablc_column = imw_fetch_object($resp_fee_table_columns) ){
								$fee_values .= '('.$procedureId .', '.$fee_tablc_column->fee_table_column_id.', \'0.00\'),';
							}
							
							if( $fee_values!='' ){
								$fee_values = rtrim($fee_values, ',');
								$cpt_fee_sql = $cpt_fee_sql.$fee_values;
								imw_query($cpt_fee_sql);
							}
						}
					}
				}
			/*End Add New Prac Code*/
			}
		}
		imw_free_result($prac_resp);
	}
	

	return($procedureIds);
}
function spec_char($string){
	$regex = '/[^\w\d\-\'\"\.\&\/\$]+/';
	$response = preg_replace($regex, "", html_entity_decode($string));
	return $response;
}
function post_charges($ord_id,$ord_det_id=0){
	
	$opr_id = $_SESSION['authId'];
	$entered_date=date('Y-m-d');
	$entered_time=date('H:i:s');
	$entered_date_time = date("Y-m-d H:i:s");
	$pro_fac_id = $_SESSION['pro_fac_id'];
	
	//get pos_facilityies_tbl id linked with location table
	$loc_qry=imw_query("select tax_label,pos,fac_group from in_location where id='$pro_fac_id' limit 0,1");
	$loc_row=imw_fetch_array($loc_qry);
	$fac_group=$loc_row['fac_group'];
	$idoc_facility=$loc_row['pos'];
	$tax_label=$loc_row['tax_label'];
	
	//get pos_tbl id linked with pos_facilityies_tbl 
	$loc_qry2=imw_query("select pos_id from pos_facilityies_tbl where pos_facility_id='$idoc_facility' limit 0,1");
	$loc_row2=imw_fetch_array($loc_qry2);
	$idoc_pos_id=$loc_row2['pos_id'];
	
	//get facility id linked to pos_tbl
	$loc_qry2=imw_query("select facility from pos_tbl where pos_id='$idoc_pos_id' limit 0,1");
	$loc_row2=imw_fetch_array($loc_qry2);
	//this variable is being overwrite below
	$billing_facility_id=$loc_row2['facility'];
	
	//overwrite linked facility id with newly configured linking function
	$qry_fac=imw_query("select idoc_fac_id from in_location where id='$pro_fac_id'");
	$row_fac = imw_fetch_array($qry_fac);
	if($row_fac['idoc_fac_id']>0)$billing_facility_id=$row_fac['idoc_fac_id'];
		
	if(!$billing_facility_id){
		$loc_fac_qry2=imw_query("select id from facility where facility_type='1' limit 0,1");
		$loc_fac_row2=imw_fetch_array($loc_fac_qry2);
		$billing_facility_id=$loc_fac_row2['id'];//idoc_fac_id variable name changed to billing_facility_id
	}
	
	$groups_arr=array();
	$selQry = "select gro_id,group_institution,group_anesthesia from groups_new where gro_id='$fac_group'";
	$res = imw_query($selQry);
	$grp_detail = imw_fetch_array($res);
	
	$mod_qry=imw_query("select modifiers_id,modifier_code,mod_prac_code from modifiers_tbl where (mod_prac_code in('lt','rt') or modifier_code in('lt','rt')) and delete_status='0'");
	while($mod_row=imw_fetch_array($mod_qry)){
		$mod_arr[strtolower($mod_row['mod_prac_code'])]=$mod_row['modifiers_id'];
		$mod_arr[strtolower($mod_row['modifier_code'])]=$mod_row['modifiers_id'];
	}
	
	$sel_ord=imw_query("select * from in_order where id='$ord_id' and del_status='0'");
	$row_ord=imw_fetch_array($sel_ord);
	$order_id=$row_ord['id'];
	$order_enc_id=$row_ord['order_enc_id'];
	$order_chl_id=$row_ord['order_chl_id'];
	$patient_id=$row_ord['patient_id'];
	$order_entered_date=$row_ord['entered_date'];
	$payment_mode=$row_ord['payment_mode'];
	$checkNo=$row_ord['checkNo'];
	$creditCardNo=$row_ord['creditCardNo'];
	$creditCardCo=$row_ord['creditCardCo'];
	$expirationDate=$row_ord['expirationDate'];
	$tax_chld=$row_ord['tax_chld'];
	$tax_prac_code=$row_ord['tax_prac_code'];
	$tax_payable=$row_ord['tax_payable'];
	$tax_pt_paid=$row_ord['tax_pt_paid'];
	$cn_prov_id=$cn_del_status="";
	$overall_discount_code=$row_ord['overall_discount_code'];
	if($row_ord['order_enc_id']==0){
		
		//----------------Provider Id from the Rx of the order-----------------------------//
		$sqlRxPhy = 'SELECT `physician_id` FROM `in_optical_order_form` WHERE `patient_id`='.((int)$patient_id).' AND `order_id`='.((int)$order_id).' AND `del_status`=0 ORDER BY `id` ASC LIMIT 1';
		$sqlRxPhy = imw_query($sqlRxPhy);
		if($sqlRxPhy && imw_num_rows($sqlRxPhy)>0){
			$row = imw_fetch_assoc($sqlRxPhy);
			$cn_prov_id = $row['physician_id'];
		}
		else{
			$sqlRxPhy = 'SELECT `physician_id` FROM `in_cl_prescriptions` WHERE `patient_id`='.((int)$patient_id).' AND `order_id`='.((int)$order_id).' AND `del_status`=0 ORDER BY `id` ASC LIMIT 1';
			$sqlRxPhy = imw_query($sqlRxPhy);
			if($sqlRxPhy && imw_num_rows($sqlRxPhy)>0){
				$row = imw_fetch_assoc($sqlRxPhy);
				$cn_prov_id = $row['physician_id'];
			}
		}
		//-------------END Provider Id from the Rx of the order---------------------------//
		
		/*Get primary care physician Id for the patient if provider Id for the order id does not exists*/
		if( $cn_prov_id=='' || $cn_prov_id=='0' ){
			$sqlPrimaryPhy = 'SELECT `providerID` FROM `patient_data` WHERE `id`='.((int)$patient_id);
			$sqlPrimaryPhy = imw_query($sqlPrimaryPhy);
			if($sqlPrimaryPhy && imw_num_rows($sqlPrimaryPhy)>0){
				$row = imw_fetch_assoc($sqlPrimaryPhy);
				$cn_prov_id = $row['providerID'];
			}
		}
		/*End Get primary care physician Id*/
		
		$encounterIdText="";
		do{
			$encounterIdText = getEncounterId();
			$chl_qry=imw_query("select encounter_id from patient_charge_list where encounter_id='$encounterIdText'");
			$getMatchFound=imw_num_rows($chl_qry);
		}while($getMatchFound);
		$order_enc_id=$encounterIdText;
	
		imw_query("insert into patient_charge_list set encounter_id='$order_enc_id',patient_id='$patient_id',primaryProviderId='$cn_prov_id',
		primary_provider_id_for_reports='$cn_prov_id',date_of_service='$order_entered_date',opt_order_id='$order_id',entered_date='$entered_date',
		entered_time='$entered_time',operator_id='$opr_id',enc_icd10='1',facility_id='$idoc_facility',gro_id='$fac_group',billing_facility_id='$billing_facility_id'");
		$order_chl_id=imw_insert_id();
		
		imw_query("update in_order set order_enc_id='$order_enc_id',order_chl_id='$order_chl_id',modified_date='$entered_date',modified_time='$entered_time', modified_by='$opr_id' where id='$order_id'");
	}else{
		$sel_chl=imw_query("select primaryProviderId,del_status,reff_phy_id from patient_charge_list where encounter_id='".$row_ord['order_enc_id']."' and del_status='0'");
		$row_chl=imw_fetch_array($sel_chl);
		$cn_prov_id = $row_chl['primaryProviderId'];
		$cn_del_status = $row_chl['del_status'];
		$cn_reff_phy_id = $row_chl['reff_phy_id'];
	}
	if($cn_del_status>0){
		/*If encounter is deleted in iDOC then encounter will not update*/
	}else{
		$all_dx_arr_qry=array();
		$vision_arr=array("od","os");
		$sel_ord_det=imw_query("select * from in_order_details where order_id='$order_id' and del_status='0' order by id asc");
		while($row_ord_det=imw_fetch_array($sel_ord_det)){
			foreach($vision_arr as $vision_val){
				$vision_type="";
				$modifier_id="";
				if($vision_val=="os"){
					$vision_type="_os";
				}
				$order_det_id=$row_ord_det['id'];
				$item_comment=$row_ord_det['item_comment'];
				$module_type_id=$row_ord_det['module_type_id'];
				if($module_type_id=="3"){
					if($vision_val=="os"){
						$order_det_qty=$row_ord_det['qty'];
						$modifier_id=$mod_arr['lt'];
					}else{
						$order_det_qty=$row_ord_det['qty_right'];
						$modifier_id=$mod_arr['rt'];
					}
				}else{
					$order_det_qty=$row_ord_det['qty'];
				}
				$ins_case_id=$row_ord_det['ins_case_id'.$vision_type];
				
				$item_prac_code=$row_ord_det['item_prac_code'.$vision_type];
				$price=$row_ord_det['price'.$vision_type];
				$total_amount=$row_ord_det['total_amount'.$vision_type];
				$allowed=$row_ord_det['allowed'.$vision_type];
				$ins_amount=$row_ord_det['ins_amount'.$vision_type];
				$pt_resp=$row_ord_det['pt_resp'.$vision_type];
				$pt_paid=$row_ord_det['pt_paid'.$vision_type];
				
				if($ins_case_id>0){
					$ord_ins_case_id=$ins_case_id;
				}
				$proc_selfpay=1;
				if($ins_case_id>0){
					$proc_selfpay=0;
				}
				$all_dx_str_qry="";
				$all_dx_arr_qry=array();
				if($row_ord_det['dx_code']!="" && $row_ord_det['dx_code']!="0"){
					$dxx_exp = explode(',',$row_ord_det['dx_code']);
					for($gd=0;$gd<count($dxx_exp);$gd++){
						$dx_arr_sin = trim($dxx_exp[$gd]);
						$all_unq_dx_arr[$dx_arr_sin]=$dx_arr_sin;
					}
					
					$h=1;
					for($g=1;$g<=12;$g++){
						$all_dx_arr[$g]='';
					}
					foreach($all_unq_dx_arr as $dx_val){
						$all_dx_arr[$h]=$dx_val;
						$h++;
					}
					for($gd1=0;$gd1<count($dxx_exp);$gd1++){
						for($f=1;$f<=12;$f++){
							if($all_dx_arr[$f]==trim($dxx_exp[$gd1])){
								$all_dx_arr_qry[]="diagnosis_id".$f."='".trim($all_dx_arr[$f])."'";
							}
						}
					}
					$all_dx_str_qry=implode(',',$all_dx_arr_qry);
					if($all_dx_str_qry!=""){
						$all_dx_str_qry=",".$all_dx_str_qry;
					}
				}
				if($module_type_id=="2"){
					$lens_items_qry=imw_query("select * from in_order_lens_price_detail where order_id='$order_id' and order_detail_id='$order_det_id' and wholesale_price > 0 and del_status='0' and vision='$vision_val'");
					while($data_di=imw_fetch_array($lens_items_qry)){
						if( $data_di['item_prac_code'] > 0 ){
							$di_detail_id=$data_di['id'];
							$item_prac_code=$data_di['item_prac_code'];
							$order_det_qty=$data_di['qty'];
							$price=$data_di['wholesale_price'];
							$total_amount=$data_di['total_amt'];
							$allowed=$data_di['allowed'];
							$ins_amount=$data_di['ins_amount'];
							$pt_resp=$data_di['pt_resp'];
							$pt_paid=$data_di['pt_paid'];
							
							if($data_di['ins_case_id']>0){
								$ord_ins_case_id=$data_di['ins_case_id'];
							}
							
							$proc_selfpay=1;
							if($data_di['ins_case_id']>0){
								$proc_selfpay=0;
							}
							if($vision_val=="os"){
								$modifier_id=$mod_arr['lt'];
							}else{
								$modifier_id=$mod_arr['rt'];
							}
							
							$sqlLens = '';
							$whereLens = '';
							if( $data_di['order_chld_id']==0 )
								$sqlLens = 'insert into';
							else
							{
								$sqlLens = 'update';
								$whereLens = " where charge_list_detail_id='".$data_di['order_chld_id']."' and charge_list_id='$order_chl_id' and patient_id='$patient_id'";
							}
							
							$sqlLens .= " patient_charge_list_details set charge_list_id='$order_chl_id',patient_id='$patient_id',start_date='$order_entered_date',
							procCode='$item_prac_code',primaryProviderId='$cn_prov_id',primary_provider_id_for_reports='$cn_prov_id',units='$order_det_qty',procCharges='$price',totalAmount='$total_amount',approvedAmt='$allowed',
							newBalance='$total_amount',balForProc='$total_amount',opt_order_detail_id='$di_detail_id',proc_selfpay='$proc_selfpay',entered_date='$entered_date_time',modifier_id1='$modifier_id',
							operator_id='$opr_id',pri_due='$ins_amount',pat_due='$pt_resp',posFacilityId='$idoc_facility',place_of_service='$idoc_pos_id',notes='".imw_real_escape_string($item_comment)."'".$whereLens;
							
							imw_query($sqlLens);
							if( $data_di['order_chld_id'] == 0 )
								$order_chld_id=imw_insert_id();
							else
								$order_chld_id = $data_di['order_chld_id'];
				
							$discounts=0;
							$discountCode=0;
							if($data_di['overall_discount']>0){
								$discounts=$data_di['overall_discount'];
								$discountCode=$overall_discount_code;
							}else{
								if(strpos($data_di['discount'], "%")!==FALSE){
									$discounts_val = preg_replace("/[^\d\.]/", "", $data_di['discount']);
									$discounts = ($allowed-$ins_amount) * ($discounts_val/100);
								}
								elseif( trim($data_di['discount'])!='' && trim($data_di['discount'])!='0' ){
									$discounts = (float)trim($data_di['discount']);
								}
								$discountCode=$data_di['discount_code'];
							}
							
							/*Check if the entry already exists for the line item*/
							$writeoffSql = 'SELECT `write_off_id` FROM `paymentswriteoff` WHERE `charge_list_detail_id`=\''.$order_chld_id.'\' AND `optical_order_detail_id`=\''.$di_detail_id.'\'';
							$respWriteoff = imw_query($writeoffSql);
							$writeoffRows = imw_num_rows($respWriteoff);
							
							$writeoffSql = '';
							$writeoffWhere = '';
							if( $writeoffRows == 0 && $discounts > 0 )
								$writeoffSql = "insert into";
							elseif( $writeoffRows == 1 )
							{
								$writeoffSql = "update";
								$respWriteoff  = imw_fetch_assoc($respWriteoff);
								$writeoffWhere = " where write_off_id='".$respWriteoff['write_off_id']."'";
							}
							
							if( $writeoffSql !== '' ){
								$writeoffSql .= " paymentswriteoff set patient_id='$patient_id',encounter_id='$order_enc_id',charge_list_detail_id='$order_chld_id',write_off_amount='$discounts',write_off_operator_id='$opr_id',entered_date='$entered_date_time',write_off_date='$entered_date',write_off_code_id='$discountCode',paymentStatus='Discount',optical_order_detail_id='$di_detail_id'".$writeoffWhere;
								imw_query($writeoffSql);
							}
							
							$chargePaymentSql = 'SELECT `payment_details_id`, `payment_id` FROM `patient_charges_detail_payment_info` WHERE `charge_list_detail_id`=\''.$order_chld_id.'\' AND `optical_order_detail_id`=\''.$di_detail_id.'\'';
							$chargePaymentResp = imw_query($chargePaymentSql);
							$chargePaymentRows = imw_num_rows($chargePaymentResp);
							
							$chargePaymentSql = $chargePaymentInfoSql = "";
							$chargePaymentWhere = $chargePaymentInfoWhere = "";
							
							if( $chargePaymentRows == 0 && $pt_paid > 0 )
								$chargePaymentSql = $chargePaymentInfoSql = "insert into";
							elseif( $chargePaymentRows == 1 )
							{
								$chargePaymentSql = $chargePaymentInfoSql = "update";
								$chargePaymentResp = imw_fetch_assoc($chargePaymentResp);
								$chargePaymentWhere = " where payment_details_id='".$chargePaymentResp['payment_details_id']."'";
								$chargePaymentInfoWhere = " where payment_id='".$chargePaymentResp['payment_id']."'";
							}
							
							if( $chargePaymentSql !== '' && $chargePaymentInfoSql !== '' ){
								
								$chargePaymentInfoSql .= " patient_chargesheet_payment_info set encounter_id='$order_enc_id',paid_by='Patient',payment_amount='$pt_paid',
											payment_mode='$payment_mode',checkNo='$checkNo',creditCardNo='$creditCardNo',creditCardCo='$creditCardCo',
											date_of_payment='$entered_date',payment_time='$entered_time',expirationDate='$expirationDate',
											operatorId='$opr_id',paymentClaims='Paid',transaction_date='$entered_date',optical_order_id='$order_id', facility_id='$billing_facility_id' ".$chargePaymentInfoWhere;
								imw_query($chargePaymentInfoSql);
								
								if( $chargePaymentRows == 0 && $pt_paid > 0 )
									$order_payment_id = imw_insert_id();
								else
									$order_payment_id = $chargePaymentResp['payment_id'];
								
								$chargePaymentSql .= " patient_charges_detail_payment_info set payment_id='$order_payment_id',charge_list_detail_id='$order_chld_id',
											 paidBy='Patient',paidDate='$entered_date',paid_time='$entered_time',paidForProc='$pt_paid',operator_id='$opr_id',
											 entered_date='$entered_date_time',optical_order_detail_id='$di_detail_id'".$chargePaymentWhere;
								imw_query($chargePaymentSql);
							}
							
							$chk_tx_ins=0;
							if($pt_resp>0 || $ins_amount>0){
								if($proc_selfpay>0 && $ins_amount>0){
									$chk_tx_ins=1;
									$old_tx_pat_due=$allowed;
									$old_tx_ins_due=0;
								}
								if($proc_selfpay<1 && $pt_resp>0){
									$chk_tx_ins=1;
									$old_tx_pat_due=0;
									$old_tx_ins_due=$allowed;
								}
							}
							
							if($pt_resp>0 || $ins_amount>0){
								if($chk_tx_ins>0){
									$q_tx=imw_query("select id from tx_payments where patient_id='$patient_id' and encounter_id='$order_enc_id' and charge_list_id='$order_chl_id' and charge_list_detail_id='$order_chld_id' and pri_due='$old_tx_ins_due' and pat_due='$old_tx_pat_due' order by id desc limit 0,1");
									if(imw_num_rows($q_tx)<=0){
										imw_query("insert into tx_payments set patient_id='$patient_id',encounter_id='$order_enc_id',charge_list_id='$order_chl_id',charge_list_detail_id='$order_chld_id',pri_due='$old_tx_ins_due',pat_due='$old_tx_pat_due',payment_date='$entered_date',
										entered_date='$entered_date_time',payment_time='$entered_time',operator_id='$opr_id'");
									}
								}
							}
							imw_query("update in_order_lens_price_detail set order_enc_id='$order_enc_id',order_chld_id='$order_chld_id', modified_date='$entered_date', modified_time='$entered_time', modified_by='$opr_id' where id='$di_detail_id'");
						}
					}
				}else{
					if( $row_ord_det['item_prac_code'.$vision_type]>0){
						
						$itemSql = '';
						$itemWhere = '';
						if( $row_ord_det['order_chld_id'.$vision_type] == 0 )
							$itemSql = "insert into";
						else
						{
							//match charge_list_id and patient_id for confirmation
							$qConfirm=imw_query("select charge_list_detail_id from patient_charge_list_details where charge_list_id='$order_chl_id' and patient_id='$patient_id' and charge_list_detail_id='".$row_ord_det['order_chld_id'.$vision_type]."'");
							if(imw_num_rows($qConfirm)>0)
							{
								$itemSql = "update";
								$itemWhere = " where charge_list_detail_id='".$row_ord_det['order_chld_id'.$vision_type]."'";
							}else{
								$itemSql = "insert into";
							}
						}
						
						$itemSql .= " patient_charge_list_details set charge_list_id='$order_chl_id',patient_id='$patient_id',start_date='$order_entered_date',
						procCode='$item_prac_code',primaryProviderId='$cn_prov_id',primary_provider_id_for_reports='$cn_prov_id',units='$order_det_qty',procCharges='$price',totalAmount='$total_amount',approvedAmt='$allowed',
						newBalance='$total_amount',balForProc='$total_amount',opt_order_detail_id='$order_det_id',proc_selfpay='$proc_selfpay',entered_date='$entered_date_time',modifier_id1='$modifier_id',
						operator_id='$opr_id',pri_due='$ins_amount',pat_due='$pt_resp',posFacilityId='$idoc_facility',place_of_service='$idoc_pos_id',notes='".imw_real_escape_string($item_comment)."'".$itemWhere;
						imw_query($itemSql);
						
						if( $row_ord_det['order_chld_id'.$vision_type] == 0 )
							$order_chld_id = imw_insert_id();
						else
							$order_chld_id = $row_ord_det['order_chld_id'.$vision_type];
			
						$discounts=0;
						$discountCode=0;
						if($row_ord_det['overall_discount'.$vision_type]>0){
							$discounts=$row_ord_det['overall_discount'.$vision_type];
							$discountCode=$overall_discount_code;
						}else{
							if(strpos($row_ord_det['discount'.$vision_type], "%")!==FALSE){
								$discounts_val = preg_replace("/[^\d\.]/", "", $row_ord_det['discount'.$vision_type]);
								$discounts = ($allowed-$ins_amount) * ($discounts_val/100);
							}
							elseif( trim($row_ord_det['discount'.$vision_type])!='' && trim($row_ord_det['discount'.$vision_type])!='0' ){
								$discounts = (float)trim($row_ord_det['discount'.$vision_type]);
							}
							$discountCode=$row_ord_det['discount_code'.$vision_type];
						}
						
						/*Check if the entry already exists for the line item*/
						$writeoffSql = 'SELECT `write_off_id` FROM `paymentswriteoff` WHERE `charge_list_detail_id`=\''.$order_chld_id.'\' AND `optical_order_detail_id`=\''.$order_det_id.'\'';
						$respWriteoff = imw_query($writeoffSql);
						$writeoffRows = imw_num_rows($respWriteoff);
						
						$writeoffSql = '';
						$writeoffWhere = '';
						if( $writeoffRows == 0 && $discounts > 0 )
							$writeoffSql = "insert into";
						elseif( $writeoffRows == 1 )
						{
							$writeoffSql = "update";
							$respWriteoff  = imw_fetch_assoc($respWriteoff);
							$writeoffWhere = " where write_off_id='".$respWriteoff['write_off_id']."'";
						}
						
						if( $writeoffSql !== '' ){
							$writeoffSql .= " paymentswriteoff set patient_id='$patient_id',encounter_id='$order_enc_id',charge_list_detail_id='$order_chld_id',write_off_amount='$discounts',write_off_operator_id='$opr_id',entered_date='$entered_date_time',write_off_date='$entered_date',write_off_code_id='$discountCode',paymentStatus='Discount',optical_order_detail_id='$order_det_id'".$writeoffWhere;
							imw_query($writeoffSql);
						}
						
						$chargePaymentSql = 'SELECT `payment_details_id`, `payment_id` FROM `patient_charges_detail_payment_info` WHERE `charge_list_detail_id`=\''.$order_chld_id.'\' AND `optical_order_detail_id`=\''.$order_det_id.'\'';
	
						$chargePaymentResp = imw_query($chargePaymentSql);
						$chargePaymentRows = imw_num_rows($chargePaymentResp);
						
						$chargePaymentSql = $chargePaymentInfoSql = "";
						$chargePaymentWhere = $chargePaymentInfoWhere = "";
						
						if( $chargePaymentRows == 0 && $pt_paid > 0 )
							$chargePaymentSql = $chargePaymentInfoSql = "insert into";
						elseif( $chargePaymentRows == 1 )
						{
							$chargePaymentSql = $chargePaymentInfoSql = "update";
							$chargePaymentResp = imw_fetch_assoc($chargePaymentResp);
							$chargePaymentWhere = " where payment_details_id='".$chargePaymentResp['payment_details_id']."'";
							$chargePaymentInfoWhere = " where payment_id='".$chargePaymentResp['payment_id']."'";
						}
						
						if( $chargePaymentSql !== '' && $chargePaymentInfoSql !== '' ){
							
							$chargePaymentInfoSql .= " patient_chargesheet_payment_info set encounter_id='$order_enc_id',paid_by='Patient',payment_amount='$pt_paid',
										payment_mode='$payment_mode',checkNo='$checkNo',creditCardNo='$creditCardNo',creditCardCo='$creditCardCo',
										date_of_payment='$entered_date',payment_time='$entered_time',expirationDate='$expirationDate',
										operatorId='$opr_id',paymentClaims='Paid',transaction_date='$entered_date',optical_order_id='$order_id', facility_id='$billing_facility_id' ".$chargePaymentInfoWhere;
							imw_query($chargePaymentInfoSql);
							
							if( $chargePaymentRows == 0 && $pt_paid > 0 )
								$order_payment_id = imw_insert_id();
							else
								$order_payment_id = $chargePaymentResp['payment_id'];
							
							$chargePaymentSql .= " patient_charges_detail_payment_info set payment_id='$order_payment_id',charge_list_detail_id='$order_chld_id',
										 paidBy='Patient',paidDate='$entered_date',paid_time='$entered_time',paidForProc='$pt_paid',operator_id='$opr_id',
										 entered_date='$entered_date_time',optical_order_detail_id='$order_det_id'".$chargePaymentWhere;
							imw_query($chargePaymentSql);
						}
						
						$chk_tx_ins=0;
						if($pt_resp>0 || $ins_amount>0){
							if($proc_selfpay>0 && $ins_amount>0){
								$chk_tx_ins=1;
								$old_tx_pat_due=$allowed;
								$old_tx_ins_due=0;
							}
							if($proc_selfpay<1 && $pt_resp>0){
								$chk_tx_ins=1;
								$old_tx_pat_due=0;
								$old_tx_ins_due=$allowed;
							}
						}
						
						if($pt_resp>0 || $ins_amount>0){
							if($chk_tx_ins>0){
								$q_tx=imw_query("select id from tx_payments 
								where patient_id='$patient_id' and encounter_id='$order_enc_id' and charge_list_id='$order_chl_id' and
								charge_list_detail_id='$order_chld_id' and pri_due='$old_tx_ins_due' and pat_due='$old_tx_pat_due'");
								if(imw_num_rows($q_tx)<=0){
									imw_query("insert into tx_payments set patient_id='$patient_id', encounter_id='$order_enc_id', charge_list_id='$order_chl_id', charge_list_detail_id='$order_chld_id', pri_due='$old_tx_ins_due', pat_due='$old_tx_pat_due', payment_date='$entered_date', entered_date='$entered_date_time', payment_time='$entered_time', operator_id='$opr_id'");
								}
							}
						}
						imw_query("update in_order_details set order_chld_id".$vision_type."='$order_chld_id', modified_date='$entered_date', modified_time='$entered_time', modified_by='$opr_id' where id='$order_det_id'");
					}
				
					if($module_type_id=="3" && $vision_type==""){
						$chk_ord_chld_di=imw_query("select * from in_order_cl_detail where order_detail_id='$order_det_id' AND order_id='$order_id' and price > 0 and del_status='0'");
						if(imw_num_rows($chk_ord_chld_di)>0){
							$data_di = imw_fetch_array($chk_ord_chld_di);
							if( $data_di['prac_code_id'] > 0 ){
								$di_detail_id=$data_di['id'];
								$item_prac_code=$data_di['prac_code_id'];
								$order_det_qty=$data_di['qty'];
								$price=$data_di['price'];
								$total_amount=$data_di['total_amount'];
								$allowed=$data_di['allowed'];
								$ins_amount=$data_di['ins_amount'];
								$pt_resp=$data_di['pt_resp'];
								$pt_paid=$data_di['pt_paid'];
								
								if($data_di['ins_case_id']>0){
									$ord_ins_case_id=$data_di['ins_case_id'];
								}
								
								$proc_selfpay=1;
								if($data_di['ins_case_id']>0){
									$proc_selfpay=0;
								}
								
								$itemSql = '';
								$itemWhere = '';
								if( $data_di['order_chld_id'] == 0 )
									$itemSql = "insert into";
								else
								{
									//match charge_list_id and patient_id for confirmation
									$qConfirm=imw_query("select charge_list_detail_id from patient_charge_list_details where charge_list_id='$order_chl_id' and patient_id='$patient_id' and charge_list_detail_id='".$data_di['order_chld_id']."'");
									if(imw_num_rows($qConfirm)>0)
									{
										$itemSql = "update";
										$itemWhere = " where charge_list_detail_id='".$data_di['order_chld_id']."'";
									}else
									{
										$itemSql = "insert into";
									}
								}
								
								$itemSql .= " patient_charge_list_details set charge_list_id='$order_chl_id',patient_id='$patient_id',start_date='$order_entered_date',
								procCode='$item_prac_code',primaryProviderId='$cn_prov_id',primary_provider_id_for_reports='$cn_prov_id',units='$order_det_qty',procCharges='$price',totalAmount='$total_amount',approvedAmt='$allowed',
								newBalance='$total_amount',balForProc='$total_amount',opt_order_detail_id='$di_detail_id',proc_selfpay='$proc_selfpay',entered_date='$entered_date_time',
								operator_id='$opr_id',pri_due='$ins_amount',pat_due='$pt_resp',posFacilityId='$idoc_facility',place_of_service='$idoc_pos_id',notes='".imw_real_escape_string($item_comment)."'".$itemWhere;
								imw_query($itemSql);
								
								if( $data_di['order_chld_id']  == 0 )
									$order_chld_id = imw_insert_id();
								else
									$order_chld_id = $data_di['order_chld_id'];
								
								$discounts=0;
								$discountCode=0;
								if($data_di['overall_discount']>0){
									$discounts=$data_di['overall_discount'];
									$discountCode=$overall_discount_code;
								}else{
									if(strpos($data_di['discount'], "%")!==FALSE){
										$discounts_val = preg_replace("/[^\d\.]/", "", $data_di['discount']);
										$discounts = ($allowed-$ins_amount) * ($discounts_val/100);
									}
									elseif( trim($data_di['discount'])!='' && trim($data_di['discount'])!='0' ){
										$discounts = (float)trim($data_di['discount']);
									}
									$discountCode=$data_di['discount_code'];
								}
								
								/*Check if the entry already exists for the line item*/
								$writeoffSql = 'SELECT `write_off_id` FROM `paymentswriteoff` WHERE `charge_list_detail_id`=\''.$order_chld_id.'\' AND `optical_order_detail_id`=\''.$di_detail_id.'\'';
								$respWriteoff = imw_query($writeoffSql);
								$writeoffRows = imw_num_rows($respWriteoff);
								
								$writeoffSql = '';
								$writeoffWhere = '';
								if( $writeoffRows == 0 && $discounts > 0 )
									$writeoffSql = "insert into";
								elseif( $writeoffRows == 1 )
								{
									$writeoffSql = "update";
									$respWriteoff  = imw_fetch_assoc($respWriteoff);
									$writeoffWhere = " where write_off_id='".$respWriteoff['write_off_id']."'";
								}
								
								if( $writeoffSql !== '' ){
									$writeoffSql .= " paymentswriteoff set patient_id='$patient_id',encounter_id='$order_enc_id',charge_list_detail_id='$order_chld_id',write_off_amount='$discounts',write_off_operator_id='$opr_id',entered_date='$entered_date_time',write_off_date='$entered_date',write_off_code_id='$discountCode',paymentStatus='Discount',optical_order_detail_id='$di_detail_id'".$writeoffWhere;
									imw_query($writeoffSql);
								}
								
								$chargePaymentSql = 'SELECT `payment_details_id`, `payment_id` FROM `patient_charges_detail_payment_info` WHERE `charge_list_detail_id`=\''.$order_chld_id.'\' AND `optical_order_detail_id`=\''.$di_detail_id.'\'';
								$chargePaymentResp = imw_query($chargePaymentSql);
								$chargePaymentRows = imw_num_rows($chargePaymentResp);
								
								$chargePaymentSql = $chargePaymentInfoSql = "";
								$chargePaymentWhere = $chargePaymentInfoWhere = "";
								
								if( $chargePaymentRows == 0 && $pt_paid > 0 )
									$chargePaymentSql = $chargePaymentInfoSql = "insert into";
								elseif( $chargePaymentRows == 1 )
								{
									$chargePaymentSql = $chargePaymentInfoSql = "update";
									$chargePaymentResp = imw_fetch_assoc($chargePaymentResp);
									$chargePaymentWhere = " where payment_details_id='".$chargePaymentResp['payment_details_id']."'";
									$chargePaymentInfoWhere = " where payment_id='".$chargePaymentResp['payment_id']."'";
								}
								
								if( $chargePaymentSql !== '' && $chargePaymentInfoSql !== '' ){
									
									$chargePaymentInfoSql .= " patient_chargesheet_payment_info set encounter_id='$order_enc_id',paid_by='Patient',payment_amount='$pt_paid',
												payment_mode='$payment_mode',checkNo='$checkNo',creditCardNo='$creditCardNo',creditCardCo='$creditCardCo',
												date_of_payment='$entered_date',payment_time='$entered_time',expirationDate='$expirationDate',
												operatorId='$opr_id',paymentClaims='Paid',transaction_date='$entered_date',optical_order_id='$order_id', facility_id='$billing_facility_id' ".$chargePaymentInfoWhere;
									imw_query($chargePaymentInfoSql);
									
									if( $chargePaymentRows == 0 && $pt_paid > 0 )
										$order_payment_id = imw_insert_id();
									else
										$order_payment_id = $chargePaymentResp['payment_id'];
									
									$chargePaymentSql .= " patient_charges_detail_payment_info set payment_id='$order_payment_id',charge_list_detail_id='$order_chld_id',
												 paidBy='Patient',paidDate='$entered_date',paid_time='$entered_time',paidForProc='$pt_paid',operator_id='$opr_id',
												 entered_date='$entered_date_time',optical_order_detail_id='$di_detail_id'".$chargePaymentWhere;
									imw_query($chargePaymentSql);
								}
								
								$chk_tx_ins=0;
								if($pt_resp>0 || $ins_amount>0){
									if($proc_selfpay>0 && $ins_amount>0){
										$chk_tx_ins=1;
										$old_tx_pat_due=$allowed;
										$old_tx_ins_due=0;
									}
									if($proc_selfpay<1 && $pt_resp>0){
										$chk_tx_ins=1;
										$old_tx_pat_due=0;
										$old_tx_ins_due=$allowed;
									}
								}
								
								if($pt_resp>0 || $ins_amount>0){
									if($chk_tx_ins>0){
										$q_tx=imw_query("select id from tx_payments 
										where patient_id='$patient_id' and encounter_id='$order_enc_id' and charge_list_id='$order_chl_id' and charge_list_detail_id='$order_chld_id' and pri_due='$old_tx_ins_due' and pat_due='$old_tx_pat_due' and payment_date='$entered_date'");
										if(imw_num_rows($q_tx)<=0){
											imw_query("insert into tx_payments set patient_id='$patient_id', encounter_id='$order_enc_id', charge_list_id='$order_chl_id', charge_list_detail_id='$order_chld_id', pri_due='$old_tx_ins_due', pat_due='$old_tx_pat_due', payment_date='$entered_date', entered_date='$entered_date_time', payment_time='$entered_time', operator_id='$opr_id'");
										}
									}
								}
								imw_query("update in_order_cl_detail set order_chld_id='$order_chld_id', modified_date='$entered_date', modified_time='$entered_time', modified_by='$opr_id' where id='$di_detail_id'");
							}
						}
					}
				}
			}
		}
		if($ord_ins_case_id>0){
			$sel_ins_ids_qry=imw_query("SELECT provider,type FROM insurance_data WHERE
							ins_caseid='$ord_ins_case_id'
							AND pid='$patient_id'
							and provider > 0
							order by actInsComp asc, effective_date asc, id asc");
			while($sel_ins_ids_run=imw_fetch_array($sel_ins_ids_qry)){
				if($sel_ins_ids_run['type']=="primary"){
					$primaryInsuranceCoId=$sel_ins_ids_run['provider'];
				}
				if($sel_ins_ids_run['type']=="secondary"){
					$secondaryInsuranceCoId=$sel_ins_ids_run['provider'];
				}
				if($sel_ins_ids_run['type']=="tertiary"){
					$tertiaryInsuranceCoId=$sel_ins_ids_run['provider'];
				}
			}
		}
		
	
		$billing_type=3;
		if($grp_detail['group_anesthesia']>0){
			$billing_type=1;
		}else if($grp_detail['group_institution']>0){
			$get_ins_type = imw_query("SELECT * FROM insurance_companies WHERE id='$primaryInsuranceCoId'");
			$get_ins_type_row = imw_fetch_array($get_ins_type);
			if($get_ins_type_row['institutional_type']=="INST_PROF"){
				$billing_type=3;
			}else{
				$billing_type=2;
			}
		}
		if($order_chl_id>0){
	
			$all_dx_arr_seriz = serialize($all_dx_arr);
			$ref_phy_upd="";
			if($cn_reff_phy_id<=0){
				$qry = imw_query("select primary_care_id from patient_data where id = '$patient_id'");
				$patientDetail = imw_fetch_array($qry);
				
				$qry = imw_query("select patient_reff.reff_phy_id from patient_reff join insurance_data
				on insurance_data.id = patient_reff.ins_data_id where insurance_data.type = 'primary'
				and insurance_data.pid = '$patient_id' and insurance_data.ins_caseid = '$ord_ins_case_id'
				and insurance_data.actInsComp = '1' and insurance_data.referal_required = 'Yes'
				and insurance_data.provider > '0' and patient_reff.reff_type = '1'
				and ((patient_reff.end_date >= current_date() and patient_reff.effective_date <= current_date()) or(patient_reff.no_of_reffs > '0'))
				order by patient_reff.end_date desc,patient_reff.reff_id desc,insurance_data.actInsComp desc limit 0,1");
				$reffDetail = imw_fetch_array($qry);
				
				if($reffDetail['reff_phy_id']>0){
					$reff_phy_id = $reffDetail['reff_phy_id'];
				}else{
					$reff_phy_id = $patientDetail['primary_care_id'];	
				}
				if($reff_phy_id>0){
					$ref_phy_upd=", reff_phy_id='".$reff_phy_id."'";
				}
			}
			
			imw_query("update patient_charge_list set case_type_id='$ord_ins_case_id',primaryInsuranceCoId='$primaryInsuranceCoId',secondaryInsuranceCoId='$secondaryInsuranceCoId',
			tertiaryInsuranceCoId='$tertiaryInsuranceCoId',all_dx_codes='$all_dx_arr_seriz', billing_type='$billing_type' $ref_phy_upd where charge_list_id='$order_chl_id'");
		
			if($tax_chld>0){
				$tax_procedureId = back_prac_id($tax_prac_code);
				imw_query("Update patient_charge_list_details set start_date='$order_entered_date',procCode='$tax_procedureId',procCharges='$tax_payable',totalAmount='$tax_payable',approvedAmt='$tax_payable' where charge_list_detail_id='$tax_chld' and charge_list_id='$order_chl_id'");
			}else{
				if($tax_payable>0){
					if($tax_label==""){
						$tax_label="Tax";
					}
					$tax_procedureId = back_prac_id($tax_prac_code);
					if($tax_procedureId!="" && $tax_procedureId>0){
						imw_query("insert into patient_charge_list_details set charge_list_id='$order_chl_id',patient_id='$patient_id',
						  start_date='$order_entered_date',procCode='$tax_procedureId',primaryProviderId='$cn_prov_id',primary_provider_id_for_reports='$cn_prov_id',units='1',procCharges='$tax_payable',
						  totalAmount='$tax_payable',approvedAmt='$tax_payable',newBalance='$tax_payable',
						  balForProc='$tax_payable',entered_date='$entered_date_time',operator_id='$opr_id',pat_due='$tax_payable',
						  posFacilityId='$idoc_facility',place_of_service='$idoc_pos_id',proc_selfpay='1',display_order='2',notes='$tax_label'");
						$order_tax_chl_id=imw_insert_id();
						imw_query("update in_order set tax_chld='$order_tax_chl_id' where id='$order_id'");
						
						if($tax_pt_paid>0){
							imw_query("insert into patient_chargesheet_payment_info set encounter_id='$order_enc_id',paid_by='Patient',payment_amount='$tax_pt_paid',
							payment_mode='$payment_mode',checkNo='$checkNo',creditCardNo='$creditCardNo',creditCardCo='$creditCardCo',
							date_of_payment='$entered_date',payment_time='$entered_time',expirationDate='$expirationDate',
							operatorId='$opr_id',paymentClaims='Paid',transaction_date='$entered_date',optical_order_id='$order_id', facility_id='$billing_facility_id'");
							$order_payment_id=imw_insert_id();
							imw_query("insert into patient_charges_detail_payment_info set payment_id='$order_payment_id',charge_list_detail_id='$order_tax_chl_id',
							paidBy='Patient',paidDate='$entered_date',paid_time='$entered_time',paidForProc='$tax_pt_paid',operator_id='$opr_id',
							entered_date='$entered_date_time',optical_order_detail_id='$order_tax_chl_id'");
										
						}
					}
				}
			}
			
			$getProcDetailsStr =  "SELECT * FROM patient_charge_list_details WHERE del_status='0' and charge_list_id = '$order_chl_id'";
			$getProcDetailsQry = imw_query($getProcDetailsStr);
			while($getProcDetailsRows = imw_fetch_array($getProcDetailsQry)){				
					$totalEncounterAmt_arr[]=$getProcDetailsRows['totalAmount'];
					$approvedTotalAmt_arr[]=$getProcDetailsRows['approvedAmt'];
					$paidTotal_arr[]=$getProcDetailsRows['paidForProc'];
					$newTotalBalance_arr[]=$getProcDetailsRows['newBalance'];
			}
			$totalEncounterAmt=array_sum($totalEncounterAmt_arr);
			$approvedTotalAmt=array_sum($approvedTotalAmt_arr);
			$paidTotal=array_sum($paidTotal_arr);
			$newTotalBalance_final=array_sum($newTotalBalance_arr);
			
			$updatepatientAmtStr = imw_query("UPDATE patient_charge_list SET totalAmt = '$totalEncounterAmt',approvedTotalAmt = '$approvedTotalAmt',
			amtPaid = '$paidTotal',amountDue = '$newTotalBalance_final',totalBalance = '$newTotalBalance_final' WHERE charge_list_id = '$order_chl_id'");									
		}
	}
	
	$patient_id = (int)$patient_id;
	
	if( !empty($order_enc_id) && strtolower($GLOBALS['LOCAL_SERVER'])==='childrenseye' && $patient_id>0)
	{
		$ignoreAuth = true;
		require_once($GLOBALS['IMW_DIR_PATH']."/interface/patient_info/CLS_makeHL7.php");
		
		if( defined('ACC_DFT_GENERATION_OPTICAL') && ACC_DFT_GENERATION_OPTICAL === true )
		{
			
			$makeHL7 = new makeHL7;
			$makeHL7->patient_id = $patient_id;
			
			$sqlDOS = "SELECT `sa_app_start_date` FROM `schedule_appointments` WHERE `sa_app_start_date` <= '".$order_entered_date."' AND `sa_patient_id` = '".$patient_id."' AND `hl7_appt_external_fac_id` != '' AND `athenaID` != '' ORDER BY `sa_app_start_date` DESC LIMIT 1";
			
			$respDOS = imw_query($sqlDOS);
			if( $respDOS && imw_num_rows($respDOS) > 0 )
			{
				$respDOS = imw_fetch_assoc($respDOS);
				$order_entered_date = $respDOS['sa_app_start_date'];
				
				$makeHL7->date_of_service = $order_entered_date;	/*Order data to map appointment*/
			}
			else
			{
				$makeHL7->date_of_service = date("Y-m-d");	/*Order data to map appointment*/
				$makeHL7->NoAppt = true;
			}
			
			$makeHL7->log_HL7_message($order_enc_id,'Detailed Financial Transaction','ACC_DFT');
			unset($makeHL7);
		}
	}
}

/* Get Retail Price Calculation formula for the Item
 * @module_type_id = Item's Module type id
 * @data = parameters to query formula
 */
function get_retail_formula($module_type_id, $data){
	
	$module_type_id = (int)$module_type_id;
	
	$formula_params	= array();
	$data_params	= array();
	$formula		= '';
	
	if( $module_type_id > 0 ){
		
		if( $module_type_id == 1 ){
			$formula_params = array('manufacturer_id', 'brand_id', 'style_id');
			$data_params	= array('manufacturer_id', 'brand_id', 'frame_style');
		}
		elseif( $module_type_id == 3 ){
			$formula_params = array('manufacturer_id', 'brand_id');
			$data_params	= array('manufacturer_id', 'brand_id');
		}
		elseif( $module_type_id == 5 ){
			$formula_params = array('manufacturer_id', 'vendor_id');
			$data_params	= array('manufacturer_id', 'vendor_id');
		}
		elseif( $module_type_id == 6 ){
			$formula_params = array('manufacturer_id', 'vendor_id');
			$data_params	= array('manufacturer_id', 'vendor_id');
		}
		$data_params_bk = $data_params;	/*Backup Copy*/
	
		$sql_formula = 'SELECT `formula` FROM `in_retail_price_markup` WHERE `module_type_id`='.$module_type_id;
		$sql_formula_default = $sql_formula;
		
		foreach( $data_params as $key => $data_param ){
			
			if( (int)$data[$data_param] > 0 ){
				$sql_formula .= ' AND `'.$formula_params[$key].'`='.( (int)$data[$data_param] );
				unset($data_params[$key]);
			}
			else{
				break;
			}
		}
		
		if( count($data_params) > 0 ){
			foreach( $data_params as $key => $data_param ){
				$sql_formula .= ' AND `'.$formula_params[$key].'`=0';
				unset($data_params[$key]);
			}
		}
		$sql_formula .= ' AND `del_status`=0';
		
		$resp_formula = imw_query($sql_formula);
		
		if( !$resp_formula || imw_num_rows($resp_formula) <= 0 ){
			
			$data_params = $data_params_bk;
			
			do
			{
				$sql_formula = $sql_formula_default;
				
				$paramName = array_pop($data_params_bk);
				unset($data[$paramName]);
				
				foreach( $data_params as $key => $data_param ){
					
					if( (int)$data[$data_param] > 0 ){
						$sql_formula .= ' AND `'.$formula_params[$key].'`='.( (int)$data[$data_param] );
					}
					else{
						$sql_formula .= ' AND `'.$formula_params[$key].'`=0';
					}
				}
				$sql_formula .= ' AND `del_status`=0';
				$resp_formula = imw_query($sql_formula);
				
			}
			while(imw_num_rows($resp_formula) <= 0 && count($data_params_bk) > 0);
		}
		
		if( !$resp_formula || imw_num_rows($resp_formula) <= 0 ){
			
			foreach( $data_params_bk as $key => $data_param ){
				$sql_formula_default .= ' AND `'.$formula_params[$key].'`=0';
			}
			$resp_formula = imw_query($sql_formula_default);
		}
		
		/*Fetch Default Formula*/
		$formula = imw_fetch_assoc($resp_formula);
		$formula = $formula['formula'];
		imw_free_result($resp_formula);
	}
	return trim($formula);
}

/*
 * Calculate Markup price on the basis of formula given
 * Required
 *  @formula
 */
function calculate_markup_price($formula, $wholesale=0, $purchase=0){
	
	$formula	= trim($formula);
	$wholesale	= trim($wholesale);
	$purchase	= trim($purchase);
	
	$mathString	= '';
	$retailPrice= 0;
	
	/*Replace Price value in the formula*/
	if( $formula !='' ){
		
		/*Replace wholesale price - case insensitive*/
		if( stripos($formula, 'W')!==false)
		{
			if($wholesale>0)$mathString = preg_replace('/W/i', $wholesale, $formula);
		}
		/*Replace purchase price - case insensitive*/
		else if( stripos($formula, 'P')!==false){
			if($purchase>0)$mathString = preg_replace('/P/i', $purchase, $formula);
		}
		else $mathString = $formula;
	}
	
	/*Calculate the retail price value by executing the formula*/
	if( trim($mathString)!='' ){
		try{
			$mathString	= trim($mathString); /*trim white spaces*/
			$mathString	= preg_replace('/[^0-9\.\+\-\*\/\(\)]/', '', $mathString); /*remove any non-numbers chars; exception for math operators*/
			//$compute	= create_function("", "return (" . $mathString . ");" );
			$compute = 0;
			eval( "\$compute = ($mathString);" );

			$retailPrice = 0 + $compute;
		}catch(Throwable $e)
		{
			//do nothing
			//echo $e->errorMessage();
			$retailPrice=0;
		}
	}
	return $retailPrice;
}

/*Add/Update Custom Charges added to the Order*/
function update_order_custom_charges($order_id, &$charge_rows, $order_facility=0){
	
	$return_qry_fields = array(0=>'', 1=>'', 2=>'', 3=>'');
	
	if(isset($_POST['paymentMode']) && $_POST['paymentMode']!=""){
		$paymentMode = $_POST['paymentMode'];
		if($paymentMode == 'Check' || $paymentMode == 'EFT' || $paymentMode == 'Money Order'){
			$checkNo = $_POST['checkNo'];
		}
		elseif($paymentMode == 'Credit Card'){
			$cCNo = $_POST['cCNo'];
			$creditCardCo = $_POST['creditCardCo'];
			$expireDate = $_POST['expireDate'];
		}
		$return_qry_fields[0] = ",payment_mode='".$paymentMode."',checkNo='".$checkNo."',creditCardNo='".$cCNo."',creditCardCo='".$creditCardCo."',expirationDate='".$expireDate."'";
	}
	if(isset($_POST['total_overall_discount'])){
		$overall_discount =  preg_replace("/[^\d]/", "", $_POST['overall_discount']);
		$total_overall_discount = $_POST['total_overall_discount'];
		if($overall_discount<=0){
			$total_overall_discount = 0;
		}
		$overall_discount =  preg_replace("/[^\d\%\.]/", "", $_POST['overall_discount']);
		if($total_overall_discount!="" || $total_overall_discount==0){
			$return_qry_fields[1] = ", overall_discount='".$overall_discount."', overall_discount_code=".((int)$_POST['overall_discount_code']).", total_overall_discount='".$total_overall_discount."', overall_discount_prac_code='".$_POST['overall_discount_prac_code']."'";
		}
	}
	if(isset($_POST['tax_prac_code'])){
		$return_qry_fields[2] = ",tax_prac_code='".$_POST['tax_prac_code']."', tax_payable='".$_POST['tax_payable']."', tax_pt_paid='".$_POST['tax_pt_paid']."', tax_pt_resp='".$_POST['tax_pt_resp']."', `tax_custom`='".$_POST['tax_custom']."'";
	}
	if(isset($_POST['grand_total'])){
		$return_qry_fields[3] = ", grand_total='".$_POST['grand_total']."'";
	}
	
	$order_id = (int)$order_id;
	/*Skip execution if order id not provided or custom charge does not exists*/
	if($order_id<=0 || !is_array($charge_rows))
		return($return_qry_fields);
	
	$insertFlag = false;
	
	krsort($charge_rows);
	while($row = array_pop($charge_rows)){
	
		$sql = '';
		$where = '';
		$other_details = '';
		$del_data = '';
		$row['pos_order_detail_id'] = (int)$row['pos_order_detail_id'];
		if($row['pos_order_detail_id']>0){
			$sql = 'UPDATE `in_order_details`';
			$where = ' WHERE `id`='.$row['pos_order_detail_id'];
			$other_details = ",	`modified_date`='".date('Y-m-d')."',
								`modified_time`='".date('H:i:s')."',
								`modified_by`='".$_SESSION['authId']."'";
			
			$del_status = (int)$row['del_status'];
			if($del_status>0){
				$del_data = ",	`del_status`='".$del_status."',
								`del_date`=	'".date('Y-m-d')."',
								`del_time`='".date('H:i:s')."',
								`del_operator_id`='".$_SESSION['authId']."'";
			}
		}
		else{
			$row['del_status'] = (int)$row['del_status'];
			if($row['del_status']>0)
				continue;
			
			$sql = 'INSERT INTO `in_order_details`';
			$other_details = ", `entered_date`='".date('Y-m-d')."',
								`entered_time`='".date('H:i:s')."',
								`operator_id`='".$_SESSION['authId']."'";
			$insertFlag = true;
		}
		
		$prac_code_id = back_prac_id($row['pos_item_prac_code']);
		
		$sql .= " SET
				`order_id`='".$order_id."',
				`patient_id`='".$_SESSION['patient_session_id']."',
				`item_prac_code`='".$prac_code_id."',
				`module_type_id`='".$row['pos_module_type_id']."',
				`item_name`='".$row['pos_item_name']."',
				`price`='".$row['pos_price']."',
				`qty`='".$row['pos_qty']."',
				`allowed`='".$row['pos_allowed']."',
				`total_amount`='".$row['total_amount']."',
				`tax_rate`='".$row['tax_p']."',
				`tax_paid`='".$row['tax_v']."',
				`ins_amount`='".$row['ins_amount']."',
				`overall_discount`='".$row['item_overall_discount']."',
				`discount`='".$row['discount']."',
				`pt_paid`='".$row['pt_paid']."',
				`pt_resp`='".$row['pt_resp']."',
				`discount_code`='".$row['discount_code']."',
				`ins_case_id`='".$row['ins_case_id']."',
				`loc_id`='$order_facility',
				`tax_applied`='".$row['tax_applied']."'".$other_details.$del_data.$where;
		imw_query($sql);
	}
	
	if( $insertFlag === true ){
		change_order_status($order_id, 'pending');
		update_in_order_status($order_id);
	}
	
	return($return_qry_fields);
}

function check_privillage($option){
	
	$resp = false;
	switch( $option ){
		case 'pos':
			$resp = ( !isset($_SESSION["PERMISSION"]['priv_Optical_POS']) || $_SESSION["PERMISSION"]['priv_Optical_POS']==1 );
			break;
		case 'inventory':
			$resp = ( !isset($_SESSION["PERMISSION"]['priv_Optical_Inventory']) || $_SESSION["PERMISSION"]['priv_Optical_Inventory']==1 );
			break;
		case 'admin':
			$resp = ( !isset($_SESSION["PERMISSION"]['priv_Optical_Admin']) || $_SESSION["PERMISSION"]['priv_Optical_Admin']==1 );
			break;
		case 'reports':
			$resp = ( !isset($_SESSION["PERMISSION"]['priv_Optical_Reports']) || $_SESSION["PERMISSION"]['priv_Optical_Reports']==1 );
			break;
		default:
			$resp = true;
			break;
	}
	return $resp;
}

/*
 * @string = String to be splitted
 * @lineLength = No. of characters in one line
 * @seperator = Line seperator
 * 
 * return processed string seperated by seperator provided.
 */
function splitLongString($string, $lineLength=40, $seperator=" "){
	
	$finalString = '';
	$lineLength = (int)$lineLength;
	$start = 0;
	
	if( strlen($string) > $lineLength )
	{
		$loops = ceil(strlen($string) / $lineLength);
		
		for( $i = 0; $i < $loops; $i++ )
		{
			$tempComment = ltrim( substr($string, $start, $lineLength) );
			
			if( substr($tempComment, -1, 1) != ' ' )
			{
				$tempLength = strpos($string, ' ', ($start+$lineLength));
				
				if( $tempLength ){
					
					$lengthDiff = $tempLength - ($start+$lineLength);
					
					$tempComment = substr($string, $start, $lineLength+$lengthDiff);
					
					$start += $lineLength+$lengthDiff;
				}
				else{
					$tempComment = substr($string, $start);
					$finalString .= trim($tempComment).$seperator;
					break;
				}
			}
			else
				$start = $start + $lineLength;
			$finalString .= trim($tempComment).$seperator;
		}
	}
	else
		$finalString = trim($string);
	
	return $finalString;
}

//BELOW FUNCTION TAKE OVER FROM IDOC => LIBRARY/CLASSES/FUNCTIONS.PHP TO REPLACE THE APPOINTMENT RELATED VARIABLES
function __getApptInfo($patient_id,$providerIds=0,$report_start_date,$report_end_date){
		$appStrtDate = $appStrtTime = $doctorName = $facName = $procName = $andSchProvQry = "";
		$schDataQryRes=array();		
		if($providerIds) { $andSchProvQry = "AND sc.sa_doctor_id IN($providerIds)";}
		//if($appt_id){ $andSchProvQry.= " AND sc.id='".$appt_id."'";}
		
		if($report_start_date || $report_end_date){
			$schDataQry = "SELECT DATE_FORMAT(sc.sa_app_start_date,'".get_sql_date_format()."') as appStrtDate, DATE_FORMAT(sc.sa_app_start_date,'%M %d, %Y') as appStrtDate_FORMAT, sc.procedure_site as appSite,DATE_FORMAT(sc.sa_app_starttime,'%h:%i %p') as appStrtTime,
						sc.sa_patient_app_status_id as appStatus, sc.case_type_id as casetypeid, CONCAT_WS(', ', us.lname, us.fname) as doctorName, us.lname as doctorLastName, fac.name as facName,fac.street as facStreet,fac.city as facCity,fac.state as facState,fac.postal_code as facPostal_code,fac.zip_ext as faczip_ext, fac.phone as facPhone,slp.proc as procName, sc.sa_comments  
						FROM schedule_appointments sc 
						LEFT JOIN users us ON us.id = sc.sa_doctor_id 
						LEFT JOIN facility fac ON fac.id = sc.sa_facility_id 
						LEFT JOIN slot_procedures slp ON slp.id = sc.procedureid 
						WHERE sa_patient_id = '".$patient_id."'
						AND sc.sa_app_start_date BETWEEN '".$report_start_date."' AND '".$report_end_date."'
						AND sc.sa_patient_app_status_id NOT IN('18','203')
						$andSchProvQry
						ORDER BY sc.sa_app_start_date DESC
						LIMIT 0,1";
			$schDataQryRes = get_array_records_query($schDataQry);
		}
		
		if(count($schDataQryRes)<=0) {
			$schDataQry = "SELECT DATE_FORMAT(sc.sa_app_start_date,'".get_sql_date_format()."') as appStrtDate, DATE_FORMAT(sc.sa_app_start_date,'%M %d, %Y') as appStrtDate_FORMAT, sc.procedure_site as appSite,DATE_FORMAT(sc.sa_app_starttime,'%h:%i %p') as appStrtTime,
							sc.sa_patient_app_status_id as appStatus, sc.case_type_id as casetypeid, CONCAT_WS(', ', us.lname, us.fname) as doctorName, us.lname as doctorLastName, fac.name as facName,fac.street as facStreet,fac.city as facCity,fac.state as facState,fac.postal_code as facPostal_code,fac.zip_ext as faczip_ext,fac.phone as facPhone,slp.proc as procName, sc.sa_comments  
							FROM schedule_appointments sc 
							LEFT JOIN users us ON us.id = if(sc.facility_type_provider!='0',sc.facility_type_provider,sc.sa_doctor_id)  
							LEFT JOIN facility fac ON fac.id = sc.sa_facility_id 
							LEFT JOIN slot_procedures slp ON slp.id = sc.procedureid 
							WHERE sa_patient_id = '".$patient_id."'
							AND sc.sa_app_start_date >= current_date()
							AND sc.sa_patient_app_status_id NOT IN('18','203')
							AND sc.sa_patient_app_status_id IN('0','13','17','202')
							$andSchProvQry
							ORDER BY sc.sa_app_start_date ASC
							LIMIT 0,1";
			$schDataQryRes = get_array_records_query($schDataQry);
		}		
		if(count($schDataQryRes)<=0) {
			$schDataQry = "SELECT DATE_FORMAT(sc.sa_app_start_date,'".get_sql_date_format()."') as appStrtDate, DATE_FORMAT(sc.sa_app_start_date,'%M %d, %Y') as appStrtDate_FORMAT, sc.procedure_site as appSite,DATE_FORMAT(sc.sa_app_starttime,'%h:%i %p') as appStrtTime,
							sc.sa_patient_app_status_id as appStatus, sc.case_type_id as casetypeid, CONCAT_WS(', ', us.lname, us.fname) as doctorName, us.lname as doctorLastName, fac.name as facName,fac.street as facStreet,fac.city as facCity,fac.state as facState,fac.postal_code as facPostal_code,fac.zip_ext as faczip_ext ,fac.phone as facPhone,slp.proc as procName, sc.sa_comments  
							FROM schedule_appointments sc 
							LEFT JOIN users us ON us.id = if(sc.facility_type_provider!='0',sc.facility_type_provider,sc.sa_doctor_id)  
							LEFT JOIN facility fac ON fac.id = sc.sa_facility_id 
							LEFT JOIN slot_procedures slp ON slp.id = sc.procedureid 
							WHERE sa_patient_id = '".$patient_id."'
							AND sc.sa_app_start_date <= current_date() 
							AND sc.sa_patient_app_status_id NOT IN('18','203')
							$andSchProvQry
							ORDER BY sc.sa_app_start_date DESC
							LIMIT 0,1";
			$schDataQryRes = get_array_records_query($schDataQry);		
		}
		if(count($schDataQryRes)<=0) {
			$schDataQry = "SELECT DATE_FORMAT(sc.sa_app_start_date,'".get_sql_date_format()."') as appStrtDate, DATE_FORMAT(sc.sa_app_start_date,'%M %d, %Y') as appStrtDate_FORMAT, sc.procedure_site as appSite,DATE_FORMAT(sc.sa_app_starttime,'%h:%i %p') as appStrtTime,
							sc.sa_patient_app_status_id as appStatus, sc.case_type_id as casetypeid, CONCAT_WS(', ', us.lname, us.fname) as doctorName, us.lname as doctorLastName, fac.name as facName,fac.street as facStreet,fac.city as facCity,fac.state as facState,fac.postal_code as facPostal_code,fac.zip_ext as faczip_ext ,fac.phone as facPhone,slp.proc as procName, sc.sa_comments  
							FROM schedule_appointments sc 
							LEFT JOIN users us ON us.id = sc.sa_doctor_id 
							LEFT JOIN facility fac ON fac.id = sc.sa_facility_id 
							LEFT JOIN slot_procedures slp ON slp.id = sc.procedureid 
							WHERE sa_patient_id = '".$patient_id."'
							AND sc.sa_app_start_date <= current_date() 
							$andSchProvQry
							ORDER BY sc.sa_app_start_date DESC
							LIMIT 0,1";
			$schDataQryRes = get_array_records_query($schDataQry);		
		}
		if(count($schDataQryRes)>0) {
			for($i=0;$i<count($schDataQryRes);$i++){
				$appStrtDate 			= $schDataQryRes[$i]['appStrtDate'];
				$appStrtDate_FORMAT 	= $schDataQryRes[$i]['appStrtDate_FORMAT'];
				$facName 				= $schDataQryRes[$i]['facName'];
				$facStreet 				= $schDataQryRes[$i]['facStreet'];
				$facCity 				= $schDataQryRes[$i]['facCity'];
				$facState 				= $schDataQryRes[$i]['facState'];
				$facPostal_code			= $schDataQryRes[$i]['facPostal_code'];
				$faczip_ext				= $schDataQryRes[$i]['faczip_ext'];
				$facPhone 				= $schDataQryRes[$i]['facPhone'];
				$facPhoneFormat			= $facPhone;
				if(trim($facPhoneFormat)) {
					$facPhoneFormat = str_ireplace("-","",$facPhoneFormat);
					$facPhoneFormat = "(".substr($facPhoneFormat,0,3).") ".substr($facPhoneFormat,3,3)."-".substr($facPhoneFormat,6);
				}
				
				$procName 				= $schDataQryRes[$i]['procName'];
				$doctorName 			= $schDataQryRes[$i]['doctorName'];
				$doctorLastName 		= $schDataQryRes[$i]['doctorLastName'];
				
				$appSite 				= ucfirst($schDataQryRes[$i]['appSite']);
				$appSiteShow 			= $appSite;
				if($appSite == "Bilateral") {$appSiteShow="Both"; }
				
				$appStrtTime 			= $schDataQryRes[$i]['appStrtTime'];
				if($appStrtTime[0]=="0") { $appStrtTime = substr($appStrtTime, 1); }

				$appComments 			= $schDataQryRes[$i]['sa_comments'];
				$appComments 			= htmlentities($appComments);
				$appcasetypeid			= $schDataQryRes[$i]['casetypeid'];
			}
		}
		$appInfo = array($appStrtDate,$appStrtDate_FORMAT,$facName,$facPhoneFormat,$procName,$doctorName,$doctorLastName,$appSiteShow,$appStrtTime,$appComments,$facStreet,$facCity,$facState,$facPostal_code,$faczip_ext,$appcasetypeid);
		return $appInfo;
	}
	
	function get_array_records_query($query)
{
	$return = array();
	$sql = imw_query($query);
	$cnt = imw_num_rows($sql);
	if($cnt > 0 )
	{
		while( $row = imw_fetch_assoc($sql))
		{
			$return[] = $row;
		}
	}
	return $return;
}

// ----------- This function return date format to be used in sql query in date_format function
function get_sql_date_format($date_format='',$yearL='Y',$separator=''){ 
	$date_format = $date_format == '' ? inter_date_format() : $date_format;
	$separator = ($separator == "")?get_separator_inter($date_format):$separator;
	$date_format = str_replace("-",$separator,$date_format);
	$yearL = ($yearL!="")?$yearL:"Y";
	switch($date_format){
		case "yyyy".$separator."dd".$separator."mm":
			$sqlFormat = '%'.$yearL.$separator.'%d'.$separator.'%m';
		break;
		case "yyyy".$separator."mm".$separator."dd":
			$sqlFormat = '%'.$yearL.$separator.'%m'.$separator.'%d';
		break;
		case "mm".$separator."dd".$separator."yyyy":
			$sqlFormat = '%m'.$separator.'%d'.$separator.'%'.$yearL;
		break;
		case "dd".$separator."mm".$separator."yyyy":

			$sqlFormat = '%d'.$separator.'%m'.$separator.'%'.$yearL;
		break;
		default:
			$sqlFormat = '%m'.$separator.'%d'.$separator.'%'.$yearL;
	}
	return $sqlFormat;
}

function inter_date_format()
{
	return $GLOBALS['date_format'] ? $GLOBALS['date_format'] : 'mm-dd-yyyy';
}

function get_separator_inter($date){
		$separator = "-";
		if(strpos($date,'/')!==false) { $separator = "/";}
		else if(strpos($date,'-')!==false){ $separator = "-";}
		else if(strpos($date,'\\')!==false){$separator = "\\";}
		return $separator;
}
/*
Function: show_image_thumb
Purpose: to show logo
Author: PP
*/
function show_image_thumb($fileName, $targetWidth = 116, $targetHeight = 116, $id='', $style='', $target=''){
	$return = "";
	//$path = $GLOBALS['WEB_PATH']."/interface/patient_interface/uploaddir/facility_logo/"
	$path = $GLOBALS['DIR_PATH']."/interface/patient_interface/uploaddir/facility_logo/".$fileName;
	if(file_exists($path)){	
		$img_size = getimagesize($path);
		$width = $img_size[0];
		$height = $img_size[1];

		do{
			if($width > $targetWidth){
				$width = $targetWidth;
				$percent = $img_size[0] / $width;
				$height = $img_size[1] / $percent; 
			}
			if($height > $targetHeight){
				$height = $targetHeight;
				$percent = $img_size[1] / $height;
				$width = $img_size[0] / $percent; 
			}
		}while($width > $targetWidth || $height > $targetHeight);

		if($target=='locatio_page'){
		$imageWebPath = $GLOBALS['WEB_PATH']."/interface/patient_interface/uploaddir/facility_logo/".$fileName;
		}else{
		//we were having issue in printing logo on /imwcloud19.mednetworx.com/fses_optical/ with web path so we have changed it to dir path
		$imageWebPath = $GLOBALS['DIR_PATH']."/interface/patient_interface/uploaddir/facility_logo/".$fileName;}
		$return = "<img id=\"$id\" src=\"".$imageWebPath."\" style=\"width:".$width."px;height:".$height."px; $style\">";
	}
	return $return;
}

###################################################################
#   Getting optical notes for particular patient
###################################################################
function patient_optical_notes_alert($patient_id){	
    $optical_notes = false;
    $sql_opt_notes = "SELECT patient_notes FROM patient_data where id ='".imw_real_escape_string($patient_id)."' and chk_notes_optical=1 ";
    $res_opt_notes = imw_query($sql_opt_notes);				
    if($res_opt_notes && imw_num_rows($res_opt_notes)>0){
        $resultRow = imw_fetch_assoc($res_opt_notes);
        if(is_array($resultRow)){
           $optical_notes=$resultRow["patient_notes"];
        }
    }
    return $optical_notes;
}



?>