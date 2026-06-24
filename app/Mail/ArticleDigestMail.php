<?php

namespace App\Mail;

use App\Entities\Article;
use App\Entities\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ArticleDigestMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * @param list<Article> $articles
     */
    public function __construct(
        public Subscriber $subscriber,
        public array $articles,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your article digest',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.article-digest',
        );
    }
}
