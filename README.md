git-helper
==========
A simple library and shell script intended to be a set of subcommands for git written in PHP.

## Configuration

The branches used are stored in the following git config variables (default values shown).  Setting globals will change the defaults for all repositories.

    githelper.branch.master = master
    githelper.branch.release = release
    githelper.branch.hotfix = hotfix
    githelper.branch.develop = develop

## SubCommands

### Config

The config subcommand walks the user through setting the expected configuration variables for the repository.

### Feature

The feature subcommand handles branch creation, publishing, and merging for feature branches in the workflow.  This is not intended to be used for long term development branches due to the merge method being based on a 'pull --rebase' of the develop branch.

##### Create

Creates a new branch for feature to be developed.  This is generally based on a codename or ticket number.  The name of the feature chosen will be used as a commit message prefix if the repository is configured to squash on publish.

##### Publish

Publishes the current state of the feature branch to the develop branch.  This is done by first doing a pull --rebase of the develop branch.  The branch is then switched to the develop branch and the changes are merged with --ff-only.  This can be configured to be squashed on merge done by the config subcommand.  If squash is configured the user can opt to prefix the merge message with the feature name in '[]' brackets also done in the config subcommand. The user will be prompted for a commit message if squashed.

##### Close

Closes the feature branch.

### Sprint

The sprint subcommand is for agile workflows that track features in development to a specific development cycle to then be merged into a master development branch.  By itself will display the current sprint in use.  Calling 'sprint set' will change the current sprint and update the develop branch accordingly.  The user will be prompted to confirm the switch if features are still in development.

### Status

The status subcommand is a skeleton subcommand that informs the user if the current branch is clean or not.

### Whoami

The whoami subcommand reads the 'user.name' and 'user.email' configuration variables to allow the user to see the identity used by git for the commits.

## Installation

A basic installation requires obtaining the sources and adding a symlink to the git-helper script somewhere in the user's path.

    git clone git://github.com/slbmeh/git-helper.git
    ln -s $(pwd)/git-helper/git-helper /usr/bin/

## Hints

I have configured the following global aliases:

    alias.close=helper feature close
    alias.create=helper feature create
    alias.publish=helper feature publish
    alias.sprint=helper sprint

This is a simple configuration that allows me to set my sprint with 'git sprint set sprint-1'.  I can then work on an issue from my bug tracker with 'git create BUG-1234' and publish it to sprint-1 with 'git publish BUG-1234'.  Once the issue is resolved for the feature I can run 'git close BUG-1234'.

## Rationale

Some individuals are adamant against the fast-forward default nature of git.  I personally believe, when working in teams larger than two individuals on anything non-trivial, the central repository should always be fast-forward only.  This prevents rewriting history and losing commits.

Additionally, the commit frequency varies from developer to developer.  It is not necessary to track every single commit.  Often times there are commits reverting changes a few commits back.  'Software development archaeologists' will disagree with my methodologies, however, the master branch should only track what was merged, when it was merged, and why it was merged.

Maintaining a clean history of merges based on simply features and hotfixes will significantly reduce the amount of time required to track down when a bug was introduced.

In a structured agile development process feature branches should be designated for each feature.  Sprint branches should track merges of new features, and master development branches should track sprints and hotfixes being merged in.  The branches should remain until they pass the QA/UX testing required to be placed into production and potentially longer dependant upon the team's ability to pinpoint a bug introduced using SCM history.
