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
<?php $teams = json_decode($match->teams,true); //print_r($odds);die;?>
<div class="container-fluid">
    <div class="row bg-title">
       
    </div>
    <!-- .row -->
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading">
                     <?=$match->event_name;?> 
                    <div class="pull-right"><a href="<?=base_url('Admin/runningCricket');?>">Running Matches</a><a href="#" data-perform="panel-collapse"><i class="ti-minus"></i></a></div>
                </div>
                <div class="panel-body">
                    <div id="message"><?php if ($this->session->flashdata('message')) {
                          echo $this->session->flashdata('message');
                        } ?>
                    </div>
                    <div class="row">
                      <div class="col-md-8">
                        <div class="row">
                          <?php $scores = $fancy['score']; ?>
                          <table class="table table-bordered bg-dark" id="scoreTable">
                            <tr>
                              <th class="text-white">Team</th>
                              <th class="text-white">RR</th>
                              <th class="text-white">Over</th>
                            </tr>
                            <tr class="text-white">
                              <td><?=$scores['Team1']['score'];?></td>
                              <td><?=$scores['Team1']['RR'];?></td>
                              <td><?=$scores['Team1']['over'];?></td>
                            </tr>
                            <tr class="text-white">
                              <td><?=$scores['Team2']['score'];?></td>
                              <td><?=$scores['Team2']['RR'];?></td>
                              <td><?=$scores['Team2']['over'];?></td>
                            </tr>
                            <tr class="text-white">
                              <td colspan="3">
                                <b>Commentary: </b><?=$scores['comm'];?>
                              </td>
                            </tr>
                          </table>
                          <div class="table-responsive" id="singleMatchTable">
                              <table class="table table-bordered table-condensed" width="100%" >
                                <tr>
                                  <th style="border: none !important;"><b style="color: red;">Min stake:100 Max stake:200000</b></th>
                                  <th style="border: none !important; min-width: 50px; max-width: 50px;"></th>
                                  <th style="border: none !important; min-width: 50px; max-width: 50px;"></th>
                                  <th style="background: #2c5ca9; color: white; border: none !important; min-width: 50px;  max-width: 50px;"><center>back</center></th>
                                  <th style="background: red; color: white; border: none !important; min-width: 50px; max-width: 50px;"><center>lay</center></th>
                                  <th style="border: none !important; min-width: 50px; max-width: 50px;"></th>
                                  <th style="border: none !important; min-width: 50px; max-width: 50px;"></th>
                                </tr>

                                <?php $uid = $this->session->userdata('user_id'); 
                                  $t1id = $teams[0]['id'];
                                  $t2id = $teams[1]['id'];
                                  $runners = $odds['runners'];
                                  foreach($runners as $rk => $r){
                                    $back = $r['ex']['availableToBack'];
                                    $bprice = $back[0]['price'];
                                    $lay = $r['ex']['availableToLay'];
                                    $lprice = $lay[0]['price'];
                                    //print_r($lay[0]['price']);die;
                                    $rid = $r['selectionId'];
                                    $mid = $match->event_id;
                                    foreach ($teams as $tkey => $t) {
                                      if($t['id'] == $rid) {
                                        $rname = $t['name'];
                                      }
                                    }
                                  ?>
                                  <tr>
                                    <td><b><?=$rname;?></b><span class="pull-right" id="<?='team'.$rk;?>"></span></td>
                                    <td><center><b><?=$back[2]['price'];?></b><br><?=$back[2]['size'];?></center></td>
                                    <td><center><b><?=$back[1]['price'];?></b><br/><?=$back[1]['size'];?></center></td>
                                    <td style="background: #b5e0ff; cursor: pointer;"><center><b><?=$back[0]['price'];?></b><br/><?=$back[0]['size'];?></center></td>
                                    <td style="background: #ffbfcd; cursor: pointer;" ><center><b><?=$lay[0]['price'];?></b><br/><?=$lay[0]['size'];?></center></td>
                                    <td><center><b><?=$lay[1]['price'];?></b><br/><?=$lay[1]['size'];?></center></td>
                                    <td><center><b><?=$lay[2]['price'];?></b><br/><?=$lay[2]['size'];?></center></td>
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
                                <?php $did = array();
                                foreach ($dfancy as $dkey => $d) {
                                  $did[] = $d['fancy_id'];
                                }
                                ?>
                                <?php $fancies = $fancy['session'];?>
                                <?php foreach ($fancies as $fkey => $f) { ?>
                                  <?php if(in_array($f['SelectionId'], $did)) { ?>
                                  <tr>
                                    <td><?=$f['RunnerName'];?><span class="pull-right"><button class="btn btn-warning btn-sm" onclick="getBookedFancy('<?=$f['RunnerName'];?>','<?=$match->market_id;?>')" data-toggle="modal" data-target="#bookFancyModal">book</button></span></td>
                                    <td style="background-color: #ffbfcd; cursor: pointer; text-align: center;"><b><?=$f['LayPrice1'];?></b><br><?=$f['LaySize1'];?></td>
                                    <td style="background-color: #b5e0ff; cursor: pointer; text-align: center;"><b><?=$f['BackPrice1'];?></b><br><?=$f['BackSize1'];?></td>
                                    <td></td>
                                  </tr>
                                <?php } ?>
                                <?php } ?>
                              </table>
                            </div>
                          </div>
                          <div class="row">
                            <div class="table-responsive" id="panaTable">
                              
                            </div>
                          </div>
                      </div>
                      <div class="col-md-4">
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
    var team1Id = '<?=$t1id;?>';
    var team2Id = '<?=$t2id;?>';
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
    });

    function profitNLoss() {
      $.ajax({
        url : "<?php echo site_url('Admin/profitNLoss?market_id=')?>"+marketId+"&team1="+team1Id+"&team2="+team2Id,
        type: "POST",
        dataType: 'json',
        success: function(data)
        {
          if(data) {
            team1pl = data[0].pl;
            team2pl = data[1].pl;
            if(data.length > 2)
            {
              team3pl = data[2].pl;
            }
          }
          team1pl >= 0 ? $("#team0").addClass("text-danger") : $("#team0").addClass("text-success");
          team1pl >= 0 ? $("#team0").removeClass("text-success") : $("#team0").removeClass("text-danger");
          $("#team0").text(Math.abs(team1pl));
          team2pl >= 0 ? $("#team1").addClass("text-danger") : $("#team1").addClass("text-success");
          team2pl >= 0 ? $("#team1").removeClass("text-success") : $("#team1").removeClass("text-danger");
          $("#team1").text(Math.abs(team2pl));
          if(data.length > 2)
          {
            team3pl >= 0 ? $("#team2").addClass("text-danger") : $("#team2").addClass("text-success");
            team3pl >= 0 ? $("#team2").removeClass("text-success") : $("#team2").removeClass("text-danger");
            $("#team2").text(Math.abs(team3pl));
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
        url : "<?php echo site_url('Admin/matchReload?market_id=')?>"+mid,
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
        url : "<?php echo site_url('Admin/fancyReload?market_id=')?>"+mid,
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
        url : "<?php echo site_url('Admin/betReload?market_id=')?>"+mid,
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
        url : "<?php echo site_url('Admin/teamProfitLossSuperMaster?market_id=')?>"+mid,
        type: "POST",
        success: function(data)
        {
          //console.log(data);
          $("#panaTable").html(data);
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
          //alert("error");
        }
      });
    }
    function getBookedFancy(runner,mid) {
      console.log(runner);
      $.ajax({
        url : "<?php echo site_url('Admin/getBookedFancy?runner=');?>"+runner+"&market_id="+mid,
        type: "POST",
        success: function(data)
        {
          console.log(data);
          $("#fancyBookingTable").html(data);
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
          //alert("error");
        }
      });
    }
</script>