<?php
session_start();
$path_root = "../";
include_once("config.php");
include_once($path_root."includes/php/db.php");
include_once($path_root."includes/php/table.php");
include_once($path_root."includes/php/library.php");

$li = new table();

# check permission
if($li->hasAdminRight())
	$mode_tab = $li->genModeTab('e');
else
	header("location:index.php");

if(trim($_GET['field'])!='' && trim($_GET['id'])!='')
{
	# get embed image and put in hidden field
	$inserted_image = $li->getEmbededImage($_GET['field'],$_GET['id']);
}


# login bar
$login_bar = $li->genLoginBar();

# generate insert record js function
$insert_record_js = $li->genInsertRecordJS();

# get js variable
$js_var = $li->genJSVariable();


$is_thickbox = true;
?>

<?=$li->genHeader($is_thickbox,$cfg['theme_color']);?>
<?=$js_var?>
<script type="text/javascript"> 
$(document).ready(function(){ 
    $(document).pngFix();
    loadLibraryData();
}); 

function changeCss(file)
{
	document.getElementById('color_css').href = "includes/css/"+file;
	return false;	
}    

<?=$insert_record_js?>
</script> 
</head>
<body>
<form action="library.php" method="post" name="form1" id="form1" onSubmit="return false">
	<div class="thick_box_wrapper">
	<div class="thick_box_header">
	<!-- tabs --> 
    <ul class="admin_tab">
    <li><a href="upload.php?field=<?=$_GET['field']?>&id=<?=$_GET['id']?>">Upload Image</a></li>
    <li class="current">Image Library</li>
    </ul>
    </div>
<!-- ############# content start ############# -->
        <div class="content">
	        <div class="table_top_left_tool">            
			Choose from you library
            </div>
	        <div class="table_right_tool">
			<div class="searchbox"><!-- searchbox start-->
			<input type="text" class="textfield" size="24" id="keyword" name="keyword" value="" onKeyUp="searchImage(event)"/>
			<input type="button" class="button" value="" />
			</div><!-- searchbox end --> 
            </div>
	        <br class="clear" />
            <div id="content_div">
    		<?=$display?>	
            <br class="clear" />
            </div>
        </div>
        <!-- ############# content end ############# -->
         	<!-- action btn start --> 
            <div class="action_btn">
	        <input class="action_btn submit" type="button" value="Save" onClick="attachImage('<?=$_GET['field']?>','<?=$_GET['id']?>');"/>
            <input class="action_btn cancel" type="button" value="Cancel" onClick="parent.$.fn.colorbox.close();"/>
            </div>
            <!-- action btn end -->  
</div>
<input type="hidden" name="selected_image" id="selected_image" value="<?=$inserted_image?>"/>
<input type="hidden" name="field" id="field" value="<?=$_GET['field']?>"/>
<input type="hidden" name="id" id="id" value="<?=$_GET['id']?>"/>
</form>
</body>
<?=$li->genFooter();?>