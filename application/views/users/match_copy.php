<script src="<?php echo base_url('assets/backend/js/jquery-3.3.1.min.js')?>"></script>

<style type="text/css">
  .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
    padding: 10px 8px !important;
    
  }
  hr {
      margin: 0px !important;
      padding: 0 !important;
    }
</style>
<?php $teams = $match['team']; //print_r($teams);die;?>
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
                     <?=$match['competition']['name'];?> 
                    <div class="pull-right"><a href="<?=base_url('User/matches');?>">All Matches</a><a href="#" data-perform="panel-collapse"><i class="ti-minus"></i></a></div>
                </div>
                <div class="panel-body">
                    <div><?php if ($this->session->flashdata('message')) {
                          echo $this->session->flashdata('message');
                        } ?>
                    </div>
                    <div class="row">
                      <div class="col-md-7">
                        <div class="table-responsive" id="singleMatchTable">
                          <table class="table table-bordered table-condensed" width="100%">
                            <tr>
                              <th width="60%" colspan="3" style="border: none !important;"><b style="color: red;">Min stake:100 Max stake:200000</b></th>
                              <th width="10%" style="background: #2c5ca9; color: white; border: none !important;"><center>back</center></th>
                              <th width="10%" style="background: red; color: white; border: none !important;"><center>lay</center></th>
                              <th width="10%" style="border: none !important;"></th>
                              <th width="10%" style="border: none !important;"></th>
                            </tr>
                            <tr>

                              <?php foreach($match['runners'] as $rk => $r):?>
                                <tr>
                                  <td id="<?='rname-'.$rk;?>"><b><?=$r['name'];?> <span class="pull-right" id="<?='teamKey-'.$rk;?>">0</span></b></td>
                                  <?php if($r['back']) {
                                    $rback = array_reverse($r['back']);
                                    $lastback = end($rback);
                                    foreach($rback as $rb):
                                      if($rb == $lastback) { ?>
                                        <td style="background: #b5e0ff; cursor: pointer;"><center><span onclick="getBackLay('back','<?=$rb['price'];?>','<?=$r['name'];?>','<?='teamKey-'.$rk;?>','<?=$r['id'];?>')"><?='<b>'.$rb['price'].'</b><br/>'.$rb['size'];?></span></center></td>
                                      <?php } else { ?>
                                        <td style="cursor: pointer;"><center><span><?='<b>'.$rb['price'].'</b><br/>'.$rb['size'];?></span></center></td>
                                      <?php } ?>
                                    <?php endforeach; ?>
                                  <?php } ?>
                                  <?php if($r['lay']) {
                                    $rlay = $r['lay'];
                                    $firstlay = $rlay[0];
                                    foreach($rlay as $rl):
                                      if($rl == $firstlay) { ?>
                                        <td style="background: #ffbfcd; cursor: pointer;" onc><center><span onclick="getBackLay('lay','<?=$rl['price'];?>','<?=$r['name'];?>','<?='teamKey-'.$rk;?>','<?=$r['id'];?>')"><?='<b>'.$rl['price'].'</b><br/>'.$rl['size'];?></span></center></td>
                                      <?php } else { ?>
                                        <td style="cursor: pointer;"><center><span><?='<b>'.$rl['price'].'</b><br/>'.$rl['size'];?></span></center></td>
                                      <?php } ?>
                                    <?php endforeach; ?>
                                  <?php } ?>
                                </tr>
                              <?php endforeach;?>
                            </tr>
                          </table>
                        </div>
                        <div class="row">
                          <div class="col-md-12 table-responsive">
                            <table class="table table-condensed table-bordered" width="100%">
                              <tr>
                                <td width="80%"></td>
                                <td style="background: red; color: white;"><center><b>NO(L)</b></center></td>
                                <td style="background: #2c5ca9; color: white;"><center><b>YES(B)</b></center></td>
                              </tr>
                              <?php foreach ($fancy as $fkey => $f) { 
                                if($f['runners'][0]['lay'] || $f['runners'][0]['back']) { ?>
                                  <tr>
                                    <td><?=$f['name'];?></td>
                                    <td style="background: #ffbfcd" onclick="getBackLay('lay','<?=$f['runners'][0]['lay'][0]['line'];?>','<?=$f['name'];?>','0','<?=$f['id'];?>')"><center><b><?=$f['runners'][0]['lay'][0]['line'];?></b><hr/><?=$f['runners'][0]['lay'][0]['price'];?></center></td>
                                    <td style="background: #b5e0ff"><?=$f['runners'][0]['back'][0]['line'];?><br/><?=$f['runners'][0]['back'][0]['price'];?></td>
                                  </tr>
                              <?php } } ?>
                            </table>
                          </div>
                        </div>
                      </div>
                      
                      <div class="col-md-5">
                        <div class="well" style="background: #b5e0ff; display: none;" id="backWell">
                            <div class="row">
                              <div class="col-md-4"><b>Lay (Bet For)</b></div>
                              <div class="col-md-2"><b>Profit<br/><span style="color: green;" id="backBetProfit">0</span></b></div>
                              <div class="col-md-2"><b>Loss<br/><span style="color: red" id="backBetLoss">0</span></b></div>
                              <div class="col-md-4"><span id="backBetTeam"></span></div>
                            </div>
                            <form method="post" action="<?=base_url('User/placeBet');?>">
                              <div class="row">
                                <div class="col-md-6">
                                  <label class="control-label">Odd </label>
                                  <input id="backOdd" type="text" value="0" name="odd" data-bts-button-down-class="btn btn-danger" data-bts-button-up-class="btn btn-success" onchange="profitLossBack(this.value)" onkeyup="profitLossBack(this.value)" onkeydown="profitLossBack(this.value)">
                                </div>
                                <div class="col-md-6">
                                  <label class="control-label">Stake </label>
                                  <input id="backStake" type="text" value="0" name="stake" data-bts-button-down-class="btn btn-danger" data-bts-button-up-class="btn btn-success" onchange="profitLossBack(this.value)" onkeyup="profitLossBack(this.value)" onkeydown="profitLossBack(this.value)">
                                  <input type="hidden" name="match_id" id="backMatchId" value="<?=$match['event']['id'];?>">
                                  <input type="hidden" name="match_name" id="backMatchName" value="<?=$match['event']['name'];?>">
                                  <input type="hidden" name="team" id="backTeam">
                                  <input type="hidden" name="team_id" id="backTeamId">
                                  <input type="hidden" name="back_lay" id="backType" value="back">
                                  <input type="hidden" name="profit" id="backProfit">
                                  <input type="hidden" name="loss" id="backLoss">
                                  <input type="hidden" name="market" id="backMarket" value="<?=$match['name'];?>">
                                  <input type="hidden" name="bet_type" value="matched">
                                  <?php foreach($teams as $tm):?>
                                    <input type="hidden" name="allteam_id[]" value="<?php print($tm['id']);?>">
                                    <input type="hidden" name="allteam_name[]" value="<?php print($tm['name']);?>">
                                  <?php endforeach;?>
                                </div>
                              </div>
                              <br/>
                              <div class="row">
                                <div class="col-md-12">
                                  <button class="btn btn-primary" type="button" onclick="profitLossBack('<?=$chipSetting->chip_value_1;?>')"><?=$chipSetting->chip_name_1;?></button>
                                  <button class="btn btn-primary" type="button" onclick="profitLossBack('<?=$chipSetting->chip_value_2;?>')"><?=$chipSetting->chip_name_2;?></button>
                                  <button class="btn btn-primary" type="button" onclick="profitLossBack('<?=$chipSetting->chip_value_3;?>')"><?=$chipSetting->chip_name_3;?></button>
                                  <button class="btn btn-primary" type="button" onclick="profitLossBack('<?=$chipSetting->chip_value_4;?>')"><?=$chipSetting->chip_name_4;?></button>
                                  <button class="btn btn-primary" type="button" onclick="profitLossBack('<?=$chipSetting->chip_value_5;?>')"><?=$chipSetting->chip_name_5;?></button>
                                  <button class="btn btn-primary" type="button" onclick="profitLossBack('<?=$chipSetting->chip_value_6;?>')"><?=$chipSetting->chip_name_6;?></button>
                                  <button type="submit" class="btn btn-primary">Place Bet</button>
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
                            <form method="post" action="<?=base_url('User/placeBet');?>">
                              <div class="row">
                                <div class="col-md-6">
                                  <label class="control-label">Odd </label>
                                  <input id="layOdd" type="text" value="0" name="odd" data-bts-button-down-class="btn btn-danger" data-bts-button-up-class="btn btn-success" onchange="profitLossLay(this.value)" onkeyup="profitLossLay(this.value)" onkeydown="profitLossLay(this.value)">
                                </div>
                                <div class="col-md-6">
                                  <label class="control-label">Stake </label>
                                  <input id="layStake" type="text" value="0" name="stake" data-bts-button-down-class="btn btn-danger" data-bts-button-up-class="btn btn-success" onchange="profitLossLay(this.value)" onkeyup="profitLossLay(this.value)" onkeydown="profitLossLay(this.value)">
                                  <input type="hidden" name="match_id" id="layMatchId" value="<?=$match['event']['id'];?>">
                                  <input type="hidden" name="match_name" id="layMatchName" value="<?=$match['event']['name'];?>">
                                  <input type="hidden" name="team" id="layTeam">
                                  <input type="hidden" name="team_id" id="layTeamId">
                                  <input type="hidden" name="back_lay" id="layType" value="lay">
                                  <input type="hidden" name="profit" id="layProfit">
                                  <input type="hidden" name="loss" id="layLoss">
                                  <input type="hidden" name="market" id="layMarket" value="<?=$match['name'];?>">
                                  <input type="hidden" name="bet_type" value="matched">
                                  <?php foreach($teams as $tm):?>
                                    <input type="hidden" name="allteam_id[]" value="<?php print($tm['id']);?>">
                                    <input type="hidden" name="allteam_name[]" value="<?php print($tm['name']);?>">
                                  <?php endforeach;?>
                                </div>
                              </div>
                              <br/>
                              <div class="row">
                                <div class="col-md-12">
                                  <button class="btn btn-primary" type="button" onclick="profitLossLay('<?=$chipSetting->chip_value_1;?>')"><?=$chipSetting->chip_name_1;?></button>
                                  <button class="btn btn-primary" type="button" onclick="profitLossLay('<?=$chipSetting->chip_value_2;?>')"><?=$chipSetting->chip_name_2;?></button>
                                  <button class="btn btn-primary" type="button" onclick="profitLossLay('<?=$chipSetting->chip_value_3;?>')"><?=$chipSetting->chip_name_3;?></button>
                                  <button class="btn btn-primary" type="button" onclick="profitLossLay('<?=$chipSetting->chip_value_4;?>')"><?=$chipSetting->chip_name_4;?></button>
                                  <button class="btn btn-primary" type="button" onclick="profitLossLay('<?=$chipSetting->chip_value_5;?>')"><?=$chipSetting->chip_name_5;?></button>
                                  <button class="btn btn-primary" type="button" onclick="profitLossLay('<?=$chipSetting->chip_value_6;?>')"><?=$chipSetting->chip_name_6;?></button>
                                  <button type="submit" class="btn btn-primary">Place Bet</button>
                                </div>
                              </div>
                            </form>
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
    var team;
    var teamKey;
    var profit;
    var loss;
    var stake;
    var price;
    $(document).ready(function(){
      $("input[name='odd']").TouchSpin();
      $("input[name='stake']").TouchSpin();
      //match = setInterval("matchReload()", 2000);
      

    });
    function getBackLay(type,price,team,teamKey,teamId) {
      if(type == 'back')
      {
        stake = $("#backStake").val();
        $("#backWell").show();
        $("#layWell").hide();
        $("#backBetTeam").text(team);
        $("#backTeamId").val(teamId);
        $("#backOdd").val(price);
        $("#backTeam").val(team);
        profitLossBack(stake);
      } else {
        stake = $("#layStake").val();
        $("#layWell").show();
        $("#backWell").hide();
        $("#layBetTeam").text(team);
        $("#layTeamId").val(teamId);
        $("#layOdd").val(price);
        $("#layTeam").val(team);
        profitLossLay(stake);
      }
    }

    function stakeAdd(stake,type) {
      if(type == 'back') {
        $("#backStake").val(stake);
        profitLossBack(stake);
      } else {
        $("#layStake").val(stake);
        profitLossLay(stake);
      }
    }

    function profitLossLay(stake) {
      price = $("#layOdd").val();
      //stake = $("#layStake").val();
      var total = (price*stake);
      profit = parseInt(stake);
      profit = profit.toFixed(0);
      loss = parseInt(total - stake);
      loss = loss.toFixed(0);
      $("#layStake").val(stake);
      $("#layBetProfit").text(profit);
      $("#layBetLoss").text(loss);
      $("#layProfit").val(profit);
      $("#layLoss").val(loss);
    }

    function profitLossBack(stake) {
      price = $("#backOdd").val();
      //stake = $("#backStake").val();
      var total = (price*stake);
      profit = total - stake;
      profit = profit.toFixed(0);
      loss = parseInt(stake);
      loss = loss.toFixed(0);
      $("#backStake").val(stake);
      $("#backBetProfit").text(profit);
      $("#backBetLoss").text(loss);
      $("#backProfit").val(profit);
      $("#backLoss").val(loss);
    }

    function matchReload() {
      var mid = "<?=$match['event']['id'];?>";
      var type = "<?=$type;?>";
      $.ajax({
        url : "<?php echo site_url('User/matchReload?matchId=')?>"+mid+"&type="+type,
        type: "POST",
        success: function(data)
        {
          $("#singleMatchTable").html(data);
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
          alert("error");
        }
      });
    }
</script>