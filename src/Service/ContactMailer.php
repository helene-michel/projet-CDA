<?php

namespace App\Service;

use App\Model\ContactMessage;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class ContactMailer
{
    public function __construct(
        private MailerInterface $mailer,
        #[Autowire('%env(CONTACT_EMAIL)%')] private string $contactEmail,
    ) {
    }

    public function send(ContactMessage $contact): void
    {
        $this->mailer->send((new TemplatedEmail())
            ->from(new Address('no-reply@lecomptoirdescouturiers.fr', 'Le comptoir des couturiers'))
            ->to($this->contactEmail)
            ->replyTo(new Address($contact->email, $contact->name))
            ->subject('[Contact] '.$contact->subject)
            ->textTemplate('page/contact_email.txt.twig')
            ->context(['contact' => $contact])
        );
    }
}
