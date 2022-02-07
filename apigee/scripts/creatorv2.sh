#!/bin/bash

#TO run this script use ./ or bash commands only since it is a bash script. Some commands trigger error if you use other shell
#Google Health Care FHIR Creator and data importar script

#Get OS Version to make script independent
OS_Version=`awk -F= '/^NAME/{print $2}' /etc/os-release | awk '{print$1}'`
OS_Version="${OS_Version:1}"


set -x
##Variables goes here
input_file=input.yaml
DATE=`date "+%F~%H:%M"`
tf_file=terraform.tfstate

UUID=`uuidgen`
inputFolder="input_files"
outputFolder="output_files"
saFolder="service_account_files"


##variables for debian based linux
dataname=`yq r ${input_file}  'GHC.dataset.name'`
fhirname=`yq r ${input_file} 'GHC.fhir_store_name.name'`
location=`yq r ${input_file} 'GHC.location.name'`
import_option=`yq r ${input_file} 'GHC.import_data.option'`
project_name=`yq r ${input_file}  'GHC.gcp_project.name'`
load_option=`yq r ${input_file} 'GHC.import_data.option'`
bucket_name=`yq r ${input_file} 'GHC.import_data.bucket_name'`
use_custom_proxy=`yq r ${input_file}  'GHC.custom_proxy.option'`
custom_proxy_name=`yq r ${input_file}  'GHC.custom_proxy.proxy'`
custom_product_name=`yq r ${input_file} 'GHC.custom_proxy.product'`
use_fhir_store_as_proxy=`yq r ${input_file} 'GHC.use_fhirstore_as_proxy_name'`
version=`yq r ${input_file} 'GHC.fhir_version'`
backupBucketName=`yq r ${input_file} 'GHC.backupBucketName'`
gpsub=`yq r ${input_file} 'GHC.pubsub_topic'`
gpsub=$(sed 's|/|\\/|g' <<< $gpsub)
up_create=`yq r ${input_file} 'GHC.enable_update_create'`
ref_integrity=`yq r ${input_file} 'GHC.disable_referential_integrity'`
res_versioning=`yq r ${input_file} 'GHC.disable_resource_versioning'`
hist_import=`yq r ${input_file} 'GHC.enable_history_import'`
google_api_version=`yq r ${input_file} 'GHC.google_api_version'`

google_fhirstore_url=/$google_api_version/projects/$project_name/locations/$location/datasets/$dataname/fhirStores/$fhirname/fhir

backupBucket="gs://$backupBucketName"

fhirserver_name="not_set"
apigeeproduct_name="not_set"
isCustomProxy=false
#Decide how to name proxy and other log files etc.
#Priority -> use_custom_proxy > use_fhir_store_as_proxy > default fhir_fhirversion_UUID
echo "use_custom_proxy $use_custom_proxy"
echo "use_fhir_store_as_proxy $use_fhir_store_as_proxy"

if [ -n "$use_custom_proxy" ] && [ "$use_custom_proxy" = true ]
then
  echo "using custom proxy name and product..."
  if [ "$custom_proxy_name" = null ] || [ "$custom_product_name" = null ]
  then
    echo "either custom proxy name or custom product name has not been specified correctly. Check and try again."
    exit 10
  fi
  fhirserver_name=$custom_proxy_name
  apigeeproduct_name=$custom_product_name
  isCustomProxy=true
elif [ -n "$use_fhir_store_as_proxy" ] && [ "$use_fhir_store_as_proxy" = true ]
then
  echo "using fhir store name for proxy and product..."
  echo "this will replace if there is already a proxy/product with same FHIR server name..."
  fhirserver_name="${fhirname}_proxy"
  apigeeproduct_name="${fhirname}_product"
  isCustomProxy=true
else
  echo "using fhir version and UUID for proxy name and product... "
  fhirserver_name="${version}_${UUID}"
  apigeeproduct_name="fhir_${version}_${UUID}_product"
  #statements
fi
echo "$fhirserver_name $apigeeproduct_name"

#Variable required by Json output
logfile="fhir_store-$DATE.log"
#proxyurl="https://healthapix-demo-test.apigee.net/"
proxyurl=`yq r ${input_file}  'GHC.proxyurl'`
nextpath="v1/${version}/"
outfile="${fhirserver_name}.json"


#DATE=`date +%F~%T`
##Adding log Use only bash and name of this script to work
exec > >(tee -i $logfile)
exec 2>&1


