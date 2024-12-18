<?php

namespace App\Form;

use App\Entity\Categories;
use App\Entity\Products;
use App\Repository\CategoriesRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', options: ['label' => 'Nom'])
            ->add('description')
            ->add('price', options: ['label' => 'Prix'])
            ->add('stock', options: ['label' => 'Unité'])
            // ->add('slug')
            // ->add('created_at', null, [
            //     'widget' => 'single_text'
            // ]) ON ENLEVE CAR PAS BESOIN
            // comme catégorie est une entité on est obligé d'avoir EntityType
            ->add('categories', EntityType::class, [
                'class' => Categories::class,
                'choice_label' => 'name',
                'label' => 'Catégorie',
                'group_by' => 'parent.name',
                'query_builder' => function (CategoriesRepository $cr) {
                    return $cr->createQueryBuilder('c')
                        ->where('c.parent IS NOT NULL')
                        ->orderBy('c.name', 'ASC');
                }
                // on choisit le nom pour choice_label et pas l'id
                // le query_builder est une fonction qui va être executé de facon à sélectionner les info qu'on souhaite: ici il va chercher les cat dont le parent n'est pas null
                // Arreté à 27:00
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Products::class,
        ]);
    }
}