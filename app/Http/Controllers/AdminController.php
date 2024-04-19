<?php

namespace App\Http\Controllers;


use App\Models\Pelanggan;
use App\Models\Penjualan;
use App\Models\Produk ;
use App\Models\User;

class AdminController extends Controller
{
    public function dashboard()
    {
        $produk = Produk::all();
        $users = User::all();
        $totalUser = User::count();
        $pelanggans = Pelanggan::count();
        $totalProduk = Produk::count();

        return view('admin.dashboard', compact('produk','totalProduk','pelanggans','users','totalUser'));
    }

    public function pembelian()
    {
        $penjualans = Penjualan::with(['pelanggan', 'detailPenjualan.product'])->get();
        $pelanggans = Pelanggan::all();
        return view('admin.pembelian', compact('pelanggans','penjualans'));
    }
}
