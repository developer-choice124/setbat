<script src="<?php echo base_url('assets/backend/js/jquery-3.3.1.min.js')?>"></script>

<style type="text/css">
  .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
    padding: 10px 8px !important;
    
  }
  hr {
      margin: 0px !important;
      padding: 0 !important;
    }
  .back {
    background-color: #b5e0ff;
  }
  .lay {
    background-color: #ffbfcd;
  }
  .loader {
    display: none;
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(255,255,255,0.8) url(<?=base_url('assets/plugins/images/loader.gif');?>) top center no-repeat;
    z-index: 1000;
  }
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
                  <span class="small"><?=$match->event_name;?></span>
                  <div class="pull-right"><a href="<?=base_url('SuperMaster/runningCricket');?>">Back</a><a href="#" data-perform="panel-collapse"><i class="ti-minus"></i></a></div>
                </div>
                <div class="panel-body">
                    <div id="message"><?php if ($this->session->flashdata('message')) {
                          echo $this->session->flashdata('message');
                        } ?>
                    </div>
                    <div id="scoreReload"></div>
                    <div class="row">
                      <div class="col-md-7">
                        <div class="row">
                          <div class="table-responsive" id="singleMatchTable">
                              <table class="table table-bordered table-condensed" width="100%" >
                                <tr>
                                  <th style="border: none !important;"><b style="color: red;">Min stake:100 Max stake:200000</b></th>
                                  <th style="background: #2c5ca9; color: white; border: none !important; min-width: 50px;  max-width: 50px;"><center>back</center></th>
                                  <th style="background: red; color: white; border: none !important; min-width: 50px; max-width: 50px;"><center>lay</center></th>
                                </tr>

                                <?php $uid = $this->session->userdata('user_id');?>
                                <?php $matchOdds = $odds[0]['teams']; 
                                  foreach($matchOdds as $mk => $mo){ ?>
                                  <tr>
                                    <td><b><?=$mo->name;?></b><span class="pull-right" id="<?=$mo->id;?>"></span></td>
                                    <td style="background: #b5e0ff; cursor: pointer;"><center><b><?=$mo->back['price'];?></b><br/><?=$mo->back['size'];?></center></td>
                                    <td style="background: #ffbfcd; cursor: pointer;" ><center><b><?=$mo->lay['price'];?></b><br/><?=$mo->lay['size'];?></center></td>
                                  </tr>
                                <?php } ?>
                              </table>
                            </div>
                          </div>
                          
                          <div class="row">
                            <div class="table-responsive" id="fancyTable">
                              <table class="table table-bordered" width="100%">
                                <tr>
                                  <th style="border: none !important;" width="63%"></th>
                                  <th style="background: red; color: white; border: none !important; min-width: 50px; max-width: 50px;"><center>NO(L)</center></th>
                                  <th style="background: #2c5ca9; color: white; border: none !important; min-width: 50px;  max-width: 50px;"><center>YES(B)</center></th>
                                  <th width="17%"></th>
                                </tr>
                                <?php if($dfancy) {
                                  $did = array();
                                  foreach ($dfancy as $dkey => $d) {
                                    $did[] = $d['fancy_id'];
                                  }
                                  ?>
                                  <?php if($fancy) { ?>
                                    <?php foreach ($fancy as $fk => $f) { ?>
                                      <?php if(in_array($f['SelectionId'], $did)) { ?>
                                        <tr>
                                          <td><?=$f['RunnerName'];?><span class="pull-right"><button class="btn btn-warning btn-sm" onclick="getBookedFancy('<?=$f['RunnerName'];?>','<?=$match->market_id;?>')" data-toggle="modal" data-target="#bookFancyModal">book</button></span></td>
                                          <td style="background-color: #ffbfcd; cursor: pointer; text-align: center;"><b><?=$f['LayPrice1'];?></b><br><?=$f['LaySize1'];?></td>
                                          <td style="background-color: #b5e0ff; cursor: pointer; text-align: center;"><b><?=$f['BackPrice1'];?></b><br><?=$f['BackSize1'];?></td>
                                          <td></td>
                                        </tr>
                                      <?php } ?>
                                    <?php } ?>
                                  <?php } ?>
                                <?php } ?>
                              </table>
                            </div>
                          </div>
                          <div class="row">
                            <div class="table-responsive" id="panaTable"></div>
                          </div>
                      </div>
                      <div class="col-md-5">
                        <div class="white-box" id="betReload">
                          <!-- Nav tabs -->
                          <ul class="nav customtab nav-tabs" role="tablist">
                              <li role="presentation" class=""><a href="#unmatchedTab" aria-controls="home" role="tab" data-toggle="tab" aria-expanded="true"><span class="visible-xs"><i class="ti-home"></i></span><span class="hidden-xs"> Unmatched</span></a></li>
                              <li role="presentation" class="active"><a href="#matchedTab" aria-controls="profile" role="tab" data-toggle="tab" aria-expanded="false"><span class="visible-xs"><i class="ti-user"></i></span> <span class="hidden-xs">Matched</span></a></li>
                              <li role="presentation" class=""><a href="#fancyTab" aria-controls="messages" role="tab" data-toggle="tab" aria-expanded="false"><span class="visible-xs"><i class="ti-email"></i></span> <span class="hidden-xs">Fancy</span></a></li>
                          </ul>
                          <!-- Tab panes -->
                          <div class="tab-content">
                            <div role="tabpanel" class="tab-pane fade " id="unmatchedTab">
                              
                                <div class="table-responsive">
                                  <table class="table table-bordered">
                                    <tr class="headings">
                                      <th class="">User</th>
                                      <th class="">Runner </th>
                                      <th class="">Type </th>
                                      <th class="">Odds </th>
                                      <th class="">Stack </th>
                                      <th class="">Profit </th>
                                      <th class="">Loss </th>
                                      <th class="">IP </th>
                                      <th class="">ID </th>
                                    </tr>
                                    <?php foreach($ubets as $ub):?>
                                      <tr class="<?=$ub['back_lay'] == 'back' ? 'back' : 'lay';?>">
                                        <td><?=$this->Common_model->findfield('users','id',$ub['user_id'],'username');?></td>
                                        <td><?=$ub['team'];?></td>
                                        <td><?=$ub['back_lay'];?></td>
                                        <td><?=$ub['odd'];?></td>
                                        <td><?=$ub['stake'];?></td>
                                        <td><?=$ub['profit'];?></td>
                                        <td><?=$ub['loss'];?></td>
                                        <td><?=$ub['ip'];?></td>
                                        <td><?=$ub['id'];?></td>
                                      </tr>
                                    <?php endforeach;?>
                                  </table>
                                </div>
                              <div class="clearfix"></div>
                            </div>
                            <div role="tabpanel" class="tab-pane fade active in" id="matchedTab">
                              
                                <div class="table-responsive">
                                  <table class="table table-bordered">
                                    <tr class="headings">
                                      <th class="">User</th>
                                      <th class="">Runner </th>
                                      <th class="">Type </th>
                                      <th class="">Odds </th>
                                      <th class="">Stack </th>
                                      <th class="">Profit </th>
                                      <th class="">Loss </th>
                                      <th class="">IP </th>
                                      <th class="">ID </th>
                                    </tr>
                                    <?php foreach($mbets as $mb):?>
                                      <tr class="<?=$mb['back_lay'] == 'back' ? 'back' : 'lay';?>">
                                        <td><?=$this->Common_model->findfield('users','id',$mb['user_id'],'username');?></td>
                                        <td><?=$mb['team'];?></td>
                                        <td><?=$mb['back_lay'];?></td>
                                        <td><?=$mb['odd'];?></td>
                                        <td><?=$mb['stake'];?></td>
                                        <td><?=$mb['profit'];?></td>
                                        <td><?=$mb['loss'];?></td>
                                        <td><?=$mb['ip'];?></td>
                                        <td><?=$mb['id'];?></td>
                                      </tr>
                                    <?php endforeach;?>
                                  </table>
                                </div>
                              <div class="clearfix"></div>
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="fancyTab">
                              
                                <div class="table-responsive">
                                  <table class="table table-bordered">
                                    <tr class="headings">
                                      <th class="">User</th>
                                      <th class="">Runner </th>
                                      <th class="">Type </th>
                                      <th class="">Odds </th>
                                      <th class="">Stack </th>
                                      <th class="">Profit </th>
                                      <th class="">Loss </th>
                                      <th class="">IP </th>
                                      <th class="">ID </th>
                                    </tr>
                                    <?php foreach($fbets as $fb):?>
                                      <tr class="<?=$fb['back_lay'] == 'back' ? 'back' : 'lay';?>">
                                        <td><?=$this->Common_model->findfield('users','id',$fb['user_id'],'username');?></td>
                                        <td><?=$fb['team'];?></td>
                                        <td><?=$fb['back_lay'];?></td>
                                        <td><?=$fb['odd'];?></td>
                                        <td><?=$fb['stake'];?></td>
                                        <td><?=$fb['profit'];?></td>
                                        <td><?=$fb['loss'];?></td>
                                        <td><?=$fb['ip'];?></td>
                                        <td><?=$fb['id'];?></td>
                                      </tr>
                                    <?php endforeach;?>
                                  </table>
                                </div>
                              </div>   
                              <div class="clearfix"></div>
                          </div>
                        </div>
                      </div>  
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div id="bookFancyModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Fancy Booking Details</h4>
      </div>
      <div class="modal-body">
        <div class="table-responsive" id="fancyBookingTable">
          
        </div>
      </div>
    </div>

  </div>
