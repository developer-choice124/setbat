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
    <div id="alerttopright" class="myadmin-alert alert-success myadmin-alert-top-right">
      <a href="#" class="closed">&times;</a>
      <span id="placeMessage"></span>
    </div>
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
                     <?=$match->event_name;?> 
                    <div class="pull-right"><a href="<?=base_url('User/matches');?>">All Matches</a><a href="#" data-perform="panel-collapse"><i class="ti-minus"></i></a></div>
                </div>
                <div class="panel-body">
                    <div id="message"><?php if ($this->session->flashdata('message')) {
                          echo $this->session->flashdata('message');
                        } ?>
                    </div>
                    <div class="row">
                      <div class="col-md-8">
                        <div class="row">
                          <div class="col-md-12">
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
                                    <td style="background: #b5e0ff; cursor: pointer;" onclick="getBackLay('back','<?=$bprice?>','<?=$rname;?>','<?=$rid?>','<?=$rk;?>','matched')"><center><b><?=$back[0]['price'];?></b><br/><?=$back[0]['size'];?></center></td>
                                    <td style="background: #ffbfcd; cursor: pointer;" onclick="getBackLay('lay','<?=$lprice?>','<?=$rname;?>','<?=$rid?>','<?=$rk;?>','matched')"><center><b><?=$lay[0]['price'];?></b><br/><?=$lay[0]['size'];?></center></td>
                                    <td><center><b><?=$lay[1]['price'];?></b><br/><?=$lay[1]['size'];?></center></td>
                                    <td><center><b><?=$lay[2]['price'];?></b><br/><?=$lay[2]['size'];?></center></td>
                                  </tr>
                                <?php } ?>
                              </table>
                            </div>
                          </div>
                          <div class="col-md-12">
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
                                    <td><?=$f['RunnerName'];?></td>
                                    <td style="background-color: #ffbfcd; cursor: pointer; text-align: center;" onclick="getBackLay('lay','<?=$f['LaySize1'];?>','<?=$f['RunnerName'];?>','<?=$f['SelectionId']?>','<?=$fkey;?>','fancy','<?=$f['LayPrice1'];?>')"><b><?=$f['LayPrice1'];?></b><br><?=$f['LaySize1'];?></td>
                                    <td style="background-color: #b5e0ff; cursor: pointer; text-align: center;" onclick="getBackLay('back','<?=$f['BackSize1'];?>','<?=$f['RunnerName'];?>','<?=$f['SelectionId']?>','<?=$fkey;?>','fancy','<?=$f['BackPrice1'];?>')"><b><?=$f['BackPrice1'];?></b><br><?=$f['BackSize1'];?></td>
                                    <td></td>
                                  </tr>
                                  <?php } ?>
                                <?php } ?>
                                
                              </table>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="row">
                          <div class="loader"></div>
                          <div class="well" style="background: #b5e0ff; display: none;" id="backWell">
                            <div class="row">
                              <div class="col-md-4"><b>Back (Bet For)</b></div>
                              <div class="col-md-2"><b>Profit<br/><span style="color: green;" id="backBetProfit">0</span></b></div>
                              <div class="col-md-2"><b>Loss<br/><span style="color: red" id="backBetLoss">0</span></b></div>
                              <div class="col-md-4"><span id="backBetTeam"></span></div>
                            </div>
                            <form method="post" action="#">
                              <div class="row">
                                <div class="col-md-6">
                                  <label class="control-label">Odd </label>
                                  <input id="backOdd" type="text" value="0" name="odd" data-bts-button-down-class="btn btn-danger" data-bts-button-up-class="btn btn-success" onchange="profitLossBack(this.value,'yes')" onkeyup="profitLossBack(this.value,'yes')" onkeydown="profitLossBack(this.value,'yes')">
                                </div>
                                <div class="col-md-6">
                                  <label class="control-label">Stake </label>
                                  <input id="backStake" type="text" value="0" name="stake" data-bts-button-down-class="btn btn-danger" data-bts-button-up-class="btn btn-success" onchange="profitLossBack(this.value,'no')" onkeyup="profitLossBack(this.value,'no')" onkeydown="profitLossBack(this.value,'no')">
                                  <input type="hidden" name="match_id" id="backMatchId" value="<?=$match->event_id;?>">
                                  <input type="hidden" name="match_name" id="backMatchName" value="<?=$match->event_name;?>">
                                  <input type="hidden" name="team" id="backTeam">
                                  <input type="hidden" name="team_id" id="backTeamId">
                                  <input type="hidden" name="back_lay" id="backType" value="back">
                                  <input type="hidden" name="profit" id="backProfit">
                                  <input type="hidden" name="loss" id="backLoss">
                                  <input type="hidden" name="market" id="backMarket" value="<?=$match->mtype;?>">
                                  <input type="hidden" name="bet_type" id="backBet_type" value="">
                                  <input type="hidden" name="line" id="back_line" >
                                  <input type="hidden" name="changed" id="backChanged" value="no">
                                </div>
                              </div>
                              <br/>
                              <div class="row">
                                <div class="col-md-12">
                                  <button class="btn btn-primary" type="button" onclick="profitLossBack('<?=$chipSetting->chip_value_1;?>','no')"><?=$chipSetting->chip_name_1;?></button>
                                  <button class="btn btn-primary" type="button" onclick="profitLossBack('<?=$chipSetting->chip_value_2;?>','no')"><?=$chipSetting->chip_name_2;?></button>
                                  <button class="btn btn-primary" type="button" onclick="profitLossBack('<?=$chipSetting->chip_value_3;?>','no')"><?=$chipSetting->chip_name_3;?></button>
                                  <button class="btn btn-primary" type="button" onclick="profitLossBack('<?=$chipSetting->chip_value_4;?>','no')"><?=$chipSetting->chip_name_4;?></button>
                                  <button class="btn btn-primary" type="button" onclick="profitLossBack('<?=$chipSetting->chip_value_5;?>','no')"><?=$chipSetting->chip_name_5;?></button>
                                  <button class="btn btn-primary" type="button" onclick="profitLossBack('<?=$chipSetting->chip_value_6;?>','no')"><?=$chipSetting->chip_name_6;?></button>
                                  <button type="button" class="btn btn-primary" onclick="placeBetBack()">Place Bet</button>
                                  <button type="button" onclick="clearBackLay()" class="btn btn-primary">Clear</button>
                                  <button type="button" onclick="closeBackLay()" class="btn btn-primary">Close</button>
                                </div>
                              </div>
                            </form>
                          </div>
                          <div class="well" style="background: #ffbfcd; display: none;" id="layWell">
                            <div class="row">
                              <div class="col-md-4"><b>Lay (Bet For)</b></div>
                              <div class="col-md-2"><b>Profit<br/><span style="color: green;" id="layBetProfit">0</span></b></div>
                              <div class="col-md-2"><b>Loss<br/><span style="color: red" id="layBetLoss">0</span></b></div>
                              <div class="col-md-4"><span id="layBetTeam"></span></div>
                            </div>
                            <form method="post" action="#">
                              <div class="row">
                                <div class="col-md-6">
                                  <label class="control-label">Odd </label>
                                  <input id="layOdd" type="text" value="0" name="odd" data-bts-button-down-class="btn btn-danger" data-bts-button-up-class="btn btn-success" onchange="profitLossLay(this.value,'yes')" onkeyup="profitLossLay(this.value,'yes')" onkeydown="profitLossLay(this.value,'yes')">
                                </div>
                                <div class="col-md-6">
                                  <label class="control-label">Stake </label>
                                  <input id="layStake" type="text" value="0" name="stake" data-bts-button-down-class="btn btn-danger" data-bts-button-up-class="btn btn-success" onchange="profitLossLay(this.value,'no')" onkeyup="profitLossLay(this.value,'no')" onkeydown="profitLossLay(this.value,'no')">
                                  <input type="hidden" name="match_id" id="layMatchId" value="<?=$match->event_id;?>">
                                  <input type="hidden" name="match_name" id="layMatchName" value="<?=$match->event_name;?>">
                                  <input type="hidden" name="team" id="layTeam">
                                  <input type="hidden" name="team_id" id="layTeamId">
                                  <input type="hidden" name="back_lay" id="layType" value="lay">
                                  <input type="hidden" name="profit" id="layProfit">
                                  <input type="hidden" name="loss" id="layLoss">
                                  <input type="hidden" name="market" id="layMarket" value="<?=$match->mtype;?>">
                                  <input type="hidden" name="bet_type" id="layBet_type" value="">
                                  <input type="hidden" name="line" id="lay_line" >
                                  <input type="hidden" name="changed" id="layChanged" value="no">
                                </div>
                              </div>
                              <br/>
                              <div class="row">
                                <div class="col-md-12">
                                  <button class="btn btn-primary" type="button" onclick="profitLossLay('<?=$chipSetting->chip_value_1;?>','no')"><?=$chipSetting->chip_name_1;?></button>
                                  <button class="btn btn-primary" type="button" onclick="profitLossLay('<?=$chipSetting->chip_value_2;?>','no')"><?=$chipSetting->chip_name_2;?></button>
                                  <button class="btn btn-primary" type="button" onclick="profitLossLay('<?=$chipSetting->chip_value_3;?>','no')"><?=$chipSetting->chip_name_3;?></button>
                                  <button class="btn btn-primary" type="button" onclick="profitLossLay('<?=$chipSetting->chip_value_4;?>','no')"><?=$chipSetting->chip_name_4;?></button>
                                  <button class="btn btn-primary" type="button" onclick="profitLossLay('<?=$chipSetting->chip_value_5;?>','no')"><?=$chipSetting->chip_name_5;?></button>
                                  <button class="btn btn-primary" type="button" onclick="profitLossLay('<?=$chipSetting->chip_value_6;?>','no')"><?=$chipSetting->chip_name_6;?></button>
                                  <button type="button" onclick="placeBetLay()" class="btn btn-primary">Place Bet</button>
                                  <button type="button" onclick="clearBackLay()" class="btn btn-primary">Clear</button>
                                  <button type="button" onclick="closeBackLay()" class="btn btn-primary">Close</button>
                                </div>
                              </div>
                            </form>
                          </div>
                        </div>
                        <div class="white-box" id="betReload">
                          <!-- Nav tabs -->
                          <ul class="nav customtab nav-tabs" role="tablist">
                              <li role="presentation" class=""><a href="#unmatchedTab" aria-controls="home" role="tab" data-toggle="tab" aria-expanded="true"><span class="visible-xs"><img src="<?=base_url('assets/icons/u.png');?>" /></span><span class="hidden-xs"> Unmatched</span></a></li>
                              <li role="presentation" class="active"><a href="#matchedTab" aria-controls="profile" role="tab" data-toggle="tab" aria-expanded="false"><span class="visible-xs"><img src="<?=base_url('assets/icons/m.png');?>" /></span> <span class="hidden-xs">Matched</span></a></li>
                              <li role="presentation" class=""><a href="#fancyTab" aria-controls="messages" role="tab" data-toggle="tab" aria-expanded="false"><span class="visible-xs"><img src="<?=base_url('assets/icons/f.png');?>" /></span> <span class="hidden-xs">Fancy</span></a></li>
                          </ul>
                          <!-- Tab panes -->
                          <div class="tab-content">
                            <div role="tabpanel" class="tab-pane fade " id="unmatchedTab">
                              
                                <div class="table-responsive">
                                  <table class="table table-bordered">
                                    <tr class="headings">
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
                                        <td><a href="javascript:void(0)" onclick="deleteUnmatched('<?=$ub['id'];?>')"><i class="fa fa-trash-o"></i></a>&nbsp;&nbsp;<?=$ub['team'];?></td>
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

