<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<!--<link rel="stylesheet" href="css/form.css" type="text/css" />-->
<link rel="stylesheet" href="../css/style_surgery.css" type="text/css" >
<style>
body { text-align:left; margin: 0px; background: #ECF1EA; width:100%; font:normal 11px Verdana, Arial, sans-serif;color:#000}
.link_slid_right{ color:#000000; text-decoration:none;}
.link_slid_right:hover{ color:#F10; text-decoration:none;}
</style>
<script type="text/javascript" src="../js/jquery-1.9.0.min.js"></script>
<script type="text/javascript" src="../js/jsFunction.js"></script>
<script type="text/javascript" src="../js/moocheck.js"></script>
<script src="../js/epost.js"></script>
<script type="text/javascript" src="../js/disableKeyBackspace.js"></script>
<script type="text/javascript" src="../js/actb.js"></script>
<script type="text/javascript" src="../js/common.js"></script>

<!--RESPONSIVE CSS AND JAVASCRIPT-->
<link rel="stylesheet" type="text/css" href="../css/style.css" />
<link rel="stylesheet" type="text/css" href="../css/font-awesome.css" />
<link rel="stylesheet" type="text/css" href="../css/bootstrap.css" />
<link rel="stylesheet" type="text/css" href="../css/bootstrap-select.css" />
<link rel="stylesheet" type="text/css" href="../css/ion.calendar.css" />
<link rel="stylesheet" type="text/css" href="../css/datepicker.css" />


<script type="text/javascript" src="../js/jquery-1.11.3.js"></script>
<script type="text/javascript" src="../js/bootstrap.js"></script>
<script type="text/javascript" src="../js/bootstrap-select.js"></script>
<script type="text/javascript" src="../js/moment.js"></script>
<script type="text/javascript" src="../js/bootstrap-datepicker.js"></script>    
<script type="text/javascript" src="../js/ion.calendar.js"></script>
<script type="text/javascript" src="../js/overflow.js"></script>
<script type="text/javascript" src="../js/front_page.js"></script>
<!--RESPONSIVE CSS AND JAVASCRIPT-->
<script>
$(function(){		
	$(".selectpicker").selectpicker();
	
	$('.selectpicker').on('change', function (e) {
		// take jquery val()s array for multiple
		//store the selected value
		var $selectedOption = $(this).find(':selected');
		var attending = $selectedOption.data('attending');
		var checkAttendings = [];
		$selectedOption.each(function(index){
			checkAttendings.push($(this).data('attending'));
		});
		
		// take jquery vals() array for multiple
		var value = checkAttendings || [];
		
		// take the existing old data or create new
		var old = $(this).data('old') || [];
		//alert('OLD IS '+JSON.stringify(old));
		// take the old order or create a new
		var order = $(this).data('order') || [];
		//alert('ORDER'+JSON.stringify(order));
		// find the new items
		var newone = value.filter(function (val) {
			return old.indexOf(val) == -1;
		});
		//alert('NEW One'+JSON.stringify(newone));
		// find missing items
		var missing = old.filter(function (val) {
			return value.indexOf(val) == -1;
		});
		// console.log(missing,newone)
		// remove missing items from order array and add new ones to it
		$.each(missing, function (i, miss) {
			order.splice(order.indexOf(miss), 1);
		})
		$.each(newone, function (i, thing) {
			order.push(thing);
		})
	
		// save the order and old in data()
		$(this).data('old', value).data('order', order);
		
		if(JSON.stringify(order) == JSON.stringify([1,2]) || JSON.stringify(order) == JSON.stringify([0,2]))
		{
			attending = 2;
		}
		
		
		//if current selected array equal to [0,2] that means user trying to check TBD
		if(JSON.stringify(order) == JSON.stringify([1,0]) || JSON.stringify(order) == JSON.stringify([2,0]) )
		{
			attending = 0;
		}
	
		//if current selected array equal to [0,1]  or [2,1] that means user trying to check Attending date
		if(JSON.stringify(order) == JSON.stringify([0,1]) || JSON.stringify(order) == JSON.stringify([2,1])){
			attending = 1;
		}
		//alert(attending);
		console.log(order, 'attending :' + attending);
	
		if(attending == 1)
		{
	
			//$(this).next().find('button').removeClass('btn-default btn-danger btn-success').addClass('btn-success');
			var tbd = $(this).find('[data-attending="2"]');
			var notAttending = $(this).find('[data-attending="0"]');
			if(tbd.is(':selected'))
			{
				tbd.prop('selected',false);
				
				$(this).selectpicker('refresh');
			}
	
			if(notAttending.is(':selected'))
			{
				notAttending.prop('selected',false);
				$(this).selectpicker('refresh');
			}
			order.reverse();
		}
		 
		/*if(attending == 2)
		{
	
			$(this).next().find('button').removeClass('btn-default btn-danger btn-success').addClass('btn-default');
	
			var coming = $(this).find('[data-attending="1"]');
			var not = $(this).find('[data-attending="0"]');
	
			if(coming.is(':selected'))
			{
				console.log('comming');
				coming.prop('selected',false);
				$(this).selectpicker('refresh');
			}
			if(not.is(':selected'))
			{
				console.log('not comming');
				not.prop('selected',false);
				
				$(this).selectpicker('refresh');
			}
			order.reverse();
		}*/
		
		if(attending == 0 )
		{
			var screening_date_id 	= 0;
			
			//$(this).next().find('button').removeClass('btn-default btn-danger btn-success').addClass('btn-danger');
	
			var yesComing	=	$(this).find('[data-attending="1"]');
			var tbd 		=	$(this).find('[data-attending="2"]');
			
			if(yesComing.is(':selected'))
			{
				yesComing.prop('selected',false);
				$(this).selectpicker('refresh');
			}
			if(tbd.is(':selected'))
			{
				tbd.prop('selected',false);
			   
				$(this).selectpicker('refresh');
			}
			
			order.reverse();
			console.log(order, 'attending :'+attending);
		}
		
		if(typeof attending == 'undefined' || attending == '' )
		{	
			$(this).find('[data-attending="0"]').prop('selected',true);	
			$(this).selectpicker('refresh');
		}
		//order.length = 0;
	});
	 
	
		$('[id^="datetimepicker"]').datetimepicker({ format: 'MM-DD-YYYY' });
		
		
		$('#ResetForm').on('click', function(event) {
			event.preventDefault()
			$form = $(event.target).closest('form')
			$form[0].reset()
			$form.find('select').selectpicker('render')
		});
		
	});
</script>