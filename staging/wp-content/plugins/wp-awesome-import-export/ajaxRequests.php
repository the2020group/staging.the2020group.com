<?php
function wpaie_ajax_action_callback()
{
	$action = $_POST['operation'];
	$ACS = new ACS();
	
	switch($action)
	{
		
		case "wpTables":$cols = $ACS->getColumnName($_POST["tableName"]); 
						foreach($cols as $col)
						{
						   echo "<option selected='selected' value='".$col["Field"]."'>".$col["Field"]."</option>";
						}
						break;
						
		case "import": $WPAIEImport = new WPAIEImport();
					   $output = $WPAIEImport->renderImport();
					   echo json_encode($output);
					   break;
					  
		case "export": $WPAIEExport = new WPAIEExport();
					   $output = $WPAIEExport->renderExport();
					   echo json_encode($output);
					   break;
	}
	die();
}
?>