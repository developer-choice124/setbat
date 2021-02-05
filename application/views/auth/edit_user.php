            <div class="container-fluid">
                <div class="row bg-title">
                    <!-- .page title -->
                    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                        <h4 class="page-title">Create User</h4>
                    </div>
                    <!-- /.page title -->
                    <!-- .breadcrumb -->
                    <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
                        <ol class="breadcrumb">
                            <li><a href="#">Dashboard</a></li>
                            <li class="active">Edit User</li>
                        </ol>
                    </div>
                    <!-- /.breadcrumb -->
                </div>
                <!-- .row -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                 Edit User
                                <div class="pull-right"><a class="btn btn-info" href="<?=base_url('Auth/allusers')?>"><i class="fa fa-user m-l-5"></i> All Users</a><a href="#" data-perform="panel-collapse"><i class="ti-minus"></i></a> <a href="#" data-perform="panel-dismiss"><i class="ti-close"></i></a> </div>
                            </div>
                            <div class="panel-body">
                                <div id="infoMessage"><?php echo $message;?></div>
                                <form class="form-material form-horizontal" action="<?=base_url('auth/edit_user/'.$user->id)?>" method="post" >
                                    <div class="form-group">
                                        <div class="col-md-4">
                                            <label>Select Group</label>
                                            <select class="form-control form-control-line" name="groups" required>
                                                <?php $groups = $this->Common_model->get_data_by_query("select * from groups order by id ASC"); ?>
                                                    <option>Select User Group</option>
                                                    <?php foreach ($groups as $group) { ?>
                                                        <option value="<?=$group['id']?>" <?php if($group['id']==$currentGroups[0]->id) echo 'selected';?>><?=$group['name']?></option>
                                                <?php  }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label>Full Name*</label>
                                            <input type="text" class="form-control form-control-line" name="full_name" placeholder="Enter users full name" required value="<?=$user->full_name;?>">
                                        </div>
                                        <div class="col-md-4">
                                            <label>Mobile No*</label>
                                            <input type="number" class="form-control form-control-line" name="phone" placeholder="Enter users phone no" required value="<?=$user->phone?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-6">
                                            <label>Email</label>
                                            <input type="email" name="email" class="form-control" placeholder="Email" required value="<?=$user->email?>" disabled="disabled">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="">Gender*</label>
                                            <select class="form-control" name="gender" id= "gender" required="true">
                                                <option value="">Select Gender</option>
                                                <option value="male" <?php if($user->gender == 'male') echo 'selected="selected"';?>>Male</option>
                                                <option value="female" <?php if($user->gender == 'female') echo 'selected="selected"';?>>Female</option>
                                                <option value="other" <?php if($user->gender == 'other') echo 'selected="selected"';?>>Other</option>
                                            </select>
                                        </div>
                                    </div>
                                    <?php echo form_hidden('id', $user->id);?>
                                    <?php echo form_hidden($csrf); ?>
                                    <button type="submit" class="btn btn-info waves-effect waves-light">Submit</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- .row -->
            </div>