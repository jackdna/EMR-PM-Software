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
require_once("../../config/globals.php");
$proc_code=$_REQUEST['proc_code'];
$getCPTPriceQry = imw_query("SELECT a.tos_prac_cod FROM tos_tbl a,
										cpt_fee_tbl b
										WHERE 
										a.tos_id = b.tos_id
										and (b.cpt4_code='$proc_code' or b.cpt_desc='$proc_code')
										AND b.delete_status = '0' order by b.status asc");
		$getCPTPriceRow = imw_fetch_assoc($getCPTPriceQry);
echo $proc = $getCPTPriceRow['tos_prac_cod'];
?>