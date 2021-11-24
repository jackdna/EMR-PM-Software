<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

File: prescription.php
Purpose: This file provides Prescription section in work view.
Access Type : Include
*/
//============GLOBAL FILE INCLUSION=====================
require_once(dirname(__FILE__).'/../../config/globals.php');

//============SESSION VARIABLES=========================
$_SESSION["encounter"] = "";
$pid = $_SESSION['patient'];	

//============GET PRESCRIPTION DATA=====================
$vquery_c = "SELECT 
				*
			FROM
				`prescriptions`
			WHERE 
				patient_id = $pid 
			ORDER BY 
				date_modified desc ";

$vsql_c = imw_query($vquery_c);
$count = imw_num_rows($vsql_c);

//============PRESCRIPTION STOP WORK====================
//============CALL BY copyitt FUNCTION BELOW============
if(isset($_REQUEST["pres"]) && $_REQUEST["pres"] != "")
{	
	$pres_id=($_REQUEST["pres"]);
	$stop_date=($_REQUEST["stop_date"]);
	$stop_prid=($_REQUEST["stop_prid"]);
	
	$updateQry ="UPDATE
					`prescriptions`
				SET
					stop = '5',
					stop_date='$stop_date',
					stop_facility='$stop_prid'  
				WHERE 
					id = $pres_id 
				";
					
	$updateSql = mysql_query($updateQry);
	if(!$updateSql)
	echo ("Error : ". mysql_error());	
	
	//REDIRECT TO MAIN FILE AFTER SAVING 
	$url_header = "p_pres_med.php";
	header("Location:".$url_header);			
}
?>
<html>
<head>
<!---------------------------------- BASIC CSS FILES-------------------------------------------->
<link href="<?php echo $library_path; ?>../../library/css/bootstrap.css" rel="stylesheet" type="text/css">
<link href="<?php echo $library_path; ?>../../library/css/common.css" rel="stylesheet" type="text/css">
<!---------------------------------- JS FILES--------------------------------------------------->
<script language="javascript">
var menuskin = "skin1"; // skin0, or skin1
var display_url = 0; // Show URLs in status bar?

function showmenuie5()
{
	var rightedge = document.body.clientWidth-event.clientX;
	var bottomedge = document.body.clientHeight-event.clientY;
	if (rightedge < ie5menu.offsetWidth)
	ie5menu.style.left = document.body.scrollLeft + event.clientX - ie5menu.offsetWidth;
	else
	ie5menu.style.left = document.body.scrollLeft + event.clientX;
	if (bottomedge < ie5menu.offsetHeight)
	ie5menu.style.top = document.body.scrollTop + event.clientY - ie5menu.offsetHeight;
	else
	ie5menu.style.top = document.body.scrollTop + event.clientY;
	ie5menu.style.visibility = "visible";
	return false;
}

function hidemenuie5()
{
	ie5menu.style.visibility = "hidden";
}

function highlightie5()
{
	if (event.srcElement.className == "menuitems")
	{
		event.srcElement.style.backgroundColor = "highlight";
		event.srcElement.style.color = "white";
		if (display_url)
			window.status = event.srcElement.url;
	}
}
function lowlightie5()
{
	if (event.srcElement.className == "menuitems")
	{
		event.srcElement.style.backgroundColor = "";
		event.srcElement.style.color = "black";
		window.status = "";
	}
}

function jumptoie5()
{
	if (event.srcElement.className == "menuitems")
	{
		if (event.srcElement.getAttribute("target") != null)
			window.open(event.srcElement.url, event.srcElement.getAttribute("target"));
		else
			window.location = event.srcElement.url;
	}
}

function copyitt(idx)
{
	var id=document.it.get_id.value;
	var stop_date=document.it.stop_date.value;
	var stop_prid=document.it.stop_prid.value;
	if(idx==2)
	{
		window.location.href("p_pres_med.php?pres="+id+"&stop_date="+stop_date+"&stop_prid="+stop_prid);
	}

	if(idx==3)
	{
		var parWidth = parent.document.body.clientWidth;
		var parHeight = parent.document.body.clientHeight;
		window.open('print_patient_prescription.php?printType=3&preId='+id,'printPatientPrescription','scrollbars=1,resizable=1,width='+parWidth+'height='+parHeight+',top=0,left=0');
	}
}

function get_value(id)
{
	document.it.get_id.value=id;
}

