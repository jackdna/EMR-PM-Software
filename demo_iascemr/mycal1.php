<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<html>
<head>
<title>iMedic: Surgery Center</title>
<link rel="stylesheet" href="scheduler.css">	
<style>
/* font-family:"verdana"; font-size:10px; color:#333333;*/
.tbl_calendar{
	font-family: verdana,Arial, Helvetica, sans-serif;
	font-size:10px;
}
.select{
	font-family: verdana, Arial, Helvetica, sans-serif;
	font-size:10px;
	color:#000000;			
}
a{
	color:#333333;
	font-weight:bold;		 		 
}
a:hover{
	text-decoration: none;
	color:#FFCC00;
}
a.sel_date{
	color:red;
}
#days{
	background-color:#ECE9D8;
	border:1px solid #ECE9D8;
	color:#000000;		
}		
</style>
<script>
//To date
todate = new Date();
to_dat = todate.getDate();
to_mn = todate.getMonth();
to_yr = todate.getFullYear();
//

function Calendar(Month,Year){
	var output='';
	output+='<form name="Cal">'; //Make A form 'Cal'
	output+='<table cellpadding="0"  cellspacing="0"  class="tbl_calendar" border="0">'; //Start A table
	
	output+='<tr class="noborder">';
	output+='<td>';
	output+='<table align="center"><tr>';
	output+='<td>';
	output+='<select name="Month" onChange="changeMonth();" class="select">'; //select for changing month
	for(month=0;month<12;month++){
		if(month == Month){
			output+='<option value="'+ month +'" selected>'+ names[month] +'<\/option>';
		}else{
			output+='<option value="'+ month +'">'+ names[month] +'<\/option>';
		}
	}
	output+='<\/select>';
	output+='<\/td><td>';
	output+='<select name="Year" onChange="changeYear();" class="select">'; //select for changing year
	//cr_yr=eval(Year);
	cr_yr=to_yr;
	st_yr=cr_yr-100; /*cr_yr-80;*/
	ed_yr=cr_yr+10;
	for(year=st_yr;year < ed_yr;year++){
		if(year == Year){	
			output+='<option value="'+ year +'" selected>'+ year +'<\/option>'; 
		}else{
			output+='<option value="'+ year +'" >'+ year +'<\/option>'; 
		}	
	}
	output+='<\/td><\/tr><\/table>';	
	output+='<\/td><\/tr>';
	output+='<tr>';
	output+='<td bgcolor="#EFEFEF" align="center"><font color="#4D4B4B"><B>'+names[Month]+' '+Year+'</B><\/font><\/td>'; //Show month name and year
	output+='<\/tr>';
	output+='<tr><td bgcolor="#FFFFFF" style="border-width:2px;border-style:solid;border-color:#cccccc">';
	firstDay= new Date(Year,Month,1); //Give First Date of Given Year And Given Month 
	startDay=firstDay.getDay();  //Give First Day of Given Year And Given Month (say Sun as 0,Mon as 1 so on..)

	if((Year % 4 == 0)){ //set the days for February
		days[1]=29;	
	}else{
		days[1]=28;
	}
	output+='<table bgcolor="#FFFFFF" class="tbl_calendar" cellspacing="0" cellpadding="2">'; //start table for showing dates
	output+='<tr>';
	for(i=0;i<7;i++){
		output+='<td id="days"><font color="#OOOOOO">'+dow[i]+'<\/font><\/td>';	 //set the first row with day's names	
	}	
	output+='<\/tr>';
	output+='<tr>';
	output+='<\/tr>';
	var column=0;
	var lastMonth=Month - 1; //get last month
	if(lastMonth == -1){
		lastMonth=11;		
	}
	for(i=0;i<startDay;i++,column++){ //make td's for last months with last month dates
		output+='<td>'+(days[lastMonth]-startDay+i+1)+'<\/td>';  	
	}
	for(i=1;i<=days[Month];i++,column++){ //make td for this month and this months dates
		sel_date_cl = '';
		if(i == to_dat){
			if(Month == to_mn){
				if(Year == to_yr){
					sel_date_cl = 'class="sel_date"';
				}
			}
		}	
		output+='<td><a href="javascript:changeDay('+ i +')" '+sel_date_cl+'>'+i+'<\/a><\/td>';
		if(column == 6){
			output+='<\/tr><tr>';
			column = -1;		
		}
	}
	if(column  > 0){
		for(i=1;column<7;i++,column++){
			output+='<td>'+ i +'<\/td>';
		}
	}
	output+='<\/tr><\/table><\/form>';
	output+='<\/td><\/tr><\/table>';
	return output;
}
function changeDay(day){
	opener.day = day + '';
	md=document.goto.md.value;	
	opener.restart(md);
	self.close();
}
function changeMonth(){
	opener.month=document.Cal.Month.options[document.Cal.Month.selectedIndex].value + '';
	md=document.goto.md.value;	
	location.href='mycal1.php?md='+md;
}
function changeYear(){
	opener.year = document.Cal.Year.options[document.Cal.Year.selectedIndex].value + '';
	md=document.goto.md.value;	
	location.href='mycal1.php?md='+md;
}
function makeArray1(){
	for(i=0;i<makeArray1.arguments.length;i++){
		this[i]=makeArray1.arguments[i];
	}
}
var names =  new makeArray1('January','February','March','April','May','June','July','August','September','October','November','December');
var days = new makeArray1(31,28,31,30,31,30,31,31,30,31,30,31);
var dow = new makeArray1('Sun','Mon','Tue','Wed','Thr','Fri','Sat');
</script>	
</head>
<body topmargin="2" leftmargin="2">
<form name="goto" method="get" action="">
<input type="hidden" name="md" value="<?php print $_GET['md'];?>">	
</form>
<table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td>
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="10px" align="right"></td>
					<td style="padding:5px; background:#cd523f;"><font color="#FFFFFF" size="1px" face="Verdana, Arial, Helvetica, sans-serif">
						<b>Select Date</b>											 
						</font>
					</td>
					<td width="6px"></td>
				</tr>
			</table>	
		</td>
	</tr>
	<tr>
	<td>
		<table width="100%" height="100%"  border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="2" background="s../../images/border_left.jpg"></td>						
				<td height="100%" bgcolor="#ffffff">
					<TABLE width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
						<TR>
							<TD align="center">
								<script>document.write(Calendar(opener.month,opener.year));</script>
							</TD>
						</TR>
						<TR>
							<TD>&nbsp;</TD>
						</TR>
						<TR>
							<TD>	
								<table align="center">
									<tr>
										<td align="center"><a href="javascript:window.close();" class="select"><B>Close</B></a></td>
									</tr>
								</table>
							</TD>
						</TR>
						<TR>
							<TD>&nbsp;</TD>
						</TR>
					</TABLE>
				</td>
				<td width="2" background="../../images/border_left.jpg"></td>
			</tr>
		</table>
	</td>
</tr>  
<tr>
	<td>
		<table width="100%" height="100%"  border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="8"><img src="../../images/edge_left.jpg" width="8"></td>
				<td width="100%" width="2px;" background="../../images/bottom_line.jpg"></td>
				<td width="8"><img src="../../images/edge_right.jpg" width="8"></td>
			</tr>
		</table>
	</td>
</tr>				  
</table>
</body>
</html>