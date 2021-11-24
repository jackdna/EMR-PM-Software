<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php
include_once("funcSurgeryCenter.php");
?>
<html>
	<head>
	<title>List Surgery Center</title>
	<link rel="stylesheet" href="../common/coolstyle.css"></link>
	<LINK HREF="../form_design/css/style_surgery.css" TYPE="text/css" REL="stylesheet">
		<link rel="stylesheet" href="../form_design/css/form.css" type="text/css" />
		<link rel="stylesheet" href="../form_design/css/theme.css" type="text/css" />
		<link rel="stylesheet" href="../form_design/css/sfdc_header.css" type="text/css" />
		<link rel="stylesheet" href="../form_design/css/simpletree.css" type="text/css" />
		<script src="../common/jscript.js"></script>
		<style>
			a.black:hover{ color:"Red"; }
		</style>
	<script>
		function editRecord(id)
		{
			var objFrm=document.forms["frm".concat(id)];
			if(objFrm != "undefined")
			{
				var flagButtons = false;
				var objTargetFrm = top.iframeHome.iframeMain.document.frmSurgeryCenter;
				var arrElem = new Array("elem_surgeryCenterId","elem_mode","elem_name","elem_address","elem_phone","elem_fax",
							"elem_email","elem_npi","elem_federalEin","elem_billLocation","elem_acceptAssignment",
							"elem_maxRecentlyUsedPass","elem_maxLoginAttempts","elem_maxPassExpiresDays",
							"elem_loginLegalNotice","elem_finalizeDays","elem_finalizeWarningDays","elem_submit");
				for(var x in arrElem)
				{
					var objElem = objFrm.elements[arrElem[x]];
					var objTargetElem = objTargetFrm[arrElem[x]];					
					if((typeof objElem != "undefined") || (typeof objTargetElem != "undefined"))
					{						
						var typeElem = objTargetElem.type;						
						if(typeof typeElem == "undefined")
						{
							typeElem = objTargetElem[0].type;
						}						
						switch(typeElem)
						{
							case "text":
							case "textarea":
							case "select-one":
							case "submit":
							case "hidden":
								 objTargetElem.value = objElem.value;
							break;
							case "radio":
								var rLen = objTargetElem.length;								
								for(var i=0;i<rLen;i++)
								{
									objTargetElem[i].type;
									objTargetElem[i].checked = (objTargetElem[i].value == objElem.value) ? "checked" : "" ;								
								}
							break
						}
						
						if(flagButtons == false)
						{
							flagButtons =true;
						}
					}				
				}
				//set buttons
				if(flagButtons == true)
				{
					var objReset = top.iframeHome.iframeMain.document.getElementById("tdReset");
					objReset.style.display = "block";
				}
			}		
		}
		
		function deleteRecord(id)
		{
			if(confirm("Do you want to delete this record?"))
			{
				var objFrm = top.iframeHome.iframeMain.document.frmSurgeryCenter;
				objFrm.elem_mode.value="3";
				objFrm.elem_surgeryCenterId.value=id;
				objFrm.submit();
			}
		}
		
		<?php
		if(isset($_GET["op"]))
		{
			echo "top.iframeHome.iframeMain.document.frmSurgeryCenter.elem_reset.click();";
			if($_GET["op"] == "1")
			{
				echo "alert(\"surgery center is saved.\");";
			}
			elseif($_GET["op"] == "2")
			{
				echo "alert(\"surgery center is edited.\");";
			}
			elseif($_GET["op"] == "3")
			{
				echo "alert(\"surgery center is deleted.\");";
			}
		}
		?>
	</script>
	</head>
	<body>
		<table align="center" width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr id="head">
			<th>Name</th>
			<th>Address</th>
			<th>NPI</th>
			<th>Federal EIN</th>
			<th>Function</th>
		</tr>
		<?php
			$res = getSurgeryCenters();
			if(imw_num_rows($res)>0)
			{
				for($i=0;$row=sqlFetchArray($res);$i++)
				{
					$name=$row["name"];
					$address=$row["address"];
					$npi=$row["npi"];
					$federalEin=$row["federalEin"];
					$id=$row["surgeryCenterId"];
					$phone=$row["phone"];
					$fax=$row["fax"];
					$email=$row["email"];
					$federalEin=$row["federalEin"];
					$billLocation=$row["billLocation"];
					$acceptAssignment=$row["acceptAssignment"];
					$maxRecentlyUsedPass=$row["maxRecentlyUsedPass"];
					$maxLoginAttempts=$row["maxLoginAttempts"];
					$maxPassExpiresDays=$row["maxPassExpiresDays"];
					$loginLegalNotice=$row["loginLegalNotice"];
					$finalizeDays=$row["finalizeDays"];
					$finalizeWarningDays=$row["finalizeWarningDays"];
					$surgeryCenterLogo=$row["surgeryCenterLogo"];
					echo "<tr>
							<td>".$name."</td>
							<td>".$address."</td>
							<td>".$npi."</td>
							<td>".$federalEin."</td>
							<td><a href=\"#\" onclick=\"editRecord('".$id."')\" class=\"black\" >Edit</a>/<a href=\"#\" onclick=\"deleteRecord('".$id."')\" class=\"black\">Delete</a>
							</td>
						</tr>";
					echo "<form name=\"frm".$id."\">";
					$arrElem = array("elem_surgeryCenterId"=>$id,"elem_name"=>$name,"elem_address"=>$address,"elem_phone"=>$phone,
								"elem_fax"=>$fax,"elem_email"=>$email,"elem_npi"=>$npi,"elem_federalEin"=>$federalEin,"elem_billLocation"=>$billLocation,
								"elem_acceptAssignment"=>$acceptAssignment,"elem_surgeryCenterLogo","elem_maxRecentlyUsedPass"=>$maxRecentlyUsedPass,
								"elem_maxLoginAttempts"=>$maxLoginAttempts,"elem_maxPassExpiresDays"=>$maxPassExpiresDays,"elem_loginLegalNotice"=>$loginLegalNotice,
								"elem_finalizeDays"=>$finalizeDays,"elem_finalizeWarningDays"=>$finalizeWarningDays,"elem_mode"=>"2","elem_submit"=>"Edit");
					foreach($arrElem as $key => $val)
					{
						echo "<input type=\"hidden\" name=\"".$key."\" value=\"".$val."\">\n";
					}
					echo "</form>";
				}
			}
			else
			{
				echo "<tr><td colspan=\"5\">No Record Found.</td></tr>";
			}
		?>	
		</table>
	</body>
</html>