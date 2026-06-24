<?php

namespace App\Http\Controllers;

use App\Exceptions\SloneekExceptions\SloneekInternalErrorException;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller
{
    public function getVersion(): JsonResponse
    {
        $contents = file_get_contents(base_path('composer.json'));

        if ($contents === false) {
            throw new SloneekInternalErrorException();
        }

        $composerData = json_decode($contents, true);

        if (!is_array($composerData)) {
            throw new SloneekInternalErrorException();
        }

        return $this->successResponse(
            [
                'name'        => $composerData['name'] ?? '',
                'description' => $composerData['description'] ?? '',
            ]
        );
    }

}
