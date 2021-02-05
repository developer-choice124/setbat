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
            <div class="panel panel-info">
                <div class="panel-heading">
                     Update Users
                    <div class="pull-right"><a class="btn btn-info" href="<?=base_url('SuperAdmin/allUsers')?>"><i class="fa fa-user m-l-5"></i>All Users</a><a href="#" data-perform="panel-collapse"><i class="ti-minus"></i></a></div>
                </div>
                <div class="panel-body">
                    <div id="infoMessage"><?php if($this->session->flashdata('message')) { echo $this->session->flashdata('message'); }?></div>
                    <form action="<?=base_url('SuperAdmin/updateUser/'.$user->id);?>" method="post" class="form-horizontal">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control form-control-line" placeholder="please enter email address" required value="<?=$user->email;?>">
                                </div>
                                <div class="col-md-4">
                                    <label>Full Name</label>
                                    <input type="text" name="full_name" class="form-control form-control-line" placeholder="please enter user full name" value="<?=$user->full_name;?>">
                                </div>
                                <div class="col-md-4">
                                    <label>Group</label>
                                    <select class="form-control form-control-line" name="groups" id="groups">
                                        <?php foreach($groups as $g):?>
                                            <option value="<?=$g['id'];?>" <?=$g['id']==$user->group_id ? 'selected' : '' ;?>><?=ucwords($g['name']);?></option>
                                        <?php endforeach;?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-3">
                                    <label>Phone</label>
                                    <input type="text" name="phone" class="form-control form-control-line" placeholder="please enter user phone number" required value="<?=$user->phone;?>">
                                </div>
                                <div class="col-md-3">
                                    <label>Gender</label>
                                    <select class="form-control form-control-line" name="gender">
                                        <option value="male" <?=$user->gender == 'male' ? 'selected' : '' ;?>>Male</option>
                                        <option value="female" <?=$user->gender == 'female' ? 'selected' : '' ;?>>Female</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>Partnership(in %)</label>
                                    <input type="number" name="commission" min="0" max="100" class="form-control form-control-line" placeholder="Commission in (%)" value="<?=$user->commission;?>">
                                </div>
                                <div class="col-md-3">
                                    <?php echo form_hidden('id', $user->id);?>
                                    <?php echo form_hidden($csrf); ?>
                                    <center><button type="submit" class="btn btn-info">submit</button></center>
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