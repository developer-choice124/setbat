<?php
include_once 'match_style.php';
?>

<?php $teams = json_decode($match->teams, true); //print_r($odds);die;?>
<div class="container-fluid">
    <div id="alerttopright" class="myadmin-alert alert-success myadmin-alert-top-right">
        <a href="#" class="closed">&times;</a>
        <span id="placeMessage"></span>
    </div>
    <div class="row bg-title">

    </div>
    <!-- .row -->
    <div class="row">
        <?php
        include_once 'left-menu.php';
        ?>
        <div class="col-sm-10">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <?= $match->event_name; ?> 
                    <div class="pull-right"><a href="<?= base_url('MsUser/matches'); ?>">All Matches</a><a href="#" data-perform="panel-collapse"><i class="ti-minus"></i></a></div>
                </div>
                <div class="panel-body">
                    <div id="message"><?php
                        if ($this->session->flashdata('message')) {
                            echo $this->session->flashdata('message');
                        }
                        ?>
                    </div>
                    <div class="row shah">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-12">
                                    
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
                                            <?php
                                            $uid = $this->session->userdata('user_id');
                                            $t1id = $teams[0]['id'];
                                            $t2id = $teams[1]['id'];
                                            $runners = $odds['runners'];
                                            foreach ($runners as $rk => $r) {
                                                $back = $r['ex']['availableToBack'];
                                                $bprice = $back[0]['price'];
                                                $lay = $r['ex']['availableToLay'];
                                                $lprice = $lay[0]['price'];
                                                //print_r($lay[0]['price']);die;
                                                $rid = $r['selectionId'];
                                                $mid = $match->event_id;
                                                foreach ($teams as $tkey => $t) {
                                                    if ($t['id'] == $rid) {
                                                        $rname = $t['name'];
                                                    }
                                                }
                                                ?>
                                                <tr>
                                                    <td><b><?= $rname; ?></b><span class="pull-right" id="<?= 'team' . $rk; ?>"></span></td>
                                                    <td><center><b><?= $back[2]['price']; ?></b><br><?= $back[2]['size']; ?></center></td>
                                                <td><center><b><?= $back[1]['price']; ?></b><br/><?= $back[1]['size']; ?></center></td>
                                                <td style="background: #b5e0ff; cursor: pointer;" onclick="getBackLay('back', '<?= $bprice ?>', '<?= $rname; ?>', '<?= $rid ?>', '<?= $rk; ?>', 'matched')"><center><b><?= $back[0]['price']; ?></b><br/><?= $back[0]['size']; ?></center></td>
                                                <td style="background: #ffbfcd; cursor: pointer;" onclick="getBackLay('lay', '<?= $lprice ?>', '<?= $rname; ?>', '<?= $rid ?>', '<?= $rk; ?>', 'matched')"><center><b><?= $lay[0]['price']; ?></b><br/><?= $lay[0]['size']; ?></center></td>
                                                <td><center><b><?= $lay[1]['price']; ?></b><br/><?= $lay[1]['size']; ?></center></td>
                                                <td><center><b><?= $lay[2]['price']; ?></b><br/><?= $lay[2]['size']; ?></center></td>
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
                                            <?php
                                            $did = array();
                                            foreach ($dfancy as $dkey => $d) {
                                                $did[] = $d['fancy_id'];
                                            }
                                            ?>
                                            <?php $fancies = $fancy['session']; ?>
                                            <?php foreach ($fancies as $fkey => $f) { ?>
                                                <?php if (in_array($f['SelectionId'], $did)) { ?>
                                                    <tr>
                                                        <td><?= $f['RunnerName']; ?></td>
                                                        <td style="background-color: #ffbfcd; cursor: pointer; text-align: center;" onclick="getBackLay('lay', '<?= $f['LaySize1']; ?>', '<?= $f['RunnerName']; ?>', '<?= $f['SelectionId'] ?>', '<?= $fkey; ?>', 'fancy', '<?= $f['LayPrice1']; ?>')"><b><?= $f['LayPrice1']; ?></b><br><?= $f['LaySize1']; ?></td>
                                                        <td style="background-color: #b5e0ff; cursor: pointer; text-align: center;" onclick="getBackLay('back', '<?= $f['BackSize1']; ?>', '<?= $f['RunnerName']; ?>', '<?= $f['SelectionId'] ?>', '<?= $fkey; ?>', 'fancy', '<?= $f['BackPrice1']; ?>')"><b><?= $f['BackPrice1']; ?></b><br><?= $f['BackSize1']; ?></td>
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
                                                <input id="backOdd" 
                                                       readonly="readonly"
                                                       type="text" value="0" name="odd" data-bts-button-down-class="btn btn-danger" data-bts-button-up-class="btn btn-success" onchange="profitLossBack(this.value, 'yes')" onkeyup="profitLossBack(this.value, 'yes')" onkeydown="profitLossBack(this.value, 'yes')">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="control-label">Stake </label>
                                                <input id="backStake" type="text" value="0" name="stake" data-bts-button-down-class="btn btn-danger" data-bts-button-up-class="btn btn-success" onchange="profitLossBack(this.value, 'no')" onkeyup="profitLossBack(this.value, 'no')" onkeydown="profitLossBack(this.value, 'no')">
                                                <input type="hidden" name="match_id" id="backMatchId" value="<?= $match->event_id; ?>">
                                                <input type="hidden" name="match_name" id="backMatchName" value="<?= $match->event_name; ?>">
                                                <input type="hidden" name="team" id="backTeam">
                                                <input type="hidden" name="team_id" id="backTeamId">
                                                <input type="hidden" name="back_lay" id="backType" value="back">
                                                <input type="hidden" name="profit" id="backProfit">
                                                <input type="hidden" name="loss" id="backLoss">
                                                <input type="hidden" name="market" id="backMarket" value="<?= $match->mtype; ?>">
                                                <input type="hidden" name="bet_type" id="backBet_type" value="">
                                                <input type="hidden" name="line" id="back_line" >
                                                <input type="hidden" name="changed" id="backChanged" value="no">
                                            </div>
                                        </div>
                                        <br/>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <button class="btn btn-primary" type="button" onclick="profitLossBack('<?= $chipSetting->chip_value_1; ?>', 'no')"><?= $chipSetting->chip_name_1; ?></button>
                                                <button class="btn btn-primary" type="button" onclick="profitLossBack('<?= $chipSetting->chip_value_2; ?>', 'no')"><?= $chipSetting->chip_name_2; ?></button>
                                                <button class="btn btn-primary" type="button" onclick="profitLossBack('<?= $chipSetting->chip_value_3; ?>', 'no')"><?= $chipSetting->chip_name_3; ?></button>
                                                <button class="btn btn-primary" type="button" onclick="profitLossBack('<?= $chipSetting->chip_value_4; ?>', 'no')"><?= $chipSetting->chip_name_4; ?></button>
                                                <button class="btn btn-primary" type="button" onclick="profitLossBack('<?= $chipSetting->chip_value_5; ?>', 'no')"><?= $chipSetting->chip_name_5; ?></button>
                                                <button class="btn btn-primary" type="button" onclick="profitLossBack('<?= $chipSetting->chip_value_6; ?>', 'no')"><?= $chipSetting->chip_name_6; ?></button>
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
                                                <input id="layOdd" 
                                                       readonly="readonly"
                                                       type="text" value="0" name="odd" data-bts-button-down-class="btn btn-danger" data-bts-button-up-class="btn btn-success" onchange="profitLossLay(this.value, 'yes')" onkeyup="profitLossLay(this.value, 'yes')" onkeydown="profitLossLay(this.value, 'yes')">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="control-label">Stake </label>
                                                <input id="layStake"
                                                      
                                                       type="text" value="0" name="stake" data-bts-button-down-class="btn btn-danger" data-bts-button-up-class="btn btn-success" onchange="profitLossLay(this.value, 'no')" onkeyup="profitLossLay(this.value, 'no')" onkeydown="profitLossLay(this.value, 'no')">
                                                <input type="hidden" name="match_id" id="layMatchId" value="<?= $match->event_id; ?>">
                                                <input type="hidden" name="match_name" id="layMatchName" value="<?= $match->event_name; ?>">
                                                <input type="hidden" name="team" id="layTeam">
                                                <input type="hidden" name="team_id" id="layTeamId">
                                                <input type="hidden" name="back_lay" id="layType" value="lay">
                                                <input type="hidden" name="profit" id="layProfit">
                                                <input type="hidden" name="loss" id="layLoss">
                                                <input type="hidden" name="market" id="layMarket" value="<?= $match->mtype; ?>">
                                                <input type="hidden" name="bet_type" id="layBet_type" value="">
                                                <input type="hidden" name="line" id="lay_line" >
                                                <input type="hidden" name="changed" id="layChanged" value="no">
                                            </div>
                                        </div>
                                        <br/>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <button class="btn btn-primary" type="button" onclick="profitLossLay('<?= $chipSetting->chip_value_1; ?>', 'no')"><?= $chipSetting->chip_name_1; ?></button>
                                                <button class="btn btn-primary" type="button" onclick="profitLossLay('<?= $chipSetting->chip_value_2; ?>', 'no')"><?= $chipSetting->chip_name_2; ?></button>
                                                <button class="btn btn-primary" type="button" onclick="profitLossLay('<?= $chipSetting->chip_value_3; ?>', 'no')"><?= $chipSetting->chip_name_3; ?></button>
                                                <button class="btn btn-primary" type="button" onclick="profitLossLay('<?= $chipSetting->chip_value_4; ?>', 'no')"><?= $chipSetting->chip_name_4; ?></button>
                                                <button class="btn btn-primary" type="button" onclick="profitLossLay('<?= $chipSetting->chip_value_5; ?>', 'no')"><?= $chipSetting->chip_name_5; ?></button>
                                                <button class="btn btn-primary" type="button" onclick="profitLossLay('<?= $chipSetting->chip_value_6; ?>', 'no')"><?= $chipSetting->chip_name_6; ?></button>
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
                                    <li role="presentation" class=""><a href="#unmatchedTab" aria-controls="home" role="tab" data-toggle="tab" aria-expanded="true"><span class="visible-xs"><img src="<?= base_url('assets/icons/u.png'); ?>" /></span><span class="hidden-xs"> Unmatched</span></a></li>
                                    <li role="presentation" class="active"><a href="#matchedTab" aria-controls="profile" role="tab" data-toggle="tab" aria-expanded="false"><span class="visible-xs"><img src="<?= base_url('assets/icons/m.png'); ?>" /></span> <span class="hidden-xs">Matched</span></a></li>
                                    <li role="presentation" class=""><a href="#fancyTab" aria-controls="messages" role="tab" data-toggle="tab" aria-expanded="false"><span class="visible-xs"><img src="<?= base_url('assets/icons/f.png'); ?>" /></span> <span class="hidden-xs">Fancy</span></a></li>
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
                                                <?php foreach ($ubets as $ub): ?>
                                                    <tr class="<?= $ub['back_lay'] == 'back' ? 'back' : 'lay'; ?>">
                                                        <td><a href="javascript:void(0)" onclick="deleteUnmatched('<?= $ub['id']; ?>')"><i class="fa fa-trash-o"></i></a>&nbsp;&nbsp;<?= $ub['team']; ?></td>
                                                        <td><?= $ub['back_lay']; ?></td>
                                                        <td><?= $ub['odd']; ?></td>
                                                        <td><?= $ub['stake']; ?></td>
                                                        <td><?= $ub['profit']; ?></td>
                                                        <td><?= $ub['loss']; ?></td>
                                                        <td><?= $ub['ip']; ?></td>
                                                        <td><?= $ub['id']; ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
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
                                                <?php foreach ($mbets as $mb): ?>
                                                    <tr class="<?= $mb['back_lay'] == 'back' ? 'back' : 'lay'; ?>">
                                                        <td><?= $mb['team']; ?></td>
                                                        <td><?= $mb['back_lay']; ?></td>
                                                        <td><?= $mb['odd']; ?></td>
                                                        <td><?= $mb['stake']; ?></td>
                                                        <td><?= $mb['profit']; ?></td>
                                                        <td><?= $mb['loss']; ?></td>
                                                        <td><?= $mb['ip']; ?></td>
                                                        <td><?= $mb['id']; ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
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
                                                <?php foreach ($fbets as $fb): ?>
                                                    <tr class="<?= $fb['back_lay'] == 'back' ? 'back' : 'lay'; ?>">
                                                        <td><?= $fb['team']; ?></td>
                                                        <td><?= $fb['back_lay']; ?></td>
                                                        <td><?= $fb['odd']; ?></td>
                                                        <td><?= $fb['stake']; ?></td>
                                                        <td><?= $fb['profit']; ?></td>
                                                        <td><?= $fb['loss']; ?></td>
                                                        <td><?= $fb['ip']; ?></td>
                                                        <td><?= $fb['id']; ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
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

<?php
include_once 'calls.php';
?>