<?php

namespace App\Services;

use PhpOffice\PhpWord\IOFactory as WordIOFactory;
use PhpOffice\PhpWord\Shared\XMLReader;
use PhpOffice\PhpSpreadsheet\IOFactory as SpreadsheetIOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Http;
use PhpOffice\PhpWord\Element\TextRun;

class ProcessFileContentService
{
    // Método para procesar archivos PDF
    public static function processPdf($filePath)
    {
        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($filePath);
            $pdfText = $pdf->getText();
            return $pdfText;
        } catch (\Exception $e) {
            // Manejar cualquier error que ocurra durante el procesamiento del PDF
            return null;
        }
    }

    // Método para procesar archivos Word
    public static function processWord($filePath)
    {
        try {
            $phpWord = WordIOFactory::load($filePath);
            $text = '';
            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    if ($element instanceof TextRun) {
                        $text .= $element->getText() . "\n";
                    }
                }
            }
            return $text;
        } catch (\Exception $e) {
            // Manejar cualquier error que ocurra durante el procesamiento del archivo Word
            return null;
        }
    }

    public static function processExcel($filePath)
    {
        try {
            $spreadsheet = SpreadsheetIOFactory::load($filePath);
            $text = '';

            foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
                foreach ($worksheet->getRowIterator() as $row) {
                    foreach ($row->getCellIterator() as $cell) {
                        $text .= $cell->getValue() . "\t";
                    }
                    $text = rtrim($text, "\t"); // Remove the trailing tab character
                    $text .= "\n";
                }
            }
            return $text;
        } catch (\Exception $e) {
            // Manejar cualquier error que ocurra durante el procesamiento del archivo Excel
            return null;
        }
    }

    public static function processCSV($filePath)
    {
        try {
            if (!file_exists($filePath) || !is_readable($filePath)) {
                throw new \Exception("El archivo no existe o no se puede leer.");
            }

            $text = '';
            if (($handle = fopen($filePath, 'r')) !== false) {
                while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                    $text .= implode("\t", $data) . "\n";
                }
                fclose($handle);
            }
            return $text;
        } catch (\Exception $e) {
            // Manejar cualquier error que ocurra durante el procesamiento del archivo CSV
            return null;
        }
    }

    public static function processCSV2($filePath)
    {
        try {
            if (!file_exists($filePath) || !is_readable($filePath)) {
                throw new \Exception("El archivo no existe o no se puede leer.");
            }

            $spreadsheet = SpreadsheetIOFactory::load($filePath);
            $text = '';
            $worksheet = $spreadsheet->getActiveSheet();
            foreach ($worksheet->getRowIterator() as $row) {
                $rowText = [];
                foreach ($row->getCellIterator() as $cell) {
                    $rowText[] = $cell->getValue();
                }
                $text .= implode("\t", $rowText) . "\n";
            }
            return $text;
        } catch (\Exception $e) {
            return null;
        }
    }

    // Método para procesar archivos TXT
    public static function processTxt($filePath)
    {
        try {
            $text = file_get_contents($filePath);
            return $text;
        } catch (\Exception $e) {
            // Manejar cualquier error que ocurra durante la lectura del archivo TXT
            return null;
        }
    }

    public static function processUrl($url)
    {
        try {
            $response = Http::get($url);

            if ($response->successful()) {
                $content = $response->body();
                $plainText = preg_replace('/<script\b[^>]*>(.*?)<\/script>|<style\b[^>]*>(.*?)<\/style>/is', '', $content);
                $plainText = strip_tags($plainText, "\n");
                $plainText = preg_replace('/\n\s*\n\s*\n*/', "\n\n", $plainText);
                return $plainText;
            } else {
                return null;
            }
        } catch (\Exception $e) {
            // Manejar cualquier error que ocurra durante la solicitud HTTP
            return null;
        }
    }
}