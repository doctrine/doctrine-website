<?php

declare(strict_types=1);

namespace Doctrine\Website\Email;

final class RenderedEmail
{
    public function __construct(private string $subject, private string $bodyText, private string $bodyHtml)
    {
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getBodyText(): string
    {
        return $this->bodyText;
    }

    public function getBodyHtml(): string
    {
        return $this->bodyHtml;
    }
}
