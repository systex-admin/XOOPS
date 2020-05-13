#!/bin/bash

XOOPS_ROOT_PATH=${1}
XOOPS_CHANGE_URL=${2}
XOOPS_OLD_URL=${3}

if [ $# -lt 3 ]; then
        echo "usage: ${0} [XOOPS_ROOT_PATH] [XOOPS_CHANGE_URL] [XOOPS_OLD_URL] "
        echo "Example:"
        echo "${0} /var/www/ www.faes.tyc.edu.tw 10.240.49.101"
        echo
        echo "error: too few arguments"
        exit 1
fi

#grep -rl www.faes.tyc.edu.tw /var/www/ | xargs sed -i 's/www.faes.tyc.edu.tw/10.240.49.101/g'
grep -rl ${XOOPS_CHANGE_URL} ${XOOPS_ROOT_PATH} | xargs sed -i "s/${XOOPS_CHANGE_URL}/${XOOPS_OLD_URL}/g"
