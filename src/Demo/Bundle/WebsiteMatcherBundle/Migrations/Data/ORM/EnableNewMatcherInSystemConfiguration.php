<?php
declare(strict_types=1);

namespace Demo\Bundle\WebsiteMatcherBundle\Migrations\Data\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use Oro\Component\PhpUtils\ArrayUtil;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * In a freshly installed system with the default system configuration all website matchers are considered as enabled.
 * But if a new website matcher is added later, there might already be a stored system configuration that does not
 * include this new matcher. In this case either an administrator has to go to System Configuration -> Websites ->
 * Routing and update the settings to enable the new website matcher, or we can create a migration that will do it
 * for them. This class is an example of such migration.
 */
class EnableNewMatcherInSystemConfiguration extends AbstractFixture implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function load(ObjectManager $manager)
    {
        $configManager = $this->container->get('oro_config.global');
        $matchersConfig = $configManager->get('oro_multiwebsite.website_matchers_settings');
        if ($matchersConfig) {
            // Use the same alias and priority as in services.yml, and the same label and tooltip as in the class.
            $newMatcherConfig = [
                'enabled' => true,
                'matcher_alias' => 'url_and_cookie',
                'label' => 'demo_website_matcher.url_and_cookie_matcher.label',
                'tooltip' => 'demo_website_matcher.url_and_cookie_matcher.label',
                'priority' => 500
            ];

            $found = false;
            foreach (\array_keys($matchersConfig) as $key) {
                if ('url_and_cookie' === $matchersConfig[$key]['matcher_alias']) {
                    $matchersConfig[$key] = $newMatcherConfig;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $matchersConfig[] = $newMatcherConfig;
            }
            ArrayUtil::sortBy($matchersConfig, true, 'priority');
            $configManager->set('oro_multiwebsite.website_matchers_settings', $matchersConfig);
            $configManager->flush();
        } // else is not needed because in absence of any configuration all matchers are considered enabled
    }
}
