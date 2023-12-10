import yaml from "yaml";
import groupBy from "object.groupby";

const OUR_DOMAIN = "catl.st";

const file = Bun.file("config.yaml");
const config = yaml.parse(await file.text()) as Record<string, string | Record<string, string>>;

function getSubdomainsByUrl(map: Record<string, string>): Record<string, string[]> {
  return groupBy(Object.keys(map), (key) => map[key])
}

const singleLevelSubdomains = Object.keys(config).filter(key => typeof config[key] === "string").reduce((acc, key) => ({...acc, [key]: config[key]}), {}) as Record<string, string>;
const doubleLevelSubdomains = Object.keys(config).filter(key => typeof config[key] !== "string").reduce((acc, key) => ({...acc, [key]: config[key]}), {}) as Record<string, Record<string, string>>;

const sans = [] as string[];
const baseRewrites = [] as string[];
const serverBlocks = [] as string[];

const singleLevelSubdomainsByUrl = getSubdomainsByUrl(singleLevelSubdomains);

for (const [url, subdomains] of Object.entries(singleLevelSubdomainsByUrl)) {
  for (const subdomain of subdomains) {
    baseRewrites.push(`rewrite ^/${subdomain}(/.*)$ ${url} break;`);
    baseRewrites.push(`rewrite ^/${subdomain}$ ${url} break;`);
  }

  serverBlocks.push(`
  server {
    listen [::]:8080;
    listen 8080;
    server_name ${subdomains.map(subdomain => `${subdomain}.${OUR_DOMAIN}`).join(" ")};

    return 301 ${url.replaceAll("$1", "$request_uri")};
  }
  `);

  if (url.indexOf("$") !== -1) {
    subdomains.forEach(subdomain => sans.push(`${subdomain}.${OUR_DOMAIN}`));
    serverBlocks.push(`
    server {
      listen [::]:8080;
      listen 8080;
      server_name ~^(.+)\\.(${subdomains.join("|")})\\.catl\\.st$;

      return 301 ${url.replaceAll("$1", "/$1")};
    }
    `);
  }
}


for (const [prefix, group] of Object.entries(doubleLevelSubdomains)) {
  sans.push(`${prefix}.${OUR_DOMAIN}`);

  for (const [subdomain, url] of Object.entries(group)) {
    if (subdomain === "$root") {
      continue;
    }

    baseRewrites.push(`rewrite ^/${prefix}/${subdomain}(/?.*)$ ${url} break;`);
  }

  baseRewrites.push(`rewrite ^/${prefix}(/.*)$ ${group["$root"]} break;`);
  baseRewrites.push(`rewrite ^/${prefix}$ ${group["$root"]} break;`);

  serverBlocks.push(`
  server {
    listen [::]:8080;
    listen 8080;
    server_name ${prefix}.${OUR_DOMAIN};

    ${Object.entries(group).filter(([subdomain]) => subdomain !== "$root").map(([subdomain, url]) => `rewrite ^/${subdomain}(/.*)$ ${url} break;`).join("\n")}
    ${Object.entries(group).filter(([subdomain]) => subdomain !== "$root").map(([subdomain, url]) => `rewrite ^/${subdomain}$ ${url} break;`).join("\n")}

    return 301 ${group["$root"].replaceAll("$1", "$request_uri")};
  }
  `);

  const innerByUrl = getSubdomainsByUrl(Object.keys(group).filter(key => key !== "$root").reduce((acc, key) => ({...acc, [key]: group[key]}), {}) as Record<string, string>);

  for (const [url, subdomains] of Object.entries(innerByUrl)) {
    // must do separate
    serverBlocks.push(`
    server {
      listen [::]:8080;
      listen 8080;
      server_name ${subdomains.map(subdomain => `${subdomain}.${prefix}.${OUR_DOMAIN}`).join(" ")};

      return 301 ${url.replaceAll("$1", "$request_uri")};
    }
    `);

    if (url.indexOf("$") !== -1) {
      subdomains.forEach(subdomain => sans.push(`${subdomain}.${prefix}.${OUR_DOMAIN}`));
      serverBlocks.push(`
      server {
        listen [::]:8080;
        listen 8080;
        server_name ~^(.+)\\.(${subdomains.join("|")})\\.${prefix}\\.catl\\.st$;

        return 301 ${url.replaceAll("$1", "/$1")};
      }
      `);
    }
  }
}

console.log(`
# Required SSL domains:
${sans.map(san => `#   ${san}`).join("\n")}

server {
  listen [::]:8080 default_server;
  listen 8080 default_server;
  server_name internal-status;

  location /status {
    stub_status on;
  }
}

server {
  listen [::]:8080 default_server;
  listen 8080 default_server;
  server_name _;

  location / {
    ${baseRewrites.join("\n")}
  }
}

${serverBlocks.join("")}
`);
