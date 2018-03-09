<?php
declare(strict_types=1);

/**
 * This file is part of the server-status package.
 *
 * (c) Roberto Martin <rmh.dev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ServerStatus\Domain\Model\Check;

class CheckName
{
    const MAX_LENGTH = 60;

    private $value;

    private $slug;

    public function __construct($name = "", string $slug = "")
    {
        $processedName = $this->processName($name);
        $this->assertNotEmpty($processedName);
        $this->assertNotTooLong($processedName);
        $this->value = $processedName;
        $this->slug = $this->processSlug($processedName, $slug);
    }

    private function assertNotEmpty(string $value, $field = "name"): void
    {
        if (0 === strlen($value)) {
            throw new \InvalidArgumentException(sprintf('Check field "%s" is empty', $field));
        }
    }

    private function assertNotTooLong(string $value, $field = "name"): void
    {
        if (self::MAX_LENGTH < mb_strlen($value)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'CheckName %s "%s..." is too long (%d chars), max length is %d.',
                    $field,
                    mb_substr($value, 0, 20),
                    mb_strlen($value),
                    self::MAX_LENGTH
                )
            );
        }
    }

    private function processName($name): string
    {
        return trim((string)$name);
    }

    private function processSlug(string $name, string $slug = ""): string
    {
        $processedSlug = trim((string)$slug);
        if (!strlen($processedSlug)) {
            return $this->slugify($name);
        }
        $this->assertNotTooLong($processedSlug, "slug");
        if ($processedSlug !== $this->slugify($slug)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'CheckName slug "%s" is not correct, "%s" expected',
                    $slug,
                    $this->slugify($name)
                )
            );
        }

        return $processedSlug;
    }

    private function slugify($string): string
    {
        return preg_replace('/\s+/', '-', mb_strtolower(trim(strip_tags($string)), 'UTF-8'));
    }

    public function value(): string
    {
        return $this->value;
    }

    public function slug(): string
    {
        return $this->slug;
    }

    public function __toString(): string
    {
        return $this->value();
    }
}
