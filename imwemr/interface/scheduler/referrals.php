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

if($pt_id!=""){
	//--- Primary Secondary	
	$priDetail = getActiveInsId(1,$pt_id);
	$priFlag = getReferralFlag(1,$priDetail->id);

	$priDetail2 = getActiveInsId(2,$pt_id);
	$secFlag = getReferralFlag(2,$priDetail2->id);


	$priDetail3 = getActiveInsId(3,$pt_id);
	$terFlag = getReferralFlag(3,$priDetail3->id);
	if($priFlag)
	{
	?>
		  &nbsp;<img src="<?php echo $GLOBALS['webroot'];?>/library/images/<?php echo $priFlag;?>.gif" align="bottom" title="Primary Referrals" tbl="pri">
	<?php
	}
	
	if($secFlag)
	{
	?>
		  &nbsp;<img src="<?php echo $GLOBALS['webroot'];?>/library/images/<?php echo $secFlag;?>.gif" align="bottom" title="Secondary Referrals" tbl="sec">
	<?php
	}
	if($terFlag)
	{
	?>						
		  &nbsp;<img src="<?php echo $GLOBALS['webroot'];?>/library/images/<?php echo $terFlag;?>.gif" align="bottom" title="Tertiary Referrals" tbl="ter">						
	<?php
	}
}
?>
	