<?php

namespace App\Security\Voter;

use App\Entity\Products;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Un vendeur ne peut gérer (voir dans son espace, modifier, supprimer) que ses propres produits.
 * Un administrateur peut tout gérer.
 *
 * @extends Voter<string, Products>
 */
final class ProductVoter extends Voter
{
    public const MANAGE = 'PRODUCT_MANAGE';

    public function __construct(private AccessDecisionManagerInterface $accessDecisionManager)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return self::MANAGE === $attribute && $subject instanceof Products;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        if ($this->accessDecisionManager->decide($token, ['ROLE_ADMIN'])) {
            return true;
        }

        return null !== $subject->getUser() && $subject->getUser()->getId() === $user->getId();
    }
}
