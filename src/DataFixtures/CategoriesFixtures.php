<?php

namespace App\DataFixtures;

use App\Entity\Categories;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoriesFixtures extends Fixture
{
    //on rajoute un counter pour ProuctsFixture où il nous faut un category_id
    private $counter = 1;
    public function __construct(private SluggerInterface $slugger) {}
    public function load(ObjectManager $manager): void
    {
        $parent = $this->createCategory('Informatique', null, $manager);
        // mtn on oeut utiliser la méthode de slugger comme on l'a mit dans le constructor
        // $parent->setSLug($this->slugger->slug(strtolower($parent->getName()))); plus besoin donc je commente à cause du create
        // je fais un get name pour avoir informatique par ex, lower pour mettre en minuscule
        // $manager->persist($parent);
        // persist pour valider et flush pour envoyer en bdd
        // $manager->flush();

        // pour en refaire une autre:
        // $category = new Categories();
        // $category->setName('Ordinateurs portable');
        // $category->setSLug($this->slugger->slug(strtolower($category->getName())));
        // $category->setParent($parent);
        // $manager->persist($category);
        // je change le nom de ctagory pour parent ppour que informatique soit le parent dans la bdd
        $this->createCategory('Ordinateurs portables', $parent, $manager);
        $this->createCategory('Ecrans', $parent, $manager);
        $this->createCategory('Souris', $parent, $manager);

        $parent = $this->createCategory('Mode', null, $manager);
        $this->createCategory('Homme', $parent, $manager);
        $this->createCategory('Femme', $parent, $manager);
        $this->createCategory('Enfant', $parent, $manager);
        $manager->flush();
    }

    public function createCategory(string $name, Categories $parent = null, ObjectManager $manager)
    {
        $category = new Categories();
        $category->setName($name);
        $category->setSLug($this->slugger->slug(strtolower($category->getName())));
        $category->setParent($parent);
        $manager->persist($category);

        // A chaque fois que je crée une catégorie je stocke son numéro donc on fait une reference: 

        $this->addReference('cat-' . $this->counter, $category);
        $this->counter++;

        return $category;
    }
}
