<?php

class table extends database{

	function table($page_no='',$order='',$field='',$record_per_page='',$current_mode='')
	{
		global $cfg;
		
		$this->pageNo 			= trim($page_no);
		$this->order 			= trim($order);
		$this->field 			= trim($field);
		$this->record_per_page	= trim($record_per_page);
		$this->current_mode		= $current_mode;
		$this->pageNo 			= ($this->pageNo=='0'||$this->pageNo!='')?$this->pageNo:"1";
		$this->order 			= ($this->order=='0'||$this->order!='')?$this->order:"1";
		$this->field 			= ($this->field=='0'||$this->field!='')?$this->field:"2";
		$this->record_per_page 	= ($this->record_per_page=='0'||$this->record_per_page!='')?$this->record_per_page:"10";
		$this->form_name		= 'form1';
		
		$this->record_id		= '';
		$this->primary_id 		= $cfg['db_field'][0]['field'];
		
		# create table if it does not exist
		if($cfg['create_table_sql']!='')
			$this->runQuery($cfg['create_table_sql']);
		
	}
	
	function isEditMode()
	{
		return $this->current_mode=='e';
	}
	
	function isViewMode()
	{
		return $this->current_mode=='v';
	}
	
	
	function column($field_index, $field_name)
    {
	    $x = "";
	    if($this->field==$field_index)
	    {
	        $sort_order = ($this->order==1)? 0 : 1;
	        $class  	= ($this->order==1)? 'sort_asc':'sort_dec';
	    }
    	else
    	{
	    	$sort_order=1;
    	}
    	$x .= '<a class="'.$class.'" href="javascript:ajax_sort('.$sort_order.','.$field_index.',document.'.$this->form_name.')" >'.$field_name.'</a>';
    	
    	return $x;
    }
	
    function built_sql()
    {

         $x = $this->sql;
       
         $x = str_replace("\n"," ",$x);
         $x = str_replace("\r"," ",$x);
         $x = str_replace("\t"," ",$x);
         $y = "SELECT COUNT(*) " . stristr($x, " FROM ");
         $this->rs=$this->runQuery($y);
         if ($this->db_num_rows()!=0)
         {
             $row = $this->db_fetch_array();
             $this->total_row = ($this->db_num_rows()!=1) ? $this->db_num_rows() : $row[0];
         }

        $max_page = ceil($this->total_row/$this->record_per_page);
        if ($this->pageNo > $max_page && $max_page != 0)
        {
            $this->pageNo = $max_page;
        }
        $this->n_start=($this->pageNo-1)*$this->record_per_page;

        $x .= " ORDER BY ";
        
        if($this->field_array[$this->field]['order_unsigned'])
        	$x.=" cast(";
        	
        $this->order_field_name = (count($this->field_array)<=$this->field) ? $this->field_array[0]['field'] : $this->field_array[$this->field]['field'];      
        $x .= (count($this->field_array)<=$this->field) ? $this->field_array[0]['field'] : $this->field_array[$this->field]['field'];
        
        if($this->field_array[$this->field]['order_unsigned'])
        	$x.=" as unsigned )";
		
		$x .= ($this->order==0) ? " DESC" : " ASC";
        $x .= " LIMIT ".$this->n_start.", ".($this->record_per_page * $this->pageNo);

        return $x;
    }    
        
