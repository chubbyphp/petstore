#!/bin/sh
# Creates every file/dir that docker-compose.yml bind-mounts, so docker does not
# turn missing files into directories. Existing files are never overwritten.
set -eu

# bash
touch ~/.bash_docker
touch ~/.bash_history

# zsh
touch ~/.zsh_docker
touch ~/.zsh_history

# git
touch ~/.gitconfig
touch ~/.gitignore

# ssh
mkdir -p ~/.ssh
touch ~/.ssh/github.pub

# npm
touch ~/.npmrc

# claude
if [ ! -f ~/.claude.json ]; then
    cat > ~/.claude.json <<'EOF'
{}
EOF
fi

mkdir -p ~/.claude

if [ ! -f ~/.claude/.credentials.json ]; then
    cat > ~/.claude/.credentials.json <<'EOF'
{}
EOF
fi

if [ ! -f ~/.claude/settings.json ]; then
    cat > ~/.claude/settings.json <<'EOF'
{
    "fileCheckpointingEnabled": false,
    "permissions": {
        "defaultMode": "bypassPermissions"
    },
    "skipDangerousModePermissionPrompt": true,
    "spinnerTipsEnabled": false,
    "switchModelsOnFlag": false,
    "theme": "auto"
}
EOF
fi

chmod 600 \
    ~/.claude/.credentials.json \
    ~/.claude/settings.json

# codex
mkdir -p ~/.codex

if [ ! -f ~/.codex/auth.json ]; then
    cat > ~/.codex/auth.json <<'EOF'
{}
EOF
fi

if [ ! -f ~/.codex/config.toml ]; then
    cat > ~/.codex/config.toml <<'EOF'
approval_policy = "never"
sandbox_mode = "danger-full-access"

[notice]
hide_full_access_warning = true
EOF
fi

chmod 600 \
    ~/.codex/auth.json \
    ~/.codex/config.toml

# opencode
mkdir -p ~/.config/opencode ~/.local/share/opencode

if [ ! -f ~/.config/opencode/opencode.jsonc ]; then
    cat > ~/.config/opencode/opencode.jsonc <<'EOF'
{
    "$schema": "https://opencode.ai/config.json",
    "permission": {
        "*": "allow"
    }
}
EOF
fi

if [ ! -f ~/.config/opencode/tui.json ]; then
    cat > ~/.config/opencode/tui.json <<'EOF'
{
    "$schema": "https://opencode.ai/tui.json",
    "theme": "system",
    "tips": false
}
EOF
fi

if [ ! -f ~/.local/share/opencode/auth.json ]; then
    cat > ~/.local/share/opencode/auth.json <<'EOF'
{}
EOF
fi

chmod 600 \
    ~/.config/opencode/opencode.jsonc \
    ~/.config/opencode/tui.json \
    ~/.local/share/opencode/auth.json

# pi
mkdir -p ~/.pi/agent

if [ ! -f ~/.pi/agent/auth.json ]; then
    cat > ~/.pi/agent/auth.json <<'EOF'
{}
EOF
fi

chmod 600 ~/.pi/agent/auth.json
