<?php
declare(strict_types=1);

namespace App\classes; 

use InvalidArgumentException;

/** Programme: a degree programme (e.g. "Bachelor of Computer Science"). */
final class Programme
{
    private string $name;

    public function __construct(string $name, private ?int $id = null)
    {
        $this->setName($name);
    }

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }

    public function setName(string $name): void
    {
        $name = trim($name);
        if ($name === '') {
            throw new InvalidArgumentException('Programme name cannot be empty.');
        }
        if (mb_strlen($name) > 100) {
            throw new InvalidArgumentException('Programme name must be 100 characters or fewer.');
        }
        $this->name = $name;
    }
}
