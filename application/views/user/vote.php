<datalist id="search-data">
    <?php foreach ($search_data as $search_option) { ?>
        <option value="<?php echo $search_option['name']; ?>"><?php echo !empty($search_option['description']) ? $search_option['description'].'...' : ''; ?></option>
    <?php } ?>
</datalist>
<script>

window.candidates = {};

</script>
<div class="container grid-xl">
    <form method="get" action="<?php echo site_url(uri_string()); ?>" style="margin-bottom: 1rem">
        <div class="form-group">
            <div class="input-group">
                <span class="input-group-addon">Cari:</span>
                <input type="text" class="form-input" name="query" placeholder="Masukkan kriteria ..." list="search-data" value="<?php echo htmlspecialchars($query); ?>">
                <button class="btn btn-success input-group-btn"><i class="icon icon-search"></i></button>
            </div>
        </div>
    </form>
    <div style="margin-bottom: 1rem">
        <?php echo $status; ?>
    </div>
    <div class="columns">
        <?php foreach ($candidates as $candidate) { ?>
            <div class="column col-4 col-sm-12 col-ml-auto col-mr-auto" style="margin-bottom: 1rem">
                <div class="card" id="__candidate_<?php echo $candidate['candidate_id']; ?>" style="height: 100%">
                    <div class="card-image">
                        <div class="empty" v-if="!imgLoaded">
                            <div class="empty-icon"><i class="icon icon-4x" v-bind:class="[chosen ? 'icon-check text-success' : 'icon-people']"></i></div>
                        </div>
                        <div style="position: relative; text-align: center" v-bind:class="[imgLoaded ? '' : 'd-none']">
                            <img src="<?php echo site_url('content/data_img/candidate_'.$candidate['candidate_id']); ?>" width="100%" v-on:load="imgLoaded = true" v-bind:style="chosen ? {filter: 'opacity(30%)'} : {}">
                            <i v-if="chosen" class="icon icon-4x icon-check text-success" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%)"></i>
                        </div>
                    </div>
                    <div class="card-header">
                        <div class="card-title h5"><?php echo $candidate['name']; ?></div>
                        <div class="card-subtitle text-gray" style="font-family: monospace">Kode: <?php echo $candidate['candidate_id']; ?></div>
                    </div>
                    <div class="card-body">
                        <p>{{ description }} <span v-if="descriptionText.length > 50">(<a href="#!" v-on:click="fullDescription = !fullDescription">{{ fullDescription ? 'lebih sedikit' : 'lebih banyak' }}</a>)</span></p>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-block" v-on:click="choose" v-bind:class="[chosen ? '' : 'btn-primary']" v-bind:disabled="chosen">{{ chosen ? 'TERPILIH' : 'PILIH' }}</button>
                    </div>
                </div>
            </div>
            <script>
                window.addEventListener('load', function() {
                    window.candidates[<?php echo '\''.$candidate['candidate_id'].'\''; ?>] = new Vue({
                        el: <?php echo '\'#__candidate_'.$candidate['candidate_id'].'\''; ?>,
                        data: {
                            imgLoaded: false,
                            chosen: false,
                            name: <?php echo '\''.$candidate['name'].'\''; ?>,
                            candidateID: <?php echo '\''.$candidate['candidate_id'].'\''; ?>,
                            fullDescription: false,
                            descriptionText: <?php echo '\''.str_replace('\'', '\\\'', $candidate['description']).'\''; ?>
                        },
                        computed: {
                            description: function() {
                                if (this.descriptionText.length > 0) {
                                    if (this.fullDescription) {
                                        return this.descriptionText;
                                    } else {
                                        if (this.descriptionText.length > 50) {
                                            return this.descriptionText.substring(0, 50) + '...';
                                        } else {
                                            return this.descriptionText;
                                        }
                                    }
                                }
                                return '';
                            },
                        },
                        methods: {
                            choose: function() {
                                window.voting.candidateID = this.candidateID;
                                window.location.href = '#voting';

                                for (var candidateID in window.candidates) {
                                    window.candidates[candidateID].chosen = false;
                                }
                                window.candidates[this.candidateID].chosen = true;
                            }
                        }
                    });
                });
            </script>
        <?php } ?>
    </div>
    <div class="columns" style="margin-bottom: 1rem">
        <div class="column col-2 col-mr-auto">
            <?php if (isset($previous)) { ?>
                <a href="<?php echo $previous; ?>" class="btn btn-link"><i class="icon icon-arrow-left"></i> Sebelumnya</a>
            <?php } ?>
        </div>
        <div class="column col-auto col-ml-auto col-mr-auto text-center">
            <p>Halaman: <b><?php echo $page; ?></b></p>
        </div>
        <div class="column col-2 col-ml-auto">
            <?php if (isset($next)) { ?>
                <a href="<?php echo $next; ?>" class="btn btn-link">Berikutnya <i class="icon icon-arrow-right"></i></a>
            <?php } ?>
        </div>
    </div>
    <div class="columns" id="voting" style="margin-bottom: 1rem">
        <div class="column col-4 col-sm-12 col-ml-auto col-mr-auto text-center">
            <div>Kandidat terpilih:</div>
            <div v-if="candidateID !== null">
                <?php echo form_open('user/vote', 'onsubmit="return window.confirm(\'Apakah anda yakin?\')"'); ?>
                    <input type="hidden" name="candidate_id" v-bind:value="candidateID">
                    <div class="form-group">
                        <div><b>{{ candidateName }}</b></div>
                        <div>Kode: <code>{{ candidateID }}</code></div>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-success btn-block" name="submit" value="1">SELESAI <i class="icon icon-check"></i></button>
                    </div>
                </form>
            </div>
            <div v-if="candidateID === null">(tidak ada)</div>
        </div>
    </div>
</div>
<script>

window.voting = null;

window.addEventListener('load', function() {
    window.voting = new Vue({
        el: '#voting',
        data: {
            candidateID: null
        },
        computed: {
            candidateName: function() {
                if (this.candidateID !== null) {
                    return window.candidates[this.candidateID].name;
                }
                return '(tidak ada)';
            }
        }
    });
});

</script>
