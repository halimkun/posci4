<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Irsyadulibad\DataTables\DataTables;

class Laporan extends BaseController
{
    protected $penjualan;
    protected $transaksi;
    protected $transaksi_barang;
    protected $dompdf;

    public function __construct()
    {
        $this->penjualan = new \App\Models\PenjualanModel();
        $this->transaksi = new \App\Models\TransaksiModel();
        $this->transaksi_barang = new \App\Models\TransaksiBarangModel();

        $this->dompdf = new \Dompdf\Dompdf([
            'defaultPaperSize' => 'A4',
            'defaultFont' => 'sans-serif',
            'isRemoteEnabled' => TRUE,
        ]);
    }

    public function index()
    {
        return redirect()->to(base_url('laporan/penjualan'));
    }

    public function penjualan()
    {
        $tanggal = $this->request->getGet('tanggal');
        $status = $this->request->getGet('status');
        $filter = $this->request->getGet();

        $dataPenjualan = $this->penjualan->select('tb_penjualan.*, tb_users.nama as kasir')
            ->join('tb_users', 'tb_users.id = tb_penjualan.id_user', 'left')
            ->orderBy('tb_penjualan.id', 'asc');
        // ->where('tunai >= total_akhir');

        if ($tanggal) {
            $start = date('Y-m-d', strtotime(str_replace('/', '-', trim(explode('-', $tanggal)[0]))));
            $end = date('Y-m-d', strtotime(str_replace('/', '-', trim(explode('-', $tanggal)[1]))));

            // $dataPenjualan->where('MONTH(tanggal)', date('m', strtotime($tanggal)))->where('YEAR(tanggal)', date('Y', strtotime($tanggal)));
            $dataPenjualan->where("tanggal BETWEEN '$start' AND '$end'");
        } else {
            $start = date('Y-m-1');
            $end = date('Y-m-t');

            // $dataPenjualan->where('MONTH(tanggal)', date('m'))->where('YEAR(tanggal)', date('Y'));
            $dataPenjualan->where("tanggal BETWEEN '$start' AND '$end'");

            $filter['tanggal'] = date("d/m/Y", strtotime($start)) . " - " . date("d/m/Y", strtotime($end));
        }

        if ($status) {
            if ($status == 1) {
                $dataPenjualan->where('tb_penjualan.tunai >= tb_penjualan.total_akhir');
            } else if ($status == 0) {
                $dataPenjualan->where('tb_penjualan.tunai < tb_penjualan.total_akhir')->orWhere('tb_penjualan.tunai', null);
            }
        } else {
            $dataPenjualan->where('tb_penjualan.tunai >= tb_penjualan.total_akhir');
        }

        $dataPenjualan = $dataPenjualan->findAll();

        $dataTransaksi = [];
        foreach ($dataPenjualan as $item) {
            $dataTransaksi[$item['id']] = $this->transaksi->select('tb_transaksi.*, tb_item.nama_item, tb_item.barcode, tb_item.stok')
                ->join('tb_item', 'tb_item.id = tb_transaksi.id_item', 'left')
                ->where('id_penjualan', $item['id'])
                ->findAll();
        }

        $data = [
            'title'     => 'Laporan Penjualan | ' . mediumdate_indo($start) . " - " . mediumdate_indo($end),
            'data'      => $dataPenjualan,
            'transaksi' => $dataTransaksi,
            'isLunas'   => $status,
            'filter'    => $filter
        ];

        return view('laporan/penjualan', $data);
    }

