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
            <div class="panel panel-info">
                <div class="panel-heading">
                     BET History
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
                                  <th>User</th>
                                  <th class="">Description </th>
                                  <th class="">Selection </th>
                                  <th class="">Type </th>
                                  <th class="">Odds </th>
                                  <th class="">Stack </th>
                                  <th class="">Date </th>
                                  <th class="">Profit </th>
                                  <th class="">Loss </th>
                                  <th class="">Liability </th>
                                  <th class="">Bet type</th>
                                  <th class="">Status </th>
                                  <th class="">IP </th>
                                  <th class="">ID </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                  <td><?=$this->Common_model->findfield('users','id',$bet->user_id,'username');?></td>
                                  <td><?=$bet->match_name;?></td>
                                  <td><?=$bet->team;?></td>
                                  <td><?=$bet->back_lay;?></td>
                                  <td><?=$bet->odd;?></td>
                                  <td><?=$bet->stake;?></td>
                                  <td><?=date('Y-M-d H:i:s a',strtotime($bet->created_at));?></td>
                                  <td><?php if($bet->status == 'pending') echo 'Pending'; else echo '<span class="text-success">'.$bet->profit.'</span>';?></td>
                                  <td><?php if($bet->status == 'pending') echo 'Pending'; else echo '<span class="text-danger">'.$bet->loss.'</span>';?></td>
                                  <td><?php if($bet->status == 'pending') echo 'Pending'; else echo '<span class="text-danger">'.$bet->loss.'</span>';?></td>
                                  <td><?=ucwords($bet->bet_type);?></td>
                                  <td><?=$bet->status;?></td>
                                  <td><?=$bet->ip;?></td>
                                  <td><?=$bet->id;?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>