<form method="get" action="<?php echo site_url('admin/session'); ?>">
    <div class="form-group">
        <div class="columns">
            <div class="column col-8 col-sm-12">
                <select class="form-select" name="id">
                    <option value="">-- pilih sesi --</option>
                    <?php foreach ($sessions as $session) { ?>
                        <option value="<?php echo $session['session_id']; ?>" <?php echo ($id === $session['session_id']) ? 'selected' : ''; ?>><?php echo $session['title']; ?> (<?php echo $session['session_id']; ?>)</option>
                    <?php } ?>
                </select>
            </div>
            <div class="column col-2 col-sm-6">
                <button type="submit" class="btn btn-success btn-block">PILIH</button>
            </div>
            <div class="column col-2 col-sm-6">
                <a class="btn btn-primary btn-block" href="<?php echo site_url('admin/session_create'); ?>">BUAT SESI</a>
            </div>
        </div>
    </div>
</form>
