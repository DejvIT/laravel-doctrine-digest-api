<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Article Digest</title>
</head>
<body>
    <h1>Hello, {{ $subscriber->getName() }}!</h1>

    <p>Here are the latest articles from your subscribed categories:</p>

    <ul>
        @foreach ($articles as $article)
            <li>
                <strong>{{ $article->getTitle() }}</strong>
                — by {{ $article->getBlogger()->getName() }}
                in {{ $article->getCategory()->getName() }}
            </li>
        @endforeach
    </ul>

    <p>
        <a href="{{ config('app.url') }}">Visit Sloneek</a>
    </p>
</body>
</html>
