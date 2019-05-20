<?php

declare(strict_types=1);

namespace Doctrine\Website\Email;

final class RenderedEmail
{
    /** @var string */
    private $subject;

    /** @var string */
    private $bodyText;

    /** @var string */
    private $bodyHtml;

    public function __construct(string $subject, string $bodyText, string $bodyHtml)
    {
        $this->subject  = $subject;
        $this->bodyText = $bodyText;
        $this->bodyHtml = $bodyHtml;
    }

    public function getSubject() : string
    {
        return $this->subject;
    }

    public function getBodyText() : string
    {
        return $this->bodyText;
    }

    public function getBodyHtml() : string
    {
        return $this->bodyHtml;
    }
}
