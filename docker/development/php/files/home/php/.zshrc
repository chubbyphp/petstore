source $HOME/.sharedrc

eval "$(fnm env --shell=zsh --use-on-cd)"

autoload colors && colors
setopt PROMPT_SUBST
export PROMPT='%{$fg_bold[green]%}%m%{$reset_color%} %{$fg_bold[white]%}%~%{$reset_color%} ($(git symbolic-ref --short HEAD 2> /dev/null)) %{$fg_bold[green]%}%n%{$reset_color%} % '

# history
setopt hist_ignore_dups
setopt hist_ignore_space
setopt hist_expire_dups_first
setopt inc_append_history

export HISTFILE=~/.zsh_history

if [ -f "$HOME/.zsh_docker" ]; then
    source $HOME/.zsh_docker
fi
