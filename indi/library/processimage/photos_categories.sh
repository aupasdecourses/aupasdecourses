#!/bin/bash

if [ $# -ne 1 ]; then
    echo $0: usage: photo_categories nom_fichier
    exit 1
fi

URL="$(cd "$(dirname "$0")" && pwd)"
cd "$URL"
cd ../../web/uploads/gallery
URL="$(pwd)"
echo $URL
src_com="${URL}/"$1

# Gravity is for cropping left/right edges for different proportions (center, east, west)
default_gravity="center";

# Output JPG quality: maximum is 100 (recommended)
quality=100;

function math(){
  echo $(python -c "from __future__ import division; print $@");
}

# To make our script short and nice, here is the "save()" function.
# We'll use it to save each size.

function save(){

  # read target width and height from function parameters
  local dst_com_w=400;
  local dst_com_h=300;
  local dst_thb_com_w=100;
  local dst_thb_com_h=100;

  # calculate ratio 
  local ratio_com=$(math $dst_com_w/$dst_com_h);
  local ratio_thmb_com=$(math $dst_thb_com_w/$dst_thb_com_h);

  # calculate "intermediate" width and height
  local inter_com_w=$(math "int(round($src_com_h*$ratio_com))");
  local inter_com_h=${src_com_h};
  local inter_thb_com_w=$(math "int(round($thb_com_h*$ratio_thmb_com))");
  local inter_thb_com_h=${thb_com_h};

  # calculate best sharpness
  local sharp_com=$(math "round((1/$ratio_com)/4, 2)");
  local sharp_thb_com=$(math "round((1/$ratio_thmb_com)/4, 2)");

  # which size we're saving now
  local size_com="${dst_com_w}x${dst_com_h}";
  echo "Saving ${size_com}...";
  local size_thb_com="${dst_thb_com_w}x${dst_thb_com_h}";
  echo "Saving ${size_thb_com}...";

  #crop intermediate image (with target ratio)
  convert "${src_com}" -gravity ${gravity} -crop ${inter_com_w}x${inter_com_h}+0+0 +repage temp_com.psd;
  convert "${src_com}" -gravity ${gravity} -crop ${inter_thb_com_w}x${inter_thb_com_h}+0+0 +repage temp_thb.psd;
  # final convert! resize, sharpen, save
  convert temp_com.psd -interpolate bicubic -filter Lagrange -resize ${dst_com_w}x${dst_com_h} -unsharp 0x${sharp_com} +repage -density 72x72 +repage -quality "${quality}" "${dst}full.jpg";
  convert temp_thb.psd -interpolate bicubic -filter Lagrange -resize ${dst_thb_com_w}x${dst_thb_com_h} -unsharp 0x${sharp_thb_com} +repage -density 72x72 +repage -quality "${quality}" "${dst}thumbnail.jpg";
  #optimisation pour le web
  jpegoptim "${dst}"*.jpg --max=90 --all-progressive --strip-all --strip-com --strip-exif --strip-iptc --strip-icc

}

gravity=${default_gravity}

#loop commercant images
thb_com_w=$(identify -format "%w" "${src_com}");
thb_com_h=$(identify -format "%h" "${src_com}");
src_com_w=$thb_com_w
src_com_h=$thb_com_h

save

# Delete temporary file
rm temp_com.psd;
rm temp_thb.psd;

# Done!
echo "Done!";
