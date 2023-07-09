<?= $this->extend('layout/template'); ?>
<?= $this->section('content'); ?>
<style>
    /* div.dataTables_wrapper div.dataTables_length label {
        display: none !important;
    } */
    #hidepenjualan {
        display: none;
    }
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-5">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary card-outline">
                        <div class="card-body">
                            <div class="text-right">
                                <h4>Invoice : <span class="text-bold" id="invoice"></span></h4>
                                <h1 class="m-0 p-0"><span class="text-bold text-danger" id="tampilkan_total">0</span></h1>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card card-primary card-outline">
                        <div class="card-body">
                            <div class="form-group row">
                                <label for="sub_total" class="col-sm-5 col-form-label">Sub Total</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control text-right" name="sub_total" id="sub_total" value="0" disabled>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="diskon" class="col-sm-5 col-form-label">Dis Total (%)</label>
                                <div class="col-sm-7">
                                    <input type="number" class="form-control text-right" name="diskon" id="diskon" autocomplete="off" value="0" min="0" disabled>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="total_akhir" class="col-sm-5 col-form-label">Total Akhir</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control text-right" name="total_akhir" id="total_akhir" value="0" disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card card-primary card-outline">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="catatan">Catatan</label>
                                <textarea class="form-control" name="catatan" id="catatan" rows="2" disabled></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card card-primary card-outline">
                        <div class="card-body">
                            <div class="form-group row">
                                <label for="tunai" class="col-sm-5 col-form-label">Tunai</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control text-right" name="tunai" id="tunai" value="0" disabled>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="kembalian" class="col-sm-5 col-form-label">Kembalian</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control text-right" name="kembalian" id="kembalian" value="0" disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="card card-primary card-outline">
                <div class="card-body p-0">
                    <div class="p-3">
                        <div class="form-group row">
                            <label for="barcode" class="col-sm-3 col-form-label">Barcode</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <input type="hidden" id="iditem">
                                    <input type="hidden" id="nama">
                                    <input type="hidden" id="harga">
                                    <input type="hidden" id="stok">
                                    <input type="text" class="form-control mr-2" id="barcode" name="barcode" placeholder="Cari barcode / nama barang" autofocus autocomplete="off">
                                    <span class="text-muted" id="tampil-stok"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="jumlah" class="col-sm-3 col-form-label">Jumlah</label>
                            <div class="col-sm-5">
                                <input type="number" class="form-control" name="jumlah" id="jumlah" min="1" disabled>
                            </div>
                            <div class="col-sm-4">
                                <button type="button" id="tambah" class="btn btn-primary" disabled>Tambah</button>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="tabel-keranjang" width="100%">
                            <thead>
                                <tr>
                                    <th>Barcode</th>
                                    <th>Menu</th>
                                    <th>Harga</th>
                                    <th>Jumlah</th>
                                    <th style="width: 137px;">Diskon item (%)</th>
                                    <th>Total</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    <div class="px-3 pb-3 pt-1">
                        <div class="mb-2">
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="text" name="kasir" id="kasir" value="<?= get_user('nama') ?>" readonly class="form-control">
                                </div>
                                <div class="col">
                                    <div class="form-group row d-none">
                                        <label for="tanggal" class="col-sm-3 col-form-label">Tanggal</label>
                                        <div class="col-sm-9">
                                            <input type="date" class="form-control" name="tanggal" id="tanggal" value="<?= date('Y-m-d') ?>">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-12">
                                            <input type="text" class="form-control" name="pelanggan" id="pelanggan" placeholder="Pesanan atas nama . . .">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between" style="gap:5px">
                            <button class="btn btn-sm btn-warning" id="batal" disabled><i class="fa fa-refresh"></i> Batal</button>
                            <button class="btn btn-sm btn-success" id="bayar" disabled><i class="fa fa-paper-plane"></i> Proses Pembayaran</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- modal edit item produk -->
<div class="modal fade" id="modal-item-edit" aria-modal="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <?= form_open('', ['csrf_id' => 'token']); ?>
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Item</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="item_id" name="item_id">
                <input type="hidden" id="item_stok" name="item_stok">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for=""> Barcode</label>
                        <input type="text" id="item_barcode" name="item_barcode" class="form-control" readonly>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="">Item Produk</label>
                        <input type="text" id="item_nama" name="item_nama" class="form-control" readonly>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="item_harga">Harga</label>
                        <input type="text" id="item_harga" name="item_harga" class="form-control" readonly>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="item_jumlah">Jumlah <small id="modal-stok" class="text-muted"></small></label>
                        <input type="number" id="item_jumlah" name="item_jumlah" class="form-control" min="1">
                    </div>
                </div>
                <div class="form-group">
                    <label for="harga_sebelum_diskon">Total sebelum Diskon</label>
                    <input type="text" id="harga_sebelum_diskon" name="harga_sebelum_diskon" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <label for="item_diskon">Diskon Item (%)</label>
                    <input type="number" id="item_diskon" name="item_diskon" class="form-control" min="0">
                </div>
                <div class="form-group">
                    <label for="harga_setelah_diskon">Total setelah Diskon</label>
                    <input type="text" id="harga_setelah_diskon" name="harga_setelah_diskon" class="form-control" min="0" readonly>
                </div>
            </div>
            <div class="form-group">
                <div class="float-right mr-3">
                    <button type="button" class="btn btn-success" id="edit-keranjang"><i class="fa fa-paper-plane"></i> Simpan</button>
                </div>
            </div>
        </div>
        <!-- /.modal-content -->
        <?= form_close(); ?>
    </div>
    <!-- /.modal-dialog -->
</div>
<?= $this->endSection(); ?>

<?= $this->section('js'); ?>
<script src="<?= base_url('js/penjualan.js') ?>"></script>
<script src="<?= base_url('plugins/jquery-ui/jquery-ui.min.js') ?>"></script>
<script src="<?= base_url('plugins/autoNumeric.min.js') ?>"></script>
<script>
    let auto_numeric = new AutoNumeric('#tunai', {
        decimalCharacter: ",",
        decimalPlaces: 0,
        digitGroupSeparator: ".",
    });
</script>
<?= $this->endSection(); ?>