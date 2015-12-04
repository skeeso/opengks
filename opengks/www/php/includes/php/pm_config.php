<?php
$cfg['page_title'] = "--";

$cfg['photo_folder_path'] = "../../photo";
$cfg['frontend_folder_httppath'] = "http://websolution.hk/sandbox/ajax_table2/php/photo";
$cfg['http_root_path'] = "http://websolution.hk/sandbox/ajax_table2/php/";
$cfg['ajax_file'] = $cfg['http_root_path']."includes/php/photo_manager_ajax.php";
# user login
$cfg['login'] = "admin";
$cfg['password'] = "gksadmin";
# database field type
$cfg['db_field_type']['int'] = "int";
$cfg['db_field_type']['varchar'] = "varchar";
$cfg['db_field_type']['text'] = "text";
$cfg['db_field_type']['date'] = "date";
$cfg['db_field_type']['datetime'] = "datetime";
$cfg['db_field_type']['tinyint'] = "tinyint";
$cfg['db_field_type']['float'] = "float";
$cfg['db_field_type']['double'] = "double";
$cfg['db_field_type']['longtext'] = "longtext";

$cfg['grid_image_width'] = "80px";
$cfg['library_image_width'] = "60";

# input type
$cfg['record_input_type']['textbox'] = 'textbox';
$cfg['record_input_type']['textarea'] = 'textarea';
$cfg['record_input_type']['selection'] = 'select';
$cfg['record_input_type']['media'] = 'media';
$cfg['record_input_type']['link'] = 'link';

# what to display if the data is empty string or null
$cfg['db_field_empty_display'] = '--';

# the link for opening color box to upload image
$cfg['upload_image_word'] = "Upload Image";

$cfg['thumbnail_resize_width'] = 180;
$cfg['thumbnail_resize_height'] = 180;

$cfg['frontend_datagrid_link_target'] = 'target="_blank"';

$cfg['no_record_msg'] = 'no record at this moment';

$cfg['db_table'] = 'photo_manager'; 
# db fields in table
################################################################################################
#   The first array of $cfg['db_field'] MUST be the primary id !!!!!!!
#
#   For each array, 
#	"field" 			The db table field name
#	"title" 			The name to display in the search selection box and data grid column title
#	"search" 			Whether this field is allowed to be searched
#	"show" 				Whether this field will be shown on the grid 
#	"input_type" 		The database data field (textbox,textarea,selection,from media library)
#	"field_type" 		Database field type
#	"order_unsigned"	Whether the field will be sort unsigned
#	"allowed_data" 		Set specific data to edit
#   "cell_css" 			The css class for the cell in data grid
# 						in style.css we have num_col,image_col,dynamic_col,data_col,data_col,short_col,act_col

################################################################################################

$cfg['db_field'] = array( 
					array(
							"field"=>"image_id",
							"title"=>"",
							"search"=>0,
							"show"=>0,
							"input_type"=>'',
							"field_type"=>$cfg['db_field_type']['int'],
							"order_unsigned"=>true,
							"allowed_data"=>array(),
							
							),
							
						array(
							"field"=>"image_name",
							"title"=>"Image",
							"search"=>0,
							"show"=>1,
							"input_type"=>$cfg['record_input_type']['media'],
							"field_type"=>$cfg['db_field_type']['int'],
							"order_unsigned"=>false,
							"allowed_data"=>array(),
							"grid_display"=>"'<img src=\"photo/',image_path,'\">'",
							"cell_css"=>'image_col'),
						
					array(
							"field"=>"image_location",
							"title"=>"URL",
							"search"=>1,
							"show"=>1,
							"input_type"=>$cfg['record_input_type']['link'],
							"field_type"=>$cfg['db_field_type']['varchar'],
							"order_unsigned"=>false,
							"allowed_data"=>array(),
							"cell_css"=>'dynamic_col'
							),		
					
					
					  array(
							"field"=>"status",
							"title"=>"Status",
							"search"=>1,
							"show"=>1,
							"input_type"=>$cfg['record_input_type']['selection'],
							"field_type"=>$cfg['db_field_type']['tinyint'],
							"order_unsigned"=>true,
							"allowed_data"=> array(
													array("value"=>1,"title"=>'Success',"display"=>'<img src="includes/images/tick.png" />'),
													array("value"=>2,"title"=>'Normal',"display"=>'<img src="includes/images/exclamation.png" />'),
													array("value"=>3,"title"=>'Fail',"display"=>'<img src="includes/images/cross.png" />')),
							"cell_css"=>'short_col'						
							),
							
							);
							//"{'1':'Success','2':'Normal','3':'Fail', 'selected':'2'}"

$cfg['sys_msg']['record_inserted'] = "Record inserted";
$cfg['sys_msg']['fail_insert_record'] = "Fail to insert Record";

$cfg['sys_msg']['record_deleted'] = "Record deleted";
$cfg['sys_msg']['fail_delete_record'] = "Fail to delete Record";

$cfg['sys_msg']['record_updated'] = "Record updated";
$cfg['sys_msg']['fail_update_record'] = "Fail to update record";

$cfg['sys_msg']['no_permission'] = "You don't have permission";


$cfg['js_msg']['g_delete_record_confirm'] = "Are you sure you want to delete the selected record(s)?";
$cfg['js_msg']['g_select_image'] = "Please select an image";
$cfg['js_msg']['g_input_resize_h'] = "Please enter the resize height";
$cfg['js_msg']['g_input_resize_w'] = "Please enter the resize width";
$cfg['js_msg']['g_either_upload_or_link'] = "Please either upload an image or input an image link";
$cfg['js_msg']['g_upload_or_link'] = "Please select a file to upload or insert an image link";
$cfg['js_msg']['g_delete_library_img'] = "Are you sure you want to delete the selected image?";
$cfg['js_msg']['g_incorrect_link_format'] = "Incorrect link format";

$cfg['js_var']['g_grid_image_width'] = $cfg['grid_image_width'];
$cfg['js_var']['g_frontend_folder_httppath'] = $cfg['frontend_folder_httppath'];
$cfg['js_var']['g_http_root_path'] = $cfg['http_root_path'];
$cfg['js_var']['g_ajax_file'] = $cfg['ajax_file'];
$cfg['frontend_link_target'] = "_blank";							  
?>
