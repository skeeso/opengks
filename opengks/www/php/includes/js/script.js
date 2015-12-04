var g_current_mode = '';
var g_new_row_id = new Array();
var g_newest_row_id = '';
function applyColorBox()
{
	$(".library").colorbox({iframe:true, innerWidth:680, innerHeight:550,title:false});
}

function applyParentColorBox()
{
	parent.$(".library").colorbox({iframe:true, innerWidth:680, innerHeight:550});
}
function loadData()
{
	// retrieve navigation detail
	var pageNo = ($('#pageNo').length)?$('#pageNo').val():'';
	var order = ($('#order').length)?$('#order').val():'';
	var field = ($('#field').length)?$('#field').val():'';
	var recordPerPage = ($('#recordPerPage').length)?$('#recordPerPage').val():'';
	var search_field = $('#search_field').val();
	var search_str = ($('#search_str').length)?$('#search_str').val():'';

	// message to display while getting data
	$('#content_div').html('Loading..');
	
	// call ajax.php to generate the data table
	$.post(g_ajax_file, {task: 'gen_table',pageNo:pageNo,order:order,field:field,recordPerPage:recordPerPage,search_field:search_field,search_str:search_str,current_mode:g_current_mode},  
			function(data, textStatus){
				$('#content_div').html(data);
				updateCheckboxCount(); 
				// apply editable only if it's edit mode
				if(g_current_mode =='e')
				{
					
					applyColorBox();
					applyEditable();
				}
			}
				
		);
	$('#new_row_ct').val(0);	
}
function removeThisRow(id)
{
	$('#'+id).remove();
}
function loadLibraryData()
{
	// retrieve navigation detail
	var pageNo = ($('#pageNo').length)?$('#pageNo').val():'';
	var order = ($('#order').length)?$('#order').val():'';
	var field = ($('#field').length)?$('#field').val():'';
	var recordPerPage = ($('#recordPerPage').length)?$('#recordPerPage').val():'';
	var keyword = $('#keyword').val();
	var selected_image = $('#selected_image').val();
	// message to display while getting data
	$('#content_div').html('Loading..');
	
	// call ajax.php to generate the data table
	$.post(g_ajax_file, {task: 'gen_library_table',pageNo:pageNo,order:order,recordPerPage:recordPerPage,keyword:keyword,selected_image:selected_image},  
			function(data, textStatus){
				$('#content_div').html(data);
				
				
			}
				
		);
}

function searchImage(e)
{
	if(e.keyCode == 13)
	{
		loadLibraryData();
	}
}

function deleteImage(id)
{
	if(confirm(g_delete_library_img))
	{
		$.post(g_ajax_file, {task: 'delete_image',id:id},  
				function(data, textStatus){
					showNoticeBox(data);
					loadLibraryData();
				}
					
			);
	}	
}

function showParentNoticeBox(data)
{
	parent.$('#notice_box').html(data);
	setTimeout("hideParentNoticeBox()",3000);
}


function showNoticeBox(data)
{
	$('#notice_box').html(data);
	setTimeout("hideNoticeBox()",3000);
}

function hideNoticeBox()
{
	$('#notice_msg_div').fadeOut();
}

function hideParentNoticeBox()
{
	parent.$('#notice_msg_div').fadeOut();
	//$('#notice_msg_div').animate({opacity: 'toggle'});
}


// handle the enter event for the search textbox
function searchEnterEvent(e)
{
	// if press enter in textbox
	if(e.keyCode == 13)
		loadData();
}
 
// sort record by field
function ajax_sort(order, field, obj)
{
    obj.order.value=order;
    obj.field.value=field;
    obj.pageNo.value=1;
    loadData();
}

// go to next page
function ajax_gopage(page, obj)
{
	obj.pageNo.value=page;
	loadData();
}

function ajax_library_gopage(page, obj)
{
	obj.pageNo.value=page;
	loadLibraryData();
}

// change record view per page
function ajax_changeRecordPerPage(obj,size)
{
	obj.pageNo.value=1;
	obj.recordPerPage.value=size;
	loadData();	
}

