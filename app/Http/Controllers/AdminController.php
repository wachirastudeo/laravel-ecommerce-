<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class AdminController extends Controller
{
    //
    public function index()
    {
        return view('admin.index');
    }

    public function brands()
    {
        $brands = Brand::orderBy('id', 'DESC')->paginate(10);
        return view('admin.brands', compact('brands'));
    }
    public function add_brands()
    {
        return view('admin.brands-add');
    }
    public function brand_store(Request $request)
    {
        $request->validate(
            [
                'name' => 'required',
                'slug' => 'required|unique:brands,slug',
                'image' => 'mimes:png,jpg,jpeg|max:2048'
            ]
        );
        $brand = new Brand();
        $brand->name = $request->name;
        $brand->slog = Str::slug($request->name);
        $image = $request->file('image');
        $file_extention =   $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp . '.' . $file_extention;
        $this->GenerateBrandThumbnailsImage($image, $file_name);
        $brand->image = $file_name;
        $brand->save();
        return redirect()->route('admin.brands')->with('status', 'Brand has been added susscessfully');
    }

    public function GenerateBrandThumbnailsImage($image, $imagename)
    {
        // $destinationPath = public_path('uploads/brands');
        // $img = Image::read($image->path);
        // $img->cover(124, 124, "top");
        // $img->resize(124, 124, function ($constraint) {
        //     $constraint->aspectRatio();
        // })->save($destinationPath . '/' . $imagename);


        $destinationPath = public_path('uploads/brands');

        // สร้างอ็อบเจ็กต์ภาพ
        $img = Image::make($image->path()); // ใช้ make() แทน read()

        // ปรับขนาดให้พอดีกับ 124x124 โดยรักษาอัตราส่วนและครอบถ้าจำเป็น
        $img->fit(124, 124, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize(); // ป้องกันการขยายภาพ
        });

        // บันทึกภาพ
        $img->save($destinationPath . '/' . $imagename);
    }
}