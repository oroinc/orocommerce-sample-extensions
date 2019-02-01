<?php

namespace Demo\Bundle\ShippingTableRatesBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Oro\Bundle\IntegrationBundle\Entity\Transport;
use Oro\Bundle\LocaleBundle\Entity\LocalizedFallbackValue;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * @ORM\Entity
 */
class ShippingTableRatesSettings extends Transport
{
    /**
     * @var Collection|LocalizedFallbackValue[]
     *
     * @ORM\ManyToMany(
     *      targetEntity="Oro\Bundle\LocaleBundle\Entity\LocalizedFallbackValue",
     *      cascade={"ALL"},
     *      orphanRemoval=true
     * )
     * @ORM\JoinTable(
     *      name="demo_ship_tbl_rates_trans_lbl",
     *      joinColumns={
     *          @ORM\JoinColumn(name="transport_id", referencedColumnName="id", onDelete="CASCADE")
     *      },
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(name="localized_value_id", referencedColumnName="id", onDelete="CASCADE", unique=true)
     *      }
     * )
     */
    protected $labels;

    /** @var ParameterBag */
    protected $settings;

    /**
     * ShippingTableRatesSettings constructor.
     */
    public function __construct()
    {
        $this->labels = new ArrayCollection();
    }

    /**
     * @return Collection|LocalizedFallbackValue[]
     */
    public function getLabels()
    {
        return $this->labels;
    }

    /**
     * @param LocalizedFallbackValue $label
     *
     * @return ShippingTableRatesSettings
     */
    public function addLabel(LocalizedFallbackValue $label)
    {
        if (!$this->labels->contains($label)) {
            $this->labels->add($label);
        }

        return $this;
    }

    /**
     * @param LocalizedFallbackValue $label
     *
     * @return ShippingTableRatesSettings
     */
    public function removeLabel(LocalizedFallbackValue $label)
    {
        if ($this->labels->contains($label)) {
            $this->labels->removeElement($label);
        }

        return $this;
    }

    /**
     * @return ParameterBag
     */
    public function getSettingsBag()
    {
        if (null === $this->settings) {
            $this->settings = new ParameterBag([]);
        }

        return $this->settings;
    }
}
