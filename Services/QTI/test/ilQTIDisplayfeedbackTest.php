<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class ilQTIDisplayfeedbackTest extends TestCase
{
    public function testConstruct() : void
    {
        $this->assertInstanceOf(ilQTIDisplayfeedback::class, new ilQTIDisplayfeedback());
    }
}
