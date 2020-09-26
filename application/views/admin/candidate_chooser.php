<form method="get" action="<?php echo site_url('admin/candidate'); ?>">
    <div class="form-group">
        <div class="columns">
            <div class="column col-8 col-sm-12">
                <select class="form-select" name="candidate_id">
                    <option value="">-- pilih kandidat --</option>
                    <?php foreach ($candidates as $candidate) { ?>
                        <option value="<?php echo $candidate['candidate_id']; ?>" <?php echo ($id === $candidate['candidate_id']) ? 'selected' : ''; ?>><?php echo $candidate['name']; ?> (<?php echo $candidate['candidate_id']; ?>)</option>
                    <?php } ?>
                </select>
            </div>
            <div class="column col-2 col-sm-6">
                <button type="submit" class="btn btn-success btn-block">PILIH</button>
            </div>
            <div class="column col-2 col-sm-6">
                <a class="btn btn-primary btn-block" href="<?php echo site_url('admin/candidate_create'); ?>">TAMBAH KANDIDAT</a>
            </div>
        </div>
    </div>
</form>
