#!/bin/bash
URL="$(cd "$(dirname "$0")" && pwd)"
cd "$URL"
cd ../uploads/gallery
dir="temp"
mkdir "$dir"
#convert png if exist
echo "Convert png if exist ..."
mogrify -format jpg *.png
echo "Done!"

output_com="500x500"

echo "Autorotate ..."
jhead -autorot -ft *.jpg
echo "Done!"

#read size
echo "Crop and resize ..."
for f in *.jpg; do
read w h < <(convert "$f" -format "%w %h" info:)
if (($h>$w))
then
echo "Processing "$f
yoffset=$(echo "$h"/2-"$w"/2 | bc -l)
convert -crop "$w"x"$w"+0+"$yoffset" "$f" "$dir"/"$f"
mogrify -resize "$output_com" "$dir"/"$f"
else
xoffset=$(echo "$w"/2-"$h"/2 | bc -l)
convert -crop "$h"x"$h"+"$xoffset"+0 "$f" "$dir"/"$f"
mogrify -resize "$output_com" "$dir"/"$f"
fi
done
echo "Done!"

#optimize
echo "Optimize jpg ..."
for f in "$dir"/*.jpg; do
jpegoptim "$f" --max=90 --all-progressive --strip-all --strip-com --strip-exif --strip-iptc --strip-icc
done
echo "Done!"

#move to media/import folder
pwd
for f in "$dir"/*.jpg; do
mv "$f" /home/sturquier/www/media/import
done
echo "Done!"

#remove temp files
rm -R * temp/
