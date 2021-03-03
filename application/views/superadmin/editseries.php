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
                        }?>
                    </div>
                    <form action="<?= base_url("SuperAdmin/updateSeries/$series->id"); ?>" method="POST" class="form-horizontal">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-3">
                                    <input type="text" name="name" class="form-control form-control-line" value="<?= $series->name; ?>" placeholder="please enter name here" required />
                                </div>
                                <div class="col-md-3">
                                    <input type="text" name="duration" class="form-control form-control-line" value="<?= $series->duration; ?>" placeholder="please enter duration" />
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <section>
                                            <select name="status" class="form-control form-control-line" id="status">
                                                <option value="">--- Status ---</option>
                                                <option value="UPCOMING" <?php if($series->status === "UPCOMING"){ echo "selected"; }?>>Upcoming</option>
                                                <option value="RUNNING" <?php if($series->status === "RUNNING"){ echo "selected"; }?>>Running</option>
                                                <option value="COMPLETED" <?php if($series->status === "COMPLETED"){ echo "selected"; }?>>Completed</option>
                                                <option value="SUSPENDED" <?php if($series->status === "SUSPENDED"){ echo "selected"; }?>>Suspended</option>
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
                </div>
            </div>
        </div>
    </div>
</div>




