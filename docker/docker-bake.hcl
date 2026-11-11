variable "IMAGE_NAME" {
  default = "fkint/playground-php-app"
}

# Shared base: each CI job writes its own cache scope (avoids concurrent writes
# to one scope) but reads both, since the stages overlap.
target "_base" {
  context    = "."
  dockerfile = "docker/Dockerfile"
  output     = ["type=docker"]
  cache-from = ["type=gha,scope=docker-test", "type=gha,scope=docker-prod"]
}

target "app-test" {
  inherits = ["_base"]
  target   = "app-test"
  tags     = ["${IMAGE_NAME}:app-test"]
  cache-to = ["type=gha,scope=docker-test,mode=max"]
}

target "app-prod" {
  inherits = ["_base"]
  target   = "app-prod"
  tags     = ["${IMAGE_NAME}:app-prod"]
  cache-to = ["type=gha,scope=docker-prod,mode=max"]
}

target "nginx-prod" {
  inherits = ["_base"]
  target   = "nginx-prod"
  tags     = ["${IMAGE_NAME}:nginx-prod"]
  cache-to = ["type=gha,scope=docker-prod,mode=max"]
}

group "test" {
  targets = ["app-test"]
}

group "prod" {
  targets = ["app-prod", "nginx-prod"]
}
