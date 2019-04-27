<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;

class FilterEmailType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('upload_file', Type\FileType::class, [
                'label' => 'Upload file',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('output_format', Type\TextType::class, [
                'label' => 'Output Format',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                ],
                'data' => 'uid|email'
            ])
//            ->add('lst_filter', Type\TextareaType::class, [
//                'label' => 'List need filter',
//                'required' => false,
//                'attr' => [
//                    'class' => 'form-control',
//                    'rows' => 30,
//                    'cols' => 50
//                ],
//            ])
            ->add('support_domains', Type\CollectionType::class, [
                'required' => false,
                'entry_type' => Type\HiddenType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false,
                'data' => [
                    'yahoo.*',
                    'hotmail.*',
                    'outlook.*',
                    'gmail.*',
                ]
            ]);
    }
}
