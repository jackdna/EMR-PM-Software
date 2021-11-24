<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include_once("../../common/conDb.php");

$c_date = date('Y-m-d H:i:s');
$sql = array();
$sql[] = "CREATE TABLE IF NOT EXISTS supply_categories (id int(11) NOT NULL AUTO_INCREMENT, name varchar(255) NOT NULL, deleted tinyint(2) NOT NULL, date_created datetime NOT NULL, date_updated datetime NOT NULL, PRIMARY KEY (id) ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7;";

$sql[] = "INSERT INTO supply_categories(id,name,deleted,date_created)VALUES(1,'Vicoelastics',0,'".$c_date."');";
$sql[] = "INSERT INTO supply_categories(id,name,deleted,date_created)VALUES(2,'Intra Op Injectables',0,'".$c_date."');";
$sql[] = "INSERT INTO supply_categories(id,name,deleted,date_created)VALUES(3,'Sutures',0,'".$c_date."');";
$sql[] = "INSERT INTO supply_categories(id,name,deleted,date_created)VALUES(4,'Supplies',0,'".$c_date."');";
$sql[] = "INSERT INTO supply_categories(id,name,deleted,date_created)VALUES(5,'IOLs',0,'".$c_date."');";
$sql[] = "INSERT INTO supply_categories(id,name,deleted,date_created)VALUES(6,'Other',0,'".$c_date."');";

$sql[] = "ALTER TABLE predefine_suppliesused ADD cat_id INT NOT NULL AFTER suppliesUsedId;";
$sql[] = "CREATE TABLE predefine_suppliesused_bak_".date("d_m_Y")." AS (SELECT * FROM predefine_suppliesused)";
$sql[] = "UPDATE predefine_suppliesused SET cat_id = 6 Where cat_id = 0";

foreach($sql as $qry){
	imw_query($qry)or $msg_info[] = imw_error();
}

$supp_data = array();
$supp_data['1'] = array('Amvisc Plus','Duovisc','Healon','Healon 5','Healon GV','Ocucoat','Provisc','Viscoat');
$supp_data['2'] = array('Miochol','Miostat','Trypan Blue','Xylocaine MPF 1%','4% Lidocaine','2% Lidocaine /Epi','Mitosol','Moxifloxacin','Dexamethasone','BSS','Cefuroxime','Sterile Tetracaine','Omidria','Kenalog','Epinephrine 1:1000');
$supp_data['3'] = array('9-0 nylon 2890G','10-0 nylon 9000','10-0 nylon 9003','10-0 prolene 788G','10-0 prolene 1713G','9-0 prolene 1754','8-0 Gortex 8J02A','7-0 vicryl 575G','7-0 vicryl 546G','8-0 vicryl J975','8-0 vicryl J401G');
$supp_data['4'] = array('Alcon pack','Amo pack','Alcon PPK pack','Alcon Vit kit','Amo Vit Pack','15 degree blade','2.75 blade','2.65 slit blade','6.0 mm blade','1mm stab blade','MVR blade','5.2 blade','Capsule retractors','Capsule Polisher','CT ring','Iris retractors','Malyugin ring','I stent left','I stent right','MST 23G forcep','MST IOL cutter','Liquid  Optic (femto)','Cypass stent','Canaloplasty itrack250','I ring','Trabectome pack','18G eraser cautery','23G fine tip cautery');
$supp_data['5'] = array('Alcon Toric', 'AO1UV', 'AQ5010V', 'AR40e', 'AT-50AO (Crystalens)','AT-52AO (Crystalens)', 'BL1UT (Trulign)', 'CC4204A (Nanoflex)', 'L161AO', 'Lenstec HDO', 'MA60MA', 'MN60AC', 'MTA4UO (a/c)', 'SA60AT', 'SA60WF', 'SN6ad1 Restor', 'SN6AT (alcon toric)', 'Softec', 'SV25T0', 'SV25T3', 'SV25T4', 'SV25T5', 'Symphony', 'Verisyse', 'ZA9003', 'ZCBOO', 'ZCT', 'ZKBOO', 'ZLBOO', 'ZMA00', 'ZMBOO', 'ZXR (symphony)','ZXT');

foreach( $supp_data as $cat_id => $supplies)
{
	foreach($supplies as $supply)
	{
		$supply = trim($supply);
		if( $supply)
		{
			$chk_qry = "Select suppliesUsedId From predefine_suppliesused Where name='".$supply."' ";
			$chk_sql = imw_query($chk_qry) or $msg_info[] = imw_error();
			$chk_rows= imw_num_rows($chk_sql);
			if( $chk_rows > 0 )
			{
				$row = imw_fetch_object($chk_sql);
				$qry = "Update predefine_suppliesused Set cat_id = '".$cat_id."' Where suppliesUsedId = '".$row->suppliesUsedId."'  ";
			}
			else
			{
				$qry = "Insert Into predefine_suppliesused Set cat_id = '".$cat_id."', name='".$supply."', qtyChkBox = 1  ";
			}
			
			imw_query($qry) or $msg_info[] = imw_error();
		}
	}
}




$message = '';
if(count($msg_info)>0)
{
	$message = "<br><br><b>Update 142 Failed!</b><br>".implode("<br>",$msg_info)."<br>";
	$color = "red";	
}
else
{	
	$message = "<br><br><b>Update 142 Success.</b><br>";
	$color = "green";			
}

?>
<html>
<head>
<title>Update 142</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br><br>
<?php if($message!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"><?php echo($message);?></font>
<?php
@imw_close();
}
?> 
</body>
</html>