<?php

namespace Demo\Bundle\PricingBundle\Feature;

use Oro\Bundle\FeatureToggleBundle\Checker\Voter\VoterInterface;

/**
 * Disable oro_pricing
 */
class PricingVoter implements VoterInterface
{
    const PRICING_FEATURE_NAME = 'oro_pricing';

    /**
     * {@inheritdoc}
     */
    public function vote($feature, $scopeIdentifier = null)
    {
        if ($feature === self::PRICING_FEATURE_NAME) {
            return VoterInterface::FEATURE_DISABLED;
        }

        return VoterInterface::FEATURE_ABSTAIN;
    }
}