	function generateRecordTable()
	{
		global $cfg;
		
		$this->rs = $this->runQuery($this->built_sql());
		$n_start = $this->n_start;
		
		$x .= '<table id="record_table" class="invoice_input common_table"><thead>'.$this->displayColumn().'</thead>';
		$x .="<tbody>";
		if ($this->db_num_rows()==0)
		{
			$x .= '<tr><td align="center" colspan="'.$this->no_col.'"><br>'.$this->no_record_msg.'<br><br></td></tr>';
		}
		else
		{

			$sql_field_obj = $cfg['db_field'];
			$i = 0;
			while($row = $this->db_fetch_array()) 
			{				
				$i++;
				if($i>$this->record_per_page) break;
				
				$class = (($i%2)==0)?'alt_row':'';
				$x .= '<tr class="'.$class.'">';
				
				if($this->isViewMode())
					$x .= '<td>'.($n_start+$i).'</td>';
				elseif($this->isEditMode())
					$x .= '<td><input type="checkbox" id="'.$this->primary_id.'[]" name="'.$this->primary_id.'[]" value="'.($row[$this->primary_id]).'" onClick="updateCheckboxCount();"/></td>';
								
				for($j=0; $j<$this->no_col-1; $j++)
				{
					# show action btn is edit mode
					$edit_action_btn = ($this->isEditMode() && $j==$this->no_col-2);
					
					# if we don't need to show this column of data
					if($sql_field_obj[$j]['show']==0 && !$edit_action_btn)
					{
						continue;
					}
														
					if($this->isEditMode())
					{
						# setup css for editable
						$edit_css = 'edit_'.$sql_field_obj[$j]['field'];
						# setup the td id for editable.
						$td_id = $row[$this->primary_id].'##'.$sql_field_obj[$j]['field'];
					}		
					
					# css for each cell
					$cell_css = $sql_field_obj[$j]['cell_css'];
											
					# check for fixed data to display, such as status image	
					$allowed_data = $sql_field_obj[$j]['allowed_data'];
													
					if(sizeof($allowed_data)>0)
					{
						for($k=0;$k<sizeof($allowed_data);$k++)
						{
							if($allowed_data[$k]['value'] == $row[$j])
							{
								$display = $allowed_data[$k]['display'];
								break;
							}
							else
								$display = $cfg['db_field_empty_display'];
						}
						if($this->isViewMode())
							$display = nl2br($display);
						else
							$display = $display;	
						$x .= '<td class="'.$edit_css.' '.$cell_css.'" id="'.$td_id.'" >'.$display.'</td>';
					}
					else
					{
						if($this->isViewMode())
							$display = nl2br($row[$j]);
						else
							$display = $row[$j];
						
						$x .= '<td class="'.$edit_css.' '.$cell_css.'" id="'.$td_id.'">'.$display.'</td>';
					}
				}
				$x .= "</tr>\n";
			}
			
		}
		$x .= "</tbody>"; 
		$x.='<input type="hidden" id="order" name="order" value="'.$this->order.'"/>';
		$x.='<input type="hidden" id="field" name="field" value="'.$this->field.'"/>';
		$x.='<input type="hidden" id="pageNo" name="pageNo" value="'.$this->pageNo.'"/>';
		$x.='<input type="hidden" id="recordPerPage" name="recordPerPage" value="'.$this->record_per_page.'"/></table>';
		
		# display the page info if there's record
		if($this->db_num_rows()<>0)
		{
        	$x.='<div class="table_bottom_left_tool"><div class="item_per_page"><span>Items per page: </span>'.$this->record_per_page_input().'</div></div>';
        	$x.='<div class="table_bottom_right_tool"><div class="page_area">'.$this->record_range().$this->go_page().'</div></div>';
    	}
		$x.='<br class="clear" />';
		
		
		return $x;
	}
	    
    function record_per_page_input ()
    {
         $x = " <select name='num_per_page' onChange='ajax_changeRecordPerPage(".$this->form_name.",this.options[this.selectedIndex].value)'>\n";
         $x .= "<option value=10 ".($this->record_per_page==10? "SELECTED":"").">10</option>\n";
         $x .= "<option value=20 ".($this->record_per_page==20? "SELECTED":"").">20</option>\n";
         $x .= "<option value=30 ".($this->record_per_page==30? "SELECTED":"").">30</option>\n";
         $x .= "<option value=40 ".($this->record_per_page==40? "SELECTED":"").">40</option>\n";
         $x .= "<option value=50 ".($this->record_per_page==50? "SELECTED":"").">50</option>\n";
         $x .= "<option value=60 ".($this->record_per_page==60? "SELECTED":"").">60</option>\n";
         $x .= "<option value=70 ".($this->record_per_page==70? "SELECTED":"").">70</option>\n";
         $x .= "<option value=80 ".($this->record_per_page==80? "SELECTED":"").">80</option>\n";
         $x .= "<option value=90 ".($this->record_per_page==90? "SELECTED":"").">90</option>\n";
         $x .= "<option value=100 ".($this->record_per_page==100? "SELECTED":"").">100</option>\n";
         $x .= "</select>";
         return $x;
    }
    
    
	function displayColumn()
    {
        $x .= "<tr>".$this->column_list."</tr>";
        
        return $x;
    }
	