function reLocate(id)
{
	var refilledById = "sel_refilled_by_"+id;	
	var refilledBy = document.getElementById(refilledById).value;	
	top.window.location.replace("prescription.php?renew_id="+id+"&refilledBy="+refilledBy);
}
</script>
</head>
<body topmargin="0" leftmargin="0" class="scrol_Vblue_color" style="background-color:white!important;">
<?php 
if($count>0)
{
?>
<!--
<div id="ie5menu" class="skin1" onMouseover="highlightie5()" onMouseout="lowlightie5()">
	<div class="menuitems">
		<a href="#" class="txt_10b" onClick="javascript:copyitt(3);">
			Print	
		</a>
	</div>
	<div class="menuitems">
		<a href="#" class="txt_10b" onClick="javascript:copyitt(1);">
		Email
		</a>		
	</div>		
	<div class="menuitems">
		<a href="#" class="txt_10b" onClick="javascript:copyitt(2);">
			Stop
		</a>
	</div>
</div>
-->
<?php 
}
?>
	<table class="table table-striped table-bordered" width="100%" border="0" cellspacing="1"  cellpadding="1" >
			<tr class='grythead' height="20" >
				<td  align="center" onMouseDown="get_value(0);">Renew</td>
				<td  align="center" onMouseDown="get_value(0);">Created/ Changed</td>
				<td  align="center" onMouseDown="get_value(0);">Drug</td>
				
				<td  align="center"onmousedown="get_value(0);" >Dosage</td>
				<!--<td align="center">Unit</td>-->
				<td  align="center" onMouseDown="get_value(0);">Qty.</td>
				<td  align="center" onMouseDown="get_value(0);">Provider</td>
				<td  align="center" onMouseDown="get_value(0);">Notes</td>
				<td  align='center' onMouseDown="get_value(0);">Stop Date(Provider Name)</td>
				<!--<td align="center" onmousedown="get_value(0);">Substitution</td> -->	
				<td  align='center' >Refilled-by</td>	
				<!--<td width="20">&nbsp;</td>-->
			</tr>
			<form name="it">
			<?php
			
			while($rss = imw_fetch_array($vsql_c))
			{
				$i++;
			?>			
				
				<tr  height="20"   <? if($rss['stop']==5) { echo("style='color:#FF0000'");}?>>
				<td  align="center" onMouseDown="get_value(<?=$rss['id']?>);" >
					<?php
						if ($rss['stop_date'] && getNumber($rss['stop_date']) != "00000000000000")
						{
							echo "R";
						}
						else
						{
					?>		
							<a href="javascript:reLocate(<?php echo $rss['id'];?>);" class="butn">
							R
							</a>						
					<?php	
						}
					?>
				</td>
				<td align="center" onMouseDown="get_value(<?=$rss['id']?>);" >
						<?php
							if ($rss['date_modified'] && getNumber($rss['date_modified']) != "00000000") {									
								$tmp_date = $rss['date_modified'];
								$create_date = get_date_format($rss['date_modified']);		
								echo $create_date;
							}
						?>
						</a>
					</td>
					<td  onMouseDown="get_value(<?=$rss['id']?>);">
						&nbsp; <?=$rss['drug']?>
						</a>
					</td>					
					<td  align="center" onMouseDown="get_value(<?=$rss['id']?>);"><?=$rss['size']?>&nbsp;<?=$dos?></td>					
					<td  align="center" onMouseDown="get_value(<?=$rss['id']?>);"><?php echo $rss['quantity']."&nbsp;".$rss['quantity_unit'];?></td>
					<td  align="center" onMouseDown="get_value(<?=$rss['id']?>);">
						<?
					
						$provQry = "SELECT	
										fname,
										mname,
										lname
									FROM 
										users 
									WHERE
										id = $rss[filled_by_id]";
						
						$provSql = @imw_query($provQry);
						$provRt = @imw_fetch_array($provSql);
						if($provRt{'lname'} == 'Administrator')
						{
						 
						}
						else
						{
						
						 echo $provRt['fname'].' '.$provRt['lname'].' '.$provRt['mname'];
						} 
						?>
					</td>
					<td onMouseDown="get_value(<?=$rss['id']?>);"><?=$rss['note']?></td>
					<?php
						if ($rss['stop_date'] && $rss['stop_date'] != "0000-00-00 00:00:00")
						{									
							$tmp_date = $rss['stop_date'];
							list($year, $month, $day) = split('-',$tmp_date);													
							list($da, $timey) = split(' ',$day);	
							$create_date = $month."-".$da."-".$year." ".$timey;			
							echo("<td align='center'>".$create_date."(".$rss['stop_facility'].")</td>");
						}
						else
						{
							echo("<td></td>");
						}						
					?>
					
					<td align="center" onMouseDown="get_value(<?=$rss['id']?>);">
						<!-------------- REFILLED BY----------->
						<select name="sel_refilled_by_<?=$rss['id']?>" id="sel_refilled_by_<?=$rss['id']?>" class='txt_10'>
						<?php
							//=========GET USER INFORMATION================
							$sql="SELECT 
										id,
										fname,
										mname,
										lname		
									FROM 
										`users`
									ORDER BY
										lname, fname, mname
									";
							$rez = sqlStatement($sql);								
							for($i=0;$row=sqlFetchArray($rez);$i++)
							{									
								if($row["id"])
								{	
									if(!empty($rss['refilled_by']))
									{
										$selected = ($rss['refilled_by'] == $row["id"]) ? "selected" : "";
									}
									else
									{
										$selected = ($_SESSION['authUserID'] == $row["id"]) ? "selected" : "";																						
									}
									
									$fullName = $row["lname"].", ".$row["fname"]." ".$row["mname"];									
									echo "<option value=\"".$row["id"]."\" $selected>".$fullName."</option>";
								}
							}								
						?>
						</select>						
					</td>
				</tr>
			<?php
			}
			?>
				<tr>
					<td colspan="10" bgcolor="#FFFFFF">
					</td>
				</tr>
				<?php
				$tdate	= date("Y-m-d H:i:s");
				$sqlQry ="	SELECT 
								fname,
								mname,
								lname
							FROM
								`users`
							WHERE 
								username='".$_SESSION{"authUser"}."'
						 ";
						 
				$qryRes = imw_query($sqlQry);
				$res	= imw_fetch_array($qryRes);
				?>
				<input type="hidden" name="stop_date" value="<?=$tdate?>">
				<input type="hidden" name="stop_prid" value="<?php echo $res{"fname"}.' '.$res{"lname"};?>">
				<input type="hidden" name="get_id" value="">
				</form>
	</table>
<?php 
if($count>0)
{
?>
	<script language="JavaScript1.2">
	if (document.all && window.print)
	{
		ie5menu.className = menuskin;
		document.oncontextmenu = showmenuie5;
		document.body.onclick = hidemenuie5;
	}
	</script>
<?php 
} 
?>
</body>
</html>