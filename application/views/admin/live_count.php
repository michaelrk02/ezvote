<div style="margin-top: 4rem" class="text-center">
    <div style="margin-bottom: 1rem">
        <h1>LIVE COUNT</h1>
        <h3><?php echo $session['title']; ?></h3>
    </div>
    <div style="margin-bottom: 1rem">
        <h5>Jumlah suara masuk: <b><?php echo $current; ?></b> dari <b><?php echo $total; ?></b></h5>
        <h4>Persentase: <b><?php echo $percentage; ?>%</b></h4>
    </div>
    <?php if (!empty($session['tagline'])) { ?>
        <h5><i>"<?php echo $session['tagline']; ?>"</i></h5>
    <?php } ?>
</div>
<script>

window.addEventListener('load', function() {
    window.setTimeout(function() {
        window.location.reload();
    }, 30000);
});

</script>
