<?php

namespace App\Tests\Form;

use App\Entity\Customer;
use App\Form\CustomerType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerTypeTest extends TestCase
{
    public function testFormDesign()
    {
        $formBuilderExpectation = $this->prophesize(FormBuilderInterface::class);
        $formBuilderMock = $formBuilderExpectation->reveal();
        $formBuilderExpectation->add('name')->shouldBeCalled()->willReturn($formBuilderMock);
        $formBuilderExpectation
            ->add('surname', null, ['empty_data' => ''])
            ->shouldBeCalled()
            ->willReturn($formBuilderMock)
        ;
        $formBuilderExpectation
            ->add('email', null, ['empty_data' => ''])
            ->shouldBeCalled()
            ->willReturn($formBuilderMock)
        ;
        $formBuilderExpectation
            ->add('birthday', DateType::class, ['widget' => 'single_text'])
            ->shouldBeCalled()
            ->willReturn($formBuilderMock)
        ;
        $formBuilderExpectation
            ->add('save', SubmitType::class)
            ->shouldBeCalled()
            ->willReturn($formBuilderMock)
        ;

        $customerType = new CustomerType();
        $customerType->buildForm($formBuilderMock, []);

        $optionResolverExpectation = $this->prophesize(OptionsResolver::class);
        $optionResolverExpectation->setDefaults([
            'data_class' => Customer::class,
            'csrf_protection' => false,
            'validation_groups' => ['Default', 'registration'],
        ])->shouldBeCalled();

        $customerType->configureOptions($optionResolverExpectation->reveal());
    }
}