# Private Repo Example

This silex application demonstrates how to use private repositories as composer dependencies for Google App Engine flexible environment.

Composer will use a local `auth.json` file (same folder as `composer.json`) and use any auth tokens configured within. See [`auth.json.example`](auth.json.example).

The `composer.json` adds repositories to use when resolving dependencies.
