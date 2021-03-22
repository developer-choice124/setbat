<script src="<?php echo base_url('assets/backend/js/jquery-3.3.1.min.js') ?>"></script>
<?php $id = $this->session->userdata('user_id');?>
<div class="container-fluid">
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">Update Match</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="#">Dashboard</a></li>
                <li class="active">Update Match</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
    <!-- .row -->
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-darkblue">
                <div class="panel-heading">
                     Update Match
                    <div class="pull-right"><a class="btn btn-darkblue" href="<?=base_url('SuperAdmin/allCricket');?>"><i class="fa fa-user m-l-5"></i>ALL MATCHES</a><a href="#" data-perform="panel-collapse"><i class="ti-minus"></i></a></div>
                </div>
                <div class="panel-body">
                    <div class="badge badge-warning"><?php if ($this->session->flashdata('message')) {echo $this->session->flashdata('message');}?></div>
                    <form action="<?=base_url('SuperAdmin/updateMatch/' . $match->id);?>" method="post" class="form-horizontal">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <label>Event Name</label>
                                    <input type="text" name="event_name" class="form-control form-control-line" placeholder="please enter Event Name" required value="<?=$match->event_name;?>">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label>Market Id</label>
                                    <input type="text" name="market_id" min="0" max="100" class="form-control form-control-line" placeholder="Market Id" value="<?=$match->market_id;?>">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label>Event Id</label>
                                    <input type="text" name="event_id" min="0" max="100" class="form-control form-control-line" placeholder="Event Id" value="<?=$match->event_id;?>">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label>Series </label>
                                    <select class="form-control  form-control-line" name="series_id" id="series_id">
                                        <?php foreach ($series as $key => $list): ?>
                                            <option value="<?=$list['id'];?>" <?php
if ($match->series_id == $list['id']) {echo "selected";}
?>><?=$list['name'];?></option>
                                        <?php endforeach;?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <?php echo form_hidden('id', $match->id); ?>
                                    <?php echo form_hidden($csrf); ?>
                                    <button type="submit" class="btn btn-darkblue">submit</button>
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