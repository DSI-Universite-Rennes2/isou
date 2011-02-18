#!/bin/bash

# url_site = "http://www.univ-rennes2.fr/cri"
url_isou="https://services.univ-rennes2.fr/isou"

sed -i s,https://services.univ-rennes2.fr/isou,$url_isou, install.rdf
sed -i s,https://services.univ-rennes2.fr/isou,$url_isou, update.rdf
sed -i s,https://services.univ-rennes2.fr/isou,$url_isou, chrome/content/fisou.js
sed -i s,https://services.univ-rennes2.fr/isou,$url_isou, chrome/content/preferences.xul
sed -i s,https://services.univ-rennes2.fr/isou,$url_isou, chrome/content/about.xul



