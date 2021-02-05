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
                                <h3>Chip History</h3>
                            </div>

                        </div>
                        <div class="card_chart">
                            <div class="student_table table-responsive-lg">
                                <table id="datatable" class="table table-bordered table-striped table-sm" cellspacing="0" width="100%">
                                    <thead>
                                      <tr class="headings">
                                          <th>S. No</th>
                                          <th>Description</th>
                                          <th>Date</th>
                                          <th>Credit</th>
                                          <th>Debit</th>
                                          <th>Balance</th>
                                          <th>Id</th>
                                      </tr>
                                  </thead>
                                  <tbody>
                                      <?php $i = 1; foreach ($history as $key => $h):?>
                                        <tr>
                                          <td><?=$i++;?></td>
                                          <td><?=$h['match_name'].'&nbsp;['.$h['market'].']&nbsp;Winner: '.$h['winner_team'];?></td>
                                          <td><?=date('d-M-Y h:i:sa', strtotime($h['created_at']));?></td>
                                          <td><span class="text-success"><?=$h['profit']?$h['profit']:0;?></span></td>
                                          <td><span class="text-danger"><?=$h['loss']?$h['loss']:0?></span></td>
                                          <td></td>
                                          <td><?=ucwords($h['id']);?></td>
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