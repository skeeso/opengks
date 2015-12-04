<?php
session_start();
$path_root = "../";
include_once("config.php");
include_once($path_root."includes/php/db.php");
include_once($path_root."includes/php/table.php");

$li = new table();

# verify with preset info
if(isset($_POST['username']) || $_POST['password'])
{
	
	if($li->auth(trim($_POST['username']),trim($_POST['password'])))
	{
		# redirect to edit page
		header("location:edit.php");
	}
	else
	{
		# display message
		$notice_msg = '<div class="notice_box" id="notice_box">
        				<div class="alert">Invalid login name or password</div>
        				</div>';		
	}
}

if($li->hasAdminRight())
$mode_tab = $li->genModeTab('v');

# login bar
$login_bar = $li->genLoginBar();

# get js variable
$js_var = $li->genJSVariable();

#$module_menu = $li->genModuleMenu();
?>

<?=$li->genHeader('',$cfg['theme_color']);?>
<?=$js_var?>
<script type="text/javascript"> 
$(document).ready(function(){
	  
    $(document).pngFix();
    g_current_mode = 'v'; 
    loadData();
}); 
    
</script> 
</head>
<body>
<form action="index.php" method="post" name="form1" id="form1" onSubmit="return false">
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
        	<div class="table_right_tool">
            <?=$li->genSearchFilter();?>
            
			<div class="searchbox"><!-- searchbox start-->
			<input type="text" class="textfield" size="24" name="search_str" id="search_str" onKeyDown="searchEnterEvent(event)"/>
			<input type="button" class="button" value="" onClick="loadData();"/>
			</div><!-- searchbox end --> 
            </div>
            
            <br class="clear" />
            <div id="content_div">
			
            </div>
        </div>
        <!-- ############# content end ############# -->
</div>
<!-- ############# Photo Manage ########### admin_box end ############# -->
</div>
<!-- ############# wrapper end ############# -->
</form>
</body>
<?=$li->genFooter();?>
