<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class BookExcelImportService
{
    private const COLUMN_ALIASES = [
        'title' => ['judul buku', 'judul', 'nama buku', 'title'],
        'author' => ['pengarang', 'penulis', 'author'],
        'isbn' => ['isbn'],
        'publisher' => ['penerbit', 'publisher'],
        'year' => ['tahun terbit', 'tahun', 'year'],
        'category' => ['kategori', 'category', 'jenis buku'],
        'total_copies' => ['jumlah', 'jumlah buku', 'jumlah eksemplar', 'eksemplar', 'stok', 'stock', 'total copies'],
        'available_copies' => ['tersedia', 'available copies', 'jumlah tersedia'],
        'description' => ['deskripsi', 'description', 'keterangan'],
        'location' => ['kode buku', 'kode', 'nomor panggil', 'no panggil', 'call number', 'lokasi', 'rak', 'location'],
    ];

    public function __construct(private SimpleXlsxReader $reader)
    {
    }

    public function import(string $path): array
    {
        $rows = $this->reader->rows($path);

        if ($rows === []) {
            throw ValidationException::withMessages([
                'excel_file' => 'File Excel kosong atau tidak memiliki baris data.',
            ]);
        }

        [$headerIndex, $columns] = $this->findHeader($rows);

        $summary = [
            'imported' => 0,
            'skipped' => 0,
            'failed' => 0,
            'processed' => 0,
            'errors' => [],
        ];

        $categoryIdsByName = Category::select('id', 'name')
            ->get()
            ->mapWithKeys(fn (Category $category) => [$this->normalizeKey($category->name) => $category->id])
            ->all();

        DB::transaction(function () use ($rows, $headerIndex, $columns, &$summary, &$categoryIdsByName): void {
            for ($index = $headerIndex + 1; $index < count($rows); $index++) {
                $row = $rows[$index];

                if (! $this->hasImportableValue($row, $columns)) {
                    $summary['skipped']++;
                    continue;
                }

                $summary['processed']++;

                try {
                    $data = $this->bookDataFromRow($row, $columns, $categoryIdsByName);

                    if ($this->bookAlreadyExists($data)) {
                        $summary['skipped']++;
                        continue;
                    }

                    Book::create($data);
                    $summary['imported']++;
                } catch (InvalidArgumentException $exception) {
                    $summary['failed']++;
                    $this->addError($summary, 'Baris '.($index + 1).': '.$exception->getMessage());
                }
            }
        });

        return $summary;
    }

    private function findHeader(array $rows): array
    {
        foreach ($rows as $index => $row) {
            $columns = [];

            foreach ($row as $position => $value) {
                $key = $this->normalizeKey($value);

                if ($key === '') {
                    continue;
                }

                foreach (self::COLUMN_ALIASES as $field => $aliases) {
                    if (in_array($key, $aliases, true) && ! isset($columns[$field])) {
                        $columns[$field] = $position;
                    }
                }
            }

            if (isset($columns['title'])) {
                return [$index, $columns];
            }
        }

        throw ValidationException::withMessages([
            'excel_file' => 'Header Excel harus memiliki kolom Judul Buku atau Judul.',
        ]);
    }

    private function bookDataFromRow(array $row, array $columns, array &$categoryIdsByName): array
    {
        $title = $this->requiredText($this->cell($row, $columns, 'title'), 'Judul Buku', 255);
        $author = $this->nullableText($this->cell($row, $columns, 'author'), 255) ?? 'Tidak diketahui';
        $publisher = $this->nullableText($this->cell($row, $columns, 'publisher'), 100);
        $totalCopies = $this->positiveInteger($this->cell($row, $columns, 'total_copies'), 1);
        $availableCopies = $this->positiveInteger($this->cell($row, $columns, 'available_copies'), $totalCopies);

        $availableCopies = min($availableCopies, $totalCopies);

        return [
            'title' => $title,
            'author' => $author,
            'isbn' => $this->normalizeIsbn($this->cell($row, $columns, 'isbn')),
            'publisher' => $publisher,
            'year' => $this->normalizeYear($this->cell($row, $columns, 'year')),
            'category_id' => $this->resolveCategoryId($this->cell($row, $columns, 'category'), $categoryIdsByName),
            'total_copies' => $totalCopies,
            'available_copies' => $availableCopies,
            'description' => $this->nullableText($this->cell($row, $columns, 'description')),
            'cover_image' => null,
            'location' => $this->nullableText($this->cell($row, $columns, 'location'), 50),
        ];
    }

    private function bookAlreadyExists(array $data): bool
    {
        if ($data['isbn'] !== null) {
            return Book::where('isbn', $data['isbn'])->exists();
        }

        $query = Book::query()
            ->where('title', $data['title'])
            ->where('author', $data['author']);

        foreach (['publisher', 'year', 'location'] as $column) {
            if ($data[$column] === null) {
                $query->whereNull($column);
            } else {
                $query->where($column, $data[$column]);
            }
        }

        return $query->exists();
    }

    private function resolveCategoryId(string $value, array &$categoryIdsByName): ?int
    {
        $name = $this->nullableText($value, 100);

        if ($name === null) {
            return null;
        }

        $key = $this->normalizeKey($name);

        if (isset($categoryIdsByName[$key])) {
            return $categoryIdsByName[$key];
        }

        $baseSlug = Str::slug($name) ?: 'kategori';
        $slug = $baseSlug;
        $counter = 2;

        while (Category::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        $category = Category::create([
            'name' => $name,
            'slug' => $slug,
        ]);

        $categoryIdsByName[$key] = $category->id;

        return $category->id;
    }

    private function cell(array $row, array $columns, string $field): string
    {
        if (! isset($columns[$field])) {
            return '';
        }

        return trim((string) ($row[$columns[$field]] ?? ''));
    }

    private function hasImportableValue(array $row, array $columns): bool
    {
        foreach (array_keys(self::COLUMN_ALIASES) as $field) {
            if ($this->cell($row, $columns, $field) !== '') {
                return true;
            }
        }

        return false;
    }

    private function requiredText(string $value, string $label, int $limit): string
    {
        $value = trim($value);

        if ($value === '') {
            throw new InvalidArgumentException($label.' wajib diisi.');
        }

        return mb_substr($value, 0, $limit);
    }

    private function nullableText(string $value, ?int $limit = null): ?string
    {
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        return $limit === null ? $value : mb_substr($value, 0, $limit);
    }

    private function normalizeIsbn(string $value): ?string
    {
        $value = strtoupper(preg_replace('/[\s-]+/', '', trim($value)) ?? '');

        if ($value === '') {
            return null;
        }

        if (mb_strlen($value) > 20) {
            return null;
        }

        return $value;
    }

    private function normalizeYear(string $value): ?int
    {
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        if (is_numeric($value)) {
            $year = (int) $value;
        } elseif (preg_match('/(19|20)\d{2}/', $value, $matches)) {
            $year = (int) $matches[0];
        } else {
            return null;
        }

        $maxYear = ((int) date('Y')) + 1;

        return $year >= 1900 && $year <= $maxYear ? $year : null;
    }

    private function positiveInteger(string $value, int $default): int
    {
        $value = trim(str_replace(',', '.', $value));

        if ($value === '') {
            return max(1, $default);
        }

        if (is_numeric($value)) {
            return max(1, (int) floor((float) $value));
        }

        if (preg_match('/\d+/', $value, $matches)) {
            return max(1, (int) $matches[0]);
        }

        return max(1, $default);
    }

    private function normalizeKey(string $value): string
    {
        $value = mb_strtolower(trim($value));
        $value = preg_replace('/[^\p{L}\p{N}]+/u', ' ', $value) ?? '';

        return trim(preg_replace('/\s+/', ' ', $value) ?? '');
    }

    private function addError(array &$summary, string $message): void
    {
        if (count($summary['errors']) < 8) {
            $summary['errors'][] = $message;
        }
    }
}
