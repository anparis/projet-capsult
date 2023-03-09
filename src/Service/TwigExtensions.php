<?php

// app/src/Service/TwigExtensions.php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class TwigExtensions extends 
{
    protected $params; 

    public function getFunctions()
    {
        return [
            new \Twig('get_parameter', array($this, 'getParameter'))
        ];
    }
    
    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }     

    public function getParameter($parameter)
    {
        return $params->get($parameter);
    }
    
    public function getName()
    {
        return 'TwigExtensions';
    }
}