function ajax_library_changeRecordPerPage(obj,size)
{
	obj.pageNo.value=1;
	obj.recordPerPage.value=size;
	loadLibraryData();	
}
function addRow(jQtable)
{
	// check how many row to add
	var row_num = $('#row_num').val();
		
    // get the row info
	$.post(g_ajax_file, {task: 'get_row'},  
		function(data, textStatus)
		{
			
			tds = data; // row get from table.php (one row only)
			
			jQtable.each(function()
			{
        		var $table = $(this);
		        
		        var td_count = $('tr:last td', this).length; // Number of td's in the last table row
		        var tr_count = $('tr', this).length ; // // Number of row in the table
		        
		        var inserted_row = $('#new_row_ct').val();
		        
		        // remove 'no record found' row, if any
		        if(td_count==1 && inserted_row == 0)
		        {
		        	removeLastRow();
					tr_count = 1;
				}
				
				// if is currently inserting new data, don't count the submit button row 
				if(inserted_row>0)
					tr_count--;
		         
		      	for(var a=0;a<row_num;a++)
		      	{
		      		// insert button row
		      		if(a==0 && inserted_row==0)
					{
						var cell_num = g_sql_field_count+2;
						var button_row = '<tr><td colspan="'+cell_num+'" class="btn_area"><input  class="action_btn submit" type="button" value="insert" onClick="insertRecords();"/></td></tr>';
						
						if($('tbody', this).length > 0)
				     	{
							$('#record_table').prepend(button_row);
				        }
				        else 
				        {
							$(this).prepend(button_row);
						}
					}
					
		      		// adjust the id for each cell
		      		g_newest_row_id = tr_count+a;
		        	for(var k=0;k<g_new_row_id.length;k++)
		        	{
			        	if(g_new_row_id[k]==g_newest_row_id)  
			        	{
				        	g_newest_row_id++;	
			        	}
		        	}
		        	
		        	// assign the new row id to a global variable for checking
		        	g_new_row_id[g_newest_row_id]=g_newest_row_id;
		        	
		        	td = tds.replace(/newdata/g,'newdata'+(g_newest_row_id));
		        	
					if($('tbody', this).length > 0)
			     	{
						$('#record_table').prepend(td);
			        }
			        else 
			        {
						$(this).prepend(td);
					}
					
				}
				applyColorBox();
				addNewRowValue($('#row_num').val());
			}
			
		);
    });
}

function removeLastRow(row)
{
	$('#record_table tr:last').remove();
	
}

function insertRecords()
{
	var total_new_row = parseInt($('#new_row_ct').val());
	var table_total_row = parseInt(($('#record_table tr').length));

	var from = 0;
	var to = g_newest_row_id+1;
	
	for(var a=from;a<to;a++)
	{
		insertRecord('newdata'+a);
	}
}

function addNewRowValue(add_ct)
{
	var current_ct = $('#new_row_ct').val();
	var new_ct = parseInt(current_ct) + parseInt(add_ct);
	
	$('#new_row_ct').val(new_ct);
}

function deductRowValue(d_ct)
{
	var current_ct = $('#new_row_ct').val();
	
	if(current_ct!=0)
	{	
		var new_ct = parseInt(current_ct) - parseInt(d_ct);
		
		$('#new_row_ct').val(new_ct);
	}
}

function deleteRecord(id)
{
	if(confirm(g_delete_record_confirm))
	{	
		if(typeof(id)=='undefined')
		{			
			var ids = '';
		
		
			var ct=0;
			$('#record_table :checked').each(function() {
	       		
	       		if($(this).attr("id")==g_primary_id+"[]")
	       		{
					if(ct>0)
						ids+=",";
		       		ids +=$(this).val();
		       		ct++;
		       	}
		      
		       	
		     });
			id = ids;
			
		
		}
		
		$.post(g_ajax_file, {task: 'delete_record',id:id},  
					function(data, textStatus){
						showNoticeBox(data);
						loadData();
					}
						
				);
	}
}

function removeRecordImage(id,field)
{
	if(confirm(g_delete_record_image_confirm))
	{	
		$.post(g_ajax_file, {task: 'delete_record_image',id:id,field:field},  
					function(data, textStatus){
						showNoticeBox(data);
						loadData();
					}
						
				);
	}
}

