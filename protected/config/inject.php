<?php

/**
 * Injects classes into different parts of the application with aliases
 *
 * FORMAT:
 *
 * 'alias' => array(
 * 		'class' => 'FullyQualifiedClassName',
 * 		'params' => array(param1,param2,param3,...)
 * ).
 *
 * To access the class from the relevant part, call it with $this->{alias}.
 *
 * Example:
 *
 * 'ao' => array(
 * 		'class' => 'ArrayObject',
 * 		'params' => array(
 * 			array('This', 'is', 'an', 'example','parameter', 'of', 'an', 'array')
 * 		),
 * ),
 *
 * The above will inject an ArrayObject model into any part with a single constructor
 * parameter as the passed array.
 *
 * To call it, this would be $this->ao;
 * To get the number of elements in the object, call $this->ao->count();
 */
return array(
	'all' => array(
	/*
	 * Inject classes into views, controllers, and services
	 */
	),
	'controllers' => array(
	/*
	 * Inject classes into controllers only
	 */
	),
	'services' => array(
	/**
	 * Inject classes into services only
	 */
	),
	'views' => array(
	/*
	 * Inject classes into views only
	 */
	),
);
