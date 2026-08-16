<?php

namespace TomNiemantsverdriet\MannenweekendBingo\Models;

use Lumi\Core\Model;
use TomNiemantsverdriet\MannenweekendBingo\ActiveRecords\CommentRecord;

/**
 * Comment class.
 *
 * Model that stores comments posted on completed bingo squares.
 *
 * @author Tom Niemantsverdriet <tom@lumitec.nl>
 */
class Comment extends Model
{
    /**
     * Configures the comment schema
     * @return void
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function onStart(): void
    {
        $this->setColumns([
            'comment_id' => ['is-auto-id' => true],
            'square_completed' => ['referenced-table' => 'square_completed', 'required' => true],
            'user' => ['referenced-table' => 'user', 'required' => true],
            'posted_at' => ['data-type' => 'datetime', 'required' => true],
            'comment' => ['data-type' => 'text', 'required' => true],
        ]);

        $this->setActiveRecordClass(CommentRecord::class);
    }

    /**
     * Inserts a new comment. Defaults the posted timestamp to now when none is given.
     * @param array $data The data to insert
     * @return string|int|bool The identifier of the new comment
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function insert($data)
    {
        if (empty($data['posted_at'])) {
            $data['posted_at'] = date('Y-m-d H:i:s');
        }

        return parent::insert($data);
    }

    /**
     * Counts comments grouped by completed square
     * @param array $completedIds The completed square identifiers
     * @return array The comment count indexed by completed square identifier
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function countComments(array $completedIds): array
    {
        if (empty($completedIds)) {
            return [];
        }

        // Query all counts in one grouped lookup

        $placeholders = implode(', ', array_fill(0, count($completedIds), '?'));
        $rows = $this->query("
            SELECT `square_completed`, COUNT(*) AS `count`
            FROM `comment`
            WHERE `square_completed` IN ($placeholders)
            GROUP BY `square_completed`
        ", ...$completedIds);

        // Index the result by completed square

        $counts = [];

        foreach ($rows as $row) {
            $counts[(int) $row['square_completed']] = (int) $row['count'];
        }

        return $counts;
    }
}
