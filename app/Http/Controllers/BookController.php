<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
        public function index()
    {
        // $books = Book::all();
        // return response()->json($books);
         $items = Book::paginate(10); // Menampilkan 10 item per halaman

         

    $items->getCollection()->transform(function ($item) {
        return [
            'id' => $item->id,
            'judul' => $item->judul,
            'penulis' => $item->penulis,
            'penerbit' => $item->penerbit,
            'tahun_terbit' => $item->tahun_terbit,
            'harga' => $item->harga,
            'sinopsis' => $item->sinopsis,
            'gambar' => $item->url, // Menggunakan aksesor 'url' untuk mengambil URL gambar
            'create_at' => $item->create_at,
            'update_at' => $item->update_at,
        ];
    });

    return response()->json([
        'data' => $items->getCollection(),
        'next_url' => $items->nextPageUrl(),
        'prev_url' => $items->previousPageUrl(),
        'pagination' => [
            'current_page' => $items->currentPage(),
            'last_page' => $items->lastPage(),
            'per_page' => $items->perPage(),
            'total' => $items->total(),
        ],
    ]);
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

   public function update(Request $request, $id)
{
    $book = Book::find($id);
    if (!$book) {
        return response()->json(['message' => 'Buku tidak dbookukan'], 404);
    }
    // $request->validate([
    //     'judul' => 'required',
    //     'penulis' => 'required',
    //     'penerbit' => 'required',
    //     'tahun_terbit' => 'required',
    //     'harga' => 'required',
    //     'sinopsis' => 'required',
    //     'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    // ]);

    // Hapus gambar lama jika ada dan simpan gambar baru
    if ($request->hasFile('gambar')) {
        // Hapus gambar lama jika ada
        if ($book->gambar) {
            $path = public_path('images/' . $book->gambar);
            if (file_exists($path)) {
                unlink($path);
            }
        }

        $gambar = $request->file('gambar');
        $nama_gambar = time().'.'.$gambar->getClientOriginalExtension();
        $gambar->move(public_path('images'), $nama_gambar);
        $book->gambar = $nama_gambar;
    }

    $book->judul = $request->input('judul', $book->judul);
    $book->penulis = $request->input('penulis', $book->penulis);
    $book->penerbit = $request->input('penerbit', $book->penerbit);
    $book->tahun_terbit = $request->input('tahun_terbit', $book->tahun_terbit);
    $book->harga = $request->input('harga', $book->harga);
    $book->sinopsis = $request->input('sinopsis', $book->sinopsis);
    $book->save();

    return response()->json(['message' => 'book updated successfully', 'data' => $book]);
}


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
  public function show($id)
    {
        $item = Book::find($id);
          return response()->json([
            'id' => $item->id,
            'judul' => $item->judul,
            'penulis' => $item->penulis,
            'penerbit' => $item->penerbit,
            'tahun_terbit' => $item->tahun_terbit,
            'harga' => $item->harga,
            'sinopsis' => $item->sinopsis,
            'gambar' => $item->url, // Menggunakan aksesor 'url' untuk mengambil URL gambar
            'create_at' => $item->create_at,
            'update_at' => $item->update_at,
    ]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
        'judul' => 'required',
        'penulis' => 'required',
        'penerbit' => 'required',
        'tahun_terbit' => 'required',
        'harga' => 'required',
        'sinopsis' => 'required',
        'gambar' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);
    
    // Hapus gambar lama jika ada dan simpan gambar baru
     $nama_gambar = null;

    if ($request->hasFile('gambar')) {
        $gambar = $request->file('gambar');
        $nama_gambar = time().'.'.$gambar->extension();
        $gambar->move(public_path('images'), $nama_gambar);
    } else {
        // Set gambar default jika tidak ada gambar yang diunggah
        $nama_gambar = 'default.jpg';
    }
    
    $item = Book::create([
        'judul' => $request->judul,
        'penulis' => $request->penulis,
        'penerbit' => $request->penerbit,
        'tahun_terbit' => $request->tahun_terbit,
        'harga' => $request->harga,
        'sinopsis' => $request->sinopsis,
        'gambar' => $nama_gambar,
    ]);


     return response()->json([
    'message' => 'Item created successfully',
    'data' => [
        'id' => $item->id,
        'judul' => $item->judul,
        'penerbit' => $item->penerbit,
        'tahun_terbit' => $item->tahun_terbit,
        'harga' => $item->harga,
        'sinopsis' => $item->sinopsis,
        'gambar' => $item->gambar ? asset('images/' . $item->gambar) : asset('images/default.jpg'),
    ]
], 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
         $item = Book::findOrFail($id);
    
    // Hapus gambar jika ada sebelum menghapus item
    if ($item->gambar) {
        // Hapus file gambar dari direktori uploads
        $path = public_path('images/' . $item->gambar);
        if (file_exists($path)) {
            unlink($path);
        }
    }

    $item->delete();

    return response()->json(['message' => 'Item deleted successfully']);
    }
}
