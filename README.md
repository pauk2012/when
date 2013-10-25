# When [![Build Status](https://travis-ci.org/mynameiszanders/when.png?branch=master)](https://travis-ci.org/mynameiszanders/when)

A PHP library, managed by Composer, for RFC-2445 RRule date recursion; version **0.4.1**.

**Please note** that this repository was forked from [tplaner/When](https://github.com/tplaner/When); this is not
originally my project. I plan on altering the specification beyong the recommended standards to accomodate the need for
a completely specific commercial use-case.

Author: [Zander Baldwin](https://github.com/mynameiszanders)<br />
Original Author: [Tom Planer](https://github.com/tplaner)

##Original README

###About
The second version of When.

###Current Features
Currently this version does everything version 1 was capable of, it also supports byhour, byminute, and bysecond. Please check the [unit tests](https://github.com/tplaner/When/tree/develop/tests) for information about how to use it.

I will be replacing version 1 with this as soon as I complete the documentation. Until then here are some simple examples:

    // friday the 13th for the next 5 occurences
    $r = new When();
    $r->startDate(new DateTime("19980213T090000"))
      ->freq("monthly")
      ->count(5)
      ->byday("fr")
      ->bymonthday(13)
      ->generateOccurences();

    print_r($r->occurences);



    // friday the 13th for the next 5 occurences rrule
    $r = new When();
    $r->startDate(new DateTime("19980213T090000"))
      ->rrule("FREQ=MONTHLY;BYDAY=FR;BYMONTHDAY=13")
      ->generateOccurences();

    print_r($r->occurences);

###License
When is licensed under the MIT License, see `LICENSE` for specific details.
