<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{

    /**
     * supports est appelé par symfony lorsque l'on demande si un utilisateur a des droits d'accès
     * Si cette fonction renvoit true alors symfony demandera un vote
     */
    protected function supports($droitATester, $objetAuquelOnVeutAcceder)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($droitATester, ['EDIT'])
            && $objetAuquelOnVeutAcceder instanceof \App\Entity\User;
    }

    protected function voteOnAttribute($droitATester, $objetAuquelOnVeutAcceder, TokenInterface $token)
    {
        $userConnecte = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$userConnecte instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($droitATester) {
            case 'EDIT':

                if ($userConnecte == $objetAuquelOnVeutAcceder)
                {
                    return true;
                }
                if (in_array('ROLE_ADMIN', $userConnecte->getRoles()) )
                {
                    return true;
                }
                // logic to determine if the user can EDIT
                // return true or false
                break;
            case 'POST_VIEW':
                // logic to determine if the user can VIEW
                // return true or false
                break;
        }

        return false;
    }
}
