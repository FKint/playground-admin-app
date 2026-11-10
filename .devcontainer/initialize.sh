#!/bin/bash
# Runs on the host (initializeCommand) before the containers start. Writes .devcontainer/.env so compose
# mounts the checkout and the repository's shared .git at their host paths. A git worktree's .git file
# holds absolute host paths, so this keeps git working when the dev container is opened from a worktree.
set -euo pipefail
cd "$(dirname "$0")/.."
WORKSPACE=$(pwd)
GIT_COMMON_DIR=$(git rev-parse --path-format=absolute --git-common-dir 2>/dev/null || echo "$WORKSPACE/.git")
printf 'WORKSPACE=%s\nGIT_COMMON_DIR=%s\n' "$WORKSPACE" "$GIT_COMMON_DIR" > .devcontainer/.env
