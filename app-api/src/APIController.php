<?php

namespace TomNiemantsverdriet\MannenweekendBingo\AppAPI;

use Exception;
use Psr\Http\Message\ResponseInterface;

use Lumi\Core\ActionController;

/**
 * APIController class.
 *
 * Abstract controller from which all bingo API controllers inherit. Wraps every
 * response in a JSON envelope of {status, message, data}.
 *
 * @author Tom Niemantsverdriet <tom@lumitec.nl>
 */
abstract class APIController extends ActionController
{
    /**
     * Executes the action and preserves the established JSON error envelope.
     * @return ResponseInterface The Controller response
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function execute(): ResponseInterface
    {
        try {
            return parent::execute();
        } catch (Exception $exception) {
            return $this->respondWithJSON([
                'status' => 'error',
                'message' => $exception->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    /**
     * Wraps successful API data in the legacy bingo envelope.
     * @param mixed $data The response data
     * @return ResponseInterface The JSON response
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    protected function respondWithData(mixed $data): ResponseInterface
    {
        return $this->respondWithJSON([
            'status' => 'success',
            'message' => '',
            'data' => $data,
        ]);
    }

    /**
     * Returns the decoded JSON payload of the request
     * @return array The decoded request payload
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    protected function getRequestPayload(): array
    {
        $payload = json_decode(file_get_contents('php://input'), true);

        return is_array($payload) ? $payload : [];
    }
}
