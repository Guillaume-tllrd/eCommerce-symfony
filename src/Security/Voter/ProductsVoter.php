<?php

// c'est le fichier qui va permetre de dire que tel utilisateur avec tel role role va pouvoir de faire tel ou tel chose

namespace App\Security\Voter;

use App\Entity\Products;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
// use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\SecurityBundle\Security;

class ProductsVoter extends Voter
{

    const EDIT = 'PRODUCT_EDIT';
    const DELETE = 'PRODUCT_DELETE';

    private $security;
    // propriété qui va nous permettre d'aller chercher le composant sécurité

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $product): bool
    {
        // on vérifie si l'attribut n'est pas dans un tabelau avec les class edit ou delete:
        if (!in_array($attribute, [self::EDIT, self::DELETE])) {
            return false;
        }
        // ensuite on vérifie si product est une instance de Products
        if (!$product instanceof Products) {
            return false;
        }
        return true;
        // on aurait pu écrire directement:
        // return (in_array($attribute, [self::EDIT, self::DELETE])) && $product instanceof Products;
    }

    protected function voteOnAttribute($attribute, $product, TokenInterface $token): bool
    {

        //On récupère l'utilisateur à paritr du token:
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            //si l'utilisateur n'est pas connecté on return false
            return false;
        }

        // on vérifie si l'utilisateur est admin:
        if ($this->security->isGranted('ROLE_ADMIN')) return true;

        // On vérifie les permissions:
        switch ($attribute) {
            case self::EDIT:
                //On vérifie si l'utilisateur peut éditer, à partir du rôle product admin par ex
                return $this->canEdit();
                break;
            case self::DELETE:
                // on vérifie si l'utilisateur peut supprimer à partir du role_admin:
                return $this->canDelete();
                break;
        }
    }
    private function canEdit()
    {
        return $this->security->isGranted('ROLE_ADMIN');
    }
    private function canDelete()
    {
        return $this->security->isGranted('ROLE_ADMIN');
    }
}
