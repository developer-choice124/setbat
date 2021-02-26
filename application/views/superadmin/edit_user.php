<script src="<?php echo base_url('assets/backend/js/jquery-3.3.1.min.js')?>"></script>
<?php $id = $this->session->userdata('user_id'); ?>
<div class="container-fluid">
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">Update User</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="#">Dashboard</a></li>
                <li class="active">Update User</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
    <!-- .row -->
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-darkblue">
                <div class="panel-heading">
                     Update Users
                    <div class="pull-right"><a class="btn btn-darkblue" href="<?=base_url('SuperAdmin/'.($user->group_name=='user'?'users': ($user->group_name == 'master'?'masters':'supermasters')));?>"><i class="fa fa-user m-l-5"></i>All Users</a><a href="#" data-perform="panel-collapse"><i class="ti-minus"></i></a></div>
                </div>
                <div class="panel-body">
                    <div id="infoMessage"><?php if($this->session->flashdata('message')) { echo $this->session->flashdata('message'); }?></div>
                    <form action="<?=base_url('SuperAdmin/updateUser/'.$user->id);?>" method="post" class="form-horizontal">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label>Full Name</label>
                                    <input type="text" name="full_name" class="form-control form-control-line" placeholder="please enter user full name" required value="<?=$user->full_name;?>">
                                </div>
                                <div class="col-md-4">
                                    <label>Partnership(in %)</label>
                                    <input type="number" name="commission" min="0" max="100" class="form-control form-control-line" placeholder="Commission in (%)" value="<?=$user->commission;?>">
                                </div>
                                <div class="col-md-4">
                                    <?php echo form_hidden('id', $user->id);?>
                                    <?php echo form_hidden($csrf); ?>
                                    <center><button type="submit" class="btn btn-darkblue">submit</button></center>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- .row -->
</div>