	function record_range()
	{
		$total_page = ceil($this->total_row/$this->record_per_page);
	    $x = '<span class="pages">Page '.$this->pageNo.' of '.$total_page.'&#8201;</span>';
	    return $x;
	}
        
   	function go_page()
	{
		$page_total = ceil($this->total_row/$this->record_per_page);
		
		# control the page option
		$max_page_option = 1;
		
		# first page button
		$x.= ($this->pageNo>1)?'<a href="javascript:ajax_gopage(1,document.'.$this->form_name.');" title="First Page">&laquo;</a>':'';
		# previous page button
		$x.= ($this->pageNo>1)?'<a href="javascript:ajax_gopage('.($this->pageNo-1).',document.'.$this->form_name.');" title="Previous Page">&lsaquo;</a>':'';
		
		if($this->pageNo-$max_page_option-1>0)
		$x.='<span class="dot">...</span>';
		
		for($a=0;$a<$page_total;$a++)
		{
			$page = $a+1;
			if($page == $this->pageNo)
			{
				$x.='<span class="current">'.$page.'</span>';
			}
			else
			{
				if($page<$this->pageNo && $page>=$this->pageNo-$max_page_option)
				{
					$x.='<a href="javascript:ajax_gopage('.$page.',document.'.$this->form_name.');" title="Page '.$page.'">'.$page.'</a>';	
				}
				else if($page>$this->pageNo && $page<=$this->pageNo+$max_page_option)
				{
					$x.='<a href="javascript:ajax_gopage('.$page.',document.'.$this->form_name.');" title="Page '.$page.'">'.$page.'</a>';
				}
				
			}
		}
		if($page_total-$this->pageNo>$max_page_option)
		$x.='<span class="dot">...</span>';
		
		# next page button
		$x .= ($this->pageNo<$page_total)?'<a href="javascript:ajax_gopage('.($this->pageNo+1).',document.'.$this->form_name.');" title="Next Page">&rsaquo;</a>':'';
		# last page button
		$x .= ($this->pageNo<$page_total)?'<a href="javascript:ajax_gopage('.($page_total).',document.'.$this->form_name.');" title="Last Page">&raquo;</a>':'';

		
		return $x;		
	}
	
	function genSearchFilter()
	{
		global $cfg;
		$x='<select class="col_filter" id="search_field" name="search_field"><option value="">-- All Column--</option>';

		$field_arr = $cfg['db_field'];
		for($a=0;$a<sizeof($field_arr);$a++)
		{
			# only get those that are set to be searchable
			if($field_arr[$a]['search']==true)
			{
				# get the field display name (not sql name)
				$x.='<option value="'.$a.'">'.$field_arr[$a]['title'].'</option>';
			}
		}
		$x.='</select>';
		return $x;	
	}
		
	function updateTableRecord($data)
	{
		global $cfg;
		
		$sql = "UPDATE ".$cfg['db_table']." set ".$this->field." = '".$data."' WHERE ".$this->primary_id." = '".$this->record_id."'";
		
		return $this->runQuery($sql);
	}
	
