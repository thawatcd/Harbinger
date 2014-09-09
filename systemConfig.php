<?php require_once("include/init.php"); ?>
<!DOCTYPE html>
<html>
	<head>
		<?php require_once("include/head.html"); ?>
	</head>
	<body>
		<?php require_once("include/nav.html"); ?>
		<div id="page-wrapper" class="container">

			<h2 class="page-header">Configure System '<span id="systemName"><? echo $_GET['systemName']; ?></span>'</h2>

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
								<label for="name">Node Name :</label>
								<span id="oldName" style="color:#909090;"></span>
								<div>
									<input type="text" class="form-control" name="name" id="name" placeholder="Node's name">
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
			        		<button id="btnAdd" type="button" class="btn btn-primary">Add Node</button>
			        		<button id="btnSave" type="button" class="btn btn-primary">Save changes</button>
			      		</div>
			    	</div>
			  	</div>
			</div>
			<!-- Modal -->

			<!-- /.panel-heading -->
			<div class="panel-body col-sm-offset-2 col-sm-8">
				<h4 class="page-header" style="margin-top:10px">Add Node
					<button type="button" id="btnShowAdd" style="float:right;margin-top:-8px" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#Modal">Add Node</button>
				</h4><br/>
				<div  class="table-responsive">
					<table class="table table-striped table-bordered table-hover" id="dataTables">
						<thead>
							<tr>
								<th>Node</th>
								<th>Description</th>
								<th style="min-width:53px;width:53px;"></th>
							</tr>
						</thead>
						<tbody>
							<?
								$db = $mongo->logsearch;
								$collection = $db->system_config;
								$document = array( "system" => $_GET['systemName'] );
								$cursor = $collection->find($document);
								$i = 0;
								foreach ($cursor as $doc) {
							?>
							<tr>
								<td id="<?=$i?>_name"><?=$doc['node']?></td>
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
			    

			    $('#btnAdd').click(function() {
			        var system = $('#systemName').html();
			        var node = $('#name').val();
			        var description = $('#description').val();
			        if (jQuery.trim(node).length > 0) {
			            $('#waitAdd').show();
			            $.get("function/systemFunction.php", {
							method: "addSystemConfig",
							system: system,
							node: node,
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
			            alert("Plese fill node's name.");
			    });

			    $('#btnSave').click(function() {
			        var system = $('#systemName').html();
			        var node = $('#name').val();
			        var description = $('#description').val();
			        var oldNode = $('#oldName').html();
			        if (jQuery.trim(node).length > 0) {
			            $('#waitAdd').show();
			            $.get("function/systemFunction.php", {
							method: "editSystemConfig",
							system: system,
							node: node,
							oldNode: oldNode,
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
			            alert("Plese fill node's name.");
			    });

			    $('#btnShowAdd').click(function() {
			        $('#ModalLabel').html('Add Node');
			        $('#btnAdd').show();
			        $('#btnSave').hide();
			        $('#name').val('');
			        $('#description').val('');
			        $('#oldName').html('');
			        $('#oldDescription').html('');
			    });

			    $('.btnEdit').click(function() {
			    	var system = $('#systemName').html();
			        var node = $('#' + this.value + '_name').html();
			        var description = $('#' + this.value + '_des').html();
			        $('#ModalLabel').html("Edit Node '" + node + "'");
			        $('#btnAdd').hide();
			        $('#btnSave').show();
			        $('#name').val(node);
			        $('#description').val(description);
			        $('#oldName').html(node);
			        $('#oldDescription').html(description);
			    });

			    $('.btnDelete').click(function() {
			        var node = $('#' + this.value + '_name').html();
			        var system = $('#systemName').html();
			        var r = confirm("Do you want to delete node '" + node + "'?");
			        if (r == true) {
			           $.get("function/systemFunction.php", {
							method: "deleteSystemConfig",
							system: system,
							node: node
						},function(result){
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