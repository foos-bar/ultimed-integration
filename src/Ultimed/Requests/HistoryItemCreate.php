<?php namespace Ultimed\Requests;

use DateTime;
use Ultimed\Presenters\EmberDate;

class HistoryItemCreate extends ApiRequest
{
    use IncludesOAuthAccessToken;

    public function __construct(array $data = [])
    {
        if (array_key_exists('date', $data)) {
            $data['date'] = (new EmberDate($data['date']))->format();
        }

        $body = json_encode(['historyItem' => $data]);
        parent::__construct('POST', 'v1/historyItems', [], $body);
    }
}
