<?php


class ExternalValidator {

    const SERVICE_ERROR = 1;
    const INTAKE_FORM_UNKNOWN = 2;
    const INTAKE_FORM_UNKNOWN_CHECK_NUMBER = 3;
    const INTAKE_FORM_INCORRECT_CHECK_NUMBER = 4;
    const INTAKE_FORM_UNKNOWN_CHECK_DOB = 5;
    const INTAKE_FORM_INCORRECT_CHECK_DOB = 6;

    protected $_errors = array(
        self::SERVICE_ERROR => 'Invalid service is selected. Please select another service to continue booking.',
        self::INTAKE_FORM_UNKNOWN => 'Intake Forms are missing for this service',
        self::INTAKE_FORM_UNKNOWN_CHECK_NUMBER => '"Check number" field is missing.',
        self::INTAKE_FORM_INCORRECT_CHECK_NUMBER => '"Check number" field is incorrect.',
        self::INTAKE_FORM_UNKNOWN_CHECK_DOB => '"Date of birth" field is missing.',
        self::INTAKE_FORM_INCORRECT_CHECK_DOB => 'Incorrect date of birth',
        self::EMAIL_ERROR => 'Please enter an email address'
    );

    protected $_fieldsNameMap = array(
        'checkNumber' => 'Check number',
        'checkString' => 'Some string',
        'dateOfBirth' => 'Date of birth',
    );

    public function validate($bookingData){
        try{
            $timeStart = microtime(true);
            $this->_log($bookingData);
            $this->_log('here1');
            //It is an example of service validation. Similarly, you can check the provider, client or number of bookings
            //if (!isset($bookingData['client_id'])) {
            //    $this->_error(self::SERVICE_ERROR, 'client_id');
            //    return false;
            //}

            if (!isset($bookingData['provider_id']) || ($bookingData['provider_id'] == 1 && !isset($bookingData['client_email']))) {
                this->_log('here2');
                $this->_error(self::SERVICE_ERROR, 'service_id');
                return false;
            }

            $timeEnd = microtime(true);
            $executionTime = $timeEnd - $timeStart;

            return array();
        } catch(ExternalValidatorException $e){ //validator Error
            return $this->_sendError($e);
        } catch (Exception $e){ // other error
            $result = array(
                'errors' => array($e->getMessage())
            );
            $this->_log($result);
            return $result;
        }
    }



    /**
     * Generation error for output on the Simplybook.me booking page
     *
     * @param ExternalValidatorException $e
     * @return array[]|array[][]
     */
    protected function _sendError(ExternalValidatorException $e){
        if($e->getFieldId()){
            $result = array(
                array(
                    'id' => $e->getFieldId(),
                    'errors' => array($e->getMessage())
                )
            );
            $this->_log($result);
            return $result;
        }else if($e->getIntakeFieldId()){
            $result = array(
                'additional_fields' => array(
                    array(
                        'id' => $e->getIntakeFieldId(),
                        'errors' => array($e->getMessage())
                    )
                )
            );
            $this->_log($result);
            return $result;
        } else {
            $result = array(
                'errors' => array($e->getMessage())
            );
            $this->_log($result);
            return $result;
        }
    }

    /**
     * @param int $code
     * @param null|array $fieldId
     * @param null|array $intakeFieldId
     * @param null|array $data
     * @throws ExternalValidatorException
     */
    protected function _error($code, $fieldId = null, $intakeFieldId = null, $data = NULL) {
        $message = '';
        if (isset($this->_errors[$code])) {
            $message = $this->_errors[$code];
        }
        $this->_throwError($message, $code, $fieldId, $intakeFieldId, $data);
    }

    /**
     * @param string $message
     * @param int $code
     * @param null|string $fieldId
     * @param array $data
     * @throws ExternalValidatorException
     */
    protected function _throwError($message, $code = -1, $fieldId = null, $intakeFieldId = null, $data = array()) {
        $error = new ExternalValidatorException($message, $code);
        if($fieldId){
            $error->setFieldId($fieldId);
        }
        if($intakeFieldId){
            $error->setIntakeFieldId($intakeFieldId);
        }
        if ($data && count($data)) {
            $error->setData($data);
        }
        throw $error;
    }

    /**
     * Log to file
     * @param $var
     * @param string $name
     */
    protected function _log($var, $name = 'log'){
        $bugtrace = debug_backtrace();
        $bugTraceIterator = 0;
        //dump var to string
        ob_start();
        var_dump( $var );
        $data = ob_get_clean();

        $logContent = "\n\n" .
            "--------------------------------\n" .
            date("d.m.Y H:i:s") . "\n" .
            "{$bugtrace[$bugTraceIterator]['file']} : {$bugtrace[$bugTraceIterator]['line']}\n\n" .
            $data . "\n" .
            "--------------------------------\n";

        $fh = fopen( $name . '.txt', 'a');
        fwrite($fh, $logContent);
        fclose($fh);
    }

}
