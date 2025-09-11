<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Test\Unit\Mock;

use PHPUnit\Framework\TestCase;

/**
 * Factory to create PHPUnit MockObjects. Runs the default methods
 * we want to run on all MockObjects and updates the object based on
 * the given parameters.
 *
 * @internal
 */
class MockFactory
{
    /**
     * @var TestCase
     */
    private $testClass;

    /**
     * @param TestCase $testClass
     * @codeCoverageIgnore
     */
    public function __construct(TestCase $testClass)
    {
        $this->testClass = $testClass;
    }

    /**
     * Creates and returns a unique PHPUnit MockObject
     *
     * @param string $className
     * @param array $onlyMethods
     * @param array $addMethods
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    public function create(string $className, array $onlyMethods = [], array $addMethods = [])
    {
        $mock = $this->testClass->getMockBuilder($className);
        $mock->disableOriginalConstructor();

        if (!empty($onlyMethods)) {
            $mock->onlyMethods($onlyMethods);
        }

        if (!empty($addMethods)) {
            $mock->addMethods($addMethods);
        }

        return $mock->getMock();
    }
}
