<?php

namespace Omines\DataTablesBundle\Filter;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class SelectFilter extends AbstractFilter
{
    /** @var string */
    protected $placeholder;

    /** @var array */
    protected $choices = [];

    public function __construct($options, RouterInterface $router)
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        foreach ($resolver->resolve() as $key => $value) {
            $this->$key = $value;
        }

        $options['remote_params']['field_name'] = $options['field_name'];
        $options['remote_params']['page_limit'] = '20';
        $options['remote_url'] = $router->generate($options['remote_route'], $options['remote_params']);

        $this->set($options);
    }

    /**
     * @return $this
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefaults([
                'template_html' => '@DataTables/Filter/select_filter.html.twig',
                'placeholder' => null,
                'choices' => [],
                'class' => null,
                'field_name' => null,
                'property' => null,
                'remote_route' => null,
                'remote_params' => [],
                'remote_url' => null,
            ])
            ->setAllowedTypes('placeholder', ['null', 'string'])
            ->setAllowedTypes('choices', ['array'])
            ->setAllowedTypes('remote_route', ['null', 'string'])
            ->setAllowedTypes('remote_params', ['array'])
            ;


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
     * @return mixed
     */
    public function getChoices()
    {
        return $this->choices;
    }

    /**
     * {@inheritdoc}
     */
    public function isValidValue($value): bool
    {
        return true;
    }
}
