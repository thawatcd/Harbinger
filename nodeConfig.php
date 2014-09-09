<?php require_once("include/init.php"); ?>
<!DOCTYPE html>
<html>
	<head>
		<?php require_once("include/head.html"); ?>
	</head>
	<body>
		<?php require_once("include/nav.html"); ?>
		<div id="page-wrapper" class="container">
			<h2 class="page-header">Configure Service '<span id="service"><? echo $_GET['service']; ?></span>'</h2>						
			<form class="col-sm-offset-3 col-sm-6">
				<h3 class="page-header" id="nodeConfigure">Node Configure</h3>
				<div class="panel-body">
		    		<div class="form-group">
						<label for="system">Select System :</label>
						<span id="oldSystem" style="color:#909090;"><? if(isset($_GET['system'])) echo $_GET['system']; ?></span>
						<div>
							<select class="form-control" name='system' id='system'>
								<option value='' disabled selected>------- Select system  -------</option>
								<?
									$db = $mongo->logsearch;
									$collection = $db->system;
									$system = $collection->find();
									$system->sort(array('system' => 1));
									foreach ($system as $doc) {
								?>
									<option value='<? echo $doc['system']; ?>' <? if(isset($_GET['system']) && $_GET['system'] == $doc['system']) echo "selected='selected'"; ?>>
										<? echo $doc['system']; ?>
									</option>
								<? } ?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label id='lbSelectNode' for="node">Select Node :</label>
						<span id="oldNode" style="color:#909090;"><? if(isset($_GET['node'])) echo $_GET['node']; ?></span>
						<div>
							<? foreach ($system as $doc) { ?>
								<select id='<? echo $doc['system']; ?>' class="form-control node-select" name='<? echo $doc['system']; ?>'>
									<option value='' disabled selected>------- Select node  -------</option>
									<?
										$collection = $db->system_config;
										$target = array('system' => $doc['system']);
										$node = $collection->find($target);
										$node->sort(array('node' => 1));
										foreach ($node as $tmp) {
									?>
										<option value='<? echo $tmp['node']; ?>' <? if(isset($_GET['node']) && $_GET['node'] == $tmp['node']) echo "selected='selected'"; ?>>
											<? echo $tmp['node']; ?>
										</option>
									<? } ?>
								</select>
							<? } ?>
						</div>
					</div>
				</div>

				<h3 class="page-header" id="logConfigure">Log Configure</h3>
				<div class="panel-body">
					<div class="form-group">
						<label for="process">Process :</label>
						<span id="oldProcess" style="color:#909090;"><? if(isset($_GET['process'])) echo $_GET['process']; ?></span>
						<div>
							<input type="text" class="form-control" name="process" id="process" placeholder="process" <? if(isset($_GET['process'])) echo "value='".$_GET['process']."'"; ?>>
						</div>
					</div>
					<?
						if($_GET['method'] == 'edit'){
							$collection = $db->service_config;
							$target = array('service' => $_GET['service'],
											'system' => $_GET['system'],
											'node' => $_GET['node'],
											'process' => $_GET['process'],);
							$cursor = $collection->findOne($target);
						}
					?>
					<div class="form-group tooltip-demo">
						<label for="path">Log Path :</label>
						<span id="oldPath" style="color:#909090;"><? if(isset($cursor['path'])) echo $cursor['path']; ?></span>
						<div data-toggle="tooltip" title="Path to your log file from root directory.<br/><br/>*Be careful your path is directory, isn't file.<br/>*Agent can find file in subdirectory.">
							<input type="text" class="form-control" name="path" id="path" placeholder="/path/to/your/log/" <? if(isset($cursor['path'])) echo "value='".$cursor['path']."'"; ?>>
						</div>
					</div>
					<div class="form-group">
						<label for="logType">Description :</label>
						<div>
							<input type="text" class="form-control" name="description" id="description" placeholder="description" <? if(isset($cursor['description'])) echo "value='".$cursor['description']."'"; ?>>
						</div>
					</div>
					<div class="form-group">
						<label for="logType">Log Type :</label>
						<div class="radio col-sm-offset-1"><input type="radio" name="logType" value="singleLine" <? if(isset($cursor['logType']) && $cursor['logType'] == 'singleLine') echo 'checked="checked"'; ?>>Single Line</div>
						<div class="radio col-sm-offset-1"><input type="radio" name="logType" value="multiLine" <? if(isset($cursor['logType']) && $cursor['logType'] == 'multiLine') echo 'checked="checked"'; ?>>Multiple Line</div>
					</div>
					<div class="form-group logTag tooltip-demo">
						<label for="logStartTag">Start Tag Regex:</label>
						<span id="oldLogStartTag" style="color:#909090;"><? if(isset($cursor['logStartTag'])) echo $cursor['logStartTag']; ?></span>
						<div  data-toggle="tooltip" title="Regular Expression for detect start log line.<br/>Ex. Strat log line begin with '&amp;#60;CDR' can use it as Start Tag Regex.<br/><br/>*Be careful it will detect only start log line.">
							<input type="text" class="form-control" name="logStartTag" id="logStartTag" placeholder="" <? if(isset($cursor['logStartTag'])) echo "value='".$cursor['logStartTag']."'"; ?>>
						</div>
					</div>
					<div class="form-group logTag tooltip-demo">
						<label for="logEndTag">End Tag Regex:</label>
						<span id="oldLogEndTag" style="color:#909090;"><? if(isset($cursor['logEndTag'])) echo $cursor['logEndTag']; ?></span>
						<div  data-toggle="tooltip" title="Regular Expression for detect end log line.<br/>Ex. End log line begin with '&amp;#60;/CDR' can use it as End Tag Regex.<br/><br/>*Be careful it will detect only end log line.">
							<input type="text" class="form-control" name="logEndTag" id="logEndTag" placeholder="" <? if(isset($cursor['logEndTag'])) echo "value='".$cursor['logEndTag']."'"; ?>>
						</div>
					</div>
				</div>

				<h3 class="page-header" id="msisdnConfigure">MSISDN Configure</h3>
				<div class="panel-body">
					<div class="form-group tooltip-demo">
						<label for="msisdnRegex">MSISDN Regex :</label>
						<span id="oldMsisdnRegex" style="color:#909090;"><? if(isset($cursor['msisdnRegex'])) echo $cursor['msisdnRegex']; ?></span>
						<div  data-toggle="tooltip" title="Regular Expression for detect msisdn for indexing.<br/>Ex. 'sip:870031720@10.134.192.18;transport=udp;user=phone'<br/>In this line msisdn is '870031720'<br/>can use 'sip:([0-9]{9})@' as msisdn regex<br/><br/>*Regex must contain other word beside msisdn for make sure that is msisdn.<br/>*Be careful in regex, msisdn must be inside bracket-() ">
							<input type="text" class="form-control" name="msisdnRegex" id="msisdnRegex" placeholder="" <? if(isset($cursor['msisdnRegex'])) echo "value='".$cursor['msisdnRegex']."'"; ?>>
						</div>
					</div>
				</div>

				<h3 class="page-header" id="dateConfigure">Date Configure</h3>
				<div class="panel-body">
					<div class="form-group">
						<label for="dateHolder">Date Holder :</label>
						<div class="radio col-sm-offset-1"><input type="radio" name="dateHolder" value="inside" <? if(isset($cursor['dateHolder']) && $cursor['dateHolder'] == 'inside') echo 'checked="checked"'; ?>>Inside log file</div>
						<div class="radio col-sm-offset-1"><input type="radio" name="dateHolder" value="outside" <? if(isset($cursor['dateHolder']) && $cursor['dateHolder'] == 'outside') echo 'checked="checked"'; ?>>In file's path</div>
					</div>
					<div class="form-group tooltip-demo">
						<label for="dateRegex">Date Regex :</label>
						<span id="oldDateRegex" style="color:#909090;"><? if(isset($cursor['dateRegex'])) echo $cursor['dateRegex']; ?></span>
						<div data-toggle="tooltip" title='Regular Expression for detect date for indexing.<br/>Ex. "starttime=2014-06-24T12:20:21.332"<br/>In this line date is "2014-06-24" can use<br/> "starttime=([0-9]{4}-[0-1][0-9]-[0-3][0-9])T[0-2][0-9]:[0-5][0-9]:[0-5][0-9]"<br/>as date regex.<br/><br/>*Regex must contain other word beside date for make sure that is date.<br/>*Be careful in regex, date must be inside bracket-( )'>
							<input type="text" class="form-control" name="dateRegex" id="dateRegex" placeholder="" <? if(isset($cursor['dateRegex'])) echo "value='".$cursor['dateRegex']."'"; ?>>
						</div>
					</div>
					<div class="form-group tooltip-demo">
						<label for="dateFormat">Date Format :</label>
						<span id="oldDateFormat" style="color:#909090;"><? if(isset($cursor['dateFormat'])) echo $cursor['dateFormat']; ?></span>
						<div  data-toggle="tooltip" title='Format:<br/>Use "%Y" for year, "%m" for month and "%d" for day.<br/>Ex. "2014-06-24" use "%Y-%m-%d"<br/>"20140624" use "%Y%m%d"'>
							<input type="text" class="form-control" name="dateFormat" id="dateFormat" placeholder="" <? if(isset($cursor['dateFormat'])) echo "value='".$cursor['dateFormat']."'"; ?>>
						</div>
					</div>
				</div>

				<h3 class="page-header" id="timeConfigure">Time Configure</h3>
				<div class="panel-body">
					<div class="form-group tooltip-demo">
						<label for="timeRegex">Time Regex :</label>
						<span id="oldTimeRegex" style="color:#909090;"><? if(isset($cursor['timeRegex'])) echo $cursor['timeRegex']; ?></span>
						<div  data-toggle="tooltip" title='Regular Expression for detect time for indexing.<br/>Ex. "starttime=2014-06-24T12:20:21.332"<br/>In this line time is "12:20:21" can use <br/> "starttime=[0-9]{4}-[0-1][0-9]-[0-3][0-9]T([0-2][0-9]:[0-5][0-9]:[0-5][0-9])"<br/> as time regex.<br/><br/>*Regex must contain other word beside time for make sure that is time.<br/>*Be careful in regex, time must be inside bracket-( )'>
							<input type="text" class="form-control" name="timeRegex" id="timeRegex" placeholder="" <? if(isset($cursor['timeRegex'])) echo "value='".$cursor['timeRegex']."'"; ?>>
						</div>
					</div>
					<div class="form-group tooltip-demo">
						<label for="timeFormat">Time Format :</label>
						<span id="oldTimeFormat" style="color:#909090;"><? if(isset($cursor['timeFormat'])) echo $cursor['timeFormat']; ?></span>
						<div  data-toggle="tooltip" title='Format:<br/>Use "%H" for hour, "%M" for minute and "%S" for second.<br/>Ex. "12:20:21" use "%H:%M:%S"'>
							<input type="text" class="form-control" name="timeFormat" id="timeFormat" placeholder="" <? if(isset($cursor['timeFormat'])) echo "value='".$cursor['timeFormat']."'"; ?>>
						</div>
					</div>
				</div>

				<h3 class="page-header" id="crontabConfigure">Crontab Configure
					<small style="color:#909090;"><? if(isset($cursor['state'])) echo $cursor['state']; else echo "Stopped"; ?></small>
					<input id="state" type="hidden" value="<? if(isset($cursor['state'])) echo $cursor['state']; else echo "Stopped"; ?>">
					<div style="float:right;">
						<button type="button" class="btnState btn btn-sm btn-success">START</button>
						<button type="button" class="btnState btn btn-sm btn-danger">STOP</button>
					</div>
				</h3>
				<div class="panel-body">
					<div class="form-group tooltip-demo">
						<label for="crontab">Crontab Setting :</label>
						<span id="oldCrontab" style="color:#909090;"><? if(isset($cursor['crontab'])) echo $cursor['crontab']; ?></span>
						<div  data-toggle="tooltip" title='Ex.<br/>"*/15  *  *  *  *" Run every 15 minutes<br/>"15  *  *  *  *" Run at ?:15 every hour<br/>"0  20  *  *  *" Run every day at 20:00<br/>"0  12  *  *  0" Run every 12:00 on Sunday<br/>"0  18  1  *  *" Run at 18:00 on 1st day every month'>
							<input type="text" class="form-control" name="crontab" id="crontab" placeholder="{*,[0-59]} {*,[0-23]} {*,[1-31]} {*,[1-12]} {*,[0-6]}" <? if(isset($cursor['crontab'])) echo "value='".$cursor['crontab']."'"; ?>>
						</div>
						<small style="color:#909090;">{minute} {hour} {day_of_month} {month} {day_of_week}</small>
					</div>
					<div class="form-group tooltip-demo">
						<label for="mmin">mmin<small>(min)</small> :</label>
						<span id="oldMmin" style="color:#909090;"><? if(isset($cursor['mmin'])) echo $cursor['mmin']; ?></span>
						<div data-toggle="tooltip" title="Crontab finds files, file’s data was last modified mmin minutes ago.<br/>*mmin should be greater than gap's time of crontab<br/> and greater than double of inteval">
							<input type="text" class="form-control" name="mmin" id="mmin" placeholder="Minutes scale" <? if(isset($cursor['mmin'])) echo "value='".$cursor['mmin']."'"; ?>>
						</div>
					</div>
					<div class="form-group tooltip-demo">
						<label for="interval">Interval Time<small>(min)</small> :</label>
						<span id="oldInterval" style="color:#909090;"><? if(isset($cursor['interval'])) echo $cursor['interval']; ?></span>
						<div data-toggle="tooltip" title="Crontab finds files, file’s data was last modified mmin minutes ago<br/>and skip last interval minutes.<br/>*interval should be greater than gap's time of temp log still written">
							<input type="text" class="form-control" name="interval" id="interval" placeholder="Minutes scale" <? if(isset($cursor['interval'])) echo "value='".$cursor['interval']."'"; ?>>
						</div>
					</div>
				</div>

				<h6 class="page-header"></h4>
				<div class="panel-body">
		      		<div class="form-group" style="float:right;">
		      			<img id='waitAdd' src='img/ajax-loader.gif'></img>
		        		<button id="btnAdd" type="button" class="btn btn-primary">Add Node</button>
		        		<button id="btnSave" type="button" class="btn btn-primary">Save changes</button>
		      		</div>
		      		<div class="form-group">
		      			<button id="btnTest" type="button" class="btn btn-primary">Test</button>
		      		</div>
		      	</div>
	      	</form>
	      	<div id="testResult" class="panel-body"></div>
		</div>
        <!-- /#page-wrapper -->
		<script>
			function checkInput()
			{
				var pattern;
				var system = $('#system').val();
				if(system == '' || system == null){
					alert("Please, select system");
					$('html,body').animate({ scrollTop: $('#nodeConfigure').offset().top - 20 }, 'slow');
					return false;
				}	
				var node = $('#' + system).val();
				if(node == '' || node == null){
					alert("Please, select node");
					$('html,body').animate({ scrollTop: $('#nodeConfigure').offset().top - 20 }, 'slow');
					return false;
				}
				if(jQuery.trim($('#process').val()).length <= 0){
					alert("Please, fill your process");
					$('html,body').animate({ scrollTop: $('#logConfigure').offset().top - 20 }, 'slow');
					return false;
				}
				if(jQuery.trim($('#path').val()).length <= 0){
					alert("Please, fill your path");
					$('html,body').animate({ scrollTop: $('#logConfigure').offset().top - 20 }, 'slow');
					return false;
				}else{
					pattern = /^\//
					if (!pattern.test($('#path').val())){
						alert("Log's path must be full path -form root directory.");
						$('html,body').animate({ scrollTop: $('#logConfigure').offset().top - 20 }, 'slow');
						return false;
					}
				}
				if (!$('input[name="logType"]').is(':checked')){
					alert("Please, select log's type");
					$('html,body').animate({ scrollTop: $('#logConfigure').offset().top - 20 }, 'slow');
					return false;
				}else if($("input[name='logType']:checked").val() == 'multiLine'){
					if(jQuery.trim($('#logStartTag').val()).length <= 0){
						alert("Please, fill your log's start tag");
						$('html,body').animate({ scrollTop: $('#logConfigure').offset().top - 20 }, 'slow');
						return false;
					}
					if(jQuery.trim($('#logEndTag').val()).length <= 0){
						alert("Please, fill your log's end tag");
						$('html,body').animate({ scrollTop: $('#logConfigure').offset().top - 20 }, 'slow');
						return false;
					}
				}
				if(jQuery.trim($('#msisdnRegex').val()).length <= 0){
					alert("Please, fill your msisdn's regular expression");
					$('html,body').animate({ scrollTop: $('#msisdnConfigure').offset().top - 20 }, 'slow');
					return false;
				}
				if (!$('input[name="dateHolder"]').is(':checked')){
					alert("Please, select date holder");
					$('html,body').animate({ scrollTop: $('#dateConfigure').offset().top - 20 }, 'slow');
					return false;
				}
				if(jQuery.trim($('#dateRegex').val()).length <= 0){
					alert("Please, fill your date's regular expression");
					$('html,body').animate({ scrollTop: $('#dateConfigure').offset().top - 20 }, 'slow');
					return false;
				}
				if(jQuery.trim($('#dateFormat').val()).length <= 0){
					alert("Please, fill your date's format");
					$('html,body').animate({ scrollTop: $('#dateConfigure').offset().top - 20 }, 'slow');
					return false;
				}
				if(jQuery.trim($('#timeRegex').val()).length <= 0){
					alert("Please, fill your time's regular expression");
					$('html,body').animate({ scrollTop: $('#timeConfigure').offset().top - 20 }, 'slow');
					return false;
				}
				if(jQuery.trim($('#timeFormat').val()).length <= 0){
					alert("Please, fill your time's format");
					$('html,body').animate({ scrollTop: $('#timeConfigure').offset().top - 20 }, 'slow');
					return false;
				}
				pattern = /^(\*|\*\/([0-9]|[1-5][0-9])|([0-9]|[1-5][0-9])) (\*|\*\/([0-9]|1[0-9]|2[0-3])|([0-9]|1[0-9]|2[0-3])) (\*|\*\/([1-9]|[1-2][0-9]|3[0-1])|([1-9]|[1-2][0-9]|3[0-1])) (\*|\*\/([1-9]|1[0-2])|([1-9]|1[0-2])) (\*|\*\/[0-6]|[0-6])$/;
				if(!pattern.test($('#crontab').val())){
					alert("Please, fill your contrab with correct pattern.");
					$('html,body').animate({ scrollTop: $('#crontabConfigure').offset().top - 20 }, 'slow');
					return false;
				}
				
				pattern = /^([1-9]|[1-9][0-9]+)$/;
				if(!pattern.test($('#mmin').val())){
					alert("Please, fill your mmin with positive integer\nand greater than 0.");
					$('html,body').animate({ scrollTop: $('#crontabConfigure').offset().top - 20 }, 'slow');
					return false;
				}
				if(!pattern.test($('#interval').val())){
					alert("Please, fill your interval with positive integer\nand greater than 0.");
					$('html,body').animate({ scrollTop: $('#crontabConfigure').offset().top - 20 }, 'slow');
					return false;
				}
				if(pattern.test($('#interval').val()) && pattern.test($('#mmin').val()))
					if(parseInt(jQuery.trim($('#mmin').val()))/2 <= parseInt(jQuery.trim($('#interval').val()))){
						alert("mmin, must be greater than double of inteval.");
						$('html,body').animate({ scrollTop: $('#crontabConfigure').offset().top - 20 }, 'slow');
						return false;
					}				
				//var crontab = $('#crontab').val();
				return true;
			}
		
			$(document).ready(function() {
				$('#waitAdd').hide();
			    $('.node-select').hide();
			    $('#lbSelectNode').hide();
			    $('.logTag').hide();
				
				$('.tooltip-demo').tooltip({
					selector: "[data-toggle=tooltip]",
					container: "body",
					html: true,
					placement: "bottom"
				})
				
		    	if($("input[name='logType']:checked").val() == 'multiLine')
		    		$('.logTag').show();
			    
			    if("<? echo $_GET['method']; ?>" == "edit"){
			    	$('#btnAdd').hide();
			    }else{
			    	$('#btnSave').hide();
			    }

			    if($('#state').val() == 'Running'){
			    	$('.btnState.btn-success').attr("disabled","disabled");
			    }else{
			    	$('.btnState.btn-danger').attr("disabled","disabled");
			    }
			    
			    if(jQuery.trim($('#system').val()).length > 0){
			    	$('#lbSelectNode').show();
			    	$('#' + $('#system').val()).show();
			    }

			    $("input[name=logType]").change(function(){
			    	if($("input[name='logType']:checked").val() == 'singleLine')
			    		$('.logTag').hide();
			    	else if($("input[name='logType']:checked").val() == 'multiLine')
			    		$('.logTag').show();
			    });

			     $('.btnState').click(function() {
			     	if($('#state').val() == 'Running'){
			     		$('.btnState.btn-danger').attr("disabled","disabled");
				    	$('.btnState.btn-success').removeAttr("disabled");
				    	$('#state').val('Stopped');
				    }else{
						$('.btnState.btn-success').attr("disabled","disabled");
						$('.btnState.btn-danger').removeAttr("disabled");
						$('#state').val('Running');
			    	}
			     });

			    $('#btnTest').click(function() {
			    	$('#waitAdd').show();
			        var path = escape($('#path').val());
			        var logType = $("input[name='logType']:checked").val();
			        var logStartTag = 'none';
					var logEndTag = 'none';
			        if(logType == 'multiLine'){
			        	logStartTag = escape($("#logStartTag").val());
						logEndTag = escape($("#logEndTag").val());
			        }
					var msisdnRegex = escape($("#msisdnRegex").val());
					var dateHolder = $("input[name='dateHolder']:checked").val();
					var dateRegex = escape($("#dateRegex").val());
					var dateFormat = escape($("#dateFormat").val());
					var timeRegex = escape($("#timeRegex").val());
					var timeFormat = escape($("#timeFormat").val());
		            $.get("function/serviceFunction.php", {
		                method: 'indexTest',
		                mode: 'test',
		                path: path,
		            	logType: logType,
		            	logStartTag: logStartTag,
		            	logEndTag: logEndTag,
		            	msisdnRegex: msisdnRegex,
		            	dateHolder: dateHolder,
		            	dateRegex: dateRegex,
		            	dateFormat: dateFormat,
		            	timeRegex: timeRegex,
		            	timeFormat: timeFormat
		            }, function(result) {
		            	$('#testResult').fadeOut(0);
		            	$('#testResult').fadeIn(300);
		                $('#testResult').html(result);
		                $("form").removeClass("col-sm-offset-3");
		                $('#waitAdd').hide();
		                $('html, body').animate({ scrollTop: $('#testResult').offset().top }, 'slow');
		            });
		            
			    });

			    $('#btnAdd').click(function() {
			        var service = $('#service').html();
			        var system = $('#system').val();
			        var node = $('#' + system).val();
					var process = jQuery.trim($('#process').val());
			       	var path = jQuery.trim($('#path').val());
			       	var description = $('#description').val();
			        var logType = $("input[name='logType']:checked").val();
					var logStartTag = $("#logStartTag").val();
					var logEndTag = $("#logEndTag").val();
					var msisdnRegex = $("#msisdnRegex").val();
					var dateHolder = $("input[name='dateHolder']:checked").val();
					var dateRegex = $("#dateRegex").val();
					var dateFormat = $("#dateFormat").val();
					var timeRegex = $("#timeRegex").val();
					var timeFormat = $("#timeFormat").val();
					var mmin = $('#mmin').val();
			        var interval = $('#interval').val();
			        var crontab = $('#crontab').val();
			        var state = $('#state').val();
					if(state == 'Running' && !(checkInput() && confirm("You're going to run crontab.\n\nDo you test already? If not, Cancel and test your config.\n\nBecareful, make sure your config is work then OK.")))
						return;
			        if (jQuery.trim(node).length > 0) {
			            $('#waitAdd').show();
			            $.get("function/serviceFunction.php", {
			                method: "addServiceConfig",
			                service: service,
			                system: system,
			                node: node,
							process: process,
			                path: path,
			                description: description,
			                logType: logType,
			                logStartTag: logStartTag,
			                logEndTag: logEndTag,
			                msisdnRegex: msisdnRegex,
			                dateHolder: dateHolder,
			                dateRegex: dateRegex,
			                dateFormat: dateFormat,
			                timeRegex: timeRegex,
			                timeFormat: timeFormat,
			                mmin: mmin,
			                interval: interval,
			                crontab: crontab,
			                state: state
			            }, function(result) {
			                if (result == "success") {
			                    $('#Modal').modal('toggle');
			                    window.location = "<?=$root_url?>" + "/serviceConfig.php?serviceName=" + service;
			                } else
			                    alert(result);
			                $('#waitAdd').hide();
			            });
			        } else{
			            alert("Plese select node.");
						$('html,body').animate({ scrollTop: $('#nodeConfigure').offset().top - 20 }, 'slow');
					}
			    });

			    $('#btnSave').click(function() {
			        var service = $('#service').html();
			        var oldSystem = $('#oldSystem').html();
			        var oldNode = $('#oldNode').html();
			        var oldProcess = $('#oldProcess').html();
			        var system = $('#system').val();
			        var node = $('#' + system).val();
					var process = jQuery.trim($('#process').val());
			       	var path = jQuery.trim($('#path').val());
			       	var description = $('#description').val();
			        var logType = $("input[name='logType']:checked").val();
					var logStartTag = $("#logStartTag").val();
					var logEndTag = $("#logEndTag").val();
					var msisdnRegex = $("#msisdnRegex").val();
					var dateHolder = $("input[name='dateHolder']:checked").val();
					var dateRegex = $("#dateRegex").val();
					var dateFormat = $("#dateFormat").val();
					var timeRegex = $("#timeRegex").val();
					var timeFormat = $("#timeFormat").val();
					var mmin = $('#mmin').val();
			        var interval = $('#interval').val();
			        var crontab = $('#crontab').val();
			        var state = $('#state').val();
					if(state == 'Running' && !(checkInput() && confirm("You're going to run crontab.\n\nDo you test already? If not, Cancel and test your config.\n\nBecareful, make sure your config is work then OK.")))
						return;
			        if (jQuery.trim(node).length > 0) {
			            $('#waitAdd').show();
			            $.get("function/serviceFunction.php", {
			                method: "editServiceConfig",
			                service: service,
			                oldSystem: oldSystem,
			                oldNode: oldNode,
			                oldProcess: oldProcess,
			                system: system,
			                node: node,
							process: process,
			                path: path,
			                description: description,
			                logType: logType,
			                logStartTag: logStartTag,
			                logEndTag: logEndTag,
			                msisdnRegex: msisdnRegex,
			                dateHolder: dateHolder,
			                dateRegex: dateRegex,
			                dateFormat: dateFormat,
			                timeRegex: timeRegex,
			                timeFormat: timeFormat,
			                mmin: mmin,
			                interval: interval,
			                crontab: crontab,
			                state: state
			            }, function(result) {
			                if (result == "success") {
			                    //alert(result);
			                    window.location = "<?=$root_url?>" + "/serviceConfig.php?serviceName=" + service;
			                } else
			                    alert(result);
			                $('#waitAdd').hide();
			            });
			        } else{
			            alert("Plese select node.");
						$('html,body').animate({ scrollTop: $('#nodeConfigure').offset().top - 20 }, 'slow');
					}
			    });

			    $('#system').change(function() {
			        $('#lbSelectNode').show();
			        $('.node-select').hide();
			        $('.node-select').val('');
			        $('#' + this.value).fadeIn(300);
			    });
			});

		</script>
	</body>
</html>