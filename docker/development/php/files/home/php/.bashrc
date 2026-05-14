source $HOME/.sharedrc

eval "$(fnm env --shell=bash --use-on-cd)"

export CLICOLOR='true'
export PS1='\[\e[1;32m\]\h\[\e[0m\] \[\e[1;37m\]\w\[\e[0m\] \[\e[3m\]($(git rev-parse --abbrev-ref HEAD 2> /dev/null))\[\e[23m\] \[\e[1;32m\]\u\[\e[0m\] '

# history
export HISTCONTROL='ignoreboth:erasedups'
export HISTFILE=~/.bash_history
export PROMPT_COMMAND='history -a'

if [ -f "$HOME/.bash_docker" ]; then
    source $HOME/.bash_docker
fi
