<?php
session_start();
$path_root = "../";
include_once("config.php");
include_once($path_root."includes/php/db.php");
include_once($path_root."includes/php/table.php");

$li = new table();

if($li->hasAdminRight())
	$mode_tab = $li->genModeTab('e');
else
	header("location:index.php");

# login bar
$login_bar = $li->genLoginBar();

# generate insert record js function
$insert_record_js = $li->genInsertRecordJS($path_root);

# get js variable
$js_var = $li->genJSVariable();

# get editable js function
$editable_function_js = $li->genEditableFunction($path_root);

#$module_menu = $li->genModuleMenu();
?>

<?=$li->genHeader('',$cfg['theme_color']);?>
<?=$js_var?>
<script type="text/javascript"> 
$(document).ready(function(){ 
    $(document).pngFix();
    g_current_mode = 'e'; 
    loadData();

}); 
<?=$insert_record_js?>
<?=$editable_function_js?>
</script> 
</head>
<body>
<form action="edit.php" method="post" name="form1" id="form1" onSubmit="return false">
<!-- ############# header start ############# -->
<?=$login_bar?>
<!-- ############# header end ############# -->


<!-- ############# style menu ############# -->

<!-- ############# wrapper start ############# -->
<div class="wrapper">
<!-- ############# Photo Manage ########### admin_box start ############# -->
<div class="admin_box" >
    <div class="admin_box_header"><h3><?=$cfg['module_name']?></h3><?=$mode_tab?></div>
        <!-- ############# content start ############# -->
        <div class="content">
        	        <div class="notice_box" id="notice_box">
        <!--<div class="success">5 record(s) deleted</div>-->
        <!--<div class="alert">error</div>-->    
        </div>
            
        	<div class="table_top_left_tool">            
			<!--<a href="#" class="addnew">Add Category</a>-->
            Selected ( <span id="selected_cell_span">0</span> )<input class="action_btn small submit" type="button" value="Delete" onClick="deleteRecord();"/>
           
            &nbsp;&nbsp; | &nbsp;&nbsp; 
            
            
            <select id="row_num" name="row_num">
			<option>- Add Row --</option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
			<option value="6">6</option>
            <option value="7">7</option>
            <option value="8">8</option>
            <option value="9">9</option>
            <option value="10">10</option>
            </select>

            
            <input class="action_btn small submit" type="button" value="Add" onClick="addRow($('#record_table'));"/>
            </div>
            
            
            <div class="table_right_tool">
           <?=$li->genSearchFilter();?>
            
			<div class="searchbox"><!-- searchbox start-->
			<input type="text" class="textfield" size="24" name="search_str" id="search_str" onKeyDown="searchEnterEvent(event)"/>
			<input type="button" class="button" value="" onClick="loadData();"/>
			</div><!-- searchbox end --> 
            </div>

            
            <br class="clear" />

            
            <br class="clear" />
            <div id="content_div">
			</div>
            <br class="clear" />
            </div>
        </div>
        <!-- ############# content end ############# --> 
</div>
<!-- ############# Photo Manage ########### admin_box end ############# -->
</div>
<!-- ############# wrapper end ############# -->
<input type="hidden" id="new_row_ct" name="new_row_ct" value="0"/>
</form>

</body>
<?=$li->genFooter();?>
