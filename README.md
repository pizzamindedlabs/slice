# jadob/jadob

![Packagist](https://img.shields.io/packagist/l/jadob/jadob.svg)

## DISCLAIMER

There is no production-ready version at this time. Use at your own risk.

## Introduction

Jadob is a PHP7 application framework, based on Symfony components. It comes with the most important
stuff needed to start developing your PHP project.
Jadob uses Twig for templating and Doctrine ORM/DBAL/ODM for SQL and MongoDB databases support.


## Requirements

- PHP 7.4.0 or higher

## Things that need to be done before `1.0.0` release

### In general

* [ ]  Allow to work in multiple dispatch cycles (e.g. in ReactPHP, php-pm, or swoole)
* [x]  30%+ Code Coverage
* [x]  40%+ Code Coverage
* [ ]  50%+ Code Coverage
* [ ]  60%+ Code Coverage
* [ ]  70%+ Code Coverage
* [ ]  80%+ Code Coverage
* [ ]  90%+ Code Coverage
* [ ]  95%+ Code Coverage
* [ ] Custom CSRF Extension to Forms 
* [ ] Fluent configuration objects for each provider


### Allow to work in multiple dispatch cycles

- [ ] Drop session out of container
- [ ] Any service that rely on session from container, should be changed and receive them from BeforeControllerEvent

### URL

* [ ] Immutable Url object
 
### Config

* [ ] Support for YAML files
* [ ] Fluent ConfigNode object

### EventSourcing

* [ ] Generating events and testcases classes from php/yaml config 

## Container
- aliasing
- better autowiring

## Testing

Jadob uses PHPUnit for unit test, Psalm for static analysis and Infection for Mutation testing.


## Getting Started

@TODO

## License 

MIT


