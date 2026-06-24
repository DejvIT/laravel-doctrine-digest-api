<?php

namespace App\Http\Controllers;

use App\EntityRepositories\BloggerRepository;
use App\Entities\ArticleCategory;
use App\Entities\Blogger;
use App\Exceptions\SloneekExceptions\SloneekUnauthorizedException;
use App\Http\Requests\LoginRequest;
use App\Services\JwtService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct(
        private readonly BloggerRepository $bloggerRepository,
    ) {
    }

    public function login(LoginRequest $request, JwtService $jwtService): JsonResponse
    {
        $credentials = $request->validated();
        $blogger = $this->bloggerRepository->findByEmail($credentials['email']);

        if ($blogger === null || !Hash::check($credentials['password'], $blogger->getPassword())) {
            throw new SloneekUnauthorizedException(__('be.responses.auth.invalidCredentials'));
        }

        return $this->successResponse([
            'token' => $jwtService->issueToken($blogger),
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        /** @var Blogger $blogger */
        $blogger = $request->attributes->get('blogger');

        return $this->successResponse($this->serializeBlogger($blogger));
    }

    /**
     * @return array<string, mixed>
     */
    private function serializeBlogger(Blogger $blogger): array
    {
        return [
            'uuid'       => $blogger->getUuid(),
            'email'      => $blogger->getEmail(),
            'name'       => $blogger->getName(),
            'categories' => array_map(
                fn (ArticleCategory $category) => [
                    'uuid' => $category->getUuid(),
                    'name' => $category->getName(),
                ],
                $blogger->getCategories()->toArray()
            ),
        ];
    }
}
