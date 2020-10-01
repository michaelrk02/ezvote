<div id="token-form" class="container grid-sm" style="margin-top: 1rem; margin-bottom: 2rem">
    <?php echo form_open('user/token', 'onsubmit="return window.alert(\'Apakah anda yakin?\')"'); ?>
        <div class="card">
            <div class="card-image">
                <div class="empty">
                    <div class="empty-icon"><i class="icon icon-3x icon-photo"></i></div>
                </div>
            </div>
            <div class="card-header">
                <div class="card-title h5"><?php echo $session['title']; ?></div>
                <div class="card-subtitle text-gray" style="font-family: monospace">Kode: <?php echo $session['session_id']; ?></div>
            </div>
            <div class="card-body">
                <p>{{ description }} (<a href="#!" v-on:click="fullDescription = !fullDescription">{{ fullDescription ? 'lebih sedikit' : 'lebih banyak' }}</a>)</p>
            </div>
            <div class="card-footer">
                <p>Silakan untuk memasukkan token yang telah diberikan oleh panitia di bawah kemudian klik tombol <b>LANJUT</b></p>
                <div class="input-group">
                    <span class="input-group-addon">Token:</span>
                    <input type="text" class="form-input" name="token" placeholder="Masukkan token ..." style="font-family: monospace">
                    <button class="btn btn-success input-group-btn" name="submit" value="1">LANJUT &raquo;</button>
                </div>
                <div class="my-2">
                    <?php echo $status; ?>
                </div>
                <?php if (!empty($session['tagline'])) { ?>
                    <div class="my2 text-center"><i>"<?php echo $session['tagline']; ?>"</i></div>
                <?php } ?>
            </div>
        </div>
    </form>
</div>

<script>

window.addEventListener('load', function() {
    new Vue({
        el: '#token-form',
        data: {
            fullDescription: false,
            descriptionText: <?php echo '\''.str_replace('\'', '\\\'', $session['description']).'\''; ?>
        },
        computed: {
            description: function() {
                if (this.fullDescription) {
                    return this.descriptionText;
                } else {
                    return this.descriptionText.substring(0, Math.min(this.descriptionText.length, 100)) + '...';
                }
            }
        }
    });
});

</script>
