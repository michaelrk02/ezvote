<div>
    <div style="margin-bottom: 1rem">
        <?php echo $status; ?>
    </div>
    <?php if (!$dramatic) { ?>
        <table class="table table-scroll table-striped table-hover">
            <thead>
                <tr>
                    <th>Kategori</th>
                    <th>Nama Pemenang</th>
                    <th>Jumlah Suara</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><b>PEMENANG UTAMA</b></td>
                    <td><b><?php echo $winner; ?></b></td>
                    <td><b><?php echo $voters; ?></b></td>
                </tr>
                <?php foreach ($categories as $category) { ?>
                    <tr>
                        <td><?php echo $category['name']; ?></td>
                        <td><?php echo isset($category['winner']) ? $category['winner'] : '(tidak ada)'; ?></td>
                        <td><?php echo $category['voters']; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <script>
            window.candidates = {};
        </script>
        <div class="columns" id="candidates">
            <?php foreach ($candidates as $candidate) { ?>
                <div class="column col-4 col-sm-12 col-ml-auto col-mr-auto" style="margin-bottom: 1rem">
                    <div class="card" id="__candidate_<?php echo $candidate['candidate_id']; ?>" style="height: 100%">
                        <div class="card-image">
                            <div class="empty" v-if="!imgLoaded">
                                <div class="empty-icon"><i v-if="!counterEnabled" class="icon icon-4x icon-people"></i><h1 v-if="counterEnabled">{{ voters }}</h1></div>
                            </div>
                            <div style="position: relative; text-align: center" v-bind:class="[imgLoaded ? '' : 'd-none']">
                                <img src="<?php echo site_url('content/data_img/candidate_'.$candidate['candidate_id']); ?>" width="100%" v-on:load="imgLoaded = true" v-bind:style="counterEnabled ? {filter: 'opacity(30%)'} : {}">
                                <h1 v-if="counterEnabled" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%)">{{ voters }}</h1>
                            </div>
                        </div>
                        <div class="card-header">
                            <div class="card-title h5"><?php echo $candidate['name']; ?></div>
                            <div class="card-subtitle text-gray" style="font-family: monospace">Kode: <?php echo $candidate['candidate_id']; ?></div>
                        </div>
                    </div>
                </div>
                <script>
                    window.addEventListener('load', function() {
                        window.candidates[<?php echo '\''.$candidate['candidate_id'].'\''; ?>] = new Vue({
                            el: <?php echo '\'#__candidate_'.$candidate['candidate_id'].'\''; ?>,
                            data: {
                                timer: null,
                                imgLoaded: false,
                                counterEnabled: false,
                                voters: 0,
                                votersMax: <?php echo $candidate['voters']; ?>,
                                name: <?php echo '\''.$candidate['name'].'\''; ?>,
                                candidateID: <?php echo '\''.$candidate['candidate_id'].'\''; ?>
                            },
                            methods: {
                                count: function() {
                                    this.counterEnabled = true;
                                    this.timer = window.setInterval((function() {
                                        if (this.voters < this.votersMax) {
                                            this.voters++;
                                        } else {
                                            window.clearInterval(this.timer);
                                        }
                                    }).bind(this), 25);
                                }
                            }
                        });
                    });
                </script>
            <?php } ?>
        </div>
        <div id="counter" class="columns">
            <div class="column col-2 col-ml-auto col-mr-auto">
                <button class="btn btn-success" v-on:click="count" v-bind:disabled="counterEnabled">HITUNG</button>
            </div>
        </div>
        <script>
            window.addEventListener('load', function() {
                new Vue({
                    el: '#counter',
                    data: {
                        counterEnabled: false
                    },
                    methods: {
                        count: function() {
                            if (window.confirm('Apakah anda yakin?')) {
                                window.location.href = '#candidates';
                                this.counterEnabled = true
                                for (var candidateID in window.candidates) {
                                    window.candidates[candidateID].count();
                                }
                            }
                        }
                    }
                });
            });
        </script>
    <?php } ?>
</div>
