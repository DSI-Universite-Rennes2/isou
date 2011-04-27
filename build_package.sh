#!/bin/bash
if [ $# == 1 ]; then
    version=$1
else
    version=""
fi

mkdir isou && \
cp -r database/ doc/ fisou/ install/ sources/ AUTHORS COPYRIGHT install.php README README-CRONTAB update.php isou/ && \
zip -r isou_"$version".zip isou/ && \
rm -rf isou

