<?php

if (!class_exists('WPAIEExport')) {

    class WPAIEExport {

        public function renderExport() {
            $ACS = new ACS();

            $ACS->operationType = "EXPORT";

            $postData = array();

            parse_str($_POST['exportData'], $postData);

            $ACS->operationCategory = $postData["operationCategory"];

            $ACS->exportFileType = $postData["optionFileType"];
            
            $ACS->orderBy = $postData["orderBy"];

            $ACS->orderAscDesc = $postData["orderAscDesc"];

            if (strtoupper($postData["operationCategory"]) == "CATEGORY" || strtoupper($postData["operationCategory"]) == "TAXONOMY") {
                $ACS->customTaxonomies = $postData["taxonomyType"];
                $ACS->hideEmpty = $postData["hideEmpty"];
            } else if (strtoupper($postData["operationCategory"]) == "COMMENT") {
                $ACS->columns = $postData["commentFields"];
                $ACS->postStatus = $postData["postStatus"];
                $ACS->commentStatus = $postData["commentStatus"];
                $ACS->postAuthor = $postData["postAuthor"];
                $ACS->byPostId = $postData["byPostId"];
            } else if (strtoupper($postData["operationCategory"]) == "USER") {
                $ACS->columns = $postData["userFields"];
                $ACS->userMeta = $postData["userMeta"];
                $ACS->userRole = $postData["userRole"];
            } else if (strtoupper($postData["operationCategory"]) == "WPTABLE") {
                $ACS->columns = $postData["wpTableColumns"];
                $ACS->dbTableName = $postData["wpTables"];
            } else if (strtoupper($postData["operationCategory"]) == "SQL") {
                $ACS->sql = $postData["sql"];
            } else {
                $ACS->columns = $postData["postColumns"];
                $ACS->exportFeaturedImage = $postData["exportFeaturedImg"];
                $ACS->postMeta = $postData["postMeta"];
                $ACS->customTaxonomies = $postData["postCustomFields"];
                $ACS->postStatus = $postData["postStatus"];
                $ACS->postType = $postData["postType"];
                $ACS->optionNoOfPost = $postData["optionNoOfPost"];
                if (isset($postData["postStartRange"]))
                    $ACS->postStartRange = $postData["postStartRange"];
                if (isset($postData["postTotalCount"]))
                    $ACS->postTotalCount = $postData["postTotalCount"];
            }
            $ACS->convert($ACS->exportFileType, "db");
            return $ACS->output;
        }
    }
}