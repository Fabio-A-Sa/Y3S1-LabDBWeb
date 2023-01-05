# This script changes images extension .png -> .jpg
for folder in profile post groups icons; do  
    cd $folder
    for file in *.png; do 
        mv -- "$file" "${file%.png}.jpg"
    done
    cd ..
done