<?php
	$root_url = 'http://'.$_SERVER['HTTP_HOST'].'/logsearch';
	$python_script_path = '/opt/lampp/htdocs/logsearch/function/';
	$mongo = new MongoClient("mongodb://127.0.0.1:2884");
	//$mongo = new MongoClient();
	
	function createCrontab()
	{
		$crontabFilePath = '/tmp/crontab.txt';
		$file = fopen($crontabFilePath, "w") or die("Unable to open file!");
		global $mongo,$python_script_path;
		$db = $mongo->logsearch;
		$collection = $db->service_config;
		$cursor = $collection->find();
		foreach ($cursor as $doc) {
			if( $doc['state'] == 'Running' && $doc['path'] != '' && $doc['crontab'] != ''){
				$txt = '# Index, service:"'.$doc['service'].'" system:"'.$doc['system']
						.'" node:"'.$doc['node'].'" process:"'.$doc['process'].'" path:"'.$doc['path'].'"'.PHP_EOL;
				fwrite($file, $txt); 
				$txt = $doc['crontab'].' sudo -u logsearch python '
							.$python_script_path.'indexScript.py index '.$doc['_id'].PHP_EOL;
				fwrite($file, $txt); 
			}
		}
		//purge data here
		$keepDataMonth = 3;
		$txt = '# Purge data at 04:00 everyday, keep data '.$keepDataMonth.' months'.PHP_EOL;
		fwrite($file, $txt);
		$txt = '0 4 * * *'.' sudo -u logsearch python '.$python_script_path.'purgeData.py '.$keepDataMonth.' '.PHP_EOL;
		fwrite($file, $txt); 
		fclose($file);
		$cmd = "sudo -u logsearch crontab ".$crontabFilePath;
		exec($cmd);
	} 
?>