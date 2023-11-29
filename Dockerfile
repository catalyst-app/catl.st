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

  COPY --from=config-gen nginx.conf /etc/nginx/conf.d/default.conf
