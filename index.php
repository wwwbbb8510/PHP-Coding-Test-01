<?php
	require_once('filebrowser.php');
	/*
	 * Add your filebrowser definition code here
	 */
        
        $rootPath = dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR;//set the root directory
        $exensionFilter = array('php');//set the filter
        $currentPath = isset($_REQUEST['current_path']) ? $_REQUEST['current_path'] : NULL;//get the current path from HTTP REQUEST
        $arrFiles = FileBrowser::Factory($rootPath, $currentPath, $exensionFilter)->Get();
        
?>
<!doctype html>
<html lang="en">
 <head>
  <title>File browser</title>
 </head>
 <body>
     <div>
        <ul>
            <?php 
               //display the parnet dir
               if(isset($arrFiles['parent_dir']) && 
                       !empty($arrFiles['parent_dir'])){
            ?>
            <li><a href="?current_path=<?php echo $arrFiles['parent_dir']; ?>">Parent</a></li>
            <?php
               }
            ?>
            <?php 
               //display the dirs
               if(isset($arrFiles['dirs']) && 
                       is_array($arrFiles['dirs'])){
                   foreach($arrFiles['dirs'] as $dir){

            ?>
            <li><a href="?current_path=<?php echo $dir; ?>"><?php echo $dir; ?></a></li>
            <?php
                   }
               }
            ?>
            <?php 
               //display the files
               if(isset($arrFiles['files']) && 
                       is_array($arrFiles['files'])){
                   foreach($arrFiles['files'] as $dir){

            ?>
            <li><?php echo $dir; ?></li>
            <?php
                   }
               }
            ?>
        </ul>
     </div>
 </body>
</html>