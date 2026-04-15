<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('payments', function (Blueprint $table) {
        // Ubah kolom status menjadi string biasa dengan panjang 50 karakter
        // Note: Kita gunakan string agar bisa menampung 'success', 'settlement', 'paid', dll.
        $table->string('status', 50)->change();
    });
}

public function down()
{
    Schema::table('payments', function (Blueprint $table) {
        // Kembalikan ke enum jika rollback (sesuaikan dengan nilai enum lama Anda)
        // $table->enum('status', ['pending', 'paid', 'failed'])->change();
    });
}
};
