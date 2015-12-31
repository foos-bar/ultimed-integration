<?php namespace Ultimed\Responses;

class PatientShow extends ApiResponse
{
    private $patient;

    protected function init()
    {
        $data = $this->parseJson();
        $this->patient = $data['patient'];
    }

    public function getPatient()
    {
        return $this->patient;
    }
}
