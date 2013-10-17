<?php

    use \Sami\Sami;
    use \Sami\Version\GitVersionCollection;
    use \Symfony\Component\Finder\Finder;

    $dir = dirname(__FILE__);

    // Define where we should look for source files, and where we should not look for source files.
    $iterator = Finder::create()
        ->files()
        ->name('*.php')
        ->in($dir . '/../src');

    // Setup the basic configuration settings for Sami.
    $configuration = array(
        'title'     => 'Documentation: When (Date Recursion Library)',
        'build_dir' => $dir,
        'cache_dir' => $dir . '/.cache',
    );

    // If this library has been cloned from the repository, then we can build documentation for the development branch,
    // the current release, and all other releases (tags).
    if(is_dir($dir . '/../.git')) {
        $configuration['versions'] = GitVersionCollection::create($dir . '/..')
            ->addFromTags()
            ->add('develop', 'Development Branch')
            ->add('master', 'Current Release');
        $configuration['build_dir'] .= '/%version%';
        $configuration['cache_dir'] .= '/%version%';
    }

    // Create a new instance of the Sami Docs Generator and return it, after passing in some configuration options of
    // course.
    return new Sami($iterator, $configuration);
