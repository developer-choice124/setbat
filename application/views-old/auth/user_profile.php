<style type="text/css">
    .hovereffect {
width:100%;
height:100%;
float:left;
overflow:hidden;
position:relative;
text-align:center;
cursor:default;
}

.hovereffect .overlay {
width:100%;
height:100%;
position:absolute;
overflow:hidden;
top:0;
left:0;
opacity:0;
background-color:rgba(0,0,0,0.5);
-webkit-transition:all .4s ease-in-out;
transition:all .4s ease-in-out
}

.hovereffect img {
display:block;
position:relative;
-webkit-transition:all .4s linear;
transition:all .4s linear;
margin-left: auto;
margin-right: auto;
vertical-align: middle;
}

.hovereffect h2 {
text-transform:uppercase;
color:#fff;
text-align:center;
position:relative;
font-size:17px;
background:rgba(0,0,0,0.6);
-webkit-transform:translatey(-100px);
-ms-transform:translatey(-100px);
transform:translatey(-100px);
-webkit-transition:all .2s ease-in-out;
transition:all .2s ease-in-out;
padding:10px;
}

.hovereffect a.info {
text-decoration:none;
display:inline-block;
text-transform:uppercase;
color:#fff;
border:1px solid #fff;
background-color:transparent;
opacity:0;
filter:alpha(opacity=0);
-webkit-transition:all .2s ease-in-out;
transition:all .2s ease-in-out;
margin:50px 0 0;
padding:7px 14px;
}

.hovereffect a.info:hover {
box-shadow:0 0 5px #fff;
}

.hovereffect:hover img {
-ms-transform:scale(1.2);
-webkit-transform:scale(1.2);
transform:scale(1.2);
}

.hovereffect:hover .overlay {
opacity:1;
filter:alpha(opacity=100);
}

.hovereffect:hover h2,.hovereffect:hover a.info {
opacity:1;
filter:alpha(opacity=100);
-ms-transform:translatey(0);
-webkit-transform:translatey(0);
transform:translatey(0);
}

