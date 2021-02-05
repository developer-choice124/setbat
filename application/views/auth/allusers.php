<script src="<?php echo base_url('assets/backend/js/jquery-3.3.1.min.js')?>"></script>
<?php $id = $this->session->userdata('user_id'); ?>
<div class="container-fluid">
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">All Users</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="#">Dashboard</a></li>
                <li class="active">All Users</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
    <!-- .row -->
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading">
                     All Users
                    <div class="pull-right"><!--<a class="btn btn-info" href="<?=base_url('auth/create_user')?>"><i class="fa fa-user m-l-5"></i> Add User</a>--><a href="#" data-perform="panel-collapse"><i class="ti-minus"></i></a> <a href="#" data-perform="panel-dismiss"><i class="ti-close"></i></a> </div>
                </div>
                <div class="panel-body">
                    <div id="infoMessage"><?php if($this->session->flashdata('message')) { echo $message; }?></div>
                    <form action="<?=base_url('Auth/add_user');?>" method="post" class="form-horizontal">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="email" name="email" class="form-control form-control-line" placeholder="please enter email address" required>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="full_name" class="form-control form-control-line" placeholder="please enter user full name">
                                </div>
                                <div class="col-md-4">
                                    <select class="form-control form-control-line" name="groups" id="groups">
                                        <?php foreach($groups as $g):?>
                                            <option value="<?=$g['id'];?>"><?=ucwords($g['name']);?></option>
                                        <?php endforeach;?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="text" name="phone" class="form-control form-control-line" placeholder="please enter user phone number" required>
                                </div>
                                <div class="col-md-4">
                                    <select class="form-control form-control-line" name="gender">
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <center><button type="submit" class="btn btn-info">submit</button></center>
                                </div>
                            </div>
                        </div>
                    </form>
                    <hr>
                    <div class="table-responsive">
                        <table id="allusers" class="display nowrap" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Gender</th>
                                    <th>Group</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                              <?php $i = 1; foreach ($users as $user):?>
                              <?php $gid = $this->Common_model->findfield('users_groups', 'user_id', $user->id, 'group_id'); ?>
                                <tr>
                                    <td><?=$i++;?></td>
                                    <td><?=ucwords(strtolower(htmlspecialchars($user->full_name,ENT_QUOTES,'UTF-8')));?></td>
                                    <td><?=htmlspecialchars($user->email,ENT_QUOTES,'UTF-8');?></td>
                                    <td><?=$user->phone;?></td>
                                    <td><?=ucwords(strtolower(htmlspecialchars($user->gender,ENT_QUOTES,'UTF-8')));?></td>
                                    <td><?=ucwords(strtolower($this->Common_model->findfield('groups', 'id', $gid, 'name'))); ?></td>
                                    <td>
                                        <?php if($id == $user->id) {
                                            echo $user->active == 0 ? 'Block' : 'Active';
                                        } else {
                                            echo $user->active == 0 ? '<a href="'.base_url('Auth/activateUser?id='.$user->id.'&status='.$user->active).'">Block</a>' : '<a href="'.base_url('Auth/activateUser?id='.$user->id.'&status='.$user->active).'">Active</a>';
                                        } ?>
                                    </td>
                                    <td><?php if($user->group_name == 'admin') {} else { ?><a href="javascript:void(0)" data-toggle="modal" data-target="#paymentModal" onclick="addMoney('<?=$user->id;?>')"><i class="fa fa-money" data-toggle="tooltip" title="Add Money"></i></a>&nbsp;&nbsp;<?php } ?><a href="<?=base_url('auth/edit_user/'.$user->id)?>"><i class="ti-pencil-alt" data-toggle="tooltip" title="Edit"></i></a>&nbsp;&nbsp;<a href="#" onclick="removeUser('<?= $user->id?>')"><i class="ti-close" data-toggle="tooltip" title="Delete"></i></a></td>
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
</div>
<!-- Modal -->
<div id="paymentModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Transaction Details</h4>
      </div>
      <form action="<?=base_url('Admin/addMoney');?>" method="POST">
          <div class="modal-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="free_chips">Free Chips</label>
                        <select class="form-control" name="free_chips" id="free_chips">
                            <option value="no">No</option>
                            <option value="yes">Yes</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="amount">Amount Received</label>
                        <input type="number" name="amount" id="amount" class="form-control" min="0" value="0">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="chips">No of Chips</label>
                        <input type="number" name="chips" id="chips" class="form-control">
                        <input type="hidden" name="user_id" id="user_id">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="transaction_date">Transaction Date</label>
                        <input type="text" name="transaction_date" id="transaction_date" class="form-control date" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="expiry_date">Expiry Date</label>
                        <input type="text" name="expiry_date" id="expiry_date" class="form-control date" placeholder="yyyy-mm-dd" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group">
                    <div class="col-md-12">
                        <label for="description">Description</label>
                        <textarea class="form-control" name="description" id="description"></textarea>
                    </div>
                </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-info">Add Money</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
      </form>
    </div>
  </div>
</div>
<script>
    
    $(function(){
      window.prettyPrint && prettyPrint();
      $('.date').datepicker({
          format: 'yyyy-mm-dd',
          autoclose: true,
          todayHighlight: true
      });
      $('#transaction_date').datepicker('setDate', new Date());
    });
    function removeUser(id)
    {
      swal({   
            title: "Are you sure?",   
            text: "You will not be able to recover this user!",   
            type: "warning",   
            showCancelButton: true,   
            confirmButtonColor: "#DD6B55",   
            confirmButtonText: "Yes, delete it!",   
            closeOnConfirm: false 
        }, function(){   
             $.ajax({
                url : "<?php echo site_url('auth/delete_user')?>/"+id,
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
   function addMoney(id)
   {
    $("#user_id").val(id);
   }
</script>