<?php


namespace Omines\DataTablesBundle\Filter;

use Symfony\Component\OptionsResolver\OptionsResolver;

class DateRangeFilter extends AbstractFilter
{
    /** @var string */
    protected $placeholder;

    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefaults([
                'template_html' => '@DataTables/Filter/daterange_filter.html.twig',
                'placeholder' => "Select date period",
            ])
            ->setAllowedTypes('placeholder', ['null', 'string']);

        return $this;
    }

    /**
     * @return string
     */
    public function getPlaceholder()
    {
        return $this->placeholder;
    }

    /**
     * @param $value
     */
    public function isValidValue($value): bool
    {
        return preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2} AND [0-9]{4}-[0-9]{2}-[0-9]{2}(| [0-9]{2}\:[0-9]{2}\:[0-9]{2})$/', $value);
    }
}