    public function penjualanPrint()
    {
        $tanggal = $this->request->getGet('tanggal');
        $status = $this->request->getGet('status');
        $filter = $this->request->getGet();

        $dataPenjualan = $this->penjualan->select('tb_penjualan.*, tb_users.nama as kasir')
            ->join('tb_users', 'tb_users.id = tb_penjualan.id_user', 'left')
            ->orderBy('tb_penjualan.updated_at', 'asc');

        if ($tanggal) {
            $start = date('Y-m-d', strtotime(str_replace('/', '-', trim(explode('-', $tanggal)[0]))));
            $end = date('Y-m-d', strtotime(str_replace('/', '-', trim(explode('-', $tanggal)[1]))));

            // $dataPenjualan->where('MONTH(tanggal)', date('m', strtotime($tanggal)))->where('YEAR(tanggal)', date('Y', strtotime($tanggal)));
            $dataPenjualan->where("tanggal BETWEEN '$start' AND '$end'");
        } else {
            $start = date('Y-m-1');
            $end = date('Y-m-t');

            // $dataPenjualan->where('MONTH(tanggal)', date('m'))->where('YEAR(tanggal)', date('Y'));
            $dataPenjualan->where("tanggal BETWEEN '$start' AND '$end'");

            $filter['tanggal'] = date("d/m/Y", strtotime($start)) . " - " . date("d/m/Y", strtotime($end));
        }

        if ($status) {
            if ($status == 1) {
                $dataPenjualan->where('tb_penjualan.tunai >= tb_penjualan.total_akhir');
            } else if ($status == 0) {
                $dataPenjualan->where('tb_penjualan.tunai < tb_penjualan.total_akhir')->orWhere('tb_penjualan.tunai', null);
            }
        } else {
            $dataPenjualan->where('tb_penjualan.tunai >= tb_penjualan.total_akhir');
        }

        $dataPenjualan = $dataPenjualan->findAll();

        // total akhir penjualan
        $totalPendapatan = 0;
        foreach ($dataPenjualan as $item) {
            $totalPendapatan += $item['total_akhir'];
        }

        // persen untung penjualan dibandingkan dengan bulan sebelumnya
        $totalPemdapatanBulanLalu = $this->penjualan->selectSum('total_akhir')->where('tunai >', 0)->where('tunai !=', null)->where('MONTH(tanggal)', date('m', strtotime('-1 month', strtotime($tanggal ?? date('Y-m')))))->where('YEAR(tanggal)', date('Y', strtotime('-1 month', strtotime($tanggal ?? date('Y-m')))))->first();

        $persenUntung = 0;
        if ($totalPemdapatanBulanLalu['total_akhir'] > 0) {
            $persenUntung = ($totalPendapatan - $totalPemdapatanBulanLalu['total_akhir']) / $totalPemdapatanBulanLalu['total_akhir'] * 100;
        }

        // total item terjual
        $totalItemTerjual = 0;
        $dataTransaksi = [];
        foreach ($dataPenjualan as $item) {
            $dataTransaksi[$item['id']] = $this->transaksi->select('tb_transaksi.*, tb_item.nama_item, tb_item.barcode, tb_item.stok')
                ->join('tb_item', 'tb_item.id = tb_transaksi.id_item', 'left')
                ->where('id_penjualan', $item['id'])
                ->findAll();

            foreach ($dataTransaksi[$item['id']] as $t) {
                $totalItemTerjual += $t['jumlah_item'];
            }
        }

        $data = [
            'title'     => 'Laporan Penjualan | ' . ($tanggal ? month_year_indo(date('Y-m', strtotime($tanggal))) : month_year_indo(date('Y-m'))),
            'data'      => $dataPenjualan,
            'transaksi' => $dataTransaksi,
            'totalPendapatan' => $totalPendapatan,
            'persenUntung' => $persenUntung,
            'totalItemTerjual' => $totalItemTerjual,
            'totalPendapatanBulanLalu' => $totalPemdapatanBulanLalu['total_akhir'] ?? 0,
            'isLunas'   => $status,
            'filter'    => $filter,
        ];

        $html = view('laporan/penjualan-print', $data);

        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper('A4', 'potrait');
        $this->dompdf->render();

        $tgl_name = $start == $end ? $start : "$start ~ $end";

        $this->dompdf->stream('laporan-penjualan-' . $tgl_name . '.pdf', ['Attachment' => false]);

        exit(0);
    }

