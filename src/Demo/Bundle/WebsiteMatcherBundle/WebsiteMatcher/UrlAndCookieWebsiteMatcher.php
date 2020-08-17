<?php
declare(strict_types=1);

namespace Demo\Bundle\WebsiteMatcherBundle\WebsiteMatcher;

use Oro\Bundle\MultiWebsiteBundle\Matcher\AbstractWebsiteMatcher;

/**
 * This custom website matcher determines the website based on the website id stored in a browser cookie.
 * The current URL affects which browser cookie will be checked. For all ".com" domains we look into
 * "last_visited_website" cookie, and for all ".eu" domains we look into "user_selected_website".
 */
class UrlAndCookieWebsiteMatcher extends AbstractWebsiteMatcher
{
    public function match()
    {
        $request = $this->requestStack->getMasterRequest();
        if ($request) {
            $host = $request->getHost();
            if (false !== \strpos('.com', $host)) {
                $cookieName = 'last_visited_website';
            } elseif (false !== \strpos('.eu', $host)) {
                $cookieName = 'user_selected_website';
            } else {
                // let other matchers do the work
                return null;
            }

            $websiteId = (int) $request->cookies->get($cookieName);
            if ($websiteId) {
                return $this->getWebsiteReference($websiteId);
            }
        }

        return null;
    }

    public function getLabel()
    {
        // The label that will be used in the system configuration UI.
        // It's a good practice to use a translation key instead of returning the actual text.
        return 'demo_website_matcher.url_and_cookie_matcher.label';
    }

    public function getTooltip()
    {
        // Additional hint that will be displayed in the system configuration UI.
        // It's a good practice to use a translation key instead of returning the actual text.
        return 'demo_website_matcher.url_and_cookie_matcher.label';
    }
}
