<?php

namespace App\Observers;

use Exception;
use App\Models\Product;
use Illuminate\Support\Str;
use App\Mail\ProductCreated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Backpack\Settings\app\Models\Setting;

class ProductObserver
{
    /**
     * Handle the product "created" event.
     *
     * @param  $product
     * @return void
     */
    public function created(Product $product)
    {
        //Order to admins
        if ( !$product->parent_id ) {
            $administrators = Setting::get('administrator_email');
            $recipients = explode(';', $administrators);
            foreach ($recipients as $key => $recipient) {
                try {
                    Mail::to($recipient)->send(new ProductCreated($product, $product->seller->visible_name));
                } catch (Exception $e) {
                    Log::error($e->getMessage());
                }
            }
        }
    }

    /**
     * Handle the product "updating" event.
     *
     * @param  \App\Product  $product
     * @return void
     */
    public function updating(Product $product)
    {
        $this->validateSpecialPrice($product);
        $this->validateRejectFields($product);
        $this->syncAttributes($product);
        $this->multiUploadImages($product);

        if ($product->product_type->id == Product::PRODUCT_TYPE_CONFIGURABLE) {
            $product->createUpdateVariations();
        } else if ($product->product_type->id == Product::PRODUCT_TYPE_SIMPLE) {
            if($product->use_inventory_control) {
                $product->createUpdateInventories();
            }
        }
    }

    /**
     * Handle the product "deleting" event.
     *
     * @param  \App\Product  $product
     * @return void
     */
    public function deleting(Product $product)
    {
        // Delete all children prducts
        foreach($product->children as $children) {
            $children->delete();
        }

        // Delete custom attributes values before deleting the product
        foreach($product->custom_attributes as $custom_attribute) {
            $custom_attribute->delete();
        }

        // Delete references on other tables
        $product->super_attributes()->detach();
        $product->inventories()->detach();
        $product->categories()->detach();
        $product->shipping_methods()->detach();

        // Delete image references and files
        DB::table('product_images')->where('product_id', $product->id)->delete();
        $product->deleteImages();
    }

    /**
     * Handle the product "restored" event.
     *
     * @param  \App\Product  $product
     * @return void
     */
    public function restored(Product $product)
    {
        //
    }

    /**
     * Handle the product "force deleted" event.
     *
     * @param  \App\Product  $product
     * @return void
     */
    public function forceDeleted(Product $product)
    {
        //
    }

    // TODO: maybe this shoul go in another class?
    public function multiUploadImages($product)
    {
        $disk = 'public';
        $attribute_name = 'images_json';

        if (is_string($product->images_json)) {
            $valueDecode = json_decode($product->images_json, true);
        } else {
            $valueDecode = $product->images_json ?? [];
        }

        if (is_string($product->getOriginal('images_json'))) {
            $oldValueDecode = json_decode($product->getOriginal('images_json'), true);
        } else {
            $oldValueDecode = $product->getOriginal('images_json') ?? [];
        }

        $images = [];
        $oldImages = [];

        $updatedImages = [];
        foreach($valueDecode as $data) {
            array_push($images, $data['image']);
        }

        if(!empty($oldValueDecode)) {
            foreach($oldValueDecode as $data) {
                array_push($oldImages, $data['image']);
            }

            // delete references to erased images
            foreach($oldImages as $oldImg) {
                if (!in_array($oldImg, $images)) {

                    // delete the image from disk
                    $deleteFile = Str::replaceFirst('/storage/', '', $oldImg);
                    \Storage::disk($disk)->delete($deleteFile);

                    // delete reference on db
                    DB::table('product_images')->where('path', $oldImg)->delete();
                }
            }
        }

        foreach($images as $img) {

            // if a base64 was sent, store it in the db
            if (Str::startsWith($img, 'data:image'))
            {
                // 0. Make the image
                $image = \Image::make($img)->encode('jpg', 90);

                // 1. Generate a filename.
                $filename = md5($img.time()) . '.jpg';

                // 2. Store the image on disk.
                \Storage::disk($disk)->put('products/' . $filename, $image->stream());

                DB::table('product_images')->insert([
                    'product_id' => $product->id,
                    'path' => '/storage/products/' . $filename,
                ]);

                array_push($updatedImages, '/storage/products/' . $filename);
            } else {
                array_push($updatedImages, $img);
            }
        }

        $formattedArray = [];

        foreach($updatedImages as $data) {
            array_push($formattedArray, [
                'image' => $data,
            ]);
        }

        $product->images_json = json_encode($formattedArray);
    }

    public function validateSpecialPrice($product)
    {
       if($product->special_price == 0 || is_null($product->special_price)) {
           $product->special_price_from = null;
           $product->special_price_to = null;
           $product->special_price = null;
       }
    }

    public function validateRejectFields($product)
    {
       if($product->is_approved != 0) {
           $product->rejected_reason = null;
           $product->date_of_rejected = null;
       }
    }

    public function syncAttributes($product)
    {
        $attributesFromJson = $product->attributes_json ?? [];
        $attributes = [];

        // Get ID and values of every attribute
        foreach($attributesFromJson as $param => $value) {
            $isAnAtributte = substr($param, 0, 9) == 'attribute';
            if($isAnAtributte) {
                array_push($attributes, [
                    'id' =>  Str::replaceFirst('attribute-', '', $param),
                    'value' => $value,
                ]);
            }
        }

        // Update or create the attributes on the db
        $product->updateOrCreateAttributes($attributes);
}
}