	function genHeader($isThickbox=false,$css='')
	{
		global $cfg;
		
		$html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
				<html xmlns="http://www.w3.org/1999/xhtml">
				<head>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				<meta http-equiv="Cache-Control" content="no-cache">
				<title>'.$cfg['page_title'].'</title>
				<link href="'.$cfg['http_root_path'].'includes/css/style.css" rel="stylesheet" type="text/css" />
				<link id="color_css" href="'.$cfg['http_root_path'].'includes/css/'.$css.'.css" rel="stylesheet" type="text/css" /> <!-- color css-->';
				if($isThickbox==true)
				$html.='<link id="color_css" href="'.$cfg['http_root_path'].'includes/css/thick.css" rel="stylesheet" type="text/css" /> <!-- color css-->';
				$html.='<!--[if lte IE 7]>
				<link href="'.$cfg['http_root_path'].'includes/css/ie.css" rel="stylesheet" type="text/css" />
				<![endif]-->
				
				<!-- jQuery -->
				<script type="text/javascript" src="'.$cfg['http_root_path'].'includes/js/jquery-1.3.2.min.js"></script>
				
				<!-- jQuery Configuration -->
				<script type="text/javascript" src="'.$cfg['http_root_path'].'includes/js/script.js"></script>
				
				<!-- jQuery pngfix plugin -->
				
				<script type="text/javascript" src="'.$cfg['http_root_path'].'includes/js/jquery.pngFix.js"></script>  
				<script language="JavaScript" src="'.$cfg['http_root_path'].'includes/js/editable.js"></script>
				<script language="JavaScript" src="'.$cfg['http_root_path'].'includes/js/script.js"></script>
				<script language="JavaScript" src="'.$cfg['http_root_path'].'includes/js/jquery.form.js"></script>
				<script language="JavaScript" src="'.$cfg['http_root_path'].'includes/js/jquery.validate.js"></script>
				<script language="JavaScript" src="'.$cfg['http_root_path'].'includes/colorbox/jquery.colorbox.js"></script>
				<link type="text/css" media="screen" rel="stylesheet" href="'.$cfg['http_root_path'].'includes/colorbox/colorbox.css" />';	
		echo $html;		
	}
	
	function genFooter()
	{
		echo "</html>";	
	}
	
	function genModeTab($mode)
	{
		if($mode=='v')
		{
			$view_list = '<li class="current">View Mode</li>';
			$edit_list = '<li><a href="edit.php">Edit Mode</a></li>';
		}
		else if($mode=='e')	
		{
			$view_list = '<li><a href="index.php">View Mode</a></li>';
			$edit_list = '<li class="current">Edit Mode</li>';
		}
			
		$html='<ul class="admin_tab table_top_right_tool">
    			'.$view_list.'
    			'.$edit_list.'
    			</ul>';
    	return $html;		
	}
	
	function hasAdminRight()
	{
		
		if($_SESSION['is_login']==true)
		return true;	
	}
	
	function checkAccess()
	{
		if(!$this->hasAdminRight())
		header("location:index.php");
	}
	
	function auth($username,$password)
	{
		global $cfg;
		
		# user is now login and can access admin pages
		if($username==$cfg['login'] && $password==$cfg['password'])
			$_SESSION['is_login'] = true;
		return true;
	}
	
