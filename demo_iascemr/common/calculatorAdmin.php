<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<script>
function getValAdmin_c(n){
	if(top.frames[0].frames[0].frames[0].document.getElementById("bp").value == 'flag1'){	
		top.frames[0].frames[0].frames[0].document.getElementById("bp_temp").value = '';
		displayText1 += n;
		top.frames[0].frames[0].frames[0].document.getElementById("bp_temp").value=displayText1; 
	}else {
		for(var k=2;k<=106;k++) {
			
			if(top.frames[0].frames[0].frames[0].document.getElementById("bp").value == 'flag'+k){
				top.frames[0].frames[0].frames[0].document.getElementById("bp_temp"+k).value="";
				displayText[k]= displayText[k] + n;
				top.frames[0].frames[0].frames[0].document.getElementById("bp_temp"+k).value=displayText[k];
			}
		}			
	}
}

var tOutAdmin; 
function closeCal2Admin(){
	if(top.frames[0].frames[0].document.getElementById("cal_pop_admin").style.display == "block"){
		top.frames[0].frames[0].document.getElementById("cal_pop_admin").style.display = "none";
		if(document.getElementById("hiddCalPopId")) {
			if(document.getElementById("hiddCalPopId").value=="calOpenYes") {
				document.getElementById("hiddCalPopId").value = "";
			}
		}
	}
	
}
	
function closeCalAdmin(){
	tOutAdmin = setTimeout("closeCal2Admin()", 500);
}
function stopCloseCalAdmin() {
	clearTimeout(tOutAdmin);
}

function clearValAdmin_c(){
	if(top.frames[0].frames[0].frames[0].document.getElementById("bp").value == 'flag1'){	
		displayText1 = '';
		top.frames[0].frames[0].frames[0].document.getElementById("bp_temp").value=displayText1 
	}else {
		
		for(var k=2;k<=106;k++) {
			if(top.frames[0].frames[0].frames[0].document.getElementById("bp").value == 'flag'+k){
				
				displayText[k] = '';
				top.frames[0].frames[0].frames[0].document.getElementById("bp_temp"+k).value=displayText[k];
			}
		}
	}
}

</script>
<?php
class bp_temprature
	{
		function __construct()
		 {
			
			$output="";
			echo  "<div id=\"cal_pop_admin\" style=\"position:absolute; background-color:#FFFFFF; border:1px solid blue;top:240px;left:10px;width:155px;display:none;\" onMouseOver=\"stopCloseCalAdmin();\" onMouseOut=\"closeCalAdmin();\" >";  
			//$output . = "<table cellpadding=\"1\" cellspacing=\"1\" border=\"1\" bordercolor=\"#FFFFFF\" >";
		  echo "<table style=\"padding:1px; border-width:1px; border-color:#FFFFFF;width:150px;\" class=\"table_collapse\"  >";
		 echo "<tr>"; 
		
		  				for($i=1;$i<10;$i++){
							
							echo "<td>";
							echo "<input type=button value=\"$i\" onClick= \"getValAdmin_c($i);\" style=\"width:50px;\">" ;
							echo "</td>";
							 if($i%3 == 0){
								
								echo "</tr>";
								if($i != 9) { echo "<tr>"; }
							}
							
						}
						
				echo "<tr class=\"valignTop\"><td><input type=button value=\"0\" onClick= \"getValAdmin_c(0);\"  style=\"width:50px;\"> </td>";	  
				echo "<td><input type=button value=\".\" onClick= \"getValAdmin_c('.');\"  style=\"width:50px;\"> </td>";	  
				echo "<td><input type=button value=\"/\" onClick= \"getValAdmin_c('/');\"  style=\"width:50px;\"> </td></tr>";	  
		 		echo"<tr><td><input type=button value=\"*\" style=\"width:50px;\" onClick= \"getValAdmin_c('*');\"></td>";
				echo "<td><input type=button value=\"%\" style=\"width:50px;\" onClick= \"getValAdmin_c('%');\"></td>"; 
				echo "<td><input type=button value=\"C\" style=\"width:50px;\" onClick=\"clearValAdmin_c();\"></td></tr>";
				echo"<tr><td><input type=button value=\":\" style=\"width:50px;\" onClick= \"getValAdmin_c(':');\"></td>";
				echo "<td><input type=button value=\"A\" style=\"width:50px;\" onClick= \"getValAdmin_c(' AM');\"></td>"; 
				echo "<td><input type=button value=\"P\" style=\"width:50px;\" onClick=\"getValAdmin_c(' PM');\"></td></tr>";
				echo "</table>";
	  	 		echo "</div>";
		}
	}
	 $obj=new bp_temprature;
?>