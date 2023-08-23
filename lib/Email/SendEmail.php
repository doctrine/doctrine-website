<?php

declare(strict_types=1);

namespace Doctrine\Website\Email;

use Doctrine\Website\Site;
use SendGrid;

final class SendEmail
{
    public function __construct(
        private Site $site,
        private SendGrid $sendGrid,
        private RenderEmail $renderEmail,
    ) {
    }

    /** @param mixed[] $parameters */
    public function __invoke(string $to, string $template, array $parameters = []): void
    {
        $parameters['site'] = $this->site;

        $renderedEmail = $this->renderEmail->__invoke($template, $parameters);

        $email = new SendGrid\Mail\Mail();
        $email->setFrom('doctrine@doctrine-project.org', 'Doctrine');
        $email->setSubject($renderedEmail->getSubject());
        $email->addTo($to);
        $email->addContent('text/plain', $renderedEmail->getBodyText());
        $email->addContent('text/html', $renderedEmail->getBodyHtml());

        $this->sendGrid->send($email);
    }
}
