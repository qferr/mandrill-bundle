<?php

namespace Qferrer\Symfony\MandrillBundle;

use Qferrer\Symfony\MandrillBundle\DependencyInjection\MandrillExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class QferrerMandrillBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new MandrillExtension();
    }
}