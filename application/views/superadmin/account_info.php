<script src="<?php echo base_url('assets/backend/js/jquery-3.3.1.min.js')?>"></script>
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
                     Account Info
                    <div class="pull-right"><a href="#" data-perform="panel-collapse"><i class="ti-minus"></i></a></div>
                </div>
                <div class="panel-body">
                    <div><?php if ($this->session->flashdata('message')) {
                          echo $this->session->flashdata('message');
                        } ?>
                    </div>
                    <div class="table-responsive" id="divtlimit">
                        <table id="allusers" class="table table-condensed" >
                            <thead>
                                <tr class="headings">
                                    <th>S. No</th>
                                    <th>Up</th>
                                    <th>Down</th>
                                    <th>P/L</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                  <?php $bal = $up->up - $down->down;?>
                                  <td>1</td>
                                  <td><?=$up->up;?></td>
                                  <td><?=$down->down;?></td>
                                  <td><?=$bal < 0 ? '<span class="text-danger">'.$bal.'</span>' : '<span class="text-success">'.$bal.'</span>' ;?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>