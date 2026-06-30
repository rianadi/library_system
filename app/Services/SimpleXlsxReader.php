<?php

namespace App\Services;

use RuntimeException;
use SimpleXMLElement;

class SimpleXlsxReader
{
    private const SPREADSHEET_NS = 'http://schemas.openxmlformats.org/spreadsheetml/2006/main';
    private const RELATIONSHIPS_NS = 'http://schemas.openxmlformats.org/package/2006/relationships';
    private const OFFICE_RELATIONSHIPS_NS = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships';

    public function rows(string $path): array
    {
        if (! is_file($path) || ! is_readable($path)) {
            throw new RuntimeException('File Excel tidak dapat dibaca.');
        }

        $entries = $this->readZipEntries($path);
        $worksheetPath = $this->resolveFirstWorksheetPath($path, $entries);

        if (! isset($entries[$worksheetPath])) {
            throw new RuntimeException('Sheet pertama tidak ditemukan di file Excel.');
        }

        $sharedStrings = $this->readSharedStrings($path, $entries);
        $sheet = $this->loadXml($this->readEntry($path, $entries[$worksheetPath]));
        $sheet->registerXPathNamespace('sheet', self::SPREADSHEET_NS);

        $rows = [];

        foreach ($sheet->xpath('//sheet:sheetData/sheet:row') ?: [] as $row) {
            $values = [];
            $maxColumn = -1;
            $position = 0;

            foreach ($row->children(self::SPREADSHEET_NS)->c as $cell) {
                $attributes = $cell->attributes();
                $column = isset($attributes['r'])
                    ? $this->columnIndex((string) $attributes['r'])
                    : $position;

                $values[$column] = $this->cellValue($cell, (string) ($attributes['t'] ?? ''), $sharedStrings);
                $maxColumn = max($maxColumn, $column);
                $position++;
            }

            if ($maxColumn < 0) {
                continue;
            }

            $normalizedRow = [];

            for ($column = 0; $column <= $maxColumn; $column++) {
                $normalizedRow[$column] = trim((string) ($values[$column] ?? ''));
            }

            if ($this->hasValue($normalizedRow)) {
                $rows[] = $normalizedRow;
            }
        }

        return $rows;
    }

    private function resolveFirstWorksheetPath(string $path, array $entries): string
    {
        if (! isset($entries['xl/workbook.xml'], $entries['xl/_rels/workbook.xml.rels'])) {
            return 'xl/worksheets/sheet1.xml';
        }

        $workbook = $this->loadXml($this->readEntry($path, $entries['xl/workbook.xml']));
        $workbook->registerXPathNamespace('sheet', self::SPREADSHEET_NS);

        $sheets = $workbook->xpath('//sheet:sheets/sheet:sheet') ?: [];
        $firstSheet = $sheets[0] ?? null;

        if (! $firstSheet instanceof SimpleXMLElement) {
            return 'xl/worksheets/sheet1.xml';
        }

        $relationships = $firstSheet->attributes(self::OFFICE_RELATIONSHIPS_NS);
        $relationshipId = (string) ($relationships['id'] ?? '');

        if ($relationshipId === '') {
            return 'xl/worksheets/sheet1.xml';
        }

        $rels = $this->loadXml($this->readEntry($path, $entries['xl/_rels/workbook.xml.rels']));
        $rels->registerXPathNamespace('rel', self::RELATIONSHIPS_NS);

        foreach ($rels->xpath('//rel:Relationship') ?: [] as $relationship) {
            $attributes = $relationship->attributes();

            if ((string) ($attributes['Id'] ?? '') !== $relationshipId) {
                continue;
            }

            $target = str_replace('\\', '/', (string) ($attributes['Target'] ?? ''));

            if ($target === '') {
                break;
            }

            return str_starts_with($target, '/')
                ? ltrim($target, '/')
                : 'xl/'.ltrim($target, '/');
        }

        return 'xl/worksheets/sheet1.xml';
    }

    private function readSharedStrings(string $path, array $entries): array
    {
        if (! isset($entries['xl/sharedStrings.xml'])) {
            return [];
        }

        $xml = $this->loadXml($this->readEntry($path, $entries['xl/sharedStrings.xml']));
        $xml->registerXPathNamespace('sheet', self::SPREADSHEET_NS);

        $strings = [];

        foreach ($xml->xpath('//sheet:si') ?: [] as $item) {
            $item->registerXPathNamespace('sheet', self::SPREADSHEET_NS);
            $text = '';

            foreach ($item->xpath('.//sheet:t') ?: [] as $node) {
                $text .= (string) $node;
            }

            $strings[] = $text;
        }

        return $strings;
    }

    private function cellValue(SimpleXMLElement $cell, string $type, array $sharedStrings): string
    {
        if ($type === 'inlineStr') {
            $cell->registerXPathNamespace('sheet', self::SPREADSHEET_NS);
            $text = '';

            foreach ($cell->xpath('.//sheet:t') ?: [] as $node) {
                $text .= (string) $node;
            }

            return $text;
        }

        $children = $cell->children(self::SPREADSHEET_NS);
        $rawValue = isset($children->v) ? (string) $children->v : '';

        if ($type === 's') {
            return $sharedStrings[(int) $rawValue] ?? '';
        }

        if ($type === 'b') {
            return $rawValue === '1' ? 'TRUE' : 'FALSE';
        }

        return $rawValue;
    }

