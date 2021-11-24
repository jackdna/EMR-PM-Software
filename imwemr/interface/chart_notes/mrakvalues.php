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
*/
?>
<?php 
error_reporting(1);
$sqlVisionQry = imw_query("SELECT *, chart_vis_master.id as id_chart_vis_master,
k_od as vis_ak_od_k, slash_od as vis_ak_od_slash, x_od as vis_ak_od_x, 
k_os as vis_ak_os_k, slash_os as vis_ak_os_slash, x_os as vis_ak_os_x, ex_desc as vis_dis_near_desc
FROM chart_master_table
LEFT JOIN chart_left_cc_history ON chart_left_cc_history.form_id = chart_master_table.id
LEFT JOIN chart_vis_master ON chart_vis_master.form_id = chart_master_table.id
LEFT JOIN chart_ak ON chart_vis_master.id = chart_ak.id_chart_vis_master
WHERE chart_master_table.patient_id = '".$_SESSION["patient"]."'
ORDER BY chart_master_table.date_of_service DESC , chart_master_table.id DESC 
LIMIT 0 , 1");
$MRHASVALUESORNOT=false;
$vis_mr_none_given="";
$arrMRGiven = array();

if(imw_num_rows($sqlVisionQry)>0){
	$sqlVisionRow = imw_fetch_assoc($sqlVisionQry);
	extract($sqlVisionRow);
	$MRHASVALUESORNOT=true;
	//$vis_mr_none_given="";1,3 whichever is done by Doctor show that, if alll are done by Tech then show MR1
}

if(!empty($id_chart_vis_master)){
	$vis_mr_none_given="";
	$sql = "SELECT  
			c1.*,
			c2.sph as sph_r, c2.cyl as cyl_r, c2.axs as axs_r, c2.ad as ad_r, c2.prsm_p as prsm_p_r, c2.prism as prism_r, c2.slash as slash_r, c2.sel_1 as sel_1_r, c2.sel_2 as sel_2_r, 
			c2.txt_1 as txt_1_r, c2.txt_2 as txt_2_r, c2.sel2v as sel2v_r,
						
			c3.sph as sph_l, c3.cyl as cyl_l, c3.axs as axs_l, c3.ad as ad_l, c3.prsm_p as prsm_p_l, c3.prism as prism_l, c3.slash as slash_l, c3.sel_1 as sel_1_l, c3.sel_2 as sel_2_l, 
			c3.txt_1 as txt_1_l, c3.txt_2 as txt_2_l, c3.sel2v as sel2v_l
			
			FROM chart_pc_mr c1
			LEFT JOIN chart_pc_mr_values c2 ON c1.id = c2.chart_pc_mr_id AND c2.site='OD'
			LEFT JOIN chart_pc_mr_values c3 ON c1.id = c3.chart_pc_mr_id AND c3.site='OS'	
			WHERE c1.id_chart_vis_master='".$id_chart_vis_master."' 
			AND c1.delete_by = '0' AND c1.ex_type='MR'
			ORDER BY c1.ex_number 
		";
	$res = sqlStatement($sql);
	for($i=1; $row=sqlFetchArray($res);$i++){
		$cc= $row["ex_number"];
		if(!empty($row["mr_none_given"])){ $vis_mr_none_given.=$row["mr_none_given"].", "; }
		
		if($cc==1){		
			$vis_mr_od_s= $row["sph_r"];
			$vis_mr_od_c= $row["cyl_r"];
			$vis_mr_od_a= $row["axs_r"];
			$vis_mr_od_add= $row["ad_r"];
			$vis_mr_od_txt_1= $row["txt_1_r"];
			$vis_mr_od_txt_2= $row["txt_2_r"];

			$vis_mr_os_s= $row["sph_l"];
			$vis_mr_os_c= $row["cyl_l"];
			$vis_mr_os_a= $row["axs_l"];
			$vis_mr_os_add= $row["ad_l"];
			$vis_mr_os_txt_1= $row["txt_1_l"];
			$vis_mr_os_txt_2= $row["txt_2_l"];

			$vis_mr_desc=$row["ex_desc"];
		
		}else if($cc==2){
			$vis_mr_od_given_s= $row["sph_r"];
			$vis_mr_od_given_c= $row["cyl_r"];
			$vis_mr_od_given_a= $row["axs_r"];
			$vis_mr_od_given_add= $row["ad_r"];
			$vis_mr_od_given_txt_1= $row["txt_1_r"];
			$vis_mr_od_given_txt_2= $row["txt_2_r"];

			$vis_mr_os_given_s= $row["sph_l"];
			$vis_mr_os_given_c= $row["cyl_l"];
			$vis_mr_os_given_a= $row["axs_l"];
			$vis_mr_os_given_add= $row["ad_l"];
			$vis_mr_os_given_txt_1= $row["txt_1_l"];
			$vis_mr_os_given_txt_2= $row["txt_2_l"];

			$vis_mr_desc_other=$row["ex_desc"];
		}else if($cc==3){
			$visMrOtherOdS_3= $row["sph_r"];
			$visMrOtherOdC_3= $row["cyl_r"];
			$visMrOtherOdA_3= $row["axs_r"];
			$visMrOtherOdAdd_3= $row["ad_r"];
			$visMrOtherOdTxt1_3= $row["txt_1_r"];
			$visMrOtherOdTxt2_3= $row["txt_2_r"];

			$visMrOtherOsS_3= $row["sph_l"];
			$visMrOtherOsC_3= $row["cyl_l"];
			$visMrOtherOsA_3= $row["axs_l"];
			$visMrOtherOsAdd_3= $row["ad_l"];
			$visMrOtherOsTxt1_3= $row["txt_1_l"];
			$visMrOtherOsTxt2_3= $row["txt_2_l"];

			$vis_mr_desc_3=$row["ex_desc"];
		}
	}	
}


$akHasvalue=false;
$mrHasValue=false;

if(strlen($vis_mr_none_given)>3){	// it will just check if any word exist in column
	$arrMRGiven = explode(",", $vis_mr_none_given);
}

if(in_array("None", $arrMRGiven) || sizeof($arrMRGiven)<=0){
	$showGiven="style='display:none;'";
	$showGivenTemp="style='display:block;'";
}else{
$showGiven="style='display:block;'";
$showGivenTemp="style='display:none;'";
}

?>

<div id="contactLensMR" style="text-align:left; margin-top:3px; display:none;"><span id="imgPrintMr" onClick="javascript:print_CL_Mr();" class="printmr" title="Print MR" ></span>&nbsp;&nbsp;</div>


<table width="100%" cellpadding="0"  border="0"cellspacing="1" <?php // echo($showGiven);?>>
<tr>
<td valign="top">
<div class="fl" style="width:80%; margin-right:10%">
<table style="margin-left:10px;">
 <?php
			if(
				($vis_mr_od_s || $vis_mr_od_c || $vis_mr_od_a || ($vis_mr_od_add && $vis_mr_od_add!='+') || ($vis_mr_od_txt_1 && $vis_mr_od_txt_1!='20/')
				||
				($vis_mr_od_txt_2 && $vis_mr_od_txt_2!='20/')
				||
				$vis_mr_os_s || $vis_mr_os_c || $vis_mr_os_a || ($vis_mr_os_add && $vis_mr_os_add!='+') || ($vis_mr_os_txt_1 && $vis_mr_os_txt_1!='20/')
				||
				($vis_mr_os_txt_2 && $vis_mr_os_txt_2!='20/'))
				){
				$Mr1st = true;
				$mrHasValue=true;
				?>
				
		<tr style="height:20px;">
			<td colspan="5" class="text12b"></td>
		</tr>
	   <tr style="height:20px;">
			<td colspan="5" class="text12b">MR 1st</td>
		</tr>
		 <tr style="height:20px;">
				<td style="width:60px" class="blue_color txt_10b">&nbsp;</td> 
				<td style="width:100px" class="txt_11b">Sphere</td>												
				<td style="width:100px" class="txt_11b">Cylinder</td>
				<td style="width:100px" class="txt_11b">Axis</td>  
				<td style="width:100px" class="txt_11b">ADD</td>
				<?php
				
				if($vis_ak_od_k!="" ||$vis_ak_od_slash!="" || $vis_ak_od_x!="" ||$vis_ak_os_k!="" ||$vis_ak_os_slash!="" || $vis_ak_os_x!=""){?>
    			<td class="txt_11b" colspan="4">AK</td>
    			<?php $akHasvalue=true;
				}?>
		</tr>
	 <?php
 }
if($Mr1st == true){
	?>
		<tr style="height:20px;">
				<td class="blue_color text13 textBold">OD</td> 
				<td class="text12"><?php if($vis_mr_od_s){ echo("$vis_mr_od_s"); }?></td>												
				<td class="text12"><?php if($vis_mr_od_c){ echo("$vis_mr_od_c"); }?></td>
				<td class="text12"><?php if($vis_mr_od_a){ echo($vis_mr_od_a."&nbsp;&nbsp;".$vis_mr_od_txt_1); }?></td>  
				<td class="text12"><?php 
				    if(($vis_mr_od_add!=""))
				    {
				        echo($vis_mr_od_add."&nbsp;&nbsp;".$vis_mr_od_txt_2);
				    }
				    ?>
				</td>
				<?php
				 if($vis_ak_od_k!="" ||$vis_ak_od_slash!="" || $vis_ak_od_x!="" ||$vis_ak_os_k!="" ||$vis_ak_os_slash!="" || $vis_ak_os_x!="")
				 {
				 ?>
    				<td class="text12" ><span class="txt_11b">K:&nbsp;</span><?php  print($vis_ak_od_k."&nbsp;/&nbsp;".$vis_ak_od_slash);?></td>
    				<td class="text12"><span class="txt_11b">&nbsp;X&nbsp;</span><?php  print($vis_ak_od_x."&#176;");?></td>
    				<td>&nbsp;</td>
				<?php } ?>
		
		</tr>
		<tr style="height:20px;">
				<td class="green_color text13 textBold">OS</td> 
				<td class="text12"><?php if($vis_mr_os_s){ echo("$vis_mr_os_s"); }?></td>												
				<td class="text12"><?php if($vis_mr_os_c){ echo("$vis_mr_os_c"); }?></td>
				<td class="text12"><?php if($vis_mr_os_a){ echo($vis_mr_os_a."&nbsp;&nbsp;".$vis_mr_os_txt_2); }?></td>  
				<td class="text12"><?php if(($vis_mr_os_txt_2!="" && $vis_mr_os_txt_2!='20/')){ echo($vis_mr_os_add."&nbsp;&nbsp;".$vis_mr_os_txt_2); }?></td>
		
				<?php
				 if($vis_ak_od_k!="" ||$vis_ak_od_slash!="" || $vis_ak_od_x!="" ||$vis_ak_os_k!="" ||$vis_ak_os_slash!="" || $vis_ak_os_x!=""){?>
				<td  class="text12" ><span class="txt_11b">K:&nbsp;</span><?php  print($vis_ak_os_k."&nbsp;/&nbsp;".$vis_ak_os_slash);?></td>
				<td  class="text12"><span class="txt_11b">&nbsp;X&nbsp;</span><?php  print($vis_ak_os_x."&#176;");?></td>
				<td>&nbsp;</td>
				<?php } ?>		
		</tr>	
		<tr style="height:20px;">
        	<?php if($vis_mr_desc!=''){?>
			<td class="text" colspan="5"><span class="txt_11b">MR Description :&nbsp;</span><?php echo $vis_mr_desc; ?></td>
			<?php
			}
				 if($vis_ak_od_k!="" ||$vis_ak_od_slash!="" || $vis_ak_od_x!="" ||$vis_ak_os_k!="" ||$vis_ak_os_slash!="" || $vis_ak_os_x!=""){?>
					<td  class="text12" valign="top" colspan="4" >
                    	<?php if($vis_dis_near_desc !=""){ ?>
                    	<span class="txt_11b">AK Description :&nbsp;</span><?php echo $vis_dis_near_desc;?>
                        <?php } ?>
                        </td>
			<?php } ?>		
		</tr>		
   
<?php } ?>
</table>
</div>
	<?php
	if(($vis_mr_od_given_s || $vis_mr_od_given_given_c || $vis_mr_od_given_given_a || ($vis_mr_od_given_given_add && $vis_mr_od_given_given_add!='+'))
		||
		($vis_mr_od_given_given_txt_1 && $vis_mr_od_given_txt_1!='20/')
		||
		($vis_mr_od_given_txt_2 && $vis_mr_od_given_txt_2!='20/')
		||
		($vis_mr_os_given_s || $vis_mr_os_given_c || $vis_mr_os_given_a || ($vis_mr_os_given_add && $vis_mr_os_given_add!='+'))
		||
		($vis_mr_os_given_txt_1 && $vis_mr_os_given_txt_1!='20/')
		||
		($vis_mr_os_given_txt_2 && $vis_mr_os_given_txt_2!='20/')
		){?>
	
<div class="fl" style="width:80%; margin-right:10%">    	
<table style="margin-left:10px;">
 <?php
			if(
				($vis_mr_od_given_s || $vis_mr_od_given_c || $vis_mr_od_given_a || $vis_mr_od_given_add || ($vis_mr_od_given_txt_1 && $vis_mr_od_given_txt_1!='20/')
				||
				($vis_mr_od_given_txt_2 && $vis_mr_od_given_txt_2!='20/')
				||
				$vis_mr_os_given_s || $vis_mr_os_given_c || $vis_mr_os_given_a || $vis_mr_os_given_add || ($vis_mr_os_given_txt_1 && $vis_mr_os_given_txt_1!='20/')
				||
				($vis_mr_os_given_txt_2 && $vis_mr_os_given_txt_2!='20/'))
				){
				$Mr2ng = true;
				$mr2HasValue=true;
				?>
				
	  	 <tr style="height:20px;">
			<td colspan="5" class="text12b">MR 2nd</td>
		</tr>
		 <tr style="height:20px;">
				<td style="width:60px" class="blue_color txt_10b">&nbsp;</td> 
				<td style="width:100px" class="txt_11b">Sphere</td>												
				<td style="width:100px" class="txt_11b">Cylinder</td>
				<td style="width:100px" class="txt_11b">Axis</td>  
				<td style="width:100px" class="txt_11b">ADD</td>
				<?php
				
				if($vis_ak_od_k!="" ||$vis_ak_od_slash!="" || $vis_ak_od_x!="" ||$vis_ak_os_k!="" ||$vis_ak_os_slash!="" || $vis_ak_os_x!=""){?>
    			<td class="txt_11b" colspan="4">AK</td>
    			<?php $akHasvalue=true;
				}?>
		</tr>
	 <?php
 }
if($Mr2ng == true){?>
		<tr style="height:20px;">
				<td class="blue_color text13 textBold">OD</td> 
				<td class="text12"><?php if($vis_mr_od_given_s){ echo("$vis_mr_od_given_s"); }?></td>												
				<td class="text12"><?php if($vis_mr_od_given_c){ echo("$vis_mr_od_given_c"); }?></td>
				<td class="text12"><?php if($vis_mr_od_given_a){ echo($vis_mr_od_given_a."&nbsp;&nbsp;".$vis_mr_od_given_txt_1); }?></td>  
				<td class="text12"><?php if(($vis_mr_od_given_s!="" and $vis_mr_od_given_add!="" and $vis_mr_od_given_c!="" and $vis_mr_od_given_a!="" and $vis_mr_od_given_txt_1 and $vis_mr_od_given_txt_1!='20/')){ echo($vis_mr_od_given_add."&nbsp;&nbsp;".$vis_mr_od_given_txt_2); }?></td>
				<?php
				 if($vis_ak_od_k!="" ||$vis_ak_od_slash!="" || $vis_ak_od_x!="" ||$vis_ak_os_k!="" ||$vis_ak_os_slash!="" || $vis_ak_os_x!=""){?>
				 <td class="text12" ><span class="txt_11b">K:&nbsp;</span><?php  print($vis_ak_od_k."&nbsp;/&nbsp;".$vis_ak_od_slash);?></td>
				 <td class="text12"><span class="txt_11b">&nbsp;X&nbsp;</span><?php  print($vis_ak_od_x."&#176;");?></td>
				<td>&nbsp;</td>
				<?php } ?>
		
		</tr>
		<tr style="height:20px;">
				<td class="green_color text13 textBold">OS</td> 
				<td class="text12"><?php if($vis_mr_os_given_s){ echo("$vis_mr_os_given_s"); }?></td>												
				<td class="text12"><?php if($vis_mr_os_given_c){ echo("$vis_mr_os_given_c"); }?></td>
				<td class="text12"><?php if($vis_mr_os_given_a){ echo($vis_mr_os_given_a."&nbsp;&nbsp;".$vis_mr_os_given_txt_2); }?></td>  
				<td class="text12"><?php if(($vis_mr_os_given_txt_2!="" && $vis_mr_os_given_txt_2!='20/')){ echo($vis_mr_os_given_add."&nbsp;&nbsp;".$vis_mr_os_given_txt_2); }?></td>
		
				<?php
				 if($vis_ak_od_k!="" ||$vis_ak_od_slash!="" || $vis_ak_od_x!="" ||$vis_ak_os_k!="" ||$vis_ak_os_slash!="" || $vis_ak_os_x!=""){?>
				<td  class="text12" ><span class="txt_11b">K:&nbsp;</span><?php  print($vis_ak_os_k."&nbsp;/&nbsp;".$vis_ak_os_slash);?></td>
				<td  class="text12"><span class="txt_11b">&nbsp;X&nbsp;</span><?php  print($vis_ak_os_x."&#176;");?></td>
				<td>&nbsp;</td>
				<?php } ?>		
		</tr>	
		<tr style="height:20px;">
        	<?php if($vis_mr_desc_other!=''){?>
			<td class="text" colspan="5"><span class="txt_11b">MR Description :&nbsp;</span><?php echo $vis_mr_desc_other ; ?></td>
			<?php
			}
				 if($vis_ak_od_k!="" ||$vis_ak_od_slash!="" || $vis_ak_od_x!="" ||$vis_ak_os_k!="" ||$vis_ak_os_slash!="" || $vis_ak_os_x!=""){?>
					<td  class="text12" valign="top" colspan="4" >
                    <?php if($vis_dis_near_desc !=""){ ?>
                    <span class="txt_11b">AK Description :&nbsp;</span><?php if($vis_dis_near_desc !=""){ echo $vis_dis_near_desc ;}?>
                    <?php } ?>
                    </td>
			<?php } ?>		
		</tr>		
   
<?php } ?>
</table>
</div>		
<?php 

}
?>
<!-- To Show MR 3 Values-->
 <?php
if(
	($visMrOtherOdS_3 || $visMrOtherOdC_3  || $visMrOtherOdA_3 || $visMrOtherOdA_3 || ($visMrOtherOdTxt1_3 && $visMrOtherOdTxt1_3!='20/') 
	||
	($visMrOtherOdTxt2_3  && $visMrOtherOdTxt2_3 !='20/') 
	||
	$visMrOtherOsS_3 || $visMrOtherOsC_3 || $visMrOtherOsA_3 || ($visMrOtherOsAdd_3 && $visMrOtherOsAdd_3!='+') || ($visMrOtherOsTxt1_3 && $visMrOtherOsTxt1_3!='20/')  
	||
	($visMrOtherOsTxt2_3 && $visMrOtherOsTxt2_3!='20/'))
	){?>
<div class="fl" style="width:80%; margin-right:10%; margin-top:10px;">    
<table style="margin-left:10px;">
 <?php 
			if(
				($visMrOtherOdS_3 || $visMrOtherOdC_3  || $visMrOtherOdA_3 || $visMrOtherOdA_3 || ($visMrOtherOdTxt1_3 && $visMrOtherOdTxt1_3!='20/')
				||
				($visMrOtherOdTxt2_3  && $visMrOtherOdTxt2_3 !='20/')
				||
				$visMrOtherOsS_3  || $visMrOtherOsC_3 || $visMrOtherOsA_3 || $visMrOtherOsAdd_3 || ($visMrOtherOsTxt1_3 && $visMrOtherOsTxt1_3!='20/')
				||
				($visMrOtherOsTxt2_3 && $visMrOtherOsTxt2_3!='20/'))
				){
				$Mr3rd = true;
				$mr2HasValue=true;
				?>
				
	  	 <tr style="height:20px;">
			<td colspan="5" class="text12b">MR 3rd</td>
		</tr>
		 <tr style="height:20px;">
				<td style="width:60px" class="blue_color txt_10b">&nbsp;</td> 
				<td style="width:100px"  class="txt_11b">Sphere</td>												
				<td style="width:100px" class="txt_11b">Cylinder</td>
				<td style="width:100px" class="txt_11b">Axis</td>  
				<td style="width:100px" class="txt_11b">ADD</td>
				<?php
				
				if($vis_ak_od_k!="" ||$vis_ak_od_slash!="" || $vis_ak_od_x!="" ||$vis_ak_os_k!="" ||$vis_ak_os_slash!="" || $vis_ak_os_x!=""){?>
    			<td class="txt_11b" colspan="4">AK</td>
    			<?php $akHasvalue=true;
				}?>
		</tr>
	 <?php
 }
if($Mr3rd == true){?>
		<tr style="height:20px;">
				<td class="blue_color text13 textBold">OD</td> 
				<td class="text12"><?php if($visMrOtherOdS_3){ echo("$visMrOtherOdS_3"); }?></td>												
				<td class="text12"><?php if($visMrOtherOdC_3 ){ echo("$visMrOtherOdC_3 "); }?></td>
				<td class="text12"><?php if($visMrOtherOdA_3){ echo($visMrOtherOdA_3."&nbsp;&nbsp;".$visMrOtherOdTxt1_3); }?></td>  
				<td class="text12"><?php if(($visMrOtherOdS_3!="" and $visMrOtherOdA_3!="" and $visMrOtherOdC_3 !="" and $visMrOtherOdAdd_3!="" and $visMrOtherOdTxt1_3 and $visMrOtherOdTxt1_3!='20/')){ echo($visMrOtherOdAdd_3."&nbsp;&nbsp;".$visMrOtherOdTxt2_3); }?></td>
				<?php
				 if($vis_ak_od_k!="" ||$vis_ak_od_slash!="" || $vis_ak_od_x!="" ||$vis_ak_os_k!="" ||$vis_ak_os_slash!="" || $vis_ak_os_x!=""){?>
				 <td class="text12" ><span class="txt_11b">K:&nbsp;</span><?php  print($vis_ak_od_k."&nbsp;/&nbsp;".$vis_ak_od_slash);?></td>
				 <td class="text12"><span class="txt_11b">&nbsp;X&nbsp;</span><?php  print($vis_ak_od_x."&#176;");?></td>
				<td >&nbsp;</td>
				<?php } ?>
		
		</tr>
		<tr style="height:20px;">
				<td class="green_color text13 textBold">OS</td> 
				<td class="text12"><?php if($visMrOtherOsS_3){ echo($visMrOtherOsS_3); }?></td>												
				<td class="text12"><?php if($visMrOtherOsC_3){ echo("$visMrOtherOsC_3"); }?></td>
				<td class="text12"><?php if($visMrOtherOsA_3){ echo($visMrOtherOsA_3."&nbsp;&nbsp;".$visMrOtherOsTxt2_3); }?></td>  
				<td class="text12"><?php if(($visMrOtherOsTxt2_3!="" && $visMrOtherOsTxt2_3!='20/')){ echo($visMrOtherOsAdd_3."&nbsp;&nbsp;".$visMrOtherOsTxt2_3); }?></td>
		
				<?php
				 if($vis_ak_od_k!="" ||$vis_ak_od_slash!="" || $vis_ak_od_x!="" ||$vis_ak_os_k!="" ||$vis_ak_os_slash!="" || $vis_ak_os_x!=""){?>
				<td  class="text12" ><span class="txt_11b">K:&nbsp;</span><?php  print($vis_ak_os_k."&nbsp;/&nbsp;".$vis_ak_os_slash);?></td>
				<td  class="text12"><span class="txt_11b">&nbsp;X&nbsp;</span><?php  print($vis_ak_os_x."&#176;");?></td>
				<td>&nbsp;</td>
				<?php } ?>		
		</tr>	
		<tr style="height:20px;">
        	<?php if($vis_mr_desc_3!=''){?>
			<td class="txt_11" colspan="5"><span class="txt_11b">MR Description :&nbsp;</span><?php echo $vis_mr_desc_3 ; ?></td>
			<?php
			}
				 if($vis_ak_od_k!="" ||$vis_ak_od_slash!="" || $vis_ak_od_x!="" ||$vis_ak_os_k!="" ||$vis_ak_os_slash!="" || $vis_ak_os_x!=""){?>
					<td  class="text12" valign="top" colspan="4" >
                    <?php if($vis_dis_near_desc !=""){ ?>
                    <span class="txt_11b">AK Description :&nbsp;</span><?php if($vis_dis_near_desc !=""){ echo $vis_dis_near_desc ;}?>
                    <?php } ?>
                    </td>
			<?php } ?>		
		</tr>		
   
<?php  } ?>
</table>
</div>
<div style="clear:both; width:100%; height:20px;"></div>
<?php 
}
?>
<!-- End To Show MR 3 Values-->
	</td>
</tr>
</table>

<!-- Temp Code to Show MR Latest Values Irespective of none given--->
<!--<?php

 if($MRHASVALUESORNOT==true && (in_array("None", $arrMRGiven) || sizeof($arrMRGiven)<=0) && 5==8){?>
<table width="100%" cellpadding="0"  border="0" cellspacing="1" <?php echo($showGivenTemp);?>>
<tr>
<td valign="top">
 <?php
			if(
				($vis_mr_od_s || $vis_mr_od_c || $vis_mr_od_a || $vis_mr_od_add || ($vis_mr_od_txt_1 && $vis_mr_od_txt_1!='20/')
				||
				($vis_mr_od_txt_2 && $vis_mr_od_txt_2!='20/')
				||
				$vis_mr_os_s || $vis_mr_os_c || $vis_mr_os_a || $vis_mr_os_add || ($vis_mr_os_txt_1 && $vis_mr_os_txt_1!='20/')
				||
				($vis_mr_os_txt_2 && $vis_mr_os_txt_2!='20/'))
				&& 
				(in_array("None", $arrMRGiven) || sizeof($arrMRGiven)<=0)				
				){
				$Mr1st = true;
				$mrHasValue=true;
				?>
<div class="fl" style="width:80%; margin-right:10%">                
<table>
	   <tr>
			<td colspan="5" class="text12b">MR 1st</td>
		</tr>
		 <tr>
				<td style="width:60px" class="blue_color txt_10b">&nbsp;</td> 
				<td style="width:100px"  class="txt_11b">Sphere</td>												
				<td style="width:100px" class="txt_11b">Cylinder</td>
				<td style="width:100px" class="txt_11b">Axis</td>  
				<td style="width:100px" class="txt_11b">ADD</td>
				<?php
				
				if($vis_ak_od_k!="" ||$vis_ak_od_slash!="" || $vis_ak_od_x!="" ||$vis_ak_os_k!="" ||$vis_ak_os_slash!="" || $vis_ak_os_x!=""){?>
    			<td class="txt_11b" colspan="4" >AK</td>
    			<?php $akHasvalue=true;
				}?>
		</tr>
	 <?php
 }
if($Mr1st == true){?>
		<tr style="height:20px;">
				<td class="blue_color text13 textBold">OD</td> 
				<td class="text12"><?php if($vis_mr_od_s){ echo("$vis_mr_od_s"); }?></td>												
				<td class="text12"><?php if($vis_mr_od_c){ echo("$vis_mr_od_c"); }?></td>
				<td class="text12"><?php if($vis_mr_od_a){ echo($vis_mr_od_a."&nbsp;&nbsp;".$vis_mr_od_txt_1); }?></td>  
				<td class="text12"><?php if(($vis_mr_od_s!="" and $vis_mr_od_add!="" and $vis_mr_od_c!="" and $vis_mr_od_a!="" and $vis_mr_od_txt_1 and $vis_mr_od_txt_1!='20/')){ echo($vis_mr_od_add."&nbsp;&nbsp;".$vis_mr_od_txt_2); }?></td>
				<?php
				 if($vis_ak_od_k!="" ||$vis_ak_od_slash!="" || $vis_ak_od_x!="" ||$vis_ak_os_k!="" ||$vis_ak_os_slash!="" || $vis_ak_os_x!=""){?>
				 <td class="text12" ><span class="txt_11b">K:&nbsp;</span><?php  print($vis_ak_od_k."&nbsp;/&nbsp;".$vis_ak_od_slash);?></td>
				 <td class="text12"><span class="txt_11b">&nbsp;X&nbsp;</span><?php  print($vis_ak_od_x."&#176;");?></td>
				<td >&nbsp;</td>
				<?php } ?>
		
		</tr>
		<tr style="height:20px;">
				<td class="green_color text13 textBold">OS</td> 
				<td class="text12"><?php if($vis_mr_os_s){ echo("$vis_mr_os_s"); }?></td>												
				<td class="text12"><?php if($vis_mr_os_c){ echo("$vis_mr_os_c"); }?></td>
				<td class="text12"><?php if($vis_mr_os_a){ echo($vis_mr_os_a."&nbsp;&nbsp;".$vis_mr_os_txt_2); }?></td>  
				<td class="text12"><?php if(($vis_mr_os_txt_2!="" && $vis_mr_os_txt_2!='20/')){ echo($vis_mr_os_add."&nbsp;&nbsp;".$vis_mr_os_txt_2); }?></td>
		
				<?php
				 if($vis_ak_od_k!="" ||$vis_ak_od_slash!="" || $vis_ak_od_x!="" ||$vis_ak_os_k!="" ||$vis_ak_os_slash!="" || $vis_ak_os_x!=""){?>
				<td  class="text12" ><span class="txt_11b">K:&nbsp;</span><?php  print($vis_ak_os_k."&nbsp;/&nbsp;".$vis_ak_os_slash);?></td>
				<td  class="text12"><span class="txt_11b">&nbsp;X&nbsp;</span><?php  print($vis_ak_os_x."&#176;");?></td>
				<td>&nbsp;</td>
				<?php } ?>		
		</tr>	
		<tr style="height:20px;">
			<?php if($vis_mr_desc!=''){?>
            <td class="text" colspan="5"><span class="txt_11b">MR Description :&nbsp;</span><?php echo $vis_mr_desc; ?></td>
			<?php
			}
				 if($vis_ak_od_k!="" ||$vis_ak_od_slash!="" || $vis_ak_od_x!="" ||$vis_ak_os_k!="" ||$vis_ak_os_slash!="" || $vis_ak_os_x!=""){?>
					<td  class="txt_11" valign="top" colspan="4" >
                    <?php if($vis_dis_near_desc !=""){ ?>
                    <span class="txt_11b">AK Description :&nbsp;</span><?php echo $vis_dis_near_desc ;?>
                    <?php } ?>
                    </td>
			<?php } ?>		
		</tr>		
   </table>
   </div>
<?php }elseif(($vis_mr_od_given_s || $vis_mr_od_given_given_c || $vis_mr_od_given_given_a || $vis_mr_od_given_given_add)
		||
		($vis_mr_od_given_given_txt_1 && $vis_mr_od_given_txt_1!='20/')
		||
		($vis_mr_od_given_txt_2 && $vis_mr_od_given_txt_2!='20/')
		||
		($vis_mr_os_given_s || $vis_mr_os_given_c || $vis_mr_os_given_a || $vis_mr_os_given_add)
		||
		($vis_mr_os_given_txt_1 && $vis_mr_os_given_txt_1!='20/')
		||
		($vis_mr_os_given_txt_2 && $vis_mr_os_given_txt_2!='20/')
		){?>
<div class="fl" style="width:80%; margin-right:10%">		
<table style="margin-left:10px;">
 <?php
			if(
				($vis_mr_od_given_s || $vis_mr_od_given_c || $vis_mr_od_given_a || $vis_mr_od_given_add || ($vis_mr_od_given_txt_1 && $vis_mr_od_given_txt_1!='20/')
				||
				($vis_mr_od_given_txt_2 && $vis_mr_od_given_txt_2!='20/')
				||
				$vis_mr_os_given_s || $vis_mr_os_given_c || $vis_mr_os_given_a || $vis_mr_os_given_add || ($vis_mr_os_given_txt_1 && $vis_mr_os_given_txt_1!='20/')
				||
				($vis_mr_os_given_txt_2 && $vis_mr_os_given_txt_2!='20/'))
				&& 
				(in_array("None", $arrMRGiven) || sizeof($arrMRGiven)<=0)				
				){
				$Mr2ng = true;
				$mr2HasValue=true;
				?>
				
	  	 <tr>
			<td colspan="5" class="text12b">MR 2nd <?php //echo showDoctorName($provider_id); ?></td>
		</tr>
		 <tr style="height:20px;">
				<td style="width:60px" class="blue_color txt_11b">&nbsp;</td> 
				<td style="width:100px" class="txt_11b">Sphere</td>												
				<td style="width:100px" class="txt_11b">Cylinder</td>
				<td style="width:100px" class="txt_11b">Axis</td>  
				<td style="width:100px" class="txt_11b">ADD</td>
				<?php
				
				if($vis_ak_od_k!="" ||$vis_ak_od_slash!="" || $vis_ak_od_x!="" ||$vis_ak_os_k!="" ||$vis_ak_os_slash!="" || $vis_ak_os_x!=""){?>
    			<td class="txt_11b" colspan="4">AK</td>
    			<?php $akHasvalue=true;
				}?>
		</tr>
	 <?php
 }
if($Mr2ng == true){?>
		<tr style="height:20px;">
				<td class="blue_color text13 textBold">OD</td> 
				<td class="text12"><?php if($vis_mr_od_given_s){ echo("$vis_mr_od_given_s"); }?></td>												
				<td class="text12"><?php if($vis_mr_od_given_c){ echo("$vis_mr_od_given_c"); }?></td>
				<td class="text12"><?php if($vis_mr_od_given_a){ echo($vis_mr_od_given_a."&nbsp;&nbsp;".$vis_mr_od_given_txt_1); }?></td>  
				<td class="text12"><?php if(($vis_mr_od_given_s!="" and $vis_mr_od_given_add!="" and $vis_mr_od_given_c!="" and $vis_mr_od_given_a!="" and $vis_mr_od_given_txt_1 and $vis_mr_od_given_txt_1!='20/')){ echo($vis_mr_od_given_add."&nbsp;&nbsp;".$vis_mr_od_given_txt_2); }?></td>
				<?php
				 if($vis_ak_od_k!="" ||$vis_ak_od_slash!="" || $vis_ak_od_x!="" ||$vis_ak_os_k!="" ||$vis_ak_os_slash!="" || $vis_ak_os_x!=""){?>
				 <td class="text12" ><span class="txt_11b">K:&nbsp;</span><?php  print($vis_ak_od_k."&nbsp;/&nbsp;".$vis_ak_od_slash);?></td>
				 <td class="text12"><span class="txt_11b">&nbsp;X&nbsp;</span><?php  print($vis_ak_od_x."&#176;");?></td>
				<td >&nbsp;</td>
				<?php } ?>
		
		</tr>
		<tr style="height:20px;">
				<td class="green_color text13 textBold">OS</td> 
				<td class="text12"><?php if($vis_mr_os_given_s){ echo("$vis_mr_os_given_s"); }?></td>												
				<td class="text12"><?php if($vis_mr_os_given_c){ echo("$vis_mr_os_given_c"); }?></td>
				<td class="text12"><?php if($vis_mr_os_given_a){ echo($vis_mr_os_given_a."&nbsp;&nbsp;".$vis_mr_os_given_txt_2); }?></td>  
				<td class="text12"><?php if(($vis_mr_os_given_txt_2!="" && $vis_mr_os_given_txt_2!='20/')){ echo($vis_mr_os_given_add."&nbsp;&nbsp;".$vis_mr_os_given_txt_2); }?></td>
		
				<?php
				 if($vis_ak_od_k!="" ||$vis_ak_od_slash!="" || $vis_ak_od_x!="" ||$vis_ak_os_k!="" ||$vis_ak_os_slash!="" || $vis_ak_os_x!=""){?>
				<td  class="text12" ><span class="txt_11b">K:&nbsp;</span><?php  print($vis_ak_os_k."&nbsp;/&nbsp;".$vis_ak_os_slash);?></td>
				<td  class="text12"><span class="txt_11b">&nbsp;X&nbsp;</span><?php  print($vis_ak_os_x."&#176;");?></td>
				<td>&nbsp;</td>
				<?php } ?>		
		</tr>	
		<tr style="height:20px;">
			<?php if($vis_mr_desc_other!=''){?>
            <td class="text" colspan="5"><span class="text12">MR Description :&nbsp;</span><?php echo $vis_mr_desc_other ; ?></td>
			<?php
			}
				 if($vis_ak_od_k!="" ||$vis_ak_od_slash!="" || $vis_ak_od_x!="" ||$vis_ak_os_k!="" ||$vis_ak_os_slash!="" || $vis_ak_os_x!=""){?>
					<td  class="text12" valign="top" colspan="4" >
                    <?php if($vis_dis_near_desc !=""){ ?>
                    <span class="txt_11b">AK Description :&nbsp;</span><?php echo $vis_dis_near_desc;?><br />
					<?php } ?>
					</td>
			<?php } ?>		
		</tr>		
   
<?php } ?>
</table>
</div>		
<?php 

}else if(
	($visMrOtherOdS_3 || $visMrOtherOdC_3  || $visMrOtherOdA_3 || $visMrOtherOdA_3 || ($visMrOtherOdTxt1_3 && $visMrOtherOdTxt1_3!='20/')
	||
	($visMrOtherOdTxt2_3  && $visMrOtherOdTxt2_3 !='20/')
	||
	$visMrOtherOsS_3  || $visMrOtherOsC_3 || $visMrOtherOsA_3 || $visMrOtherOsAdd_3 || ($visMrOtherOsTxt1_3 && $visMrOtherOsTxt1_3!='20/')
	||
	($visMrOtherOsTxt2_3 && $visMrOtherOsTxt2_3!='20/'))
			
	){?>
<!--//To Show MR 3 Values-->
<div class="fl" style="width:80%; margin-right:10%">
<table style="margin-left:10px;">
 <?php 
			if(
				($visMrOtherOdS_3 || $visMrOtherOdC_3  || $visMrOtherOdA_3 || $visMrOtherOdA_3 || ($visMrOtherOdTxt1_3 && $visMrOtherOdTxt1_3!='20/')
				||
				($visMrOtherOdTxt2_3  && $visMrOtherOdTxt2_3 !='20/')
				||
				$visMrOtherOsS_3  || $visMrOtherOsC_3 || $visMrOtherOsA_3 || $visMrOtherOsAdd_3 || ($visMrOtherOsTxt1_3 && $visMrOtherOsTxt1_3!='20/')
				||
				($visMrOtherOsTxt2_3 && $visMrOtherOsTxt2_3!='20/'))
				&& 
				(in_array("None", $arrMRGiven) || sizeof($arrMRGiven)<=0)				
				){
				$Mr3rd = true;
				$mr2HasValue=true;
				?>
				
	  	 <tr style="height:20px;">
			<td colspan="5" class="text12b">MR 3rd <?php //echo showDoctorName($provider_id); ?></td>
		</tr>
		 <tr style="height:20px;">
				<td style="width:60px" class="blue_color txt_10b">&nbsp;</td> 
				<td style="width:100px" class="txt_11b">Sphere</td>												
				<td style="width:100px" class="txt_11b">Cylinder</td>
				<td style="width:100px" class="txt_11b">Axis</td>  
				<td style="width:100px" class="txt_11b">ADD</td>
				<?php
				
				if($vis_ak_od_k!="" ||$vis_ak_od_slash!="" || $vis_ak_od_x!="" ||$vis_ak_os_k!="" ||$vis_ak_os_slash!="" || $vis_ak_os_x!=""){?>
    			<td class="txt_11b" colspan="4">AK</td>
    			<?php $akHasvalue=true;
				}?>
		</tr>
	 <?php
 }
if($Mr3rd == true){?>
		<tr style="height:20px;">
				<td class="blue_color text13 textBold">OD</td> 
				<td class="text12"><?php if($visMrOtherOdS_3){ echo("$visMrOtherOdS_3"); }?></td>												
				<td class="text12"><?php if($visMrOtherOdC_3 ){ echo("$visMrOtherOdC_3 "); }?></td>
				<td class="text12"><?php if($visMrOtherOdA_3){ echo($visMrOtherOdA_3."&nbsp;&nbsp;".$visMrOtherOdTxt1_3); }?></td>  
				<td class="text12"><?php if(($visMrOtherOdS_3!="" and $visMrOtherOdA_3!="" and $visMrOtherOdC_3 !="" and $visMrOtherOdAdd_3!="" and $visMrOtherOdTxt1_3 and $visMrOtherOdTxt1_3!='20/')){ echo($visMrOtherOdAdd_3."&nbsp;&nbsp;".$visMrOtherOdTxt2_3); }?></td>
				<?php
				 if($vis_ak_od_k!="" ||$vis_ak_od_slash!="" || $vis_ak_od_x!="" ||$vis_ak_os_k!="" ||$vis_ak_os_slash!="" || $vis_ak_os_x!=""){?>
				 <td class="text12" ><span class="txt_11b">K:&nbsp;</span><?php  print($vis_ak_od_k."&nbsp;/&nbsp;".$vis_ak_od_slash);?></td>
				 <td class="text12"><span class="txt_11b">&nbsp;X&nbsp;</span><?php  print($vis_ak_od_x."&#176;");?></td>
				<td >&nbsp;</td>
				<?php } ?>
		
		</tr>
		<tr style="height:20px;">
				<td class="green_color text13 textBold">OS</td> 
				<td class="text12"><?php if($visMrOtherOsS_3){ echo($visMrOtherOsS_3); }?></td>												
				<td class="text12"><?php if($visMrOtherOsC_3){ echo("$visMrOtherOsC_3"); }?></td>
				<td class="text12"><?php if($visMrOtherOsA_3){ echo($visMrOtherOsA_3."&nbsp;&nbsp;".$visMrOtherOsTxt2_3); }?></td>  
				<td class="text12"><?php if(($visMrOtherOsTxt2_3!="" && $visMrOtherOsTxt2_3!='20/')){ echo($visMrOtherOsAdd_3."&nbsp;&nbsp;".$visMrOtherOsTxt2_3); }?></td>
		
				<?php
				 if($vis_ak_od_k!="" ||$vis_ak_od_slash!="" || $vis_ak_od_x!="" ||$vis_ak_os_k!="" ||$vis_ak_os_slash!="" || $vis_ak_os_x!=""){?>
				<td  class="text12" ><span class="txt_11b">K:&nbsp;</span><?php  print($vis_ak_os_k."&nbsp;/&nbsp;".$vis_ak_os_slash);?></td>
				<td  class="text12"><span class="txt_11b">&nbsp;X&nbsp;</span><?php  print($vis_ak_os_x."&#176;");?></td>
				<td>&nbsp;</td>
				<?php } ?>		
		</tr>	
		<tr style="height:20px;">
			<?php if($vis_mr_desc_3!=''){?>
            <td class="text12" colspan="5"><span class="txt_11b">MR Description :&nbsp;</span><?php echo $vis_mr_desc_3; ?></td>
			<?php
			}
				 if($vis_ak_od_k!="" ||$vis_ak_od_slash!="" || $vis_ak_od_x!="" ||$vis_ak_os_k!="" ||$vis_ak_os_slash!="" || $vis_ak_os_x!=""){?>
					<td  class="text12" valign="top" colspan="4" >
                    <?php if($vis_dis_near_desc !=""){ ?>
                    <span class="txt_11b">AK Description :&nbsp;</span><?php echo $vis_dis_near_desc;?>
                    <?php } ?>
                    </td>
			<?php } ?>		
		</tr>		
   
<?php  } ?>
</table>
</div>
<?php 
}
?>
<!-- End To Show MR 3 Values-->
	</td>
</tr>
</table>
<?php } ?>
-->
<?php
$mrValsArr = array();
$mrVals='';
if($Mr1st==true){ $mrValsArr[]="MR 1"; }
if($Mr2ng==true){ $mrValsArr[]="MR 2"; }
if($Mr3rd==true){ $mrValsArr[]="MR 3"; }
$mrVals = implode(",",$mrValsArr);


?>
<input type="hidden" name="mrVals" id="mrVals" value="<?php echo $mrVals;?>" />
<script type="text/javascript">
var MRHASVALUESORNOT='<?php echo $MRHASVALUESORNOT;?>';
if(dgi('mrVals').value!=''){ dgi('contactLensMR').style.display='block'; }
</script>
<!-- End Temp Code to Show MR Latest Values Irespective of none given--->