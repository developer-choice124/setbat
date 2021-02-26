<?php
include_once 'match_style.php';
?>

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
                    <div class="pull-right"><a href="<?= base_url('MsUser/matches'); ?>">All Matches</a><a href="#" data-perform="panel-collapse"><i class="ti-minus"></i></a></div>
                </div>
                <div class="panel-body">
                    <h1>To View Match Odd Please download the app. Thank You</h1>
                </div>
            </div>
        </div>
    </div>
</div>