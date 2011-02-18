all:
	mkdir -p build
	bash clean.sh
	zip -r --exclude=*.svn* build/fisou.xpi chrome/ chrome.manifest install.rdf
	cp ./update.rdf ./build
