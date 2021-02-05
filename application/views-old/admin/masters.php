<script src="<?php echo base_url('assets/backend/js/jquery-3.3.1.min.js')?>"></script>
<?php $id = $this->session->userdata('user_id'); ?>
<div class="container-fluid">
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">All Masters</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="#">Dashboard</a></li>
                <li class="active">All Masters</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
    <!-- .row -->
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading">
                     All Masters
                    <div class="pull-right"><a href="#" data-perform="panel-collapse"><i class="ti-minus"></i></a></div>
                </div>
                <div class="panel-body">
                    <div id="infoMessage"><?php if($this->session->flashdata('message')) { echo $this->session->flashdata('message'); }?></div>
                    <div class="table-responsive">
                        <table id="allusers" class="display nowrap" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>Group</th>
                                    <th>Parent</th>
                                    <th>Childs</th>
                                    <th>Commission<br/>(in %)</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                              <?php $i = 1; foreach ($users as $user):?>
                              <?php $gid = $this->Common_model->findfield('users_groups', 'user_id', $user->id, 'group_id'); ?>
                                <tr>
                                    <td><?=$i++;?></td>
                                    <td><?=ucwords(strtolower(htmlspecialchars($user->full_name,ENT_QUOTES,'UTF-8')));?>
                                        <br/><?=$user->lock_betting == 'no' ? '<span class="text-success">Betting unlocked</span>' : '<span class="text-danger">Betting locked</span>';?><br/>
                                        <?php $bchips = $this->Common_model->findfield('user_chips','user_id',$user->id,'balanced_chips');?>
                                        <b><?=$bchips >= 0 ? '<span class="text-success">'.$bchips.'</span>' : '<span class="text-danger">'.$bchips.'</span>';?></b>
                                    </td>
                                    <td><?=$user->username;?></td>
                                    <td><?=ucwords(strtolower($this->Common_model->findfield('groups', 'id', $gid, 'name'))); ?></td>
                                    <td><?=$this->Common_model->findfield('users','id',$user->parent_id,'username');?></td>
                                    <td>
                                        <a href="<?=base_url('Admin/users?master_id='.$user->id);?>">View Users</a>
                                    </td>
                                    <td><?=$user->commission > 0 ? $user->commission.'%' : 'NONE';?></td>
                                    <td>
                                        <?php if($id == $user->id) {
                                            echo $user->active == 0 ? 'Block' : 'Active';
                                        } else {
                                            echo $user->active == 0 ? '<a href="'.base_url('Admin/activateUser?id='.$user->id.'&status='.$user->active).'">Block</a>' : '<a href="'.base_url('Admin/activateUser?id='.$user->id.'&status='.$user->active).'">Active</a>';
                                        } ?>
                                    </td>
                                    <td>
                                        <a href="javascript:void(0)" data-toggle="modal" data-target="#childModal" onclick="addChild('<?=$user->id;?>')"><i class="fa fa-user" data-toggle="tooltip" title="Add User"></i></a>&nbsp;&nbsp;
                                        <a href="javascript:void(0)" data-toggle="modal" data-target="#paymentModal" onclick="addMoney('<?=$user->id;?>')"><i class="fa fa-money" data-toggle="tooltip" title="Add/Withdraw Chips"></i></a>&nbsp;&nbsp;
                                        <a href="javascript:void(0)" data-toggle="modal" data-target="#userModal" onclick="userInfo('<?=$user->id;?>')"><i class="fa fa-eye" data-toggle="tooltip" title="User Info"></i></a>&nbsp;&nbsp;
                                        <a href="javascript:void(0)" data-toggle="modal" data-target="#passwordModal" onclick="updatePassword('<?=$user->id;?>')"><i class="fa fa-key" data-toggle="tooltip" title="Change Password"></i></a>&nbsp;&nbsp;
                                        <a href="<?=base_url('Admin/editUser?user_id='.$user->id)?>"><i class="ti-pencil-alt" data-toggle="tooltip" title="Edit"></i></a>&nbsp;&nbsp;
                                        <?php if($user->lock_betting == 'yes') { ?>
                                            <a href="#" onclick="lockBetting('<?= $user->id?>','no')"><i class="fa fa-unlock" data-toggle="tooltip" title="Unlock Betting"></i></a>&nbsp;&nbsp;
                                        <?php } else { ?>
                                            <a href="#" onclick="lockBetting('<?= $user->id?>','yes')"><i class="fa fa-lock" data-toggle="tooltip" title="Lock Betting"></i></a>&nbsp;&nbsp;
                                        <?php } ?>
                                        <a href="#" onclick="removeUser('<?= $user->id?>')"><i class="ti-close" data-toggle="tooltip" title="Delete"></i></a><br/>
                                        <a href="<?=base_url('Admin/userBetHistory?user_id='.$user->id)?>" data-toggle="tooltip" title="Bet History"><span class="label label-info rounded-0" style="border-radius: 0;">B</span></a>
                                        <a href="<?=base_url('Admin/userAccountStatement?user_id='.$user->id)?>" data-toggle="tooltip" title="Account Statement"><span class="label label-info rounded-0" style="border-radius: 0;">S</span></a>
                                        <a href="<?=base_url('Admin/userProfitLoss?user_id='.$user->id)?>" data-toggle="tooltip" title="Profit & Loss"><span class="label label-info rounded-0" style="border-radius: 0;">P-L</span></a>

                                    </td>
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
<div id="childModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add User</h4>
      </div>
      <form action="<?=base_url('Admin/addChild');?>" method="POST">
          <div class="modal-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="free_chips">Username</label>
                        <input type="text" name="identity" id="childIdentity" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="childName">Full Name</label>
                        <input type="text" name="full_name" id="childName" class="form-control" required>
                        <input type="hidden" name="parent_id" id="parent_id">
                        <input type="hidden" name="groups" value="5">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="childName">Commission</label>
                        <input type="number" min="0" name="commission" id="childCommission" class="form-control" required>
                    </div>
                </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-info">Submit</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
      </form>
    </div>
  </div>
