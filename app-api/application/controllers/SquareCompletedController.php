<?php

namespace TomNiemantsverdriet\MannenweekendBingo\AppAPI\Controllers;

use Exception;
use TomNiemantsverdriet\MannenweekendBingo\AppAPI\APIController;
use TomNiemantsverdriet\MannenweekendBingo\AppAPI\PushNotifier;
use TomNiemantsverdriet\MannenweekendBingo\Models\Static\SquareCompleted;
use TomNiemantsverdriet\MannenweekendBingo\Models\Static\User;

/**
 * SquareCompletedController class.
 *
 * API controller that returns and registers completed squares.
 *
 * @author Tom Niemantsverdriet <tom@lumitec.nl>
 */
class SquareCompletedController extends APIController
{
    /**
     * Returns an overview of all completed squares
     * @return array The list of completions
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function index(): array
    {
        $result = [];

        foreach (SquareCompleted::findAll()->sort(['completed_at' => -1]) as $completion) {
            $result[] = $completion->getAPIData();
        }

        return $result;
    }

    /**
     * Long-polls for new completions. Given a timestamp, it checks once per second for up to
     * 60 seconds whether a completion was created after that timestamp. It returns the creation
     * timestamp of that completion, or the original timestamp when nothing newer appeared.
     * @return array The timestamp to continue polling from
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function poll(): array
    {
        set_time_limit(70);

        $payload = $this->getRequestPayload();
        $timestamp = isset($payload['timestamp']) ? (int) $payload['timestamp'] : time();

        for ($second = 0; $second < 60; $second++) {
            $latest = $this->getLatestCompletionTimestamp();

            if ($latest !== null && $latest > $timestamp) {
                return ['timestamp' => $latest];
            }

            sleep(1);
        }

        return ['timestamp' => $timestamp];
    }

    /**
     * Returns the creation timestamp of the most recent completion, or null when there are none.
     * @return int|null The newest completion timestamp
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    private function getLatestCompletionTimestamp(): ?int
    {
        foreach (SquareCompleted::findAll()->sort(['completed_at' => -1])->limit(1) as $completion) {
            $completedAt = $completion->getCompletedAt();

            return $completedAt !== null ? strtotime($completedAt) : null;
        }

        return null;
    }

    /**
     * Registers a new completion. The offender and reason come from the request, while the
     * poster is taken from the authenticated session. Requires an authenticated participant.
     * @return array The created completion
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function create(): array
    {
        $postedBy = $_SESSION['user_id'] ?? null;

        if (empty($postedBy)) {
            throw new Exception('You must be authenticated to register a completion.');
        }

        $payload = $this->getRequestPayload();

        $squareId = $payload['square_id'] ?? null;
        $offenderId = $payload['offender_id'] ?? null;

        if (empty($squareId) || empty($offenderId)) {
            throw new Exception('A square and an offender are required.');
        }

        $id = SquareCompleted::insert([
            'square' => $squareId,
            'offender' => $offenderId,
            'posted_by' => $postedBy,
            'reason' => $payload['reason'] ?? null,
        ]);

        $this->notifyOtherUsers((int) $postedBy);

        return SquareCompleted::find($id)->getAPIData();
    }

    /**
     * Sends a push notification to every other participant that has notifications enabled.
     * @param int $postedBy The identifier of the participant that registered the completion
     * @return void
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    private function notifyOtherUsers(int $postedBy): void
    {
        $notifier = new PushNotifier();

        foreach (User::findAll() as $user) {
            if ((int) $user->getID() === $postedBy || !$user->hasNotifications()) {
                continue;
            }

            $notifier->send($user->getNotificationUrl());
        }
    }
}
