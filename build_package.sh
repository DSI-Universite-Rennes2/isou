#!/bin/bash
mkdir isou && \
cp -r database/ doc/ fisou/ install/ sources/ AUTHORS COPYRIGHT install.php README README-CRONTAB update.php isou/ && \
find ./isou -name ".svn" -type d -exec rm -rf {} \; ; \
zip -r isou_0.9.0-RC1.zip isou/ && \
rm -rf isou

