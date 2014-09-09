<?php

	//$msisdn = $_GET['msisdn'];
	$filename = $_GET['path'];
	$line = $_GET['startTag'];
	$endLine = $_GET['endTag'];

	if (strpos($filename,'.gz') !== false) 
		if($_GET['showLineNumber'] == 1)
			$linecmd = "zcat  $filename | cat -n | sed -n $line,{$endLine}p";
		else
			$linecmd = "zcat $filename | sed -n $line,{$endLine}p";
	else 
		if($_GET['showLineNumber'] == 1)
			$linecmd = "cat -n $filename | sed -n $line,{$endLine}p";
		else
			$linecmd = "cat $filename | sed -n $line,{$endLine}p";
	
	//echo $linecmd;
	$output = shell_exec('sudo -u logsearch '.$linecmd.' 2>&1');
	//echo "$linecmd<br/>";
	//echo str_replace($msisdn,"<mark>$msisdn</mark>",htmlspecialchars($output));
	echo htmlspecialchars($output, ENT_SUBSTITUTE, 'UTF-8');
?>