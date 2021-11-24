<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<script>
function saveTexts()
{	//alert(document.getElementById("getText"));
	//var abc = document.getElementById("eposting").value;
	document.getElementById("getText").value = document.getElementById("eposting").value;
	//alert(document.getElementById("getText").value)
	document.getElementById("evaluationEPostDiv").style.display = "none";
}

</script>
<div style="position:absolute; display:none;z-index:6;" id="evaluationEPostDiv">
<?php 
if($tablename!='')
{
?>
	<table class="alignCenter" style="border:1px solid black;width:100px; border-collapse:collapse; border:none;" >
		<tr style="background-color:#BCD2B0; height:25px;">
			<td class="text_10b alignLeft nowrap"  ></td><td class="alignRight" style="text-align:right;"><img src="images/chk_off1.gif" alt="Close" onClick="document.getElementById('evaluationEPostDiv').style.display='none';" ></td>
		</tr>
		<tr>
			<td colspan="2" class="text_9 alignLeft nowrap" >
				<!-- <textarea name="eposting" class="text_9" id="eposting" style="overflow:hidden;background-color:#FFFF99;" cols="25" rows="8" onBlur="saveTexts();"></textarea> -->
				<textarea name="eposting" class="text_9" id="eposting" style="overflow:hidden;background-color:#FFFF99;" cols="25" rows="8" ></textarea>
			</td>	
		</tr>
		 <tr>
			<td colspan="2"  class="text_9 alignLeft nowrap" style="background-color:#BCD2B0;" >
				<a href="#" onClick="addEpost(document.getElementById('eposting').value,'<?php echo $tablename;?>','<?php echo $consentMultipleId;?>','<?php echo $consentMultipleAutoIncrId;?>','<?php echo $hiddPurgestatus;?>','<?php echo $_REQUEST['pConfId'];?>','<?php echo $_REQUEST['patient_id'];?>');document.getElementById('evaluationEPostDiv').style.display = 'none';" class="link_slid_right"  style="cursor:hand; ">
					<span class="underLine">Enter-ePostit</span>
				</a>
			</td>	
		</tr>
	</table>
<?php
}
else
{
?>
    <table class="table_collapse alignCenter" style="border:1px solid black; width:250px;">
        <tr style="height:10px; background-color:#BCD2B0;">
            <td class="alignRight" style="text-align:right; cursor:pointer;"><img src="images/chk_off1.gif" alt="Close" onClick="document.getElementById('evaluationEPostDiv').style.display='none';"></td>
        </tr>	
        <tr  style="height:105px; background-color:#FFFF99;" >
            <td class="alignLeft text_10b nowarp">Select A Form For Epostit</td>
        </tr>
        <tr style="height:10px; background-color:#BCD2B0;">
            <td></td>
        </tr>	
    </table>
<?php 
}
?>
</div>
<?php
function Save_eposts($text,$tablename){
}
	function epost($tablename)
		{
			$query_rsNotes = "SELECT * FROM eposted WHERE table_name = '$tablename' AND patient_conf_id = '$pConfId' ";
				$rsNotes =imw_query($query_rsNotes);
				$totalRows_rsNotes =imw_num_rows($rsNotes);
		}


	class bp_temprature
	{
		function __construct()
		 {
			
			$output="";
			echo  "<div id=\"cal_pop\" style=\"position:absolute; background-color:#FFFFFF; border:1px solid blue;top:240px;left:10px;display:none;z-index:999;\" onMouseOver=\"stopCloseCal();\" onMouseOut=\"closeCal();\" >";  
			//$output . = "<table cellpadding=\"1\" cellspacing=\"1\" border=\"1\" bordercolor=\"#FFFFFF\" >";
		  echo "<table style=\"padding:2px; border-width:1px; border-color:#FFFFFF;width:150px;\" class=\"table_collapse\" >";
		  echo "<tr>"; 
		
		  				for($i=1;$i<10;$i++){
							
							echo "<td>";
							echo "<input type=button value=\"$i\" onClick= \"getVal_c($i);\" style=\"width:50px;\">" ;
							echo "</td>";
							 if($i%3 == 0){
								echo "</tr>";
								if($i != 9) {
									echo "<tr>";	
								}
							}
							
						}
						
				echo "<tr class=\"valignTop\"><td><input type=button value=\"0\" onClick= \"getVal_c(0);\"  style=\"width:50px;\"> </td>";	  
				echo "<td><input type=button value=\".\" onClick= \"getVal_c('.');\"  style=\"width:50px;\"> </td>";	  
				echo "<td><input type=button value=\"/\" onClick= \"getVal_c('/');\"  style=\"width:50px;\"> </td></tr>";	  
		 		echo"<tr><td><input type=button value=\"*\" style=\"width:50px;\" onClick= \"getVal_c('*');\"></td>";
				echo "<td><input type=button value=\"%\" style=\"width:50px;\" onClick= \"getVal_c('%');\"></td>"; 
				echo "<td><input type=button value=\"C\" style=\"width:50px;\" onClick=\"clearVal_c();\"></td></tr>";
				echo"<tr><td><input type=button value=\":\" style=\"width:50px;\" onClick= \"getVal_c(':');\"></td>";
				echo "<td><input type=button value=\"A\" style=\"width:50px;\" onClick= \"getVal_c(' AM');\"></td>"; 
				echo "<td><input type=button value=\"P\" style=\"width:50px;\" onClick=\"getVal_c(' PM');\"></td></tr>";
				echo"<tr><td colspan='3' class=\"alignCenter\"><input type=button value=\"-\" style=\"width:50px;\" onClick= \"getVal_c('-');\"></td></tr>";
				echo "</table>";
	  	 		echo "</div>";
						
		}
	}
	 $obj=new bp_temprature;
