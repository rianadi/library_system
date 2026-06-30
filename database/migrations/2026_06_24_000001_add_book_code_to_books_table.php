<?php

use App\Models\Book;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('books', 'book_code')) {
            Schema::table('books', function (Blueprint $table) {
                $table->string('book_code', 30)->nullable()->after('id')->index();
            });
        }

        $usedCodes = DB::table('books')
            ->whereNotNull('book_code')
            ->where('book_code', '!=', '')
            ->pluck('book_code')
            ->mapWithKeys(fn (string $code): array => [strtoupper($code) => true])
            ->all();

        DB::table('books')
            ->select('id')
            ->where(function ($query): void {
                $query->whereNull('book_code')
                    ->orWhere('book_code', '');
            })
            ->orderBy('id')
            ->chunkById(100, function ($books) use (&$usedCodes): void {
                foreach ($books as $book) {
                    $baseCode = Book::codeForId((int) $book->id);
                    $code = $baseCode;
                    $counter = 2;

                    while (isset($usedCodes[strtoupper($code)])) {
                        $code = $baseCode.'-'.$counter;
                        $counter++;
                    }

                    $usedCodes[strtoupper($code)] = true;

                    DB::table('books')
                        ->where('id', $book->id)
                        ->update(['book_code' => $code]);
                }
            });
    }

    public function down(): void
    {
        if (Schema::hasColumn('books', 'book_code')) {
            Schema::table('books', function (Blueprint $table) {
                $table->dropColumn('book_code');
            });
        }
    }
};