</div>
<script type="text/javascript">
    var match;
    var fancies;
    var bets;
    var pNl;
    var team;
    var team1pl;
    var team1color;
    var team2pl;
    var team2color;
    var matchId = '<?=$match->event_id;?>';
    var marketId = '<?=$match->market_id;?>';
    var ctype = 'back';
    var id = 1;
    var profit = 0;
    var loss = 0;
    var stake;
    var price;
    var oddFancy;
    var pana;
    $(document).ready(function(){
      match = setInterval("matchReload()", 2500);
      fancies = setInterval("fancyReload()", 3000);
      bets = setInterval("betReload()", 30000);
      pana = setInterval("panaReload()", 5000);
      profitNLoss();
      scoreReload();
    });

    function scoreReload() {
      $.ajax({
          url: "<?php echo site_url('SuperMaster/scoreReload?market_id=') ?>" + marketId + "&match_id=" + matchId,
          type: "POST",
          dataType: 'json',
          success: function (data, textStatus, jqXHR) {
              $("#scoreReload").html(data.score);
              setTimeout( scoreReload, 5000);
          }
      });
    }

    function profitNLoss() {
      $.ajax({
        url : "<?php echo site_url('SuperMaster/profitNLoss?market_id=')?>"+marketId,
        type: "POST",
        dataType: 'json',
        success: function(data)
        {
          var plData = data;
          for (var j = 0; j < plData.length; j++) {
            plData[j].pl >= 0 ? $("#"+plData[j].id).addClass("text-danger") : $("#"+plData[j].id).addClass("text-success");
            plData[j].pl >= 0 ? $("#"+plData[j].id).removeClass("text-success") : $("#"+plData[j].id).removeClass("text-danger");
            $("#"+plData[j].id).text(Math.abs(plData[j].pl));
          }
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
          
        }
      });
    }

    function matchReload() {
      var mid = "<?=$match->market_id;?>";
      $.ajax({
        url : "<?php echo site_url('SuperMaster/matchReload?market_id=')?>"+mid,
        type: "POST",
        success: function(data)
        {
          $("#singleMatchTable").html(data);
          profitNLoss();
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
          //alert("error");
        }
      });
    }

    function fancyReload() {
      var mid = "<?=$match->market_id;?>";
      $.ajax({
        url : "<?php echo site_url('SuperMaster/fancyReload?market_id=')?>"+mid,
        type: "POST",
        success: function(msg)
        {
          var obj = JSON.parse(msg);
          var scoreData = obj.score;
          var fancyData = obj.fancy; 
          $("#scoreTable").html(scoreData);
          $("#fancyTable").html(fancyData);
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
          //alert("error");
        }
      });
    }

    function betReload() {
      var mid = "<?=$match->market_id;?>";
      $.ajax({
        url : "<?php echo site_url('SuperMaster/betReload?market_id=')?>"+mid,
        type: "POST",
        success: function(data)
        {
          $("#betReload").html(data);
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
          //alert("error");
        }
      });
    }
    $(".myadmin-alert-top-right").click(function(event) {
        $(this).fadeToggle(350);
        return false;
    });
    function panaReload() {
      var mid = "<?=$match->market_id;?>";
      $.ajax({
        url : "<?php echo site_url('SuperMaster/showPana?market_id=')?>"+mid,
        type: "POST",
        success: function(data)
        {
          $("#panaTable").html(data);
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
          //alert("error");
        }
      });
    }
    function getBookedFancy(runner,mid) {
      $.ajax({
        url : "<?php echo site_url('SuperMaster/getBookedFancy?runner=');?>"+runner+"&market_id="+mid,
        type: "POST",
        success: function(data)
        {
          $("#fancyBookingTable").html(data);
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
          //alert("error");
        }
      });
    }
</script>