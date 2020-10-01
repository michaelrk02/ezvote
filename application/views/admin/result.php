<div>
    <div style="margin-bottom: 1rem">
        <?php echo $status; ?>
    </div>
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
                <td>PEMENANG UTAMA</td>
                <td><?php echo $winner; ?></td>
                <td><?php echo $voters; ?></td>
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
</div>
