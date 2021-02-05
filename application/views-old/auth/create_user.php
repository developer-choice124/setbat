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
                            <li class="active">Create User</li>
                        </ol>
                    </div>
                    <!-- /.breadcrumb -->
                </div>
                <!-- .row -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                 Add New User
                                <div class="pull-right"><a class="btn btn-info" href="<?=base_url('auth/allusers')?>"><i class="fa fa-user m-l-5"></i> All Users</a><a href="#" data-perform="panel-collapse"><i class="ti-minus"></i></a> <a href="#" data-perform="panel-dismiss"><i class="ti-close"></i></a> </div>
                            </div>
                            <div class="panel-body">
                                <div id="infoMessage"><?php echo $message;?></div>
                                <form class="form-material form-horizontal" action="<?=base_url('auth/create_user')?>" method="post" >
                                    <div class="form-group">
                                        <div class="col-md-4">
                                            <label>Select Group</label>
                                            <select class="form-control form-control-line" name="group_id" required>
                                                <?php $groups = $this->Common_model->get_data_by_query("select * from groups order by id ASC");?>
                                                    <option>Select User Group</option>
                                                    <?php foreach ($groups as $group) { ?>
                                                        <option value="<?=$group['id']?>"><?=$group['name']?></option>
                                                <?php  }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label>First Name</label>
                                            <input type="text" class="form-control form-control-line" name="first_name" placeholder="Enter users first name" autofocus required value="<?php echo set_value('first_name'); ?>">
                                        </div>
                                        <div class="col-md-4">
                                            <label>Last Name</label>
                                            <input type="text" class="form-control form-control-line" name="last_name" placeholder="Enter users last name" required value="<?php echo set_value('last_name'); ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-4">
                                            <label>Email e.g. "example@email.com"</label>
                                            <input type="email" name="email" class="form-control" placeholder="Email" required value="<?php echo set_value('email'); ?>">
                                        </div>
                                        <div class="col-md-4">
                                            <label>Password "min 5 character"</label>
                                            <input type="Password" name="password" class="form-control" minlength="5" placeholder="Password" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label>Confirm Password</label>
                                            <input  type="password" id="inputPasswordConfirm" name="password_confirm" class="form-control" placeholder="Confirm Password" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-4">
                                            <label class="">Gender*</label>
                                            <select class="form-control" name="member_gender" id= "gender" required="true">
                                                <option value="">Select Gender</option>
                                                <option value="male">Male</option>
                                                <option value="female">Female</option>
                                                <option value="other">Other</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="">Date of Birth* (YYYY-MM-DD)</label>
                                            <input type="text" name="member_dob" class="form-control" id="date" placeholder="yyyy/mm/dd" value="<?php echo set_value('member_dob'); ?>" required="true"/>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="">Mobile No*</label>
                                            <input type="text" id="phone" name="phone" class="form-control" placeholder="Phone no" value="<?php echo set_value('phone');?>" required/>
                                        </div>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-info waves-effect waves-light">Submit</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- .row -->
                
            </div>