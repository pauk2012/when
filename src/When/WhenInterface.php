<?php

    namespace When;

    use \DateTime as CoreDateTime;
    use \DateInterval as Interval;

    interface WhenInterface
    {

        /**
         * Constructor Method
         *
         * @throws When\Exceptions\InvalidArgument
         * @access public
         * @param string|CoreDateTime $datetime
         * @param string $timezone
         * @return void
         */
        public function __construct($datetime = null, $rule = null);


        /**
         * Set: Recurance Frequency
         *
         * @throws When\Exceptions\InvalidArgument
         * @access public
         * @param string|integer $frequency
         * @return $this
         */
        public function setFrequency($frequency);
        public function getFrequency();


        /**
         * Recur Until
         *
         * @throws When\Exceptions\InvalidArgument
         * @access public
         * @param string|CoreDateTime $until
         * @return $this
         */
        public function setUntil($until);
        public function getUntil();


        /**
         * Number of Recurrences
         *
         * @throws When\Exceptions\InvalidArgument
         * @access public
         * @param integer $count
         * @return $this
         */
        public function setCount($count);
        public function getCount();


        /**
         * Recurrence Interval
         *
         * @throws When\Exceptions\InvalidArgument
         * @access public
         * @param integer $interval
         * @return $this
         */
        public function setInterval($interval);
        public function getInterval();


        /**
         * Include Start and End?
         *
         * @throws When\Exceptions\InvalidArgument
         * @access public
         * @param boolean|integer $inclusive
         * @return $this
         */
        public function setInclusive($inclusive);
        public function getInclusive();


        /**
         * Offset Recurrences
         *
         * @throws When\Exceptions\InvalidArgument
         * @access public
         * @param integer $offset
         * @return $this
         */
        public function setOffset($offset);
        public function getOffset();


        /**
         * by Seconds
         *
         * @throws When\Exceptions\InsufficientArguments
         * @throws When\Exceptions\InvalidArgument
         * @access public
         * @params integer
         * @return $this
         */
        public function setSeconds(array $seconds);
        public function getSeconds();


        /**
         * by Minutes
         *
         * @throws When\Exceptions\InsufficientArguments
         * @throws When\Exceptions\InvalidArgument
         * @access public
         * @params integer
         * @return $this
         */
        public function setMinutes(array $minutes);
        public function getMinutes();


        /**
         * by Hour
         *
         * @throws When\Exceptions\InsufficientArguments
         * @throws When\Exceptions\InvalidArgument
         * @access public
         * @params integer
         * @return $this
         */
        public function setHours(array $hours);
        public function getHours();


        /**
         * Recurrence Interval
         *
         * @throws When\Exceptions\InsufficientArguments
         * @throws When\Exceptions\InvalidArgument
         * @access public
         * @params integer
         * @return $this
         */
        public function setWeekDays(array $weekDays);
        public function getWeekDays();


        /**
         * by Month Day
         *
         * @throws When\Exceptions\InsufficientArguments
         * @throws When\Exceptions\InvalidArgument
         * @access public
         * @params integer
         * @return $this
         */
        public function setMonthDays(array $monthDays);
        public function getMonthDays();


        /**
         * by Year Day
         *
         * @throws When\Exceptions\InsufficientArguments
         * @throws When\Exceptions\InvalidArgument
         * @access public
         * @params integer
         * @return $this
         */
        public function setYearDays(array $yearDays);
        public function getYearDays();


        /**
         * Recurrence Interval
         *
         * @throws When\Exceptions\InsufficientArguments
         * @throws When\Exceptions\InvalidArgument
         * @access public
         * @params integer
         * @return $this
         */
        public function setWeekNumbers(array $weekNumbers);
        public function getWeekNumbers();


        /**
         * by Month
         *
         * @throws When\Exceptions\InsufficientArguments
         * @throws When\Exceptions\InvalidArgument
         * @access public
         * @params integer|string
         * @return $this
         */
        public function setMonths(array $months);
        public function getMonths();


        /**
         * by Position
         *
         * @throws When\Exceptions\InvalidArgument
         * @access public
         * @params integer
         * @return $this
         */
        public function setPositions(array $positions);
        public function getPositions();


        /**
         * Recurrence Interval
         *
         * @throws When\Exceptions\InvalidArgument
         * @access public
         * @param string|integer $weekDay
         * @return $this
         */
        public function setWeekStart($weekDay);
        public function getWeekStart();


        /**
         * Set: Recursion Rule String
         *
         * @throws When\Exceptions\InvalidArgument
         * @access public
         * @return string
         */
        public function setRule($rule);
        public function getRule();


        /**
         * Generate Recurrences
         *
         * @access public
         * @param string $format
         * @return (CoreDateTime|string)[]
         */
        public function generate($format = null);

        /**
         * Occurs?
         *
         * Does the date provided fit the date recursion rule?
         *
         * @access public
         * @param CoreDateTime $date
         * @return boolean
         */
        public function occurs(CoreDateTime $date);

    }
