# Dev stage
FROM node:20-slim AS dev
RUN apt-get update && apt-get install -y openssl
WORKDIR /app
RUN corepack enable
#COPY .npmrc package.json pnpm-lock.yaml ./
#RUN pnpm install --frozen-lockfile
COPY . ./
EXPOSE 3000
CMD ["sh", "-c", "pnpm install --frozen-lockfile \
  && pnpm run dev"]

# Build stage
FROM node:20-alpine AS builder
RUN apk add --no-cache openssl
WORKDIR /app
RUN corepack enable
COPY .npmrc package.json pnpm-lock.yaml ./
RUN pnpm install --frozen-lockfile
COPY . ./
RUN pnpm run build

# Production stage
FROM node:20-alpine AS production
WORKDIR /app
COPY --from=builder /app/.output ./
COPY --from=builder /app/package*.json ./
EXPOSE 80
CMD ["node", "/app/server/index.mjs"]
