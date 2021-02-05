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
                                <h3>Account Info</h3>
                            </div>

                        </div>
                        <div class="card_chart">
                            <div class="student_table table-responsive-lg">
                                <table id="" class="table table-bordered table-striped table-sm" cellspacing="0" width="100%">
                                    <thead>
                                      <tr class="headings">
                                          <th>S. No</th>
                                          <th>Chips</th>
                                          <th>Free Chips</th>
                                          <th>Liability</th>
                                          <th>Wallet</th>
                                          <th>Up</th>
                                          <th>Down</th>
                                      </tr>
                                  </thead>
                                  <tbody>
                                      <tr>
                                        <td>1</td>
                                        <td><?=$info->balanced_chips;?></td>
                                        <td><?=$info->free_chips;?></td>
                                        <td><?php if($info->balanced_chips < $info->spent_chips) echo ($info->spent_chips - $info->balanced_chips); else echo 0.00;?></td>
                                        <td><?=$info->balanced_chips;?></td>
                                        <td><?=$ud->up;?></td>
                                        <td><?=$ud->down;?></td>
                                      </tr>
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