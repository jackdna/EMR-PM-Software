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
/*
FILE : statement_txt.php
PURPOSE : Fetching data for Statement report
ACCESS TYPE : Indirect
*/

//Function file
include_once(dirname(__FILE__)."/../../config/globals.php");
include_once(dirname(__FILE__)."/../../library/classes/SaveFile.php");
include_once(dirname(__FILE__)."/../../library/classes/common_function.php");

$data="";
function char_space($len){
	$spac="";
	for($h=0;$h<$len;$h++){
		$spac.=" ";
	}
	return $spac;
}
function add_line($len){
	$line="";
	for($h=0;$h<$len;$h++){
		$line.="\r\n";
	}
	return $line;
}
function ar_convert($data){
	$char_len="14";
	$data_tr_exp=explode('<tr>',$data);
	$show_data=str_replace("&nbsp;","",strip_tags(trim($data_tr_exp[2])));
	$show_data.=add_line(1);
	$data_head_td_exp=explode('</td>',$data_tr_exp[3]);
	$data_val_td_exp=explode('</td>',$data_tr_exp[4]);
	foreach($data_head_td_exp as $td_val){
		$td_val=strip_tags(trim($td_val));
		$show_data.=$td_val;
		$show_data.=char_space($char_len-strlen($td_val));
	}
	$show_data.=add_line(1);
	foreach($data_val_td_exp as $td_val){
		$td_val=strip_tags(trim($td_val));
		$show_data.=$td_val;
		$show_data.=char_space($char_len-strlen($td_val));
	}
	return $show_data;
}
$tot_char_len="83";
$char_len="55";
$tot_lines ="63";
for($f=0;$f<count($txt_arr);$f++){
	
	$group_phone_number="";
	if($txt_arr[$f][35]!="" && strtolower($billing_global_server_name)=="essi"){
		$txt_arr[$f][35]=str_replace(' - ','-',$txt_arr[$f][35]);
		$group_phone_number_exp=explode("YOUR PAYMENT ",$txt_arr[$f][35]);
		if($group_phone_number_exp[1]!=""){
			$group_phone_number="Ph: ".trim($group_phone_number_exp[1]);
		}
	}
	
	// 1 line
	$lin_cont[]=0;
	$data .= add_line(0);
	$data .= char_space($char_len);
	$data .= $txt_arr[$f][0]."\r\n";
	
	// 2 line
	$lin_cont[]=0;
	$data .= add_line(0);
	$group_name=substr($txt_arr[$f][1],0,50);
	if($display_creditcard=="yes"){
		$data .= $group_name;
		$data .= char_space($char_len-strlen($group_name));
		$data .= $txt_arr[$f][2]."\r\n";
	}else{
		$data .= $group_name;
		$data .= char_space($char_len-strlen($group_name));
		$data .= $txt_arr[$f][14];
		$data .= char_space(2);
		$data .= $txt_arr[$f][15];
		$data .= char_space(2);
		$data .= $txt_arr[$f][16]."\r\n";
		
		$data .= $txt_arr[$f][4];
		$data .= char_space($char_len-strlen($txt_arr[$f][4]));
		$data .= $txt_arr[$f][17];
		$data .= char_space(8);
		$data .= $txt_arr[$f][18];
		$data .= char_space(10);
		$data .= $txt_arr[$f][19]."\r\n";
	}
	
	// 3 line
	$lin_cont[]=0;
	$data .= add_line(0);
	if($display_creditcard=="yes"){
		$data .= $txt_arr[$f][4];
		$data .= char_space($char_len-strlen($txt_arr[$f][4]));
		$data .= $txt_arr[$f][3]."\r\n";
	}else{
		$data .= $txt_arr[$f][8];
		$data .= char_space($char_len-strlen($txt_arr[$f][8]));
		$data .= char_space(19);
		$data .= $txt_arr[$f][22]."\r\n";
	}
	
	// 4 line
	$lin_cont[]=0;
	$data .= add_line(0);
	if($display_creditcard=="yes"){
		$data .= $txt_arr[$f][8];
		$data .= char_space($char_len-strlen($txt_arr[$f][8]));
		$data .= $txt_arr[$f][5].char_space(4).$txt_arr[$f][6].char_space(4).$txt_arr[$f][7]."\r\n";
	}else{
		$data .= $group_phone_number;
		$data .= char_space($char_len-strlen($group_phone_number));
		$data .= $txt_arr[$f][21];
		$data .= char_space(10);
		$data .= $txt_arr[$f][23]."\r\n";
	}
	
	// 5 line
	$lin_cont[]=0;
	$data .= add_line(0);
	if($display_creditcard=="yes"){
		$data .= $group_phone_number;
		$data .= char_space($char_len-strlen($group_phone_number));
		$data .= $txt_arr[$f][9];
		$data .= char_space(20);
		$data .= $txt_arr[$f][10]."\r\n";
	}else{
		$data .= $txt_arr[$f][13];
		$data .= char_space($char_len-strlen($txt_arr[$f][13]));
		$data .= $txt_arr[$f][25].char_space(3).$group_phone_number."\r\n";
	}
	
	// 6 line
	$lin_cont[]=0;
	$data .= add_line(0);
	if($display_creditcard=="yes"){
		$data .= char_space($char_len);
		$data .= $txt_arr[$f][11];
		$data .= char_space(17);
		$data .= $txt_arr[$f][12]."\r\n";
	}else{
		$data .= char_space($char_len);
		$data .= $txt_arr[$f][27]."\r\n";
	}
	
	// 7 line
	$lin_cont[]=0;
	$data .= add_line(0);
	
	if($display_creditcard=="yes"){
		$data .= $txt_arr[$f][13];
		$data .= char_space($char_len-strlen($txt_arr[$f][13]));
		$data .= $txt_arr[$f][14];
		$data .= char_space(2);
		$data .= $txt_arr[$f][15];
		$data .= char_space(2);
		$data .= $txt_arr[$f][16]."\r\n";
	}else{
		$data .= char_space($char_len);
		$data .= $txt_arr[$f][29]."\r\n";
	}
	
	// 8 line
	if($display_creditcard=="yes"){
		$lin_cont[]=0;
		$data .= add_line(0);
		$data .= char_space($char_len);
		$data .= $txt_arr[$f][17];
		$data .= char_space(8);
		$data .= $txt_arr[$f][18];
		$data .= char_space(10);
		$data .= $txt_arr[$f][19]."\r\n";
	}
	
	// 9 line
	
	$lin_cont[]=0;
	if($display_creditcard=="yes"){
		$data .= add_line(0);
		$data .= char_space($char_len);
		$data .= char_space(19);
		$data .= $txt_arr[$f][22]."\r\n";
	}else{
		//$data .= "\r\n";
	}
	
	// 10 line
	$lin_cont[]=0;
	$data .= add_line(0);
	$data .= $txt_arr[$f][20];
	if($display_creditcard=="yes"){
		$data .= char_space($char_len-strlen($txt_arr[$f][20]));
		$data .= $txt_arr[$f][21];
		$data .= char_space(10);
		$data .= $txt_arr[$f][23]."\r\n";
	}else{
		$data .= "\r\n";
	}
	
	// 11 line
	$lin_cont[]=0;
	$data .= add_line(0);
	$data .= $txt_arr[$f][24];
	if($display_creditcard=="yes"){
		$data .= char_space($char_len-strlen($txt_arr[$f][24]));
		$data .= $txt_arr[$f][25].char_space(3).$group_phone_number."\r\n";
	}else{
		$data .= "\r\n";
	}
	
	// 12 line
	$lin_cont[]=0;
	$data .= add_line(0);
	$data .= $txt_arr[$f][26];
	$data .= char_space($char_len-strlen($txt_arr[$f][26]));
	if($display_creditcard=="yes"){
		$data .= $txt_arr[$f][27]."\r\n";
	}else{
		$data .= "\r\n";
	}
	
	// 13 line
	$lin_cont[]=0;
	$data .= add_line(0);
	$data .= $txt_arr[$f][28];
	$data .= char_space($char_len-strlen($txt_arr[$f][28]));
	if($display_creditcard=="yes"){
		$data .= $txt_arr[$f][29]."\r\n";
	}else{
		$data .= "\r\n";
	}
	
	// Email Address
	$lin_cont[]=0;
	$data .= add_line(0);
	$data .= $txt_arr[$f][79];
	$data .= char_space($char_len-strlen($txt_arr[$f][79]));
	$data .= "\r\n";
	
	// GUARANTOR
	if($txt_arr[$f][80]!=""){
		$data .= "\r\n";
		for($kj=80;$kj<=84;$kj++){
			$lin_cont[]=0;
			$data .= add_line(0);
			$data .= $txt_arr[$f][$kj];
			$data .= char_space($char_len-strlen($txt_arr[$f][$kj]));
			$data .= "\r\n";
		}
	}
	
	if($show_home_fac_row!="no"){
		if($txt_arr[$f][70]!=""){
			$lin_cont[]=0;
			$data .= add_line(0);
			$data .= $txt_arr[$f][70];
			$data .= char_space($char_len-strlen($txt_arr[$f][70]));
			$data .= "\r\n";
		}
		if($txt_arr[$f][71]!=""){
			$lin_cont[]=0;
			$data .= add_line(0);
			$data .= $txt_arr[$f][71];
			$data .= char_space($char_len-strlen($txt_arr[$f][71]));
			$data .= "\r\n";
		}
	}
	
	// 14 line
	$lin_cont[]=1;
	$data .= add_line(1);
	$data .= $txt_arr[$f][30];
	$data .= char_space($char_len-strlen($txt_arr[$f][30]));
	$data .= $txt_arr[$f][34]."\r\n";
	
	// 15 line
	$data .= add_line(0);
	$data .= $txt_arr[$f][31];
	$data .= char_space($char_len-strlen($txt_arr[$f][31]));
	$data .= $txt_arr[$f][35]."\r\n";
	
	// 16 line
	$lin_cont[]=0;
	$data .= add_line(0);
	$data .= $txt_arr[$f][32]."\r\n";
	
	// 17 line
	$lin_cont[]=0;
	$data .= add_line(0);
	$data .= $txt_arr[$f][33]."\r\n";
	
	// 18 line
	$lin_cont[]=1;
	$data .= add_line(1);
	//date cpt desc  charges paid  balance
	$data .= $txt_arr[$f][37];
	$data .= char_space(6);
	$data .= $txt_arr[$f][38];
	$data .= char_space(5);
	$data .= $txt_arr[$f][39];
	$data .= char_space(22);
	$data .= $txt_arr[$f][40];
	$data .= char_space(3);
	$data .= $txt_arr[$f][41];
	$data .= char_space(3);
	$data .= $txt_arr[$f][42];
	$data .= char_space(4);
	$data .= $txt_arr[$f][43];
	$data .= char_space(3.5);
	$data .= $txt_arr[$f][44];
	$data .= char_space(4);
	$data .= $txt_arr[$f][45]."\r\n";
	
	// 19 line
	$lin_cont[]=0;
	$data .= add_line(0);
	$enc_id_arr=array_values(array_unique($txt_arr[$f][46]['enc']));
	$data_arr=$txt_arr[$f][46];
	
	for($b=0;$b<count($enc_id_arr);$b++){
		$enc_id=$enc_id_arr[$b];
		$dateArr = $data_arr['date'][$enc_id];
		$lin_cont[]=count($dateArr);
		for($k=0;$k<count($dateArr);$k++){
			for($y=0;$y<count($data_arr['date'][$enc_id][$k]);$y++){
				if(strstr($data_arr['desc'][$enc_id][$k][$y],"CASCODE")){
				}else{
					if($data_arr['date'][$enc_id][$k][$y]!=""){
						$data .= $data_arr['date'][$enc_id][$k][$y];
						$data .= char_space(9-strlen($data_arr['date'][$enc_id][$k][$y]));
						$cpt="";
						$cpt = substr($data_arr['cpt'][$enc_id][$k][$y],0,8);
						$data .= $cpt;
						if(str_replace(" ","",$cpt)==""){
							$data .= char_space(17);
							$desc_space=char_space(17+9);
						}else{
							$data .= char_space(8-strlen($cpt));
							$desc_space=char_space(8-strlen($cpt)+9);
						}
					}else{
						$data .= char_space(17);
						$desc_space=char_space(17+9);
					}
					
					if(strstr($data_arr['desc'][$enc_id][$k][$y],"Ded. - ")){
						$ded_exp=explode("Ded. - ",$data_arr['desc'][$enc_id][$k][$y]);
						$desc = $ded_exp[0]."Deductible";
						$ded_den_desc=$desc_space.$ded_exp[1]."\r\n";
						$data .= $desc;
						$data .= char_space(34-strlen($desc));
					}else if(strstr($data_arr['desc'][$enc_id][$k][$y],"Denied - ")){
						$ded_exp=explode("Denied - ",$data_arr['desc'][$enc_id][$k][$y]);
						$desc = $ded_exp[0]."Denied";
						$ded_den_desc=$desc_space.$ded_exp[1]."\r\n";
						$data .= $desc;
						$data .= char_space(34-strlen($desc));
					}else{
						$desc = substr(str_replace("Adjustment","Adj",$data_arr['desc'][$enc_id][$k][$y]),0,32);
						$ded_den_desc="";
						$data .= $desc;
						$data .= char_space(34-strlen($desc));
					}
					$any_trans="";
					if(strstr($data_arr['desc'][$enc_id][$k][$y],"Ded. - ") || strstr($data_arr['desc'][$enc_id][$k][$y],"Denied") || strstr(str_replace("Adjustment","Adj",$data_arr['desc'][$enc_id][$k][$y]),"Adj")){
						$any_trans="1";
					}
					
					if($data_arr['date'][$enc_id][$k][$y]!=""){
						$units=str_replace(".00","",$data_arr['units'][$enc_id][$k]);
						$data .= char_space(4-strlen($units));
						$data .= $units;
					}
					$data .= char_space(13-strlen($data_arr['chr'][$enc_id][$k][$y]));
					
					
					$paid = $data_arr['paid'][$enc_id][$k][$y-1];
					$data .= $data_arr['chr'][$enc_id][$k][$y];
					
					$data .= char_space(15-(strlen($data_arr['ins_paid'][$enc_id][$k][$y])));
					$data .= str_replace(" ","",$data_arr['ins_paid'][$enc_id][$k][$y]);
					
					$data .= char_space(10-(strlen($data_arr['adj_paid'][$enc_id][$k][$y])));
					$data .= str_replace(" ","",$data_arr['adj_paid'][$enc_id][$k][$y]);
					
					$data .= char_space(10.5-(strlen($data_arr['pat_paid'][$enc_id][$k][$y])));
					$data .= str_replace(" ","",$data_arr['pat_paid'][$enc_id][$k][$y]);
					$cnt = $y;
					
					if(count($data_arr['paid'][$enc_id][$k])>0){
						//$cnt = $y-count($data_arr['paid'][$enc_id][$k]);
						if($y==(count($data_arr['paid'][$enc_id][$k])-1)){
							$cnt=0;
						}else{
							$cnt = $y-count($data_arr['paid'][$enc_id][$k]);
						}
					}
					$minus_bal="11";
					if(constant("DISABLE_DXCODE") != ""){
						if(trim($data_arr['ins_paid'][$enc_id][$k][$y])=="" && trim($data_arr['adj_paid'][$enc_id][$k][$y])=="" && trim($data_arr['pat_paid'][$enc_id][$k][$y])=="" && $any_trans==""){
							$minus_bal=7;
						}else{
							$minus_bal=11;
						}
					}
					$data .= char_space($minus_bal-(strlen($data_arr['bal'][$enc_id][$k][$cnt])));
					$data .= str_replace('$-','-$',$data_arr['bal'][$enc_id][$k][$cnt])."\r\n";
					if($ded_den_desc!=""){
						$data .= $ded_den_desc;	
					}
				}
			}
		}
	}
	// 20 line
	$lin_cont[]=0;
	$data .= add_line(0);
	$data .= char_space(17);
	$data .= $txt_arr[$f][47];
	$data .= char_space(25-strlen($txt_arr[$f][48]));
	$data .= $txt_arr[$f][48];
	$data .= char_space(13-strlen($txt_arr[$f][49]));
	$data .= $txt_arr[$f][49];
	$data .= char_space(11-strlen($txt_arr[$f][50]));
	$data .= $txt_arr[$f][50];
	$data .= char_space(10-strlen($txt_arr[$f][51]));
	$data .= $txt_arr[$f][51];
	$data .= char_space(11-strlen($txt_arr[$f][52]));
	$data .= $txt_arr[$f][52];
	$data .= char_space(11-strlen($txt_arr[$f][53]));
	$data .= $txt_arr[$f][53]."\r\n";
	
	
	if(str_replace(',','',str_replace('$','',$txt_arr[$f][74]))>0){
		$lin_cont[]=1;
		$data .= add_line(1);
		$data .= char_space(87);
		$data .= $txt_arr[$f][73];
		$data .= char_space(14-strlen($txt_arr[$f][74]));
		$data .= $txt_arr[$f][74]."\r\n";
	}
	if($txt_arr[$f][75]!=""){
		$lin_cont[]=1;
		$data .= add_line(1);
		$data .= char_space(72);
		$data .= $txt_arr[$f][75];
		$data .= char_space(14-strlen($txt_arr[$f][76]));
		$data .= $txt_arr[$f][76]."\r\n";
	}
	
	// 21 line
	$lin_cont[]=1;
	$data .= add_line(1);
	$data .= char_space(86);
	$data .= $txt_arr[$f][54];
	$data .= char_space(14-strlen($txt_arr[$f][55]));
	$data .= $txt_arr[$f][55]."\r\n";
	
	//comment
	// 22 line
	$lin_cont[]=0;
	$data .= add_line(0);
	$data .= $txt_arr[$f][56]."\r\n";
	
	// account number  new balance
	// 23 line
	$lin_cont[]=1;
	$data .= add_line(1);
	$data .= $txt_arr[$f][57];
	$data .= char_space(15);
	$data .= $txt_arr[$f][58];
	$data .= char_space(3);
	$data .= $txt_arr[$f][59];
	$data .= char_space(3);
	$data .= $txt_arr[$f][60];
	if($show_adj_tot_row=="yes"){
		$data .= char_space(3);
		$data .= $txt_arr[$f][61];
	}
	$data .= char_space(3);
	$data .= $txt_arr[$f][62]."\r\n";
	
	// 24 line
	$lin_cont[]=0;
	$data .= add_line(0);
	$data .= $txt_arr[$f][63];
	$data .= char_space(27-strlen($txt_arr[$f][63]));
	$data .= char_space(13-strlen($txt_arr[$f][64]));
	$data .= $txt_arr[$f][64];
	$data .= char_space(14-strlen($txt_arr[$f][65]));
	$data .= $txt_arr[$f][65];
	$data .= char_space(11-strlen($txt_arr[$f][66]));
	$data .= $txt_arr[$f][66];
	if($show_adj_tot_row=="yes"){
		$data .= char_space(11-strlen($txt_arr[$f][67]));
		$data .= $txt_arr[$f][67];
	}
	$data .= char_space(13-strlen($txt_arr[$f][68]));
	$data .= $txt_arr[$f][68]."\r\n";
	
	//A/R Aging
	if($txt_arr[$f][78]!=""){
		$data .= add_line(2);
		$data .=ar_convert($txt_arr[$f][78]);
	}
	
	// footer
	$lin_cont[]=0;
	$data .= add_line(2);
	/*$foot_txt_len= ceil(strlen($txt_arr[$f][69])/110);
	$ls=0;
	$st=0;
	
	for($j=1;$j<=$foot_txt_len;$j++){
		$st=$st;
		$ls=110*$j;
		$data .= substr($txt_arr[$f][69],$st,$ls)."\r\n";
		$st=$ls+$j;
	}*/
	
	$foot_txt_len=110;
	for($j=0;$j<=strlen($txt_arr[$f][69]);$j++){
		$data .= $txt_arr[$f][69][$j];
		if(($foot_txt_len-$j)<=0){
			if($txt_arr[$f][69][$j]==" "){
				$data .= "\r\n";
				$foot_txt_len=110+$j;
			}
		}
	}
	
	$data .= add_line(0);
	if($txt_arr[$f][77]==""){
		$data .= char_space($char_len);
	}
	$data .= $txt_arr[$f][77]."\r\n";
	$line_shw="";
	for($s=0;$s<67;$s++){
		$line_shw .= "--";
	}
	$data .= $line_shw."\r\n";
	$txt_data[$txt_arr[$f][72]][$txt_arr[$f][19]] = $data;
	$concat_data .= $data;
	$data="";
}
$filename = 'statement_report.txt';
if($st_start>0){
	$chk_file_append="yes";
}
if($text_print>0){
	$txt_filePath=write_html($concat_data,$filename,$chk_file_append);
}
if($_REQUEST['print_pdf']=='email' || $emailStatement>0){
	$txt_filePath="";
}
?>