<?php

class Validator {
    private $data;
    private $current_field;
    private $current_alias;
    private $response_messages = [
        "required" => "{field} is required.",
        "alpha" => "{field} must contains alphabatic charectors only.",
        "alpha_num" => "{field} must contains alphabatic charectors & numbers only.",
        "numeric" => "{field} must contains numbers only.",
        "email" => "{field} is invalid.",
        "max_len" => "{field} is too long.",
        "min_len" => "{field} is too short.",
        "max_val" => "{field} is too high.",
        "min_val" => "{field} is too low.",
        "enum" => "{field} is invalid.",
        "equals" => "{field} does not match.",
        "must_contain" => "{field} must contains {chars}.",
        "match" => "{field} is invalid.",
        "date" => "{field} is invalid.",
        "date_after" => "{field} date is not valid.",
        "date_before" => "{field} date is not valid.",
    ];
    public $error_messages = [];
    private $next = true;

    function __construct($data) {
        $this->data = $data;
    }

    private function add_error_message($type, $others = []) {
        $field_name = $this->current_alias ? ucfirst($this->current_alias) : ucfirst($this->current_field);
        $msg = str_replace('{field}', $field_name, $this->response_messages[$type]);
        foreach ($others as $key => $val) {
            $msg = str_replace('{'.$key.'}', $val, $msg);
        }
        $this->error_messages[$this->current_field] = $msg;
    }

    private function exists() {
        return isset($this->data[$this->current_field]) && $this->data[$this->current_field];
    }

    function set_response_messages($messages) {
        foreach ($messages as $key => $val) {
            $this->response_messages[$key] = $val;
        }
    }

    function field($name, $alias = null) {
        $this->current_field = $name;
        $this->next = true;
        $this->current_alias = $alias;
        return $this;
    }

    function required() {
        if (!$this->exists()) {
            $this->add_error_message('required');
            $this->next = false;
        }
        return $this;
    }

    function alpha($ignore = []) {
        if ($this->next && $this->exists() && !ctype_alpha(str_replace($ignore, '', $this->data[$this->current_field]))) {
            $this->add_error_message('alpha');
            $this->next = false;
        }
        return $this;
    }

    function alpha_num($ignore = []) {
        if ($this->next && $this->exists() && !ctype_alnum(str_replace($ignore, '', $this->data[$this->current_field]))) {
            $this->add_error_message('alpha_num');
            $this->next = false;
        }
        return $this;
    }

    function numeric() {
        if ($this->next && $this->exists() && !is_numeric($this->data[$this->current_field])) {
            $this->add_error_message('numeric');
            $this->next = false;
        }
        return $this;
    }

    function email() {
        if ($this->next && $this->exists() && !filter_var($this->data[$this->current_field], FILTER_VALIDATE_EMAIL)) {
            $this->add_error_message('email');
            $this->next = false;
        }
        return $this;
    }

    function max_len($size) {
        if ($this->next && $this->exists() && strlen($this->data[$this->current_field]) > $size) {
            $this->add_error_message('max_len');
            $this->next = false;
        }
        return $this;
    }

    function min_len($size) {
        if ($this->next && $this->exists() && strlen($this->data[$this->current_field]) < $size) {
            $this->add_error_message('min_len');
            $this->next = false;
        }
        return $this;
    }

    function max_val($val) {
        if ($this->next && $this->exists() && $this->data[$this->current_field] > $val) {
            $this->add_error_message('max_val');
            $this->next = false;
        }
        return $this;
    }

    function min_val($val) {
        if ($this->next && $this->exists() && $this->data[$this->current_field] < $val) {
            $this->add_error_message('min_val');
            $this->next = false;
        }
        return $this;
    }

    function enum($list) {
        if ($this->next && $this->exists() && !in_array($this->data[$this->current_field], $list)) {
            $this->add_error_message('enum');
            $this->next = false;
        }
        return $this;
    }

    function equals($value) {
        if ($this->next && $this->exists() && $this->data[$this->current_field] != $value) {
            $this->add_error_message('equals');
            $this->next = false;
        }
        return $this;
    }

    function date($format = 'Y-m-d') {
        if ($this->next && $this->exists()) {
            $dateTime = DateTime::createFromFormat($format, $this->data[$this->current_field]);
            if (!($dateTime && $dateTime->format($format) == $this->data[$this->current_field])) {
                $this->add_error_message('date');
                $this->next = false;
            }
        }
        return $this;
    }

    function date_after($date) {
        if ($this->next && $this->exists() && strtotime($date) >= strtotime($this->data[$this->current_field])) {
            $this->add_error_message('date_after');
            $this->next = false;
        }
        return $this;
    }

    function date_before($date) {
        if ($this->next && $this->exists() && strtotime($date) <= strtotime($this->data[$this->current_field])) {
            $this->add_error_message('date_before');
            $this->next = false;
        }
        return $this;
    }

    function must_contain($chars) {
        if ($this->next && $this->exists() && !preg_match("/[".$chars."]/i", $this->data[$this->current_field])) {
            $this->add_error_message('must_contain', ['chars' => $chars]);
            $this->next = false;
        }
        return $this;
    }

    function match($patarn) {
        if ($this->next && $this->exists() && !preg_match($patarn, $this->data[$this->current_field])) {
            $this->add_error_message('match');
            $this->next = false;
        }
        return $this;
    }

    function is_valid() {
        return count($this->error_messages) == 0;
    }

    function get_error_message($field) {
        return $this->error_messages[$field] ?? "";
    }

    
}
