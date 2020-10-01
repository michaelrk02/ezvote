<form method="get" action="<?php echo site_url('admin/tokenset'); ?>">
    <div class="form-group">
        <div class="columns">
            <div class="column col-8 col-sm-12">
                <select class="form-select" name="id">
                    <option value="">-- pilih tokenset --</option>
                    <?php foreach ($tokensets as $tokenset) { ?>
                        <option value="<?php echo $tokenset['tokenset_id']; ?>" <?php echo ($id === $tokenset['tokenset_id']) ? 'selected' : ''; ?>><?php echo $tokenset['session_title']; ?> - <?php echo $tokenset['name']; ?> (<?php echo $tokenset['tokenset_id']; ?>)</option>
                    <?php } ?>
                </select>
            </div>
            <div class="column col-2 col-sm-6">
                <button type="submit" class="btn btn-success btn-block">PILIH</button>
            </div>
            <div class="column col-2 col-sm-6">
                <a class="btn btn-primary btn-block" href="<?php echo site_url('admin/tokenset_create'); ?>">BUAT TOKENSET</a>
            </div>
        </div>
    </div>
</form>
