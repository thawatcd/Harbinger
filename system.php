<?php require_once("include/init.php"); ?>
<!DOCTYPE html>
<html>
	<head>
		<?php require_once("include/head.html"); ?>
	</head>
	<body>
		<?php require_once("include/nav.html"); ?>
		<div id="page-wrapper" class="container">

			<h2 class="page-header">Configure System</h2>

			<!-- Modal -->
			<div class="modal fade" id="Modal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
			  	<div class="modal-dialog">
			    	<div class="modal-content">
			      		<div class="modal-header">
			        		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			        		<h4 class="modal-title" id="ModalLabel"></h4>
			      		</div>
			      		<div class="modal-body">
			        		<div class="form-group">
								<label for="name">System Name :</label>
								<span id="oldName" style="color:#909090;"></span>
								<div>
									<input type="text" class="form-control" name="name" id="name" placeholder="System's name">
								</div>
							</div>
							<div class="form-group">
								<label for="description">Description :</label>
								<span id="oldDescription" style="color:#909090;"></span>
								<div>
									<input type="text" class="form-control" name="description" id="description" placeholder="Description">
								</div>
							</div>
			      		</div>
			      		<div class="modal-footer">
			      			<img id='waitAdd' src='img/ajax-loader.gif'></img>
			        		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			        		<button id="btnAdd" type="button" class="btn btn-primary">Add System</button>
			        		<button id="btnSave" type="button" class="btn btn-primary">Save changes</button>
			      		</div>
			    	</div>
			  	</div>
			</div>
			<!-- Modal -->

			<!-- /.panel-heading -->
			<div class="panel-body  col-sm-offset-2 col-sm-8">
			    <h4 class="page-header" style="margin-top:10px">Add System
					<button type="button" id="btnShowAdd" style="float:right;margin-top:-8px" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#Modal">Add System</button>
				</h4><br/>
				<div  class="table-responsive">
					<table class="table table-striped table-bordered table-hover" id="dataTables">
						<thead>
							<tr>
								<th>System</th>
								<th>Description</th>
								<th style="min-width:53px;width:53px;"></th>
							</tr>
						</thead>
						<tbody>
							<?
								$db = $mongo->logsearch;
								$collection = $db->system;
								$cursor = $collection->find();
								$i = 0;
								foreach ($cursor as $doc) {
							?>
							<tr>
								<td id="<?=$i?>_name" class="link" style="cursor:pointer;"><?=$doc['system']?></td>
								<td id="<?=$i?>_des"><?=$doc['description']?></td>
								<td>
									<button type="button" class="btnEdit btn btn-primary btn-xs" data-toggle="modal" data-target="#Modal" value="<?=$i?>">
									  	<span class="glyphicon glyphicon-edit"></span>
									</button>
									<button type="button" class="btnDelete btn btn-danger btn-xs" value="<?=$i?>">
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
        <script type="text/javascript">
        	$(document).ready(function() {
			    $('#waitAdd').hide();
			    $('#navSystem').addClass("active");

			    $('.link').click(function() {
			        window.location = "<?=$root_url.'/systemConfig.php?systemName='?>" + this.innerHTML;
			    });

			    $('#btnAdd').click(function() {
			        var system = $('#name').val();
			        var description = $('#description').val();
			        if (jQuery.trim(system).length > 0) {
			            $('#waitAdd').show();
						$.get("function/systemFunction.php", {
							method: "addSystem",
							system: system,
							description: description
						}, function(result){
							if(result == "success"){
								$('#Modal').modal('toggle');
								window.location.reload();
							} else
								alert(result);
							$('#waitAdd').hide();
						});
			        } else
			            alert("Plese fill system's name.");
			    });

			    $('#btnSave').click(function() {
			        var system = $('#name').val();
			        var description = $('#description').val();
			        var oldSystem = $('#oldName').html();
			        if (jQuery.trim(system).length > 0) {
			            $('#waitAdd').show();
			            $.get("function/systemFunction.php", {
							method: "editSystem",
							system: system,
							description: description,
							oldSystem: oldSystem
						}, function(result){
							if(result == "success"){
								$('#Modal').modal('toggle');
								window.location.reload();
							} else
								alert(result);
							$('#waitAdd').hide();
						});
			        } else
			            alert("Plese fill system's name.");
			    });

			    $('#btnShowAdd').click(function() {
			        $('#ModalLabel').html('Add System');
			        $('#btnAdd').show();
			        $('#btnSave').hide();
			        $('#name').val('');
			        $('#description').val('');
			        $('#oldName').html('');
			        $('#oldDescription').html('');
			    });

			    $('.btnEdit').click(function() {
			        var system = $('#' + this.value + '_name').html();
			        var description = $('#' + this.value + '_des').html();
			        $('#ModalLabel').html("Edit System '" + system + "'");
			        $('#btnAdd').hide();
			        $('#btnSave').show();
			        $('#name').val(system);
			        $('#description').val(description);
			        $('#oldName').html(system);
			        $('#oldDescription').html(description);
			    });

			    $('.btnDelete').click(function() {
			        var system = $('#' + this.value + '_name').html();
			        var r = confirm("Do you want to delete system '" + system + "'?");
			        if (r == true) {
			            $.get("function/systemFunction.php", {
							method: "deleteSystem",
							system: system
						}, function(result){
							if(result == "success"){
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