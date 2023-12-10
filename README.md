# catl.st - Catalyst's URL shortener

This is a simple project which is responsible for handling redirects for the now-defunct https://github.com/catalyst-app/Catlayst.

## Workflow

A script `generate.ts` generates an NGINX configuration based on `config.yaml`. This configuration is then used by NGINX to handle redirects.

## Building and running

Since this project is open-source, you are welcome to fork this project and run it locally for yourself.  However, please be aware that many things are hardcoded and we provide no warranty nor maintenance.

We provide a Docker image for running the project.  To build it, use the following:

```sh
docker build -t catl.st .
```

You can run it with:
```sh
docker run -p 8080:8080 catl.st
```

This container exposes port `8080` which can be mapped as desired.  Please note that no SSL or security is provided in this configuration; **you must use a reverse proxy** like Traefik to provide SSL and other important features.  To see a list of all needed SSL domains, run `bun generate.ts` and look at the top of the file.

There is a special hostname `internal-status` which is used for internal health checks.  This hostname is not exposed to the public and should be used only to check the health of the service.  It can be used by simulating a "Host" header, e.g. `curl http://catl-st-container:8080/status -H "Host: internal-status"`. `/status` returns a NGINX `stub_status` whereas `/ready` will return `ok` when the server is up and ready.

We provide two prebuilt packages:
- `ghcr.io/catalyst-app/catl.st:master`, everything, as abandoned
- `ghcr.io/catalyst-app/catl.st:limited`, a very limited version with few redirects left (from branch `limited`).
