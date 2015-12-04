<?php

# to support chinese naming
setlocale(LC_ALL, 'zh_CN.UTF8');
class libfilesystem {

        var $rs = array();

        function libfilesystem(){
        }

        ######################################################################

        
        function file_name($file){
                $file = basename($file);
                return substr($file, 0, strpos($file,"."));
        }
       
        
        function folderlist($location){
                # prevent error if folder does not exist
                clearstatcache();
                if (!file_exists($location))
                {
                        return false;
                }

                $d = dir($location);

                while($entry=$d->read()) {
                        $filepath = $location . "/" . $entry;
                        
                        if (is_dir($filepath) && $entry<>"." && $entry<>".."){
                        	
                            	$this->return_folderlist($filepath);
                                $this->rs[sizeof($this->rs)] = $filepath;
                        }
                        else if (is_file($filepath))
                                $this->rs[sizeof($this->rs)] = $filepath;

                }
                $d->close();
        }

        function return_folderlist($location)
        {
            $this->folderlist($location);

        	return $this->rs;
        }

        function return_folder($location)
		{
		
			$this->rs = array();              # clear rs
			
			$folders = array();
			$j = 0;
			$row = $this->return_folderlist($location);
			
			
			for($i=0; $i<sizeof($row); $i++){
			if(is_dir($row[$i])) $folders[$j++] = $row[$i];
			}
			
			
			return $folders;
		}


        function folder_size($location){
                $file_size = 0;
                $file_no = 0;
                $dir_no = 0;
                if(is_dir($location)){
                        $row1 = $this->return_folderlist($location);
                        for($i=0; $i<sizeof($row1); $i++){
                                if(is_file($row1[$i])){

                                        $file_size += filesize($row1[$i]); $file_no++;
                                }else{
                                        $dir_no++;
                                }
                        }
                }else{
                        $file_size += filesize($location); $file_no++;
                }
                $size = array($file_size, $file_no, $dir_no);
                return $size;
        }



        function getFileExtension($dest){
		if (is_file($dest))
		{
			return substr(basename($dest), (strpos(basename($dest), ".")));
		} else
		{
			return "";
		}
	}

}
?>
