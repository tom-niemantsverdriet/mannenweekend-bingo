<?php

namespace TomNiemantsverdriet\MannenweekendBingo\AppAPI;

use Exception;
use Lumi\Core\Controller;
use Lumi\Core\Framework;

/**
 * APIController class.
 *
 * Abstract controller from which all bingo API controllers inherit. Wraps every
 * response in a JSON envelope of {status, message, data}.
 *
 * @author Tom Niemantsverdriet <tom@lumitec.nl>
 */
abstract class APIController extends Controller
{
    /**
     * Overwrites the invoke method to return the action result as a JSON envelope
     * @param string $action The action to invoke
     * @param array $arguments The arguments to pass to the action
     * @return mixed The returned value
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function invoke(string $action, array $arguments): mixed
    {
        header('Content-Type: application/json');

        try {
            $result = parent::invoke($action, $arguments);

            echo json_encode([
                'status' => 'success',
                'message' => '',
                'data' => $result,
            ]);
        } catch (Exception $exception) {
            http_response_code(500);

            echo json_encode([
                'status' => 'error',
                'message' => $exception->getMessage(),
                'data' => null,
            ]);
        }

        Framework::getInstance()->finish();

        return null;
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
