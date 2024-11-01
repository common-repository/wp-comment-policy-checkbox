## Commit Message Guidelines

Follow [Conventional Commits](https://www.conventionalcommits.org/)
Close or fix issue on commit message.

## Planning new version

Create issues in GitLab.

Update GitLab issues as they develop.

Update README as development: features, changelog...

Update main plugin file with new version.

Update 'thanks':

When launching a new version, create a tag in git with the name of the version:
  - Tag name: x.x.x
  - Create from: develop
  - Message: = to README changelog

And new release:
  - Tag name: x.x.x
  - Release notes: to README changelog

### Test with lastest major release

Test the plugin features with the lastes major Wordpress release.

Do not need to push a new version, just update the README and edit the value of `Tested up to:` to the latest version of WordPress.


### On SVN

https://developer.wordpress.org/plugins/wordpress-org/how-to-use-subversion/#editing-existing-files
