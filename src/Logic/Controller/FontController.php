<?php

namespace App\Logic\Controller;

use App\Core\Controller;
use App\Logic\Model\Font;

class FontController extends Controller
{
    private $directory;

    function __construct()
    {
        $this->directory = implode(
            DIRECTORY_SEPARATOR,
            [dirname(__DIR__, 3), 'public', 'fonts']
        );
    }

    function list()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            $this->json(['status' => 405, 'error' => 'UNSUPPORTED!']);
        }

        try {
            $fonts = new Font()->fetchAll();

            http_response_code(200);
            $this->json([
                'status' => 200,
                'data' => $fonts,
            ]);
        } catch (\Throwable $th) {
            http_response_code(500);
            $this->json([
                'status' => 500,
                'error' => [
                    'message' => $th->getMessage(),
                    'file' => pathinfo($th->getFile(), PATHINFO_BASENAME),
                    'line' => $th->getLine(),
                ],
            ]);
        }
    }

    function persist()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            $this->json(['status' => 405, 'error' => 'UNSUPPORTED!']);
        }

        try {
            $font = $_FILES['font'];

            if ($font['error'] !== UPLOAD_ERR_OK) {
                http_response_code(400);
                $this->json([
                    'status' => 400,
                    'error' => $font['error'],
                ]);
                return;
            }

            $fileInfo = pathinfo($font['name']);

            $name = ucfirst($fileInfo['filename']);
            $file = uniqid() . '.' . $fileInfo['extension'];
            $path = implode(DIRECTORY_SEPARATOR, [$this->directory, $file]);

            move_uploaded_file($font['tmp_name'], $path);

            $font = new Font();
            $font->name = $name;
            $font->path = $file;
            $font->insert();

            http_response_code(201);
            $this->json([
                'status' => 201,
                'data' => [
                    'id' => $font->id,
                    'name' => $font->name,
                    'path' => $font->path,
                ],
            ]);
        } catch (\Throwable $th) {
            http_response_code(500);
            $this->json([
                'status' => 500,
                'error' => [
                    'message' => $th->getMessage(),
                    'file' => pathinfo($th->getFile(), PATHINFO_BASENAME),
                    'line' => $th->getLine(),
                ],
            ]);
        }
    }

    function destroy()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            $this->json(['status' => 405, 'error' => 'UNSUPPORTED!']);
        }

        $payload = filter_input_array(
            INPUT_POST,
            ['id' => FILTER_SANITIZE_NUMBER_INT],
            false,
        );

        try {
            $font = new Font();
            $font->id = $payload['id'];
            $font->delete();

            $fontPath = implode(
                DIRECTORY_SEPARATOR,
                [$this->directory, $font->path]
            );
            if (file_exists($fontPath)) unlink($fontPath);

            http_response_code(204);
        } catch (\Throwable $th) {
            http_response_code(500);
            $this->json([
                'status' => 500,
                'error' => [
                    'message' => $th->getMessage(),
                    'file' => pathinfo($th->getFile(), PATHINFO_BASENAME),
                    'line' => $th->getLine(),
                ],
            ]);
        }
    }
}
