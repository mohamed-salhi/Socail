<?php

namespace App\Http\Controllers\Api\Home;

use App\Http\Controllers\Controller;
use App\Http\Resources\DetailsShahidResource;
use App\Http\Resources\LiveResource;
use App\Http\Resources\CategoryResource;

use App\Http\Resources\MapResource;
use App\Http\Resources\MaResource;

use App\Http\Resources\ShahidResource;

use App\Models\Category;

use App\Models\Like;
use App\Models\Live;
use App\Models\Mosque;
use App\Models\Page;

use App\Models\Shahid;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{

    public function page($id)
    {
        $setting = Page::query()->where('id', $id)->first();
        return mainResponse(true, "done", $setting, []);
    }





}
