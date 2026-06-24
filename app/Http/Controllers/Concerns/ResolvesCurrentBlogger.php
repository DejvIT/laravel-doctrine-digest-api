<?php

namespace App\Http\Controllers\Concerns;

use App\Entities\Blogger;
use App\Exceptions\SloneekExceptions\SloneekUnauthorizedException;
use Illuminate\Http\Request;

trait ResolvesCurrentBlogger
{
    protected function currentBlogger(Request $request): Blogger
    {
        return $request->attributes->get('blogger')
            ?? throw new SloneekUnauthorizedException();
    }
}
