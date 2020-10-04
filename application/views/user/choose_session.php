<div class="empty" style="margin-top: 2rem">
    <div class="container grid-sm">
        <div class="empty-icon">
            <i class="icon icon-4x icon-time"></i>
        </div>
        <p class="empty-title h3">Pilih Sesi</p>
        <p class="empty-subtitle">Silakan untuk memilih sesi pemungutan suara kemudian klik tombol <b>LANJUT</b></p>
        <div class="empty-action">
            <?php echo form_open('user/choose_session'); ?>
                <div class="input-group" style="margin-bottom: 1rem">
                    <select class="form-select" name="session_id">
                        <option value="">-- pilih sesi --</option>
                        <?php foreach ($sessions as $session) { ?>
                            <option value="<?php echo $session['session_id']; ?>"><?php echo $session['title']; ?> (ID: <?php echo $session['session_id'] ?>)</option>
                        <?php } ?>
                    </select>
                    <button type="submit" class="btn btn-success input-group-btn" name="submit" value="1">LANJUT &raquo;</button>
                </div>
                <!-- <label class="form-checkbox text-left">
                    <input type="checkbox" name="automatic" value="1">
                    <i class="form-icon"></i> Pilih token otomatis <b>(hanya panitia)</b>
                </label> --> <!-- FIXME: THIS FEATURE ISN'T AVAILABLE FOR NOW -->
                <div>
                    <?php echo $status; ?>
                </div>
            </form>
        </div>
    </div>
</div>
