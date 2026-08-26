<?php

namespace TomNiemantsverdriet\MannenweekendBingo\AppAPI\Controllers;

use Exception;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Lumi\Core\Routing\Route as ControllerRoute;

use TomNiemantsverdriet\MannenweekendBingo\AppAPI\APIController;
use TomNiemantsverdriet\MannenweekendBingo\Models\Comment;

/**
 * CommentController class.
 *
 * API controller that returns and creates comments on completed squares.
 *
 * @author Tom Niemantsverdriet <tom@lumitec.nl>
 */
class CommentController extends APIController
{
    /**
     * Returns the comments for a completed square
     * @param ServerRequestInterface $request The Controller-local request
     * @return ResponseInterface The Controller response
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function index(ServerRequestInterface $request): ResponseInterface
    {
        // Determine which completed square to load comments for

        $payload = $this->getRequestPayload();
        $completedId = $payload['square_completed_id'] ?? $_GET['square_completed_id'] ?? null;

        if (empty($completedId)) {
            throw new Exception('A completed square is required.');
        }

        // Return newest comments first

        $result = [];

        foreach (Comment::getModel()->findBySquareCompleted($completedId)->sort(['posted_at' => -1]) as $comment) {
            $result[] = $comment->getAPIData();
        }

        return $this->respondWithData( $result);
    }

    /**
     * Creates a comment for a completed square under the authenticated participant
     * @param ServerRequestInterface $request The Controller-local request
     * @return ResponseInterface The Controller response
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function create(ServerRequestInterface $request): ResponseInterface
    {
        $userId = $_SESSION['user_id'] ?? null;

        if (empty($userId)) {
            throw new Exception('You must be authenticated to post a comment.');
        }

        // Validate the submitted comment payload

        $payload = $this->getRequestPayload();
        $completedId = $payload['square_completed_id'] ?? null;
        $comment = $payload['comment'] ?? null;

        if (empty($completedId) || empty($comment)) {
            throw new Exception('A completed square and comment are required.');
        }

        // Store the comment under the authenticated participant

        $id = Comment::getModel()->insert([
            'square_completed' => $completedId,
            'user' => $userId,
            'comment' => $comment,
        ]);

        return $this->respondWithData( Comment::getModel()->find($id)->getAPIData());
    }

    /**
     * Returns the intentionally public routes for this ActionController.
     * @return array The public routes
     * @author Tom Niemantsverdriet <tom@flowtogether.nl>
     */
    public function getRoutes(): array
    {
        return [
            ControllerRoute::get('/comment', 'index'),
            ControllerRoute::post('/comment/create', 'create'),
        ];
    }
}
