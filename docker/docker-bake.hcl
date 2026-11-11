variable "IMAGE_NAME" {
  default = "fkint/playground-php-app"
}

# Set by CI (empty for local builds: no version files are created).
variable "VERSION_SHA1" {
  default = ""
}
variable "VERSION_GITHUB_LINK" {
  default = ""
}
variable "VERSION_TIMESTAMP" {
  default = ""
}

# Shared base: each CI job writes its own cache scope (avoids concurrent writes
# to one scope) but reads both, since the stages overlap.
target "_base" {
  context    = "."
  dockerfile = "docker/Dockerfile"
  output     = ["type=docker"]
  args = {
    VERSION_SHA1        = VERSION_SHA1
    VERSION_GITHUB_LINK = VERSION_GITHUB_LINK
    VERSION_TIMESTAMP   = VERSION_TIMESTAMP
  }
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
