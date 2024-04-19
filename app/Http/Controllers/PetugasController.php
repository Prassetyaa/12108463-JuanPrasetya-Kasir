<?php

namespace App\Http\Controllers;

use App\Models\DetailPenjualan;
use App\Models\Pelanggan;
use App\Models\Penjualan;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PetugasController extends Controller
{
    public function penjualan()
    {
        $penjualans = Penjualan::with(['pelanggan', 'detailPenjualan.product'])->get();
        $pelanggans = Pelanggan::all();
        return view('petugas.penjualan', compact('penjualans','pelanggans'));
    }


    public function dashboard()
    {
        $products = Produk::all();
        return view('petugas.dashboard-petugas', compact('products'));
    }

    public function struk(Request $request, $id_pelanggan)
    {
        $pelanggan = Pelanggan::findOrFail($id_pelanggan);
        return view('petugas.struk', compact('pelanggan'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:produks,id',
            'namapelanggan' => 'required',
            'alamat' => 'required',
            'telepon' => 'required',
            'quantity' => 'required',
        ]);

        $produks = Produk::findOrFail($request->product_id);

        $total_price = $produks->harga * $request->quantity;

        $pelanggan = Pelanggan::create([
            'namapelanggan' => $request->namapelanggan,
            'alamat' => $request->alamat,
            'telepon' => $request->telepon,
        ]);

        $created_by_user_id = auth()->id();

        $penjualan = Penjualan::create([
            'total_price' => $total_price,
            'pelanggan_id' => $pelanggan->id,
            'created_by_user_id' => $created_by_user_id,
        ]);

        DetailPenjualan::create([
            'penjualan_id' => $penjualan->id,
            'produk_id' => $request->product_id,
            'quantity' => $request->quantity,
            'subtotal' => $total_price,
        ]);

        // Kurangi stok produk
        $produks->stock -= $request->total_product;
        $produks->save();

        return redirect()->route('struk', ['id_pelanggan' => $pelanggan->id , 'id_penjualan'=> $penjualan->id])->with('success', 'Transaksi berhasil disimpan.');
    }


    public function update(Request $request, $id)
{
    $validator = Validator::make($request->all(), [
        'nama.'.$id => 'required',
        'alamat.'.$id => 'required',
        'telepon.'.$id => 'required',
    ]);


    $data['nama'] = $request->input('nama.'.$id);
    $data['alamat'] = $request->input('alamat.'.$id);
    $data['telepon'] = $request->input('telepon.'.$id);

    Pelanggan::whereId($id)->update($data);

    return redirect()->route('pelanggan-list')->withSuccess('Successfully Updated Purchase');
}



    public function destroy($id)
    {
        $penjualans = Penjualan::find($id);
        $penjualans->delete();
        return back()->with("mantap", "History deleted successfully.");
    }
}
