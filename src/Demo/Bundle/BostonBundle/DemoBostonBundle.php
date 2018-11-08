<?php

namespace Demo\Bundle\BostonBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

use Demo\Bundle\BostonBundle\DependencyInjection\DemoBostonExtension;

class DemoBostonBundle extends Bundle
{

    public function getContainerExtension()
    {
        if (!$this->extension) {
            $this->extension = new DemoBostonExtension();
        }

        return $this->extension;
    }

}
