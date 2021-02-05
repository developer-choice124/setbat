<script src="<?php echo base_url('assets/backend/js/jquery-3.3.1.min.js')?>"></script>
<div class="container-fluid">
    <div class="row bg-title">
        
    </div>
    <!-- .row -->
    <div class="row">
        <div class="col-sm-2">
            <ul class="list-group">
              <li class="list-group-item"><a href="#"><i class="fa fa-heart"></i> &nbsp;Favorite</a></li>
              <li class="list-group-item"><a href="<?=base_url('User/inPlay');?>"><i class="fa fa-play"></i> &nbsp;In Play</a></li>
              <li class="list-group-item"><a href="#"><i class="fa fa-bar-chart"></i> &nbsp;My Market</a></li>
              <li class="list-group-item"><a href="<?=base_url('User/matches');?>"><i class="fa fa-child"></i> &nbsp;Cricket</a></li>
              <li class="list-group-item"><a href="#"><i class="fa fa-futbol-o"></i> &nbsp;Soccer</a></li>
              <li class="list-group-item"><a href="#"><i class="fa fa-globe"> </i> &nbsp;Tennis</a></li>
            </ul>
        </div>
        <div class="col-sm-10">
            <div class="panel panel-info">
                <div class="panel-heading">
                     Change Password
                    <div class="pull-right"><a href="#" data-perform="panel-collapse"><i class="ti-minus"></i></a></div>
                </div>
                <div class="panel-body">
                    <div><?php if ($this->session->flashdata('message')) {
                          echo $this->session->flashdata('message');
                        } ?>
                    </div>
                    <form method="post" action="<?=base_url('User/updatePassword?user_id='.$this->session->userdata('user_id'));?>">
                      <div class="row">
                        <div class="col-md-8">
                          <div class="form-group">
                            <label for="old">Old Password</label>
                              <input type="password" name="old" id="old" class="form-control" placeholder="Please enter your old password">
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-8">
                          <div class="form-group">
                            <label for="new">New Password</label>
                              <input type="text" name="new" id="new" class="form-control" placeholder="Please enter your new password">
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-8">
                          <div class="form-group">
                            <label for="new">Confirm New Password</label>
                              <input type="text" name="new_confirm" id="new-confirm" class="form-control" placeholder="Please confirm your new password">
                          </div>
                        </div>
                      </div>
                      <button type="submit" class="btn btn-success">Update Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function getVideos(cid) {
       $.ajax({
            url : "<?php echo site_url('Admin/getVideos')?>/"+cid,
            type: "POST",
            //dataType: "JSON",
            success: function(data)
            {
               $("#videos").html(data);
               $('#vidtable').DataTable( {
                    responsive: true
                });
            },
              error: function (jqXHR, textStatus, errorThrown)
         {
            alert("error");
         }
        });
   }
</script>