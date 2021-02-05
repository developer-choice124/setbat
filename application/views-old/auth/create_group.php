<div class="container-fluid">
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">Manage Group</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="#">Dashboard</a></li>
                <li class="active">Manage Group</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
    <!-- .row -->
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading">
                     Manage Group
                    <div class="pull-right"><a class="btn btn-info" href="<?=base_url('auth/allusers')?>"><i class="fa fa-user m-l-5"></i> All Users</a><a href="#" data-perform="panel-collapse"><i class="ti-minus"></i></a> <a href="#" data-perform="panel-dismiss"><i class="ti-close"></i></a> </div>
                </div>
                <div class="panel-body">
                    <div id="infoMessage"><?php echo $message;?></div>
                    <p></p>
                    <form class="form-material form-inline" action="<?=base_url('auth/create_group')?>" method="post" >
                        <div class="form-group" style="width: 80%">
                        	<label>Group Name</label>
                            <input type="text" class="form-control form-control-line" name="group_name" placeholder="Enter group name" autofocus required value="<?php echo set_value('group_name'); ?>" style="min-width: 35%">
                            <label>Description</label>
                            <textarea class="form-control form-control-line" name="description" required value="<?php echo set_value('description'); ?>" style="min-width: 35%"></textarea>
                        </div>
                        <button type="submit" class="btn btn-info">Submit</button>
                    </form>
                </div>
                <hr>
                <div class="panel-body">
	            	<div class="table-responsive">
			            <table id="allusers" class="display nowrap" cellspacing="0" width="100%">
			                <thead>
			                    <tr>
			                        <th>#</th>
			                        <th>Group Name</th>
			                        <th>Description</th>
			                        <th>Action</th>
			                    </tr>
			                </thead>
			                <tbody>
			                  <?php $i = 1;
			                  	$groups = $this->Common_model->get_data_by_query("select * from groups order by id ASC");
			                  	foreach ($groups as $group):?>
			                  	<tr>
			                        <td><?=$i++;?></td>
			                        <td><?=ucwords(strtolower(htmlspecialchars($group['name'],ENT_QUOTES,'UTF-8')));?></td>
			                        <td><?=htmlspecialchars($group['description'],ENT_QUOTES,'UTF-8');?></td>
			                        <td><a href="#" data-toggle="modal" data-target="#groupmodal" onclick="groupDetails('<?= $group['id']?>','<?= $group['name']?>','<?= $group['description']?>')"><i class="ti-pencil-alt" data-toggle="tooltip" title="Edit"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" onclick="removeGroup('<?= $group['id']?>')"><i class="ti-close" data-toggle="tooltip" title="Delete"></i></a></td>
			                    </tr>
			                  <?php endforeach;?>  
			                </tbody>
			            </table>
			        </div>
	            </div>
            </div>
        </div>
    </div>
    <!-- .row -->
    <div id="groupmodal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">Edit Group</h4>
                </div>
                <form method="post" action="<?=base_url('auth/edit_group')?>">
                	<div class="modal-body">
                    	<div class="form-group">
                            <label for="recipient-name" class="control-label">Group Name:</label>
                            <input type="text" class="form-control" id="group_name" name="group_name">
                        </div>
                        <div class="form-group">
                            <label for="message-text" class="control-label">Description:</label>
                            <textarea class="form-control" id="group_description" name="group_description"></textarea>
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="id" id="group_id">
                        </div>
                    
                	</div>
	                <div class="modal-footer">
	                    <button type="close" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
	                    <button type="submit" class="btn btn-info waves-effect waves-light">Save changes</button>
	                </div>
	            </form>
            </div>
        </div>
    </div>
</div>
<script>
	function groupDetails(id, name, description){
		$('#group_id').val(id);
		$('#group_name').val(name);
		$('#group_description').val(description);
	}
	function removeGroup(id)
{
      swal({   
            title: "Are you sure?",   
            text: "You will not be able to recover this group!",   
            type: "warning",   
            showCancelButton: true,   
            confirmButtonColor: "#DD6B55",   
            confirmButtonText: "Yes, delete it!",   
            closeOnConfirm: false 
        }, function(){   
             $.ajax({
	            url : "<?php echo site_url('auth/delete_group')?>/"+id,
	            type: "POST",
	            //dataType: "JSON",
	            success: function(data)
	            {
	               location.reload();
	            },
	              error: function (jqXHR, textStatus, errorThrown)
	         {
	            alert("error");
	         }
	        });
        });

   }

</script>