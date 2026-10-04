<?php
declare(strict_types=1);

namespace App\Domain;

use InvalidArgumentException;

final class Course
{
    public function __construct(
        private string $code,
        private string $name,
        private int $creditHours
    ) {
        $this->code = strtoupper(trim($this->code));
        $this->name = trim($this->name);
        if ($this->code === '' || $this->name === '') {
            throw new InvalidArgumentException('Course code and name are required.');
        }
        if ($this->creditHours < 1) {
            throw new InvalidArgumentException('Credit hours must be greater than zero.');
        }
    }

    public function code(): string { return $this->code; }
    public function name(): string { return $this->name; }
    public function creditHours(): int { return $this->creditHours; }
}
