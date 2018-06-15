<?php

namespace FM\RFMBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class RFMType.
 */
class RFMType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setAttribute('enable', $options['enable']);

        if ($builder->getAttribute('enable')) {
            $builder->setAttribute('instance', $options['instance']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['enable'] = $options['enable'];

        if ($options['enable']) {
            $view->vars['instance']   = $options['instance'];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(array(
                'enable'        => true,
                'instance'      => 'default'
            ))
            ->setAllowedTypes('enable', 'bool')
            ->setAllowedTypes('instance', array('string', 'null'));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        if (method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix')) {
            return 'Symfony\Component\Form\Extension\Core\Type\TextType';
        }

        return 'text';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'rfm';
    }
}
