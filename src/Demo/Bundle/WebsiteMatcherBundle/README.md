# DemoWebsiteMatcherBundle

This bundle contains an example of a custom website matcher for OroCommerce Enterprise Edition.

# Create and register your custom website matcher

1. Create your custom matcher by either implementing \Oro\Bundle\MultiWebsiteBundle\Matcher\WebsiteMatcherInterface or simply extend \Oro\Bundle\MultiWebsiteBundle\Matcher\AbstractWebsiteMatcher as it already contains the default implementations of many required methods.

2. Add the service definition of your custom matcher to a [DI container configuration file](Resources/config/services.yml#L2-L5) and make sure to [tag it with **oro_website_matcher tag**](Resources/config/services.yml#L5).
