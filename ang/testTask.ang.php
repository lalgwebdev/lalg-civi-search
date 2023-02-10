<?php
// This file declares an Angular module which can be autoloaded
// in CiviCRM. See also:
// \https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_angularModules/n
return [
  'js' => [
    'ang/testTask.js',
    'ang/testTask/*.js',
    'ang/testTask/*/*.js',
  ],
  'css' => [
    'ang/testTask.css',
  ],
  'partials' => [
    'ang/testTask',
  ],
  'requires' => [
    'crmUi',
    'crmUtil',
    'ngRoute',
	'api4',
  ],
  'settings' => [],
];
