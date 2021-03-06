# Requirements
## Docker

TODO

# Install
## Env variables
Create the following files in the [infra directory](./infra) of this project.

```dotenv
# blackfire.env
BLACKFIRE_SERVER_ID=
BLACKFIRE_SERVER_TOKEN=
BLACKFIRE_CLIENT_ID=
BLACKFIRE_CLIENT_TOKEN=
```
[See Blackfire chapter for more](#blackfire)

```dotenv
# php.env
PHP_XDEBUG_ENABLED=1
APP_SWITCH_ENV=true

# Host = docker network inspect "${NETWORK_NAME:?}" -f "{{range .IPAM.Config }}{{ .Gateway }}{{end}}"
# Port = ARBITRARY
XDEBUG_CONFIG=remote_host=172.28.0.4 remote_port=30093
```

## Compiling the compose files
### Local
Create the `.env` file in the [infra directory](./infra) while making sure the values are the correct ones:

```bash
$ cat > ./.env << EOF
# https://docs.docker.com/compose/reference/envvars/
COMPOSE_PROJECT_NAME=app-cli

# Path from the root of this project to the root of the symfony project
APP_PROJECT_PATH=../

# Fix permissions issues
APP_USER_ID=$(id -u)
EOF
```

## Running the containers
### Local
```bash
$ docker-compose pull; \
  docker-compose up -d --remove-orphans --build
```

The `pull` will show some errors. As long as it concerns `php` or `tools` it is fine. (mix of `build` + `image` in docker-compose).

## Setting up the project locally
### Use the `functions.sh`
This file is to ease the use of containers without thinking about it when developing.

```bash
$ source ./functions.sh
```

### Setup autoloading
```bash
$ composer install
```

### Running tests
```bash
$ unit-tests
```

### Usage
```bash
$ csv2json
$ # example:
$ csv2json --desc ./fixtures/simple.ini --pretty ./fixtures/simple.csv
```

## Blackfire
Connect to [Blackfire docker integration documentation](https://blackfire.io/docs/integrations/docker/index) and copy paste your environments variables in `blackfire.env` file above.

:warning: For now Blackfire could not work with Xdebug enabled. Set `ENABLE_XDEBUG` to `false` if you want to profile with Blackfire.

# TODO
 - [ ] Do a custom parsing of cli arguments using `$argv`
 - [ ] Create a `App\Cli\Csv2Json` class with private methods such as: ['parseArgv', 'handleError', 'showUsage', ...]
 - [ ] Create a parser for the 'schema' file instead of using 'ini' parser
 - [ ] Create more tests
 - [ ] Split `App\Parser\Csv\Parser` in several methods
 - [ ] Redo the `InputOption` (use short syntax as alias to long ones and vice versa; )
 - [ ] Check that the aggregate field is in the fields filter list
 - [ ] Redo `App\Parser\Csv\Tools::guessDelimiter()` based on `App\Cli\InputOption::parseStringToArray()`
 - [ ] Check if files exists (realpath) AND are readable
 - [ ] Add output colors to the `Terminal` class
