<script type="text/javascript" src="<?= base_url(); ?>app_assets/js/jquery.min.js"></script>
<style type="text/css">
  .headings {
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
                    <tr class="headings" role="row">
                      <th>S.No. </th>
                      <th>Date </th>
                      <th>Event Name </th>
                      <th>Market</th>
                      <th>P_L </th>
                      <th>Commission </th>
                      <th>Action </th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $i = 1;
                    foreach ($statements as $s) : ?>
                      <?php
                      $link = '<a href="' . base_url('MsAppUser/statementByMatchId?match_id=' . $s['match_id'] . '&user_id=' . $s['user_id']) . '">Action</a>';
                      ?>
                      <tr>
                        <td><?= $i++; ?></td>
                        <td><?= date('d-M-Y H:i:sa', strtotime($s['transaction_date'])); ?></td>
                        <td><?= $s['description']; ?></td>
                        <td><?= $s['market']; ?></td>
                        <td><span class="<?= $s['c_l']; ?>" style="font-weight: bold;"><?= $s['p_l']; ?></span></td>
                        <td>
                          <?php if ($s['a_c'] >= 0) { ?>
                            <span style="color: green; font-weight: bold;"><?= $s['a_c']; ?></span>
                          <?php } else { ?>
                            <span style="color: red; font-weight: bold;"><?= $s['a_c']; ?></span>
                          <?php } ?>
                        </td>
                        <td><b><?= $link; ?></b></td>
                      </tr>
                    <?php endforeach; ?>
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