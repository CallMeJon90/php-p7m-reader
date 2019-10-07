<?php

declare(strict_types=1);

namespace Slam\P7MReader\Tests;

use PHPUnit\Framework\TestCase;
use Slam\P7MReader\P7MReader;
use Slam\P7MReader\P7MReaderException;
use SplFileObject;

final class P7MReaderTest extends TestCase
{
    /**
     * @var SplFileObject
     */
    private $referenceP7m;
    /**
     * @var SplFileObject
     */
    private $referenceXmlOutput;
    /**
     * @var SplFileObject
     */
    private $referenceCrtOutput;
    /**
     * @var SplFileObject
     */
    private $p7mToWorkOn;

    protected function setUp()
    {
        $this->referenceP7m       = new SplFileObject(__DIR__ . '/TestAssets/OK.xml.p7m');
        $this->referenceXmlOutput = new SplFileObject(__DIR__ . '/TestAssets/OK.xml');
        $this->referenceCrtOutput = new SplFileObject(__DIR__ . '/TestAssets/OK.crt');

        $draftP7m = __DIR__ . '/TempOutput/OK.xml.p7m';
        \copy($this->referenceP7m->getPathname(), $draftP7m);

        $this->p7mToWorkOn = new SplFileObject($draftP7m);
    }

    public function testWorkingSample()
    {
        $p7mReader = P7MReader::decode($this->p7mToWorkOn, __DIR__ . '/TempOutput');

        static::assertFileEquals($this->referenceP7m->getPathname(), $p7mReader->getP7mFile()->getPathname());
        static::assertFileEquals($this->referenceXmlOutput->getPathname(), $p7mReader->getContentFile()->getPathname());
        static::assertFileEquals($this->referenceCrtOutput->getPathname(), $p7mReader->getCertFile()->getPathname());
        static::assertArrayHasKey('subject', $p7mReader->getCertData());
    }

    public function testTamperedSample()
    {
        $tamperedFile = new SplFileObject(__DIR__ . '/TestAssets/TAMPERED.xml.p7m');

        static::expectException(P7MReaderException::class);
        static::expectExceptionMessage('asn1 parse error');

        P7MReader::decode($tamperedFile, __DIR__ . '/TempOutput');
    }
}
