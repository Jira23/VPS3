<?php
/**
 *  @package  narezovy-formular
 */

namespace Inc\Exceptions;

class NFArdisException extends NFException {
    protected $userMessage;

    public function __construct($developerMessage, $userMessage = false, $code = 0, Exception $previous = null) {
        $this->userMessage = $userMessage;
        parent::__construct($developerMessage, $code, $previous);
    }

    // Method to get the user-friendly message
    public function get_user_message() {
        return $this->userMessage;
    }    
}
