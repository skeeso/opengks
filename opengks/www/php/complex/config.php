<?php
error_reporting(0);
$cfg['page_title'] = "OpenGKS Data Manager";

$cfg['http_root_path'] = "https://".$_SERVER['SERVER_ADDR']."/php/";
$cfg['photo_folder_path'] = "photos";
$cfg['module_http_path'] = $cfg['http_root_path']."complex/";
$cfg['frontend_folder_httppath'] = $cfg['module_http_path']."photos";
$cfg['module_name'] = "OpenGKS RFID Records";
$cfg['theme_color'] = "blue";
$cfg['ajax_file'] = "ajax.php";
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

$cfg['grid_image_width'] = "60px";
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
# 						in style.css we have num_col,image_col,dynamic_col,data_col,data_long_col,short_col,act_col

################################################################################################

#$cfg['create_table_sql'] = 'CREATE TABLE complex (
# product_id int(8) NOT NULL auto_increment,
# product_code varchar(255) NULL,
# product_name varchar(255) NULL,
# stock int(10) default NULL,
# price float default NULL,
# status tinyint(1) default NULL,
# image_path longtext,
# link varchar(255) NULL,
# PRIMARY KEY  (product_id)
#) ';
$cfg['db_table'] = 'login';
$cfg['db_field'] = array( 
					array(
							"field"=>"id",
							"title"=>"",
							"search"=>0,
							"show"=>0,
							"input_type"=>'',
							"field_type"=>$cfg['db_field_type']['int'],
							"order_unsigned"=>true,
							"allowed_data"=>array(),
							
							),
							
						array(
							"field"=>"image_path",
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
							"field"=>"rstnum",
							"title"=>"KLCID",
							"search"=>1,
							"show"=>1,
							"input_type"=>$cfg['record_input_type']['textbox'],
							"field_type"=>$cfg['db_field_type']['varchar'],
							"order_unsigned"=>true,
							"allowed_data"=>array(),
							"cell_css"=>'dynamic_col'
							),
 						array(
                                                        "field"=>"refnum",
                                                        "title"=>"RFID",
                                                        "search"=>1,
                                                        "show"=>1,
                                                        "input_type"=>$cfg['record_input_type']['textbox'],
                                                        "field_type"=>$cfg['db_field_type']['varchar'],
                                                        "order_unsigned"=>true,
                                                        "allowed_data"=>array(),
                                                        "cell_css"=>'dynamic_col'
                                                        ),

					  array(
							"field"=>"stname",
							"title"=>"FULL NAME",
							"search"=>1,
							"show"=>1,
							"input_type"=>$cfg['record_input_type']['textarea'],
							"field_type"=>$cfg['db_field_type']['varchar'],
							"order_unsigned"=>false,
							"allowed_data"=>array(),
							"cell_css"=>'dynamic_col'
							),
					array(
							"field"=>"stmobile",
							"title"=>"MOBILE",
							"search"=>1,
							"show"=>1,
							"input_type"=>$cfg['record_input_type']['textarea'],
							"field_type"=>$cfg['db_field_type']['varchar'],
							"order_unsigned"=>false,
							"allowed_data"=>array(),
							"cell_css"=>'dynamic_col'
							),		
					  array(
							"field"=>"stmail",
							"title"=>"EMAIL",
							"search"=>1,
							"show"=>1,
							"input_type"=>$cfg['record_input_type']['textbox'],
							"field_type"=>$cfg['db_field_type']['varchar'],
							"order_unsigned"=>true,
							"allowed_data"=>array(),
							"cell_css"=>'data_col'
							),
					  array(
							"field"=>"stlevel",
							"title"=>"LEVEL",
							"search"=>1,
							"show"=>1,
							"input_type"=>$cfg['record_input_type']['textbox'],
							"field_type"=>$cfg['db_field_type']['varchar'],
							"order_unsigned"=>true,
							"allowed_data"=>array(),
							"cell_css"=>'data_col'
							),
					  array(
							"field"=>"onhold",
							"title"=>"HOLD",
							"search"=>1,
							"show"=>1,
							"input_type"=>$cfg['record_input_type']['selection'],
							"field_type"=>$cfg['db_field_type']['tinyint'],
							"order_unsigned"=>true,
							"allowed_data"=> array(
					array("value"=>0,"title"=>'GREEN',"display"=>'<img src="'.$cfg['http_root_path'].'includes/images/tick.png" />'),
					array("value"=>1,"title"=>'ONHOLD',"display"=>'<img src="'.$cfg['http_root_path'].'includes/images/cross.png" />')),
							"cell_css"=>'short_col'						
							),
							
							);
						

$cfg['sys_msg']['record_inserted'] = "Record inserted";
$cfg['sys_msg']['fail_insert_record'] = "Fail to insert Record";

$cfg['sys_msg']['record_deleted'] = "Record deleted";
$cfg['sys_msg']['fail_delete_record'] = "Fail to delete Record";
$cfg['sys_msg']['image_removed'] = "Image removed";
$cfg['sys_msg']['fail_remove_image'] = "Failed to remove image";

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
$cfg['js_msg']['g_image_exists'] = "Image exists!";
$cfg['js_msg']['g_delete_record_image_confirm'] = "Are you sure you want to delete the image?";

$cfg['js_var']['g_grid_image_width'] = $cfg['grid_image_width'];
$cfg['js_var']['g_frontend_folder_httppath'] = $cfg['frontend_folder_httppath'];
$cfg['js_var']['g_http_root_path'] = $cfg['http_root_path'];
$cfg['js_var']['g_ajax_file'] = $cfg['ajax_file'];
$cfg['frontend_link_target'] = "_blank";							  
$cfg['htmlspecialchars_on'] = false;
?>
