<?php echo $status; ?>
<div class="columns">
    <div class="column col-6 col-sm-12" style="margin-bottom: 1rem">
        <div class="empty" style="box-shadow: 0 0 16px lightgray; height: 100%">
            <div class="empty-icon"><i class="icon icon-4x icon-time"></i></div>
            <p class="empty-title h5">Sesi</p>
            <p class="empty-subtitle">Terdapat total <b><?php echo $sessions; ?></b> sesi</p>
            <div class="empty-action"><a class="btn btn-primary" href="<?php echo site_url('admin/session'); ?>">LIHAT</a></div>
        </div>
    </div>
    <div class="column col-6 col-sm-12" style="margin-bottom: 1rem">
        <div class="empty" style="box-shadow: 0 0 16px lightgray; height: 100%">
            <div class="empty-icon"><i class="icon icon-4x icon-people"></i></div>
            <p class="empty-title h5"></p>
            <p class="empty-subtitle">Terdapat total <b><?php echo $candidates; ?></b> kandidat</p>
            <div class="empty-action"><a class="btn btn-primary" href="<?php echo site_url('admin/candidate'); ?>">LIHAT</a></div>
        </div>
    </div>
    <div class="column col-6 col-sm-12" style="margin-bottom: 1rem">
        <div class="empty" style="box-shadow: 0 0 16px lightgray; height: 100%">
            <div class="empty-icon"><i class="icon icon-4x icon-copy"></i></div>
            <p class="empty-title h5">Tokenset</p>
            <p class="empty-subtitle">Terdapat total <b><?php echo $tokensets; ?></b> tokenset</p>
            <div class="empty-action"><a class="btn btn-primary" href="<?php echo site_url('admin/tokenset'); ?>">LIHAT</a></div>
        </div>
    </div>
</div>
