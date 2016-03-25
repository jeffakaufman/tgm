#!/bin/bash -xv
################################################################################
#
# Nightly reindex and clear cache
################################################################################
MAGE_HOME=/var/www/benq/public_html

#
# Do a full reindexing
/usr/bin/php ${MAGE_HOME}/shell/indexer.php reindexall

sleep 30

#
# Force clear Magento cache
rm -rf ${MAGE_HOME}/var/cache
rm -rf ${MAGE_HOME}/var/full_page_cache
