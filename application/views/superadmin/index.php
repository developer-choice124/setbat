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
        <!-- .page title -->
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">Dashboard</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active"><a href="#">Dashboard</a></li>
            </ol>
        </div>
        <!-- /.breadcrumb -->
    </div>
    <!-- .row -->
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-darkblue">
                <div class="panel-heading">
                     Running Matches
                    <div class="pull-right"><a href="#" data-perform="panel-collapse"><i class="ti-minus"></i></a></div>
                </div>
                <div class="panel-body">
                    <div><?php if ($this->session->flashdata('message')) {
                          echo $this->session->flashdata('message');
                        } ?>
                    </div>
                    <div class="table-responsive" id="matchTable">
                        <table id="" class="table table-bordered table-striped" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th colspan="5">Cricket</th>
                                </tr>
                            </thead>
                            <tbody>
                              <?php $i = 1; foreach ($matches as $mkey => $m) { ?>
                                <tr>
                                  <td><?=$i++;?></td>
                                  <td><a href="<?=base_url('SuperAdmin/matchOdds?market_id='.$m['market_id'].'&match_id='.$m['event_id']);?>"><?=$m['event_name'];?></a></td>
                                  <td><a href="<?=base_url('SuperAdmin/matchOdds?market_id='.$m['market_id'].'&match_id='.$m['event_id']);?>">
                                    <?php if($m['odds'][0]['inplay'] == 1 || $m['odds'][0]['inplay'] == true || $m['odds'][0]['inPlay'] == 1 || $m['odds'][0]['inPlay'] == true) {
                                      echo 'In Play';
                                    } ?>
                                  </a></td>
                                  <td><?=date('D d-M-Y H:i:sa',strtotime($m['start_date']));?></td>
                                  <td>
                                    <?php foreach($m['odds'][0]['teams'] as $r):?>
                                      <a href="<?=base_url('SuperAdmin/matchOdds?market_id='.$m['market_id'].'&match_id='.$m['event_id']);?>" class="btn btn-darkblue" style="color: white;">
                                        <?=$r->back['price'];?>
                                      </a>
                                      <a href="<?=base_url('SuperAdmin/matchOdds?market_id='.$m['market_id'].'&match_id='.$m['event_id']);?>" class="btn btn-danger" style="color: white;">
                                        <?=$r->lay['price'];?>
                                      </a>
                                    <?php endforeach;?>
                                  </td>
                                </tr>
                              <?php } ?>
                            </tbody>
                        </table>
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
<script>
    function matchResult(id)
    {
      $.ajax({
        url : "<?php echo site_url('SuperAdmin/matchResult?id=')?>"+id,
        type: "POST",
        //dataType: "JSON",
        success: function(data)
        {
            $("#matchFormData").html(data);
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert("error");
        }
      });
    }
</script>