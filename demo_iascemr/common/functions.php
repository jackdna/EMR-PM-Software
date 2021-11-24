<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
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
<!--class="drsElement drsMoveHandle"-->
<div  style="border:0px none;background-color:transparent;position:absolute; display:none;z-index:100;left:330px;top:30px;width:300px;" id="evaluationEPostDiv">
<?php
$rsNote_bk_class = "epost_title";
$tablename = isset($tablename) ? $tablename : '';
if($tablename!='')
{
?>
	<div class="epostHead <?php echo $rsNote_bk_class; ?>" style="width: 100%;text-align:left;border-top-right-radius:5px;border-top-left-radius:5px;">
		<span style="">E-postit</span>
		<button style="opacity: .9;" type="button" class="close" data-dismiss="modal" aria-label="Close" onClick="document.getElementById('evaluationEPostDiv').style.display='none';">
			<span aria-hidden="true" style="">×</span>
		</button>
	</div>
	<div class="text-left" style="width: 100%;border: 1px solid #ababab;border-top: none;border-bottom:1px solid #EEEEEE;background-color:#FFFFFF;height:80px;">
		<textarea name="eposting" class="text_9" id="eposting" style="width: 100%;padding: 5px;overflow-y: auto;overflow-x:auto;border:0px none;height: 100%;"></textarea>
	</div>
	<div class="text-left" style="width:100%;border: 1px solid #ababab;border-top:none;border-bottom-right-radius:5px;border-bottom-left-radius:5px;background-color:#FFFFFF;padding:5px;">
		<a id="CancelBtn" class="btn btn-success epost_del" onClick="addEpost(document.getElementById('eposting').value,'<?php echo $tablename;?>','<?php echo $consentMultipleId;?>','<?php echo $consentMultipleAutoIncrId;?>','<?php echo $hiddPurgestatus;?>','<?php echo $_REQUEST['pConfId'];?>','<?php echo $_REQUEST['patient_id'];?>');document.getElementById('evaluationEPostDiv').style.display = 'none';"  href="javascript:void(0)">
			<b class="fa fa-save"></b>&nbsp;Save
		</a>
	</div>
	<div class="clearfix"></div>
<?php
}
else
{
?>
<div class="epostHead <?php echo $rsNote_bk_class; ?>" style="width: 100%;text-align:left;border-top-right-radius:5px;border-top-left-radius:5px;">
	<span style="">E-postit</span>
	<button style="opacity: .9;" type="button" class="close" data-dismiss="modal" aria-label="Close" onClick="document.getElementById('evaluationEPostDiv').style.display='none';">
		<span aria-hidden="true" style="">×</span>
	</button>
</div>
<div class="text-left" style="width: 100%;border: 1px solid #ababab;border-top: none;border-bottom:1px solid #EEEEEE;background-color:#FFFFFF;height:80px;padding: 5px;overflow-y: auto;overflow-x:auto;">
	Select A Form For Epostit
</div>
<div class="text-left" style="width:100%;border: 1px solid #ababab;border-top:none;border-bottom-right-radius:5px;border-bottom-left-radius:5px;background-color:#FFFFFF;padding:5px;">
	<a style="visibility:hidden;" id="CancelBtn" class="btn btn-success epost_del" href="javascript:void(0)">
		<b class="fa fa-save"></b>&nbsp;Save
	</a>
</div>
<div class="clearfix"></div>
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
			echo  "<div id=\"cal_pop\" style=\"position:absolute; background-color:#FFFFFF; border:1px solid blue;top:240px;left:10px; display:none;z-index:999;\" onMouseOver=\"stopCloseCal();\" onMouseOut=\"closeCal();\" >";  
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
			echo  "<div id=\"temp_pop\" style=\"position:absolute;background-color:#FFFFFF; border:1px solid blue;top:240px;left:10px;width:155px;display:none;z-index:999;\" onMouseOver=\"stopCloseTempCal();\" onMouseOut=\"closeTempCal();\" >";  
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
			echo  "<div id=\"opRoomDiopterPop\" style=\"position:absolute; background-color:#FFFFFF; border:1px solid blue;top:240px;left:10px;width:120px;display:none;z-index:999;\" onMouseOver=\"stopCloseCal();\" onMouseOut=\"closeCal();\" >";  
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


class localAnesEkgGridTemprature
	{
		function  __construct()
		 {
			$output="";
			echo  "<div id=\"localAnesEkgGridPop\" style=\"position:absolute; background-color:#FFFFFF; border:1px solid blue;top:240px;left:10px;width:120px;display:none;z-index:999;\" onMouseOver=\"stopCloseCal();\" onMouseOut=\"closeCal();\" >";  
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
				echo"<tr><td colspan=3><input type=button value=\"REPEAT\" style=\"width:150px;\" onClick= \"getVal_c('REPEAT');\"></td></tr>";
				echo "</table>";
	  	 		echo "</div>";
		}
	}
	 $obj_localAnesEkgGrid_temp=new localAnesEkgGridTemprature;
	 
	 
class vitalSignGridPop
{
		function  __construct()
		 {
				echo  '<div id="vitalSignGridPop" style="position:absolute; background-color:#FFFFFF; border:1px solid blue; top:240px;left:10px;width:155px;display:none;z-index:999;" >';
				echo '<table style="padding:1px; border-width:1px; border-color:#FFFFFF;width:155px;" class="table_collapse" >';
				echo '<tr>'; 
		
		  				for($i=1;$i<10;$i++)
						{
							echo '<td>';
							echo '<input type="button" value="'.$i.'" style="width:50px;">' ;
							echo '</td>';
							if($i%3 == 0)
							{
								echo '</tr>';
								if($i != 9) { echo '<tr>'; }
							}
						}
				
				echo '<tr class="valignTop"><td><input type=button value="0" style="width:50px;"> </td>';	  
				echo '<td><input type="button" value="."  style="width:50px;"> </td>';	  
				echo '<td><input type="button" value="/" style="width:50px;"> </td></tr>';	  
		 		echo '<tr><td><input type="button" value="+" style="width:50px;"></td>';
				echo '<td><input type="button" value="%" style="width:50px;"></td>'; 
				echo '<td><input type="button" value="C" style="width:50px;"></td></tr>';
				echo'<tr><td><input type="button" value=":" style="width:50px;"></td>';
				echo '<td><input type="button" value="A" style="width:50px;"></td>'; 
				echo '<td><input type="button" value="P" style="width:50px;"></td></tr>';
				//echo '<tr><td colspan=3><input type="button" value="REPEAT" style="width:50px;"></td></tr>';
				echo '</table>';
	  	 		echo '</div>';
		}
	}
	 $objVitalSignGrid =new vitalSignGridPop;	 

?>