    private function columnIndex(string $cellReference): int
    {
        preg_match('/^[A-Z]+/i', $cellReference, $matches);
        $letters = strtoupper($matches[0] ?? 'A');
        $index = 0;

        foreach (str_split($letters) as $letter) {
            $index = ($index * 26) + (ord($letter) - ord('A') + 1);
        }

        return max(0, $index - 1);
    }

    private function hasValue(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return true;
            }
        }

        return false;
    }

    private function loadXml(string $contents): SimpleXMLElement
    {
        $xml = simplexml_load_string($contents);

        if (! $xml instanceof SimpleXMLElement) {
            throw new RuntimeException('File Excel memiliki XML yang tidak valid.');
        }

        return $xml;
    }

    private function readZipEntries(string $path): array
    {
        $handle = fopen($path, 'rb');

        if ($handle === false) {
            throw new RuntimeException('File Excel tidak dapat dibuka.');
        }

        try {
            $size = filesize($path);
            $tailSize = min((int) $size, 65557);

            fseek($handle, (int) $size - $tailSize);
            $tail = fread($handle, $tailSize);

            if ($tail === false) {
                throw new RuntimeException('File Excel tidak dapat dibaca.');
            }

            $eocdOffset = strrpos($tail, "PK\x05\x06");

            if ($eocdOffset === false) {
                throw new RuntimeException('File yang diupload bukan file .xlsx yang valid.');
            }

            $eocd = substr($tail, $eocdOffset, 22);
            $directory = unpack(
                'Vsignature/vdisk/vcentralDisk/vdiskEntries/ventries/VcentralSize/VcentralOffset/vcommentLength',
                $eocd
            );

            if (! is_array($directory)) {
                throw new RuntimeException('Struktur file Excel tidak valid.');
            }

            fseek($handle, (int) $directory['centralOffset']);
            $centralDirectory = fread($handle, (int) $directory['centralSize']);

            if ($centralDirectory === false) {
                throw new RuntimeException('Daftar file di dalam Excel tidak dapat dibaca.');
            }

            $entries = [];
            $offset = 0;
            $length = strlen($centralDirectory);

            while ($offset < $length) {
                if (substr($centralDirectory, $offset, 4) !== "PK\x01\x02") {
                    break;
                }

                $header = unpack(
                    'Vsignature/vmadeBy/vneeded/vflags/vmethod/vmodifiedTime/vmodifiedDate/Vcrc/VcompressedSize/VuncompressedSize/vnameLength/vextraLength/vcommentLength/vdiskStart/vinternalAttributes/VexternalAttributes/VlocalOffset',
                    substr($centralDirectory, $offset, 46)
                );

                if (! is_array($header)) {
                    throw new RuntimeException('Header file Excel tidak valid.');
                }

                $nameStart = $offset + 46;
                $name = substr($centralDirectory, $nameStart, (int) $header['nameLength']);

                $entries[$name] = [
                    'compressedSize' => (int) $header['compressedSize'],
                    'flags' => (int) $header['flags'],
                    'localOffset' => (int) $header['localOffset'],
                    'method' => (int) $header['method'],
                    'uncompressedSize' => (int) $header['uncompressedSize'],
                ];

                $offset += 46
                    + (int) $header['nameLength']
                    + (int) $header['extraLength']
                    + (int) $header['commentLength'];
            }

            return $entries;
        } finally {
            fclose($handle);
        }
    }

    private function readEntry(string $path, array $entry): string
    {
        if (($entry['flags'] & 1) === 1) {
            throw new RuntimeException('File Excel yang diproteksi password tidak dapat diimport.');
        }

        $handle = fopen($path, 'rb');

        if ($handle === false) {
            throw new RuntimeException('File Excel tidak dapat dibuka.');
        }

        try {
            fseek($handle, $entry['localOffset']);
            $localHeader = fread($handle, 30);

            if ($localHeader === false || strlen($localHeader) < 30 || substr($localHeader, 0, 4) !== "PK\x03\x04") {
                throw new RuntimeException('Isi file Excel tidak valid.');
            }

            $header = unpack(
                'Vsignature/vneeded/vflags/vmethod/vmodifiedTime/vmodifiedDate/Vcrc/VcompressedSize/VuncompressedSize/vnameLength/vextraLength',
                $localHeader
            );

            if (! is_array($header)) {
                throw new RuntimeException('Isi file Excel tidak valid.');
            }

            fseek($handle, $entry['localOffset'] + 30 + (int) $header['nameLength'] + (int) $header['extraLength']);
            $compressed = fread($handle, $entry['compressedSize']);

            if ($compressed === false) {
                throw new RuntimeException('Isi file Excel tidak dapat dibaca.');
            }

            if ($entry['method'] === 0) {
                return $compressed;
            }

            if ($entry['method'] !== 8) {
                throw new RuntimeException('Metode kompresi Excel tidak didukung.');
            }

            $contents = gzinflate($compressed);

            if ($contents === false) {
                throw new RuntimeException('Isi file Excel tidak dapat diekstrak.');
            }

            return $contents;
        } finally {
            fclose($handle);
        }
    }
}
