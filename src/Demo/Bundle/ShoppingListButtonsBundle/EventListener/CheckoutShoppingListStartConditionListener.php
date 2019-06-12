<?php
declare(strict_types=1);

namespace Demo\Bundle\ShoppingListButtonsBundle\EventListener;

use Oro\Bundle\ShoppingListBundle\Entity\ShoppingList;
use Oro\Bundle\WorkflowBundle\Entity\WorkflowItem;
use Oro\Bundle\WorkflowBundle\Model\WorkflowResult;
use Oro\Component\Action\Event\ExtendableConditionEvent;

class CheckoutShoppingListStartConditionListener
{
    public function onShoppingListStart(ExtendableConditionEvent $event)
    {
        $context = $event->getContext();
        if ($context instanceof WorkflowItem) {
            /** @var WorkflowItem $context */

            $workflowResult = $context->getResult();
            if ($workflowResult instanceof WorkflowResult) {
                /** @var WorkflowResult $workflowResult */

                $shoppingList = $workflowResult->get('shoppingList');
                if ($shoppingList instanceof ShoppingList) {
                    /** @var ShoppingList $shoppingList */
                    // unless the shopping list label is "OK", we won't show Create Order button
                    if ('OK' !== $shoppingList->getLabel()) {
                        $event->addError('Anything, as this text will not be displayed in the default setup.');
                    }
                }
            }
        }
    }
}
