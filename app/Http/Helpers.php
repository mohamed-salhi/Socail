<?php


use App\Models\Notification;
use App\Models\NotificationUser;
use App\Models\Upload;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
function locale()
{
    return Mcamara\LaravelLocalization\Facades\LaravelLocalization::getCurrentLocale();
}

function locales()
{
    $arr = [];
    foreach (LaravelLocalization::getSupportedLocales() as $key => $value) {
        $arr[$key] = __('' . $value['name']);
    }
    return $arr;
}

function languages()
{
    if (app()->getLocale() == 'en') {
        return ['ar' => 'arabic', 'en' => 'english'];
    } else {
        return ['ar' => 'العربية', 'en' => 'النجليزية'];
    }
}

function mainResponse($status, $msg, $items, $validator = [], $code = 200, $pages = null)
{
    $item_with_paginate = $items;
    if (gettype($items) == 'array') {
        if (count($items)) {
            $item_with_paginate = $items[array_key_first($items)];
        }
    }

    if (isset(json_decode(json_encode($item_with_paginate, true), true)['data'])) {
        $pagination = json_decode(json_encode($item_with_paginate, true), true);
        $new_items = $pagination['data'];
        $pages = [
            "current_page" => $pagination['current_page'],
            "first_page_url" => $pagination['first_page_url'],
            "from" => $pagination['from'],
            "last_page" => $pagination['last_page'],
            "last_page_url" => $pagination['last_page_url'],
            "next_page_url" => $pagination['next_page_url'],
            "path" => $pagination['path'],
            "per_page" => $pagination['per_page'],
            "prev_page_url" => $pagination['prev_page_url'],
            "to" => $pagination['to'],
            "total" => $pagination['total'],
        ];
    } else {
        $pages = [
            "current_page" => 0,
            "first_page_url" => '',
            "from" => 0,
            "last_page" => 0,
            "last_page_url" => '',
            "next_page_url" => null,
            "path" => '',
            "per_page" => 0,
            "prev_page_url" => null,
            "to" => 0,
            "total" => 0,
        ];
    }

    if (gettype($items) == 'array') {
        if (count($items)) {
            $new_items = [];
            foreach ($items as $key => $item) {
                if (isset(json_decode(json_encode($item, true), true)['data'])) {
                    $pagination = json_decode(json_encode($item, true), true);
                    $new_items[$key] = $pagination['data'];
                } else {
                    $new_items[$key] = $item;
                }

                $items = $new_items;
            }
        }
    } else {
        if (isset(json_decode(json_encode($item_with_paginate, true), true)['data'])) {
            $pagination = json_decode(json_encode($item_with_paginate, true), true);
            $items = $pagination['data'];
        }
    }

//    $items = $new_items;

    $aryErrors = [];
    foreach ($validator as $key => $value) {
        $aryErrors[] = ['field_name' => $key, 'messages' => $value];
    }
    /*    $aryErrors = array_map(function ($i) {
            return $i[0];
        }, $validator);*/

    $newData = ['status' => $status, 'message' => __($msg), 'data' => $items, 'pages' => $pages, 'errors' => $aryErrors];

    return response()->json($newData);
}

function paginate($items, $perPage = 15, $page = null, $options = [])
{
    $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
    $items = $items instanceof Collection ? $items : Collection::make($items);
    return new LengthAwarePaginator($items->forPage($page, $perPage)->values(), $items->count(), $perPage, $page, $options);
}

function paginateOrder($items, $perPage = 15, $page = null, $options = [])
{
    $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
    $items = $items instanceof Collection ? $items : Collection::make($items);
    return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
}

function pageResource($data, $resource)
{
    $items = $data->getCollection();
    $items = $resource::collection($items);
    $data->setCollection(collect($items));
    return $data;
}


function UploadImage($file, $path = null, $model, $imageable_id, $update = false, $id = null, $type, $name = null)
{
    $path = $file->store($path, 'public');
    if (!$update) {
        return Upload::create([
            'filename' => $path,
            'path' => $path,
            'imageable_id' => $imageable_id,
            'imageable_type' => $model,
            'type' => $type,
            'name' => $name
        ]);
    } else {
        if ($name) {
            $image = Upload::query()->where('imageable_id', $imageable_id)->where('imageable_type', $model)->where('name', $name)->first();
            if ($image) {
                \Illuminate\Support\Facades\Storage::delete('public/' . @$image->path);
                return $image->update(
                    [
                        'filename' => $path,
                        'path' => $path,
                        'imageable_id' => $imageable_id,
                        'imageable_type' => $model,
                        'type' => $type,
                        'name' => $name
                    ]
                );
            } else {
                return Upload::create([
                    'filename' => $path,
                    'path' => $path,
                    'imageable_id' => $imageable_id,
                    'imageable_type' => $model,
                    'type' => $type,
                    'name' => $name
                ]);
            }
        } else {
            $image = Upload::where('imageable_id', $imageable_id)->where('imageable_type', $model)->where('type', $type)->first();
            if ($id) {
                $image = Upload::where('uuid', $id)->first();
            }
            if ($image) {
                Storage::delete('public/' . @$image->path);
                $image->update(
                    [
                        'filename' => $path,
                        'path' => $path,
                        'imageable_id' => $imageable_id,
                        'imageable_type' => $model,
                        'type' => $type,
                        'name' => $name
                    ]
                );
                return $path;
            } else {
                return Upload::create([
                    'filename' => $path,
                    'path' => $path,
                    'imageable_id' => $imageable_id,
                    'imageable_type' => $model,
                    'type' => $type,
                    'name' => $name
                ]);
            }
        }
    }

}



?>
