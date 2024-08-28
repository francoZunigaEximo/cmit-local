<?php

namespace App\Http\Controllers;

use App\Helpers\FileHelper;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class FileController extends Controller
{
    public function show($filePath)
    {
        $smbHelper = new FileHelper();

        $stream = $smbHelper->getFileStream($filePath);

        if ($stream) {
            $mimeType = mime_content_type($filePath); // Determina el tipo MIME del archivo
            return Response::stream(
                function () use ($stream) {
                    fclose($stream);
                },
                SymfonyResponse::HTTP_OK,
                ['Content-Type' => $mimeType]
            );
        } else {
            abort(404, 'File not found.');
        }
    }
}
