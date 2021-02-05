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
  .back {
      background-color: #b5e0ff;
  }
  .lay {
      background-color: #ffbfcd;
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
                                <h3>Bet History</h3>
                            </div>

                        </div>
                        <div class="card_chart">
                            <div class="student_table table-responsive-lg">
                                <table id="datatable" class="table table-bordered table-sm" cellspacing="0" width="100%">
                                    <thead>
                                      <tr class="headings">
                                        <th class="">S.No. </th>
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
                                      <?php $i = 1; foreach($bets as $b):?>
                                        <tr class="<?= $b['back_lay'] == 'back' ? 'back' : 'lay'; ?>">
                                          <td><?=$i++;?></td>
                                          <td><?=$b['match_name'];?></td>
                                          <td><?=$b['team'];?></td>
                                          <td><?=$b['back_lay'];?></td>
                                          <td><?=$b['odd'];?></td>
                                          <td><?=$b['stake'];?></td>
                                          <td><?=date('Y-M-d H:i:s a',strtotime($b['created_at']));?></td>
                                          <td><?php if($b['status'] == 'pending') echo 'Pending'; else echo '<span class="text-success">'.$b['profit'].'</span>';?></td>
                                          <td><?php if($b['status'] == 'pending') echo 'Pending'; else echo '<span class="text-danger">'.$b['loss'].'</span>';?></td>
                                          <td><?php if($b['status'] == 'pending') echo 'Pending'; else echo '<span class="text-danger">'.$b['loss'].'</span>';?></td>
                                          <td><?=ucwords($b['bet_type']);?></td>
                                          <td><?=$b['status'];?></td>
                                          <td><?=$b['ip'];?></td>
                                          <td><?=$b['id'];?></td>
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