<?php namespace Ultimed\Responses;

use Ultimed\Parsers\EmberDate;

class HistoryItemCreate extends ApiResponse
{
    private $historyItem;

    protected function init()
    {
        $data = $this->parseJson();
        $this->historyItem = $data['historyItem'];

        if (array_key_exists('date', $this->historyItem)) {
            $parser = new EmberDate($this->historyItem['date']);
            $this->historyItem['date'] = $parser->date();
        }
    }

    public function getHistoryItem()
    {
        return $this->historyItem;
    }
}
