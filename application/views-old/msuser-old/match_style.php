<script src="<?php echo base_url('assets/backend/js/jquery-3.3.1.min.js') ?>"></script>
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
        background: rgba(255,255,255,0.8) url(<?= base_url('assets/plugins/images/loader.gif'); ?>) top center no-repeat;
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