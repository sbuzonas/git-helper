git-helper
==========
A simple library and shell script intended to be a set of subcommands for git written in PHP.

## Configuration

The branches used are stored in the following git config variables (default values shown).  Setting globals will change the defaults for all repositories.

    githelper.branch.master=master
    githelper.branch.release=release
    githelper.branch.hotfix=hotfix
    githelper.branch.develop=develop

## SubCommands

#### Config

The config subcommand walks the user through setting the expected configuration variables for the repository.

#### Feature

The feature subcommand handles branch creation, publishing, and merging for feature branches in the workflow.  This is not intended to be used for long term development branches due to the merge method being based on a 'pull --rebase' of the develop branch.

###### Create

Creates a new branch for feature to be developed.  This is generally based on a codename or ticket number.  The name of the feature chosen will be used as a commit message prefix if the repository is configured to squash on publish.

###### Publish

Publishes the current state of the feature branch to the develop branch.  This is done by first doing a pull --rebase of the develop branch.  The branch is then switched to the develop branch and the changes are merged with --ff-only.  This can be configured to be squashed on merge done by the config subcommand.  If squash is configured the user can opt to prefix the merge message with the feature name in '[]' brackets also done in the config subcommand. The user will be prompted for a commit message if squashed.

##### Close

Closes the feature branch.