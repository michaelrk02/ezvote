<?php echo form_open('admin/login'); ?>
    <div class="form-group">
        <?php echo $status; ?>
    </div>
    <div class="form-group">
        <label class="form-label">Password:</label>
        <input type="password" class="form-input" name="password" placeholder="Masukkan password ...">
    </div>
    <div class="form-group">
        <button type="submit" class="btn btn-success" name="submit" value="1">MASUK &raquo;</button>
    </div>
</form>
