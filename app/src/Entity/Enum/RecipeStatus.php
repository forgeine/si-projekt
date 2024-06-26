<?php
/**
 * Recipe status.
 */

namespace App\Entity\Enum;

/**
 * Enum RecipeStatus.
 */
enum RecipeStatus: int
{
    case PENDING = 1;
    case ACTIVE = 2;
    case COMPLETED = 3;
    case ARCHIVED = 4;

    /**
     * Get the description of the status.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::ACTIVE => 'Active',
            self::COMPLETED => 'Completed',
            self::ARCHIVED => 'Archived',
        };
    }

    /**
     * Get all possible statuses as an array.
     *
     * @return RecipeStatus[]
     */
    public static function getAllStatuses(): array
    {
        return [
            self::PENDING,
            self::ACTIVE,
            self::COMPLETED,
            self::ARCHIVED,
        ];
    }
}
