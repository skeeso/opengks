<?php
session_start();
$path_root = "../";
include_once("config.php");
include_once($path_root."includes/php/db.php");
include_once($path_root."includes/php/table.php");

switch($_POST['task'])
{
	# generate datagrid, both edit mode and view mode
	case "gen_table":	
	
		$l_db = new database();
		
		$field				= $_POST['field'] ;
		$order				= $_POST['order'] ;
		$pageNo				= $_POST['pageNo'] ;
		$page_size			= $_POST['page_size'] ;
		$record_per_page	= $_POST['recordPerPage'] ;
		$search_field		= trim($_POST['search_field']);
		$search_str			= trim($_POST['search_str']);
		$current_mode		= $_POST['current_mode'];
		
		$li = new table($pageNo,$order,$field,$record_per_page,$current_mode);
		
		$li->field_array = $cfg['db_field'];
		
		# when user has entered the word to search
		if($search_str!='')
		{
			# when user has specificied which field to search
			if($search_field!='')
			{
				$cond = ' WHERE '.$li->field_array[$search_field]['field'].' like \'%'.$search_str.'%\'';	
			}
			# when no field is specificed, search all searchable field
			else 
			{
				for($a=0;$a<sizeof($li->field_array);$a++)
				{
					if($li->field_array[$a]['search']==true)
						$cond_arr[] = "(".$li->field_array[$a]['field']." like '%".$search_str."%')";
				}
				
				$cond = ' WHERE '.implode(" or ",$cond_arr);	
			}	
		}
		# get datagrid header column		
		$li->setColumnList();
		# get sql field
		$sql_field = $li->getGridSqlField();
		# the sql query to retrieve data from table
		$li->sql = 'SELECT
						'.implode(",",$sql_field).'
					 FROM
					 	'.$cfg['db_table'].'
					 	'. $cond;
			
		$li->form_name = "form1";
		$li->no_col = sizeof($li->field_array) + 1;
		if($li->isEditMode())$li->no_col++;
		
		$li->no_record_msg = $cfg['no_record_msg'];
				
		echo $li->generateRecordTable();
	
	break;
	
	# update field record
	case "update_record":
		$li = new table();
		if($li->hasAdminRight())
		{
			$obj 		= explode("##",trim($_POST['id']));
			$li->record_id = $obj[0];
			$li->field	= $obj[1];
			$data 		= trim($_POST['value']);
						
			# make sure field and primary id are set
			if($li->field!='' && $li->record_id!='')
			{
				$data = ($cfg['htmlspecialchars_on']==true)?htmlspecialchars($data):$data;
				$update_result = $li->updateTableRecord($data);
				
				# check what to return after update
				for($a=0;$a<sizeof($cfg['db_field']);$a++) 
				{
					$obj = $cfg['db_field'][$a];
					if($obj['field'] == $li->field)
					{
						# if it's media type, show notice box
						if($obj['input_type'] == $cfg['record_input_type']['media'])
						{
							if($update_result==true)
							{
								$notice_msg = $cfg['sys_msg']['record_updated'];
								$status = 1;
							}
							else
							{
								$notice_msg = $cfg['sys_msg']['fail_update_record'];
								$status = -1;
							}
							echo $li->genNoticeBoxMsg($notice_msg,$status);
						}
						# if not media type show the updated data in the cell
						else
						{							
							if($update_result==true)
							{
								# cater those that needs to show specific data
								if(sizeof($obj['allowed_data'])>0)
								{
									if($update_result==true)
									{
										for($k=0;$k<sizeof($obj['allowed_data']);$k++)
										{
											if($obj['allowed_data'][$k]['value']==$data)
											echo $obj['allowed_data'][$k]['display']; 
										}							
									}
								}
								else
								{
									echo stripslashes($data);	
								}
							}
							else
							{
								$cfg['sys_msg']['fail_update_record'];
							}
						}
					}
				}	
			}
		}
		else
		{
			echo $cfg['sys_msg']['no_permission'];
		}
	break;
	
	# update image in the cell
	case "update_image":
		$li = new table();
		if($li->hasAdminRight())
		{
			$obj 		= explode("##",trim($_POST['id']));
			$li->record_id = $obj[0];
			$li->field	= $obj[1];
			$data 		= trim($_POST['value']);
						
			if($li->field!='' && $li->record_id!='')
			{
				if($li->updateTableRecord($data)==true)
					echo $li->genNoticeBoxMsg($cfg['sys_msg']['record_updated'],true);
				else
					echo $li->genNoticeBoxMsg($cfg['sys_msg']['fail_update_record'],false);
			}
		}
		else
		{
			echo $cfg['sys_msg']['no_permission'];
		}
	break;
	
	# get a new row for inserting record in edit mode
	case "get_row":
		$li = new table();
		echo $li->genTableNewRow();
	break;
	
	# insert record to db
	case "insert_record":
		$li = new table();
		print_r($_POST);
		
		if($li->hasAdminRight())
		{
			# pass those expected field for inserting
			for($a=0;$a<sizeof($cfg['db_field']);$a++)
			{
				$obj = $cfg['db_field'][$a];
				if($obj['show'])
				{
					$field_arr[$obj['field']] = $_POST[$obj['field']];
				}
			}
			$last_insert_id = $li->insertRecord($field_arr);
		}
		else
		{
			echo $cfg['sys_msg']['no_permission'];
		}
	break;
	
	# delete a record in db 
	case "delete_record":
		$li = new table();
		if($li->hasAdminRight())
		{
			echo $li->deleteRecord($_POST['id']);	
		}
		else
		{
			echo $cfg['sys_msg']['no_permission'];
		}
	break;
	
	# delete a record image in db 
	case "delete_record_image":
		$li = new table();
		
		if($li->hasAdminRight())
		{
			if($cfg['db_field'][$_POST['field']]['field']!='')
				echo $li->emptyRecordValue($_POST['id'],$cfg['db_field'][$_POST['field']]['field']);	
		}
		else
		{
			echo $cfg['sys_msg']['no_permission'];
		}
	break;
	

	# upload an image, attaching from library, or refer to an image link 
	case "upload_image":
		$li = new table();
		echo $li->handleUploadImage($_POST['need_resize'],$_POST['image_link']);
	break; 
	
	# show a list of image uploaded previously
	case "gen_library_table":
		include_once($path_root."includes/php/library.php");
		$lib = new library($_POST['pageNo'],$_POST['order'],$_POST['field'],$_POST['recordPerPage'],trim($_POST['keyword']));
		$lib->selected_image = $_POST['selected_image'];
		$lib->form_name = "form1";
		echo $lib->genLibrary();
	break;	
	
	# remove image from folder
	case "delete_image":
		include_once($path_root."includes/php/library.php");
		$lib = new library();
		$lib->identifier = $_POST['id'];
		
		if($lib->identifier!='')
		{
			echo $lib->deleteImage();	
		}
	break;
}
?>