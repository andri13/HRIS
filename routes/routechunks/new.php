<?php

Route::prefix('datalembur')->group(function() {
    Route::post('verifikasi', 'DataLemburController@verifikasi')->name("hris.datalembur.verifikasi");
}); 

Route::prefix('employeeatr')->group(function() {
    Route::get('format-import', 'EmployeeAtrController@format_import_grading')->name("hris.employeeatr.format");
    Route::post('import-grading', 'EmployeeAtrController@import_grading')->name("hris.employeeatr.import.grading");
}); 

Route::prefix('dataabsenperijinan')->group(function() {
    Route::get('data-perijinan-verifikasi','DataAbsenPerijinanController@perijinan_verifikasi')->name("hris.dataabsenperijinan.verifikasi");
    Route::post('data-perijinan-get','DataAbsenPerijinanController@perijinan_verifikasi_get')->name("hris.dataabsenperijinan.get");
    Route::post('perijinan-verifikasi','DataAbsenPerijinanController@perijinan_verifikasi_store')->name("hris.verifikasiperijinan.store");
}); 

Route::prefix('koreksiupah')->group(function() {
    Route::get('format-koreksiupah','KoreksiUpahController@format_import_koreksiupah')->name("hris.koreksiupah.format");
    Route::post('import-koreksiupah', 'KoreksiUpahController@import_koreksiupah')->name("hris.employeeatr.import.koreksiupah");

}); 

Route::prefix('koreksipotongan')->group(function() {
    Route::get('format-koreksipotongan','KoreksiPotonganController@format_import_koreksipotongan')->name("hris.koreksipotongan.format");
    Route::post('import-koreksipotongan', 'KoreksiPotonganController@import_koreksipotongan')->name("hris.import.koreksipotongan");

}); 

Route::prefix('mdabsenhadir')->group(function() {
    Route::post('download_mesin_kehadiran-lintas', 'MdAbsenHadirController@download_mesin_kehadiran_lintas')->name("hris.mdabsenhadir.download_mesin_kehadiran_lintas");

}); 



?>