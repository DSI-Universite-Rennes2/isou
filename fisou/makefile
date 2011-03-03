all:
	mkdir -p build
	bash clean.sh
	zip -r build/fisou.xpi chrome/ chrome.manifest install.rdf -x *\.svn*
	cp ./update.rdf ./build
