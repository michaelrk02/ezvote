<?php echo form_open(site_url(uri_string()).($create ? '' : '?action=edit&id='.$data['candidate_id']), 'onsubmit="return window.confirm(\'apakah anda yakin?\')"'); ?>
    <h3><?php echo $create ? 'Tambah' : 'Perbarui'; ?> Kandidat</h3>
    <div class="form-group">
        <label class="form-label">ID kandidat:</label>
        <input type="text" class="form-input" name="candidate_id" value="<?php echo htmlspecialchars($data['candidate_id']); ?>" readonly>
    </div>
    <div class="form-group">
        <label class="form-label">Sesi:</label>
        <select class="form-select" name="session_id">
            <option value="">-- pilih sesi --</option>
            <?php foreach ($sessions as $session) { ?>
                <option value="<?php echo $session['session_id']; ?>"><?php echo $session['title']; ?></option>
            <?php } ?>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label">Nama: <span class="text-error">*</span></label>
        <input type="text" class="form-input" name="title" value="<?php echo htmlspecialchars($data['name']); ?>" placeholder="Masukkan nama kandidat ...">
    </div>
    <div class="form-group">
        <label class="form-label">Deskripsi:</label>
        <textarea class="form-input" name="description" rows="5" style="resize: none" placeholder="Masukkan deskripsi ..."><?php echo htmlspecialchars($data['description']); ?></textarea>
    </div>
    <div class="form-group">
        <button type="submit" class="btn btn-success" name="submit" value="1">KIRIM</button>
        <?php if (!$create) { ?>
            <a class="btn btn-error" href="<?php echo site_url('admin/candidate').'?id='.$data['candidate_id'].'&action=delete'; ?>" onclick="return window.confirm('Apakah anda yakin?')">HAPUS</a>
        <?php } ?>
    </div>
</form>

