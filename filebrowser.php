<?php
	require_once('interface.php');

	class FileBrowser implements __FileBrowser {
            /**
             * store the singleton objects
             * @var array 
             */
            protected static $singletonObjects = array();
            
            /**
             * file root path 
             * @var string 
             */
            private $rootPath;
            /**
             * the current file path
             * @var string 
             */
            private $currentPath;
            /**
             * the extension filters 
             * @var array 
             */
            private $extensionFilter;
            /**
             * protected constructor limited to call only by factory method
             * @param string $rootPath
             * @param string $currentPath
             * @param array $extensionFilter
             */
            public function __construct($rootPath, $currentPath = null, array $extensionFilter = array()) {
                $this->rootPath = $rootPath;
                $this->currentPath = $currentPath != NULL && $currentPath != DIRECTORY_SEPARATOR ? $currentPath : NULL;
                $this->extensionFilter = $extensionFilter;
            }
            /**
             * get a singleton object of the FileBrowser class
             * @param string $rootPath
             * @param string $currentPath
             * @param array $extensionFilter
             * @return FileBrowser
             */
            public static function Factory($rootPath, $currentPath = null, array $extensionFilter = array()){
                $objKey = self::GenerateHashKey($rootPath, $currentPath, $extensionFilter);
                if(!isset(self::$singletonObjects[$objKey]) || 
                        is_a(self::$singletonObjects[$objKey], 'FileBrowser')){
                    self::$singletonObjects[$objKey] = new FileBrowser($rootPath, $currentPath, $extensionFilter);
                }
                return self::$singletonObjects[$objKey];
            }
            
            /**
            * Set private root path
            */
            public function SetRootPath($rootPath){
                $this->rootPath = $rootPath;
                return $this;
            }
            
            /**
            * Set private current path
            */
            public function SetCurrentPath($currentPath){
                $this->currentPath = $currentPath;
                return $this;
            }
            
            /**
            * Set private extension filter
            */
            public function SetExtensionFilter(array $extensionFilter){
                $this->extensionFilter = $extensionFilter;
                return $this;
            }
            
            /**
            * Get files using currently-defined object properties
            * @return array Array of files within the current directory
            */
            public function Get(){
                $currentAbsPath = $this->rootPath;
                $currentAbsPath .= empty($this->currentPath) ? '' : $this->currentPath;
                return $this->GetFilteredFileList($currentAbsPath);
            }
            
            /**
             * get All the files and filter the files
             * @param type $currentAbsPath
             * @return string
             */
            protected function GetFilteredFileList($currentAbsPath){
                $arrFiles = scandir($currentAbsPath);
                if($arrFiles !== FALSE && !empty($arrFiles)){
                    $arrReturnFileList = array(
                        'parent_dir' => '',
                        'files' => array(),
                        'dirs' => array(),
                    );
                    //remove the hidden files . and .. and filter the list
                    foreach ($arrFiles as $fileName){
                        if($fileName != '.' && $fileName != '..'){
                            if(is_dir($this->rootPath . $this->currentPath . $fileName)){
                                $arrReturnFileList['dirs'][] = $this->currentPath . $fileName . DIRECTORY_SEPARATOR ;
                            }elseif(is_file($this->rootPath . $this->currentPath . $fileName)){
                                //filter the extension
                                if(is_array($this->extensionFilter) && 
                                        !empty($this->extensionFilter)){
                                    $extStr = pathinfo($fileName, PATHINFO_EXTENSION);
                                    if(in_array($extStr, $this->extensionFilter)){
                                        $arrReturnFileList['files'][] = $this->currentPath . $fileName;
                                    }
                                }else{
                                    $arrReturnFileList['files'][] = $this->currentPath . $fileName;
                                }
                            }
                        }
                    }
                    //get the parent path
                    if(!empty($this->currentPath)){
                        $parentPath = dirname($this->rootPath . $this->currentPath) . DIRECTORY_SEPARATOR;
                        $arrReturnFileList['parent_dir'] = str_replace($this->rootPath, '', $parentPath) . DIRECTORY_SEPARATOR;
                    }
                }
                return $arrReturnFileList;
            }

            /**
             * generate the hash key for the singleton objects
             * return string $hashStr
             */
            private static function GenerateHashKey($rootPath, $currentPath, $extensionFilter){
                $hashStr = $rootPath;
                $hashStr .= empty($currentPath) ? '' : $currentPath;
                $hashStr .= is_array($currentPath) && !empty($extensionFilter) ? implode('_', $extensionFilter) : '';
                $hashStr = md5($hashStr);
                return $hashStr;
            }
	}