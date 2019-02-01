<?php

namespace Demo\Bundle\ShippingTableRatesBundle\Form\Type;

use Oro\Bundle\LocaleBundle\Form\Type\LocalizedFallbackValueCollectionType;
use Demo\Bundle\ShippingTableRatesBundle\Entity\ShippingTableRatesSettings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Form type for shipping table rates integration settings.
 */
class ShippingTableRatesSettingsType extends AbstractType
{
    const BLOCK_PREFIX = 'demo_shipping_table_rates_transport_settings';

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'labels',
                LocalizedFallbackValueCollectionType::class,
                [
                    'label'    => 'demo.shippingtablerates.transport.labels.label',
                    'required' => true,
                    'options'  => [
                        'constraints' => [new NotBlank()],
                    ],
                ]
            );
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ShippingTableRatesSettings::class
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
