<?php require_once("include/init.php"); ?>
<!DOCTYPE html>
<html>
	<head>
		<?php require_once("include/head.html"); ?>
	</head>
	<body>
		<?php require_once("include/nav.html"); ?>
		<div id="page-wrapper" class="container">

			<h4 class="page-header">MSISDN : ' <?echo $_GET['msisdn'];?> ' Between : ' <?echo $_GET['range'];?> '</h4>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<ul class="timeline">
					<?
						$i = 1;
						$datetime = explode("-", $_GET['range']);
						$db = $mongo->logsearch;
						$collection = $db->log_index;
						$regex = new MongoRegex('/'.$_GET['msisdn'].'/');
						$test_msisdn = $_GET['msisdn'];
						if(!isset($_GET['chkAdvanceSearch'])){
							//$target = array('msisdn' => $regex, 
							$target = array('msisdn' => $test_msisdn, 
								'datetime' => array( '$gte' => trim($datetime[0]), '$lte' => trim($datetime[1]) )
							);
							$cursor = $collection->find($target);
							$cursor->sort(array('datetime' => 1));
						} else {
							$target = array(
								//'msisdn' => $regex, 
								'msisdn' => $test_msisdn, 
								'datetime' => array( '$gte' => trim($datetime[0]), '$lte' => trim($datetime[1]) ), 
								'service' => array('$in' => $_GET['serviceSelect'])
							);
							$cursor = $collection->find($target);
							$cursor->sort(array('datetime' => 1));
						}
						foreach ($cursor as $doc) {
					?>
							<li id="timeline_<?=$i?>" class="tooltip-demo">
								<div class="timeline-badge info" data-toggle="tooltip" data-placement="top" title="<?=$doc['datetime']?>"><i class="fa"></i>
								</div>
								<div class="timeline-panel" style="cursor:pointer;" id="<?=$i?>">
									<div class="timeline-heading">
										<div style="float:right;">
											<button type="button" class="log btn btn-default btn-circle glyphicon glyphicon-fullscreen" name="<?=$i?>" data-toggle="modal" data-target="#myModal"></button>
										</div>
										<div>
											<h4 class="timeline-title"><?=$doc['service']?> 
												<small><i class="glyphicon glyphicon-time"></i> <?=$doc['datetime']?></small>
											</h4>
											<p><small>
													<i class="glyphicon glyphicon-cog"></i> <?=$doc['system']?> 
													<i class="glyphicon glyphicon-play"></i> <?=$doc['node']?> 
													<i class="glyphicon glyphicon-play"></i> <?=$doc['process']?>
											</small></p>
										</div>
									</div>
									<div id="logBody_<?=$i?>" class="timeline-body">
										<!-- <pre style="cursor:text;"><?//=$doc['path']?></pre> -->
										<pre style="cursor:text;" id="logContent_<?=$i?>" class="logContent"></pre>
									</div>
									<input type="hidden" id="path_<?=$i?>" value="<?=$doc['path']?>"> 
									<input type="hidden" id="index_<?=$i?>" value="<?=$doc['index']?>"> 
									<input type="hidden" id="startTag_<?=$i?>" value="<?=$doc['startTag']?>"> 
									<input type="hidden" id="endTag_<?=$i?>" value="<?=$doc['endTag']?>">
									<input type="hidden" id="showLineNumber_<?=$i?>" value="0">
								</div>
							</li>
					<?
							$i++;
						}
						
					?>
						
			</div>
			<!-- /.panel-body -->

			<!-- Modal -->
			<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			  	<div class="modal-dialog modal-lg">
			    	<div class="modal-content">
			    		<div class="modal-header">
			    			<form class="form-inline" role="form">
							  	<div class="form-group">
							    	<label>Index</label>
							    	<input type="text" class="form-control" id="index" style="width:85px" readonly>
							  	</div>
							  	<div class="form-group">
							    	<label>Start Line</label>
							    	<input type="number" class="form-control" id="startTag" style="width:105px">
							  	</div>
							  	<div class="form-group">
							    	<label>End Line</label>
							    	<input type="number" class="form-control" id="endTag" style="width:105px">
							  	</div>
							  	<div class="form-group checkbox">
							  		<label><input type="checkbox" id="showLineNumber"> Show Line Number</label>
							  	</div>
							  	<div class="form-group">
							  		<input type="hidden" id="identifier"> 
							    	<img id='waitAdd' src='img/ajax-loader.gif'></img>
							  	</div>
							  	<div class="form-group" style="float:right;">
							    	<button type="button" class="close" data-dismiss="modal" aria-hidden="false">&times;</button>
							  	</div>
							</form>
        				</div>
				      	<div class="modal-body">
				      		<pre id="path"></pre>
				        	<pre id="logContent"></pre>
				      	</div>
			    	</div>
			  	</div>
			</div>
			
			

		</div>
        <!-- /#page-wrapper -->

        <script type="text/javascript">
		
			function animateRender(id) {
				$("#timeline_"+id).animate({opacity: 0},0);
				$("#timeline_"+id).animate({opacity: 1},400);
				
			}
			
			function renderTimeline()
			{
				var entry = <?=$i?>;
				var i = 1;
				var left = 0;
				var right = 0;
				if(entry == 0)
					return 0;
					
				//$(".timelineEntry").removeClass("timeline-inverted");
				left = $("#timeline_"+i.toString()).height() + 20;
				
				if(entry <= 1)
					return 0;
					
				$("#timeline_2").addClass("second");
				$("#timeline_2").addClass("timeline-inverted");
				right = $("#timeline_2").height() + 40;
				for(i=3;i<=entry;i++){
					if(left > right){
						if(!$("#timeline_"+i.toString()).hasClass("timeline-inverted"))
							$("#timeline_"+i.toString()).addClass("timeline-inverted", animateRender(i));
						right = right + $("#timeline_"+i.toString()).height() + 20;
					} else {
						if($("#timeline_"+i.toString()).hasClass("timeline-inverted"))
							$("#timeline_"+i.toString()).removeClass("timeline-inverted", animateRender(i));
						left = left + $("#timeline_"+i.toString()).height() + 20;
					}
				}
			}
		
        	function getLogContent(id) {
				var path = $("#path_"+ id).val();
				var index = $("#index_"+ id).val();
				var startTag = $("#startTag_"+ id).val();
				var endTag = $("#endTag_"+ id).val();
				var showLineNumber = $("#showLineNumber_"+ id).val();
				
				$('#waitAdd').show();
				$.get("function/getLogContent.php", {
	                path: path,
	            	index: index,
	            	startTag: startTag,
	            	endTag: endTag,
	            	showLineNumber: showLineNumber
	            }, function(result) {
					$('#logContent').html(result);
	                $('#logContent_'+id).html(result);
	                $('#waitAdd').hide();
	            });
			}
			
			function autoResizeDiv()
			{
				$('.logContent').css('max-height', window.innerHeight -150 +'px');
				$('#logContent').css('max-height', window.innerHeight -225 +'px');
				renderTimeline();
			}
			window.onresize = autoResizeDiv;
			autoResizeDiv();
			
			$(document).ready(function() {
				renderTimeline();
				$('#waitAdd').hide();
				$('.timeline-body').hide();
				
				$('.tooltip-demo').tooltip({
					selector: "[data-toggle=tooltip]",
					container: "body",
					html: true
				});
				
				$(".timeline-panel").bind("DOMSubtreeModified", function(e) {
					renderTimeline();
				});
				
				$('.timeline-panel').click(function(e) {
					if(!$(e.target).is('pre') && !$(e.target).is('button')){
						id = this.id;
						$("#logBody_"+id).fadeToggle(200);
						if($('#logContent_'+id).html().length <= 0)
							getLogContent(id);
						setTimeout(function(){renderTimeline()},250);
					}
			    });
				
				$('button.log').click(function(e) {
					id = this.name;
					path = $("#path_"+ id).val();
					index = $("#index_"+ id).val();
					startTag = $("#startTag_"+ id).val();
					endTag = $("#endTag_"+ id).val();
					showLineNumber = $("#showLineNumber_"+ id).val();
					$("#path").html(path);
					$("#index").val(index);
					$("#startTag").val(startTag);
					$("#endTag").val(endTag);
					if(showLineNumber == '1')
						$("#showLineNumber").prop( "checked", true );
					else
						$("#showLineNumber").prop( "checked", false );
					$("#identifier").val(id);
					getLogContent(id);
			    });

				$( "#startTag" ).change(function() {
					var id = $("#identifier").val();
					var start = $("#startTag").val();
					var end = $("#endTag").val();
					$("#startTag_"+ id).val(start);
					if(parseInt(start) > parseInt(end)){
						$("#endTag_"+ id).val(start);
						$("#endTag").val(start)
					}
					getLogContent(id);
				});

				$( "#endTag" ).change(function() {
					var id = $("#identifier").val();
					var start = $("#startTag").val();
					var end = $("#endTag").val();
					$("#endTag_"+ id).val(end);
					if(parseInt(start) > parseInt(end)){
						$("#startTag_"+ id).val(end);
						$("#startTag").val(end)
					}
					getLogContent(id);
				});

				$( "#showLineNumber" ).change(function() {
					var id = $("#identifier").val();
					var showLineNumber = $("#showLineNumber").is(':checked') ? 1 : 0;
					$("#showLineNumber_"+ id).val(showLineNumber);
					getLogContent(id);
				});
			});
		</script>
	</body>
</html>