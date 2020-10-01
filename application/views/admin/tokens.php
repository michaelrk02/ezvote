<div class="columns">
    <?php foreach ($tokens as $token) { ?>
        <div class="column col-2 col-xl-4 col-md-6 col-xs-12" style="margin-bottom: 1rem">
            <div class="bg-secondary text-center" style="padding: 1rem 0; height: 100%">
                <div class="mb-2"><code><?php echo $token['token']; ?></code></div>
                <?php if (isset($token['candidate_id'])) { ?>
                    <div class="label label-success label-rounded">Terpakai</div>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
</div>
