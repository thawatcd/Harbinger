<?php require_once("../include/init.php");
	switch ($_GET['method']) {
	    case "addService":
			// select a database
			$db = $mongo->logsearch;
			// select a collection (analogous to a relational database's table)
			$collection = $db->service;
			//check dupplicate
			$target = array( "service" => $_GET['service']);
			$cursor = $collection->findOne($target);
			if(empty($cursor)){
				// add a record
				$document = array( "service" => $_GET['service'],
								   "description" => $_GET['description'] );
				$collection->insert($document);
				echo "success";
			} else {
				echo "Service '".$_GET['service']."' already exist.";
			}
	        break;

	    case "editService":
			$db = $mongo->logsearch;
			$collection = $db->service;
			//check dupplicate
			$target = array( "service" => $_GET['service']);
			$cursor = $collection->findOne($target);
			if(empty($cursor) || $_GET['service'] == $_GET['oldService']){
				$target = array("service" => $_GET['oldService']);
				$multi = array('multiple' => true);
				$newData = array('$set' => array( "service" => $_GET['service'], 
												  "description" => $_GET['description'] ));
				$collection->update($target, $newData);
				//edit documents in relate collection 
				$newData = array('$set' => array( "service" => $_GET['service'] ));
				$collection = $db->service_config;
				$collection->update($target, $newData, $multi);
				$collection = $db->log_index;
				$collection->update($target, $newData, $multi);
				echo "success";
			} else {
				echo "Service '".$_GET['service']."' already exist.";
			}
	        break;

	    case "deleteService":
			$db = $mongo->logsearch;
			$collection = $db->service;
			$target = array( "service" => $_GET['service']);
			$collection->remove($target);
			//delete documents in relate collection
			$collection = $db->service_config;
			$collection->remove($target);
			echo "success";
			createCrontab();
	        break;

	    case "addServiceConfig":
			// select a database
			$db = $mongo->logsearch;
			// select a collection (analogous to a relational database's table)
			$collection = $db->service_config;
			//check dupplicate
			$target = array( "service" => $_GET['service'], 
							 "system" => $_GET['system'], 
							 "node" => $_GET['node'] , 
							 "process" => $_GET['process']);
			$cursor = $collection->findOne($target);
			if(empty($cursor)){
				// add a record
				if(!isset($_GET['logType']))
					$_GET['logType'] = '';
				if(!isset($_GET['dateHolder']))
					$_GET['dateHolder'] = '';
				$document = array( "service" => $_GET['service'], 
								   "system" => $_GET['system'], 
								   "node" => $_GET['node'], 
								   "process" => $_GET['process'],
								   "path" => $_GET['path'],
								   "description" => $_GET['description'],
								   "logType" => $_GET['logType'],
								   "logStartTag" => $_GET['logStartTag'],
								   "logEndTag" => $_GET['logEndTag'],
								   "msisdnRegex" => $_GET['msisdnRegex'],
								   "dateHolder" => $_GET['dateHolder'],
								   "dateRegex" => $_GET['dateRegex'],
								   "dateFormat" => $_GET['dateFormat'],
								   "timeRegex" => $_GET['timeRegex'],
								   "timeFormat" => $_GET['timeFormat'],
								   "mmin" => $_GET['mmin'],
								   "interval" => $_GET['interval'],
								   "crontab" => $_GET['crontab'],
								   "state" => $_GET['state'] );
				$collection->insert($document);
				echo "success";
				createCrontab();
			} else {
				echo "Node '".$_GET['node']."' or process '".$_GET['process']."' and its path are already exist.";
			}
	        break;

	    case "editServiceConfig":
			$db = $mongo->logsearch;
			$collection = $db->service_config;
			//check dupplicate
			$target = array( "service" => $_GET['service'], 
							 "system" => $_GET['system'], 
							 "node" => $_GET['node'] , 
							 "process" => $_GET['process']);
			$cursor = $collection->findOne($target);
			if(empty($cursor) || ($_GET['node'] == $_GET['oldNode'] 
				&& $_GET['system'] == $_GET['oldSystem'] && $_GET['process'] == $_GET['oldProcess'])){
				$target = array( "service" => $_GET['service'], 
								 "system" => $_GET['oldSystem'], 
								 "node" => $_GET['oldNode'] , 
							 	 "process" => $_GET['oldProcess']);
				if(!isset($_GET['logType']))
					$_GET['logType'] = '';
				if(!isset($_GET['dateHolder']))
					$_GET['dateHolder'] = '';
				$newData = array('$set' => array("system" => $_GET['system'], 
												 "node" => $_GET['node'],
												 "process" => $_GET['process'],
												 "path" => $_GET['path'],
												 "description" => $_GET['description'],
											   	 "logType" => $_GET['logType'],
											   	 "logStartTag" => $_GET['logStartTag'],
											   	 "logEndTag" => $_GET['logEndTag'],
											   	 "msisdnRegex" => $_GET['msisdnRegex'],
											   	 "dateHolder" => $_GET['dateHolder'],
											   	 "dateRegex" => $_GET['dateRegex'],
											   	 "dateFormat" => $_GET['dateFormat'],
											   	 "timeRegex" => $_GET['timeRegex'],
											   	 "timeFormat" => $_GET['timeFormat'],
											   	 "mmin" => $_GET['mmin'], 
												 "interval" => $_GET['interval'],
								   				 "crontab" => $_GET['crontab'],
								   				 "state" => $_GET['state'] ));
				$collection->update($target, $newData);
				$multi = array('multiple' => true);
				$newData = array('$set' => array("system" => $_GET['system'], 
												 "node" => $_GET['node'],
												 "process" => $_GET['process'] ));
				$collection = $db->log_index;
				$collection->update($target, $newData, $multi);
				echo "success";
				createCrontab();
			} else {
				echo "Node '".$_GET['node']."' or process '".$_GET['process']."' and its path are already exist.";
			}
	        break;

	    case "deleteServiceConfig":
			$db = $mongo->logsearch;
			$collection = $db->service_config;
			$target = array( "service" => $_GET['service'], 
							 "system" => $_GET['system'], 
							 "node" => $_GET['node'], 
							 "process" => $_GET['process']);
			$collection->remove($target);
			echo "success";
			createCrontab();
	        break;

	    case "indexTest":
	    	$cmd = "sudo -u logsearch python indexScript.py ".$_GET['mode'].' '
										.$_GET['path'].' '
										.$_GET['logType'].' '
										.$_GET['logStartTag'].' '
										.$_GET['logEndTag'].' '
										.$_GET['msisdnRegex'].' '
										.$_GET['dateHolder'].' '
										.$_GET['dateRegex'].' '
										.$_GET['dateFormat'].' '
										.$_GET['timeRegex'].' '
										.$_GET['timeFormat'];
			/*echo $_GET['mode']."<br/>";
		    echo $_GET['path']."<br/>";
		    echo $_GET['logType']."<br/>";
		    echo $_GET['logStartTag']."<br/>";
		    echo $_GET['logEndTag']."<br/>";
		    echo $_GET['msisdnRegex']."<br/>";
		    echo $_GET['msisdnFormatRegex']."<br/>";
		    echo $_GET['dateHolder']."<br/>";
		    echo $_GET['dateRegex']."<br/>";
		    echo $_GET['dateFormatRegex']."<br/>";
		    echo $_GET['dateFormat']."<br/>";
		    echo $_GET['timeRegex']."<br/>";
		    echo $_GET['timeFormatRegex']."<br/>";
		    echo $_GET['timeFormat']."<br/>";*/
			//echo $cmd;
			//$output = shell_exec('sudo -u logsearch whoami'.' 2>&1');
			$output = shell_exec($cmd.' 2>&1');
			echo "<pre>$output</pre>";
	        break;
	}
?>