<?php

namespace Weather\Enum;

use PHPUnit\Framework\TestCase;
use Weather\Enum\Unit;

class UnitTest extends TestCase
{
    /**
     * @covers \Weather\Enum\Unit::fromEnv
     */
    public function testFromEnvWithMetric(): void
    {
        self::assertEquals(Unit::Metric, Unit::fromEnv(Unit::Metric->value));
    }

    /**
     * @covers \Weather\Enum\Unit::fromEnv
     */
    public function testFromEnvWithImperial(): void
    {
        self::assertEquals(Unit::Imperial, Unit::fromEnv(Unit::Imperial->value));
    }

    /**
     * @covers \Weather\Enum\Unit::fromEnv
     */
    public function testFromEnvWithStandard(): void
    {
        self::assertEquals(Unit::Standard, Unit::fromEnv(Unit::Standard->value));
    }

    /**
     * @covers \Weather\Enum\Unit::fromEnv
     */
    public function testFromEnvWithNull(): void
    {
        self::assertEquals(Unit::Standard, Unit::fromEnv(null));
    }

    /**
     * @covers \Weather\Enum\Unit::fromEnv
     */
    public function testFromEnvWithUnrecognizedValue(): void
    {
        self::assertEquals(Unit::Standard, Unit::fromEnv('unrecognized_value'));
    }

    /**
     * @covers \Weather\Enum\Unit::label
     */
    public function testLabelForMetricCase(): void
    {
        $metricUnit = Unit::Metric;
        $this->assertSame('°C', $metricUnit->label());
    }

    /**
     * @covers \Weather\Enum\Unit::label
     */
    public function testLabelForImperialCase(): void
    {
        $imperialUnit = Unit::Imperial;
        $this->assertSame('°F', $imperialUnit->label());
    }

    /**
     * @covers \Weather\Enum\Unit::label
     */
    public function testLabelForStandardCase(): void
    {
        $standardUnit = Unit::Standard;
        $this->assertSame('°K', $standardUnit->label());
    }

    /**
     * @covers \Weather\Enum\Unit::label
     */
    public function testLabelForUndefinedCase(): void
    {
        $undefinedUnit = Unit::fromEnv('non_existing_case');
        $this->assertSame('°K', $undefinedUnit->label());
    }
}