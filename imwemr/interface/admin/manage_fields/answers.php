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

require_once("../../../config/globals.php");

$question_id = $_POST['question_id'];
$intTemp=0;
if(empty($question_id)===false){
	$arrAnswers=array();
	$q="Select id, option_value FROM med_hx_question_answer_options WHERE question_id = '".$question_id."' AND del_status = '0'";
	$res=imw_query($q);
	if(imw_num_rows($res)>0){
		$intTemp=1;
		while($rs=imw_fetch_assoc($res)){

		$strOpValueDB = html_entity_decode(stripslashes($rs["option_value"]));
		$strOpValHTML .= 
		"<div id=\"divTR".$intTemp."\" class=\"row\" style=\"margin-bottom:5px;\" >
			<div class=\"col-sm-1 text-center\">
				".$intTemp."
			</div>
			<div class=\"col-sm-10\">
				<input type=\"hidden\" name=\"hidId$intTemp\" id=\"hidId$intTemp\" value = \"".$rs['id']."\">
				<input type=\"text\" name=\"txtAnsOptionArr".$intTemp."\" id=\"txtAnsOptionArr".$intTemp."\" value=\"".$strOpValueDB."\" class=\"form-control\"/>
			</div>
			<div class=\"col-sm-1\">
				 <img src=\"../../../library/images/close_small.png\" id=\"imgDel".$intTemp."\" name=\"imgDel".$intTemp."\" style=\"display:block;\" onClick=\"delAnsOpRow(this,'".$intTemp."', '".$rs['id']."');\"/>
				 <img src=\"../../../library/images/add_small.png\" id=\"imgAdd".$intTemp."\" name=\"imgAdd".$intTemp."\" style=\"display:none;\" onClick=\"addAnsOpRow(this,document.getElementById('imgDel".$intTemp."'), '".$intTemp."');\"/>
			</div>
		</div>";
		$intTemp++;
		}

		if($intTemp > 0){
			$strOpValHTML .= "<div id=\"divTR".$intTemp."\" class=\"row\" style=\"margin-bottom:5px;\" >
				<div class=\"col-sm-1 text-center\">
					".$intTemp."
				</div>
				<div class=\"col-sm-10\">
					<input type=\"text\" name=\"txtAnsOptionArr".$intTemp."\" id=\"txtAnsOptionArr".$intTemp."\" value=\"\" class=\"form-control\"/>
				</div>
				<div class=\"\"col-sm-1\"\">
					 <img src=\"../../../library/images/close_small.png\" id=\"imgDel".$intTemp."\" name=\"imgDel".$intTemp."\" style=\"display:none;\" onClick=\"delAnsOpRow(this,'".$intTemp."');\"/>
					 <img src=\"../../../library/images/add_small.png\" id=\"imgAdd".$intTemp."\" name=\"imgAdd".$intTemp."\" onClick=\"addAnsOpRow(this,document.getElementById('imgDel".$intTemp."'), '".$intTemp."');\"/>
				</div>
			</div>";
		}

	}
}
echo $intTemp.'~~'.$strOpValHTML;
?>