.hovereffect:hover a.info {
-webkit-transition-delay:.2s;
transition-delay:.2s;
}
</style>
<div class="container-fluid">
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">Manage Profile</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="#">Dashboard</a></li>
                <li class="active">Manage Profile</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
    <!-- .row -->
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading">
                     Update Profile
                    <div class="pull-right"><a href="#" data-perform="panel-collapse"><i class="ti-minus"></i></a> <a href="#" data-perform="panel-dismiss"><i class="ti-close"></i></a> </div>
                </div>
                <div class="panel-body">
                   <div class="row">
                      
                        <div class="col-md-8 col-xs-12">
                            <div class="white-box">
                                <?php echo $this->session->flashdata('item'); ?>
                                <div id="infoMessage"><?php echo $message;?></div>
                                <ul class="nav nav-tabs tabs customtab">
                                    <li class="tab active">
                                        <a href="#up_password" data-toggle="tab" aria-expanded="false"> <span class="visible-xs"><i class="fa fa-user"></i></span> <span class="hidden-xs">Change Password</span> </a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane" id="profile">
                                        <div class="row">
                                            <div class="col-md-3 col-xs-6 b-r"> <strong>Full Name</strong>
                                                <br>
                                                <p class="text-muted"><?=ucwords(strtolower($userdetail->first_name.' '.$userdetail->last_name));?></p>
                                            </div>
                                            <div class="col-md-3 col-xs-6 b-r"> <strong>Mobile</strong>
                                                <br>
                                                <p class="text-muted"><?=$socialdetail->mobile?></p>
                                            </div>
                                            <div class="col-md-3 col-xs-6"> <strong>Email</strong>
                                                <br>
                                                <p class="text-muted"><?=$userdetail->email;?></p>
                                            </div>
                                        </div>
                                        <hr>
                                        <p class="m-t-30"><?=$userdetail->bio?></p>
                                        <!--<h4 class="font-bold m-t-30">Skill Set</h4>
                                        <hr>
                                        <h5>Wordpress <span class="pull-right">80%</span></h5>
                                        <div class="progress">
                                            <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100" style="width:80%;"> <span class="sr-only">50% Complete</span> </div>
                                        </div>
                                        <h5>HTML 5 <span class="pull-right">90%</span></h5>
                                        <div class="progress">
                                            <div class="progress-bar progress-bar-custom" role="progressbar" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100" style="width:90%;"> <span class="sr-only">50% Complete</span> </div>
                                        </div>
                                        <h5>jQuery <span class="pull-right">50%</span></h5>
                                        <div class="progress">
                                            <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width:50%;"> <span class="sr-only">50% Complete</span> </div>
                                        </div>
                                        <h5>Photoshop <span class="pull-right">70%</span></h5>
                                        <div class="progress">
                                            <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width:70%;"> <span class="sr-only">50% Complete</span> </div>
                                        </div>-->
                                    </div>
                                    <div class="tab-pane" id="up_basic">
                                        <form class="form-horizontal form-material" action="<?=base_url('auth/user_profile')?>" method="post">
                                            <div class="form-group">
                                                <div class="col-md-6">
                                                    <label>First Name</label>
                                                    <input type="text" placeholder="Your First Name" class="form-control form-control-line" value="<?=$userdetail->first_name?>" name="first_name">
                                                </div>
                                                <div class="col-md-6">
                                                    <label>Last Name</label>
                                                    <input type="text" placeholder="Your Last Name" class="form-control form-control-line" value="<?=$userdetail->last_name?>" name="last_name">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-md-5">
                                                    <label>Gender</label>
                                                    <div class="radio radio-info">
                                                        <div class="col-md-4">
                                                           <input type="radio" name="gender" id="gender" value="male" <?php if($userdetail->gender == 'male') echo 'checked';?>>
                                                            <label for="gender"> Male </label> 
                                                        </div>
                                                        <div class="col-md-4">
                                                            <input type="radio" name="gender" id="gender" value="female" <?php if($userdetail->gender == 'female') echo 'checked';?>>
                                                            <label for="gender"> Female </label>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <input type="radio" name="gender" id="gender" value="other" <?php if($userdetail->gender == 'other') echo 'checked';?>>
                                                            <label for="gender"> Other </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <label>Married?</label>
                                                    <select class="form-control form-control-line" name="married">
                                                        <option value="0" <?php if($userdetail->married == 0) echo 'selected';?>>No</option>
                                                        <option value="1" <?php if($userdetail->married == 1) echo 'selected';?>>Yes</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <label>DOB</label>
                                                    <select class="form-control form-control-line" name="d_year">
                                                        <?php $sel = "selected"; for($i = 2000; $i >=1945; $i--){
                                                            echo '<option value="'.$i.'"'; if($userdetail->dob == $i) echo $sel; echo '>'.$i.'</option>';
                                                        } ?>
                                                        
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <label></label>
                                                    <select class="form-control form-control-line" name="d_month">
                                                        <?php $mnth = $userdetail->dob; 
                                                      $mth = substr($mnth,5,3) ;?>
                                                        <option value="01" <?php if($mth == 1){
                                                            echo 'selected';} ?>
                                                        >January</option>
                                                        <option value="02" <?php if($mth == 2){
                                                            echo 'selected';} ?>>February</option>
                                                        <option value="03" <?php if($mth == 3){
                                                            echo 'selected';} ?>>March</option>
                                                        <option value="04" <?php if($mth == 4){
                                                            echo 'selected';} ?>>April</option>
                                                        <option value="05" <?php if($mth == 5){
                                                            echo 'selected';} ?>>May</option>
                                                        <option value="06" <?php if($mth == 6){
                                                            echo 'selected';} ?>>June</option>
                                                        <option value="07" <?php if($mth == 7){
                                                            echo 'selected';} ?>>July</option>
                                                        <option value="08" <?php if($mth == 8){
                                                            echo 'selected';} ?>>August</option>
                                                        <option value="09" <?php if($mth == 9){
                                                            echo 'selected' ;}?>>September</option>
                                                        <option value="10" <?php if($mth == 10){
                                                            echo 'selected' ;}?>>October</option>
                                                        <option value="11" <?php if($mth == 11){
                                                            echo 'selected' ;}?>>November</option>
                                                        <option value="12" <?php if($mth == 12){
                                                            echo 'selected';} ?>>December</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-1">
                                                    <label></label>
                                                    <select class="form-control form-control-line" name="d_date">
                                                        <?php $dates = $userdetail->dob; 
                                                        $dth = substr($dates,8);?>
                                                        <?php for($i = 01; $i <=31; $i++){ ?>
                                                        <option value="<?=$i?>" <?php if($i == $dth) echo 'selected';?>><?=$i?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="example-email" class="col-md-12">About Yourself</label>
                                                <div class="col-md-12">
                                                    <textarea class="form-control form-control-line" name="bio" placeholder="Describe Yourself"><?=$userdetail->bio?></textarea>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-sm-12">
                                                    <button class="btn btn-success" type="submit">Update Profile</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="tab-pane" id="up_contact">
                                        <form class="form-horizontal form-material" action="<?=base_url('auth/update_contact')?>" method="post">
                                            <div class="form-group">
                                                <div class="col-md-6">
                                                    <label for="example-email">Email</label>
                                                    <p><?=$userdetail->email?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <label>Website</label>
                                                    <input type="text" name="website" class="form-control form-control-line" value="<?=$socialdetail->website?>" placeholder="please enter your website url">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-md-6">
                                                    <label>Mobile No.</label>
                                                    <input type="text" placeholder="Please enter your mobile no" class="form-control form-control-line" value="<?=$socialdetail->mobile?>" name="mobile">
                                                </div>
                                                <div class="col-md-6">
                                                    <label>Phone No</label>
                                                    <input type="text" placeholder="Please enter your phone no" class="form-control form-control-line" value="<?=$socialdetail->phone?>" name="phone">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-md-4">
                                                    <label>State</label>
                                                    <select class="form-control form-control-line select2" id="states" name="state_id" >
                                                        <option>Select your state</option>
                                                        <?php
                                                        $states = $this->Common_model->get_data_by_query("select * from states order by state_id");
                                                        foreach ($states as $key => $state) { ?>
                                                            <option value="<?=$state['state_id']?>" <?php if($socialdetail->state_id == $state['state_id']) echo 'selected';?>><?=$state['state_name']?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label>City</label>
                                                    <select class="form-control form-control-line select2" id="cities" name="city_id">
                                                        <option>Select your city</option>
                                                        <?php if(!empty($socialdetail->city_id)){?>
                                                        <option value="<?=$socialdetail->city_id?>" selected><?=$this->Common_model->findfield('cities', 'city_id', $socialdetail->city_id, 'city_name')?></option>
                                                     <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label>Pincode</label>
                                                    <!--<select class="form-control form-control-line select2"></select>-->
                                                    <input type="number" name="pincode" min="000000" max="999999" class="form-control form-control-line" value="<?=$socialdetail->pincode?>">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <label>Address</label>
                                                    <textarea class="form-control form-control-line" name="address" placeholder="Please enter your address here"><?=$socialdetail->address?></textarea>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-sm-12">
                                                    <button class="btn btn-success" type="submit"> Update </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="tab-pane" id="up_social">
                                        <form class="form-horizontal form-material" action="<?=base_url('auth/update_social')?>" method="post">
                                            <div class="form-group">
                                                <label for="" class="col-sm-2 btn btn-facebook waves-effect waves-light"><i class="ti-facebook"></i> Facebook</label>
                                                <div class="col-sm-10">
                                                    <input type="text" name="facebook" class="form-control" value="<?=strtolower($socialdetail->facebook);?>" placeholder="Please enter facebook page url">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="" class="col-sm-2 btn btn-twitter waves-effect waves-light"><i class="ti-twitter"></i> Twitter</label>
                                                <div class="col-sm-10">
                                                    <input type="text" name="twitter" class="form-control" value="<?=strtolower($socialdetail->twitter);?>" placeholder="Please enter twitter username">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="" class="col-sm-2 btn btn-default waves-effect waves-light" style="background-color: #8a3ab9 !important; color:#ffffff;"><i class="ti-instagram"></i> Instagram</label>
                                                <div class="col-sm-10">
                                                    <input type="text" name="instagram" class="form-control" value="<?=strtolower($socialdetail->instagram);?>" placeholder="Please enter instagram username">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="" class="col-sm-2 btn btn-linkedin waves-effect waves-light"><i class="ti-linkedin"></i> Linkedin</label>
                                                <div class="col-sm-10">
                                                    <input type="text" name="linkedin" class="form-control" value="<?=strtolower($socialdetail->linkedin);?>" placeholder="Please enter linkedin username">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="" class="col-sm-2 btn btn-googleplus waves-effect waves-light"><i class="ti-google"></i> Google+</label>
                                                <div class="col-sm-10">
                                                    <input type="text" name="gplus" class="form-control" value="<?=strtolower($socialdetail->gplus);?>" placeholder="Please enter google-plus page url">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="" class="col-sm-2 btn btn-youtube waves-effect waves-light"><i class="ti-youtube"></i> Youtube</label>
                                                <div class="col-sm-10">
                                                    <input type="text" name="youtube" class="form-control" value="<?=strtolower($socialdetail->youtube);?>" placeholder="Please enter youtube page url">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="" class="col-sm-2 btn btn-inverse waves-effect waves-light"><i class="fa fa-wikipedia-w"></i> Wikipedia</label>
                                                <div class="col-sm-10">
                                                    <input type="text" name="wikipedia" class="form-control" value="<?=strtolower($socialdetail->wikipedia);?>" placeholder="Please enter wikipedia page url">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-sm-offset-2 col-sm-10">
                                                    <button type="submit" class="btn btn-success waves-effect waves-light m-t-10">&nbsp;Update&nbsp;</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="tab-pane" id="up_education">
                                        <form class="form-horizontal form-material" action="<?=base_url('auth/update_education')?>" method="post">
                                            <div class="form-group">
                                                <label for="" class="col-sm-2">HSC (10th)</label>
                                                <div class="col-sm-10">
                                                    <input type="text" name="hsc_board" class="form-control" placeholder="Please enter your school name and city">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="" class="col-sm-2">SSC (12th)</label>
                                                <div class="col-sm-10">
                                                    <input type="text" name="ssc_board" class="form-control" placeholder="Please enter your school name and city">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="" class="col-sm-2">Graduation</label>
                                                <div class="col-sm-10">
                                                    <input type="text" name="bachelor" class="form-control" placeholder="Please enter your college name and city">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="" class="col-sm-2">Masters</label>
                                                <div class="col-sm-10">
                                                    <input type="text" name="master" class="form-control" placeholder="Please enter your college name and city">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="" class="col-sm-2">PHD</label>
                                                <div class="col-sm-10">
                                                    <input type="text" name="bachelor" class="form-control" placeholder="Please enter your college name and city">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-sm-offset-2 col-sm-10">
                                                    <button type="submit" class="btn btn-success waves-effect waves-light m-t-10">&nbsp;Update&nbsp;</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="tab-pane active" id="up_password">
                                        <form class="form-horizontal form-material" action="<?=base_url('auth/change_password')?>" method="post">
                                            <div class="form-group">
                                                <label for="example-email" class="col-md-12">Old Password</label>
                                                <div class="col-md-12">
                                                    <input type="password" placeholder="Enter your old password" class="form-control form-control-line" name="old">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="example-email" class="col-md-12">New Password</label>
                                                <div class="col-md-12">
                                                    <input type="password" placeholder="Enter your new password" class="form-control form-control-line" name="new">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="example-email" class="col-md-12">Confirm New Password</label>
                                                <div class="col-md-12">
                                                    <input type="password" placeholder="Confirm your new password" class="form-control form-control-line" name="new_confirm">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-sm-12">
                                                    <button class="btn btn-success" type="submit">Update Password</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
    </div>
    <!-- .row -->
</div>
<!-- Modal -->
<div id="modal1" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Change profile image</h4>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <div class="white-box">
                        <h3 class="box-title">File Upload4</h3>
                        <label for="input-file-now-custom-3">You can combine options</label>
                        <input type="file" id="input-file-now-custom-3" class="dropify" data-height="250" data-default-file="<?php echo base_url('assets/plugins/bower_components/dropify/src/images/test-image-2.jpg')?>" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="close" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-info waves-effect waves-light">Save changes</button>
            </div>
        </div>
    </div>
</div>
<div id="political-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Add your political history</h4>
            </div>
            <form method="post" enctype="multipart/form-data"  action="<?=base_url('auth/add_political')?>" class="form-horizontal">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="" class="col-sm-4">Select party</label>
                        <div class="col-sm-8">
                            <select class="form-control form-control-line select2" name="party_id">
                                <option value="">select your party name</option>
                                <?php $allparties = $this->Common_model->get_data_by_query("select * from political_parties");
                                foreach ($allparties as $key => $party) {
                                    echo '<option value="'.$party['id'].'">'.$party['name'].' ('.$party['code'].')</option>';
                                 } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-4">Party Designation</label>
                        <div class="col-sm-8">
                            <input type="text" name="designation" class="form-control form-control-line" placeholder="Please enter your designation in party eg. (president)">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-4">Title</label>
                        <div class="col-sm-8">
                            <select name="title" class="form-control form-control-line" id="title">
                                <option value="">Select your title</option>
                                <option value="mp">Member of Parliament (MP)</option>
                                <option value="mla">Member of Legislative Assembly (MLA)</option>
                                <option value="mlc">Member of Legislative Council</option>
                                <option value="councilor">Councilor</option>
                                <option value="other">Other</option>
                            </select>
                            <input type="text" class="form-control form-control-line" name="title_other" style="display: none" id="title_other" placeholder="please enter your title">
                        </div>
                    </div>
                    <!-- MP Code start here -->
                    <div class="form-group" id="top" style="display: none">
                        <label for="" class="col-sm-4">Type of Parliament</label>
                        <div class="col-sm-8">
                            <select class="form-control form-control-line" name="parliament_type" id="parliament_type">
                                <option value="">Select type of Parliament house</option>
                                <option value="loksabha">Lok Sabha</option>
                                <option value="rajyasabha">Rajya Sabha</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group" id="rsabha_state" style="display: none">
                        <label class="col-sm-4">Select State</label>
                        <div class="col-sm-8">
                            <select class="form-control form-control-line select2" name="rajyasabha_state">
                                <option value="">Select your Rajya Sabha state</option>
                                <?php $allstates = $this->Common_model->get_data_by_query("select * from states");
                                    foreach ($allstates as $key => $state) {
                                        echo '<option value="'.$state['state_id'].'">'.$state['state_name'].'</option>';
                                     } ?> 
                            </select>
                        </div>
                    </div>
                    <div class="form-group" id="lsabha_const" style="display: none">
                        <label class="col-sm-4">Select Lok Sabha Seat</label>
                        <div class="col-sm-8">
                            <select class="form-control form-control-line select2" name="loksabha_seat">
                                <option value="">Select your parliament constituencies</option>
                                <?php $allconst = $this->Common_model->get_data_by_query("select * from constituencies");
                                    foreach ($allconst as $key => $const) {
                                        echo '<option value="'.$const['const_id'].'">'.$const['const_name'].'</option>';
                                     } ?> 
                            </select>
                        </div>
                    </div>
                    <!-- MP Code end here -->
                    <!-- MLA Code start here -->
                    <div class="form-group" id="mla_st" style="display: none">
                        <label class="col-sm-4">Select State</label>
                        <div class="col-sm-8">
                            <select class="form-control form-control-line select2" name="mla_state" id="mla_state">
                                <option value="">Select your state</option>
                                <?php
                                $allstates = $this->Common_model->get_data_by_query("select * from states order by state_id");
                                foreach ($allstates as $key => $state) { ?>
                                    <option value="<?=$state['state_id']?>"><?=$state['state_name']?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group" id="mla_di" style="display: none">
                        <label class="col-sm-4">Select District</label>
                        <div class="col-sm-8">
                            <select class="form-control form-control-line select2" name="mla_dist" id="mla_dist">
                                <option value="">Select your district</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group" id="mla_seat" style="display: none">
                        <label class="col-sm-4">Select Assembly Const</label>
                        <div class="col-sm-8">
                            <select class="form-control form-control-line select2" name="mla_const" id="mla_const">
                                <option value="">Select your Assembly seat</option>
                            </select>
                        </div>
                    </div>
                    <!-- MLA Code end here -->
                    <div class="form-group">
                        <label class="col-sm-4">From</label>
                        <div class="col-sm-3">
                            <select class="form-control form-control-line select2" name="from_date">
                                <?php for($i = 1950; $i <=2017; $i++){
                                    echo '<option value="'.$i.'">'.$i.'</option>';
                                } ?>
                            </select>    
                        </div>
                        <label class="col-sm-2">To</label>
                        <div class="col-sm-3">
                            <select class="form-control form-control-line select2" name="to_date">
                                <?php for($i = 1950; $i <=2017; $i++){
                                    echo '<option value="'.$i.'">'.$i.'</option>';
                                } ?>
                            </select>    
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="close" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-info waves-effect waves-light">Add</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>

</script>