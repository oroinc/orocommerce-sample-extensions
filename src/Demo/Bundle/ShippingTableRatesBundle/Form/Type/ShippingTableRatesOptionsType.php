<?php

namespace Demo\Bundle\ShippingTableRatesBundle\Form\Type;

use Demo\Bundle\ShippingTableRatesBundle\Method\ShippingTableRatesMethodType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShippingTableRatesOptionsType extends AbstractType
{
    const BLOCK_PREFIX = 'demo_shipping_table_rates_options_type';

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    ShippingTableRatesMethodType::PER_ITEM_TYPE =>
                        'demo.shippingtablerates.method.processing_type.per_item.label',
                    ShippingTableRatesMethodType::PER_ORDER_TYPE =>
                        'demo.shippingtablerates.method.processing_type.per_order.label',
                ],
                'label' => 'demo.shippingtablerates.method.processing_type.label',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => 'demo.shippingtablerates.form.demo_shipping_table_rates_options_type.label',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return self::BLOCK_PREFIX;
    }
}
