<form method="get" action="<?php echo site_url('admin/result'); ?>">
    <div class="form-group">
        <div class="columns">
            <div class="column col-8 col-sm-12">
                <select class="form-select" name="session">
                    <option value="">-- pilih sesi --</option>
                    <?php foreach ($sessions as $session) { ?>
                        <option value="<?php echo $session['session_id']; ?>" <?php echo ($session_id === $session['session_id']) ? 'selected' : ''; ?>><?php echo $session['title']; ?> (<?php echo $session['session_id']; ?>)</option>
                    <?php } ?>
                </select>
            </div>
            <div class="column col-2 col-sm-6">
                <label class="form-checkbox">
                    <input type="checkbox" name="dramatic" value="1" <?php echo $dramatic ? 'checked' : ''; ?>>
                    <i class="form-icon"></i> dramatis
                </label>
            </div>
            <div class="column col-2 col-sm-6">
                <button type="submit" class="btn btn-success btn-block">PILIH</button>
            </div>
        </div>
    </div>
</form>
