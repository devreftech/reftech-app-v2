<?php

namespace App\Http\Controllers;

use App\Models\SalesOnline;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SalesOnlineController extends Controller
{
    public function store(Request $request)
    {
        // dd($request->all());
        if ($request->type == 'Product') {
            foreach ($request->product as $key => $value) {
                if (!empty($value)) {
                    $salon = new SalesOnline();
                    $salon->id_sales = Auth::user()->id;
                    $salon->product = $request->product[$key];
                    $salon->desc_product = $request->desc_product[$key];
                    $salon->airend = 0;
                    $salon->kojisha = 0;
                    $salon->average = 0;
                    $salon->ig = $request->instagram;
                    $salon->tiktok = $request->tiktok;
                    $salon->tokped = $request->tokopedia;
                    $salon->type = $request->type;
                    $salon->date = Carbon::now();
                    $salonSave = $salon->save();
                }
            }
        } else {
            $salon = new SalesOnline();
            $salon->id_sales = Auth::user()->id;
            if ($request->airend) {
                $salon->airend = str_replace(',', '.', ($request->airend));
                if (Auth::user()->id == '16') {
                    $salon->kojisha = str_replace(',', '.', ($request->kojisha));
                    if ($request->average) {
                        $salon->average = str_replace(',', '.', ($request->average));
                    }
                } else {
                    $salon->kojisha = 0;
                    $salon->average = str_replace(',', '.', ($request->airend));
                }

            } else {
                $salon->airend = 0;
                $salon->kojisha = 0;
                $salon->average = 0;
            }
            $salon->product = $request->product;
            $salon->ig = $request->instagram;
            $salon->tiktok = $request->tiktok;
            $salon->tokped = $request->tokopedia;
            $salon->type = $request->type;
            $salon->date = Carbon::now();
            $salonSave = $salon->save();
        }

        if ($salonSave) {
            return redirect('/')->with('message', 'data telah dibuat');
        }
    }
    public function update(Request $request, $id)
    {
        // dd($request->all());
        if ($request->type == 'Product') {
            $product = SalesOnline::where('id_sales', Auth::user()->id)->where('type', 'product')->whereDate('date', Carbon::now())->get();
            foreach ($product as $online) {
                $online->delete();
            }
            foreach ($request->product as $key => $value) {
                if (!empty($value)) {
                    $salon = new SalesOnline();
                    $salon->id_sales = Auth::user()->id;
                    $salon->product = $request->product[$key];
                    $salon->desc_product = $request->desc_product[$key];
                    $salon->airend = 0;
                    $salon->kojisha = 0;
                    $salon->average = 0;
                    $salon->ig = $request->instagram;
                    $salon->tiktok = $request->tiktok;
                    $salon->tokped = $request->tokopedia;
                    $salon->type = $request->type;
                    $salon->date = Carbon::now();
                    $salonSave = $salon->save();
                }
            }
        } else {
            $salon = SalesOnline::find($id);
            $salon->id_sales = Auth::user()->id;
            if ($request->airend) {
                $salon->airend = str_replace(',', '.', ($request->airend));

                if (Auth::user()->id == 16) {
                    $salon->kojisha = str_replace(',', '.', ($request->kojisha));
                    $salon->average = str_replace(',', '.', ($request->average));
                } else {
                    $salon->kojisha = 0;
                    $salon->average = $salon->airend = str_replace(',', '.', ($request->airend));
                }

            } else {
                $salon->airend = 0;
                $salon->kojisha = 0;
                $salon->average = 0;
            }
            $salon->product = $request->product;
            $salon->ig = $request->instagram;
            $salon->tiktok = $request->tiktok;
            $salon->tokped = $request->tokopedia;
            $salon->type = $request->type;
            $salon->date = Carbon::now();
            $salonSave = $salon->save();
        }

        if ($salonSave) {
            return redirect('/')->with('message', 'data telah dibuat');
        }
    }
}
