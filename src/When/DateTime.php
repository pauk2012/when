<?php

    namespace When;

    use \DateTime as CoreDateTime;
    use \DateInterval as Interval;

    class DateTime extends CoreDateTime
    {

        // Recurrence Frequency
        const YEARLY            = 1;
        const MONTHLY           = 2;
        const WEEKLY            = 3;
        const DAILY             = 4;
        const HOURLY            = 5;
        const MINUTELY          = 6;
        const SECONDLY          = 7;
        // Months
        const JANUARY           = 1;
        const FEBRUARY          = 2;
        const MARCH             = 3;
        const APRIL             = 4;
        const MAY               = 5;
        const JUNE              = 6;
        const JULY              = 7;
        const AUGUST            = 8;
        const SEPTEMBER         = 9;
        const OCTOBER           = 10;
        const NOVEMBER          = 11;
        const DECEMBER          = 12;
        // Week Days
        const SUNDAY            = 0;
        const MONDAY            = 1;
        const TUESDAY           = 2;
        const WEDNESDAY         = 3;
        const THURSDAY          = 4;
        const FRIDAY            = 5;
        const SATURDAY          = 6;
        // Inclusivity Types
        const IGNORED           = 0;
        const INCLUDED          = 1;
        const REQUIRED          = 2;

        /**
         * @var integer $frequency
         */
        protected $frequency;

        /**
         * @var CoreDateTime $until
         */
        protected $until;

        /**
         * @var integer $count
         */
        protected $count;

        /**
         * @var integer $interval
         */
        protected $interval     = 1;

        /**
         * @var boolean $inclusive
         */
        protected $inclusive    = 1;

        /**
         * @var integer $offset
         */
        protected $offset       = 0;

        /**
         * @var integer[] $seconds
         */
        protected $seconds;

        /**
         * @var integer[] $minutes
         */
        protected $minutes;

        /**
         * @var integer[] $hours
         */
        protected $hours;

        /**
         * @var integer[] $weekDays
         */
        protected $weekDays;

        /**
         * @var integer[] $monthDays
         */
        protected $monthDays;

        /**
         * @var integer[] $yearDays
         */
        protected $yearDays;

        /**
         * @var integer[] $weekNumbers
         */
        protected $weekNumbers;

        /**
         * @var integer[] $months
         */
        protected $months;

        /**
         * @var integer $position
         */
        protected $position;

        /**
         * @var integer $weekStart
         */
        protected $weekStart    = 1;

        /**
         * @var CoreDateTime[] $occurrences
         */
        protected $occurrences  = array();

        /**
         * @var string $rule
         */
        protected $rule;

        /**
         * @var CoreDateTime[] $results
         */
        public $results;

        /**
         * @static
         * @var array $mapping
         */
        protected static $mapping = array(
            'frequency'     => 'FREQ',          // ENUM
            'until'         => 'UNTIL',         // Integer (singular)
            'count'         => 'COUNT',         // Integer (singular)
            'interval'      => 'INTERVAL',      // Integer (singular)
            'inclusive'     => 'INCLUSIVE',     // ENUM
            'offset'        => 'OFFSET',        // Integer (singular)
            'seconds'       => 'BYSECOND',      // Integer (multiple)
            'minutes'       => 'BYMINUTE',      // Integer (multiple)
            'hours'         => 'BYHOUR',        // Integer (multiple)
            'weekDays'      => 'BYWEEKDAY',     // Integer (multiple)
            'monthDays'     => 'BYMONTHDAY',    // Integer (multiple)
            'yearDays'      => 'BYYEARDAY',     // Integer (multiple)
            'weekNumbers'   => 'BYWEEKNO',      // Integer (multiple)
            'months'        => 'BYMONTH',       // Integer (multiple)
            'position'      => 'BYSETPOS',      // Integer (multiple)
            'weekStart'     => 'WKST',          // Integer (singular)
        );


        /**
         * Constructor Method
         *
         * @throws When\Exceptions\InvalidArgument
         * @access public
         * @param string|CoreDateTime $datetime
         * @param string $timezone
         * @return void
         */
        public function __construct($datetime = null, $rule = null)
        {
            try {
                is_object($datetime) && $datetime instanceof CoreDateTime
                    ? parent::__construct($datetime->format(self::RFC3339))
                    : parent::__construct($datetime);
            }
            catch(\Exception $e) {
                throw new Exceptions\InvalidArgument;
            }
            // If a rule has been specified to initialise the object properties with, attempt that now.
            if(!is_null($rule)) {
                $this->setRule($rule);
            }
        }


        /**
         * Recurance Frequency
         *
         * @throws When\Exceptions\InvalidArgument
         * @access public
         * @param string|integer $frequency
         * @return $this
         */
        public function frequency($frequency)
        {
            // Convert the frequency to lowercase if it is a string for easier matching.
            if(is_string($frequency)) {
                $frequency = strtolower(trim($frequency));
            }
            switch($frequency)
            {
                case 'yearly':
                case self::YEARLY:
                    $this->frequency = self::YEARLY;
                    break;
                case 'monthly':
                case self::MONTHLY:
                    $this->frequency = self::MONTHLY;
                    break;
                case 'weekly':
                case self::WEEKLY:
                    $this->frequency = self::WEEKLY;
                    break;
                case 'daily':
                case self::DAILY:
                    $this->frequency = self::DAILY;
                    break;
                case 'hourly':
                case self::HOURLY:
                    $this->frequency = self::HOURLY;
                    break;
                case 'minutely':
                case self::MINUTELY:
                    $this->frequency = self::MINUTELY;
                    break;
                case 'secondly':
                case self::SECONDLY:
                    $this->frequency = self::SECONDLY;
                    break;
                default:
                    throw new Exceptions\InvalidArgument;
                    break;
            }
            return $this;
        }


        /**
         * Recur Until
         *
         * @throws When\Exceptions\InvalidArgument
         * @access public
         * @param string|CoreDateTime $until
         * @return $this
         */
        public function until($until)
        {
            try {
                $until = is_object($until) && $until instanceof CoreDateTime
                    // Even though $until is an instance of CoreDateTime, we shall make a new instance to make sure it
                    // isn't an instance of $this.
                    ? new CoreDateTime($until->format(self::RFC3339))
                    : new CoreDateTime($until);
                if($until >= $this) {
                    $this->until = $until;
                    return $this;
                }
                throw new Exceptions\InvalidArgument;
            }
            catch(\Exception $e) {
                throw new Exceptions\InvalidArgument;
            }
        }


        /**
         * Number of Recurrences
         *
         * @throws When\Exceptions\InvalidArgument
         * @access public
         * @param integer $count
         * @return $this
         */
        public function count($count)
        {
            if(preg_match('/^[1-9]\\d*$/', $count)) {
                $this->count = (int) $count;
                return $this;
            }
            throw new Exceptions\InvalidArgument;
        }


        /**
         * Recurrence Interval
         *
         * @throws When\Exceptions\InvalidArgument
         * @access public
         * @param integer $interval
         * @return $this
         */
        public function interval($interval)
        {
            if(preg_match('/^[1-9]\\d*$/', $interval)) {
                $this->interval = (int) $interval;
                return $this;
            }
            throw new Exceptions\InvalidArgument;
        }


        /**
         * Include Start and End?
         *
         * @throws When\Exceptions\InvalidArgument
         * @access public
         * @param boolean|integer $inclusive
         * @return $this
         */
        public function inclusive($inclusive)
        {
            if(is_bool($inclusive)) {
                $inclusive = $inclusive
                    ? self::INCLUDED
                    : self::IGNORED;
            }
            elseif(is_string($inclusive)) {
                $inclusive = strtoupper($inclusive);
            }
            elseif(preg_match('/^[1-9]\\d*$/', $inclusive)) {
                $inclusive = (int) $inclusive;
            }
            switch($inclusive) {
                case 'IGNORED':
                case self::IGNORED:
                    $this->inclusive = self::IGNORED;
                    break;
                case 'INCLUDED':
                case self::INCLUDED:
                    $this->inclusive = self::INCLUDED;
                    break;
                case 'REQUIRED':
                case self::REQUIRED:
                    $this->inclusive = self::REQUIRED;
                    break;
                default:
                    throw new Exceptions\InvalidArgument;
                    break;
            }
            return $this;
        }


        /**
         * Offset Recurrences
         *
         * @throws When\Exceptions\InvalidArgument
         * @access public
         * @param integer $offset
         * @return $this
         */
        public function offset($offset)
        {
            if(preg_match('/^[1-9]\\d*$/', $offset)) {
                $this->offset = (int) $offset;
                return $this;
            }
            throw new Exceptions\InvalidArgument;
        }


        /**
         * by Seconds
         *
         * @throws When\Exceptions\InsufficientArguments
         * @throws When\Exceptions\InvalidArgument
         * @access public
         * @params integer
         * @return $this
         */
        public function second()
        {
            if(func_num_args() > 0) {
                $arguments = func_get_args();
                array_walk($arguments, function(&$argument) {
                    if(!preg_match('/^(0|[1-9]\\d*)$/', $argument) || $argument > 59) {
                        throw new Exceptions\InvalidArgument;
                    }
                    $argument = (int) $argument;
                });
                $arguments = array_unique($arguments, SORT_REGULAR);
                $this->seconds = $arguments;
                return $this;
            }
            throw new Exceptions\InsufficientArguments;
        }


        /**
         * by Minutes
         *
         * @throws When\Exceptions\InsufficientArguments
         * @throws When\Exceptions\InvalidArgument
         * @access public
         * @params integer
         * @return $this
         */
        public function minute()
        {
            if(func_num_args() > 0) {
                $arguments = func_get_args();
                array_walk($arguments, function(&$argument) {
                    if(!preg_match('/^(0|[1-9]\\d*)$/', $argument) || $argument > 59) {
                        throw new Exceptions\InvalidArgument;
                    }
                    $argument = (int) $argument;
                });
                $arguments = array_unique($arguments, SORT_REGULAR);
                $this->minutes = $arguments;
                return $this;
            }
            throw new Exceptions\InsufficientArguments;
        }


        /**
         * by Hour
         *
         * @throws When\Exceptions\InsufficientArguments
         * @throws When\Exceptions\InvalidArgument
         * @access public
         * @params integer
         * @return $this
         */
        public function hour()
        {
            if(func_num_args() > 0) {
                $arguments = func_get_args();
                array_walk($arguments, function(&$argument) {
                    if(!preg_match('/^(0|[1-9]\\d*)$/', $argument) || $argument > 23) {
                        throw new Exceptions\InvalidArgument;
                    }
                    $argument = (int) $argument;
                });
                $arguments = array_unique($arguments, SORT_REGULAR);
                $this->hours = $arguments;
                return $this;
            }
            throw new Exceptions\InsufficientArguments;
        }


        /**
         * Recurrence Interval
         *
         * @throws When\Exceptions\InsufficientArguments
         * @throws When\Exceptions\InvalidArgument
         * @access public
         * @params integer
         * @return $this
         */
        public function weekDay($weekDay)
        {
            if(func_num_args() > 0) {
                $arguments = func_get_args();
                array_walk($arguments, function(&$argument) {
                    if(!is_int($argument = $this->validateWeekDay($argument))) {
                        throw new Exceptions\InvalidArgument;
                    }
                });
                $arguments = array_unique($arguments, SORT_REGULAR);
                $this->weekDays = $arguments;
                return $this;
            }
            throw new Exceptions\InsufficientArguments;
        }


        /**
         * by Month Day
         *
         * @throws When\Exceptions\InsufficientArguments
         * @throws When\Exceptions\InvalidArgument
         * @access public
         * @params integer
         * @return $this
         */
        public function monthDay()
        {
            if(func_num_args() > 0) {
                $arguments = func_get_args();
                array_walk($arguments, function(&$argument) {
                    if(
                        !preg_match('/^-?[1-9]\\d*$/', $argument)
                     || (
                            ($argument <  1 || $argument >  31)
                         && ($argument > -1 || $argument < -31)
                        )
                    ) {
                        throw new Exceptions\InvalidArgument;
                    }
                    $argument = (int) $argument;
                });
                $arguments = array_unique($arguments, SORT_REGULAR);
                $this->monthDays = $arguments;
                return $this;
            }
            throw new Exceptions\InsufficientArguments;
        }


        /**
         * by Year Day
         *
         * @throws When\Exceptions\InsufficientArguments
         * @throws When\Exceptions\InvalidArgument
         * @access public
         * @params integer
         * @return $this
         */
        public function yearDay()
        {
            if(func_num_args() > 0) {
                $arguments = func_get_args();
                array_walk($arguments, function(&$argument) {
                    if(
                        !preg_match('/^-?[1-9]\\d*$/', $argument)
                     || (
                            ($argument <  1 || $argument >  366)
                         && ($argument > -1 || $argument < -366)
                        )
                    ) {
                        throw new Exceptions\InvalidArgument;
                    }
                    $argument = (int) $argument;
                });
                $arguments = array_unique($arguments, SORT_REGULAR);
                $this->yearDays = $arguments;
                return $this;
            }
            throw new Exceptions\InsufficientArguments;
        }


        /**
         * Recurrence Interval
         *
         * @throws When\Exceptions\InsufficientArguments
         * @throws When\Exceptions\InvalidArgument
         * @access public
         * @params integer
         * @return $this
         */
        public function weekNumber()
        {
            if(func_num_args() > 0) {
                $arguments = func_get_args();
                array_walk($arguments, function(&$argument) {
                    if(
                        !preg_match('/^-?[1-9]\\d*$/', $argument)
                     || (
                            ($argument <  1 || $argument >  53)
                         && ($argument > -1 || $argument < -53)
                        )
                    ) {
                        throw new Exceptions\InvalidArgument;
                    }
                    $argument = (int) $argument;
                });
                $arguments = array_unique($arguments, SORT_REGULAR);
                $this->weekNumbers = $arguments;
                return $this;
            }
            throw new Exceptions\InsufficientArguments;
        }


        /**
         * by Month
         *
         * @throws When\Exceptions\InsufficientArguments
         * @throws When\Exceptions\InvalidArgument
         * @access public
         * @params integer|string
         * @return $this
         */
        public function month()
        {
            if(func_num_args() > 0) {
                $arguments = func_get_args();
                array_walk($arguments, function(&$argument) {
                    if(!is_int($argument = $this->validateMonth($argument))) {
                        throw new Exceptions\InvalidArgument;
                    }
                });
                $arguments = array_unique($arguments, SORT_REGULAR);
                $this->months = $arguments;
                return $this;
            }
            throw new Exceptions\InsufficientArguments;
        }


        /**
         * by Position
         *
         * @throws When\Exceptions\InvalidArgument
         * @access public
         * @params integer
         * @return $this
         */
        public function position()
        {
            if(func_num_args() > 0) {
                $arguments = func_get_args();
                array_walk($arguments, function(&$argument) {
                    if(!preg_match('/^-?[1-9]\\d*$/', $argument)) {
                        throw new Exceptions\InvalidArgument;
                    }
                    $argument = (int) $argument;
                });
                $arguments = array_unique($arguments, SORT_REGULAR);
                $this->position = $arguments;
                return $this;
            }
            throw new Exceptions\InvalidArgument;
        }


        /**
         * Recurrence Interval
         *
         * @throws When\Exceptions\InvalidArgument
         * @access public
         * @param string|integer $weekDay
         * @return $this
         */
        public function weekStart($weekDay)
        {
            if(is_int($weekDay = $this->validateWeekDay($weekDay))) {
                $this->weekStart = $weekDay;
                return $this;
            }
            throw new Exceptions\InvalidArgument;
        }


        /**
         * Validate Month
         *
         * @access protected
         * @param string|integer $month
         * @return integer|false;
         */
        protected function validateMonth($month)
        {
            // If the month has already been specified correctly, then return it straight away.
            if(is_int($month) && $month >= 1 && $month <= 12) {
                return $month;
            }
            // if the month is a string, then we should make it all lowercase for consistency.
            if(is_string($month)) {
                $month = strtolower($month);
            }
            switch($month) {
                case 'jan':
                case 'january':
                case (string) self::JANUARY:
                    $month = self::JANUARY;
                    break;
                case 'feb':
                case 'februaru':
                case (string) self::FEBRUARY:
                    $month = self::FEBRUARY;
                    break;
                case 'mar':
                case 'march':
                case (string) self::MARCH:
                    $month = self::MARCH;
                    break;
                case 'apr':
                case 'april':
                case (string) self::APRIL:
                    $month = self::APRIL;
                    break;
                case 'may':
                case (string) self::MAY:
                    $month = self::MAY;
                    break;
                case 'jun':
                case 'june':
                case (string) self::JUNE:
                    $month = self::JUNE;
                    break;
                case 'jul':
                case 'july':
                case (string) self::JULY:
                    $month = self::JULY;
                    break;
                case 'aug':
                case 'august':
                case (string) self::AUGUST:
                    $month = self::AUGUST;
                    break;
                case 'sep':
                case 'september':
                case (string) self::SEPTEMBER:
                    $month = self::SEPTEMBER;
                    break;
                case 'oct':
                case 'october':
                case (string) self::OCTOBER:
                    $month = self::OCTOBER;
                    break;
                case 'nov':
                case 'november':
                case (string) self::NOVEMBER:
                    $month = self::NOVEMBER;
                    break;
                case 'dec':
                case 'december':
                case (string) self::DECEMBER:
                    $month = self::DECEMBER;
                    break;

                default:
                    $month = false;
                    break;
            }
            return $month;
        }


        /**
         * Validate Week Day
         *
         * @access protected
         * @param string|integer $weekDay
         * @return integer|false
         */
        protected function validateWeekDay($weekDay)
        {
            // If the week day has already been specified correctly, then return it straight away.
            if(is_int($weekDay) && $weekDay >= 0 && $weekDay <= 6) {
                return $weekDay;
            }
            // if the week day is a string, then we should make it all lowercase for consistency.
            if(is_string($weekDay)) {
                $weekDay = strtolower($weekDay);
            }
            switch($weekDay) {
                case 'su':
                case 'sun':
                case 'sunday':
                case (string) self::SUNDAY:
                    $weekDay = self::SUNDAY;
                    break;
                case 'mo':
                case 'mon':
                case 'monday':
                case (string) self::MONDAY:
                    $weekDay = self::MONDAY;
                    break;
                case 'tu':
                case 'tue':
                case 'tues':
                case 'tuesday':
                case (string) self::TUESDAY:
                    $weekDay = self::TUESDAY;
                    break;
                case 'we':
                case 'wed':
                case 'weds':
                case 'wednesday':
                case (string) self::WEDNESDAY:
                    $weekDay = self::WEDNESDAY;
                    break;
                case 'th':
                case 'thu':
                case 'thur':
                case 'thurs':
                case 'thursday':
                case (string) self::THURSDAY:
                    $weekDay = self::THURSDAY;
                    break;
                case 'fr':
                case 'fri':
                case 'friday':
                case (string) self::FRIDAY:
                    $weekDay = self::FRIDAY;
                    break;
                case 'sa':
                case 'sat':
                case 'saturday':
                case (string) self::SATURDAY:
                    $weekDay = self::SATURDAY;
                    break;
                default:
                    $weekDay = false;
                    break;
            }
            return $weekDay;
        }


        /**
         * Recursion Rule
         *
         * @throws When\Exceptions\InvalidArgument
         * @access public
         * @return string
         */
        public function rule()
        {
            // Initial checks, just as if we were generating the dates.
            $this->checkCriteria();
            // Define the variable that will hold the rule (initialise to an empty string).
            $rule = array();
            $frequencies = array(null, 'YEARLY', 'MONTHLY', 'WEEKLY', 'DAILY', 'HOURLY', 'MINUTELY', 'SECONDLY');
            $rule[] = 'FREQ=' . $frequencies[$this->frequency];
            if(isset($this->until)) {
                $rule[] = 'UNTIL=' . $this->until->format('U');
            }
            if(isset($this->count)) {
                $rule[] = 'COUNT=' . $this->count;
            }
            if($this->interval != 1) {
                $rule[] = 'INTERVAL=' . $this->interval;
            }
            if($this->inclusive != 1) {
                $rule[] = 'INCLUSIVE=' . ($this->inclusive ? 'REQUIRED' : 'IGNORED');
            }
            if($this->offset != 0) {
                $rule[] = 'OFFSET=' . $this->offset;
            }
            if(isset($this->seconds)) {
                $rule[] = 'BYSECOND=' . implode(',', $this->seconds);
            }
            if(isset($this->minutes)) {
                $rule[] = 'BYMINUTE=' . implode(',', $this->minutes);
            }
            if(isset($this->hours)) {
                $rule[] = 'BYHOUR=' . implode(',', $this->hours);
            }
            if(isset($this->weekDays)) {
                $rule[] = 'BYWEEKDAY=' . implode(',', $this->weekDays);
            }
            if(isset($this->monthDays)) {
                $rule[] = 'BYMONTHDAY=' . implode(',', $this->monthDays);
            }
            if(isset($this->yearDays)) {
                $rule[] = 'BYYEARDAY=' . implode(',', $this->yearDays);
            }
            if(isset($this->weekNumbers)) {
                $rule[] = 'BYWEEKNO=' . implode(',', $this->weekNumbers);
            }
            if(isset($this->months)) {
                $rule[] = 'BYMONTH=' . implode(',', $this->months);
            }
            if(isset($this->position)) {
                $rule[] = 'BYSETPOS=' . implode(',', $this->position);
            }
            if($this->weekStart != 1) {
                $rule[] = 'WKST=' . $this->weekStart;
            }
            $this->rule = implode(';', $rule);
            return $this->rule;
        }


        /**
         * Set RRULE String
         *
         * @access public
         * @param string $rule
         * @return void
         */
        public function setRule($rule)
        {
            if(!is_string($rule)) {
                throw new Exceptions\InvalidArgument;
            }
            $ruleParts = preg_split('/\\s*;\\s*/', $rule, -1, PREG_SPLIT_NO_EMPTY);
            try {
                foreach($ruleParts as $ruleSegment) {
                    if(!preg_match('/^([a-zA-Z]+)\s*\=\s*(([a-zA-Z]+|-?\\d+)(,\s*([a-zA-Z]+|-?\\d+))*)$/', $ruleSegment, $matches)) {
                        throw new Exceptions\InvalidRule;
                    }
                    list(, $name, $value) = $matches;
                    switch(strtoupper($name)) {
                        case 'FREQ':
                            $this->frequency($value);
                            break;
                        case 'UNTIL':
                            $this->until('@' . $value);
                            break;
                        case 'COUNT':
                            $this->count($value);
                            break;
                        case 'INTERVAL':
                            $this->interval($value);
                            break;
                        case 'INCLUSIVE':
                            $this->inclusive($value);
                            break;
                        case 'OFFSET':
                            $this->offset($value);
                            break;
                        case 'BYSECOND':
                            $value = preg_split('/\\s*,\\s*/', $value, -1, PREG_SPLIT_NO_EMPTY);
                            call_user_func_array(array($this, 'second'), $value);
                            break;
                        case 'BYMINUTE':
                            $value = preg_split('/\\s*,\\s*/', $value, -1, PREG_SPLIT_NO_EMPTY);
                            call_user_func_array(array($this, 'minute'), $value);
                            break;
                        case 'BYHOUR':
                            $value = preg_split('/\\s*,\\s*/', $value, -1, PREG_SPLIT_NO_EMPTY);
                            call_user_func_array(array($this, 'hour'), $value);
                            break;
                        case 'BYWEEKDAY':
                            $value = preg_split('/\\s*,\\s*/', $value, -1, PREG_SPLIT_NO_EMPTY);
                            call_user_func_array(array($this, 'weekDay'), $value);
                            break;
                        case 'BYMONTHDAY':
                            $value = preg_split('/\\s*,\\s*/', $value, -1, PREG_SPLIT_NO_EMPTY);
                            call_user_func_array(array($this, 'monthDay'), $value);
                            break;
                        case 'BYYEARDAY':
                            $value = preg_split('/\\s*,\\s*/', $value, -1, PREG_SPLIT_NO_EMPTY);
                            call_user_func_array(array($this, 'yearDay'), $value);
                            break;
                        case 'BYWEEKNO':
                            $value = preg_split('/\\s*,\\s*/', $value, -1, PREG_SPLIT_NO_EMPTY);
                            call_user_func_array(array($this, 'weekNumber'), $value);
                            break;
                        case 'BYMONTH':
                            $value = preg_split('/\\s*,\\s*/', $value, -1, PREG_SPLIT_NO_EMPTY);
                            call_user_func_array(array($this, 'month'), $value);
                            break;
                        case 'BYSETPOS':
                            $value = preg_split('/\\s*,\\s*/', $value, -1, PREG_SPLIT_NO_EMPTY);
                            call_user_func_array(array($this, 'position'), $value);
                            break;
                        default:
                            throw new Exceptions\InvalidRule;
                            break;
                    }
                }
            }
            catch(\Exception $e) {
                throw new Exceptions\InvalidRule;
            }
        }


        /**
         * Magic: Sleep (Serialise Object)
         *
         * There is no need to save the entire object as it can be expressed using it's RRULE string, so specify that it
         * should be the only thing serialised after it has been generated.
         *
         * @access public
         * @return array
         */
        public function __sleep()
        {
            // Generate RRULE string.
            $this->rule();
            // Specify list of object properties to be serialised.
            return array('rule');
        }


        /**
         * Magic: Wakeup (Unserialise Object)
         *
         * @access public
         * @return void
         */
        public function __wakeup()
        {
            // After the RRULE string has been populated from the unserialisation, generated the rest of the object
            // properties from it.
            $this->setRule($this->rule);
        }


        /**
         * Magic: To String
         *
         * @access public
         * @return string
         */
        public function __toString()
        {
            return $this->rule();
        }


        /**
         * Generate Recurrences
         *
         * @access public
         * @param string $format
         * @return (CoreDateTime|string)[]
         */
        public function generate($format = null)
        {
            // Before we try to generate the recurring dates, check the criteria for a couple of conditions that have to
            // be met.
            $this->checkCriteria();
            // Define and normalise a counter for the iteration loop.
            $count = 0;
            // Clone the start DateTime object, as we want to work on a copy of it, rather than editing the original
            // object itself.
            $date = clone $this;
            // NOTE: Do not add the start date to the list of occurences automatically even if it matches, it will be
            // found just like all the other dates.
            // However, if the inclusivity type is required, then we need to check now if the start date is valid.
            if($this->inclusive == self::REQUIRED && !$this->occursOn($date)) {
                throw new Exceptions\InvalidStartDate;
            }
            // Each frequency requires slightly different processing, so switch to the correct one.
            // NOTE: When referencing the "current" date, it means the date that the current iteration is pointing to,
            // not the actual current date.
            // Even though every case starts with exactly the same while loop, we're doing it this way so that there is
            // one less control structure on each iteration. It doesn't seem like much, but this iteration loop could
            // run several thousand times in one call to this method.
            switch($this->frequency) {
                case self::YEARLY:
                    while(
                        (!isset($this->until) || $date < $this->until)
                     && (!isset($this->count) || count($this->occurrences) < $this->count + $this->offset)
                    ) {
                        // If specific months of the year have been set in the criteria, iterate over each of them.
                        if(isset($this->months)) {
                            $specificDay = $date->format('j');
                            foreach($this->months as $month) {
                                // If specific days of the month have been set in the criteria, iterate over each of
                                // them.
                                if(isset($this->monthDays)) {
                                    // Set the date to the first day of the month.
                                    $date->setDate($date->format('Y'), $month, 1);
                                    // Figure out how many days are in this particular month, and iterate over each of
                                    // them.
                                    $daysInMonth = (int) $date->format('t');
                                    for($i = 0; $i < $daysInMonth; $i++) {
                                        // If the current day in the month is an occurrence according to the criteria
                                        // set, then save it to the list.
                                        if($this->occursOn($date)) {
                                            $this->addOccurrence($this->generateTimeOccurrences($date));
                                        }
                                        // Move onto the next day by adding an interval of one day.
                                        $interval = new Interval('P1D');
                                        $date->add($interval);
                                    }
                                }
                                // Otherwise, iterate over every day in the month.
                                else {
                                    // Set the date to the same year and same day of the month as the start date (only
                                    // set month).
                                    $date->setDate($date->format('Y'), $month, $specificDay);
                                    // If the current day in the month is an occurrence according to the criteria
                                    // set, then save it to the list.
                                    if($this->occursOn($date)) {
                                        $this->addOccurrence($this->generateTimeOccurrences($date));
                                    }
                                }
                            }
                        }
                        // Otherwise, iterate over every month in the year.
                        else {
                            // Set the day to the first day of the year.
                            $date->setDate($date->format('Y'), 1, 1);
                            // Determine how many days are in the current year by querying whether it is a leap year or
                            // not.
                            $days = $date->format('L')
                                ? 366
                                : 365;
                            // Iterate over every day in the year.
                            for($i = 0; $i < $days; $i++) {
                                // If the current day of the year is an occurrence according to the criteria set, then
                                // save it to the list.
                                if($this->occursOn($date)) {
                                    $this->addOccurrence($this->generateTimeOccurrences($date));
                                }
                                // Move onto the next day by adding an interval of one day.
                                $interval = new Interval('P1D');
                                $date->add($interval);
                            }
                        }
                        // Clone the start date again, we shouldn't simply add one yearly interval onto the date looper
                        // as it could have incremented too far with other intervals added to it (months or days).
                        $date = clone $this;
                        // Move onto the next yearly interval.
                        $interval = new Interval('P' . ($this->interval * ++$count) . 'Y');
                        $date->add($interval);
                    }
                    break;
                case self::MONTHLY:
                    while(
                        (!isset($this->until) || $date < $this->until)
                     && (!isset($this->count) || count($this->occurrences) < $this->count + $this->offset)
                    ) {
                        // Define an array to hold the occurrences as we need to filter them by position before adding
                        // them.
                        $occurrences = array();
                        // Calculate which day of the month we are on, and how many in total the current month has.
                        $day = (int) $date->format('j');
                        $daysInMonth = (int) $date->format('t');
                        // Iterate over the remaining days in the month.
                        for(; $day <= $daysInMonth; $day++) {
                            // If the current day of the month is an occurrence according to the criteria set, then
                            // save it to the list.
                            if($this->occursOn($date)) {
                                // Remember, we aren't adding directly; merge the temporary occurrences array with the
                                // list of generated time occurrences.
                                $occurrences = array_merge($occurrences, $this->generateTimeOccurrences($date));
                            }
                            // Move onto the next day by adding an interval of one day.
                            $interval = new Interval('P1D');
                            $date->add($interval);
                        }
                        // If the position criteria has been set, we need to filter the temporary array of occurrences
                        // accordingly.
                        if(isset($this->position) && is_array($this->position) && !empty($occurrences)) {
                            $occurrenceCount = count($occurrences);
                            foreach($this->position as $position) {
                                // If the position is positive, search from the beginning forwards.
                                if($position > 0) {
                                    $this->addOccurrence($occurrences[$position - 1]);
                                }
                                // And if it's negative, search from the end backwards. Specify an elseif allowing only
                                // negative numbers here as we cannot have a position of zero.
                                elseif($position < 0) {
                                    $this->addOccurrence($occurrences[$occurrenceCount + $position]);
                                }
                            }
                        }
                        // Otherwise we don't need to process the position criteria and we can simply add the
                        // occurrences straight away.
                        else {
                            $this->addOccurrence($occurrences);
                        }
                        // Clone the start date again, we shouldn't simply add one yearly interval onto the date looper
                        // as it could have incremented too far with other intervals added to it (months or days).
                        $date = clone $this;
                        // Reset the day of the month, just in case (like if the current day was 31 but the next month
                        // only had 30 days).
                        $date->setDate($date->format('Y'), $date->format('n'), 1);
                        // Move onto the next yearly interval.
                        $interval = new Interval('P' . ($this->interval * ++$count) . 'M');
                        $date->add($interval);
                    }
                    break;
                // Really tempted to leave this one (WEEKLY) blank (no break statement) and use DAILY. They're pretty
                // much identical (except this one is stupidly complicated).
                case self::WEEKLY:
                    $dayString = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
                    $weekStart = clone $this;
                    $weekStart->modify('last ' . $dayString[$this->weekStart]);
                    $weekStart->modify('+7 days');
                    $daysLeft = $weekStart->format('j') - $date->format('j');
                    $weekStart->modify('-7 days');
                    while(
                        (!isset($this->until) || $date < $this->until)
                     && (!isset($this->count) || count($this->occurrences) < $this->count + $this->offset)
                    ) {
                        for(; $daysLeft > 0; $daysLeft--) {
                            if($this->occursOn($date)) {
                                $this->addOccurrence($this->generateTimeOccurrences($date));
                            }
                            $interval = new Interval('P1D');
                            $date->add($interval);
                        }
                        $daysLeft = 7;
                        $date = clone $this;
                        $date->setDate($weekStart->format('Y'), $weekStart->format('n'), $weekStart->format('j'));
                        $interval = new Interval('P' . ($this->interval * (++$count * 7)) . 'D');
                        $date->add($interval);
                    }
                    break;
                case self::DAILY:
                    while(
                        (!isset($this->until) || $date < $this->until)
                     && (!isset($this->count) || count($this->occurrences) < $this->count + $this->offset)
                    ) {
                        if($this->occursOn($date)) {
                            $this->addOccurrence($this->generateTimeOccurrences($date));
                        }
                        $date = clone $this;
                        $interval = new Interval('P' . ($this->interval * ++$count) . 'D');
                        $date->add($interval);
                    }
                    break;
                case self::HOURLY:
                    while(
                        (!isset($this->until) || $date < $this->until)
                     && (!isset($this->count) || count($this->occurrences) < $this->count + $this->offset)
                    ) {
                        if($this->occursOn($date) && $this->occursAt($date)) {
                            $this->addOccurrence($date);
                        }
                        $date = clone $this;
                        $interval = new Interval('PT' . ($this->interval * ++$count) . 'H');
                        $date->add($interval);
                    }
                    break;
                case self::MINUTELY:
                    while(
                        (!isset($this->until) || $date < $this->until)
                     && (!isset($this->count) || count($this->occurrences) < $this->count + $this->offset)
                    ) {
                        if($this->occursOn($date) && $this->occursAt($date)) {
                            $this->addOccurrence($date);
                        }
                        $date = clone $this;
                        $interval = new Interval('PT' . ($this->interval * ++$count) . 'M');
                        $date->add($interval);
                    }
                    break;
                case self::SECONDLY:
                    while(
                        (!isset($this->until) || $date < $this->until)
                     && (!isset($this->count) || count($this->occurrences) < $this->count + $this->offset)
                    ) {
                        if($this->occursOn($date) && $this->occursAt($date)) {
                            $this->addOccurrence($date);
                        }
                        $date = clone $this;
                        $interval = new Interval('PT' . ($this->interval * ++$count) . 'S');
                        $date->add($interval);
                    }
                    break;
                default:
                    throw new Exceptions\FrequencyRequired;
                    break;
            }

            if($this->offset > 0) {
                // Because we may have generated extra occurences due to the offset, we should trim the occurences array to
                // contain the correct amount of datetimes. Don't forget to alter this if at some point in the future
                // negative offsets are permitted.
                $this->occurrences = array_slice($this->occurrences, $this->offset);
            }
            // Now that we have generated all of our occurences, we need to format them in the way the user wanted, and
            // then return them.
            return $this->prepare($format);
        }

        /**
         * Prepare Dates
         *
         * @access protected
         * @param string $format
         * @return array
         */
        protected function prepare($format = null)
        {
            $occurrences = array();
            // Iterate over each occurrence to determine how to return it.
            foreach($this->occurrences as $occurrence) {
                // Has a format string been specified?
                if(is_string($format)) {
                    // Do a check to see if an integer has been generated. If one has then return an integer data-type,
                    // otherwise return the string that is generated.
                    $occurrence = preg_match('/^(0|-?[1-9]\\d*)$/', $occurrence = $occurrence->format($format))
                        ? (int) $occurrence
                        : $occurrence;
                }
                // If a format string has not been passed, then return them as Date objects.
                else {
                    $occurrence = new CoreDateTime($occurrence->format(self::RFC3339));
                }
                // Add the formatted occurrence to the array to return.
                $occurrences[] = $occurrence;
            }
            return $occurrences;
        }

        /**
         * Occurs On
         *
         * @throws When\Exceptions\InvalidArgument
         * @access public
         * @param string|CoreDateTime $date
         * @return boolean
         */
        public function occursOn($date, $timezone = null)
        {
            // Attempt to create a DateTime object from the argument passed to the method. If the argument passed was
            // invalid, catch any Exceptions thrown by PHP and throw a much more informative "InvalidArgument" exception
            // of our own.
            try {
                $date = is_object($date) && $date instanceof CoreDateTime
                    ? $date
                    : new self($date, $timezone);
            }
            catch(\Exception $e) {
                throw new Exception\InvalidArgument;
            }

            // Break down the date object into usable pieces.
            $year               = (int)  $date->format('Y');
            $month              = (int)  $date->format('n');
            $monthDay           = (int)  $date->format('j');
            $leapYear           = (bool) $date->format('L');
            $yearDay            = (int)  $date->format('z') + 1;
            $fromEndOfMonth     = (int)  $date->format('t') + 1 - $monthDay;
            $fromEndOfYear = $leapYear
                ? $yearDay - 367
                : $yearDay - 366;
            $weekNumber         = (int)  $date->format('W');
            $weeksFromEnd       = (int)  ceil(abs($fromEndOfMonth) / 7) * (-1);
            $weekDay            = (int)  $date->format('w');
            $occurrence         = (int)  ceil($monthDay / 7);
            $occurrenceNegative = (int)  ceil(abs($fromEndOfMonth) / 7) * (-1);

            // Check Date Boundaries.
            // If the date is less than the start date, then it can't be a future reccurrence. On the other hand, we
            // also don't want it if the date is greater than the "until" limit.
            if($date < $this || (isset($this->until) && $date > $this->until)) {
                return false;
            }
            // If specific months have been set in the criteria, make sure that the date is one of those months.
            if(isset($this->months) && !in_array($month, $this->months)) {
                return false;
            }
            // If specific week days have been set in the criteria, make sure that the date is one of those week days.
            if(isset($this->weekDays) && !in_array($weekDay, $this->weekDays)) {
                return false;
            }
            // If specific weekday positions have been set in the criteria, make sure that the date is one of them.
            if(
                isset($this->position)
             && !in_array($occurrence, $this->position)
             && !in_array($occurrenceNegative, $this->position)
            ) {
                return false;
            }
            // If specific weeks of the year have been set in the criteria, make sure that the date is one of those
            // weeks.
            if(
                isset($this->weekNumbers)
             && !in_array($weekNumber, $this->weekNumbers)
             && !in_array($weeksFromEnd, $this->weekNumbers)
            ) {
                return false;
            }
            // If specific days of the month have been set in the criteria, make sure that the date is one of those
            // days.
            if(
                isset($this->monthDays)
             && !in_array($monthDay, $this->monthDays)
             && !in_array($fromEndOfMonth, $this->monthDays)
            ) {
                return false;
            }
            // If specific days of the year have been set in the criteria, make sure that the date is one of those days.
            if(
                isset($this->yearDays)
             && !in_array($yearDay, $this->yearDays)
             && !in_array($fromEndOfYear, $this->yearDays)
            ) {
                return false;
            }

            // If the date passed all the criteria conditions, then it is a a valid reccurrence. Return true.
            return true;
        }

        /**
         * Occurs At
         *
         * @throws When\Exceptions\InvalidArgument
         * @access public
         * @param string|CoreDateTime $time
         * @return boolean
         */
        public function occursAt(CoreDateTime $time)
        {
            // Attempt to create a DateTime object from the argument passed to the method. If the argument passed was
            // invalid, catch any Exceptions thrown by PHP and throw a much more informative "InvalidArgument" exception
            // of our own.
            try {
                $date = is_object($date) && $date instanceof CoreDateTime
                    ? $date
                    : parent::__construct($date, $timezone);
            }
            catch(\Exception $e) {
                throw new Exception\InvalidArgument;
            }

            // Break down the date object into usable pieces.
            $hour   = (int) $date->format('G');
            $minute = (int) $date->format('i');
            $second = (int) $date->format('s');

            // Check Date Boundaries.
            // If the date is less than the start date, then it can't be a future reccurrence. On the other hand, we
            // also don't want it if the date is greater than the "until" limit.
            if($date < $this || (isset($this->until) && $date > $this->until)) {
                return false;
            }
            // If specific hours of the day have been set in the criteria, make sure that the date is one of those
            // months.
            if(isset($this->hours) && !in_array($hour, $this->hours)) {
                return false;
            }
            // If specific minutes of the hour have been set in the criteria, make sure that the date is one of those
            // months.
            if(isset($this->minutes) && !in_array($minute, $this->minutes)) {
                return false;
            }
            // If specific hours of the day have been set in the criteria, make sure that the date is one of those
            // months.
            if(isset($this->seconds) && !in_array($second, $this->seconds)) {
                return false;
            }

            // If the date passed all the criteria conditions, then it is a a valid reccurrence. Return true.
            return true;
        }


        /**
         * Check Criteria
         *
         * @throws When\Exceptions\InvalidCombination
         * @throws When\Exceptions\Infinity
         * @access protected
         * @return void
         */
        protected function checkCriteria()
        {
            // If neither the "until" upper date boundary or the reccurrence count has been set then the generate()
            // method will result in an infinite loop. This is a bad thing, so we'll thrown an Infinity exception to let
            // the user know they've been a silly billy.
            if(is_null($this->until) && is_null($this->count)) {
                throw new Exceptions\Infinity;
            }
            // We'll also throw an exception if the frequency has not been set in the critera. It's kind of essential to
            // the core of a recurring date.
            if(is_null($this->frequency)) {
                throw new Exceptions\FrequencyRequired;
            }
            // If specific weeks of the year have been set in the criteria, the frequency must be yearly.
            if(isset($this->weekNumbers) && !in_array($this->frequency, array(self::YEARLY))) {
                throw new Exceptions\InvalidCombination;
            }
            // If specific days of the year have been set in the criteria, then frequency must be daily, weekly, or
            // monthly.
            if(isset($this->yearDays) && !in_array($this->frequency, array(self::DAILY, self::WEEKLY, self::MONTHLY))) {
                throw new Exceptions\InvalidCombination;
            }
            // If specific days of the month have been set, then the frequency must not be weekly.
            if(isset($this->monthDays) && in_array($this->frequency, array(self::WEEKLY))) {
                throw new Exceptions\InvalidCombination;
            }
        }


        /**
         * Add Occurrence
         *
         * @access protected
         * @param array $datetimes
         * @return void
         */
        protected function addOccurrence($datetimes)
        {
            // If a singular DateTime has been passed, we want to convert it to an array so that we don't experience
            // unexpected behaviour from the following foreach loop.
            if(!is_array($datetimes)) {
                $datetimes = array($datetimes);
            }
            foreach($datetimes as $datetime) {
                // If we already have enough occurrences, then we should break out of this loop and not bother adding
                // any more.
                if(isset($this->count) && count($this->occurrences) === $this->count + $this->offset) {
                    break;
                }
                // If DateTime object is not an instance of CoreDateTime, then something went horribly wrong considering
                // that this is a protected method.
                if(!$datetime instanceof CoreDateTime) {
                    throw new Exceptions\InvalidArgument;
                }
                // If the DateTime is not already a saved occurrence, and that it is allowed to be included (determined
                // by the "inclusive" critera), add it to the list of saved DateTimes.
                if(
                    !in_array($datetime, $this->occurrences)
                 // FOLLOWING TWO LINES IS A HACK UNTIL generateTimeOccurrences() is improved.
                 && $datetime >= $this
                 && (!isset($this->until) || $datetime <= $this->until)
                 && (
                        (bool) $this->inclusive
                     || !($datetime == $this || $datetime == $this->until)
                    )
                ) {
                    $this->occurrences[] = $datetime;
                }
            }
        }


        /**
         * Generate Time Occurrences
         *
         * @access protected
         * @param CoreDateTime $looper
         * @return CoreDateTime[]
         */
        protected function generateTimeOccurrences(CoreDateTime $looper)
        {
            $occurrences = array();
            $hours   = isset($this->hours)   ? $this->hours   : array($this->format('G'));
            $minutes = isset($this->minutes) ? $this->minutes : array($this->format('i'));
            $seconds = isset($this->seconds) ? $this->seconds : array($this->format('s'));
            foreach($hours as $hour) {
                foreach($minutes as $minute) {
                    foreach($seconds as $second) {
                        $occurrence = clone $looper;
                        $occurrence->setTime($hour, $minute, $second);
                        $occurrences[] = $occurrence;
                    }
                }
            }
            return $occurrences;
        }

    }