	function genLoginBar()
	{
		global $cfg;	
		$html = '<div class="header">
					<div class="top">
				    	<a href="#" class="logo"></a>
				    	<!-- top right btn -->
						<span class="admin_top_right">';
				        
		if($this->hasAdminRight())
		{	        
			$html.='welcome: <strong>'.$cfg['login'].'</strong> (<a href="logout.php">logout</a>)';	
		}
		else
		{
			$html.= 'login: <input type="text" name="username" />&nbsp;&nbsp;&nbsp;
			         password: <input type="password" name="password" /><input class="action_btn small submit" type="button" value="Login" onClick="document.form1.submit();"/>';
		}
		$html.='</span></div></div>';
		return $html;			
		
	}
	
	
	function genTableNewRow()
	{
		global $cfg;
		
		$field_arr = $cfg['db_field'];
				
		for($a=0;$a<sizeof($field_arr);$a++)
		{
			$obj = $field_arr[$a];
			
			if($obj['show'])
			{
				$input = $this->genInsertDataInput($obj['input_type'],$obj['field'].'_newdata',$obj['field']);
				$row.="<td id=\"".$obj['field']."_newdata_td\">".$input."</td>";
			}
		}
		$html='<tr id="newdata_tr">
				<td>&nbsp;</td>
            	'.$row.'
                <td class="act_col">
                
                <a href="javascript:removeThisRow(\'newdata_tr\')" class="del_btn" title="delete"></a></td>
            </tr>';
				//<a href="javascript:insertRecord(\'newdata\')" class="insert_btn" title="insert"></a>            
        
            return $html;
			
	}
	function genInsertDataInput($type,$name,$field)
	{
		global $cfg;
		switch ($type)
		{
			case $cfg['record_input_type']['textbox']:
			case $cfg['record_input_type']['link']:
				$html = '<input type="text" class="edit_data" id="'.$name.'" name="'.$name.'" />';
			break;
			case $cfg['record_input_type']['textarea']:
				$html = '<textarea class="edit_data" id="'.$name.'" name="'.$name.'" ></textarea>';
			break;
			case $cfg['record_input_type']['media']:
			
				$html = '<a href="upload.php?field='.$field.'&id=newdata" class="library" title="">'.$cfg['upload_image_word'].'</a>';
				$html .= '<input type="hidden" class="edit_data" id="'.$name.'" name="'.$name.'" />';
			break;
			case $cfg['record_input_type']['selection']:
				$html = '<select id="'.$name.'" name="'.$name.'">';
				for($a=0;$a<sizeof($cfg['db_field']);$a++)
				{
					$obj = $cfg['db_field'][$a];
					if($obj['field']==$field)
					{
 
						for($k=0;$k<sizeof($obj['allowed_data']);$k++)
							$html.='<option value="'.$obj['allowed_data'][$k]['value'].'">'.$obj['allowed_data'][$k]['title'].'</option>';
					}
					
				}
						 
						
				$html.='</select>';
			break;
			default:
				$html = '<input type="text" class="edit_data" id="'.$name.'" name="'.$name.'" />';
			
		}
		return $html;
	}
		
	
	function genInsertRecordJS()
	{
		global $cfg;
		
		$var = '';
		for($a=0;$a<sizeof($cfg['db_field']);$a++)
		{
			$obj = $cfg['db_field'][$a];
			
			if($obj['show'])
			{
				if($var=='')
					$var.= "if(document.getElementById('".$obj['field']."_'+index)){\n";
				$var.= " var ".$obj['field']." = $('#".$obj['field']."_'+index).val();\n";
				
				$par[] = $obj['field'].":".$obj['field']; 
			}
		}		 
		
		$success_msg = $this->genNoticeBoxMsg($cfg['sys_msg']['record_inserted'],true);
		$fail_msg = $this->genNoticeBoxMsg($cfg['sys_msg']['fail_insert_record'],false);

		$ajax = "$.post(\"ajax.php\", {task: 'insert_record',".implode(",",$par)."},  
			function(data, textStatus)
			{
				showNoticeBox(success_msg);
				loadData();	
			}
		);";
		
