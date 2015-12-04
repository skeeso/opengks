<?php
session_start();
$path_root = "../";
include_once("config.php");
include_once($path_root."includes/php/db.php");
include_once($path_root."includes/php/table.php");


$li = new table();

# check permission
if($li->hasAdminRight())
	$mode_tab = $li->genModeTab('e');
else
	header("location:index.php");


	
if(trim($_GET['field'])!='' && trim($_GET['id'])!='')
{
	# get embed image
	$inserted_image = $li->getEmbededImage($_GET['field'],$_GET['id']);
	
	# put it to the http textbox if it's a link
	if($li->isLinkImage($inserted_image))
		$image_link = $inserted_image;
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
<form action="upload.php" method="post" name="form_upload" id="form_upload" enctype="multipart/form-data">
	
    <div class="thick_box_wrapper">
    
    <div class="thick_box_header">
    <!-- tabs --> 
    <ul class="admin_tab">

    <li class="current">Upload Image</li>
    <li><a href="library.php?field=<?=$_GET['field']?>&id=<?=$_GET['id']?>">Image Library</a></li>
    </ul>
    </div>


<table class="form_table">
        <tr>
        <th>Upload File</th>
		<td>
        <input type="file" id="image" name="image"/>
        <div class="loading_msg" id="loading_msg"></div>
        <input type="checkbox" id="need_resize" name="need_resize" />Resize<br />
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Max. Width <input name="resize_size_w" type="text" id="resize_size_w" value="" class="form_short"/>&nbsp;&nbsp;&nbsp;&nbsp;
        Max. Height <input name="resize_size_h" type="text" id="resize_size_h" value="" class="form_short"/><br />
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="system_msg_grey">Crop photo to exact dimensions proportionally (not greater than setting)</span>
        </td>
        </tr>
        <tr>
        <th class="upload_option">- or -</th>
		<td>&nbsp;</td>
        </tr>
        <tr>
	    <th>Image Link</th>
		<td>
        <input name="image_link" id="image_link" type="text" class="pic_name" value="<?=$image_link?>"/><br />
		<span class="system_msg_grey">Insert an image from another web site</span>
        </td>
        </tr>
        </table>
		 	<!-- action btn start --> 
            <div class="action_btn">
            <input class="action_btn submit" type="button" value="Save" onClick="uploadImage('<?=$_GET['field']?>','<?=$_GET['id']?>');"/>
            <input class="action_btn cancel" type="button" value="Cancel" onClick="parent.$.fn.colorbox.close();"/>
            </div>
            <!-- action btn end -->  

</div>
</form>
</body>
<?=$li->genFooter();?>