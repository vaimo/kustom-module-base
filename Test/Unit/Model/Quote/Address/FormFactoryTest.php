<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Base\Test\Unit\Model\Quote\Address;

use Klarna\Base\Model\Quote\Address\FormFactory;
use Magento\Customer\Model\Form as MagentoForm;
use PHPUnit\Framework\MockObject\MockObject;
use Klarna\Base\Test\Unit\Mock\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * @coversDefaultClass \Klarna\Base\Model\Quote\Address\FormFactory
 */
class FormFactoryTest extends TestCase
{
    /**
     * @var FormFactory
     */
    private $model;
    /**
     * @var Form|PHPUnit_Framework_MockObject_MockObject
     */
    private $form;

    public function testCreateCustomerAddressFormByTypeReturnsInstance()
    {
        $this->dependencyMocks['addressFormFactory']->method('create')
            ->willReturn($this->form);
        $result = $this->model->createCustomerAddressForm();

        static::assertSame($this->form, $result);
    }

    protected function setUp(): void
    {
        $this->model = parent::setUpMocks(FormFactory::class);
        $this->form = $this->mockFactory->create(MagentoForm::class);
    }
}
