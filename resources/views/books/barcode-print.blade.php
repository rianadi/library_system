@php
    use App\Models\Book;
    use App\Support\Code128Barcode;

    $code = $book->book_code ?: Book::codeForId($book->id);
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Barcode {{ $code }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            color: #111827;
            font-family: Arial, sans-serif;
            margin: 18mm;
        }
        .sheet {
            align-items: center;
            display: flex;
            justify-content: center;
            min-height: calc(100vh - 36mm);
        }
        .label {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 14px 16px;
            text-align: center;
            width: 86mm;
        }
        .school {
            color: #075aa5;
            font-size: 8pt;
            font-weight: 700;
            letter-spacing: .08em;
            margin-bottom: 7px;
            text-transform: uppercase;
        }
        .barcode {
            margin: 8px auto 6px;
            max-width: 100%;
            overflow: hidden;
        }
        .barcode svg {
            height: 25mm;
            max-width: 100%;
            width: 100%;
        }
        .code {
            font-size: 12pt;
            font-weight: 700;
            letter-spacing: .12em;
        }
        .title {
            font-size: 10pt;
            font-weight: 700;
            line-height: 1.3;
            margin-top: 8px;
        }
        .meta {
            color: #475569;
            font-size: 8pt;
            line-height: 1.35;
            margin-top: 4px;
        }
        @media print {
            body { margin: 10mm; }
            .sheet { min-height: 0; }
        }
    </style>
</head>
<body>
    <main class="sheet">
        <section class="label">
            <div class="school">Perpustakaan SMP Negeri 11 Jember</div>
            <div class="barcode">{!! Code128Barcode::svg($code) !!}</div>
            <div class="code">{{ $code }}</div>
            <div class="title">{{ $book->title }}</div>
            <div class="meta">
                {{ $book->author }}
                @if($book->location)
                    <br>Lokasi: {{ $book->location }}
                @endif
                @if($book->category)
                    <br>{{ $book->category->name }}
                @endif
            </div>
        </section>
    </main>

    <script>window.print();</script>
</body>
</html>
