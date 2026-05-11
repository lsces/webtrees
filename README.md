![Latest version](https://img.shields.io/github/v/release/fisharebest/webtrees?sort=semver)
![Licence](https://img.shields.io/github/license/fisharebest/webtrees)
[![Unit tests](https://github.com/fisharebest/webtrees/actions/workflows/phpunit.yaml/badge.svg)](https://github.com/fisharebest/webtrees/actions/workflows/phpunit.yaml)
[![codecov](https://codecov.io/gh/fisharebest/webtrees/branch/main/graph/badge.svg?token=zREQBP4GBs)](https://codecov.io/gh/fisharebest/webtrees)
[![Translation status](https://translate.webtrees.net/widgets/webtrees/-/webtrees-22/svg-badge.svg)](https://weblate.iet.open.ac.uk/projects/webtrees/webtrees-22)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/fisharebest/webtrees/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/fisharebest/webtrees/?branch=main)
[![Code Climate](https://codeclimate.com/github/fisharebest/webtrees/badges/gpa.svg)](https://codeclimate.com/github/fisharebest/webtrees)
[![StyleCI](https://github.styleci.io/repos/11836349/shield?branch=main)](https://github.styleci.io/repos/11836349?branch=main)
# webtrees - online collaborative genealogy

## Source information

https://github.com/fisharebest/webtrees/blob/main/README.md

## Firebird Fork

This fork is being used to develope both the firebird-support driver for laravel and install webtrees to run with a firebird database.
I have been working through a lot of new stuff, not the least getting my head around the minefield of illuminate, but I am finally getting this under control.

The basics that I've had to overcome are mainly down to PDO, such as the fact that firebird does not support lastInsertId and some other PDO assumptions. I've wrapped insert so that it uses 'RETURNING' to emulate that but need a little more work so that it can identify just which field it should be returning.
The main problem is with upper and lower case names in the database. Firebird works best just defaulting to upper case and only wrapping reserved words, but PHP expects the keys to be lower case. I have emulated what happens in ADOdb and sorted the returns to be lower case, but webtrees does use some upper case keys and I'm not sure if that is a problem.
The problem of indexes that firebird can reuse so objects to the addition of 'duplicates' is a little iritating, but can be managed.

## TODO

<del>Currently while pending changes are being listed, they do not seem to be shown in the results?</del>
Actually this is now working fine, one of the other changes seems to have retored it.

The pending changes accept button was not working. This has been address and was down to the places delete function. <del>Need to override the mysql version with one more like SQLite, but at some point tidying this further to use WHERE NOT EXISTS () should tidy things further and can be done in the driver.</del>
Still working on the delete function as it needs a little more help than initially though, but WHERE NOT EXISTS () needs a different selectSQL to that used by join. Working on migrating the join into the correct extra where so that the main body of delete does not need the 'IN' field.

Gedcom Export IS working, but I need to sort the hack to handle getting the idents in numerical order using CHAR_LENGTH in place of LENGTH

Migration45.php was giving a problem, but this was down to the check if a column exists which needed upper case field name to match the systems tables.  

There are a lot of areas I have not yet exercised, but I'm happy that for people not logged in, it is working so am planning to move my sqlite powered live site over to firebird as a next step before doing more work on the logged in type stuff.

