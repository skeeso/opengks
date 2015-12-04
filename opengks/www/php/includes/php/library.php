<?php

include_once('config.php');
class library {

	function library($page_no='',$order='',$field='',$record_per_page='',$keyword='')
	{
		global $cfg;
		
		$this->folder_location 	= $cfg['photo_folder_path'];
		$this->initial_pageNo	= $page_no; // for catering the searching of selected image in image array 
		$this->pageNo 			= ($page_no=='0'||$page_no!='')?$page_no:"1";
		$this->record_per_page 	= ($record_per_page=='0'||$record_per_page!='')?$record_per_page:"10";
		$this->record_from		= ($this->pageNo-1) * $this->record_per_page;
		$this->record_to 		= $this->record_from + $this->record_per_page-1;
		$this->keyword			= $keyword;
		
	}
	
	function getFolderImage($read_all=false,$image_to_view='') 
	{		
		include_once('libfilesystem.php');
		$lf = new libfilesystem();
		
		# get the image in specified folder
		
		$folder_list = $lf->return_folderlist($this->folder_location);

		$show_this = false;
		$sort_arr = array();
				
		for($a=0;$a<sizeof($folder_list);$a++)
		{	
			$this->extension = $lf->getFileExtension($folder_list[$a]);
			if($this->extension!='')
			{
				$this->file_name = $lf->file_name($folder_list[$a]);
				$this->identifier = $this->file_name.$this->extension;
				$sort_arr[$this->identifier] = $this->file_name;	
				
				
			}
		}
		
		# sort by name
		natcasesort($sort_arr);
						
		# count the file number
		$record_count=0;
		
		# for array indexing
		$arr_count = 0;
		
		# count how many pics are being displayed
		$count_display = 0;

		// if the selected image is not in page one, find which page it is and go to that page 
		if($this->selected_image!='' && $this->initial_pageNo=='')
		{
			$ct = 1;
			foreach($sort_arr as $this->identifier => $this->file_name)
			{
				if($this->selected_image==$this->identifier)
				{
					if($ct>$this->record_per_page)
					{
						$actual_page_no = ceil($ct/$this->record_per_page);
						if($actual_page_no!=$this->pageNo)
						{	
							$this->pageNo = $actual_page_no; 
							$this->record_from		= ($this->pageNo-1) * $this->record_per_page;
							$this->record_to = $this->record_from +  $this->record_per_page;
						}
					}	
					break;
				}
				else
				{
					$ct++;
				}
			}
		}
		
		# loop throught the image array and set display
		foreach($sort_arr as $this->identifier => $this->file_name)
		{	
			$show_this = false;
			# if specified a file to look for
			if($image_to_view!='')
			{
				if($image_to_view == $this->identifier)
				$show_this = true;
			}
			else
			{
				# if in current range, put into array	
				if($this->keyword!='')
				{				
					if(trim(strpos(strtoupper($this->identifier),strtoupper($this->keyword)))!='')
					{
						$show_this = true;
						$count_display++;
					}
					
				}
				else
				{
					if($record_count>=$this->record_from && $record_count<=$this->record_to )
					{
						$show_this = true;
					}
					$count_display++;
				}
				
				if($read_all==true)
				{
					$show_this = true;
					$count_display++;
				}
			}
			if($show_this==true)
			{
				
				$photo_src[$arr_count] = array('http'=>'/'.$this->identifier,'path'=>'/'.$this->identifier);
				
				$arr_count++;
			}
			$record_count++;					
		}
		
		# set pagination information
		$this->total_row = $count_display;
		$this->max_page = ceil($this->total_row/$this->record_per_page);

		return $photo_src;
	}
	
	function genPaginationHiddenField()
	{
		$hidden_value = '<input type="hidden" id="pageNo" name="pageNo" value="'.$this->pageNo.'"/>
						   <input type="hidden" id="recordPerPage" name="recordPerPage" value="'.$this->record_per_page.'"/>';
		return $hidden_value; 
	}
	
	function genRecordPerPageInput()
	{
		$html = '<div class="table_bottom_left_tool">
			<div class="item_per_page">
			<span>Photos per page: </span>
			<select name="num_per_page" onChange="ajax_library_changeRecordPerPage('.$this->form_name.',this.options[this.selectedIndex].value);">
			<option value=10 '.($this->record_per_page==10? "SELECTED":"").'>10</option>
			<option value=20 '.($this->record_per_page==20? "SELECTED":"").'>20</option>
			<option value=30 '.($this->record_per_page==30? "SELECTED":"").'>30</option>
			<option value=40 '.($this->record_per_page==40? "SELECTED":"").'>40</option>
			<option value=50 '.($this->record_per_page==50? "SELECTED":"").'>50</option>
			<option value=60 '.($this->record_per_page==60? "SELECTED":"").'>60</option>
			<option value=70 '.($this->record_per_page==70? "SELECTED":"").'>70</option>
			<option value=80 '.($this->record_per_page==80? "SELECTED":"").'>80</option>
			<option value=90 '.($this->record_per_page==90? "SELECTED":"").'>90</option>
			<option value=100 '.($this->record_per_page==100? "SELECTED":"").'>100</option>
			</select>
			</div>
            </div>';

		return $html;
	}
	
