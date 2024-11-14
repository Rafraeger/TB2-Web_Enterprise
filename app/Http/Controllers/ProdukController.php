<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Http\Requests\StoreProdukRequest;
use App\Http\Requests\UpdateProdukRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    public function ViewProduk()
{
    // $produk = Produk::all(); // mengambil semua data di tabel produk
    $isAdmin = Auth::user()->role == 'admin';
    // jika user adalah admin, maka tampilkan semua data, jika bukan admin, maka tampilkan data dengan user_id yang sama dengan user yang login
    $produk = $isAdmin ? Produk::all() : Produk::where('user_id', Auth::user()->id)->get();

    return view('produk', ['produk' => $produk]); // menampilkan view dari produk.blade.php dengan membawa variabel $produk
}


    public function CreateProduk(Request $request)
    {
        // // Validate the request
        // $request->validate([
        //     'nama_produk' => 'required|string|max:255',
        //     'deskripsi' => 'required|string',
        //     'harga' => 'required|numeric',
        //     'jumlah_produk' => 'required|integer',
        //     'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        // ]);

        $imageName = null;
        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $imageName = time() . '_' . $imageFile->getClientOriginalName();
            $imageFile->storeAs('public/images', $imageName);
        }

        Produk::create([
            'nama_produk' => $request->nama_produk,
            'deskripsi' => $request->deskripsi,
            'harga' => $request->harga,
            'jumlah_produk' => $request->jumlah_produk,
            'image' => $imageName,
            'user_id' => Auth::user()->id
        ]);

        return redirect(Auth::user()->role . '/produk');
    }

    public function ViewAddProduk()
    {
        return view('addproduk');
    }

    public function DeleteProduk($kode_produk)
    {
        Produk::where('kode_produk', $kode_produk)->delete();

        return redirect(Auth::user()->role . '/produk');
    }

    public function ViewEditProduk($kode_produk)
    {
        $ubahproduk = Produk::where('kode_produk', $kode_produk)->first();
        return view('editproduk', compact('ubahproduk'));
    }

    public function UpdateProduk(Request $request, $kode_produk)
    {
        // Validate the request
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'harga' => 'required|numeric',
            'jumlah_produk' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imageName = null;
        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $imageName = time() . '_' . $imageFile->getClientOriginalName();
            $imageFile->storeAs('public/images', $imageName);
        }

        Produk::where('kode_produk', $kode_produk)->update([
            'nama_produk' => $request->nama_produk,
            'deskripsi' => $request->deskripsi,
            'harga' => $request->harga,
            'jumlah_produk' => $request->jumlah_produk,
            'image' => $imageName
        ]);

        return redirect(Auth::user()->role . '/produk');
    }
    public function ViewLaporan()
    {
        $isAdmin = Auth::user()->role == 'admin';
        // jika user adalah admin, maka tampilkan semua data, jika bukan admin, maka tampilkan data dengan user_id yang sama dengan user yang login
        $products = $isAdmin ? Produk::all() : Produk::where('user_id', Auth::user()->id)->get();
        // $products = Produk::all();
        return view('laporan', ['products' => $products]);
    }
    public function print()
    {
        // Mengambil semua data produk

        $isAdmin = Auth::user()->role == 'admin';
        // jika user adalah admin, maka tampilkan semua data, jika bukan admin, maka tampilkan data dengan user_id yang sama dengan user yang login
        $products = $isAdmin ? Produk::all() : Produk::where('user_id', Auth::user()->id)->get();

        // $products = Produk::all();

        // Load view untuk PDF dengan data produk
        $pdf = Pdf::loadView('report', ['products' => $products]);

        // Menampilkan PDF langsung di browser
        return $pdf->stream('laporan-produk.pdf');
    }
}
