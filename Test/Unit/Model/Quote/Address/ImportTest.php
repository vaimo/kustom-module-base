<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Base\Test\Unit\Model\Quote\Address;

use Klarna\Base\Model\Quote\Address\Import;
use Klarna\Base\Test\Unit\Mock\TestCase;
use Magento\Quote\Model\Quote\Address;
use Magento\Customer\Model\Form;
use Magento\Framework\App\Request\Http;

/**
 * @coversDefaultClass \Klarna\Base\Model\Quote\Address\Import
 */
class ImportTest extends TestCase
{
    /**
     * @var Import
     */
    private Import $model;
    /**
     * @var Form|\PHPUnit\Framework\MockObject\MockObject
     */
    private Form $form;
    /**
     * @var Address|\PHPUnit\Framework\MockObject\MockObject
     */
    private Address $quoteAddress;
    /**
     * @var Http|\PHPUnit\Framework\MockObject\MockObject
     */
    private Http $http;

    public function testImportAddressFromRequestCompactingTheAddressData(): void
    {
        $klarnaAddressData = [
            'any_key' => 'any_value'
        ];
        $extractedData = [
            'new_key' => 'new_value'
        ];

        $this->form->method('extractData')
            ->willReturn($extractedData);
        $this->form->method('prepareRequest')
            ->willReturn($this->http);
        $this->form->expects(static::once())
            ->method('compactData')
            ->with($extractedData);

        $this->model->importAddressFromRequest($klarnaAddressData, $this->form, $this->quoteAddress);
    }

    protected function setUp(): void
    {
        $this->model = parent::setUpMocks(Import::class);

        $this->form = $this->mockFactory->create(Form::class);
        $this->quoteAddress = $this->mockFactory->create(Address::class);
        $this->http = $this->mockFactory->create(Http::class);
    }
}
