<?php

namespace Omines\DataTablesBundle\Column;

use Omines\DataTablesBundle\Filter\CheckFilter;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CheckColumn extends AbstractColumn
{

    public function normalize($value): string
    {
        return $value;
    }

    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefault('label', '')
            ->setDefault('data', function ($obj) {
                return sprintf('<input class="check_object" type="checkbox" name="check[]" value="%s">', $obj->getId());
            })
            ->setDefault('filter', new CheckFilter())
        ;

        $resolver
            ->setDefault('raw', true)
            ->setAllowedTypes('raw', 'bool')
        ;

        return $this;
    }


    public function isValidForSearch($value)
    {
        return false;
    }
}
