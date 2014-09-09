<?php require_once("include/init.php"); ?>
<!DOCTYPE html>
<html>
	<head>
		<?php require_once("include/head.html"); ?>
	</head>
	<body>
		<?php require_once("include/nav.html"); ?>
		<div id="page-wrapper" class="container">
			<h2 class="page-header">Search</h2>
			<form id="searchForm" role="form" action="result.php" method="get">
				<div class="panel-body col-sm-offset-4 col-sm-4">
					<div class="form-group">
						<label for="inputMsisdn" class="control-label">MSISDN</label>
						<div>
							<input type="text" class="form-control" name='msisdn' id="msisdn" placeholder="MSISDN">
						</div>
					</div>
					<div class="form-group tooltip-demo" >
						<label for="inputDateTime" class="control-label">Date &amp; Time Range</label>
						<div class="input-group">
							<span class="input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
							<input type="text" class="form-control" name="range" id="dateTimeRange" placeholder="2014/06/30 00:00:00 - 2014/07/14 23:59:59" data-toggle="tooltip" data-placement="right" title="Format:<br/>YYYY/MM/DD hh:mm:ss - YYYY/MM/DD hh:mm:ss<br/>Ex. 2014/06/30 00:00:00 - 2014/07/14 23:59:59">
						</div>
					</div>
					<div class="form-group">
						<div class="input-group checkbox">
							<label>
								<input type="checkbox" id="chkAdvanceSearch" name="chkAdvanceSearch" value=true>Advance Search
							</label>
						</div>
						<div class="input-group" id="wpAdvanceSearch">
							<select id="serviceSelect" name="serviceSelect[]" multiple="multiple">
							<? 
								$db = $mongo->logsearch;
								$collection = $db->service;
								$cursor = $collection->find();
								$cursor->sort(array('service' => 1));
								foreach ($cursor as $doc) {
							?>
								<option value="<?=$doc['service']?>"><?=$doc['service']?></option>
							<? } ?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<button type="button" id="btnSearch" class="btn btn-primary btn-lg btn-block">Search<span id="wait">ing... <img id='waitAdd' src='img/ajax-loader.gif' style="height:20px;weight:20px;"></img></span></button>
					</div>
				</div>
			</form>
		</div>
        <!-- /#page-wrapper -->
		<script type="text/javascript">
			$(document).ready(function() {
				$('#navSearch').addClass( "active" );
				$('#wait').hide();
				
				$('.tooltip-demo').tooltip({
					selector: "[data-toggle=tooltip]",
					container: "body",
					html: true
				})
				
				$('#wpAdvanceSearch').hide();
			
				$('#dateTimeRange').daterangepicker({
					timePicker: true,
					timePickerIncrement: 10,
					format: 'YYYY/MM/DD HH:mm:ss',
					timePicker12Hour: false
				}, function(start, end, label) {
					console.log(start.toISOString(), end.toISOString(), label);
				});
				
				$('#serviceSelect').multiselect({
					includeSelectAllOption: true,
					enableFiltering: true,
					maxHeight: 200
					//selectAllValue: 'multiselect-all'
				});
				/*
				$('option', $('#serviceSelect')).each(function(element) {
				  $('#serviceSelect').multiselect('select', $(this).val());
				});
				*/
				if($('#chkAdvanceSearch:checked').val())
					$('#wpAdvanceSearch').fadeIn(300);
				
				$('#chkAdvanceSearch').change(function() {
					if($('#chkAdvanceSearch:checked').val())
						$('#wpAdvanceSearch').fadeIn(300);
					else
						$('#wpAdvanceSearch').fadeOut(200);
					
				});
				
				$('#btnSearch').click(function() {
					var patternMsisdn = /(^[0-9]{9}$)|(^[0-9]{11}$)|(^0[0-9]{9}$)/;
					var patternDate = /^(20)[0-9]{2}\/(0[1-9]|1[012])\/([0-2][0-9]|3[01]) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])( - |-)(20)[0-9]{2}\/(0[1-9]|1[012])\/([0-2][0-9]|3[01]) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/;
					var dateRange = $('#dateTimeRange').val();
					var date = dateRange.split("-");
					if (!patternMsisdn.test($('#msisdn').val()))
						alert("MSISDN is incorrect");
					else if (!patternDate.test($('#dateTimeRange').val()) || jQuery.trim(date[0])>jQuery.trim(date[1]))
						alert("Date time range is incorrect");
					else if ($('#chkAdvanceSearch:checked').val() && $('#serviceSelect').val() == null)
						alert("Please, select service at least one");
					else {
						$('#searchForm').submit();
						$( "#wait" ).show();
					}
				});
			});
	   </script>
	</body>
</html>