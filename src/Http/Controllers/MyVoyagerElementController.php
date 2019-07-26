<?php
namespace Javck\Easyweb2\Http\Controllers;


use App\Element;
use App\Tag;
use Illuminate\Http\Request;
use Session;

use TCG\Voyager\Facades\Voyager;
use Javck\Easyweb2\Http\Controllers\MyVoyagerBaseController;

class MyVoyagerElementController extends MyVoyagerBaseController
{

    public function __construct()
    {
        $this->middleware('auth');
        //$this->middleware('adminOnly'); 改用voyager內建permission
    }

    //複製該元素，並且將啟用關閉
    public function copy($id)
    {
        $element = Element::find($id);
        if (isset($element)) {
            $newElement = $element->replicate();
            $newElement->title = $newElement->title . '(複製)';
            $newElement->enabled = false;
            $newElement->save();
            return redirect('admin/elements/' . $newElement->id . '/edit')->with([
                    'message' => '元素複製成功',
                    'alert-type' => 'success',
                ]);
        }else{
            return redirect('admin/elements')->with([
                    'message' => '元素複製失敗，找不到該筆資料',
                    'alert-type' => 'error',
                ]);
        }
    }
    //暫停使用，使用TCG自帶
    // public function destroy(Request $request, $id)
    // {
    //     $result = true;
    //     if($id != 0){
    //         $element = Element::find($id);
    //         if (isset($element)) {
    //             $element->delete();
    //         }else{
    //             $result = false;
    //         }
    //     }else{
    //         //代表為Mass Delete
    //         $str_ids = $request->all()['ids'];

    //         $ids = explode(',',$str_ids);
    //         foreach ($ids as $value) {
    //             $element = Element::find($value);
    //             if (isset($element)) {
    //                 $element->delete();
    //             }else{
    //                 $result = false;
    //             }
    //         }
    //     }

    //     if ($result) {
    //         return redirect('admin/elements')->with([
    //             'message' => '元素刪除成功',
    //             'alert-type' => 'success',
    //         ]);
    //     }else{
    //         return redirect('admin/elements')->with([
    //             'message' => '元素刪除失敗，找不到該筆資料',
    //             'alert-type' => 'error',
    //         ]);
    //     }
    // }

}
