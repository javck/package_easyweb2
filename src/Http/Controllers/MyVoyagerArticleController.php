<?php

namespace Javck\Easyweb2\Http\Controllers;

use Illuminate\Http\Request;

use Javck\Easyweb2\Http\Controllers\MyVoyagerBaseController;
use App\Http\Requests;
use App\Article;
use App\Tag;
use App\Comment;
use Session;
use Auth;
use Input;
use Str;
use Mail;
use App;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Events\BreadDataAdded;
use TCG\Voyager\Events\BreadDataDeleted;
use TCG\Voyager\Events\BreadDataRestored;
use TCG\Voyager\Events\BreadDataUpdated;
use Carbon\Carbon;

class MyVoyagerArticleController extends MyVoyagerBaseController
{

    public function __construct()
    {

    }

    /**
     * POST BRE(A)D - Store data.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $slug = $this->getSlug($request);
        

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('add', app($dataType->model_name));

        // Validate fields with ajax
        $val = $this->validateBread($request->all(), $dataType->addRows)->validate();

        //自定義資料處理================================
        $inputs = $request->all();
        Input::merge(['author_id' => Auth::user()->id]);
        //附件處理
        if (isset($inputs['attachment_paths']) and $inputs['attachment_paths'][0] != null) {
            $files = Input::file('attachment_paths');
            if (!isset($inputs['attachment_names']) or strlen($inputs['attachment_names'])==0 ) {
                $fileNames = array();
                foreach ($files as $file) {
                    $fileNames[] = $file->getClientOriginalName();
                }
                Input::merge(['attachment_names' => implode(',',$fileNames)]);
            }
        }

        if (!isset($inputs['content_small'])) {
            Input::merge(['content_small' => mb_substr($inputs['content'],0,60,"utf-8") . '...']);
        }

        //=============================================
        $data = $this->insertUpdateData($request, $slug, $dataType->addRows, new $dataType->model_name());

        event(new BreadDataAdded($dataType, $data));

        return redirect()
        ->route("voyager.{$dataType->slug}.index")
        ->with([
                'message'    => __('voyager::generic.successfully_added_new')." {$dataType->display_name_singular}",
                'alert-type' => 'success',
            ]);
    }

    // POST BR(E)AD
    public function update(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Compatibility with Model binding.
        $id = $id instanceof Model ? $id->{$id->getKeyName()} : $id;

        $model = app($dataType->model_name);
        if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope'.ucfirst($dataType->scope))) {
            $model = $model->{$dataType->scope}();
        }
        if ($model && in_array(SoftDeletes::class, class_uses($model))) {
            $data = $model->withTrashed()->findOrFail($id);
        } else {
            $data = call_user_func([$dataType->model_name, 'findOrFail'], $id);
        }

        // Check permission
        $this->authorize('edit', $data);

        // Validate fields with ajax
        $val = $this->validateBread($request->all(), $dataType->editRows, $dataType->name, $id)->validate();

        //自定義資料處理================================
        $inputs = $request->all();
        //附件處理
        if (isset($inputs['attachment_paths']) and $inputs['attachment_paths'][0] != null) {
            $files = Input::file('attachment_paths');
            if (!isset($inputs['attachment_names']) or strlen($inputs['attachment_names'])==0 ) {
                $fileNames = array();
                foreach ($files as $file) {
                    $fileNames[] = $file->getClientOriginalName();
                }
                Input::merge(['attachment_names' => implode(',',$fileNames)]);
            }
        }else{
            Input::merge(['attachment_paths' => $data->attachment_paths]);
        }

        if (!isset($inputs['content_small'])) {
            Input::merge(['content_small' => mb_substr($inputs['content'],0,60,"utf-8") . '...']);
        }
        //=============================================
        $this->insertUpdateData($request, $slug, $dataType->editRows, $data);
        event(new BreadDataUpdated($dataType, $data));

        return redirect()
        ->route("voyager.{$dataType->slug}.index")
        ->with([
            'message'    => __('voyager::generic.successfully_updated')." {$dataType->display_name_singular}",
            'alert-type' => 'success',
        ]);
    }

    //儲存留言內容
    public function comment($id , Request $request)
    {
        $inputs = $request->all();
        $user = Auth::user();
        $data = array();
        if (isset($user)) {
            $data['user_id'] = $user->id;
            $data['name'] = $user->name;
            $data['email'] = $user->email;
        }else{
            if (isset($inputs['author'])) {
                $data['name'] = $inputs['author'];
            }
            if (isset($inputs['email'])) {
                $data['email'] = $inputs['email'];
            }
            if (isset($inputs['url'])) {
                $data['website'] = $inputs['url'];
            }   
        }
        $article = Article::find($id);
        $data['article_id'] = $id;
        $data['content'] = $inputs['comment'];
        $comment = Comment::create($data);

        //發送Email通知
        if (App::environment() == 'production') {
            $beautyMail = app()->make(\Snowfire\Beautymail\Beautymail::class);
            $beautyMail->send('emails.reminder', ['title' => '文章:' . $article->title .' 有新留言','comment' => $comment , 'mode' => 'comment'], function ($m) {
                $m->from(setting('site.service_mail'), setting('site.name'));
                $m->to(setting('admin.admin_mail'), '網站管理員')->subject('您有新留言');
            });
        }

        return redirect($inputs['currentUrl']);
        //return array('alert' => "success" , 'message' => trans('messages.commentSuccess') );
    }

    //下載檔案附件
    public function download($id , $index)
    {
        $article = Article::find($id);
        $filePath = json_decode($article->attachment_paths,true)[$index]['download_link'];
        $path = public_path() . $filePath;
        $fileName = $article->getAttachNameAry()[$index];
        return response()->download($path,$fileName);
    }

    //顯示某單一文章
    public function show(Request $request,$id)
    {
        //Carbon::setLocale('zh-tw'); //設定Carbon的本地化
        $currentIndex = 0;
        $article = Article::find($id);
        $articles = Article::where('cgy_id',$article->cgy_id)->where('status','published')->orderBy('created_at','desc')->orderBy('sort','asc')->get();
        for ($i=0; $i < count($articles); $i++) { 
            if ($articles[$i]->id == $id) {
                $currentIndex = $i;
                break; 
            }
        }
        //處理上一篇.下一篇文章
        $data = ['article'=>$article];
        if ($currentIndex != 0) {
            $lastArticle = $articles[$currentIndex-1];
            $data['lastArticle'] = $lastArticle;
        }
        if ($currentIndex != count($articles)-1 ) {
            $nextArticle = $articles[$currentIndex+1];
            $data['nextArticle'] = $nextArticle;
        }

        //處理關聯文章
        $related_articles = array();
        foreach ($article->tags as $_tag) {
            foreach ($_tag->articles as $_article) {
                if ($_article->mode=='singleImg' or $_article->mode=='multiImgs' or $_article->mode=='puzzle') {
                    $related_articles[$_article->id] = $_article;
                }
            }
        }
        unset($related_articles[$article->id]); //把自己這篇移除掉

        if (count($related_articles) >=3) {
            $related_articles = array_values($related_articles);
            //最多只取4篇
            if (count($related_articles) > 4) {
                $related_articles = array_slice($related_articles,0,4);
            }
            $data['relatedArticles'] = $related_articles;
        }

        $comments = Comment::where('article_id',$id)->where('enabled',1)->orderBy('created_at','desc')->get();
        $data['comments'] = $comments;

        if (isset($data['article'])) {
            return view('easyweb2::pages.article_show',$data);
        }else{
            return abort(404);
        }
    }

    //暫停使用，使用TCG自帶
    // public function destroy(Request $request, $id)
    // {
    //     $result = true;
    //     if($id != 0){
    //         $article = Article::find($id);
    //         if (isset($article)) {
    //             $article->delete();
    //         }else{
    //             $result = false;
    //         }
    //     }else{
    //         //代表為Mass Delete
    //         $str_ids = $request->all()['ids'];

    //         $ids = explode(',',$str_ids);
    //         foreach ($ids as $value) {
    //             $article = Article::find($value);
    //             if (isset($article)) {
    //                 $article->delete();
    //             }else{
    //                 $result = false;
    //             }
    //         }
    //     }

    //     if ($result) {
    //         return redirect('admin/articles')->with([
    //             'message' => '文章刪除成功',
    //             'alert-type' => 'success',
    //         ]);
    //     }else{
    //         return redirect('admin/articles')->with([
    //             'message' => '文章刪除失敗，找不到該筆資料',
    //             'alert-type' => 'error',
    //         ]);
    //     }
    // }

}