    public function pendapatan()
    {
        $month = $this->request->getGet('bulan');

        if ($month) {
            $bulan = date('m', strtotime($month));
            $tahun = date('Y', strtotime($month));
        } else {
            $bulan = date('m');
            $tahun = date('Y');
        }

        $pendapatanBulananDetail = $this->penjualan->select('tanggal, SUM(total_akhir) as total')->where('tunai >', 0)->where('tunai !=', null)->where('YEAR(tanggal)', $tahun)->groupBy('MONTH(tanggal)')->findAll();

        $pendapatan_bulanan_array_total = [];
        foreach ($pendapatanBulananDetail as $key => $value) {
            $pendapatan_bulanan_array_total[] = $value['total'];
        }

        $averageTahunan = array_sum($pendapatan_bulanan_array_total) / count($pendapatan_bulanan_array_total);
        $minTahunan = min($pendapatan_bulanan_array_total);
        $maxTahunan = max($pendapatan_bulanan_array_total);

        $data = [
            'title'                     => 'Laporan Pendapatan',
            'filter'                    => $this->request->getGet() ? $this->request->getGet() : ['bulan' => date('Y-m')],

            'pendapatanHarianDetail'    => $this->penjualan->select('tanggal, SUM(total_akhir) as total')->where('tunai >', 0)->where('tunai !=', null)->where('YEAR(tanggal)', $tahun)->where('MONTH(tanggal)', $bulan)->groupBy('tanggal')->findAll(),
            'pendapatanBulananDetail'   => $pendapatanBulananDetail,
            'pendapatanHariIniDetail'   => $this->penjualan->select('tanggal, SUM(total_akhir) as total')->where('tunai >', 0)->where('tunai !=', null)->where('tanggal', date('Y-m-d'))->groupBy('tanggal')->findAll(),

            'pendapatanBulanan'         => $this->penjualan->selectSum('total_akhir')->where('tunai >', 0)->where('tunai !=', null)->where('YEAR(tanggal)', $tahun)->where('MONTH(tanggal)', $bulan)->first(),
            'pendapatanTahunan'         => $this->penjualan->selectSum('total_akhir')->where('tunai >', 0)->where('tunai !=', null)->where('YEAR(tanggal)', $tahun)->first(),
            'pendapatanHariIni'         => $this->penjualan->selectSum('total_akhir')->where('tunai >', 0)->where('tunai !=', null)->where('tanggal', date('Y-m-d'))->first(),

            'averageBulanan'            => $this->penjualan->selectAvg('total_akhir')->where('tunai >', 0)->where('tunai !=', null)->where('YEAR(tanggal)', $tahun)->where('MONTH(tanggal)', $bulan)->first(),
            'averageTahunan'            => $averageTahunan,
            'averageHariIni'            => $this->penjualan->selectAvg('total_akhir')->where('tunai >', 0)->where('tunai !=', null)->where('tanggal', date('Y-m-d'))->first(),

            'minBulanan'                => $this->penjualan->selectMin('total_akhir')->where('tunai >', 0)->where('tunai !=', null)->where('YEAR(tanggal)', $tahun)->where('MONTH(tanggal)', $bulan)->first(),
            'minTahunan'                => $minTahunan,
            'minHariIni'                => $this->penjualan->selectMin('total_akhir')->where('tunai >', 0)->where('tunai !=', null)->where('tanggal', date('Y-m-d'))->first(), // 'minHariIni'                => '

            'maxBulanan'                => $this->penjualan->selectMax('total_akhir')->where('tunai >', 0)->where('tunai !=', null)->where('YEAR(tanggal)', $tahun)->where('MONTH(tanggal)', $bulan)->first(),
            'maxTahunan'                => $maxTahunan,
            'maxHariIni'                => $this->penjualan->selectMax('total_akhir')->where('tunai >', 0)->where('tunai !=', null)->where('tanggal', date('Y-m-d'))->first(),

        ];

        return view('laporan/pendapatan', $data);
    }