#create dataset.tf file required by terraform
data_set()
{
#cat << EOF > dataset
cat > dataset.tf <<- "EOF"
resource "google_healthcare_dataset" "default" {
  name      = "dataname"
  location  = "location_zone"
  time_zone = "UTC"
  provider  = google-beta
}
EOF

sed -i  "s/dataname/$dataname/g" dataset.tf
sed -i  "s/location_zone/$location/g" dataset.tf
}

provider_tf()
{
cat > provider.tf <<- "EOF"
provider "google" {
  credentials = file("service_account_key.json")
  project     = "proj_name"
  region      = "us-central1"
}

provider "google-beta" {
  credentials = file("service_account_key.json")
  project     = "proj_name"
  region      = "us-central1"
}
EOF
sed -i  "s/us-central1/$location/g" provider.tf
sed -i  "s/proj_name/$project_name/g" provider.tf

terraform init -no-color
}


get_old_fhir(){
##Get the old fhir name
if [[ -f "fhirstore.tf" ]]; then
old_fhir=`cat fhirstore.tf  | grep 'name' | awk '{print $3}' | sed -e 's/^"//' -e 's/"$//'`
if [[ "$fhirname" == "$old_fhir" ]]; then
echo "old fhir name and new fhir name are same hence not running terraform again"
exit 0
fi
else
provider_tf
fi
}

get_old_fhir



##Create fhir store .tf file rewquired by terraform
fhir_store()
{
cat > fhirstore.tf <<- "EOF"
resource "google_healthcare_fhir_store" "default" {
  name    = "fhirname"
  dataset = google_healthcare_dataset.default.id
  version = "sttu"

  enable_update_create          = up_create
  disable_referential_integrity = ref_integrity
  disable_resource_versioning   = res_versioning
  enable_history_import         = hist_import
  
  notification_config {
    pubsub_topic = gpsub
  }

  labels = {
    label1 = "labelvalue1"
  }
  provider = google-beta
}
EOF

sed -i  "s/fhirname/$fhirname/g" fhirstore.tf
sed -i "s/sttu/$version/g" fhirstore.tf
sed -i "s/gpsub/$gpsub/g" fhirstore.tf
sed -i "s/$gpsub/\"$gpsub\"/g" fhirstore.tf
sed -i "s/up_create/$up_create/g" fhirstore.tf
sed -i "s/ref_integrity/$ref_integrity/g" fhirstore.tf
sed -i "s/res_versioning/$res_versioning/g" fhirstore.tf
sed -i "s/hist_import/$hist_import/g" fhirstore.tf
}

##Create output json file part 1
json_part1()
{


JSON_PART1=$( jq -n \
                 --arg name "${fhirserver_name}" \
                 --arg proxyurl "$proxyurl$nextpath${fhirserver_name}" \
                 --arg defaultproduct "${apigeeproduct_name}" \
                 --arg project "$project_name" \
                 --arg location "$location" \
                 --arg dataset "$dataname"  \
                 --arg fhirstorename "$fhirname"   \
                 --arg version "$version"  \
                 --arg isCustomProxy "$isCustomProxy" \
                 --arg googleFhirstoreUrl "$google_fhirstore_url" \
                 '{"fhirserver":{"fhirversion":$version,"name":$name,"googleFhirstoreUrl":$googleFhirstoreUrl,"isCustomProxy":$isCustomProxy,"proxyurl":$proxyurl,"defaultproduct":$defaultproduct,"project":$project,"location":$location,"dataset":$dataset,"fhirstorename":$fhirstorename}}')



echo $JSON_PART1 > json1.txt
sed -i  's/} }/},/g' json1.txt
}



##Json output file creator part 2
json_part2()
{
JSON_PART2=$( jq -n \
                  --arg infile "$input_file" \
                  --arg outfile "$outfile" \
                  --arg logfile "$logfile" \
                  '{"location":{"inputfile":$infile,"outputfile":$outfile,"logfile":$logfile}}'

)

echo $JSON_PART2 >json2.txt
sed -i  's/} }/}/g' json2.txt
echo ',"data":{"loaddata":'\"$load_option\"',"bucket":'\"$bucket_name\"',"files": [ ' >> json2.txt

cat json3.txt >> json2.txt
sed -i  '$ s/.$//' json2.txt
echo "]}}" >> json2.txt
sed -i  '0,/./s/^.//' json2.txt
#mv json2.txt output.json
}



service_account()
{
keyfilename="${fhirserver_name}_serviceaccount.json"
echo "Renaming the key json file name to $keyfilename"

service_account_key=`yq r ${input_file} 'GHC.service_account'`

cp $service_account_key $keyfilename

export keyfilename="${fhirserver_name}_serviceaccount.json"

}


