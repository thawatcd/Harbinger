<?php require_once("include/init.php"); ?>
<!DOCTYPE html>
<html>
	<head>
		<?php require_once("include/head.html"); ?>
	</head>
	<body>
		<?php require_once("include/nav.html"); ?>
		<div id="page-wrapper" class="container">

			<h2 class="page-header">Configure Service '<span id="serviceName"><? echo $_GET['serviceName'] ?></span>'</h2>

			<!-- /.panel-heading -->
			<div class="panel-body">
				<h4 class="page-header" style="margin-top:10px">Add Service
					<button type="button" id="btnAdd" style="float:right;margin-top:-8px" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#Modal">Add Node</button>
				</h4><br/>
				<div  class="table-responsive">
					<table class="table table-striped table-bordered table-hover" id="dataTables">
						<thead>
							<tr>
								<th>System</th>
								<th>Node</th>
								<th>Process</th>
								<th>Crontab</th>
								<th>Description</th>
								<th style="min-width:53px;width:53px;"></th>
							</tr>
						</thead>
						<tbody>
							<?
								$i = 0;
								$db = $mongo->logsearch;
								$collection = $db->service_config;
								$target = array('service' => $_GET['serviceName']);
								$cursor = $collection->find($target);
								foreach ($cursor as $doc) {
							?>
							<tr>
								<td id="<? echo $i.'_system'; ?>"><?=$doc['system']?></td>
								<td id="<? echo $i.'_node'; ?>"><?=$doc['node']?></td>
								<td id="<? echo $i.'_process'; ?>"><?=$doc['process']?></td>
								<td id="<? echo $i.'_description'; ?>"><?=$doc['state']?></td>
								<td id="<? echo $i.'_description'; ?>"><?=$doc['description']?></td>
								<td>
									<button type="button" class="btnEdit btn btn-primary btn-xs" data-toggle="modal" data-target="#Modal" value="<? echo $i; ?>">
									  	<span class="glyphicon glyphicon-edit"></span>
									</button>
									<button type="button" class="btnDelete btn btn-danger btn-xs" value="<? echo $i; ?>">
									  	<span class="glyphicon glyphicon-trash"></span>
									</button>
								</td>
							</tr>
							<?
									$i++;
								}
							?>
						</tbody>
					</table>
				</div>
				<!-- /.table-responsive -->
			</div>
			<!-- /.panel-body -->
		</div>
        <!-- /#page-wrapper -->
		<script>
			$(document).ready(function() {
			    $('#navService').addClass("active");

			    $('#btnAdd').click(function() {
			    	var service = $('#serviceName').html();
			        window.location = "<?=$root_url?>" + "/nodeConfig.php?method=add&service=" + service;
			    });

			    $('.btnEdit').click(function() {
			        var service = $('#serviceName').html();
			        var system = $('#' + this.value + '_system').html();
			        var node = $('#' + this.value + '_node').html();
			        var process = $('#' + this.value + '_process').html();
			        window.location = "<?=$root_url?>" + "/nodeConfig.php?service=" + service + "&system=" + system + "&node=" + node + "&process=" + process + "&method=edit";
			    });

			    $('.btnDelete').click(function() {
			        var node = $('#' + this.value + '_node').html();
			        var system = $('#' + this.value + '_system').html();
			        var process = $('#' + this.value + '_process').html();
			        var service = $('#serviceName').html();
			        var r = confirm("Do you want to delete node '" + node + "'?");
			        if (r == true) {
			            $('#waitAdd').show();
			            $.get("function/serviceFunction.php", {
			                method: "deleteServiceConfig",
			                service: service,
			                system: system,
			                process: process,
			                node: node
			            }, function(result) {
			                if (result == "success") {
			                    $('#Modal').modal('toggle');
			                    window.location.reload();
			                } else
			                    alert(result);
			                $('#waitAdd').hide();
			            });
			        }
			    });
				$('#dataTables').dataTable();
			});
		</script>
	</body>
</html>