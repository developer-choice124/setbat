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
<?php $id = $this->session->userdata('user_id'); ?>
<div class="container-fluid">
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">All Users</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="#">Dashboard</a></li>
                <li class="active">All Users</li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
    <!-- .row -->
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading">
                     All Users
                    <div class="pull-right"><a href="#" data-perform="panel-collapse"><i class="ti-minus"></i></a></div>
                </div>
                <div class="panel-body">
                    <div id="infoMessage"><?php if($this->session->flashdata('message')) { echo $this->session->flashdata('message'); }?></div>
                    <div class="table-responsive" id="divtlimit">
                        <table id="allusers" class="table table-condensed" >
                            <thead>
                                <tr class="headings">
                                  <th class="">S.No. </th>
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
                                  <th class="">Bet Id </th>
                                  <th>Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; foreach($bets as $b):?>
                                  <tr>
                                    <td><?=$i++;?></td>
                                    <td><?=$this->Common_model->findfield('users','id',$b['user_id'],'username');?></td>
                                    <td><?=$b['match_name'];?></td>
                                    <td><?=$b['team'];?></td>
                                    <td><?=$b['back_lay'];?></td>
                                    <td><?=$b['odd'];?></td>
                                    <td><?=$b['stake'];?></td>
                                    <td><?=date('Y-M-d H:i:s a',strtotime($b['created_at']));?></td>
                                    <td><span class="text-success"><?=$b['profit'];?></span></td>
                                    <td><span class="text-danger"><?=$b['loss'];?></span></td>
                                    <td><?php if($b['status'] == 'pending'){ echo 'pending'; } else { 
                                          if($b['bet_result']=='win') {
                                            echo '<span class="text-success">0</span>';
                                          } else {
                                            echo '<span class="text-danger">'.$b['loss'].'</span>';
                                          }
                                      } ?> 
                                    </td>
                                    <td><?=ucwords($b['bet_type']);?></td>
                                    <td><?=$b['status'];?></td>
                                    <td><?=$b['ip'];?></td>
                                    <td><?=$b['id'];?></td>
                                    <td><?=anchor("Admin/deleteUserBet?id=".$b['id']."&user_id=".$b['user_id'],"Delete",array('onclick' => "return confirm('Do you want delete this record')"))?></td>
                                  </tr>
                                <?php endforeach;?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- .row -->
</div>