custProxy_name()
{
      sed -i "s/${fhirserver_name}/${custom_proxy_name}/g" ${fhirserver_name}.json
      sed -i "s/\"defaultproduct\": \"${custom_proxy_name}\"/\"defaultproduct\": \"${custom_product_name}\"/g" ${fhirserver_name}.json
      mv ${fhirserver_name}.json ${custom_proxy_name}.json
}
json_part4()
{
echo "execuiting JSON_PART4"
JSON_PART4=$( jq -n \
                  --arg check_option "$check_option" \
                  --arg keyfilename "$keyfilename" \
                  '{"service_account":{"filename":$keyfilename}}'

)
echo "creating json4.txt"
echo "$JSON_PART4" >json4.txt
sed -i  '0,/./s/^.//' json4.txt
}

##Content-Structure could be one of the following - RESOURCE, BUNDLE, BUNDLE_PRETTY
##Function to check and import the data to FHIR store
#check the option i.e true or false
import_data(){
if [[ "$import_option" == "true" ]]; then
	echo "import option is enabled and checking for all the required inputs"
	echo ""
	echo "Checking Bucket_name"
	bucket_name=`yq r ${input_file}  'GHC.import_data.bucket_name'`
	bucket_name=`echo $bucket_name | tr -d '"'`
	echo ""
	echo "Found bucket name as $bucket_name"
	echo ""
	echo "Importing data to FHIR store"
	num_of_lines=`awk '/file_names/,/service_account/' $input_file | wc -l`
	k=`expr $num_of_lines - 2`
	k=`expr $k / 2`
	echo "Total number of files needs to be imported is $k"
	for (( i=1; i<=$k; i++ ))
	do
		name=`awk '/file_names/,/service_account/' $input_file | grep 'name:' | head -n $i |tail -n 1 | awk '{print $2}'`
		type=`awk '/file_names/,/service_account/' $input_file | grep 'type:' | head -n $i |tail -n 1 | awk '{print $2}'`

	   	echo "Name is $name  and type is $type"

		gcloud beta healthcare fhir-stores import gcs $fhirname \
			 --dataset=$dataname --async \
			  --gcs-uri=gs://$bucket_name/$name \
			   --location=us-central1 --content-structure=$type

               echo "Creating JSON output files"
               echo '    {"type"':\"$type\", >> json3.txt
               echo '    "name"':\"$name}\", >> json3.txt

       done
fi

sed -i  's/}",/"},/g' json3.txt

}



#check the terraform tfstate file if exists no need to initiate the terraform
if [[ -f "$tf_file" ]]; then
echo "this is the old fhir name $old_fhir"
echo "$tf_file exists"
cat $tf_file | grep $old_fhir
echo "checking fhir name"
if [[ "$?" -eq "0" ]]; then
echo "fhir already exists"
echo ""
echo "editing the $tf_file"
sed -i "s/$old_fhir/tttt/g" $tf_file
mv $tf_file.* /var/tmp
fi

fi

data_set
fhir_store

read -r -p "terraform files are created.. do you want to continue with creating the fhir store...? y/n: `echo $'\n'`"  input

case $input in
 [yY][eE][sS]|[yY])
terraform apply -no-color -auto-approve
;;
*)
exit 1
;;
esac

service_account

import_data
echo "Creating Output JSON files"
json_part1
json_part2
json_part4
cat json2.txt >> json1.txt
sed -i  's/}}/},/g' json1.txt
cat json4.txt >> json1.txt
mv json1.txt $outfile
rm -rf json*.txt


echo "FILE is $keyfilename"
FILE=$keyfilename
if [ -f "$FILE" ]; then
	sed -i "s/}//g"  $FILE
	sed -i '/^$/d'  $FILE
	sed -i '$s/$/,/' $FILE
	echo "\"project\":\"$project_name\"," >> $FILE
	echo "\"location\":\"$location\"," >> $FILE
	echo "\"dataset\":\"$dataname\"," >> $FILE
	echo "\"fhirstore\":\"$fhirname\"}" >> $FILE
  echo "Copying service account file $FILE to location $backupBucket/$saFolder"
  gsutil cp $FILE $backupBucket/$saFolder
fi

echo "Copying input file $input_file to location $backupBucket/$inputFolder"
gsutil cp $input_file $backupBucket/$inputFolder

if [ -f "$outfile" ]; then
echo "Copying output file $outfile to location $backupBucket/$outputFolder"
gsutil cp $outfile $backupBucket/$outputFolder
fi


if [ ${use_custom_proxy} == "true" ]
then
custProxy_name
else
	exit 0
fi
