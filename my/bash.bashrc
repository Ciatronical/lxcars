# System-wide .bashrc file for interactive bash(1) shells.

# To enable the settings / commands in this file for login shells as well,
# this file has to be sourced in /etc/profile.

# If not running interactively, don't do anything
[ -z "$PS1" ] && return

# check the window size after each command and, if necessary,
# update the values of LINES and COLUMNS.
shopt -s checkwinsize

# set variable identifying the chroot you work in (used in the prompt below)
if [ -z "$debian_chroot" ] && [ -r /etc/debian_chroot ]; then
    debian_chroot=$(cat /etc/debian_chroot)
fi

# set a fancy prompt (non-color, overwrite the one in /etc/profile)
PS1='${debian_chroot:+($debian_chroot)}\u@\h:\w\$ '

# Commented out, don't overwrite xterm -T "title" -n "icontitle" by default.
# If this is an xterm set the title to user@host:dir
#case "$TERM" in
#xterm*|rxvt*)
#    PROMPT_COMMAND='echo -ne "\033]0;${USER}@${HOSTNAME}: ${PWD}\007"'
#    ;;
#*)
#    ;;
#esac

# enable bash completion in interactive shells
#if [ -f /etc/bash_completion ] && ! shopt -oq posix; then
#    . /etc/bash_completion
#fi

# if the command-not-found package is installed, use it
if [ -x /usr/lib/command-not-found -o -x /usr/share/command-not-found ]; then
        function command_not_found_handle {
                # check because c-n-f could've been removed in the meantime
                if [ -x /usr/lib/command-not-found ]; then
                   /usr/bin/python /usr/lib/command-not-found -- $1
                   return $?
                elif [ -x /usr/share/command-not-found ]; then
                   /usr/bin/python /usr/share/command-not-found -- $1
                   return $?
                else
                   return 127
                fi
        }
fi



alias mntid='echo Inter-Data wird gemountet && sshfs 62.141.45.200:/ /hosts/inter-data && cd /hosts/inter-data && ls -la'
alias umntid='cd /root && umount /hosts/inter-data && ls -la'
alias mntlxc='echo lxcars wird gemountet && sshfs 109.73.52.35:/ /hosts/lxcars && cd /hosts/lxcars && ls -la'
alias umntlxc='cd /root && umount /hosts/inter-data && ls -la'
alias mntm='echo Melissa wird gemountet && sshfs auto-spar.no-ip.org:/ /hosts/melissa && cd /hosts/melissa && ls -la'
alias umntm='cd /root && umount /hosts/melissa && ls -la'
alias mntw='echo Webspace wird gemountet && sshfs ssh-w00d0b24@auto-spar.de:/www/htdocs/w00d0b24 /hosts/web && cd /hosts/web && ls -la'
alias umntw='cd /root && umount /hosts/web && ls -la'
alias are='/etc/init.d/apache2 restart'
alias ud='sudo apt-get update && sudo apt-get upgrade'
alias ssha='ssh auto-spar.no-ip.org'

alias sshlxc='ssh lxcars.org' #lxcars.org
alias sshid='ssh inter-data.de' #Inter-Data
alias sshw='ssh ssh-w00d0b24@auto-spar.de'

alias cd..='cd ..'
alias cdlxca='cd /usr/lib/lx-office-crm/lxcars'
alias cdlxc='cd /usr/lib/lx-office-crm'
alias cdkc='cd /root/kivitendo-crm'
alias cdkcl='cd /root/kivitendo-crm/lxcars'
alias cdlxe='cd /usr/lib/lx-office-erp'
alias cdke='cd /root/kivitendo-erp'

mkd() {
 if [ $# -eq 1 ]
 then
  if [ ! -e "$1" ]
  then
    mkdir -p "$1" && cd "$1"
  else
    echo "$0: INFO: $1 exists" >&2
    cd "$1"
  fi
 else
  echo "$0: wrong usage" >&2
  echo "usage: $0 <PATH>"
  return 1
 fi
}