//

class temprature
	{
		function  __construct()
		 {
			
			$output="";
			echo  "<div id=\"temp_pop\" style=\"position:absolute;background-color:#FFFFFF; border:1px solid blue;top:240px;left:10px;width:155px;height:50px;display:none;z-index:999;\" onMouseOver=\"stopCloseTempCal();\" onMouseOut=\"closeTempCal();\" >";  
			//$output . = "<table cellpadding=\"1\" cellspacing=\"1\" border=\"1\" bordercolor=\"#FFFFFF\" >";
		  echo "<table style=\"padding:1px; border-width:1px; border-color:#FFFFFF;width:150px;\" class=\"table_collapse\" >";
		 echo "<tr>"; 
		
		  				for($i=1;$i<10;$i++){
							
							echo "<td>";
							echo "<input type=button value=\"$i\" onClick= \"getVal_c($i);\" style=\"width:50px;\">" ;
							echo "</td>";
							 if($i%3 == 0){
								
								echo "</tr>";
								if($i != 9) {
									echo "<tr>";	
								}
							}
							
						}
						
				echo "<tr class=\"valignTop\"><td><input type=button value=\"0\" onClick= \"getVal_c(0);\"  style=\"width:50px;\"> </td>";	  
				echo "<td><input type=button value=\".\" onClick= \"getVal_c('.');\"  style=\"width:50px;\"> </td>";	  
				echo "<td><input type=button value=\"/\" onClick= \"getVal_c('/');\"  style=\"width:50px;\"> </td></tr>";	  
		 		echo"<tr><td><input type=button value=\"F\" style=\"width:50px;\" onClick= \"getVal_c('F');\"></td>";
				echo "<td><input type=button value=\"-\" style=\"width:50px;\" onClick= \"getVal_c('-');\"></td>"; 
				echo "<td><input type=button value=\"C\" style=\"width:50px;\" onClick=\"clearVal_c();\"></td></tr>";
				echo"<tr><td colspan=\"3\"><input type=button value=\"AFEBRILE\" style=\"width=150px;\" onClick= \"getVal_c('AFEBRILE');\"></td></tr>";
				echo "</table>";
	  	 		echo "</div>";
		}
	}
	 $obj_temp=new temprature;

class opRommDiopterTemprature
	{
		function  __construct()
		 {
			$output="";
			echo  "<div id=\"opRoomDiopterPop\" style=\"position:absolute; background-color:#FFFFFF; border:1px solid blue;top:240px;left:10px;width:120px;height:50px;display:none;z-index:999;\" onMouseOver=\"stopCloseCal();\" onMouseOut=\"closeCal();\" >";  
			//$output . = "<table cellpadding=\"1\" cellspacing=\"1\" border=\"1\" bordercolor=\"#FFFFFF\" >";
		    echo "<table style=\"padding:1px; border-width:1px; border-color:#FFFFFF;width:150px;\" class=\"table_collapse\" >";
			echo "<tr>"; 
		
		  				for($i=1;$i<10;$i++){
							
							echo "<td>";
							echo "<input type=button value=\"$i\" onClick= \"getVal_c($i);\" style=\"width:50px;\">" ;
							echo "</td>";
							 if($i%3 == 0){
								
								echo "</tr>";
								if($i != 9) {
									echo "<tr>";	
								}
							}
							
						}
						
				echo "<tr class=\"valignTop\"><td><input type=button value=\"0\" onClick= \"getVal_c(0);\"  style=\"width:50px;\"> </td>";	  
				echo "<td><input type=button value=\".\" onClick= \"getVal_c('.');\"  style=\"width:50px;\"> </td>";	  
				echo "<td><input type=button value=\"/\" onClick= \"getVal_c('/');\"  style=\"width:50px;\"> </td></tr>";	  
		 		echo"<tr><td><input type=button value=\"+\" style=\"width:50px;\" onClick= \"getVal_c('+');\"></td>";
				echo "<td><input type=button value=\"%\" style=\"width:50px;\" onClick= \"getVal_c('%');\"></td>"; 
				echo "<td><input type=button value=\"C\" style=\"width:50px;\" onClick=\"clearVal_c();\"></td></tr>";
				echo"<tr><td><input type=button value=\":\" style=\"width:50px;\" onClick= \"getVal_c(':');\"></td>";
				echo "<td><input type=button value=\"A\" style=\"width:50px;\" onClick= \"getVal_c(' AM');\"></td>"; 
				echo "<td><input type=button value=\"P\" style=\"width:50px;\" onClick=\"getVal_c(' PM');\"></td></tr>";
				echo "</table>";
	  	 		echo "</div>";
		}
	}
	 $obj_opRoomDiopter_temp=new opRommDiopterTemprature;

//

?>