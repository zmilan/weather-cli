<?php

namespace Weather\Enum;

enum Unit: string
{
    case Standard = 'standard';
    case Metric = 'metric';
    case Imperial = 'imperial';

    /**
     *
     * @param string|null $unit
     * @return self
     */
    public static function fromEnv(?string $unit): self
    {
        return match ($unit) {
            self::Metric->value => self::Metric,
            self::Imperial->value => self::Imperial,
            default => self::Standard
        };
    }

    /**
     * Returns the label for the given value.
     *
     * The label is returned based on the value of the object. If the value matches the Metric constant,
     * the label '°C' is returned. If the value matches the Imperial constant, the label '°F' is returned.
     * Otherwise, the label '°K' is returned as the default value.
     *
     * @return string The label for the given value.
     */
    public function label(): string
    {
        return match ($this->value) {
            self::Metric->value => '°C',
            self::Imperial->value => '°F',
            default => '°K'
        };
    }
}