</div>
<!-- Modal -->

<!-- Modal -->
<div id="passwordModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Change Password</h4>
      </div>
      <form action="<?=base_url('Admin/resetUserPassword');?>" method="POST">
          <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="free_chips">New Password</label>
                        <input type="password" name="new" id="new" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="chips">Confirm New Password</label>
                        <input type="password" name="new_confirm" id="new_confirm" class="form-control" required>
                        <input type="hidden" name="user_id" id="passwordUser_id">
                    </div>
                </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-info">Submit</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
      </form>
    </div>
  </div>
</div>
<!-- Modal -->
<div id="paymentModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add Chips</h4>
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
                        <label for="chips">Add Chips</label>
                        <input type="number" name="chips" id="chips" min="0" class="form-control" required>
                        <input type="hidden" name="user_id" id="user_id">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="transaction_date">Transaction Date</label>
                        <input type="text" name="transaction_date" id="transaction_date" class="form-control date" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group">
                    <div class="col-md-10">
                        <label for="description">Description</label>
                        <textarea class="form-control" name="description" id="description"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-2">
                        <center><button type="submit" class="btn btn-info">Add Chips</button></center>
                    </div>
                </div>
            </div>
          </div>
      </form>
      <hr/>
      <form method="post" action="<?=base_url('Admin/witdrawChips');?>">
          <div class="modal-body">
              <h4>Withdraw Chips</h4>
              <div class="row">
                  <div class="form-group">
                      <div class="col-md-10">
                        <label for="chips">Chips to be withdrawn</label>
                        <input type="number" name="chips" id="chips" class="form-control" required>
                        <input type="hidden" name="user_id" id="wuser_id" value="">
                      </div>
                      <div class="col-md-2" style="margin-top: 25px;">
                          <center><button type="submit" class="btn btn-danger">Withdraw Chips</button></center>
                      </div>
                  </div>

              </div>
          </div>
      </form>
    </div>
  </div>
</div>
<!-- Modal -->
<div id="userModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">User Details</h4>
      </div>
      <form action="<?=base_url('Admin/updateUserInfo');?>" method="POST">
          <div class="modal-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="free_chips">Max Stake</label>
                        <input type="number" name="max_stake" id="max_stake" class="form-control" min="0">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="chips">Going in Play Stake</label>
                        <input type="number" name="in_play_stake" id="in_play_stake" class="form-control" min="0">
                        <input type="hidden" name="user_id" id="user_id">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="transaction_date">Max Profit(Market)</label>
                        <input type="number" name="max_profit_market" id="max_profit_market" class="form-control" min="0">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group">
                    <div class="col-md-4">
                        <label for="description">Max Profit(Fancy)</label>
                        <input type="number" name="max_profit_fancy" id="max_profit_fancy" class="form-control" min="0">
                    </div>
                    <div class="col-md-4">
                        <label for="description">Bet Delay(ODD)</label>
                        <input type="number" name="bet_delay" id="bet_delay" class="form-control" min="0">
                    </div>
                    <div class="col-md-4">
                        <label for="description">Fancy Bet Delay</label>
                        <input type="number" name="fancy_bet_delay" id="fancy_bet_delay" class="form-control" min="0">
                    </div>
                </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-info">Update</button>
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
                url : "<?php echo site_url('Admin/deleteUser')?>/"+id,
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
        $("#wuser_id").val(id);
    }

    function updatePassword(id)
    {
        $("#passwordUser_id").val(id);
    }

    function addChild(pid)
    {
        $("#parent_id").val(pid);
    }

    function maxChips(chips) {
        var max = 0;
        max = parseInt(max);
        if(chips >= max) {
            //swal('Maximum chips can be transferred is '+max);
            //$("#chips").val("");
        }
    }

    function userInfo(id) {
        $.ajax({
            url : "<?php echo site_url('Admin/userInfo?user_id=')?>"+id,
            type: "POST",
            //dataType: "JSON",
            success: function(data)
            {
                console.log(data);
                var a = JSON.parse(data);
                $("#user_id").val(a.user_id);
                $("#max_stake").val(a.max_stake);
                $("#in_play_stake").val(a.in_play_stake);
                $("#max_profit_market").val(a.max_profit_market);
                $("#max_profit_fancy").val(a.max_profit_fancy);
                $("#bet_delay").val(a.bet_delay);
                $("#fancy_bet_delay").val(a.fancy_bet_delay);
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert("error");
            }
        });
    }

    function lockBetting(id,status)
    {
      swal({   
            title: "Are you sure?",   
            text: "You want to Lock/Unlock this user betting!",   
            type: "warning",   
            showCancelButton: true,   
            confirmButtonColor: "#DD6B55",   
            confirmButtonText: "Yes, Do it!",   
            closeOnConfirm: false 
        }, function(){   
             $.ajax({
                url : "<?php echo site_url('Admin/lockBetting?user_id=')?>"+id+"&status="+status,
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