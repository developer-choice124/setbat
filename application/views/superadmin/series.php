<script src="<?= base_url('assets/backend/js/jquery-3.3.1.min.js')?>"></script>
<style type="text/css">
  .headings{
    background: #2c5ca9 !important;
    color: #fff;
  }
  .headings th {
    color: #fff;
    font-weight: normal !important;
  }
</style>
<div class="container-fluid">
    <div class="row bg-title">
    </div>
    <!-- .row -->
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-darkblue">
                <div class="panel-heading">
                     Series
                    <div class="pull-right"><a href="#" data-perform="panel-collapse"><i class="ti-minus"></i></a></div>
                </div>
                <div class="panel-body">
                    <div><?php if ($this->session->flashdata('message')) {
                          echo $this->session->flashdata('message');
                        } ?>
                    </div>
                    <form action="<?= base_url('SuperAdmin/addSeries'); ?>" method="POST" class="form-horizontal">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-3">
                                    <input type="text" name="name" class="form-control form-control-line" placeholder="please enter name here" required />
                                </div>
                                <div class="col-md-3">
                                    <input type="text" name="duration" class="form-control form-control-line" placeholder="please enter duration" />
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <section>
                                            <select name="status" class="form-control form-control-line" id="status">
                                                <option value="">--- Status ---</option>
                                                <option value="UPCOMING">Upcoming</option>
                                                <option value="RUNNING">Running</option>
                                                <option value="COMPLETED">Completed</option>
                                                <option value="SUSPENDED">Suspended</option>
                                            </select>
                                        </section>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <center><button type="submit" class="btn btn-darkblue">submit</button></center>
                                </div>
                            </div>
                        </div>
                    </form>
                    <hr>
                    <div class="table-responsive Series-tabs">
                        <section class="s-tabs">
                            <input id="tab-1" type="radio" name="radio-set" class="tab-selector-1" checked="checked" />
                            <label for="tab-1" class="tab-label-1">UPCOMING</label>
                            
                            <input id="tab-2" type="radio" name="radio-set" class="tab-selector-2" />
                            <label for="tab-2" class="tab-label-2">RUNNING</label>
                            
                            <input id="tab-3" type="radio" name="radio-set" class="tab-selector-3" />
                            <label for="tab-3" class="tab-label-3">COMPLETED</label>
                            
                            <input id="tab-4" type="radio" name="radio-set" class="tab-selector-4" />
                            <label for="tab-4" class="tab-label-4">SUSPENDED</label>
                                    
                            <div class="clear-shadow"></div>
                                        
                            <div class="content">
                                <div class="content-1">
                                    <table class="table table-bordered table-striped" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>S. No</th>
                                                <th>Name</th>
                                                <th>Duration</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php $i = 1; foreach ($series as $skey => $s) { 
                                                if($s['status'] === 'UPCOMING'){
                                                    ?>
                                                    <tr>
                                                        <td><?= $i++;?></td>
                                                        <td><?=$s['name'];?></td>
                                                        <td><?=$s['duration'];?></td>
                                                        <td><?=$s['status'];?></td>
                                                        <td>
                                                            <a href="<?= base_url("SuperAdmin/editSeries/"). $s['id']."/". $s['name']; ?>" class="btn btn-info text-light"><i class="fa fa-edit"></i></a>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                }
                                            ?>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="content-2">
                                    <table class="table table-bordered table-striped" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>S. No</th>
                                                <th>Name</th>
                                                <th>Duration</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php  $i = 1; foreach ($series as $skey => $s) {  
                                            
                                            if($s['status'] === 'RUNNING'){
                                            ?>
                                            <tr>
                                                <td><?= $i++;?></td>
                                                <td><?= $s['name']; ?></td>
                                                <td><?= $s['duration']; ?></td>
                                                <td><?= $s['status']; ?></td>
                                                <td>
                                                    <a href="<?= base_url("SuperAdmin/editSeries/"). $s['id']."/". $s['name']; ?>" class="btn btn-info text-light"><i class="fa fa-edit"></i></a>
                                                </td>
                                            </tr>
                                        <?php }} ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="content-3">
                                    <table class="table table-bordered table-striped" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>S. No</th>
                                                <th>Name</th>
                                                <th>Duration</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php  $i = 1; foreach ($series as $skey => $s) { 
                                            if($s['status'] === 'COMPLETED'){
                                            ?>
                                            <tr>
                                                <td><?= $i++;?></td>
                                                <td><?=$s['name'];?></td>
                                                <td><?=$s['duration'];?></td>
                                                <td><?=$s['status'];?></td>
                                                <td>
                                                    <a href="<?= base_url("SuperAdmin/editSeries/"). $s['id']."/". $s['name']; ?>" class="btn btn-info text-light"><i class="fa fa-edit"></i></a>
                                                </td>
                                            </tr>
                                        <?php } } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="content-4">
                                    <table class="col-sm-12 table table-bordered table-striped" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>S. No</th>
                                                <th>Name</th>
                                                <th>Duration</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php $i = 1; foreach ($series as $skey => $s) { 
                                            
                                            if($s['status'] === 'SUSPENDED'){
                                            ?>
                                            <tr>
                                                <td><?= $i++;?></td>
                                                <td><?=$s['name'];?></td>
                                                <td><?=$s['duration'];?></td>
                                                <td><?=$s['status'];?></td>
                                                <td>
                                                    <a href="<?= base_url("SuperAdmin/editSeries/"). $s['id']."/". $s['name']; ?>" class="btn btn-info text-light"><i class="fa fa-edit"></i></a>
                                                </td>
                                            </tr>
                                        <?php } } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div id="resultModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Match Result</h4>
      </div>
      <div id="matchFormData">
        
      </div>
      
    </div>

  </div>
</div>




