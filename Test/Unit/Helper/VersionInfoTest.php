<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Base\Test\Unit\Helper;

use Klarna\Base\Helper\VersionInfo;
use Magento\Framework\App\ProductMetadata;
use Magento\Framework\App\State;
use PHPUnit\Framework\MockObject\MockObject;
use Klarna\Base\Test\Unit\Mock\TestCase;
use Magento\Framework\Exception\FileSystemException;

/**
 * @coversDefaultClass  \Klarna\Base\Helper\VersionInfo
 */
class VersionInfoTest extends TestCase
{
    /**
     * @var VersionInfo
     */
    private $model;

    public function testGetVersion(): void
    {
        $dataVersion = '1.2.3';

        $this->dependencyMocks['resource']
            ->method('getDataVersion')
            ->willReturn($dataVersion);

        $result = $this->model->getVersion(VersionInfo::M2_KLARNA);
        static::assertSame($dataVersion, $result);
    }

    public function testGetMageMode(): void
    {
        $appState = State::MODE_DEVELOPER;

        $this->dependencyMocks['appState']
            ->method('getMode')
            ->willReturn($appState);

        $result = $this->model->getMageMode();
        static::assertSame($appState, $result);
    }

    public function testGetMageVersion(): void
    {
        $version = '2.3.4';

        $this->dependencyMocks['productMetadata']
            ->method('getVersion')
            ->willReturn($version);

        $result = $this->model->getMageVersion();
        static::assertSame($version, $result);
    }

    public function testGetMageEdition(): void
    {
        $this->dependencyMocks['productMetadata']
            ->method('getEdition')
            ->willReturn(ProductMetadata::EDITION_NAME);

        $result = $this->model->getMageEdition();
        static::assertSame(ProductMetadata::EDITION_NAME, $result);
    }

    /**
     * @covers ::getModuleVersionString()
     */
    public function testGetModuleVersionString(): void
    {
        $dataVersion = '1.2.3';
        $moduleVersion = 'a.b.c';

        $this->dependencyMocks['resource']
            ->method('getDataVersion')
            ->willReturn($dataVersion);

        $result = $this->model->getModuleVersionString($moduleVersion, '');
        static::assertSame($moduleVersion . ';Base/' . $dataVersion, $result);
    }

    public function testGetMageInfo(): void
    {
        $version = '2.3.4';
        $edition = ProductMetadata::EDITION_NAME;
        $mode = State::MODE_DEVELOPER;

        $this->dependencyMocks['productMetadata']
            ->method('getVersion')
            ->willReturn($version);
        $this->dependencyMocks['productMetadata']
            ->method('getEdition')
            ->willReturn($edition);
        $this->dependencyMocks['appState']
            ->method('getMode')
            ->willReturn($mode);

        $result = $this->model->getMageInfo();
        static::assertSame('Magento ' . $edition . '/' . $version . ' ' . $mode . ' mode', $result);
    }

    public function testGetFullM2KlarnaVersionReturnsCorrectValue(): void
    {
        $composerPackageVersion = 'e.f.g';

        $versionInfoMock = $this->createPartialMock(VersionInfo::class, ['getComposerPackageVersion']);

        $versionInfoMock
            ->expects($this->once())
            ->method('getComposerPackageVersion')
            ->with(VersionInfo::M2_KLARNA)
            ->willReturn($composerPackageVersion);

        $actualResult = $versionInfoMock->getFullM2KlarnaVersion();

        $this->assertEquals(VersionInfo::M2_KLARNA . '/' . $composerPackageVersion, $actualResult);
    }

    public function testGetM2KlarnaVersionReturnsCorrectValue(): void
    {
        $composerPackageVersion = 'e.f.g';

        $versionInfoMock = $this->createPartialMock(VersionInfo::class, ['getComposerPackageVersion']);

        $versionInfoMock
            ->expects($this->once())
            ->method('getComposerPackageVersion')
            ->with(VersionInfo::M2_KLARNA)
            ->willReturn($composerPackageVersion);

        $actualResult = $versionInfoMock->getM2KlarnaVersion();

        $this->assertEquals($composerPackageVersion, $actualResult);
    }

    public function testGetFallbackExtensionVersion(): void
    {
        $this->dependencyMocks['fileDriver']->expects($this->once())
            ->method('fileGetContents')
            ->willReturn('{"version": "3.2.0"}');
        $this->dependencyMocks['fileDriver']->expects($this->once())
            ->method('isExists')
            ->willReturn(true);

        $result = $this->model->getFallbackExtensionVersion();
        static::assertSame('3.2.0', $result);
    }

    public function testGetFallbackExtensionVersionFileGetContentsThrowsException(): void
    {
        $this->dependencyMocks['fileDriver']->expects($this->once())
            ->method('fileGetContents')
            ->willThrowException(new FileSystemException(__('Error')));
        $this->dependencyMocks['fileDriver']->expects($this->never())
            ->method('isExists');

        $result = $this->model->getFallbackExtensionVersion();
        static::assertSame('', $result);
    }

    public function testGetFallbackExtensionVersionVersionKeyValueDoesNotExist(): void
    {
        $this->dependencyMocks['fileDriver']->expects($this->once())
            ->method('fileGetContents')
            ->willReturn('{"name": "m2/klarna"}');
        $this->dependencyMocks['fileDriver']->expects($this->once())
            ->method('isExists')
            ->willReturn(true);

        $result = $this->model->getFallbackExtensionVersion();
        static::assertSame('', $result);
    }

    /**
     * Basic setup for test
     */
    protected function setUp(): void
    {
        $this->model = parent::setUpMocks(VersionInfo::class);
    }
}