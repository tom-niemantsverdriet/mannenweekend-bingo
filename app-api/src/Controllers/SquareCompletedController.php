<?php

namespace TomNiemantsverdriet\MannenweekendBingo\AppAPI\Controllers;

use Exception;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Lumi\Core\Routing\Route as ControllerRoute;

use TomNiemantsverdriet\MannenweekendBingo\AppAPI\APIController;
use TomNiemantsverdriet\MannenweekendBingo\AppAPI\PushNotifier;
use TomNiemantsverdriet\MannenweekendBingo\Models\Comment;
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
     * @param ServerRequestInterface $request The Controller-local request
     * @return ResponseInterface The Controller response
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function index(ServerRequestInterface $request): ResponseInterface
    {
        // Collect completions and their identifiers

        $completions = [];
        $completedIds = [];

        foreach (SquareCompleted::findAll()->sort(['completed_at' => -1]) as $completion) {
            $completions[] = $completion;
            $completedIds[] = $completion->getID();
        }

        // Add comment counts to the API payloads

        $commentCounts = Comment::getModel()->countComments($completedIds);
        $result = [];

        foreach ($completions as $completion) {
            $result[] = $completion->getAPIData($commentCounts[(int) $completion->getID()] ?? 0);
        }

        return $this->respondWithData( $result);
    }

    /**
     * Long-polls for new completions. Given a timestamp, it checks once per second for up to
     * 60 seconds whether a completion was created after that timestamp. It returns the creation
     * timestamp of that completion, or the original timestamp when nothing newer appeared.
     * @param ServerRequestInterface $request The Controller-local request
     * @return ResponseInterface The Controller response
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function poll(ServerRequestInterface $request): ResponseInterface
    {
        set_time_limit(70);

        $payload = $this->getRequestPayload();
        $timestamp = isset($payload['timestamp']) ? (int) $payload['timestamp'] : time();

        for ($second = 0; $second < 60; $second++) {
            $latest = $this->getLatestCompletionTimestamp();

            if ($latest !== null && $latest > $timestamp) {
                return $this->respondWithData( ['timestamp' => $latest]);
            }

            sleep(1);
        }

        return $this->respondWithData( ['timestamp' => $timestamp]);
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
     * @param ServerRequestInterface $request The Controller-local request
     * @return ResponseInterface The Controller response
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function create(ServerRequestInterface $request): ResponseInterface
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

        return $this->respondWithData( SquareCompleted::find($id)->getAPIData());
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

    /**
     * Returns the intentionally public routes for this ActionController.
     * @return array The public routes
     * @author Tom Niemantsverdriet <tom@flowtogether.nl>
     */
    public function getRoutes(): array
    {
        return [
            ControllerRoute::get('/square-completed/index', 'index'),
            ControllerRoute::post('/square-completed/poll', 'poll'),
            ControllerRoute::post('/square-completed/create', 'create'),
        ];
    }
}
