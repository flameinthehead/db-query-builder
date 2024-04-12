<?php

declare(strict_types=1);

namespace FpDbTest\App;

use FpDbTest\App\Enum\SpecifierEnum;

readonly class ConditionalBlocksConverter
{
    private const string BLOCK_PATTERN = '/\{(.*)\}/';

    public function check(string $query): string
    {
        $blocks = $this->findConditionalBlocks($query);
        if (count($blocks) === 0) {
            return $query;
        }

        foreach ($blocks as $block) {
            if ($this->shouldBeSkipped($block)) {
                $query = preg_replace(self::BLOCK_PATTERN, '', $query, 1);
                continue;
            }

            $query = preg_replace(self::BLOCK_PATTERN, '$1', $query, 1);
        }

        return $query;
    }

    private function findConditionalBlocks(string $query): array
    {
        preg_match_all(self::BLOCK_PATTERN, $query, $matches);
        return $matches[1];
    }

    private function shouldBeSkipped(string $block): bool
    {
        return mb_strpos($block, SpecifierEnum::SKIP_VALUE) !== false;
    }
}
