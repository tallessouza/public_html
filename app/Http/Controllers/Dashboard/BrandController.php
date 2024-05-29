<?php

namespace App\Http\Controllers\Dashboard;

use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    public function index()
    {
        $list = Company::where('user_id', auth()->user()->id)->orderBy('name', 'asc')->get();

        return view('panel.user.companies.list', compact('list'));
    }

    public function delete($id = null)
    {
        if (Helper::appIsDemo()) {
            return response()->json(__('This feature is disabled in Demo version.'), 419);
        }
        Product::where('user_id', auth()->user()->id)->where('company_id', $id)->delete();
        Company::where('id', $id)->where('user_id', auth()->user()->id)->delete();

        return back()->with(['message' => __('Deleted Successfully'), 'type' => 'success']);
    }

    public function addOrUpdate($id = null)
    {
        if ($id == null) {
            $item = null;
        } else {
            $item = Company::where('id', $id)->where('user_id', auth()->user()->id)->firstOrFail();
        }

        return view('panel.user.companies.form', compact('item'));
    }

    public function store(Request $request)
    {
        if (Helper::appIsDemo()) {
            return response()->json(__('This feature is disabled in Demo version.'), 419);
        }
        if ($request->item_id != 'undefined') {
            $item = Company::where('id', $request->item_id)->firstOrFail();
        } else {
            $item = new Company();
        }

        if ($request->hasFile('c_logo')) {
            $path = 'upload/images/companies/';
            $image = $request->file('c_logo');
            $image_name = Str::random(4).'-'.Str::slug($request->c_name).'-logo.'.$image->getClientOriginalExtension();

            $imageTypes = ['jpg', 'jpeg', 'png', 'svg', 'webp'];
            if (! in_array(Str::lower($image->getClientOriginalExtension()), $imageTypes)) {
                $data = [
                    'errors' => ['The file extension must be jpg, jpeg, png, webp or svg.'],
                ];

                return response()->json($data, 419);
            }

            $image->move($path, $image_name);

            $item->logo = $path.$image_name;
        }

        $item->name = $request->c_name;
        $item->website = $request->c_website;
        $item->tagline = $request->c_tagline;
        $item->description = $request->c_description;
        $item->brand_color = $request->c_color;
        $item->industry = $request->c_industry;
        $item->tone_of_voice = $request->tone_of_voice;
        $item->target_audience = $request->target_audience;

        $inputNames = explode(',', $request->input_name);
        $inputFeatures = explode(',', $request->input_features);
        $inputTypes = explode(',', $request->input_type);

        $item->user_id = auth()->user()->id;
        $item->save();

        foreach ($inputNames as $key => $inputName) {
            if ($request->item_id != 'undefined') {
                $product = Product::where('user_id', auth()->user()->id)->where('company_id', $item->id)->where('name', $inputName)->first();
                if ($product == null) {
                    $product = new Product();
                }
            } else {
                $product = new Product();
            }
            $product->name = $inputName;
            $product->key_features = $inputFeatures[$key];
            $product->type = $inputTypes[$key];
            $product->user_id = auth()->user()->id;
            $product->company_id = $item->id;
            $product->save();
        }
        // delete products that are not in the input
        Product::where('user_id', auth()->user()->id)->where('company_id', $item->id)->whereNotIn('name', $inputNames)->delete();

        return response()->json(['message' => __('Saved Successfully'), 'type' => 'success']);
    }

    public function getProducts($id)
    {
        $products = Product::where('user_id', auth()->user()->id)->where('company_id', $id)->get();

        return response()->json($products);
    }
}
