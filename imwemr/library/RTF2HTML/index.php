<?php
require_once(dirname(__FILE__).'/../../config/globals.php');

$file_src 		= $_GET['file_src']!='' ? $_GET['file_src'] : '';
$to_format 		= $_GET['to_format']!='' ? $_GET['to_format'] : '';
$file_root		= $_GET['file_root'] != '' ? $_GET['file_root'] : '';

function lreplace($search, $replace, $subject){
    $pos = strrpos($subject, $search);
    if($pos !== false){
        $subject = substr_replace($subject, $replace, $pos, strlen($search));
    }
    return $subject;
}


//$file_src = '../tmp/rtf_to_db/GEN20150602RETINA.rtf';
if($file_src != '' && $to_format=='html'){
	if($file_root=='pt_doc_root'){$file_root = lreplace('/','',data_path()).$file_src;}
	$file = $file_root;
	
	if(is_file($file) && file_exists($file)){
		require_once(dirname(__FILE__).'/RTF2HTML.php');
		$reader = new RtfReader();
		$rtf = file_get_contents($file);
		$reader->Parse($rtf);
		//$reader->root->dump();
		$formatter = new RtfHtml();
		if($formatter){
			$file_name='rft_html';
			$path = '/var/www/html'.data_path(1).'UserId_'.$_SESSION['authId'].'/tmp/'.$file_name.'.html';
			echo "<input type='button' value='Print' class='dff_button' style='float:right; border:1px solid #ccc; cursor:hand;' onclick='window.open(\"../html_to_pdf/createPdf.php?op=p&onePage=false&file_location=$path\");'> ";
			echo $retunr_rft_data=$formatter->Format($reader->root);
			$arr_find=array("<p>","</p>");
			$arr_repl=array("<br>","<br>");
			 $retunr_rft_data="<table><tr><td style='line-height:2;'>". $retunr_rft_data."</td></tr></table>";

			$data_path = ltrim(data_path(1), '/');
			$data_path = rtrim($data_path, '/');
			$data_path = explode('/',$data_path);
			$practice_dir = end($data_path);

			$file_path = '../../data/'.$practice_dir.'/UserId_'.$_SESSION['authId'].'/tmp/'.$file_name.'.html';
			file_put_contents($file_path,nl2br(str_ireplace($arr_find,$arr_repl,((($retunr_rft_data))))));
		}
	}else{
		die('Source file not found.');
	}
}
?>