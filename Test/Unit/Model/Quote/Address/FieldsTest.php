<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Base\Test\Unit\Model\Quote\Address;

use Klarna\Base\Model\Quote\Address\Fields;
use Magento\Directory\Model\Region as RegionDirectory;
use Magento\Framework\Api\SimpleDataObjectConverter;
use Magento\Framework\DataObject;
use Klarna\Base\Test\Unit\Mock\TestCase;
use Magento\Framework\DataObjectFactory;

/**
 * @coversDefaultClass \Klarna\Base\Model\Quote\Address\Fields
 */
class FieldsTest extends TestCase
{
    /**
     * @var Fields
     */
    private Fields $model;

    /**
     * @dataProvider differentAddressInputDataProvider
     *
     * @param $klarnaAddressInput
     * @param $expected
     * @return void
     */
    public function testGetQuoteAddressFieldsByKlarnaAddressCheckCorrespondingInputOutput(
        $klarnaAddressInput,
        $expected
    ): void {
        $dataObject = $this->mockFactory->create(DataObject::class, [], array_keys($klarnaAddressInput));
        foreach ($klarnaAddressInput as $title => $value) {
            $dataObject->method($title)->willReturn($value);
        }
        $this->dependencyMocks['dataObjectFactory']->method('create')->willReturn($dataObject);

        $data = $this->model->getQuoteAddressFieldsByKlarnaAddress($klarnaAddressInput);

        $this->assertSame($expected, $data);
    }

    /**
     * @dataProvider addressInputDataProviderWithStreetAddress2
     *
     * @param $klarnaAddressInput
     * @param $expected
     * @return void
     */
    public function testGetQuoteAddressStreet2FieldsByKlarnaAddressCheckCorrespondingInputOutput(
        $klarnaAddressInput,
        $expected
    ): void {

        $mock = $this->getMockBuilder(DataObject::class);
        $mock->disableOriginalConstructor();

        $mock->onlyMethods(['getData']);
        $addMethods = $klarnaAddressInput;
        unset($addMethods['getData']);

        $mock->addMethods(array_keys($addMethods));
        $dataObject = $mock->getMock();

        foreach ($klarnaAddressInput as $title => $value) {
            $dataObject->method($title)->willReturn($value);
        }
        $this->dependencyMocks['dataObjectFactory']->method('create')->willReturn($dataObject);

        $data = $this->model->getQuoteAddressFieldsByKlarnaAddress($klarnaAddressInput);

        $this->assertSame($expected, $data);
    }

    public function addressInputDataProviderWithStreetAddress2(): array
    {
        $basicAddressInput = [
            'getGivenName' => 'my firstname',
            'getFamilyName' => 'my lastname',
            'getCountry' => 'DE',
            'getEmail' => 'myEmailAddress@klarna.com',
            'getOrganizationName' => 'Klarna',
            'getTitle' => 'Mr.',
            'getStreetAddress' => 'my street address',
            'getHouseExtension' => '10',
            'getData' => 'my street address 2',
            'getRamin' => 'my ramin',
            'getPostalCode' => '10101',
            'getCity' => 'BE',
            'getRegion' => 'BE',
            'getPhone' => '+491111111111',
        ];

        $basicExpectedOutput = [
            'lastname' => 'my lastname',
            'firstname' => 'my firstname',
            'email' => 'myEmailAddress@klarna.com',
            'company' => 'Klarna',
            'prefix' => 'Mr.',
            'street' => [
                'my street address10',
                'my street address 2'
            ],
            'postcode' => '10101',
            'city' => 'BE',
            'region_id' => 20,
            'region' => 'BE',
            'telephone' => '+491111111111',
            'country_id' => 'DE',
        ];

        return [
            'inputWithStreetAddress2' => [
                $basicAddressInput,
                $basicExpectedOutput,
            ]
        ];
    }

    public function differentAddressInputDataProvider(): array
    {
        $basicAddressInput = [
            'getGivenName' => 'my firstname',
            'getFamilyName' => 'my lastname',
            'getCountry' => 'DE',
            'getEmail' => 'myEmailAddress@klarna.com',
            'getOrganizationName' => 'Klarna',
            'getTitle' => 'Mr.',
            'getStreetAddress' => 'my street address',
            'getHouseExtension' => '10',
            'getRamin' => 'my ramin',
            'getPostalCode' => '10101',
            'getCity' => 'BE',
            'getRegion' => 'BE',
            'getPhone' => '+491111111111',
        ];

        $basicExpectedOutput = [
            'lastname' => 'my lastname',
            'firstname' => 'my firstname',
            'email' => 'myEmailAddress@klarna.com',
            'company' => 'Klarna',
            'prefix' => 'Mr.',
            'street' => [
                'my street address10',
            ],
            'postcode' => '10101',
            'city' => 'BE',
            'region_id' => 20,
            'region' => 'BE',
            'telephone' => '+491111111111',
            'country_id' => 'DE',
        ];

        $addressInputContainsDOB = array_merge($basicAddressInput, [
            'hasCustomerDOB' => true,
            'getCustomerDOB' => '02/02/2002',
        ]);
        $expectedOutputContainsDOB = array_merge($basicExpectedOutput, ['dob' => '02/02/2002']);

        $addressInputContainsGender = array_merge($basicAddressInput, [
            'hasCustomerGender' => true,
            'getCustomerGender' => 'male'
        ]);
        $expectedOutputContainsGender = array_merge($basicExpectedOutput, ['gender' => 'male']);

        $addressInputContainsSomeMissingValues = $basicAddressInput;
        unset(
            $addressInputContainsSomeMissingValues['getGivenName'],
            $addressInputContainsSomeMissingValues['getEmail']
        );

        $expectedSomeMissingValues = array_merge($basicExpectedOutput, ['firstname' => null, 'email' => null]);

        return [
            'basicInput' => [
                $basicAddressInput,
                $basicExpectedOutput,
            ],
            'containsDOB' => [
                $addressInputContainsDOB,
                $expectedOutputContainsDOB,
            ],
            'containsGender' => [
                $addressInputContainsGender,
                $expectedOutputContainsGender,
            ],
            'someMissingValues' => [
                $addressInputContainsSomeMissingValues,
                $expectedSomeMissingValues,
            ]
        ];
    }

    protected function setUp(): void
    {
        $this->model = parent::setUpMocks(Fields::class);

        $regionDirectory = $this->mockFactory->create(
            RegionDirectory::class,
            ['loadByCode', 'getId']
        );
        $regionDirectory->method('loadByCode')->willReturn($regionDirectory);
        $regionDirectory->method('getId')->willReturn('20');
        $this->dependencyMocks['regionFactory']->method('create')->willReturn($regionDirectory);
    }
}
