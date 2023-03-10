<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDF;
use Excel;
use App\Exports\ReportsExport;
use App\Models\Response;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // ASC : ascending -> terkecil terbesar 1-100 / a-z
        // DESC : ascending -> terbesar terkecil 100-1 /z-a

        // $reports = Report::all();
        $reports = Report::orderBy('created_at', 'DESC')->simplePaginate(2);
        return view('index', compact('reports'));
        // compact reports disamakan dengan yang $reports diatas retutn view
    }

    public function auth(Request $request)
    {
        // validasinya
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
        //  ambil data dan simpan di variable
        $user = $request->only('email', 'password');
        // simpen data ke auth dengan Auth::attempt
        // cek proses penyimpanan ke auth berhasil atau tidak lewat if else
        if (Auth::attempt($user)) {
            if (Auth::user()->role == 'admin') {
                return redirect()->route('data');
            }elseif(Auth::user()->role == 'petugas') {
                return redirect()->route('data.petugas');
            }
        }else {
            return redirect()->back()->with('gagal', 'Gagal login, coba lagi!');
        }
    }

    public function delete($id)
    {
      $data = Report::where('id', $id)->firstOrFail();
      $image = public_path('assets/image/'.$data['foto']);
        unlink('assets/image/' . $data['foto']);

      // hapus data dari database
      $data->delete();
        Response::where('report_id', $id)->delete();
      return redirect()->back();
    }


    // Request $request ditambah karena pada dalam halamamn data ada fitur search nya, dan akan menagmbil text yang diinput search
    public function data(Request $request)
    {
        // ambil data yang diinput ke input yg name nya search
        $search = $request->search;
        // where akan mengisi data berdasarkan kalumn nama
        // data yang diambil merupan data yang 'LIKE' (terdapat) text yang dimasukin ke input search
        // contoh : ngisi  input search dengan 'arief'
        // bakal nyari ke db yang namanya ada isi 'arief' nya

        // & = untuk mencari
        $report = Report::with('response')->Where('nama', 'LIKE', '%' . $search . '%')->orderBy('created_at', 'DESC')->get();
        return view('data', compact('report'));
    }

    public function dataPetugas(Request $request)
    {
        // ambil data yang diinput ke input yg name nya search
        $search = $request->search;
        // with
        $reports = Report::with('response')->where('nama', 'LIKE', '%' . $search . '%')->orderBy('created_at', 'DESC')->get();
        // & = untuk mencari
        // $report = Report::Where('nama', 'LIKE', '%' . $search . '%')->orderBy('created_at', 'DESC')->get();
        return view('data_petugas', compact('reports'));
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }

    public function exportPDF() { 
        // ambil data yg akan ditampilkan pada pdf, bisa juga dengan where atau eloquent lainnya dan jangan gunakan pagination
        // jangan lupa konvert data jadi array dengan toArray(  )
        $data = Report::with('response')->get()->toArray(); 
        // kirim data yg diambil kepada view yg akan ditampilkan, kirim dengan inisial 
        view()->share('reports',$data); 
        // panggil view blade yg akan dicetak pdf serta data yg akan digunakan
        $pdf = PDF::loadView('print', $data)->setpaper('a4', 'landscape');
        // download PDF file dengan nama tertentu
        return $pdf->download('data_pengaduan_keseluruhan.pdf'); 
    }
        
    
    public function printPDF($id) { 
        $data = Report::with('response')->where('id', $id)->get()->toArray();
        view()->share('reports',$data);
        $pdf = PDF::loadView('print', $data)->setpaper('a4', 'landscape');
        return $pdf->download('data_pengaduan.pdf'); 


    }

    public function exportExcel() {
        // nama file yang akan terdownload
        // selain .xlxx juga bisa .csv
        $file_name = 'data_keseluruhan.xlsx';
        // memanggil file ReportsExport dan mendownloadnya dengan nama seperti $file_name
        return Excel::download(new ReportsExport, $file_name); 
    }

    
    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required',
            'nama' => 'required',
            'no_telp' => 'required',
            'pengaduan' => 'required',
            'foto' => 'required |image|mimes:jpg,jpeg,png,svg',
        ]);
        // pindah foto ke folder public
        $path = public_path('assets/image/');
        $image = $request->file('foto');
        $imgName = rand() . '.' . $image->extension();
        $image->move($path, $imgName);
        // tambah data ke db
        report::create([
            'nik' => $request->nik,
            'nama' => $request->nama,
            "no_telp" => $request->no_telp,
            'pengaduan' => $request->pengaduan,
            'foto' => $imgName,
        ]);
        return redirect()->back()->with('success','Berhasil menambahkan pengaduan!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Report  $report
     * @return \Illuminate\Http\Response
     */
    public function show(Report $report)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Report  $report
     * @return \Illuminate\Http\Response
     */
    public function edit(Report $report)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Report  $report
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Report $report)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Report  $report
     * @return \Illuminate\Http\Response
     */
    // public function destroy(Report $report)
    // {
    //     //
    // }
}
