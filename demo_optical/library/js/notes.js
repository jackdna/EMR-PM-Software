function notes_add()
{
	var new_note=$("#new_sticky_note").val();
	var note_pt=$("#note_pt").val();
	if(typeof(note_pt)=='undefined')note_pt=0;
	if(new_note){
		notes_loader('show');
		//plus counter
		var total_notes=notes_counter_get();
		total_notes=parseInt(total_notes)+1;
		notes_counter_set(total_notes);
		//send save request
		var dataString = 'action=save&note='+encodeURI(new_note)+'&note_pt='+note_pt;
		$.ajax({
			type: "GET",
			url: top.WRP+"/interface/patient_interface/ajax_notes.php",
			data: dataString,
			cache: false,
			success: function(response)
			{	
				//remove enter text
				$("#new_sticky_note").val('');
				notes_load();
			}
		});
	}
}
function notes_delete(id)
{
	if(id)
	{	
		notes_loader('show');
		//minus counter
		var total_notes=notes_counter_get();
		total_notes=parseInt(total_notes)-1;
		notes_counter_set(total_notes);
		//remove from table
		$("#notes_"+id).hide();
		//send delete request
		var dataString = 'action=delete&id='+id;
		$.ajax({
			type: "GET",
			url: top.WRP+"/interface/patient_interface/ajax_notes.php",
			data: dataString,
			cache: false,
			success: function(response)
			{	
				//do anything
			}
		});
		notes_loader('hide');
	}
}
function notes_load()
{
	notes_loader('show');
	var disp_note=$("#disp_note").val();
	var dataString = 'action=load&disp_note='+disp_note;
	$.ajax({
		type: "GET",
		url: top.WRP+"/interface/patient_interface/ajax_notes.php",
		data: dataString,
		cache: false,
		success: function(response)
		{	
			var arr = Array();
			arr=(response).split("~::~");
			 //hide loader
			notes_loader('hide');
			//update notes counter
			notes_counter_set(arr[0]);
			//update listing
			notes_listing(arr[1]);
		}
	});
}

function notes_listing(listing)
{
	$("#note_listing").html(listing);
}
function notes_counter_set(cnt)
{
	if(typeof(cnt)=='undefined' || !cnt)cnt=0;
	$("#sticky_notes").html(cnt);
}
function notes_counter_get()
{
	return $("#sticky_notes").html();
}
function notes_loader(opt)
{
	if(opt=='hide')
	{
		$("#note_loader").hide();
	}
	else
	{
		$("#note_loader").show();
	}
}