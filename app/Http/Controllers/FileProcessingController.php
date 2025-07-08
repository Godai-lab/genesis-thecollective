<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProcessFileContentService;

class FileProcessingController extends Controller
{
    public function processFile(Request $request)
    {
        
        $request->validate([
            'file' => 'required|file',
        ]);
        
        $file = $request->file('file');
        $filePath = $file->getPathname();
        $extension = $file->getClientOriginalExtension();
        $text = '';

        switch ($extension) {
            case 'pdf':
                $text = ProcessFileContentService::processPdf($filePath);
                break;
            case 'docx':
            case 'doc':
                $text = ProcessFileContentService::processWord($filePath);
                break;
            case 'xlsx':
            case 'xls':
                $text = ProcessFileContentService::processExcel($filePath);
                break;
            case 'csv':
                $text = ProcessFileContentService::processCSV($filePath);
                break;
            case 'txt':
                $text = ProcessFileContentService::processTxt($filePath);
                break;
            default:
                return response()->json(['error' => 'Unsupported file type'], 400);
        }

        return response()->json(['text' => $text]);
    }
}
