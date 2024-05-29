<?php

/*
|--------------------------------------------------------------------------
| Cache Themes
|--------------------------------------------------------------------------
|
| igaster/laravel-theme reads themes settings from json files inside
| each theme's folder. We will cache them in a single php file to
| avoid searching the filesystem for each Request. You can use
| 'theme:refresh-cache' to rebuild cache, or set config/themes.php
| 'cache' setting to false to disable completely
|
*/

return array (
  0 => 
  array (
    'name' => 'classic',
    'asset-path' => 'themes/classic',
    'extends' => 'default',
    'wideLayoutPaddingX' => 'px-4 lg:px-10',
    'defaultVariations' => 
    array (
      'card' => 
      array (
        'variant' => 'outline-shadow',
        'size' => 'md',
        'roundness' => 'none',
      ),
      'table' => 
      array (
        'variant' => 'outline-shadow',
        'size' => 'md',
        'roundness' => 'none',
      ),
    ),
    'dashboard' => 
    array (
      'googleFonts' => 
      array (
        'Poppins' => 
        array (
          0 => '400',
          1 => '500',
          2 => '600',
          3 => '700',
        ),
      ),
    ),
    'views-path' => 'classic',
  ),
  1 => 
  array (
    'name' => 'creative',
    'asset-path' => 'themes/creative',
    'extends' => 'default',
    'dashboard' => 
    array (
      'googleFonts' => 
      array (
        'Inter' => 
        array (
          0 => '400',
          1 => '500',
          2 => '600',
        ),
        'Golos+Text' => 
        array (
          0 => '400',
          1 => '500',
          2 => '600',
          3 => '700',
        ),
      ),
    ),
    'landingPage' => 
    array (
      'googleFonts' => 
      array (
        'Inter' => 
        array (
          0 => '400',
          1 => '600',
          2 => '700',
          3 => '900',
        ),
        'Golos+Text' => 
        array (
          0 => '400',
          1 => '500',
          2 => '600',
          3 => '700',
        ),
      ),
    ),
    'views-path' => 'creative',
  ),
  2 => 
  array (
    'name' => 'default',
    'asset-path' => 'themes/default',
    'extends' => '',
    'wideLayoutPaddingX' => '',
    'defaultVariations' => 
    array (
      'button' => 
      array (
        'variant' => 'primary',
        'hoverVariant' => 'none',
        'size' => 'md',
      ),
      'card' => 
      array (
        'variant' => 'outline',
        'size' => 'md',
        'roundness' => 'default',
      ),
      'table' => 
      array (
        'variant' => 'outline',
      ),
    ),
    'dashboard' => 
    array (
      'googleFonts' => 
      array (
        'Inter' => 
        array (
          0 => '400',
          1 => '500',
          2 => '600',
        ),
        'Golos+Text' => 
        array (
          0 => '400',
          1 => '500',
          2 => '600',
          3 => '700',
        ),
      ),
    ),
    'landingPage' => 
    array (
      'googleFonts' => 
      array (
        'Golos+Text' => 
        array (
          0 => '400',
          1 => '500',
          2 => '600',
          3 => '700',
        ),
        'Onest' => 
        array (
          0 => '400',
          1 => '500',
          2 => '700',
        ),
      ),
    ),
    'views-path' => 'default',
  ),
  3 => 
  array (
    'name' => 'neura',
    'asset-path' => 'themes/neura',
    'extends' => 'default',
    'wideLayoutPaddingX' => 'px-4 lg:px-10',
    'dashboard' => 
    array (
      'googleFonts' => 
      array (
        'Inter' => 
        array (
          0 => '400',
          1 => '500',
          2 => '600',
        ),
        'Poppins' => 
        array (
          0 => '600',
        ),
        'Lato' => 
        array (
          0 => '400',
          1 => '500',
        ),
      ),
    ),
    'views-path' => 'neura',
  ),
  4 => 
  array (
    'name' => 'reiblackbook',
    'asset-path' => 'themes/reiblackbook',
    'extends' => 'default',
    'views-path' => 'reiblackbook',
  ),
  5 => 
  array (
    'name' => 'sleek',
    'asset-path' => 'themes/sleek',
    'extends' => 'default',
    'wideLayoutPaddingX' => 'px-4 lg:px-10',
    'defaultVariations' => 
    array (
      'card' => 
      array (
        'variant' => 'shadow',
        'roundness' => '4xl',
      ),
      'table' => 
      array (
        'variant' => 'shadow',
      ),
    ),
    'views-path' => 'sleek',
  ),
);