	function genLibrary()
	{
		global $cfg;
		include_once('libfilesystem.php');
	
		$lf = new libfilesystem();
		
		$read_all = false;
		$image_to_view = '';
		
		$hidden_value		= $this->genPaginationHiddenField();
		
		# if an image just got updated, show that image
		if($_POST['identifier'])
		{
			$this->identifier = $_POST['identifier'];
			$image_to_view = $this->identifier;	
			$this->getImageInfo();
		}
	
		$image_arr = $this->getFolderImage('',$image_to_view);
		
		if(sizeof($image_arr)==0)
		{
			$image_html.='<tr><td colspan="5" style="text-align:center" align="center">'.$this->no_file_msg.'</td></tr>';
		}
		else
		{
			$record_per_page_input 	= $this->genRecordPerPageInput();
			$go_page 				= $this->genGoPage();
			
			for($a=0;$a<sizeof($image_arr);$a++)
			{
				# get file name
				$this->file_name = $lf->file_name($image_arr[$a]['path']);
				
				# get file extension
				$this->extension = $lf->getFileExtension($this->folder_location.$image_arr[$a]['path']);
							
				# set identifier
	            $this->identifier = $this->file_name.$this->extension;
	            
	            list($image_width,$image_height)	= getimagesize($this->folder_location.$image_arr[$a]['path']);
				
	            # resize image for display
				if($image_width>$cfg['library_image_width'])
					$resize = 'width="'.$cfg['library_image_width'].'"';
				else
					$resize = '';
	            
				
				# record count
				$record_count = $this->record_from+$a+1;

	            $radio_check = ($this->selected_image==$this->identifier)?"checked":"";
	            $image_html.='<tr>
				            	<td class="num_col"><input type="radio" name="library_image" id="library_image" '.$radio_check.' value="'.$this->identifier.'"/></td>
				                <td class="image_col"><a href="'.$cfg['frontend_folder_httppath'].$image_arr[$a]['http'].'" target="_blank"><img src="'.$cfg['frontend_folder_httppath'].$image_arr[$a]['http'].'" '.$resize.'/></a></td>
				                <td class="dynamic_col">'.$this->identifier.'</td>
				                <td class="num_col"><a href="javascript:deleteImage(\''.$this->identifier.'\')" class="del_btn" title="delete"></a></td>
				            </tr>';

	        }
		}			
		
		
		
		$html .= '<div class="table_top_left_tool"> 
	            
	    		</div>
	            
	            <br class="clear" />
	            <div id="conten_div">
	    		<table class="common_table">
	            <thead>
	            <tr>
	            	<th class="num_col">Add</th>
	                <th class="image_col">Image</th>
	            	<th class="dynamic_col">File Name</th>
	            	<th class="num_col">Action</th>
            	</tr>
	            </thead>
	            <tbody>            
	          '.$image_html.'
	            </tbody>
	            </table>
	            '.$record_per_page_input.'
				<div class="table_bottom_right_tool">
	            	'.$go_page.'
	            </div>
	            <br class="clear" />
	            </div>';
            	
		return $html.$hidden_value;
	}
	
	function deleteImage()
	{
		global $cfg;
		include_once('table.php');
			
		$li = new table();
		if(unlink($this->folder_location."/".$this->identifier))
			return $li->genNoticeBoxMsg($cfg['sys_msg']['record_deleted'],true);
		else	
			return $li->genNoticeBoxMsg($cfg['sys_msg']['fail_delete_record'],false);
		
	}
	
	function genGoPage()
	{	
		# control the page option
		$max_page_option = 1;
		$x ='<div class="page_area"><span class="pages">Page '.$this->pageNo.' of '.$this->max_page.'</span>';
		# first page button
		$x.= ($this->pageNo>1)?'<a href="javascript:ajax_library_gopage(1,document.'.$this->form_name.'); " title="First Page">&laquo;</a>':'';
		# previous page button
		$x.= ($this->pageNo>1)?'<a href="javascript:ajax_library_gopage('.($this->pageNo-1).',document.'.$this->form_name.'); " title="Previous Page">&lsaquo;</a>':'';
		
		if($this->pageNo-$max_page_option-1>0)
		$x.='<span class="dot">...</span>';
		
		for($a=0;$a<$this->max_page;$a++)
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
					$x.='<a href="javascript:void(0)" onClick="ajax_library_gopage('.$page.',document.'.$this->form_name.'); " title="Page '.$page.'">'.$page.'</a>';	
				}
				else if($page>$this->pageNo && $page<=$this->pageNo+$max_page_option)
				{
					$x.='<a href="javascript:void(0)" onClick="ajax_library_gopage('.$page.',document.'.$this->form_name.'); " title="Page '.$page.'">'.$page.'</a>';
				}
				
			}
		}
		if($this->max_page-$this->pageNo>$max_page_option)
		$x.='<span class="dot">...</span>';
		
		# next page button
		$x .= ($this->pageNo<$this->max_page)?'<a href="javascript:void(0)" onClick="ajax_library_gopage('.($this->pageNo+1).',document.'.$this->form_name.'); " title="Next Page">&rsaquo;</a>':'';
		# last page button
		$x .= ($this->pageNo<$this->max_page)?'<a href="javascript:void(0)" onClick="ajax_library_gopage('.($this->max_page).',document.'.$this->form_name.'); " title="Last Page">&raquo;</a>':'';
		
		$x.='</div>';
		return $x;		
			
	}
	
}