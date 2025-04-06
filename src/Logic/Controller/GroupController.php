<?php

namespace App\Logic\Controller;

use App\Core\Controller;
use App\Logic\Model\Font;
use App\Logic\Model\Group;

class GroupController extends Controller
{
    function list()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            $this->json(['status' => 405, 'error' => 'UNSUPPORTED!']);
        }

        try {
            $groups = new Group()->fetchAll();

            http_response_code(200);
            $this->json([
                'status' => 200,
                'data' => $groups,
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

        $payload = filter_input_array(
            INPUT_POST,
            [
                'name' => [
                    'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                    'flags' => FILTER_REQUIRE_SCALAR,
                ],
                'fonts' => [
                    'filter' => FILTER_SANITIZE_NUMBER_INT,
                    'flags' => FILTER_REQUIRE_ARRAY,
                ],
            ],
            false,
        );

        try {
            $group = new Group();
            $group->name = $payload['name'];
            foreach ($payload['fonts'] as $fontId) {
                $font = new Font()->fetchOne($fontId);
                $group->fonts[] = $font;
            }
            $group->insert();

            http_response_code(201);
            $this->json([
                'status' => 201,
                'data' => [
                    'id' => $group->id,
                    'name' => $group->name,
                    'fonts' => $group->fonts,
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

    function modify()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            $this->json(['status' => 405, 'error' => 'UNSUPPORTED!']);
        }

        http_response_code(501);
        $this->json(['status' => 501, 'error' => 'UNIMPLEMENTED!']);
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
            $group = new Group();
            $group->id = $payload['id'];
            $group->delete();

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
