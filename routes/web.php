<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'home.index')->name('home');
Route::view('/katalog', 'katalog.index')->name('katalog');
Route::view('/detail-mobil', 'detail-mobil.index')->name('detail-mobil');
Route::view('/booking', 'booking.index')->name('booking');
Route::view('/pembayaran', 'pembayaran.index')->name('pembayaran');
Route::view('/pembayaran/invoice', 'pembayaran.invoice-dummy')->name('pembayaran.invoice');
Route::view('/pembayaran/cetak-bukti', 'pembayaran.cetak-bukti')->name('pembayaran.cetak');