    public function pembelian()
    {
        $tanggal = $this->request->getGet('tanggal');
        $filter = $this->request->getGet();

        if ($tanggal) {
            $start = date('Y-m-d', strtotime(str_replace('/', '-', trim(explode('-', $tanggal)[0]))));
            $end = date('Y-m-d', strtotime(str_replace('/', '-', trim(explode('-', $tanggal)[1]))));
        } else {
            $start = date('Y-m-1');
            $end = date('Y-m-t');

            $filter['tanggal'] = date("d/m/Y", strtotime($start)) . " - " . date("d/m/Y", strtotime($end));
        }

        $pembalianBulanan = $this->transaksi_barang
            ->select("tb_transaksi_barang.*, tb_barang.kode, tb_barang.barang, tb_barang.stok as total_stok, tb_users.nama as kasir")
            ->join("tb_barang", "tb_barang.id = tb_transaksi_barang.id_barang", "left")
            ->join("tb_users", "tb_users.id = tb_transaksi_barang.id_user", "left")
            ->where("DATE(tb_transaksi_barang.created_at) BETWEEN '$start' AND '$end'")
            ->orderBy("tb_transaksi_barang.created_at", "asc")
            ->findAll();

        $data = [
            'title'                     => 'Laporan Pembelian | ' . mediumdate_indo($start) . " - " . mediumdate_indo($end),
            'filter'                    => $filter,
            'pembelianBulanan'          => $pembalianBulanan,
        ];

        return view('laporan/pembelian-2', $data);
    }

    public function pembelianPrint()
    {
        $tanggal = $this->request->getGet('tanggal');
        $filter = $this->request->getGet();

        if ($tanggal) {
            $start = date('Y-m-d', strtotime(str_replace('/', '-', trim(explode('-', $tanggal)[0]))));
            $end = date('Y-m-d', strtotime(str_replace('/', '-', trim(explode('-', $tanggal)[1]))));
        } else {
            $start = date('Y-m-1');
            $end = date('Y-m-t');

            $filter['tanggal'] = date("d/m/Y", strtotime($start)) . " - " . date("d/m/Y", strtotime($end));
        }

        $pembalianBulanan = $this->transaksi_barang
            ->select('tb_transaksi_barang.*, tb_barang.kode, tb_barang.barang, tb_barang.stok as total_stok, tb_users.nama as kasir')
            ->join('tb_barang', 'tb_barang.id = tb_transaksi_barang.id_barang', 'left')
            ->join('tb_users', 'tb_users.id = tb_transaksi_barang.id_user', 'left')
            ->where("DATE(tb_transaksi_barang.created_at) BETWEEN '$start' AND '$end'")
            ->orderBy('tb_transaksi_barang.created_at', 'ASC')
            ->findAll();

        $totalItem = 0;
        $totalUang = 0;

        foreach ($pembalianBulanan as $item) {
            $totalItem += $item->jml_beli;
            $totalUang += $item->total;
        }

        $data = [
            'title'                     => 'Laporan Pembelian | ' . mediumdate_indo($start) . " - " . mediumdate_indo($end),
            'filter'                    => $filter,
            'pembelianBulanan'          => $pembalianBulanan,
            'totalItem'                 => $totalItem,
            'totalUang'                 => $totalUang,
        ];

        $html = view('laporan/pembelian-2-print', $data);

        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper('A4', 'potrait');
        $this->dompdf->render();

        $tgl_name = $start == $end ? $start : "$start ~ $end";

        $this->dompdf->stream('laporan-pembelian-' . $tgl_name . '.pdf', ['Attachment' => false]);

        exit(0);
    }
}
