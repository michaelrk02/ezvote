<div id="editor">
    <?php echo form_open(site_url(uri_string()).($create ? '' : '?action=edit&id='.$data['session_id']), 'onsubmit="return window.confirm(\'apakah anda yakin?\')"'); ?>
        <h3><?php echo $create ? 'Buat' : 'Perbarui'; ?> Sesi</h3>
        <div class="form-group">
            <label class="form-label">ID sesi:</label>
            <input type="text" class="form-input" name="session_id" value="<?php echo htmlspecialchars($data['session_id']); ?>" readonly>
        </div>
        <div class="form-group">
            <label class="form-label">Judul sesi: <span class="text-error">*</span></label>
            <input type="text" class="form-input" name="title" value="<?php echo htmlspecialchars($data['title']); ?>" placeholder="Masukkan judul sesi ...">
        </div>
        <div class="form-group">
            <label class="form-label">Password: <?php if ($create) { ?><span class="text-error">*</span><?php } ?></label>
            <input v-bind:type="passwordVisible ? 'text' : 'password'" class="form-input" name="password" placeholder="<?php echo $create ? 'Masukkan password ...' : '(tidak diubah)'; ?>">
            <label class="form-checkbox">
                <input type="checkbox" v-on:change="togglePassword">
                <span class="form-icon"></span> Perlihatkan password
            </label>
        </div>
        <div class="form-group">
            <label class="form-label">Deskripsi:</label>
            <textarea class="form-input" name="description" rows="5" style="resize: none" placeholder="Masukkan deskripsi ..."><?php echo htmlspecialchars($data['description']); ?></textarea>
        </div>
        <div class="form-group">
            <label class="form-label">Tagline:</label>
            <input type="text" class="form-input" name="tagline" value="<?php echo htmlspecialchars($data['tagline']); ?>" placeholder="Masukkan tagline ...">
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-success" name="submit" value="1">KIRIM</button>
            <?php if (!$create) { ?>
                <a class="btn btn-error" href="<?php echo site_url('admin/session').'?id='.$data['session_id'].'&action=delete'; ?>" onclick="return window.confirm('Apakah anda yakin?')">HAPUS</a>
            <?php } ?>
        </div>
    </form>
</div>

