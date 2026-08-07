<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Auth;
use Illuminate\Http\Request;

class ApiTableController extends Controller
{
    public function tableLeads()
    {
        if (Auth::check()) {
            $user = Auth::user(); // Pastikan Anda mendapatkan objek pengguna terlebih dahulu

            $hasil = Client::select('client.*', 'p.name_pic', 'i.issue', 'u.name')
                ->join('pic as p', 'client.id', '=', 'p.id_client')
                ->join('issues as i', 'client.id_issues', '=', 'i.id')
                ->join('users as u', 'client.id_sales', '=', 'u.id')
                ->leftJoin('activities as a', 'a.id_client', '=', 'client.id')
                ->where('client.role', 'Leads')
                ->where('u.id', $user->id)
                ->groupBy('client.id')
                ->orderByDesc('a.date')
                ->get([
                    'client.*',
                    'p.name_pic',
                    'i.issue',
                    'u.name',
                    \DB::raw('MAX(a.date) as date'),
                    \DB::raw('MAX(a.follow_up) as follow_up'),
                    \DB::raw('MAX(a.note) as note'),
                ]);

            dd($user);

            // Mengembalikan hasil dalam format JSON
            return response()->json(['data' => $hasil]);
        }else {
            // Pengguna tidak terotentikasi
            echo json_encode(['error' => 'Pengguna tidak terotentikasi'], JSON_PRETTY_PRINT);
        }
    }
}
