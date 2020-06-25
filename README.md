## Description

![](files/demo.gif)

This project deploys PHP application with help of Nginx and PHP-FPM running in separate containers inside the same pod on a Kubernetes cluster. Deployment of PHP application as well as all of it's dependencies is done with Helm3 and Helmfile to simplify the deployment of multiple charts.

This was tested using Minikube installation on macOS.

## About application

Its a very simple PHP application with couple of endpoints

```
/ - Expects n query parameter and if it receives it, calculates n*n and returns the result
/blacklisted - Blacklists current user IP, sends an Email
/status - Status check for readinessProbe and livenessProbe
/unblock - Unblocks user IP. I added it only to simplify testing and for use by docker-compose tests to unblock the IP after testing everything. Theoretically redundant if we have a separate docker-compose only for testing
```

Checking if user is blacklisted and connection to database is separated into separate failes in `lib/check.php` and `lib.db.php` accordingly.

## Tools used
- [Minikube](https://github.com/kubernetes/minikube)
- [Helm3](https://github.com/helm/helm)
- [Helmfile](https://github.com/roboll/helmfile)
- [Helm Secrets](https://github.com/zendesk/helm-secrets)
- [Docker Compose](https://github.com/docker/compose)
- [Nginx](https://nginx.org/x)
- [PHP-FPM docker image by Bitnami](https://github.com/bitnami/bitnami-docker-php-fpm)
- [PostgreSQL](https://www.postgresql.org/) (in Minikube we use [Bitnami Helm chart](https://github.com/bitnami/charts/tree/master/bitnami/postgresql) as well)

## Project structure

```
project
│───app - Contains PHP application source code
│   └───lib
│           check.php - Script to check whethever user is blacklisted or not
│           db.php - Connection to database
│       .dockerignore - List of stuff we don't want│in container
│       blacklisted.php - Script which blacklists user IP
│       Dockerfile - Everything needed to make this PHP application work with PHP-FPM
│       index.php - Performs multiplying of n query parameter
│       status.php - Status endpoint which is accessible even after blacklisting, used for liveness and readiness probe
│       unblock.php - Easy way to unblock user (Since in Docker compose we run tests with same database and we don't want to end up with blocked user)
└────charts - Contains all Helm charts
│    └──helm_vars - Contains all necessary information for Helm Secrets to function
│    │      .sops.yaml - Our PGP key fingerprint which is used for encrypting secrets
│    │      secrets.yaml - Encrypted secrets
│    └──phpapp - Helm chart for deploying our PHP app to Kubernetes cluster
│    │  └───templates - All templates we are deploying for our PHP app
│    │          _helpers.tmpl - Helm helper functions to be used in templates
│    │          configmap.yaml - Configmap with Nginx config which is needed for PHP FPM setup as well as some additional custom configuration
│    │          deployment.yaml - Actual deployment of PHP app and Nginx container
│    │          service.yaml - Service with help of which you will be able to access the application
│    │       Chart.yaml - Chart metadata
│    │       values.yaml.gotmpl - Configurable application values, generally you don't need to change anything here for it to work
│    └──postgresql - Contains values overriding default values of Bitnaming PostgreSQL Helm Chart
│           values.yaml.gotmpl - List of values which we override for this Helm Chart
└───tests - Contains integration tests for the application, which are run with docker-compose
│       Dockerfile - Since we run this in docker compose, we need to have the tests as Docker container
│       run-tests.py - Quick and ugly Python script to perform integration tests
└───files - Files which didn't fit anywhere else
        default.conf - Nginx config which Docker compose uses
    docker-compose.yml - Docker compose for local development which reproduces Helm charts pretty closely
    helmfile.yaml - Helmfile which eases the deployment of multiple charts in different namespaces
```

### Prerequisites

Instructions below are written for macOS however it should be easily adaptable for any Linux distribution as well.

We assume that you have Homebrew and Docker installed on your machine so we are not going to describe process of installing Docker and Homebrew here.

1. Make sure you have minikube installed along with hyperkit (if you want to use Hyperkit as a driver)

`brew install minikube hyperkit`

2. Install helm3 and helmfile as well as gnu-getopt to avoid some warnings

`brew install helm helmfile gnu-getopt`

3. Install helm-secrets

`helm plugin install https://github.com/futuresimple/helm-secrets`

4. Install gpg-suite. This is not a required step, however if you are importing existing key it's much easier to have it installed.

`brew cask install gpg-suite`

## Usage

#### Local development

```
$ docker-compose up
```

Integration tests will automatically run on the environment upon docker-compose up.

Application will then be accessible by following URL: http://localhost:8080/

#### Minikube

```
$ minikube start
$ helmfile sync
$ minikube tunnel
```

Please note that it might take a little bit of time for application to start, you can observe status by executing
```
kubectl get pods -n phpapp --watch
```

Application will then be accessible by following URL:
http://phpapp.phpapp.svc.cluster.local/


***Important: I use Helm Secrets in this repo which uses my PGP key for decrypting encrypted values, most likely you will not be able to deploy as is without using the same key as I did.***
