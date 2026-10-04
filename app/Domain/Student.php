<?php
declare(strict_types=1);

namespace App\Domain;

use InvalidArgumentException;

final class Student
{
    public function __construct(
        private string $firstName,
        private string $lastName,
        private ?string $programme,
        private int $yearOfStudy = 1
    ) {
        $this->firstName = trim($this->firstName);
        $this->lastName = trim($this->lastName);
        $this->programme = $this->programme !== null ? trim($this->programme) : null;

        if ($this->firstName === '' || $this->lastName === '') {
            throw new InvalidArgumentException('Student first and last names are required.');
        }
        if ($this->yearOfStudy < 1 || $this->yearOfStudy > 6) {
            throw new InvalidArgumentException('Year of study must be between 1 and 6.');
        }
    }

    public function firstName(): string { return $this->firstName; }
    public function lastName(): string { return $this->lastName; }
    public function programme(): ?string { return $this->programme ?: null; }
    public function yearOfStudy(): int { return $this->yearOfStudy; }
}
