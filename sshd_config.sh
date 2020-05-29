#!/bin/bash
script_name=$0
PORT=$1
ROOT_LOGIN=$2
KEY=$3
PASS=$4

if [[ "${UID}" -ne 0 ]]; then
    echo " You need to run this script as root"
    exit 1
fi

if [[ $# -lt 4 ]]; then
        echo "usage: ${script_name} [port] [RootLogin:yes|no] [PubkeyAuthentication:yes|no] [] "
        echo "Example:"
        echo
        echo "error: too few arguments"
        exit 1
fi


cp /etc/ssh/sshd_config /etc/ssh/sshd_config.bk
sed -i 's/#\?\(Port\s*\).*$/\1 2231/' /etc/ssh/sshd_config
sed -i 's/#\?\(PerminRootLogin\s*\).*$/\1 no/' /etc/ssh/sshd_config
sed -i 's/#\?\(PubkeyAuthentication\s*\).*$/\1 yes/' /etc/ssh/sshd_config
sed -i 's/#\?\(PermitEmptyPasswords\s*\).*$/\1 no/' /etc/ssh/sshd_config
sed -i 's/#\?\(PasswordAuthentication\s*\).*$/\1 no/' /etc/ssh/sshd_config

#Check the exit status of the last command

if [[ "${?}" -ne 0 ]]; then
   echo "The sshd_config file was not modified successfully"
   exit 1
fi
/etc/init.d/ssh restart

exit 0
