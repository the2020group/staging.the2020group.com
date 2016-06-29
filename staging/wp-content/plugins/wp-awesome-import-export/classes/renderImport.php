<?php

if (!class_exists('WPAIEImport')) {
    class WPAIEImport
    {
        public function renderImport()
        {
            $ACS = new ACS();
            
            $col = array();
            
            $postData = array();
            
            parse_str($_POST['importData'], $postData);
           
            $uploadFilePath = $postData['uploadFilePath'];
            
            $importFileType = $ACS->getFileExtension($uploadFilePath);
            
            if ($importFileType == "xls" || $importFileType == "xlsx")
                $importFileType = "excel";
            
            $dbColumn = $postData['dbColumn'];
            
            for ($columnCount = 0; $columnCount < $dbColumn; $columnCount++) {
                if ($postData['dbColumn' . $columnCount] === "new_meta")
                    $col[] = "PM: " . $postData['tbColumn' . $columnCount];
                else
                    $col[] = $postData['dbColumn' . $columnCount];
            }
            
            $ACS->columns = $col;
            
            $ACS->isFirstRowHeader = true;
            
            $ACS->operationType = "IMPORT";
            
            $ACS->operationCategory = $postData["operationCategory"];
            
            if (isset($postData["taxonomyType"]))
                $ACS->taxonomy = $postData["taxonomyType"];
            
            if (isset($postData["postType"]))
                $ACS->postType = $postData["postType"];
            
            if (isset($postData["wpTable"]))
                $ACS->dbTableName = $postData["wpTable"];
            
            if (isset($postData["pluginName"]))
                $ACS->pluginName = $postData["pluginName"];
            
            $ACS->convert($importFileType, "db", $uploadFilePath);
            
           return $ACS->output;
            
        }
    }
}
?>