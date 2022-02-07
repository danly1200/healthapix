#!/bin/bash

## Install Dependencies

# check for sudo access
if [[ "$EUID" -ne 0 ]]
then
  echo "ERROR: Please run this script as root"
  exit 1
fi

# Update apt
sudo apt -y update

# Update apt-get
sudo apt-get -y update

# Install curl and wget
echo "** installing curl"
sudo apt-get -y install curl

#get nvm
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.37.2/install.sh | bash

export NVM_DIR="$HOME/.nvm"
[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"  # This loads nvm
[ -s "$NVM_DIR/bash_completion" ] && \. "$NVM_DIR/bash_completion"  # This loads nvm bash_completion

if [ -f $HOME/.bashrc ]
then 
        echo "sourcing bashrc"
        source $HOME/.bashrc
        echo "done sourcing"
fi

echo "nvm version - "
nvm --version
if [ $? -ne 0 ]
then 
        echo "nvm installation failed"
        exit 1
fi
echo "Installing node version - 10.24.0"
nvm install 10.24.0
if [ $? -ne 0 ]
then
        echo "node installation failed"
        exit 1
fi

echo "node version - " 
node --version
if [ $? -ne 0 ]
then
        echo "node installation failed"
        exit 1
fi

echo "npm version - " 
npm --version
if [ $? -ne 0 ]
then
        echo "npm installation failed"
        exit 1
fi

# install Gulp
echo "** installing gulp"
npm install --global gulp-cli
if [ $? -ne 0 ]
then
        echo "gulp installation failed"
        exit 1
fi

# install apigeetool
echo "** installing apigeetool"
npm install -g apigeetool
apigeetool

# install jq
echo "** installing jq"
sudo apt-get -y install jq
jq --version

echo "** dependencies installed"