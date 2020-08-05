<?php

function saveImage($value, $destination_path, $disk='publicmedia', $size = array(), $watermark = array())
{

  $default_size = [
    'imagesize' => [
      'width' => 1024,
      'height' => 768,
      'quality'=>80
    ],
    'mediumthumbsize' => [
      'width' => 400,
      'height' => 300,
      'quality'=>80
    ],
    'smallthumbsize' => [
      'width' => 100,
      'height' => 80,
      'quality'=>80
    ],
  ];
  $size = json_decode(json_encode(array_merge($default_size, $size)));
  //Defined return.
  if (ends_with($value, '.jpg') || ends_with($value, '.png')) {
    return $value;
  }

  // if a base64 was sent, store it in the db
  if (starts_with($value, 'data:image')) {
    // 0. Make the image
    $image = \Image::make($value);
    // resize and prevent possible upsizing
    $width = 0;
    $height = 0;
    
    if($size->imagesize->width > 700){
      $width = $size->imagesize->width / 2;
    }
    
    if($size->imagesize->height > 700){
      $height = $size->imagesize->height / 2;
    }

    $image->resize($width, $height, function ($constraint) {
      $constraint->aspectRatio();
      $constraint->upsize();
    });
    // 2. Store the image on disk.
    \Storage::disk($disk)->put($destination_path, $image->stream('jpg', $size->imagesize->quality));


    // Save Thumbs
    // \Storage::disk($disk)->put(
    //     str_replace('.jpg', '_mediumThumb.jpg', $destination_path),
    //     $image->fit($size->mediumthumbsize->width, $size->mediumthumbsize->height)->stream('jpg', $size->mediumthumbsize->quality)
    // );
    //
    // \Storage::disk($disk)->put(
    //     str_replace('.jpg', '_smallThumb.jpg', $destination_path),
    //     $image->fit($size->smallthumbsize->width, $size->smallthumbsize->height)->stream('jpg', $size->smallthumbsize->quality)
    // );

    // 3. Return the path
    return $destination_path;
  }

  // if the image was erased
  if ($value == null) {
    // delete the image from disk
    \Storage::disk($disk)->delete($destination_path);

    // set null in the database column
    return "profiles/generic-user.png";
  }


}
