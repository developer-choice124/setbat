<script type="text/javascript" src="<?=base_url();?>app_assets/js/jquery.min.js"></script>
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
<div class="content_wrapper no-gutters">
    <div class="container-fluid no-gutters">

        <!-- Section -->
        <section class="chart_section">
            <div class="row">
                <div class="col-12">
                    <div class="full_chart card mb-4">
                        <div class="chart_header">
                            <div class="chart_headibg">
                                <h3>Profit Loss</h3>
                            </div>

                        </div>
                        <div class="card_chart">
                            <div class="student_table table-responsive-lg">
                                <table id="datatable" class="table table-bordered table-striped table-sm" cellspacing="0" width="100%">
                                    <thead>
                                      <tr class="headings">
                                        <th class="">S.No. </th>
                                        <th class="">Event Name </th>
                                        <th class="">Market </th>
                                        <th class="">P_L </th>
                                        <th class="">Commission </th>
                                        <th class="">Created On </th>
                                        <th class="">Action </th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i = 1; foreach($profitLosses as $p):?>
                                          <tr>
                                            <td><?=$i++;?></td>
                                            <td><?=$p['match_name'];?></td>
                                            <td><?=$p['market'];?></td>
                                            <td><?php if($p['profit'] > 0) echo '<span class="text-success">'.$p['profit'].'</span>'; else echo '<span class="text-danger">'.$p['loss'].'</span>';?></td>
                                            <td><span class="text-success"><?=$p['commission'];?></span></td>
                                            <td><?=date('Y-M-d H:i:sa',strtotime($p['created_at']));?></td>
                                            <td><a href="<?=base_url('MsAppUser/bet?bet_id='.$p['bet_id']);?>">show bet</a></td>
                                          </tr>
                                        <?php endforeach;?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            
        </section>
        <!-- Section_End -->

    </div>
</div>