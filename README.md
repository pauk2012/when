# When 1.0.3 [![Build Status](https://travis-ci.org/mynameiszanders/when.png?branch=master)](https://travis-ci.org/mynameiszanders/when)

A PHP library, managed by Composer, for RFC-2445 RRule date recursion - written by [Zander Baldwin](https://github.com/mynameiszanders), adapted from the original version by [Tom Planer](https://github.com/tplaner).

**Update** *Friday, 7<sup>th</sup> March 2014*:<br />
The commercial project that this library is being used for is coming to an end; up until now the library was extremely biased towards working at the minimal requirements for that one project.<br />
Version 2 is now being written which will fix all known bugs (there are a **lot** of bugs), comprehensive unit tests and complete documentation. Expect this to be released around June/July.

---

##Original README (by [Tom Planer](https://github.com/tplaner))

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
