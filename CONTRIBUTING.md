Contribution guide
==================

Melody is an open source project driven by [SensioLabs](http://sensiolabs.com).

Reporting a bug
---------------

Whenever you find a bug in Melody, we kindly ask you to report it. Before
reporting a bug, please verify that it has not already been reported by someone
else in the
[existing bugs](https://github.com/sensiolabs/melody/issues?q=is%3Aopen+is%3Aissue).

If you're the first person to hit this problem,
[create an issue](https://github.com/sensiolabs/melody/issues/new) and provide
us maximum information about your system: OS, PHP version, composer version.

Release managers
----------------

Release managers are allowed to manage the Github repository and do the
following:

* They close or re-open issues;
* They merge pull-requests into master;
* They manage labels and milestones on issues;

Github labels
-------------

* **bug** ([see all](https://github.com/sensiolabs/melody/issues?q=label%3Abug)):
an issue or a PR for a feature that doesn't work

    If the issue about a usage problem, it will probably be flagged as **bug**.

* **wip** ([see all](https://github.com/sensiolabs/melody/issues?q=label%3Awip)):
long-running PR

    This label allows other people to review the code before merging the code.

    It's very useful for long-running PR, and to avoid merging an unfinished
    feature.

    If you make a pull-request and plan to make it long-running, please add
    [WIP] also in the title, so a release manager can flag it as wip and remove
    it from your title.

* **discuss** ([see all](https://github.com/sensiolabs/melody/issues?q=label%3Adiscuss)):
an issue about a feature suggestion or changing an existing feature

    An issue suggesting a new feature will be flagged as **discuss**.

* **refactor** ([see all](https://github.com/sensiolabs/melody/issues?q=label%3Arefactor)):
an issue or a PR that is about changing the implementation of an existing
feature;

    A refactor usually have no functional benefit, but can be required by a
    **new feature**, or any other issue;

* **new feature** ([see all](https://github.com/sensiolabs/melody/issues?q=label%3A"new+feature")):
a feature that is decided to be done

    If there is no milestone attached, it's an unplanned new feature.

* **duplicate** ([see all](https://github.com/sensiolabs/melody/issues?q=label%3Aduplicate)):
an issue or PR that is a duplicate of another.

    The original issue should be referenced inside duplicated issue with a comment or in the description:

    > Duplicate of #32

* **wontfix** ([see all](https://github.com/sensiolabs/melody/issues?q=label%3Awontfix)):
an issue that cannot be fixed because of a limitation or a decision;

    The limitation or reasons of the decision should be exposed in the issue.

Labels on the repository are managed by release managers. Users can suggest tags
by adding pseudo-labels in title:

> [bug][new-feature] It's not a bug, it's a new feature!

Release managers are allowed to edit the user title to remove those labels and
add them as labels.

Acceptance criteria
-------------------

* Every new feature should be documented;
* Every new feature should be tested;
