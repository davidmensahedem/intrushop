<?php
class Validate {
    private $fields;

    public function __construct() {
        $this->fields = new Fields();
    }

    public function getFields() {
        return $this->fields;
    }

    // Validate a generic text field and return the Field object
    public function text($name, $value, $min = 1, $max = 255) {  

        // Get Field object and set its value
        $field = $this->fields->getField($name);
        $field->setValue($value);
        
        // Check field and set or clear error message
        if ($field->isRequired() && $field->isEmpty()) {
            $field->setErrorMessage('Required.');
        } else if (strlen($value) < $min && !$field->isEmpty()) {
            $field->setErrorMessage('Too short.');
        } else if (strlen($value) > $max) {
            $field->setErrorMessage('Too long.');
        } else {
            $field->clearErrorMessage();
        }
        
        return $field;
    }
    
    public function number($name, $value, $min = NULL, $max = NULL) {
        // Get Field object and do text field check
        $field = $this->text($name, $value);

        // if OK after text field check, move on to number check
        if (!$field->hasError() && !$field->isEmpty()) {
            if (!is_numeric($value)) {
                $field->setErrorMessage('Must be a valid number.');
            } else {
                if (isset($min) && $value < $min) {
                    $field->setErrorMessage("Must be $min or more.");
                } else if (isset($max) && $value > $max) {
                    $field->setErrorMessage("Must be $max or less.");
                } else {
                    $field->clearErrorMessage();
                }
            }
        }
    }

    // Validate a field with a generic pattern
    public function pattern($name, $value, $pattern, $message) {
        // Get Field object and do text field check
        $field = $this->text($name, $value);

        // if OK after text field check, move on to pattern check
        if (!$field->hasError() && !$field->isEmpty()) {
            $match = preg_match($pattern, $value);
            if ($match === FALSE) {
                $field->setErrorMessage('Error testing field.');
            } else if ( $match != 1 ) {
                $field->setErrorMessage($message);
            } else {
                $field->clearErrorMessage();
            }
        }
    }

    public function phone($name, $value) {
        // Get Field object and do text field check
        $field = $this->text($name, $value);

        // if OK after text field check, move on to phone check
        if (!$field->hasError() && !$field->isEmpty()) {
            // Call the pattern method to validate a phone number
            $pattern = '/^[[:digit:]]{3}-[[:digit:]]{3}-[[:digit:]]{4}$/';
            $message = 'Enter nnn-nnn-nnnn.';
            $this->pattern($name, $value, $pattern, $message);
        }
    }
    
    public function email($name, $value) {
        // Get Field object and do text field check
        $field = $this->text($name, $value);

        // if OK after text field check, move on to email check
        if (!$field->hasError() && !$field->isEmpty()) {
            if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $field->clearErrorMessage();
            } else {
                $field->setErrorMessage("Invalid email address.");
            }
        }
    }

    public function password($name, $password) {
        // Get Field object and do text field check
        $field = $this->text($name, $password, 6, 30);  // min 6, max 30 characters

        // if OK after text field check, move on to password check
        if (!$field->hasError() && !$field->isEmpty()) {

            // Pattern to validate password
            $pattern = "/^(?=.*[[:digit:]])(?=.*[[:upper:]])(?=.*[[:lower:]])";
            $pattern .= "[[:digit:][:upper:][:lower:]]{6,}$/";
            $message = "Must have at least one upper case, lower case, and digit.";
            $this->pattern($name, $password, $pattern, $message);
        }
    }

    public function verify($name, $password, $verify) {
        // Get Field object and do text field check
        $field = $this->text($name, $verify, 6, 30);  // min 6, max 30 characters
        
        // if OK after text field check, move on to verify check
        if (!$field->hasError() && (!$field->isEmpty() || !empty($password))) {
            
            if (strcmp($password, $verify) != 0) {
                $field->setErrorMessage('Passwords do not match.');
            } else {
                $field->clearErrorMessage();
            }
        }
    }

    public function state($name, $value) {
        // Get Field object and do text field check
        $field = $this->text($name, $value);

        // if OK after text field check, move on to state check
        if (!$field->hasError() && !$field->isEmpty()) {
            $states = [
                'AL', 'AK', 'AZ', 'AR', 'CA', 'CO', 'CT', 'DE', 'DC',
                'FL', 'GA', 'HI', 'ID', 'IL', 'IN', 'IA', 'KS', 'KY',
                'LA', 'ME', 'MA', 'MD', 'MI', 'MN', 'MS', 'MO', 'MT',
                'NE', 'NV', 'NH', 'NJ', 'NM', 'NY', 'NC', 'ND', 'OH',
                'OK', 'OR', 'PA', 'RI', 'SC', 'SD', 'TN', 'TX', 'UT',
                'VT', 'VA', 'WA', 'WV', 'WI', 'WY'
            ];
            $stateString = implode('|', $states);
            $pattern = '/^(' . $stateString . ')$/';
            $this->pattern($name, $value, $pattern, 'Enter 2 letter upper case state code.');
        }
    }

    public function zip($name, $value) {
        // Get Field object and do text field check
        $field = $this->text($name, $value);

        // if OK after text field check, move on to zip check
        if (!$field->hasError() && !$field->isEmpty()) {
            $pattern = '/^[[:digit:]]{5}(-[[:digit:]]{4})?$/';
            $message = 'Enter nnnnn or nnnnn-nnnn.';
            $this->pattern($name, $value, $pattern, $message);
        }
    }

    public function cardType($name, $value, $types) {
        $field = $this->fields->getField($name);
        if (empty($value)) {
            $field->setErrorMessage('Please select a card type.');
        } else {
            $typeString = implode('|', $types);
            $pattern = '/^(' . $typeString . ')$/';
            $this->pattern($name, $value, $pattern, 'Invalid card type.');
        }
    }

    public function cardNumber($name, $value, $type) {
        // Get Field object and do text field check
        $field = $this->text($name, $value);

        // if OK after text field check, move on to card number check
        if (!$field->hasError() && !$field->isEmpty()) {
            $pattern = '/^\d{16}$/';
            if ($type === 'a') {  // American Express
                $pattern = '/^\d{15}$/';
            }
            $this->pattern($name, $value, $pattern, "Invalid card number.");
        }  
    }
    
    public function cardCvv($name, $value, $type) {
        // Get Field object and do text field check
        $field = $this->text($name, $value);

        // if OK after text field check, move on to cvv check
        if (!$field->hasError() && !$field->isEmpty()) {
            $pattern = '/^\d{3}$/';
            if ($type === 'a') {  // American Express
                $pattern = '/^\d{4}$/';
            }
            $this->pattern($name, $value, $pattern, 'Invalid card cvv.');
        }  
    }
    
    public function cardExpDate($name, $value) {
        // Get Field object and do text field check
        $field = $this->text($name, $value);
        
        // if OK after text field check, move on to date format check
        if (!$field->hasError() && !$field->isEmpty()) {
            $pattern = '/^(0[1-9]|1[012])\/[1-9][[:digit:]]{3}?$/';
            $message = 'Invalid expiration date format.';
            $this->pattern($name, $value, $pattern, $message);    
            
            // if OK after date format check, move on to expired check
            if (!$field->hasError()) {
                $dateParts = explode('/', $value);
                $month = $dateParts[0];
                $year  = $dateParts[1];
                $dateString = $month . '/01/' . $year . ' last day of 23:59:59';
                $exp = new \DateTime($dateString);
                $now = new \DateTime();
                if ( $exp < $now ) {
                    $field->setErrorMessage('Card has expired.');
                } else {
                    $field->clearErrorMessage();
                }
            }
        }
    }

}
?>