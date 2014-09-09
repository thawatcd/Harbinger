<?php require_once("../include/init.php");
	switch ($_GET['method']) {
	    case "addSystem":
			// select a database
			$db = $mongo->logsearch;
			// select a collection (analogous to a relational database's table)
			$collection = $db->system;
			//check dupplicate
			$target = array( "system" => $_GET['system']);
			$cursor = $collection->findOne($target);
			if(empty($cursor)){
				// add a record
				$document = array( "system" => $_GET['system'], "description" => $_GET['description'] );
				$collection->insert($document);
				echo "success";
			} else {
				echo "System '".$_GET['system']."' already exist.";
			}
	        break;

	    case "editSystem":
			$db = $mongo->logsearch;
			$collection = $db->system;
			//check dupplicate
			$target = array( "system" => $_GET['system']);
			$cursor = $collection->findOne($target);
			if(empty($cursor) || $_GET['system'] == $_GET['oldSystem']){
				$target = array("system" => $_GET['oldSystem']);
				$multi = array('multiple' => true);
				$newData = array('$set' => array( "system" => $_GET['system'], "description" => $_GET['description'] ));
				$collection->update($target, $newData);
				//edit documents in relate collection 
				$newData = array('$set' => array( "system" => $_GET['system'] ));
				$collection = $db->system_config;
				$collection->update($target, $newData, $multi);
				$collection = $db->service_config;
				$collection->update($target, $newData, $multi);
				$collection = $db->log_index;
				$collection->update($target, $newData, $multi);
				echo "success";
			} else {
				echo "System '".$_GET['system']."' already exist.";
			}
	        break;

	    case "deleteSystem":
			$db = $mongo->logsearch;
			$collection = $db->system;
			$target = array( "system" => $_GET['system']);
			$collection->remove($target);
			//delete documents in relate collection 
			$collection = $db->system_config;
			$collection->remove($target);
			$collection = $db->service_config;
			$collection->remove($target);
			echo "success";
	        break;

	    case "addSystemConfig":
			// select a database
			$db = $mongo->logsearch;
			// select a collection (analogous to a relational database's table)
			$collection = $db->system_config;
			//check dupplicate
			$target = array( "system" => $_GET['system'], "node" => $_GET['node']);
			$cursor = $collection->findOne($target);
			if(empty($cursor)){
				// add a record
				$document = array( "system" => $_GET['system'], "node" => $_GET['node'], "description" => $_GET['description'] );
				$collection->insert($document);
				echo "success";
			} else {
				echo "Node '".$_GET['node']."' already exist.";
			}
	        break;

	    case "editSystemConfig":
			$db = $mongo->logsearch;
			$collection = $db->system_config;
			//check dupplicate
			$target = array( "system" => $_GET['system'], "node" => $_GET['node']);
			$cursor = $collection->findOne($target);
			if(empty($cursor) || $_GET['node'] == $_GET['oldNode']){
				$target = array("system" => $_GET['system'], "node" => $_GET['oldNode']);
				$multi = array('multiple' => true);
				$newData = array('$set' => array( "node" => $_GET['node'], "description" => $_GET['description'] ));
				$collection->update($target, $newData);
				//edit documents in relate collection 
				$newData = array('$set' => array( "node" => $_GET['node'] ));
				$collection = $db->service_config;
				$collection->update($target, $newData, $multi);
				$collection = $db->log_index;
				$collection->update($target, $newData, $multi);
				echo "success";
			} else {
				echo "Node '".$_GET['node']."' already exist.";
			}
	        break;

	    case "deleteSystemConfig":
			$db = $mongo->logsearch;
			$collection = $db->system_config;
			$target = array( "system" => $_GET['system'], "node" => $_GET['node']);
			$collection->remove($target);
			//delete documents in relate collection
			$collection = $db->service_config;
			$collection->remove($target);
			echo "success";
	        break;
	}
?>