<script type="text/javascript">
    var match;
    var fancies;
    var bets;
    var scores;
    var pNl;
    var team;
    var team1Id = '<?=$t1id;?>';
    var team2Id = '<?=$t2id;?>';
    var team1pl = 0;
    var team1color;
    var team2pl = 0;
    var team2color;
    var matchId = '<?=$match->event_id;?>';
    var marketId = '<?=$match->market_id;?>'
    var ctype = 'back';
    var id = 1;
    var profit = 0;
    var loss = 0;
    var stake;
    var price;
    var curChips = '<?=$chips->current_chips ? $chips->current_chips : 0;?>';
    var maxchips = '<?=$chips->balanced_chips ? $chips->balanced_chips : 0;?>';
    var oddFancy;
    var finalTotal = 0;
    var selectedPrice;
    var currentPrice;
    var selectedKey;
    var selectedType;
    var team1;
    var team2;
    var changed = 'no';
    maxchips = parseInt(maxchips);
    var previousBalance = 0;
    var maxLimit = 0;
    var maxUse = 0;
    $(document).ready(function(){
      team1 = "#team0";
      team2 = "#team1"; 
      selectedPrice = 0;
      currentPrice = 0;
      selectedKey = 0;
      selectedType = 'back';
      $("input[name='odd']").TouchSpin();
      $("input[name='stake']").TouchSpin();
      showPL();
      match = setInterval("matchReload()", 1000);
      fancies = setInterval("fancyReload()", 3000);
      scores = setInterval("scoreReload()", 4000);
      bets = setInterval("betReload()", 30000);
      profitNLoss();
    });

    function profitNLoss() {
      $.ajax({
        url : "<?php echo site_url('User/profitNLoss?market_id=')?>"+marketId+"&team1="+team1Id+"&team2="+team2Id,
        type: "POST",
        success: function(data)
        {
          var obj = JSON.parse(data);
          team1color = obj.team1status;
          team2color = obj.team2status;
          team1pl = parseInt(obj.team1pl);
          team1pl = team1pl.toFixed(0);
          team2pl = parseInt(obj.team2pl);
          team2pl = team2pl.toFixed(0);
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
          
        }
      });
    }

    
    function closeBackLay() {
      $("#backWell").hide();
      $("#layWell").hide();
      profit = 0;
      loss = 0;
      $(team1).html("0");
      $(team2).html("0");
    }

    function clearBackLay() {
      $("#backStake").val(0);
      $("#layStake").val(0);
      profit = 0;
      loss = 0;
      $(team1).html("0");
      $(team2).html("0");
    }

    function getBackLay(type,price,team,teamId,teamKey,betType,line) {
      id = teamKey;
      ctype = type;
      oddFancy = betType;
      if(type == 'back')
      {
        stake = $("#backStake").val();
        $("#backWell").show();
        $("#layWell").hide();
        $("#backBetTeam").text(team);
        $("#backTeamId").val(teamId);
        if(betType == 'fancy') {
          $("#backOdd").val(line);
          $("#back_line").val(price);
          selectedPrice = line;
          selectedKey = teamKey;
          selectedType = type;
        } else {
          $("#backOdd").val(price);
          $("#back_line").val(line);
          selectedPrice = price;
          selectedKey = teamKey;
          selectedType = type;
        }
        
        $("#backTeam").val(team);
        $("#backBet_type").val(betType);
        profitLossBack(stake,changed);
        checkMax();
      } else {
        stake = $("#layStake").val();
        $("#layWell").show();
        $("#backWell").hide();
        $("#layBetTeam").text(team);
        $("#layTeamId").val(teamId);
        if(betType == 'fancy') {
          $("#layOdd").val(line);
          $("#lay_line").val(price);
          selectedPrice = line;
          selectedKey = teamKey;
          selectedType = type;
        } else {
          $("#layOdd").val(price);
          $("#lay_line").val(line);
          selectedPrice = price;
          selectedKey = teamKey;
          selectedType = type;
        }
        
        $("#layTeam").val(team);
        $("#layBet_type").val(betType);
        profitLossLay(stake,changed);
        checkMax();
      }
      showPL();
    }

    function stakeAdd(stake,type) {
      if(type == 'back') {
        $("#backStake").val(stake);
        profitLossBack(stake);
      } else {
        $("#layStake").val(stake);
        profitLossLay(stake,changed);
      }
    }

    function profitLossLayFancy(stake) {
      price = $("#lay_line").val();
      //stake = $("#layStake").val();
      var total = (price*stake);
      finalTotal = total/100;
      profit = parseInt(stake);
      profit = profit.toFixed(0);
      loss = parseInt(total/100);
      loss = loss.toFixed(0);
      if(loss > maxchips) {
        swal('loss chips can not be greater than balanced_chips');
        $("#layStake").val(0);
      } else {
        $("#layStake").val(stake);
        $("#layBetProfit").text(profit);
        $("#layBetLoss").text(loss);
        $("#layProfit").val(profit);
        $("#layLoss").val(loss);
      }
      showPL();
    }

    function profitLossBackFancy(stake) {
      price = $("#back_line").val();

      //stake = $("#backStake").val();
      var total = (price*stake);
      finalTotal = total/100;
      profit = total/100;
      profit = profit.toFixed(0);
      loss = parseInt(stake);
      loss = loss.toFixed(0);
      if(loss > maxchips) {
        swal('loss chips can not be greater than balanced_chips');
        $("#backStake").val(0);
      } else {
        $("#backStake").val(stake);
        $("#backBetProfit").text(profit);
        $("#backBetLoss").text(loss);
        $("#backProfit").val(profit);
        $("#backLoss").val(loss);
      }
      showPL();
    }

    function profitLossLay(stake,change) {
      
      if(change) {
        changed = change;
      }
      if(oddFancy == 'fancy') {
        profitLossLayFancy(stake);
      } else {
        price = $("#layOdd").val();
        //stake = $("#layStake").val();
        var total = (price*stake);
        finalTotal = total;
        profit = parseInt(stake);
        profit = profit.toFixed(0);
        loss = parseInt(total - stake);
        loss = loss.toFixed(0);
        $("#layStake").val(stake);
        $("#layBetProfit").text(profit);
        $("#layBetLoss").text(loss);
        $("#layProfit").val(profit);
        $("#layLoss").val(loss);
        $("#layChanged").val(changed);
        profitNLoss();
        showPL();
        checkMax();
      }
    }
    function profitLossBack(stake,change) {
      
      if(change) {
        changed = change;
      }
      if(oddFancy == 'fancy') {
        profitLossBackFancy(stake);
      } else {
        price = $("#backOdd").val();
        //stake = $("#backStake").val();
        var total = (price*stake);
        finalTotal = total;
        profit = total - stake;
        profit = profit.toFixed(0);
        loss = parseInt(stake);
        loss = loss.toFixed(0);
        $("#backStake").val(stake);
        $("#backBetProfit").text(profit);
        $("#backBetLoss").text(loss);
        $("#backProfit").val(profit);
        $("#backLoss").val(loss);
        $("#backChanged").val(changed);
        profitNLoss();
        showPL();
        checkMax();
      }
    }

    function checkPreviousBalance() {
      $.ajax({
        url : "<?php echo site_url('User/checkUserMaxLimit/')?>"+marketId,
        type: "POST",
        success: function(pbal)
        {
          previousBalance = parseInt(pbal);
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
          
        }
      });
    }

    function checkMax() {
      checkPreviousBalance();
      if(ctype == 'back')
      {
        if(id == 1){
          var totalTeam1 = parseInt(team1pl) - parseInt(loss);
          totalTeam1 = totalTeam1.toFixed(0);
          var totalTeam2 = parseInt(team2pl) + parseInt(profit);
          totalTeam2 = totalTeam2.toFixed(0);
          var t1loss = totalTeam1 >= 0 ? 0 : totalTeam1;
          var t2loss = totalTeam2 >= 0 ? 0 : totalTeam2;
          
        } else {
          var totalTeam1 = parseInt(team1pl) + parseInt(profit);
          totalTeam1 = totalTeam1.toFixed(0);
          var totalTeam2 = parseInt(team2pl) - parseInt(loss);
          totalTeam2 = totalTeam2.toFixed(0);
          var t1loss = totalTeam1 >= 0 ? 0 : totalTeam1;
          var t2loss = totalTeam2 >= 0 ? 0 : totalTeam2;
          
        }
        t1loss = parseInt(t1loss);
        t2loss = parseInt(t2loss);
        if(t1loss < 0 && t2loss < 0) {
          var finalLoss = t1loss > t2loss ? t1loss : t2loss;
        }
        else if(t1loss < 0 && t2loss >= 0) {
          var finalLoss = t1loss;
        } else if(t1loss >= 0 && t2loss < 0){
          var finalLoss = t2loss;
        }
        maxLimit = parseInt(finalLoss);
      } else {
        if(id == 1){
          var totalTeam1 = parseInt(team1pl) + parseInt(profit);
          totalTeam1 = totalTeam1.toFixed(0);
          var totalTeam2 = parseInt(team2pl) - parseInt(loss);
          totalTeam2 = totalTeam2.toFixed(0);
          var t1loss = totalTeam1 >= 0 ? 0 : totalTeam1;
          var t2loss = totalTeam2 >= 0 ? 0 : totalTeam2;
        } else {
          var totalTeam1 = parseInt(team1pl) - parseInt(loss);
          totalTeam1 = totalTeam1.toFixed(0);
          var totalTeam2 = parseInt(team2pl) + parseInt(profit);
          totalTeam2 = totalTeam2.toFixed(0);
          var t1loss = totalTeam1 >= 0 ? 0 : totalTeam1;
          var t2loss = totalTeam2 >= 0 ? 0 : totalTeam2;
        }
        t1loss = parseInt(t1loss);
        t2loss = parseInt(t2loss);
        if(t1loss < 0 && t2loss < 0) {
          var finalLoss = t1loss > t2loss ? t1loss : t2loss;
        }
        else if(t1loss < 0 && t2loss >= 0) {
          var finalLoss = t1loss;
        } else if(t1loss >= 0 && t2loss < 0){
          var finalLoss = t2loss;
        }
        maxLimit = parseInt(finalLoss);
      }
      maxLimit = Math.abs(maxLimit);
      curChips = parseInt(curChips);
      maxUse = previousBalance - maxLimit;
      if(maxUse < 0) {
        $("#layStake").val(0);
        $("#backStake").val(0);
        swal("max stake should not be greater than balanced_chips");
      }
    }

    function showPL() {
      var team1 = "#team0";
      var team2 = "#team1";
      if(ctype == 'back')
      {
        if(id == 1){
          // if(team1color == 'p') {
          //   var pp = team1pl - loss;
          //   pp = Math.abs(pp);
          //   $(team1).html("<span class='text-danger'>"+loss+"</span>");
          // } else {
          //   $(team1).html("<span class='text-danger'>"+loss+"</span>");
          // }
          var totalTeam1 = parseInt(team1pl) - parseInt(loss);
          totalTeam1 = totalTeam1.toFixed(0);
          var totalTeam2 = parseInt(team2pl) + parseInt(profit);
          totalTeam2 = totalTeam2.toFixed(0);
          var class1 = totalTeam1 >= 0 ? 'text-success' : 'text-danger';
          var class2 = totalTeam2 >= 0 ? 'text-success' : 'text-danger';
          $(team1).html("<span class='"+class1+"'>"+totalTeam1+"</span>");
          $(team2).html("<span class='"+class2+"'>"+totalTeam2+"</span>");
        } else {
          var totalTeam1 = parseInt(team1pl) + parseInt(profit);
          totalTeam1 = totalTeam1.toFixed(0);
          var totalTeam2 = parseInt(team2pl) - parseInt(loss);
          totalTeam2 = totalTeam2.toFixed(0);
          var class1 = totalTeam1 >= 0 ? 'text-success' : 'text-danger';
          var class2 = totalTeam2 >= 0 ? 'text-success' : 'text-danger';
          $(team2).html("<span class='"+class2+"'>"+totalTeam2+"</span>");
          $(team1).html("<span class='"+class1+"'>"+totalTeam1+"</span>");
        }
      } else {
        if(id == 1){
          var totalTeam1 = parseInt(team1pl) + parseInt(profit);
          totalTeam1 = totalTeam1.toFixed(0);
          var totalTeam2 = parseInt(team2pl) - parseInt(loss);
          totalTeam2 = totalTeam2.toFixed(0);
          var class1 = totalTeam1 >= 0 ? 'text-success' : 'text-danger';
          var class2 = totalTeam2 >= 0 ? 'text-success' : 'text-danger';
          $(team2).html("<span class='"+class2+"'>"+totalTeam2+"</span>");
          $(team1).html("<span class='"+class1+"'>"+totalTeam1+"</span>");
        } else {
          var totalTeam1 = parseInt(team1pl) - parseInt(loss);
          totalTeam1 = totalTeam1.toFixed(0);
          var totalTeam2 = parseInt(team2pl) + parseInt(profit);
          totalTeam2 = totalTeam2.toFixed(0);
          var class1 = totalTeam1 >= 0 ? 'text-success' : 'text-danger';
          var class2 = totalTeam2 >= 0 ? 'text-success' : 'text-danger';
          $(team1).html("<span class='"+class1+"'>"+totalTeam1+"</span>");
          $(team2).html("<span class='"+class2+"'>"+totalTeam2+"</span>");
        }
      }
    }

    function updateMainBalance() {
      $.ajax({
        url : "<?php echo site_url('User/updateMainBalance');?>",
        type: "POST",
        success: function(data)
        {
          $("#mainBalance").html(data);
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
          //alert("error");
        }
      });
    }

    function matchReload() {
      var mid = "<?=$match->market_id;?>";
      $.ajax({
        url : "<?php echo site_url('User/matchReload?market_id=')?>"+mid+"&selectedKey="+selectedKey+"&selectedType="+selectedType,
        type: "POST",
        success: function(data)
        {
          //console.log(data);
          var obj = JSON.parse(data);
          $("#singleMatchTable").html(obj.mdata);
          currentPrice = obj.currentPrice;
          currentPrice = parseInt(currentPrice);
          showPL();
          updateMainBalance();
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
          //alert("error");
        }
      });
    }

    function fancyReload() {
      var mid = "<?=$match->market_id;?>";
      var fid = id;
      $.ajax({
        url : "<?php echo site_url('User/fancyReload?market_id=')?>"+mid+"&fancy_id="+id,
        type: "POST",
        success: function(msg)
        {
          var obj = JSON.parse(msg);
          $("#fancyTable").html(obj.fancy);
          var showed = obj.show;
          if(oddFancy == 'fancy') {
            if(showed == 'no') {
              //closeBackLay();
            }
          }
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
          //alert("error");
        }
      });
    }

    function scoreReload() {
      var mid = "<?=$match->market_id;?>";
      $.ajax({
        url : "<?php echo site_url('User/scoreReload?market_id=')?>"+mid,
        type: "POST",
        success: function(msg)
        {
          $("#scoreTable").html(msg);
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
          //alert("error");
        }
      });
    }

    function placeBetLay(){
      var odd = $("#layOdd").val();
      if(odd) {
        var maxStake = $("#layStake").val();
        maxStake = parseInt(maxStake).toFixed(0);
        if(maxStake >= 100 && maxStake <= 500000) {
          $('.loader').show();
          setTimeout(function(){
            layBetRequest();
          }, 3000);
        } else {
          alert('bet stake should be greater than 100 & less than 500000'); 
        }
      } else {
        alert('odds can not be empty');  
      }
    }

    function layBetRequest() {
      var match_id = $("#layMatchId").val(); 
      var match_name = $("#layMatchName").val();  
      var team = $("#layTeam").val();        
      var team_id = $("#layTeamId").val();      
      var market = $("#layMarket").val();      
      var back_lay = $("#layType").val();     
      var odd = $("#layOdd").val();
      var stake = $("#layStake").val();        
      var profit = $("#layProfit").val();       
      var loss = $("#layLoss").val();         
      var bet_type = $("#layBet_type").val();
      var line = $("#lay_line").val(); 
      var change = $("#layChanged").val();
      $.ajax({
        url : "<?php echo site_url('User/placeBet')?>",
        type: "POST",
        data: {
          match_id: match_id,
          market_id: marketId,
          match_name: match_name,
          team: team,
          team_id: team_id,
          market: market,
          back_lay: back_lay,
          odd: odd, 
          stake: stake,
          profit: profit,
          loss: loss,
          bet_type: bet_type,
          line: line,
          change: change
        },
        success: function(response)
        {
          //console.log(response);
          $('.loader').hide();
          var obj = JSON.parse(response);
          msgg = obj.message;
          $("#placeMessage").text(msgg);
          $("#alerttopright").fadeToggle(350);
          betReload();
          closeBackLay();
          clearBackLay();
          profitNLoss();
          showPL();
          //location.reload();
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
          //alert("error");
        }
      });
    }

    function placeBetBack(){
     var odd = $("#backOdd").val();
     //console.log(finalTotal);
      if(odd) {
        var maxStake = $("#backStake").val();
        maxStake = parseInt(maxStake).toFixed(0);
        if(maxStake >= 100 && maxStake <= 500000) {
          $('.loader').show();
          setTimeout(function(){
            backBetRequest();
          }, 3000);
        } else {
          alert('bet stake should be greater than 100 & less than 500000'); 
        }
      } else {
        alert('odds can not be empty');  
      }
    }

    function backBetRequest() {
      var match_id = $("#backMatchId").val(); 
      var match_name = $("#backMatchName").val();  
      var team = $("#backTeam").val();        
      var team_id = $("#backTeamId").val();      
      var market = $("#backMarket").val();      
      var back_lay = $("#backType").val();     
      var odd = $("#backOdd").val();
      var stake = $("#backStake").val();        
      var profit = $("#backProfit").val();       
      var loss = $("#backLoss").val();         
      var bet_type = $("#backBet_type").val();
      var line = $("#back_line").val();
      var change = $("#backChanged").val();
      $.ajax({
        url : "<?php echo site_url('User/placeBet')?>",
        type: "POST",
        data: {
          market_id: marketId,
          match_id: match_id,
          match_name: match_name,
          team: team,
          team_id: team_id,
          market: market,
          back_lay: back_lay,
          odd: odd, 
          stake: stake,
          profit: profit,
          loss: loss,
          bet_type: bet_type,
          line:line,
          change: change
        },
        success: function(response)
        {
          //console.log(response);
          $('.loader').hide();
          var obj = JSON.parse(response);
          msgg = obj.message;
          $("#placeMessage").text(msgg);
          $("#alerttopright").fadeToggle(350);
          betReload();
          closeBackLay();
          clearBackLay()
          profitNLoss();
          showPL();
          //location.reload();
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
        url : "<?php echo site_url('User/betReload?market_id=')?>"+mid,
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
    function deleteUnmatched(bid) {
      $.ajax({
        url : "<?php echo site_url('User/deleteUnmatched?bet_id=')?>"+bid,
        type: "POST",
        success: function(data)
        {
          setTimeout(function(){ betReload(); }, 1000);
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
</script>