		$js = "function insertRecord(index)\n
				{
					var success_msg = '".$success_msg."';\n
					var fail_msg = '".$fail_msg."';\n
					
					$var	
					
					$ajax
					}
				}";
				
		return $js;		
	}
	
	function insertRecord($field_arr)
	{
		global $cfg;
		
		if(is_array($field_arr))
		{
			$ct = 0;
			foreach($field_arr as $field=>$value)
			{
				$fields.= ($ct>0)?",":"";
				$fields.= $field;
				
				$values.= ($ct>0)?",":"";
				$values.= "'".$value."'";
				
				$ct++;
			}
			
			$sql = "INSERT INTO ".$cfg['db_table']." 
					(".$fields.")
					values(".$values.");";
					
			if($this->runQuery($sql))
				return mysql_insert_id();
		}
	}
		
	function setColumnList()
	{
		global $cfg;
		
		if($this->isEditMode())
			$this->column_list .= '<th class="num_col"><input id="checkbox" type="checkbox" onclick="toggleCheckbox(this)" id="master_checkbox" name="master_checkbox"/></th>';
		else
			$this->column_list .= '<th class="num_col">#</th>';
			
		for($a=0;$a<sizeof($this->field_array);$a++)
		{
			# if we need to show this column of data
			if($this->field_array[$a]['show']==1)
				$this->column_list .= '<th class="'.$this->field_array[$a]['cell_css'].'">' . $this->column($a, $this->field_array[$a]['title']) . '</th>';
		}
	
		if($this->isEditMode())
		{
			$this->column_list .= '<th class="act_col">Action</th>';
		}	
	}
	
	function getGridSqlField()
	{
		global $cfg;
		
		$sql_field = array();
		for($a=0;$a<sizeof($this->field_array);$a++)
		{
			$field_name = $this->field_array[$a]['field'];
			
			$upload_btn = ''; 
			
			# if it's image field
			if($this->field_array[$a]['input_type'] == $cfg['record_input_type']['media'])
			{


				
				if($this->isEditMode())
				{ 
					//$attach_img = "<a href=\"library.php?id=',".$this->primary_id.",'&field=".$field_name."\" class=\"library\" title=\"\"><img src=\"".$cfg['frontend_folder_httppath']."/',".$field_name.",'\" width=\"".$cfg['grid_image_width']."\" title=\"Click to edit image\"></a>";
					//$http_img = "<a href=\"upload.php?id=',".$this->primary_id.",'&field=".$field_name."\" class=\"library\" title=\"\"><img src=\"',".$field_name.",'\" width=\"".$cfg['grid_image_width']."\"></a>";
					$attach_img = "<img src=\"".$cfg['frontend_folder_httppath']."/',".$field_name.",'\" width=\"".$cfg['grid_image_width']."\"><br/><a href=\"library.php?id=',".$this->primary_id.",'&field=".$field_name."\" class=\"library\" title=\"\">Edit</a>";
					$http_img = "<img src=\"',".$field_name.",'\" width=\"".$cfg['grid_image_width']."\"><br/><a href=\"upload.php?id=',".$this->primary_id.",'&field=".$field_name."\" class=\"library\">Edit</a>";
					$remove_img = " | <a href=\"javascript:removeRecordImage(',".$this->primary_id.",',".$a.")\">Remove</a>";
				}
				else
				{	
					$attach_img = "<a href=\"".$cfg['frontend_folder_httppath']."/',".$field_name.",'\" class=\"library\" target=\"_blank\" title=\"\"><img src=\"".$cfg['frontend_folder_httppath']."/',".$field_name.",'\" width=\"".$cfg['grid_image_width']."\"></a>";
					$http_img = " | <a href=\"',".$field_name.",'\" class=\"library\" target=\"_blank\"><img src=\"',".$field_name.",'\" width=\"".$cfg['grid_image_width']."\"></a>";
				}
				
				$hidden_field = "<input type=\"hidden\" id=\"".$field_name."_',".$this->primary_id.",'\" name=\"".$field_name."_',".$this->primary_id.",'\" />";
				 
				$data_display = "if(substr(".$field_name.",1,4)='http',concat('".$http_img.$remove_img.$hidden_field."'),concat('".$attach_img.$remove_img.$hidden_field."'))";
				if($this->isEditMode())
				{
					$null_display = "<a href=\"upload.php?field=".$field_name."&id=',".$this->primary_id.",'\" class=\"library\" title=\"\">".$cfg['upload_image_word']."</a>";
					$null_display .= "<input type=\"hidden\" id=\"".$field_name."_',".$this->primary_id.",'\" name=\"".$field_name."_',".$this->primary_id.",'\" />";
					
					$upload_btn = $null_display;
				}
				else
				{
					$null_display = $cfg['db_field_empty_display'];
					$upload_btn = '';
				}
				
			}
			else if($this->field_array[$a]['input_type'] == $cfg['record_input_type']['link'])
			{
				if($this->isEditMode())
				{
					$data_display = $field_name;
				}
				else
				{
					$data_display = "'<a href=\"',".$field_name.",'\" ".$cfg['frontend_datagrid_link_target'].">',".$field_name.",'</a>'";
				}
			}
			else
			{				
				$data_display = $field_name;
				$null_display = $cfg['db_field_empty_display'];
			}
				
			$sql_field[$a]= "if((".$field_name."!='' && $field_name is not null) || ".$field_name."='0',
								concat(".$data_display."),
								concat('".$null_display."')
							) as ".$field_name;
		}
			
		if($this->isEditMode())
		{
			$primary_id = $this->field_array[0]['field']; // primary id field must be the first field array in $cfg['db_field']
			$sql_field[] = ' concat(\'<a href="javascript:deleteRecord(\','.$primary_id.',\')" class="del_btn" title="delete"></a>\')';
		}
		return $sql_field;
	}
	
	function deleteRecord($id)
	{
		global $cfg;
		
		$sql = "DELETE FROM ".$cfg['db_table']." WHERE ".$this->primary_id." in (".$id.")";
		
		if($this->runQuery($sql))
			return $this->genNoticeBoxMsg($cfg['sys_msg']['record_deleted'],true);
		else
			return $this->genNoticeBoxMsg($cfg['sys_msg']['fail_delete_record'],false);
	}
	
	function emptyRecordValue($id,$field)
	{
		global $cfg;
		
		$sql = "UPDATE ".$cfg['db_table']." SET ".$field." = '' WHERE ".$this->primary_id." = ".$id;
		
		if($this->runQuery($sql))
			return $this->genNoticeBoxMsg($cfg['sys_msg']['image_removed'],true);
		else
			return $this->genNoticeBoxMsg($cfg['sys_msg']['fail_remove_image'],false);
	}
	
	function genJSVariable()
	{  
		global $cfg;
		
		$js = '<script language="Javascript">';
		foreach ($cfg['js_msg'] as $var_name => $value)
		{
			$js.= "var ".$var_name." = '".$value."';\n"; 
		}
		foreach ($cfg['js_var'] as $var_name => $value)
		{
			$js.= "var ".$var_name." = '".$value."';\n"; 
		}
		# primary id
		$js.= "var g_primary_id = '".$this->primary_id."';\n";
		
		$field_ct = 0;
		for($a=0;$a<sizeof($cfg['db_field']);$a++)
		{
			if($cfg['db_field'][$a]['show'])
				$field_ct++;
		}
		$js.= "var g_sql_field_count = ".$field_ct.";\n";
		$js.='</script>';
		return $js;		
	}
	
	function handleUploadImage($need_resize,$image_link)
	{
		global $cfg;
				
		if($_FILES['image']['name']!='')
		{
			$org_folder = $cfg['photo_folder_path'].'/'.$_FILES['image']['name'];
					
			if(is_file($org_folder))
				return -1;	
			$temp_name =$_FILES['image']['tmp_name'];  
			$userfile_type =$_FILES['image']['type'];  
			
			if (!($userfile_type =="image/bmp" OR $userfile_type =="image/pjpeg" OR $userfile_type =="image/jpeg" OR $userfile_type=="image/gif" OR $userfile_type=="image/png" OR $userfile_type=="image/x-png")){  
			return false;  
			}  
			
			# store original pic to be stored    
			copy ($temp_name, $org_folder);
			chmod($org_folder,0777);
						
								
			$data = fread(fopen($temp_name, "rb"), filesize($temp_name));  
			$src_image = imagecreatefromstring($data);  
			$width = imagesx($src_image);  
			$height = imagesy($src_image);  
			
			if($need_resize==false)
			{
				
			}
			else
			{
				# original image ratio
				$image_ratio = $width/$height;
				
				#  resize
				if($image_ratio<($_POST['resize_size_w']/$_POST['resize_size_h']))
				{			
					$resize_width = $_POST['resize_size_w'];
					$resize_height = $_POST['resize_size_w']/$image_ratio;
				}
				else
				{
					$resize_height = $_POST['resize_size_h'];  
					$resize_width = $_POST['resize_size_h']*$image_ratio;
				}
				
				$dest_img = imagecreatetruecolor($resize_width, $resize_height);  
				imagecopyresized($dest_img, $src_image,0, 0, 0, 0,$resize_width, $resize_height,$width, $height);  
				
				ob_start();  
				if($userfile_type == "image/jpeg" OR $userfile_type == "image/pjpeg"){  
				    imagejpeg($dest_img,$org_folder,100);  
				}  
				if($userfile_type == "image/gif"){  
				    imagegif($dest_img,$org_folder,100);  
				}  
				if($userfile_type == "image/png" OR $userfile_type == "image/x-png"){  
				    imagepng($dest_img,$org_folder,9);  
				}  
				$binary_image = ob_get_contents();  
				ob_end_clean();  
				ob_start();  
					
				ob_end_clean();  
				if(!get_magic_quotes_gpc()){  
				    $binary_image=addslashes($binary_image);  
	
				}  
			}
			
			return $_FILES['image']['name'];	
		}
		else if($image_link!='')
		{
			if($this->isLinkImage($image_link))
				return $image_link;	
			else
				return $cfg['frontend_folder_httppath']."/".$image_link;
		}
	}
	
	function genEditableFunction()
	{
		global $cfg;
		
		$js = 'function applyEditable(){';
		for($a=0;$a<sizeof($cfg['db_field']);$a++)
		{
			$obj = $cfg['db_field'][$a];
			
			if($obj['show'])
			{
				if($obj['input_type'] == $cfg['record_input_type']['textbox'] || $obj['input_type'] == $cfg['record_input_type']['link'])
				{
					$js.='$(\'.edit_'.$obj['field'].'\').editable(\'ajax.php\', {
									    indicator : "Saving...",
										tooltip   : "Double-click to edit",
										event : "dblclick",
										submitdata: {task:"update_record"},
										onblur: "submit",
										callback : function(value, settings) 
										{
											
										}
								});';
				}
				if($obj['input_type'] == $cfg['record_input_type']['textarea'])
				{
					$js.='$(\'.edit_'.$obj['field'].'\').editable(\'ajax.php\', {
									    indicator : "Saving...",
										tooltip   : "Double-click to edit",
										event : "dblclick",
										submitdata: {task:"update_record"},
										onblur: "submit",
										type    : "textarea",
										callback : function(value, settings) 
										{
											
										}
								});';
				}
				if($obj['input_type'] == $cfg['record_input_type']['selection'])
				{
					# get the allowed data from config for the selection box option
					$allowed_data_arr = array();
					for($k=0;$k<sizeof($obj['allowed_data']);$k++)
					{
						$allowed_data_arr[]= "'".$obj['allowed_data'][$k]['value']."':'".$obj['allowed_data'][$k]['title']."'";
					}
					$allowed_data = "{".implode(",",$allowed_data_arr)."}";
					
					$js.='$(\'.edit_'.$obj['field'].'\').editable(\'ajax.php\', {
									    indicator : "Saving...",
										tooltip   : "Double-click to edit",
										event : "dblclick",
										submitdata: {task:"update_record"},
										onblur: "submit",
										data   : "'.$allowed_data.'",
			     						type   : "select",
										callback : function(value, settings) 
										{
											
										}
								});';
				}
			}
		}
		$js.='}';
		
		return $js;
	}
	
	function getEmbededImage($field,$id)
	{
		global $cfg;
		$sql = "SELECT ".$field." FROM ".$cfg['db_table']." where ".$this->primary_id." = '".$id."'";
		$obj = $this->getResultVector($sql);
		return $obj[0];
	}
	
	function isLinkImage($image)
	{
		return strtoupper(substr($image,0,4))=='HTTP';
	}
	
	function genNoticeBoxMsg($msg,$status)
	{
		$css = ($status==true)?"success":"alert";
		return '<div class="'.$css.'" id="notice_msg_div">'.$msg.'</div>';
	}
	
	function genModuleMenu()
	{
		$html = '<div class="style_menu">
		Chose other example:
		<span><a href="../photo_manager/">Photo Manager (Silver)</a></span> | 
		<span><a href="../product/">Product (Red)</a></span> | 
		<span><a href="../website/">Website Reference (Green)</a></span> | 
		<span><a href="../complex/">Complex (Blue)</a></span>
		</div>';
		return $html;
	}
}
?>