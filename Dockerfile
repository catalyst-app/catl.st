# generate the nginx configuration
FROM oven/bun:canary AS config-gen

  WORKDIR /

  COPY package.json .
  COPY bun.lockb .
  COPY tsconfig.json .

  RUN bun install --frozen-lockfile

  COPY config.yaml .
  COPY generate.ts .

  RUN bun ./generate.ts > /nginx.conf
  RUN cat nginx.conf

FROM nginx:alpine-slim AS final

  RUN sed -i '/http {/a proxy_hide_header X-Powered-By;' /etc/nginx/nginx.conf
  RUN sed -i '/http {/a server_tokens off;' /etc/nginx/nginx.conf

  COPY --from=config-gen nginx.conf /etc/nginx/conf.d/default.conf
