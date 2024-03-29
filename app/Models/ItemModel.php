<?php

namespace App\Models;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\Model;

class ItemModel extends Model
{
    protected $table      = 'tb_item';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    // protected $useSoftDeletes = true;
    protected $allowedFields = ['barcode', 'nama_item', 'id_kategori', 'id_unit', 'harga', 'stok', 'gambar'];
    protected $useTimestamps = true;

    public function detailItem($id = null)
    {
        $builder = $this->builder($this->table)->select('tb_item.id AS iditem, barcode, nama_item AS item, harga, stok, gambar, nama_unit AS unit, nama_kategori AS kategori')
            ->join('tb_unit', 'tb_unit.id = id_unit')
            ->join('tb_kategori', 'tb_kategori.id = id_kategori');
        if (empty($id)) {
            return $builder->get()->getResult(); // tampilkan semua data
        } else {
            // tampilkan data sesuai id/barcode
            return $builder->where('tb_item.id', $id)->orWhere('barcode', $id)->get(1)->getRow();
        }
    }

    public function barcodeModel($keyword)
    {
        // get barcode nama_item id_kategori id_unit dari tb_item dan nama_unit dari tb_unit 
        $data = $this->builder($this->table)->select('tb_item.barcode, tb_item.nama_item, tb_unit.nama_unit, tb_kategori.nama_kategori')
            ->join('tb_unit', 'tb_unit.id = id_unit')
            ->join('tb_kategori', 'tb_kategori.id = id_kategori')
            ->like('barcode', $keyword)
            ->orLike('nama_item', $keyword)
            ->get()->getResult();

        return $data;


        // return $this->builder($this->table)->select('barcode, nama_item, id_kategori, id_unit')
        // ->like('barcode', $keyword)
        // ->orLike('nama_item', $keyword)
        // ->get()->getResult();
    }

    public function cariProduk($keyword)
    {
        $builder = $this->builder($this->table);
        $query = $builder->select('barcode, nama_item');
        if (empty($keyword)) {
            $data = $query->get(10)->getResult();
        } else {
            $data = $query->like('barcode', $keyword)->orLike('nama_item', $keyword)->get()->getResult();
        }
        return $data;
    }
}
