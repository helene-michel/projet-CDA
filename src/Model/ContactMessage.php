<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class ContactMessage
{
    #[Assert\NotBlank(message: 'Indiquez votre nom.')]
    #[Assert\Length(max: 100)]
    public ?string $name = null;

    #[Assert\NotBlank(message: 'Indiquez votre adresse email.')]
    #[Assert\Email(message: 'Cette adresse email n\'est pas valide.')]
    public ?string $email = null;

    #[Assert\NotBlank(message: 'Choisissez un sujet.')]
    public ?string $subject = null;

    #[Assert\NotBlank(message: 'Écrivez votre message.')]
    #[Assert\Length(min: 10, max: 3000, minMessage: 'Votre message doit faire au moins {{ limit }} caractères.')]
    public ?string $message = null;
}
