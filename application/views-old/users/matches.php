<script src="<?php echo base_url('assets/backend/js/jquery-3.3.1.min.js')?>"></script>
<div class="container-fluid">
    <div class="row bg-title">
        
    </div>
    <!-- .row -->
    <div class="row">
        <div class="col-sm-2">
            <ul class="list-group">
              <li class="list-group-item"><a href="#"><i class="fa fa-heart"></i> &nbsp;Favorite</a></li>
              <li class="list-group-item"><a href="<?=base_url('User/inPlay');?>"><i class="fa fa-play"></i> &nbsp;In Play</a></li>
              <li class="list-group-item"><a href="#"><i class="fa fa-bar-chart"></i> &nbsp;My Market</a></li>
              <li class="list-group-item"><a href="<?=base_url('User/matches');?>"><i class="fa fa-child"></i> &nbsp;Cricket</a></li>
              <li class="list-group-item"><a href="#"><i class="fa fa-futbol-o"></i> &nbsp;Soccer</a></li>
              <li class="list-group-item"><a href="#"><i class="fa fa-globe"> </i> &nbsp;Tennis</a></li>
            </ul>
        </div>
        <div class="col-sm-10">
            <div class="panel panel-info">
                <div class="panel-heading">
                     Matches 
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
                                  <td><a href="<?=base_url('MsUser/match?market_id='.$m['market_id'].'&match_id='.$m['event_id']);?>"><?=$m['event_name'];?></a></td>
                                  <td><a href="<?=base_url('MsUser/match?market_id='.$m['market_id'].'&match_id='.$m['event_id']);?>">
                                    <?php if($m['status'] == 1 || $m['status'] == true) {
                                      echo 'In Play';
                                    } ?>
                                  </a></td>
                                  <td><?=date('D d-M-Y H:i:sa',strtotime($m['start_date']));?></td>
                                  <td>
                                    <?php foreach($m['odds'] as $r):?>
                                      <a href="<?=base_url('MsUser/match?market_id='.$m['market_id'].'&match_id='.$m['event_id']);?>" class="btn btn-info" style="color: white;">
                                        <?=$r['ex']['availableToBack'][0]['price'];?>
                                      </a>
                                      <a href="<?=base_url('MsUser/match?market_id='.$m['market_id'].'&match_id='.$m['event_id']);?>" class="btn btn-danger" style="color: white;">
                                        <?=$r['ex']['availableToLay'][0]['price'];?>
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

<script type="text/javascript">
    var matches;
    $(document).ready(function(){
      matches = setInterval("getMatches()", 1500);
    });

    function getMatches() {
      $.ajax({
        url : "<?php echo site_url('User/getMatches')?>",
        type: "POST",
        success: function(data)
        {
          $("#matchTable").html(data);
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
          //alert("error");
        }
      });
    }
</script>