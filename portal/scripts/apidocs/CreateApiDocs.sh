#!/bin/bash
set -x
#set -e
echo "Received input $1 $2"


usage() {
  echo "Usage: $(basename $0) [apigee-orgaisation] [apigee-environment]"
  exit 0
}

if [ $# -eq 0 ]; then
    echo "Script cannot proceed without all the inputs..."
	usage
fi

org=$1
env=$2

echo "Creating API docs for Apigee instance - $org-$env "
apidocs_dir=$org-$env
mkdir $apidocs_dir

APIDOCSDIR="DSTU2 STU3 R4"

for file in $APIDOCSDIR
do
    if [ ! -d $apidocs_dir/$file ]
    then
        mkdir $apidocs_dir/$file
    fi
    
    echo "Working on $file..."
    for k in `ls $file`
    do
        echo "Processing $file/$k to $apidocs_dir/$file/..."
        sed 's/org-env/'"$org-$env"'/g' $file/$k > $apidocs_dir/$file/$k
    done
done

echo;echo
