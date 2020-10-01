<?php echo form_open(site_url(uri_string()).($create ? '' : '?action=edit&id='.$data['tokenset_id']), 'onsubmit="return window.confirm(\'apakah anda yakin?\')"'); ?>
    <h3><?php echo $create ? 'Buat' : 'Perbarui'; ?> Tokenset</h3>
    <div class="form-group">
        <label class="form-label">ID tokenset:</label>
        <input type="text" class="form-input" name="tokenset_id" value="<?php echo htmlspecialchars($data['tokenset_id']); ?>" readonly>
    </div>
    <div class="form-group">
        <label class="form-label">Sesi:</label>
        <select class="form-select" name="session_id">
            <option value="">-- pilih sesi --</option>
            <?php foreach ($sessions as $session) { ?>
                <option value="<?php echo $session['session_id']; ?>" <?php echo $create ? '' : ($data['session_id'] === $session['session_id'] ? 'selected' : ''); ?>><?php echo $session['title']; ?></option>
            <?php } ?>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label">Nama: <span class="text-error">*</span></label>
        <input type="text" class="form-input" name="name" value="<?php echo htmlspecialchars($data['name']); ?>" placeholder="Masukkan nama tokenset ...">
    </div>
    <?php if ($create) { ?>
        <div class="form-group">
            <label class="form-label">Jumlah token: <span class="text-error">*</span></label>
            <input type="number" class="form-input" name="tokens" min="1" value="<?php echo htmlspecialchars($data['tokens']); ?>" placeholder="Masukkan jumlah token ...">
        </div>
    <?php } else { ?>
        <div class="form-group">
            <label class="form-label">
                Jumlah token: <b><?php echo $data['tokens']; ?></b>
                (<a target="_blank" href="<?php echo site_url('admin/tokens').'?tokenset='.$data['tokenset_id']; ?>">tampilkan</a>)
                (<a target="_blank" href="<?php echo site_url('admin/tokens_csv').'?tokenset='.$data['tokenset_id']; ?>">unduh</a> <span class="text-error">*</span>)
            </label>
            <div>
                <i><span class="text-error">*</span> NB: Jika ingin menyebarkan token via WhatsApp, pastikan terformat secara monospace (dikutip dengan <code>```</code> mis: <code>```AbCd-0000-EfGhIjKl```</code>) untuk memudahkan pembacaan</i>
            </div>
        </div>
    <?php } ?>
    <div class="form-group">
        <button type="submit" class="btn btn-success" name="submit" value="1">KIRIM</button>
        <?php if (!$create) { ?>
            <a class="btn btn-error" href="<?php echo site_url('admin/tokenset').'?id='.$data['tokenset_id'].'&action=delete'; ?>" onclick="return window.confirm('Apakah anda yakin?')">HAPUS</a>
        <?php } ?>
    </div>
</form>

