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

require_once(dirname(__FILE__).'/../../config/globals.php');
$provider_id = $_POST['provider_id'];
$facility_id = $_POST['facility_id'];
$date_or = $_POST['date_or'];
$assign_or = $_POST['assign_or'];	

$reqQry = 'SELECT sch_or_id FROM schedule_or_allocations WHERE date_or = "'.$date_or.'" and provider_id = "'.$provider_id.'" and facility_id = "'.$facility_id.'" order by sch_or_id DESC LIMIT 0,1';
$result_data = imw_query($reqQry);
$result_data_arr = imw_fetch_assoc($result_data);	

if(imw_num_rows($result_data)>0)
{
	$reqQry = 'UPDATE schedule_or_allocations SET assign_or = "'.$assign_or.'" WHERE sch_or_id = "'.$result_data_arr['sch_or_id'].'"';
	imw_query($reqQry);
}
else
{
	$reqQry = 'INSERT INTO schedule_or_allocations(provider_id,facility_id,date_or,assign_or) VALUES("'.$provider_id.'","'.$facility_id.'","'.$date_or.'","'.$assign_or.'")';	
	imw_query($reqQry);		
}	
?>