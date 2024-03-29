<?php $this->extend('layout/print'); ?>
<?php $this->section('content'); ?>

<?php
$start = date('Y-m-d', strtotime(str_replace('/', '-', trim(explode('-', $filter['tanggal'])[0]))));
$end = date('Y-m-d', strtotime(str_replace('/', '-', trim(explode('-', $filter['tanggal'])[1]))));
?>

<div class="p-2" style="font-size:13px !important;">

    <h5 class="text-center pb-0 mb-0">Laporan Pembelian Bahan</h5>
    <h4 class="text-center pb-0 mb-0"><u>RESTORAN LEGITA</u></h4>
    <h6 class="text-center"><?=  $start == $end ? "Tanggal" : "Periode" ?> <?= $start == $end ? longdate_indo_without_day_name($start) : longdate_indo_without_day_name($start) . " - " . longdate_indo_without_day_name($end) ?></h6>

    <div class="my-3 mt-4" style="text-align: justify !important; text-justify: inter-word;">
        <span>
            Berdasarkan data pembelian barang / bahan <span class="mr-1"><?=  $start == $end ? "Tanggal" : "Periode" ?></span> <strong><?= $start == $end ? longdate_indo_without_day_name($start) : longdate_indo_without_day_name($start) . " - " . longdate_indo_without_day_name($end) ?></strong>, berikut ini adalah rinciannya :
        </span>
    </div>

    <!-- avoid break -->
    <table class="table table-bordered table-sm" style="page-break-inside: avoid;">
        <thead>
            <tr>
                <th>No.</th>
                <th>Tanggal</th>
                <th>No. Inv</th>
                <th>Barang</th>
                <th>Jml</th>
                <th>Satuan</th>
                <th>Sub Total</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1 ?>
            <?php foreach ($pembelianBulanan as $item) : ?>
                <!-- <tr style="background-color: #f3f3f3;">
                    <td colspan="2">
                        <strong>No. Faktur : </strong> <code><?= $item->kode ?></code>
                    </td>
                    <td colspan='3'>
                        <strong>Oleh :</strong> <?= $item->kasir ?>
                    </td>
                </tr> -->

                <tr class="">
                    <td><?= $no++ ?></td>
                    <td>
                        <span style="white-space: nowrap;"><?= shortdate_day_indo(date('Y-m-d', strtotime($item->created_at))) ?></span>
                    </td>
                    <td><code><?= $item->kode ?></code></td>
                    <td><?= $item->barang ?></td>
                    <td><?= $item->jml_beli ?></td>
                    <td style="white-space: nowrap;">Rp. <?= rupiah($item->harga) ?></td>
                    <td style="white-space: nowrap;">Rp. <?= rupiah($item->total) ?></td>
                </tr>
            <?php endforeach ?>
        </tbody>
        <tfoot class="bg-warning">
            <tr>
                <td colspan="4" class="text-right">
                    <strong>Total Pembelian : </strong>
                </td>
                <td>
                    <div style="white-space: nowrap;"><?= $totalItem ?> Item</div>
                </td>
                <td colspan='' class="text-right">
                    <strong>Total : </strong>
                </td>
                <td>
                    <div style="white-space: nowrap;">Rp. <?= rupiah($totalUang) ?></div>
                </td>
            </tr>
        </tfoot>
    </table>

    <!-- <table class="table table-bordered">
        <tbody class="bg-warning">
            <tr>
                <td colspan="2">
                    <strong>Total Pembelian : </strong> <?= $totalItem ?> Item
                </td>
                <td colspan='3'>
                    <strong>Total : </strong> Rp. <?= rupiah($totalUang) ?>
                </td>
            </tr>
        </tbody>
    </table> -->

    <!-- avoid break -->
    <div class="my-3 mt-4" style="text-align: right; text-justify: inter-word; page-break-inside: avoid;">
        <!-- <span style="font-size: 11px !important;" class="pb-0 mb-0">
            * Dokumen ini dibuat secara otomatis oleh sistem pada tanggal <?= date('d M Y') ?>. <br />
        </span> -->
        <table class="table table-borderless" width="100%">
            <tr></tr>
            <tr></tr>
            <tr>
                <td class="text-center">
                    <p>dibuat oleh</p>
                    <br />
                    <br />
                    <br />
                    <br />
                    <p>
                        (........................................)
                        <br>
                        Admin
                    </p>
                </td>
                <td class="text-center">
                    <strong>Pemalang, <?= $start ? date_indo(date('Y-m-t', strtotime($start))) : date_indo(date('Y-m-t')) ?></strong>
                    <p>Mengetahui</p>
                    <br />
                    <br />
                    <br />
                    <p>
                        (........................................)
                        <br>
                        <span class="text-center">
                            Pemilik Restoran Legita
                        </span>
                    </p>
                </td>
            </tr>
        </table>
    </div>
</div>

<?php $this->endsection(); ?>