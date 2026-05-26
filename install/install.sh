#!/usr/bin/env bash
#
# This file is part of ezlogging
#
# (c) 2024 Oliver Glowa, coding.glowa.com
#
# This source file is subject to the Apache-2.0 license that is bundled
# with this source code in the file LICENSE.
#

# Define variables
Y_ROOT=$(realpath $(dirname ${0})/..)
Y_SRC=$(realpath ${Y_ROOT}/cfg)
Y_TARG=${HOME}/.yacorapi

# Check folders
if [[ ! -d ${Y_SRC} ]]; then
    echo -e "Not exists : '${Y_SRC}'!"
    exit 1;
else
    echo "Source exists : '${Y_SRC}'"
fi

if [[ ! -d ${Y_TARG} ]]; then
    mkdir -p ${Y_TARG}
    if [[ ! -d ${Y_TARG} ]]; then
        echo -e "Cannot create : '${Y_TARG}'!"
        exit 1;
    fi
else
    echo -e "Target exists : '${Y_TARG}'!"
fi

# Check files
if [[ ! -f  ${Y_TARG}/MyAuth.php ]]; then
    cp -v ${Y_SRC}/MyAuth.php.dist ${Y_TARG}/MyAuth.php
else
    echo -e "Already exists: ${Y_TARG}/MyAuth.php"
fi
if [[ ! -f ${Y_TARG}/MySpaces.php ]]; then
    cp -v ${Y_SRC}/MySpaces.php.dist ${Y_TARG}/MySpaces.php
else
    echo -e "Already exists: ${Y_TARG}/MySpaces.php"
fi

# Manual instructions
echo -e "\nNow edit the file ${Y_TARG}/MyAuth.php
- Replace #confluence-url_for_production# with your Confluence URL for the production environment
- Replace #confluence-url_for_testing# with your Confluence URL for the testing environment
- Set USE_PROD=true if you want to use production"