function attachImage(field,p_id)
{
	
	var image = $("input[name='library_image']:checked").val();
	
		
	if(p_id.indexOf("newdata")==-1)
	{
		
		// update path to db
		id = p_id+'##'+field;
		value = image;
		
		$.post(g_ajax_file, {task: 'update_record',id:id,value:value},  
			function(data, textStatus)
			{
				showParentNoticeBox(data);
				parent.$.fn.colorbox.close();
				parent.loadData();
			}
		);
	}
	else
	{
		// put image name to hidden field for insert
		parent.document.getElementById(field+'_'+p_id).value = image;
		
		// embed image
		if(parent.document.getElementById(field+'_'+p_id+'_img'))
			parent.document.getElementById(field+'_'+p_id+'_img').src = g_frontend_folder_httppath+"/"+image;
		else
		{
			var current = parent.$('#'+field+'_'+p_id+'_td').html();
			parent.$('#'+field+'_'+p_id+'_td').html('<img id="'+field+'_'+p_id+'_img" src="'+g_frontend_folder_httppath+"/"+image+'" width="'+g_grid_image_width+'"/><br/>'+current)	
		}
			
		
		parent.$.fn.colorbox.close();
		applyParentColorBox();
		
	}
}

function uploadImage(field,p_id)
{
	// if have selected a file and insert an link
	if($('#image').val()!='' && $('#image_link').val()!='')
	{
		alert(g_either_upload_or_link);
		return false	
	}
	// if no file selected and no link inserted
	if($('#image').val()=='' && $('#image_link').val()=='')
	{
		alert(g_upload_or_link);
		return false	
	}
	
	var need_resize = $("#need_resize").attr('checked'); 
	var image_link = '';
	// when upload image
	if($('#image').val()!='')
	{
		if(need_resize==false)
			need_resize = 0;	
		else
		{
			
			if($('#resize_size_w').val()=='')
			{
				alert(g_input_resize_w);
				return false;	
			}
			if($('#resize_size_h').val()=='')
			{
				alert(g_input_resize_h);
				return false;	
			}
		}
		
		$('#loading_msg').html('<div class="uploading">Uploading, please...........</div>');		
	}
	// when input link
	else if($('#image_link').val()!='')
	{
		if(!validateURL(document.getElementById('image_link'),g_incorrect_link_format))
			return false;
		else
			image_link = $('#image_link').val();
	}
	
	
	$('#form_upload').ajaxSubmit({
		url: g_ajax_file, 
		type: 'post',
		data: {task: 'upload_image',need_resize:need_resize,image_link:image_link}, 
		success: function(data){
		
		// put image name to hidden field for insert
		parent.document.getElementById(field+'_'+p_id).value = data;
		
		if(data=='')
		{
			alert('Error uploading image');
			parent.$.fn.colorbox.close();
			return false
		}
		else
		{
			if(data.substr(0,4)=='http')
				img_src = data;
			else if (data==-1)
			{
				parent.$.fn.colorbox.close();
				applyParentColorBox();
				alert(g_image_exists);
				return false;
			}	
			else
				img_src = g_frontend_folder_httppath+"/"+data;	 
		}	 
		
		// embed image
		if(parent.document.getElementById(field+'_'+p_id+'_img'))
		{
			parent.document.getElementById(field+'_'+p_id+'_img').src = img_src;
		}
		else
		{
			var current = parent.$('#'+field+'_'+p_id+'_td').html();
			parent.$('#'+field+'_'+p_id+'_td').html('<img id="'+field+'_'+p_id+'_img" src="'+img_src+'" width="'+g_grid_image_width+'"/><br/>'+current);
		}
		
		// if update field
		if(p_id.indexOf("newdata")==-1)
		{
			// update path to db
			id = p_id+'##'+field;
			value = data;
			
			$.post(g_ajax_file, {task: 'update_image',id:id,value:value},  
				function(data, textStatus)
				{
					
					showParentNoticeBox(data);
						
					parent.$.fn.colorbox.close();
					parent.loadData();
					
				}
			);
		}
		else
		{
			parent.$.fn.colorbox.close();
			applyParentColorBox();
		}
		}
	});
}

function validateURL(obj,msg){
        //var re = /http(s?):\/\/[A-Za-z0-9\.-]{3,}\.[A-Za-z]{3}/;
        var re = /(http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/
        if (re.test(obj.value)) {
                return true;
        }else{
                alert(msg);
                obj.focus();
                return false;
        }
}

function toggleCheckbox(obj){
	if (!obj.checked){
		$('input:checkbox').attr('checked','');
	}
	else{
		$('input:checkbox').attr('checked','checked');
	}
	updateCheckboxCount();
}

function updateCheckboxCount()
{
	var checked = $("input[name="+g_primary_id+"[]]:checked").length;
	$("#selected_cell_span").html(checked);
}
