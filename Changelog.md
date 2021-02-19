# Changelog

### Menu Component For Laravel

## 1.X

### 1.8.2
###### 2021-02-19

- Fixed method annotations on the `Menu` facade 🤦

### 1.8.1
###### 2021-02-19

- Tiny internal polish release
- Added method annotations to `Menu` facade
- Switch over to PSR-12 internally

### 1.8.0
###### 2020-12-07

- Added PHP 8 support
- Dropped PHP 7.1 support (this time really :) (see 1.5.0))
- Dropped PHP 7.2 support
- Dropped Laravel 5 support
- Changed CI from Travis to Github Actions

### 1.7.0
###### 2020-09-13

- Added Laravel 8 Support

### 1.6.2
###### 2020-03-15

- Fixed URL wildcard matching bug when the wildcard wasn't placed right after a slash

### 1.6.1
###### 2020-03-15

- Fixed `hasActiveChild` and `actives` methods so that it works even if `active_element` config is set to "link"

### 1.6.0
###### 2020-03-15

- Added `hasActiveChildren` and `actives` methods to ItemCollection class

### 1.5.0
###### 2020-03-13

- Added Laravel 7 Support
- Added PHP 7.4 Support
- Dropped PHP 7.1 Support

### 1.4.0
###### 2019-11-24

- Added Laravel 6.0 Support
- Dropped Laravel 5.4 Support

### 1.3.1
###### 2019-03-24

- Changed Item::checkActivation from protected to public

### 1.3.0
###### 2019-03-16

- Laravel 5.8 Support (doesn't work with earlier versions)
- PHP 7.0 support has been dropped
- PHP 7.3 supported
- v1.2.0 supports PHP 7.0-7.3 and Laravel 5.4-5.7

### 1.2.0
###### 2018-07-23

- Added Authorization support
- Minor composer fixes

### 1.1.0
###### 2018-06-29

- Added `Menu::removeItem()` method
- Proven PHP 7.2 and Laravel 5.6 support (actually worked with 1.0, but has never been tested)

### 1.0.0
###### 2017-10-23

- Initial release, codebase is 100% the 2017-07-31 status
